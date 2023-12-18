<?php
/**
 * Base class for (hopefully) future api managers
 *
 * @link       http://example.com
 * @since      0.1.0
 *
 * @package    WP_Job_Board
 * @subpackage WP_Job_Board/includes
 */

/**
 * Class holds some common helper methods.
 *
 * @since      0.1.0
 * @package    WP_Job_Board
 * @subpackage WP_Job_Board/includes
 * @author     Little Fork
 */
class WP_Job_Board_API_Manager_Base {

    /**
     * Helper function to replace tokens `{token_name}` with values from
     * a token_name => value array;
     *
     * @param $url
     * @param $tokens
     *
     * @return string
     */
    protected function get_url($url, $tokens): string {
        $built_url = str_replace(array_keys($tokens), array_values($tokens), $url);

        if (is_array($built_url)) {
            return $built_url[0];
        }

        return $built_url;
    }

    protected function call_api(string $endpoint_url, array $args = array(), string $method = 'get', string $return = 'body', bool $json_decode = true) {
        $func = "wp_remote_{$method}";
        if ( ! function_exists($func)) {
            $this->throw_error("Could not call {$method}");
        }
        if ( ! empty($args['body']) && is_array($args['body']) && empty($args['skip_json'])) {
            $args['body'] = json_encode($args['body']);
            if (empty($args['headers'])) {
                $args['headers'] = [];
            }
            if (empty($args['headers']['Content-Type'])) {
                $args['headers']['Content-Type'] = 'application/json';
            }
        }
        $response = $func($endpoint_url, $args);
        $code     = wp_remote_retrieve_response_code($response);
        $message  = wp_remote_retrieve_response_message($response);
        if ($response instanceof WP_Error) {
            $this->throw_error("Could not reach {$endpoint_url} - {$code} - {$message}");
        }

        $body    = wp_remote_retrieve_body($response);
        $headers = wp_remote_retrieve_headers($response);
        $cookies = wp_remote_retrieve_cookies($response);

        if ($return === 'body') {
            if ($json_decode) {
                $decoded = json_decode($body, true);
                if ( ! $decoded) {
                    $decoded = array();
                }

                return $decoded;
            }

            return $body;
        }

        return $response;
    }

    protected function throw_error(string $message) {
        $class = $this::class;
        throw new Error("WP Job Board - {$class} - Message: {$message}");
    }

    protected function get_mapped_state(string $state): string {
        $abbrev = strtoupper($state);
        if (isset(self::$state_map[$abbrev])) {
            return self::$state_map[$abbrev];
        }
        return $state;
    }

    public static array $state_map = array(
        'AL' => 'Alabama',
        'AK' => 'Alaska',
        'AZ' => 'Arizona',
        'AR' => 'Arkansas',
        'CA' => 'California',
        'CO' => 'Colorado',
        'CT' => 'Connecticut',
        'DE' => 'Delaware',
        'DC' => 'District of Columbia',
        'FL' => 'Florida',
        'GA' => 'Georgia',
        'HI' => 'Hawaii',
        'ID' => 'Idaho',
        'IL' => 'Illinois',
        'IN' => 'Indiana',
        'IA' => 'Iowa',
        'KS' => 'Kansas',
        'KY' => 'Kentucky',
        'LA' => 'Louisiana',
        'ME' => 'Maine',
        'MD' => 'Maryland',
        'MA' => 'Massachusetts',
        'MI' => 'Michigan',
        'MN' => 'Minnesota',
        'MS' => 'Mississippi',
        'MO' => 'Missouri',
        'MT' => 'Montana',
        'NE' => 'Nebraska',
        'NV' => 'Nevada',
        'NH' => 'New Hampshire',
        'NJ' => 'New Jersey',
        'NM' => 'New Mexico',
        'NY' => 'New York',
        'NC' => 'North Carolina',
        'ND' => 'North Dakota',
        'OH' => 'Ohio',
        'OK' => 'Oklahoma',
        'OR' => 'Oregon',
        'PA' => 'Pennsylvania',
        'RI' => 'Rhode Island',
        'SC' => 'South Carolina',
        'SD' => 'South Dakota',
        'TN' => 'Tennessee',
        'TX' => 'Texas',
        'UT' => 'Utah',
        'VT' => 'Vermont',
        'VI' => 'Virgin Islands',
        'VA' => 'Virginia',
        'WA' => 'Washington',
        'WV' => 'West Virginia',
        'WI' => 'Wisconsin',
        'WY' => 'Wyoming',
        'AS' => 'American Samoa',
        'AE' => 'Armed Forces - Europe',
        'AP' => 'Armed Forces - Pacific',
        'AA' => 'Armed Forces - USA/Canada',
        'FM' => 'Federated States of Micronesia',
        'GU' => 'Guam',
        'MH' => 'Marshall Islands',
        'PR' => 'Puerto Rico',
    );
}
