<?php
/**
 * Fired during plugin activation
 *
 * @link       http://example.com
 * @since      0.1.0
 *
 * @package    WP_Job_Board
 * @subpackage WP_Job_Board/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      0.1.0
 * @package    WP_Job_Board
 * @subpackage WP_Job_Board/includes
 * @author     Little Fork
 */
class WP_Job_Board_Activator
{
    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since    0.1.0
     */
    public static function activate()
    {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();
        $table_name = $wpdb->prefix . 'job_board_log';

        $sql = "CREATE TABLE $table_name (
                id    bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                bh_id    bigint(20) UNSIGNED NOT NULL,
                bh_title varchar(255) NOT NULL,
                action   varchar(255) NOT NULL,
                timestamp bigint(20) unsigned NOT NULL,
                delta text NULL,
                PRIMARY KEY  (id)
                ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);
        update_option(WP_Job_Board_Admin::SETTING_PLUGIN_VERSION, WP_JOB_BOARD_VERSION);
    }
}
