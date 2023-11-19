<?php
/**
 * API Manager Class
 *
 * Manging the calls for the Bullhorn REST API.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    WP_Job_Board
 * @subpackage WP_Job_Board/includes
 */

/**
 * Class to handle interactions with the Bullhorn REST API.
 *
 * @since      1.0.0
 * @package    WP_Job_Board
 * @subpackage WP_Job_Board/includes
 * @author     Drew Brown <dbrown78@gmail.com>
 */
class WP_Job_Board_Bullhorn_Manager extends WP_Job_Board_API_Manager_Base {

	/**
	 * Urls and endpoints for fetching our information.
	 */
	const SERVICES_URL                  = 'https://rest.bullhornstaffing.com/rest-services/loginInfo?username={API_Username}';
	const ACCESS_CODE_ENDPOINT          = '{oauth_url}/authorize?client_id={client_id}&response_type=code&action=Login&username={username}&password={password}';
	const ACCESS_TOKEN_ENDPOINT         = '{oauth_url}/token?grant_type=authorization_code&code={auth_code}&client_id={client_id}&client_secret={client_secret}';
	const ACCESS_TOKEN_REFRESH_ENDPOINT = '{oauth_url}/token?grant_type=refresh_token&refresh_token={refresh_token}&client_id={client_id}&client_secret={client_secret}';

	/**
	 * The following are config array keys.
	 */
	const ACCESS_CODE          = 'wp_job_board_bullhorn_access_code';
	const ACCESS_TOKEN         = 'wp_job_board_bullhorn_access_token';
	const ACCESS_TOKEN_REFRESH = 'wp_job_board_bullhorn_access_token_refresh';
	const ACCESS_TOKEN_EXPIRES = 'wp_job_board_bullhorn_access_token_expires';
	const OAUTH_URL            = 'wp_job_board_bullhorn_oauth_url';
	const REST_URL             = 'wp_job_board_bullhorn_rest_url';
	const REST_TOKEN           = 'wp_job_board_bullhorn_rest_token';
	const CORP_TOKEN           = 'wp_job_board_bullhorn_corp_token';

	/**
	 * Date format for consistency.
	 */
	const DATE_FORMAT = 'Y-m-d H:i:s';

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


	/**
	 * Constructor to set up our class.
	 */
	public function __construct() {
		$this->options           = get_option( WP_Job_Board_Admin::OPTION_ARRAY_KEY, array() );
		$this->api_username      = get_option( WP_Job_Board_Admin::SETTING_API_USERNAME );
		$this->api_password      = get_option( WP_Job_Board_Admin::SETTING_API_PASSWORD );
		$this->api_client_id     = get_option( WP_Job_Board_Admin::SETTING_CLIENT_ID );
		$this->api_client_secret = get_option( WP_Job_Board_Admin::SETTING_CLIENT_SECRET );

		if (
			! $this->api_username
			|| ! $this->api_password
			|| ! $this->api_client_id
			|| ! $this->api_client_secret
		) {
			$this->throw_error( 'Not all Bullhorn settings are configured' );
		}
	}

	/**
	 * This triggers our syncing of our Job Queue.
	 *
	 * @param $redirect string|null If set will redirect to url.
	 *
	 * @return void
	 */
	public function trigger_sync( $redirect = null ): void {
		$log_data = array();
		$jobs     = $this->get_jobs();

		if ( ! $jobs ) {
			// TODO what to do here?
		}

		global $wpdb;

		$existing_job_orders_result = $wpdb->get_results( "SELECT post_id, meta_value FROM wp_postmeta WHERE meta_key = 'wp_job_board_bh_id' AND meta_value IS NOT NULL" );
		$existing_job_orders        = array();

		foreach ( $existing_job_orders_result as $item ) {
			$existing_job_orders[ $item->meta_value ] = $item->post_id;
		}

        $count = 0;

		foreach ( $jobs as $job_order ) {
			$bh_data   = json_encode( $job_order );
			$post_data = array(
				'post_title'     => $job_order['title'],
				'post_type'      => 'wjb_bh_job_order',
				'post_content'   => '',
				'post_status'    => 'publish',
				'comment_status' => 'closed',
				'meta_input'     => array(
					'wp_job_board_bh_data'    => $bh_data,
					'wp_job_board_bh_updated' => 1,
					'wp_job_board_bh_id'      => $job_order['id'],
				),
			);

			if ( isset( $existing_job_orders[ $job_order['id'] ] ) ) {
				$post_data['ID'] = $existing_job_orders[ $job_order['id'] ];
				$post_bh_data    = get_post_meta( $existing_job_orders[ $job_order['id'] ], 'wp_job_board_bh_data', true );

				// if our data is the same skip it.
				if ( $post_bh_data === $bh_data ) {
					update_post_meta( $existing_job_orders[ $job_order['id'] ], 'wp_job_board_bh_updated', 1 );
					continue;
				}
				$log_data[] = array(
					'bh_id'  => $job_order['id'],
					'action' => 'Updated',
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

			$result = wp_insert_post( $post_data, true );

            $count++;

            if ($count && $count % 20 === 0) {
                $this->save_logs($log_data);
                $log_data = array();
            }

			if ( ! $result || $result instanceof WP_Error ) {
				$this->throw_error( 'Could not insert Job Order ' . $job_order['id'] . ( $result ? ' - ' . $result->get_error_message() : '' ) );
			}
		}

		// Trash our un-updated items
		$result = $wpdb->get_results( "SELECT pm2.meta_value FROM wp_postmeta pm1 JOIN wp_postmeta pm2 on pm1.post_id = pm2.post_id WHERE pm1.meta_key = 'wp_job_board_bh_updated' AND pm1.meta_value = 0 AND pm2.meta_key = 'wp_job_board_bh_data'" );
		$time   = time();
		foreach ( $result as $item ) {
			$bh_data    = json_decode( $item->meta_value, true );
			$log_data[] = array(
				'bh_id'  => $bh_data['id'],
				'action' => 'Removed',
				'title'  => $bh_data['title'],
				'time'   => $time,
			);
		}
		$result = $wpdb->get_results( "UPDATE wp_posts SET post_status = 'trash' WHERE ID IN(SELECT post_id FROM wp_postmeta WHERE meta_key = 'wp_job_board_bh_updated' AND meta_value = 0);" );

		// mark everything as unupdated since we're done processing
		$result = $wpdb->get_results( "UPDATE wp_postmeta SET meta_value = 0 WHERE meta_key = 'wp_job_board_bh_updated'" );

        $this->save_logs($log_data);

		if ( $redirect ) {
			wp_redirect( $redirect );
		}
	}

    private function save_logs( $log_data ) {
        global $wpdb;
        $sql_start   = 'INSERT INTO wp_job_board_log (bh_id, bh_title, action, timestamp) values';
        $insert_data = '';
        $sql_end     = ';';

        foreach ( $log_data as $index => $log_datum ) {
            if ( $index > 0 ) {
                $insert_data .= ',';
            }
            $insert_data .= $wpdb->prepare( '(%d,%s,%s,%d)', array( $log_datum['bh_id'], $log_datum['title'], $log_datum['action'], $log_datum['time'] ) );

            if ( $index > 0 && $index % 20 === 0 ) {
                $wpdb->query( $sql_start . $insert_data . $sql_end );
                $insert_data = '';
            }
        }
        if ( strlen( $insert_data ) ) {
            $wpdb->query( $sql_start . $insert_data . $sql_end );
        }

        $one_week_ago = ( new DateTime() )->sub( new DateInterval( 'P7D' ) )->getTimestamp();

        $wpdb->query( $wpdb->prepare( 'DELETE FROM wp_job_board_log WHERE timestamp < %d', $one_week_ago ) );

    }

	/**
	 * @return string
	 * @throws Exception
	 */
	private function get_access_token(): string {
		$expired = true;
		// If we have an expires date, we should have a token.  If we have both of those
		// things and the expires is not past, return the token.
		if ( isset( $this->options[ self::ACCESS_TOKEN_EXPIRES ] ) ) {
			$expiresDate = DateTime::createFromFormat( self::DATE_FORMAT, $this->options[ self::ACCESS_TOKEN_EXPIRES ] );
			$now         = new DateTime( 'now', new DateTimeZone( 'UTC' ) );
			$expired     = $now > $expiresDate;
		}

		if ( ! $expired && isset( $this->options[ self::ACCESS_TOKEN ] ) ) {
			return $this->options[ self::ACCESS_TOKEN ];
		}

		if ( $expired && isset( $this->options[ self::ACCESS_TOKEN_REFRESH ] ) ) {
			$url = $this->get_url(
				self::ACCESS_TOKEN_REFRESH_ENDPOINT,
				array(
					'{oauth_url}'     => $this->get_oauth_url(),
					'{refresh_token}' => $this->options[ self::ACCESS_TOKEN_REFRESH ],
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

		$token = $this->call_api( $url, array(), 'post' );

		if ( isset( $token['error'] ) ) {
			if ( isset( $token['error_description'] ) && $token['error_description'] === 'Invalid, expired, or revoked authorization code.' ) {
				update_option( WP_Job_Board_Admin::OPTION_ARRAY_KEY, array() );
				$this->throw_error( 'Problem getting authorize token.  Please attempt action again.' );
			}
		}

		$expires = ( new DateTime() )->add( new DateInterval( "PT{$token['expires_in']}S" ) );

		$this->options[ self::ACCESS_TOKEN ]         = $token['access_token'];
		$this->options[ self::ACCESS_TOKEN_EXPIRES ] = $expires->format( self::DATE_FORMAT );
		$this->options[ self::ACCESS_TOKEN_REFRESH ] = $token['refresh_token'];

		update_option( WP_Job_Board_Admin::OPTION_ARRAY_KEY, $this->options );

		return $this->options[ self::ACCESS_TOKEN ];
	}

	/**
	 * @return string
	 */
	private function get_access_code(): string {
		if ( isset( $this->options[ self::ACCESS_CODE ] ) ) {
			return $this->options[ self::ACCESS_CODE ];
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

		$response     = $this->call_api( $url, array( 'redirects' => 0 ), 'get', 'response' );
		$redirect_url = $response['http_response']->get_response_object()->url;
		parse_str( parse_url( $redirect_url )['query'], $output );

		if ( empty( $output['code'] ) ) {
			$this->throw_error( 'Could not retrieve Access Code' );
		}

		$this->options[ self::ACCESS_CODE ] = $output['code'];
		update_option( WP_Job_Board_Admin::OPTION_ARRAY_KEY, $this->options );

		return $this->options[ self::ACCESS_CODE ];
	}


	/**
	 * @return mixed|string
	 */
	private function get_oauth_url() {
		if ( isset( $this->options[ self::OAUTH_URL ] ) ) {
			return $this->options[ self::OAUTH_URL ];
		}

		return $this->get_services( self::OAUTH_URL );
	}

	/**
	 * @param string $service
	 *
	 * @return string
	 */
	private function get_services( string $service ): string {
		$endpoint_url = $this->get_url(
			self::SERVICES_URL,
			array(
				'{API_Username}' => $this->api_username,
			)
		);

		$results = $this->call_api( $endpoint_url );

		if ( empty( $results['oauthUrl'] ) ) {
			$this->throw_error( 'Could not retrieve OAuth Endpoint' );
		}

		$this->options[ self::OAUTH_URL ] = $results['oauthUrl'];
		$this->options[ self::REST_URL ]  = $results['restUrl'];

		update_option( WP_Job_Board_Admin::OPTION_ARRAY_KEY, $this->options );

		return $this->options[ $service ];
	}

	/**
	 * @return mixed|void
	 */
	private function get_jobs() {
        $fields = [
            'id', 'title', 'publicDescription','address', 'publishedCategory', 'dateLastPublished', 'dateLastModified',
        ];

		$baseUrl = '{corpToken}query/JobOrder?fields=id,title,dateAdded&BhRestToken={rest_token}';
        $baseUrl = '{corpToken}search/{entity}?fields={fields}&sort={fields}&count={count}&start={start}&BhRestToken={rest_token}';
        $baseUrl = '{corpToken}search/{entity}?fields={fields}&sort={sort}&count={count}&start={start}&BhRestToken={rest_token}';

        $tokens = array(
            '{corpToken}'  => $this->get_corp_token(),
            '{entity}' => 'JobOrder',
            '{fields}' => implode(',', $fields),
            '{start}' => 0,
            '{count}' => 500,
            '{sort}' => $fields[0],
            '{rest_token}' => $this->options[ self::REST_TOKEN ],
        );

        $callAgain = true;
        $results = [];

        while ($callAgain) {
            $url = $this->get_url(
                $baseUrl,
                $tokens,
            );
            $result = $this->call_api( $url, array( 'body' => array( 'query' => 'isOpen:true and isPublic:true' ) ) );

            if ( isset( $result['errorMessageKey'] ) ) {
                $callAgain = false;
                $this->throw_error( "{$result['errorMessageKey']} - {$result['errorMessage']}" );
            }

            if ( isset( $result['errorMessage']) && isset($result['errorCode'])) {
                $callAgain = false;
                $this->throw_error( "{$result['errorCode']} - {$result['errorMessage']}" );
            }

            if ( isset( $result['message'] ) && $result['message'] === "Bad 'BhRestToken' or timed-out." ) {
                $callAgain = false;
                unset( $this->options[ self::CORP_TOKEN ] );
                unset( $this->options[ self::REST_TOKEN ] );
                $this->trigger_sync();
                return;
            }

            if ( ! isset( $result['data'] ) ) {
                $callAgain = false;
                $this->throw_error( 'Could not sync any jobs.' );
            }

            if (isset($result['total']) && isset($result['start']) && isset($result['count'])) {
                $callAgain = !($result['start'] + $result['count'] >= $result['total']);
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
		if ( ! empty( $this->options[ self::CORP_TOKEN ] ) && ! empty( $this->options[ self::REST_TOKEN ] ) ) {
			$result = $this->call_api( $this->options[ self::CORP_TOKEN ] . 'ping?BhRestToken=' . $this->options[ self::REST_TOKEN ] );
			if ( ! isset( $result['errorCode'] ) ) {
				return $this->options[ self::CORP_TOKEN ];
			}
			unset(
				$this->options[ self::CORP_TOKEN ],
				$this->options[ self::REST_TOKEN ]
			);
		}

		$loginUrl = '{rest_url}/login?version=2.0&access_token={access_token}';
		$url      = $this->get_url(
			$loginUrl,
			array(
				'{rest_url}'     => $this->get_services( self::REST_URL ),
				'{access_token}' => $this->get_access_token(),
			)
		);

		$result = $this->call_api( $url, array(), 'post' );
		if ( isset( $result['errorCode'] ) ) {
			update_option( WP_Job_Board_Admin::OPTION_ARRAY_KEY, array() );
			$this->throw_error( 'Could not login to REST API, please try again' );
		}
		$this->options[ self::REST_TOKEN ] = $result['BhRestToken'];
		$this->options[ self::CORP_TOKEN ] = $result['restUrl'];

		update_option( WP_Job_Board_Admin::OPTION_ARRAY_KEY, $this->options );
		return $this->options[ self::CORP_TOKEN ];
	}

    public function submit_resume() {
        $first_name = $this->get_posted_data('first_name');
        $last_name = $this->get_posted_data('last_name');
        $phone = $this->get_posted_data('phone');
        $email = $this->get_posted_data('email');
        $resume = $_FILES['resume'];

        if (empty($first_name) || empty($last_name) || empty($phone) || empty($email) || empty($resume)) {
            $this->throw_error('Must submit all data (First name, Last name, Phone number, Email address, and Resume');
        }

    }

    private function get_posted_data( $key, $default = null ) {
        if (!isset($_POST[$key])) {
            return $default;
        }

        return $_POST[$key];
    }
}
