<?php
/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       http://example.com
 * @since      0.1.0
 *
 * @package    WP_Job_Board
 * @subpackage WP_Job_Board/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      0.1.0
 * @package    WP_Job_Board
 * @subpackage WP_Job_Board/includes
 * @author     Little Fork
 */
class WP_Job_Board_I18n
{
    /**
     * Load the plugin text domain for translation.
     *
     * @since    0.1.0
     */
    public function load_plugin_textdomain()
    {
        load_plugin_textdomain(
            'wp-job-board',
            false,
            dirname(plugin_basename(__FILE__), 2) . '/languages/'
        );
    }
}
