<?php
/**
 * WP Job Board
 *
 * @package   LittleFork/WPJobBoard
 * @author    Little Fork
 * @copyright 2023 Little Fork
 * @license   GPL v2 or later
 *
 * @wordpress-plugin
 * Plugin Name:       WP Job Board
 * Plugin URI:        https://little-fork.com/
 * Description:       A plugin to pull and list jobs from Bullhorn(and other sites in the future).
 * Version:           0.1.1
 * Requires at least: 6.0
 * Requires PHP:      8
 * Author:            Little Fork
 * Author             URI: https://little-fork.com/
 * License:           GPL v2 or later
 */

// If this file is called directly, abort.
if ( ! defined('WPINC')) {
    die;
}
/**
 * Currently plugin version.
 */
define('WP_JOB_BOARD_VERSION', '0.1.0');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wp-job-board-activator.php
 */
function activate_wp_job_board() {
    require_once plugin_dir_path(__FILE__) . 'includes/class-wp-job-board-activator.php';
    WP_Job_Board_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wp-job-board-deactivator.php
 */
function deactivate_wp_job_board() {
    require_once plugin_dir_path(__FILE__) . 'includes/class-wp-job-board-deactivator.php';
    WP_Job_Board_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_wp_job_board');
register_deactivation_hook(__FILE__, 'deactivate_wp_job_board');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-wp-job-board.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    0.1.0
 */
function run_wp_job_board() {

    $plugin = new WP_Job_Board();
    $plugin->run();
}

run_wp_job_board();
