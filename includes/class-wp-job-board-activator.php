<?php
/**
 * Fired during plugin activation
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    WP_Job_Board
 * @subpackage WP_Job_Board/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    WP_Job_Board
 * @subpackage WP_Job_Board/includes
 * @author     Your Name <email@example.com>
 */
class WP_Job_Board_Activator {


	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		$sql = 'CREATE TABLE IF NOT EXISTS wp_job_board_log(
    id    bigint unsigned auto_increment primary key,
    bh_id    bigint unsigned not null,
    bh_title varchar(255) not null,
    action   varchar(255) not null,
    timestamp bigint unsigned not null
);';
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
	}
}
