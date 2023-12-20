<?php
/**
 * API Manager Class
 *
 * Manging the calls for the Bullhorn REST API.
 *
 * @link       http://example.com
 * @since      0.1.0
 *
 * @package    WP_Job_Board
 * @subpackage WP_Job_Board/includes
 */

/**
 * Class to handle interactions with the Bullhorn REST API.
 *
 * @since      0.1.0
 * @package    WP_Job_Board
 * @subpackage WP_Job_Board/includes
 * @author     Little Fork
 */
class WP_Job_Board_Bullhorn_Manager extends WP_Job_Board_API_Manager_Base {

    /**
     * Urls and endpoints for fetching our information.
     */
    const SERVICES_URL = 'https://rest.bullhornstaffing.com/rest-services/loginInfo?username={API_Username}';
    const ACCESS_CODE_ENDPOINT = '{oauth_url}/authorize?client_id={client_id}&response_type=code&action=Login&username={username}&password={password}';
    const ACCESS_TOKEN_ENDPOINT = '{oauth_url}/token?grant_type=authorization_code&code={auth_code}&client_id={client_id}&client_secret={client_secret}';
    const ACCESS_TOKEN_REFRESH_ENDPOINT = '{oauth_url}/token?grant_type=refresh_token&refresh_token={refresh_token}&client_id={client_id}&client_secret={client_secret}';

    /**
     * The following are config array keys.
     */
    const ACCESS_CODE = 'wp_job_board_bullhorn_access_code';
    const ACCESS_TOKEN = 'wp_job_board_bullhorn_access_token';
    const ACCESS_TOKEN_REFRESH = 'wp_job_board_bullhorn_access_token_refresh';
    const ACCESS_TOKEN_EXPIRES = 'wp_job_board_bullhorn_access_token_expires';
    const OAUTH_URL = 'wp_job_board_bullhorn_oauth_url';
    const REST_URL = 'wp_job_board_bullhorn_rest_url';
    const REST_TOKEN = 'wp_job_board_bullhorn_rest_token';
    const CORP_TOKEN = 'wp_job_board_bullhorn_corp_token';

    /**
     * Date format for consistency.
     */
    const DATE_FORMAT = 'Y-m-d H:i:s';
    const SESSION_REST_EXPIRES = 'wp_job_board_session_rest_key_expires';

    /**
     * Our config options managed as an array.
     *
     * @var array
     */
    private array $options;
    /**
     * Bullhorn REST API username.
     *
     * @var string
     */
    private string $api_username;
    /**
     * Bullhorn REST API password
     *
     * @var string
     */
    private string $api_password;
    /**
     * Bullhorn REST API Client ID
     *
     * @var string
     */
    private string $api_client_id;
    /**
     * Bullhorn REST API Client Secret
     *
     * @var string
     */
    private string $api_client_secret;

    private array $job_order_fields = array(
        'address',
        'dateLastModified',
        'dateLastPublished',
        'employmentType',
        'id',
        'isOpen',
        'isPublic',
        'isDeleted',
        'publicDescription',
        'publishedCategory',
        'publishedZip',
        'salary',
        'salaryUnit',
        'status',
        'title',
    );


    /**
     * Constructor to set up our class.
     */
    public function __construct() {
        $this->options           = get_option(WP_Job_Board_Admin::OPTION_ARRAY_KEY, array());
        $this->api_username      = get_option(WP_Job_Board_Admin::SETTING_API_USERNAME);
        $this->api_password      = get_option(WP_Job_Board_Admin::SETTING_API_PASSWORD);
        $this->api_client_id     = get_option(WP_Job_Board_Admin::SETTING_CLIENT_ID);
        $this->api_client_secret = get_option(WP_Job_Board_Admin::SETTING_CLIENT_SECRET);

        if (
            ! $this->api_username
            || ! $this->api_password
            || ! $this->api_client_id
            || ! $this->api_client_secret
        ) {
            $this->throw_error('Not all Bullhorn settings are configured');
        }
    }

    /**
     * This triggers our syncing of our Job Queue.
     *
     * @param $redirect string|null If set will redirect to url.
     *
     * @return void
     */
    public function trigger_sync(string $redirect = null, bool $force = false): void {
        $log_data = array();
        $jobs     = $this->get_jobs($redirect, $force);

        if ( ! $jobs) {
            $this->throw_error('No jobs found to sync');
        }

        global $wpdb;

        $existing_job_orders_result = $wpdb->get_results("SELECT post_id, meta_value FROM wp_postmeta WHERE meta_key = 'wp_job_board_bh_id' AND meta_value IS NOT NULL");
        $existing_job_orders        = array();

        foreach ($existing_job_orders_result as $item) {
            $existing_job_orders[$item->meta_value] = $item->post_id;
        }

        $count = 0;

        foreach ($jobs as $job_order) {
//            $job_order['publicDescription'] = addslashes($job_order['publicDescription']);
            $bh_data   = json_encode($job_order);
            $clean_title = sanitize_title($job_order['title'] . '-' . $job_order['id']);
            if (!$bh_data) {
                error_log('Problem encoding job('.$clean_title.'): ' . json_last_error_msg());
            }
            $post_data = array(
                'post_title'     => $job_order['title'],
                'post_name'      => $clean_title,
                'post_type'      => 'wjb_bh_job_order',
                'post_content'   => '',
                'post_status'    => 'publish',
                'comment_status' => 'closed',
                'meta_input'     => array(
                    'wp_job_board_bh_data'    => addslashes($bh_data), // we have to do this because wp strips slashes when saving
                    'wp_job_board_bh_updated' => 1,
                    'wp_job_board_bh_id'      => $job_order['id'],
                ),
                'tax_input' => array(
                    'wjb_bh_job_type_tax' => $job_order['employmentType'],
                    'wjb_bh_job_location_tax' => $this->get_mapped_state($job_order['address']['state']),
                    'wjb_bh_job_category_tax' => $job_order['publishedCategory']['name'],
                )
            );

            if (isset($existing_job_orders[$job_order['id']])) {
                $post_data['ID'] = $existing_job_orders[$job_order['id']];
                $post_bh_data    = get_post_meta($existing_job_orders[$job_order['id']], 'wp_job_board_bh_data', true);

                // if our data is the same mark as updated and skip it.
                if (!$force && $post_bh_data === $bh_data) {
                    update_post_meta($existing_job_orders[$job_order['id']], 'wp_job_board_bh_updated', 1);
                    continue;
                }
                $log_data[] = array(
                    'bh_id'  => $job_order['id'],
                    'action' => 'Updated' . ($force ? ' (Manually)' : ''),
                    'title'  => $job_order['title'],
                    'time'   => time(),
                );
            } else {
                $log_data[] = array(
                    'bh_id'  => $job_order['id'],
                    'action' => 'Created',
                    'title'  => $job_order['title'],
                    'time'   => time(),
                );
            }

            $result = wp_insert_post($post_data, true);

            $count ++;

            if ($count && $count % 20 === 0) {
                $this->save_logs($log_data);
                $log_data = array();
            }

            if ( ! $result || $result instanceof WP_Error) {
                $this->throw_error('Could not insert Job Order ' . $job_order['id'] . ($result ? ' - ' . $result->get_error_message() : ''));
            }
        }

        // Trash our un-updated items
        $result = $wpdb->get_results("SELECT pm2.meta_value FROM wp_postmeta pm1 JOIN wp_postmeta pm2 on pm1.post_id = pm2.post_id WHERE pm1.meta_key = 'wp_job_board_bh_updated' AND pm1.meta_value = 0 AND pm2.meta_key = 'wp_job_board_bh_data'");
        $time   = time();
        foreach ($result as $item) {
            $bh_data    = json_decode($item->meta_value, true);
            $log_data[] = array(
                'bh_id'  => $bh_data['id'],
                'action' => 'Removed',
                'title'  => $bh_data['title'],
                'time'   => $time,
            );
        }
        $result = $wpdb->get_results("UPDATE wp_posts SET post_status = 'trash' WHERE ID IN(SELECT post_id FROM wp_postmeta WHERE meta_key = 'wp_job_board_bh_updated' AND meta_value = 0);");

        // mark everything as unupdated since we're done processing
        $result = $wpdb->get_results("UPDATE wp_postmeta SET meta_value = 0 WHERE meta_key = 'wp_job_board_bh_updated'");

        $this->save_logs($log_data);

        if ($redirect) {
            wp_redirect($redirect);
        }
    }

    /**
     * @return mixed|void
     */
    private function get_jobs(string $redirect = null, bool $force = false) {


        $baseUrl = '{corp_token}query/JobOrder?fields=id,title,dateAdded&BhRestToken={rest_token}';
        $baseUrl = '{corp_token}search/{entity}?fields={fields}&sort={fields}&count={count}&start={start}&BhRestToken={rest_token}';
        $baseUrl = '{corp_token}search/{entity}?fields={fields}&sort={sort}&count={count}&start={start}&BhRestToken={rest_token}';

        $tokens = array(
            '{corp_token}' => $this->get_corp_token(),
            '{entity}'     => 'JobOrder',
            '{fields}'     => implode(',', $this->job_order_fields),
            '{start}'      => 0,
            '{count}'      => 500,
            '{sort}'       => 'id',
            '{rest_token}' => $this->options[self::REST_TOKEN],
        );

        $callAgain = true;
        $results   = [];

        while ($callAgain) {
            $url    = $this->get_url(
                $baseUrl,
                $tokens,
            );
            $result = $this->call_api($url, array('body' => array('query' => 'status:"Accepting Candidates" AND isOpen:true AND isPublic:1 AND isDeleted:false')), 'post');

            if (isset($result['errorMessageKey'])) {
                $callAgain = false;
                $this->throw_error("{$result['errorMessageKey']} - {$result['errorMessage']}");
            }

            if (isset($result['errorMessage']) && isset($result['errorCode'])) {
                $callAgain = false;
                $this->throw_error("{$result['errorCode']} - {$result['errorMessage']}");
            }

            if (isset($result['message']) && $result['message'] === "Bad 'BhRestToken' or timed-out.") {
                $callAgain = false;
                unset($this->options[self::CORP_TOKEN]);
                unset($this->options[self::REST_TOKEN]);
                $this->trigger_sync($redirect, $force);

                return;
            }

            if ( ! isset($result['data'])) {
                $callAgain = false;
                $this->throw_error('Could not sync any jobs.');
            }

            if (isset($result['total']) && isset($result['start']) && isset($result['count'])) {
                $callAgain         = ! ($result['start'] + $result['count'] >= $result['total']);
                $tokens['{start}'] = $result['start'] + $result['count'];
            }

            $results = array_merge($results, $result['data']);
        }

        return $results;
    }

    /**
     * @return mixed
     * @throws Exception
     */
    private function get_corp_token() {
        if ( ! empty($this->options[self::CORP_TOKEN]) && ! empty($this->options[self::REST_TOKEN])) {
            // check our session first
            if ( ! empty($_SESSION[self::SESSION_REST_EXPIRES]) && $_SESSION[self::SESSION_REST_EXPIRES] > time()) {
                return $this->options[self::CORP_TOKEN];
            }
            // if we're still not good, ping
            $result  = $this->call_api($this->options[self::CORP_TOKEN] . 'ping?BhRestToken=' . $this->options[self::REST_TOKEN]);
            $expires = isset($result['sessionExpires']) ? floor($result['sessionExpires'] / 1000) : null;
            if ($expires && $expires > time()) {
                $_SESSION[self::SESSION_REST_EXPIRES] = $expires;

                return $this->options[self::CORP_TOKEN];
            }
            // if we're still not good, clear everything, and start over.
            unset(
                $this->options[self::CORP_TOKEN],
                $this->options[self::REST_TOKEN],
                $_SESSION[self::SESSION_REST_EXPIRES],
            );
        }

        $loginUrl = '{rest_url}/login?version=2.0&access_token={access_token}';
        $url      = $this->get_url(
            $loginUrl,
            array(
                '{rest_url}'     => $this->get_services(self::REST_URL),
                '{access_token}' => $this->get_access_token(),
            )
        );

        $result = $this->call_api($url, array(), 'post');
        if (isset($result['errorCode'])) {
            update_option(WP_Job_Board_Admin::OPTION_ARRAY_KEY, array());
            $this->throw_error('Could not login to REST API, please try again');
        }
        $this->options[self::REST_TOKEN] = $result['BhRestToken'];
        $this->options[self::CORP_TOKEN] = $result['restUrl'];

        update_option(WP_Job_Board_Admin::OPTION_ARRAY_KEY, $this->options);

        return $this->options[self::CORP_TOKEN];
    }

    /**
     * @param string $service
     *
     * @return string
     */
    private function get_services(string $service): string {
        $endpoint_url = $this->get_url(
            self::SERVICES_URL,
            array(
                '{API_Username}' => $this->api_username,
            )
        );

        $results = $this->call_api($endpoint_url);

        if (empty($results['oauthUrl'])) {
            $this->throw_error('Could not retrieve OAuth Endpoint');
        }

        $this->options[self::OAUTH_URL] = $results['oauthUrl'];
        $this->options[self::REST_URL]  = $results['restUrl'];

        update_option(WP_Job_Board_Admin::OPTION_ARRAY_KEY, $this->options);

        return $this->options[$service];
    }

    /**
     * @return string
     * @throws Exception
     */
    private function get_access_token(): string {
        $expired = true;
        // If we have an expires date, we should have a token.  If we have both of those
        // things and the expires is not past, return the token.
        if (isset($this->options[self::ACCESS_TOKEN_EXPIRES])) {
            $expiresDate = DateTime::createFromFormat(self::DATE_FORMAT, $this->options[self::ACCESS_TOKEN_EXPIRES]);
            $now         = new DateTime('now', new DateTimeZone('UTC'));
            $expired     = $now > $expiresDate;
        }

        if ( ! $expired && isset($this->options[self::ACCESS_TOKEN])) {
            return $this->options[self::ACCESS_TOKEN];
        }

        if ($expired && isset($this->options[self::ACCESS_TOKEN_REFRESH])) {
            $url = $this->get_url(
                self::ACCESS_TOKEN_REFRESH_ENDPOINT,
                array(
                    '{oauth_url}'     => $this->get_oauth_url(),
                    '{refresh_token}' => $this->options[self::ACCESS_TOKEN_REFRESH],
                    '{client_id}'     => $this->api_client_id,
                    '{client_secret}' => $this->api_client_secret,
                )
            );
        } else {
            $url = $this->get_url(
                self::ACCESS_TOKEN_ENDPOINT,
                array(
                    '{oauth_url}'     => $this->get_oauth_url(),
                    '{auth_code}'     => $this->get_access_code(),
                    '{client_id}'     => $this->api_client_id,
                    '{client_secret}' => $this->api_client_secret,
                )
            );
        }

        $token = $this->call_api($url, array(), 'post');

        if (isset($token['error'])) {
            if (isset($token['error_description'])
                && ($token['error_description'] === 'Invalid, expired, or revoked authorization code.'
                    || $token['error_description'] === 'Invalid, expired, or revoked refresh token.')
                ) {
                update_option(WP_Job_Board_Admin::OPTION_ARRAY_KEY, array());
                $this->throw_error('Problem getting authorize token.  Please attempt action again.');
            }
            $this->throw_error('Problem with authorization: ' . json_encode($token));
        }

        $expires = (new DateTime())->add(new DateInterval("PT{$token['expires_in']}S"));

        $this->options[self::ACCESS_TOKEN]         = $token['access_token'];
        $this->options[self::ACCESS_TOKEN_EXPIRES] = $expires->format(self::DATE_FORMAT);
        $this->options[self::ACCESS_TOKEN_REFRESH] = $token['refresh_token'];

        update_option(WP_Job_Board_Admin::OPTION_ARRAY_KEY, $this->options);

        return $this->options[self::ACCESS_TOKEN];
    }

    /**
     * @return mixed|string
     */
    private function get_oauth_url() {
        if (isset($this->options[self::OAUTH_URL])) {
            return $this->options[self::OAUTH_URL];
        }

        return $this->get_services(self::OAUTH_URL);
    }

    /**
     * @return string
     */
    private function get_access_code(): string {
        if (isset($this->options[self::ACCESS_CODE])) {
            return $this->options[self::ACCESS_CODE];
        }

        $url = $this->get_url(
            self::ACCESS_CODE_ENDPOINT,
            array(
                '{oauth_url}' => $this->get_oauth_url(),
                '{client_id}' => $this->api_client_id,
                '{username}'  => $this->api_username,
                '{password}'  => $this->api_password,
            )
        );

        $response     = $this->call_api($url, array('redirects' => 0), 'get', 'response');
        $redirect_url = $response['http_response']->get_response_object()->url;
        parse_str(parse_url($redirect_url)['query'], $output);

        if (empty($output['code'])) {
            $this->throw_error('Could not retrieve Access Code');
        }

        $this->options[self::ACCESS_CODE] = $output['code'];
        update_option(WP_Job_Board_Admin::OPTION_ARRAY_KEY, $this->options);

        return $this->options[self::ACCESS_CODE];
    }

    private function save_logs($log_data) {
        global $wpdb;
        $sql_start   = 'INSERT INTO wp_job_board_log (bh_id, bh_title, action, timestamp) values';
        $insert_data = '';
        $sql_end     = ';';

        foreach ($log_data as $index => $log_datum) {
            if ($index > 0 && ! empty($insert_data)) {
                $insert_data .= ',';
            }
            $insert_data .= $wpdb->prepare('(%d,%s,%s,%d)', array(
                $log_datum['bh_id'],
                $log_datum['title'],
                $log_datum['action'],
                $log_datum['time']
            ));

            if ($index > 0 && $index % 20 === 0) {
                $wpdb->query($sql_start . $insert_data . $sql_end);
                $insert_data = '';
            }
        }
        if (strlen($insert_data)) {
            $wpdb->query($sql_start . $insert_data . $sql_end);
        }

        $one_week_ago = (new DateTime())->sub(new DateInterval('P7D'))->getTimestamp();

        $wpdb->query($wpdb->prepare('DELETE FROM wp_job_board_log WHERE timestamp < %d', $one_week_ago));

    }

    public function submit_resume(): array {
        $first_name   = $this->get_posted_data('first_name');
        $last_name    = $this->get_posted_data('last_name');
        $phone        = $this->get_posted_data('phone');
        $email        = $this->get_posted_data('email');
        $job_order_id = $this->get_posted_data('job_order_id');
        $wp_post_id   = $this->get_posted_data('wp_post_id');
        $resume       = $_FILES['resume'];

        if (empty($first_name) || empty($last_name) || empty($phone) || empty($email) || empty($resume)) {
            $this->throw_error('Must submit all data (First name, Last name, Phone number, Email address, and Resume');
        }

        if (empty($resume['type']) || $resume['type'] !== 'application/pdf') {
            $this->throw_error('Must submit a PDF of your resume');
        }

        $job_order   = $this->get_job_order($wp_post_id, $job_order_id);
        $candidate   = $this->get_candidate($email, $job_order);
        $submission  = $this->create_submission($job_order, $candidate);
        $file        = $this->add_file($resume, $candidate);
        $resume_data = $this->get_data_from_resume($resume);
        $updated     = $this->update_candidate_from_resume($candidate, $resume_data);


        return array();
    }

    private function get_posted_data($key, $default = null) {
        if ( ! isset($_POST[$key])) {
            return $default;
        }

        return $_POST[$key];
    }

    private function get_job_order(string|int $wp_post_id, string|int $job_order_id) {
        $post = get_post($wp_post_id);
        $meta = get_post_meta($wp_post_id, 'wp_job_board_bh_data');

        if ($post->post_status !== 'publish') {
            $this->throw_error('Job has been closed');
        }

        if ( ! empty($meta[0])) {
            $data = json_decode($meta[0], true);
        } else {
            $data = null;
        }

        if ( ! $data || ! isset($data['publishedCategory']) || ! isset($data['publishedCategory']['id'])) {
            $tokens = array(
                '{corp_token}'   => $this->get_corp_token(),
                '{job_order_id}' => $job_order_id,
                '{fields}'       => implode(',', $this->job_order_fields),
                '{rest_token}'   => $this->options[self::REST_TOKEN],
            );
            $url    = $this->get_url('{corp_token}search/JobOrder?fields={fields}&BhRestToken={rest_token}', $tokens);
            $result = $this->call_api(
                $url,
                array(
                    'body' => array(
                        'query' => "id:{$job_order_id}"
                    )
                ),
                'post'
            );

            if ( ! isset($result['total']) || $result['total'] !== 1) {
                $this->throw_error('Could not find Job Order');
            }

            $data = $result['data'][0];

            update_post_meta($wp_post_id, 'wp_job_board_bh_data', json_encode($data, JSON_HEX_APOS | JSON_HEX_QUOT));
        }

        return $data;
    }

    private function get_candidate(string $email, array $job_order) {
        $category_data = null;
        if ( ! empty($job_order['publishedCategory']) && ! empty($job_order['publishedCategory']['id']) && ! empty($job_order['publishedCategory']['name'])) {
            $category_data = array(
                'id'   => $job_order['publishedCategory']['id'],
                'name' => $job_order['publishedCategory']['name'],
            );
        }
        $fields = array(
            'id',
            'email',
            'firstName',
            'lastName',
            'category',
        );
        $tokens = array(
            '{corp_token}' => $this->get_corp_token(),
            '{fields}'     => implode(',', $fields),
            '{rest_token}' => $this->options[self::REST_TOKEN],
        );
        $url    = $this->get_url('{corp_token}search/Candidate?fields={fields}&count=1&BhRestToken={rest_token}', $tokens);

        $result = $this->call_api(
            $url,
            array(
                'body' => array(
                    'query' => "email:{$email}",
                )
            ),
            'post'
        );

        if (isset($result['total']) && $result['total'] > 0) {
            $candidate = $result['data'][0];
            if ($category_data) {
                $tokens = array(
                    '{corp_token}'   => $this->get_corp_token(),
                    '{rest_token}'   => $this->options[self::REST_TOKEN],
                    '{candidate_id}' => $candidate['id'],
                );
                $args   = array(
                    'body' => array(
                        'category' => $category_data,
                    ),
                );
                $url    = $this->get_url('{corp_token}entity/Candidate/{candidate_id}?BhRestToken={rest_token}', $tokens);

                $result = $this->call_api($url, $args, 'post');
                if (empty($result['changeType'])) {
                    //TODO is it okay to fail silently here?
                }
            }

            return $candidate;
        }

        $tokens = array(
            '{corp_token}' => $this->get_corp_token(),
            '{rest_token}' => $this->options[self::REST_TOKEN]
        );
        $body   = array(
            'firstName' => $this->get_posted_data('first_name'),
            'lastName'  => $this->get_posted_data('last_name'),
            'phone'     => $this->get_posted_data('phone'),
            'email'     => $email,
        );
        if ($category_data) {
            $body['category'] = $category_data;
        }

        if ($source = $this->get_posted_data('source')) {
            $body['source'] = $source;
        }

        $url    = $this->get_url('{corp_token}entity/Candidate?BhRestToken={rest_token}', $tokens);
        $result = $this->call_api($url, array('body' => $body, 'method' => 'PUT'), 'post');
        if ( ! empty($result['errorMessage'])) {
            $this->throw_error($result['errorMessage']);
        }

        if (empty($result['data'])) {
            $this->throw_error('Problem creating candidate');
        }

        $candidate       = $result['data'];
        $candidate['id'] = $result['changedEntityId'];

        return $candidate;
    }

    private function create_submission(array $job_order, array $candidate) {
        $tokens = array(
            '{corp_token}' => $this->get_corp_token(),
            '{rest_token}' => $this->options[self::REST_TOKEN],
        );
        $args   = array(
            'body'   => array(
                'candidate' => array(
                    'id' => $candidate['id']
                ),
                'jobOrder'  => array(
                    'id' => $job_order['id']
                ),
                'status'    => 'New Lead',
            ),
            'method' => 'PUT',
        );
        if ($source = $this->get_posted_data('source')) {
            $args['body']['source'] = $source;
        }
        $url    = $this->get_url('{corp_token}entity/JobSubmission?BhRestToken={rest_token}', $tokens);
        $result = $this->call_api($url, $args, 'post');

        if (empty($result['changedEntityType'])) {
            $this->throw_error('Could not create job submission');
        }

        return $result;

    }

    private function add_file(mixed $resume, array $candidate) {
        $tokens = array(
            '{corp_token}'   => $this->get_corp_token(),
            '{candidate_id}' => $candidate['id'],
            '{fields}'       => implode(',', array('id', 'name')),
            '{rest_token}'   => $this->options[self::REST_TOKEN],
        );
        $url    = $this->get_url('{corp_token}entity/Candidate/{candidate_id}/fileAttachments?fields={fields}&BhRestToken={rest_token}', $tokens);
        $result = $this->call_api($url);

        $duplicate        = false;
        $uploaded_content = str_replace(array(
            ' ',
            "\n",
            "\t",
            "\r"
        ), '', base64_encode(file_get_contents($resume['tmp_name'])));
        if ( ! empty($result['data'])) {
            foreach ($result['data'] as $entity_file) {
                $tokens['{file_id}'] = $entity_file['id'];
                $file_url            = $this->get_url('{corp_token}file/Candidate/{candidate_id}/{file_id}', $tokens);
                $file_result         = $this->call_api($file_url);
                if ( ! empty($file_result['File']) && ! empty($file_result['File']['fileContent'])) {
                    $test_content = str_replace(array(
                        ' ',
                        "\n",
                        "\t",
                        "\r"
                    ), '', base64_encode(file_get_contents($file_result['File']['fileContent'])));
                    if ($test_content === $uploaded_content) {
                        $duplicate = true;
                        break;
                    }
                }
            }
        }
        if ($duplicate) {
            return $resume;
        }

        $args   = array(
            'method' => 'PUT',
            'body'   => array(
                'externalID'  => "resume_{$candidate['id']}_{$resume['name']}",
                'fileContent' => base64_encode(file_get_contents($resume['tmp_name'])),
                'fileType'    => 'SAMPLE',
                'name'        => $resume['name'],
                'contentType' => 'application/pdf',
                'description' => 'Resume file for candidate.',
                'type'        => 'Resume',
            )
        );
        $url    = $this->get_url('{corp_token}file/Candidate/{candidate_id}?BhRestToken={rest_token}', $tokens);
        $result = $this->call_api($url, $args, 'post');

        if (empty($result['fileId'])) {
            $this->throw_error('Could not upload resume');
        }

        return $result;
    }

    private function get_data_from_resume(mixed $resume) {
        $file_type = pathinfo($resume['name'])['extension'];

        $local_file = $resume['tmp_name']; //path to a local file on your server

        $boundary = wp_generate_password(24);
        $headers  = array(
            'content-type' => 'multipart/form-data; boundary=' . $boundary,
        );
        $payload  = '';

        if ($local_file) {
            $payload .= '--' . $boundary;
            $payload .= "\r\n";
            $payload .= 'Content-Disposition: form-data; name="' . 'upload' .
                        '"; filename="' . $resume['name'] . '"' . "\r\n";
            $payload .= 'Content-Type: application/pdf' . "\r\n";
            $payload .= "\r\n";
            $payload .= file_get_contents($local_file);
            $payload .= "\r\n";
        }
        $payload .= '--' . $boundary . '--';


        $tokens = array(
            '{corp_token}' => $this->get_corp_token(),
            '{format}'     => $file_type,
            '{rest_token}' => $this->options[self::REST_TOKEN],
        );
        $args   = array(
            'skip_json' => true,
            'headers'   => $headers,
            'body'      => $payload
        );
        $url    = $this->get_url('{corp_token}resume/parseToCandidate?format={format}&BhRestToken={rest_token}', $tokens);
        $result = $this->call_api($url, $args, 'post');

        if (empty($result['candidate'])) {
            //TODO Fail silently here?
        }

        return $result;
    }

    private function update_candidate_from_resume(mixed $candidate, mixed $resume_data) {
        $body = array();
        if ( ! empty($resume_data['candidate']['occupation'])) {
            $body['occupation'] = $resume_data['candidate']['occupation'];
        }
        if ( ! empty($resume_data['candidate']['companyName'])) {
            $body['companyName'] = $resume_data['candidate']['companyName'];
        }
        if ( ! empty($resume_data['candidate']['phone'])) {
            $body['phone'] = $resume_data['candidate']['phone'];
        }
        if (
            ! empty($resume_data['candidateEducation'][0]['major'])
            && ! empty($resume_data['candidateEducation'][0]['school'])
            && ! empty($resume_data['candidateEducation'][0]['degree'])
        ) {
            $body['degreeList'] = "{$resume_data['candidateEducation'][0]['major']} - {$resume_data['candidateEducation'][0]['degree']} - {$resume_data['candidateEducation'][0]['school']}";
        }
        if ( ! empty($resume_data['candidateEducation'][0]['degree'])) {
            $body['educationDegree'] = $resume_data['candidateEducation'][0]['degree'];
        }
        if ( ! empty($resume_data['candidate']['address'])) {
            if ( ! empty($resume_data['candidate']['address']['address1'])) {
                $body['address']['address1'] = $resume_data['candidate']['address']['address1'];
            }
            if ( ! empty($resume_data['candidate']['address']['address2'])) {
                $body['address']['address2'] = $resume_data['candidate']['address']['address2'];
            }
            if ( ! empty($resume_data['candidate']['address']['city'])) {
                $body['address']['city'] = $resume_data['candidate']['address']['city'];
            }
            if ( ! empty($resume_data['candidate']['address']['state'])) {
                $body['address']['state'] = $resume_data['candidate']['address']['state'];
            }
            if ( ! empty($resume_data['candidate']['address']['zip'])) {
                $body['address']['zip'] = $resume_data['candidate']['address']['zip'];
            }
            if ( ! empty($resume_data['candidate']['address']['countryID'])) {
                $body['address']['countryID'] = $resume_data['candidate']['address']['countryID'];
            }
            if ( ! empty($resume_data['candidate']['address']['countryName'])) {
                $body['address']['countryName'] = $resume_data['candidate']['address']['countryName'];
            }
        }

        if (empty($body)) {
            //TODO Nothing to update?  Fail silently?
            return;
        }
        $tokens = array(
            '{corp_token}'   => $this->get_corp_token(),
            '{candidate_id}' => $candidate['id'],
            '{rest_token}'   => $this->options[self::REST_TOKEN],
        );
        $args   = array(
            'body' => $body
        );
        $url    = $this->get_url('{corp_token}entity/Candidate/{candidate_id}?BhRestToken={rest_token}', $tokens);
        $result = $this->call_api($url, $args, 'post');

        if (empty($result['changedEntityId'])) {
            // TODO fail silently?
        }

        return $result;
    }
}
