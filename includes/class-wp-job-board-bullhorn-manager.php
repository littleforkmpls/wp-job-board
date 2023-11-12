<?php
require_once plugin_dir_path(__DIR__) . 'includes/class-wp-job-board-api-manager-base.php';


class WP_Job_Board_Bullhorn_Manager extends WP_Job_Board_API_Manager_Base
{
	const SERVICES_URL = 'https://rest.bullhornstaffing.com/rest-services/loginInfo?username={API_Username}';
	const ACCESS_CODE_ENDPOINT = '{oauth_url}/authorize?client_id={client_id}&response_type=code&action=Login&username={username}&password={password}';
	const ACCESS_TOKEN_ENDPOINT = '{oauth_url}/token?grant_type=authorization_code&code={auth_code}&client_id={client_id}&client_secret={client_secret}';
	const ACCESS_TOKEN_REFRESH_ENDPOINT = '{oauth_url}/token?grant_type=refresh_token&refresh_token={refresh_token}&client_id={client_id}&client_secret={client_secret}';
	const ACCESS_CODE = 'wp_job_board_bullhorn_access_code';
	const ACCESS_TOKEN = 'wp_job_board_bullhorn_access_token';
	const ACCESS_TOKEN_REFRESH = 'wp_job_board_bullhorn_access_token_refresh';
	const ACCESS_TOKEN_EXPIRES = 'wp_job_board_bullhorn_access_token_expires';
	const OAUTH_URL = 'wp_job_board_bullhorn_oauth_url';

	const DATE_FORMAT = 'Y-m-d H:i:s';
	const REST_URL = 'wp_job_board_bullhorn_rest_url';
	const REST_TOKEN = 'wp_job_board_bullhorn_rest_token';
	const CORP_TOKEN = 'wp_job_board_bullhorn_corp_token';

	private $options;
	private $api_username;
	private $api_password;
	private $api_client_id;
	private $api_client_secret;


	public function __construct()
	{
		$this->options = get_option(WP_Job_Board_Admin::OPTION_ARRAY_KEY, array());
		$this->api_username = get_option(WP_Job_Board_Admin::SETTING_API_USERNAME);
		$this->api_password = get_option(WP_Job_Board_Admin::SETTING_API_PASSWORD);
		$this->api_client_id = get_option(WP_Job_Board_Admin::SETTING_CLIENT_ID);
		$this->api_client_secret = get_option(WP_Job_Board_Admin::SETTING_CLIENT_SECRET);

		if (
			!$this->api_username
			|| !$this->api_password
			|| !$this->api_client_id
			|| !$this->api_client_secret
		) {
			$this->throw_error('Not all Bullhorn settings are configured');
		}

	}

	public function trigger_sync($redirect = null): void
	{
		$jobs = $this->get_jobs();

		if (!$jobs) {
			// TODO what to do here?
		}

		global $wpdb;

		$existing_job_orders_result = $wpdb->get_results("SELECT post_id, meta_value FROM wp_postmeta WHERE meta_key = 'wp_job_board_bh_id' AND meta_value IS NOT NULL");
		$existing_job_orders = array();

		foreach ($existing_job_orders_result as $item) {
			$existing_job_orders[$item->meta_value] = $item->post_id;
		}

		foreach ($jobs as $job_order) {
			// Todo change this to inserting custom post types.
			// echo "{$job_order['title']} - {$job_order['id']}: Posted {$job_order['dateAdded']}<br />";
			$postData = array(
				'post_title' => $job_order['title'],
				'post_type' => 'wjb_bh_job_order',
				'post_content' => '',
				'post_status' => 'publish',
				'comment_status' => 'closed',
				'meta_input' => array(
					'wp_job_board_bh_data' => json_encode($job_order),
					'wp_job_board_bh_updated' => 1,
					'wp_job_board_bh_id' => $job_order['id'],
				)
			);

			if (isset($existing_job_orders[$job_order['id']])) {
				$postData['ID'] = $existing_job_orders[$job_order['id']];
			}

			$result = wp_insert_post($postData, true);

			if (!$result || $result instanceof WP_Error) {
				$this->throw_error('Could not insert Job Order ' . $job_order['id'] . ($result ? ' - ' . $result->get_error_message() : ''));
			}

		}

		// Trash our un-updated items
		$result = $wpdb->get_results("UPDATE wp_posts SET post_status = 'trash' WHERE ID IN(SELECT post_id FROM wp_postmeta WHERE meta_key = 'wp_job_board_bh_updated' AND meta_value = 0);");

		// mark everything as unupdated since we're done processing
		$result = $wpdb->get_results("UPDATE wp_postmeta SET meta_value = 0 WHERE meta_key = 'wp_job_board_bh_updated'");

		if($redirect) {
			wp_redirect($redirect);
		}

	}

	private function authorize(): bool
	{
		return !empty($this->get_access_token());
	}



	private function get_access_token(): string
	{
		$expired = true;
		// If we have an expires date, we should have a token.  If we have both of those
		// things and the expires is not past, return the token.
		if (isset($this->options[self::ACCESS_TOKEN_EXPIRES])) {
			$expiresDate = DateTime::createFromFormat(self::DATE_FORMAT, $this->options[self::ACCESS_TOKEN_EXPIRES]);
			$now = new DateTime('now', new DateTimeZone('UTC'));
			$expired = $now > $expiresDate;
		}

		if (!$expired && isset($this->options[self::ACCESS_TOKEN])) {
			return $this->options[self::ACCESS_TOKEN];
		}

		if ($expired && isset($this->options[self::ACCESS_TOKEN_REFRESH])) {
			$url = $this->get_url(self::ACCESS_TOKEN_REFRESH_ENDPOINT, array(
				'{oauth_url}' => $this->get_oauth_url(),
				'{refresh_token}' => $this->options[self::ACCESS_TOKEN_REFRESH],
				'{client_id}' => $this->api_client_id,
				'{client_secret}' => $this->api_client_secret,
			));
		} else {
			$url = $this->get_url(self::ACCESS_TOKEN_ENDPOINT, array(
				'{oauth_url}' => $this->get_oauth_url(),
				'{auth_code}' => $this->get_access_code(),
				'{client_id}' => $this->api_client_id,
				'{client_secret}' => $this->api_client_secret,
			));
		}

		$token = $this->call_api($url, [], 'post');

		if (isset($token['error'])) {
			if (isset($token['error_description']) && $token['error_description'] === 'Invalid, expired, or revoked authorization code.') {
				update_option(WP_Job_Board_Admin::OPTION_ARRAY_KEY, []);
				$this->throw_error('Problem getting authorize token.  Please attempt action again.');
			}
		}

		$expires = (new DateTime())->add(new DateInterval("PT{$token['expires_in']}S"));

		$this->options[self::ACCESS_TOKEN] = $token['access_token'];
		$this->options[self::ACCESS_TOKEN_EXPIRES] = $expires->format(self::DATE_FORMAT);
		$this->options[self::ACCESS_TOKEN_REFRESH] = $token['refresh_token'];

		update_option(WP_Job_Board_Admin::OPTION_ARRAY_KEY, $this->options);

		return $this->options[self::ACCESS_TOKEN];
	}

	private function get_access_code(): string
	{
		if (isset($this->options[self::ACCESS_CODE])) {
			return $this->options[self::ACCESS_CODE];
		}

		$url = $this->get_url(self::ACCESS_CODE_ENDPOINT, array(
			'{oauth_url}' => $this->get_oauth_url(),
			'{client_id}' => $this->api_client_id,
			'{username}' => $this->api_username,
			'{password}' => $this->api_password,
		));

		$response = $this->call_api($url, ['redirects' => 0], 'get', 'response');
		$redirect_url = $response['http_response']->get_response_object()->url;
		parse_str(parse_url($redirect_url)['query'], $output);

		if (empty($output['code'])) {
			$this->throw_error('Could not retrieve Access Code');
		}

		$this->options[self::ACCESS_CODE] = $output['code'];
		update_option(WP_Job_Board_Admin::OPTION_ARRAY_KEY, $this->options);

		return $this->options[self::ACCESS_CODE];
	}


	private function get_oauth_url()
	{
		if (isset($this->options[self::OAUTH_URL])) {
			return $this->options[self::OAUTH_URL];
		}

		return $this->get_services(self::OAUTH_URL);
	}

	private function get_services(string $service): string
	{
		$endpoint_url = $this->get_url(self::SERVICES_URL, array(
			'{API_Username}' => $this->api_username,
		));
		
		$results = $this->call_api($endpoint_url);

		if (empty($results['oauthUrl'])) {
			$this->throw_error('Could not retrieve OAuth Endpoint');
		}

		$this->options[self::OAUTH_URL] = $results['oauthUrl'];
		$this->options[self::REST_URL] = $results['restUrl'];

		update_option(WP_Job_Board_Admin::OPTION_ARRAY_KEY, $this->options);

		return $this->options[$service];
	}

	private function get_jobs()
	{
		$baseUrl = '{corpToken}query/JobOrder?fields=id,title,dateAdded&BhRestToken={rest_token}';

		$url = $this->get_url($baseUrl, array(
			'{corpToken}' => $this->get_corp_token(),
			'{rest_token}' => $this->options[self::REST_TOKEN],
		));

		$result = $this->call_api($url, ['body' => ['where' => 'id IS NOT NULL']]);

		if (isset($result['errorMessageKey'])) {
			$this->throw_error("{$result['errorMessageKey']} - {$result['errorMessage']}");
		}

		if (isset($result['message']) && $result['message'] === "Bad 'BhRestToken' or timed-out." ) {
			unset($this->options[self::CORP_TOKEN]);
			unset($this->options[self::REST_TOKEN]);
			$this->trigger_sync();
			return;
		}

		if (!isset($result['data'])) {
			$this->throw_error('Could not sync any jobs.');
		}

		return $result['data'];
	}

	private function get_corp_token()
	{
		if (!empty($this->options[self::CORP_TOKEN]) && !empty($this->options[self::REST_TOKEN])) {
			$result = $this->call_api($this->options[self::CORP_TOKEN] . 'ping?BhRestToken='.$this->options[self::REST_TOKEN]);
			if (!isset($result['errorCode'])) {
				return $this->options[self::CORP_TOKEN];
			}
			unset(
				$this->options[self::CORP_TOKEN],
				$this->options[self::REST_TOKEN]
			);
		}

		$loginUrl = '{rest_url}/login?version=2.0&access_token={access_token}';
		$url = $this->get_url($loginUrl, array(
			'{rest_url}' => $this->get_services(self::REST_URL),
			'{access_token}' => $this->get_access_token(),
		));

		$result = $this->call_api($url, [], 'post');
		if (isset($result['errorCode'])) {
			update_option(WP_Job_Board_Admin::OPTION_ARRAY_KEY, []);
			$this->throw_error('Could not login to REST API, please try again');
		}
		$this->options[self::REST_TOKEN] = $result['BhRestToken'];
		$this->options[self::CORP_TOKEN] = $result['restUrl'];

		update_option(WP_Job_Board_Admin::OPTION_ARRAY_KEY, $this->options);
		return $this->options[self::CORP_TOKEN];
	}


}