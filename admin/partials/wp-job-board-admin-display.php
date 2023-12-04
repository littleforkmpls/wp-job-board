<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://example.com
 * @since      0.1.0
 *
 * @package    WP_Job_Board
 * @subpackage WP_Job_Board/admin/partials
 */


?>
    <div class="wrap" id="wp_job_board_admin">
        <?php
        require_once plugin_dir_path(__FILE__) . 'wp-job-board-settings-menu.php'
        ?>
        <div id="wp_job_board_settings" class="wp_job_board_tab">
            <?php require_once plugin_dir_path(__FILE__) . 'wp-job-board-settings.php' ?>
        </div>
    </div>
<?php
