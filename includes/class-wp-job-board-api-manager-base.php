<?php
/**
 * Base class for (hopefully) future api managers
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    WP_Job_Board
 * @subpackage WP_Job_Board/includes
 */

/**
 * Class holds some common helper methods.
 *
 * @since      1.0.0
 * @package    WP_Job_Board
 * @subpackage WP_Job_Board/includes
 * @author     Drew Brown <dbrown78@gmail.com>
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
	protected function get_url( $url, $tokens ): string {
		$built_url = str_replace( array_keys( $tokens ), array_values( $tokens ), $url );

		if ( is_array( $built_url ) ) {
			return $built_url[0];
		}

		return $built_url;
	}

	protected function call_api( string $endpoint_url, array $args = array(), string $method = 'get', string $return = 'body', bool $json_decode = true ) {
		$func = "wp_remote_{$method}";
		if ( ! function_exists( $func ) ) {
			$this->throw_error( "Could not call {$method}" );
		}
		$response = $func( $endpoint_url, $args );
		$code     = wp_remote_retrieve_response_code( $response );
		$message  = wp_remote_retrieve_response_message( $response );
		if ( $response instanceof WP_Error ) {
			$this->throw_error( "Could not reach {$endpoint_url} - {$code} - {$message}" );
		}

		$body    = wp_remote_retrieve_body( $response );
		$headers = wp_remote_retrieve_headers( $response );
		$cookies = wp_remote_retrieve_cookies( $response );

		if ( $return === 'body' ) {
			if ( $json_decode ) {
				$decoded = json_decode( $body, true );
				if ( ! $decoded ) {
					$decoded = array();
				}
				return $decoded;
			}
			return $body;
		}

		return $response;
	}

	protected function throw_error( string $message ) {
		$class = $this::class;
		throw new Error( "WP Job Board - {$class} - Message: {$message}" );
	}
}
