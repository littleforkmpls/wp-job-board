<?php

/**
 * WP Shortcodes for use with WP Job Board
 */

/**
 * Shortcode for displaying job archive partial on a WP Archive page
 *
 * @since  0.1.8
 * @return string JSON
 */
function wpjb_archive($attr)
{
    $html = '';

    if (file_exists(plugin_dir_path(__DIR__) . 'public/pages/wp-job-board-archive.php')) {
        ob_start();
        include(plugin_dir_path(__DIR__) . 'public/pages/wp-job-board-archive.php');
        $html .= ob_get_clean();
    }

    return $html;
}

add_shortcode('wpjb_archive', 'wpjb_archive');

/**
 * Shortcode for displaying job single partial on a WP Archive page
 *
 * @since  0.1.8
 * @return string JSON
 */
function wpjb_single($attr)
{
    $html = '';

    if (file_exists(plugin_dir_path(__DIR__) . 'public/pages/wp-job-board-single.php')) {
        ob_start();
        include(plugin_dir_path(__DIR__) . 'public/pages/wp-job-board-single.php');
        $html .= ob_get_clean();
    }

    return $html;
}

add_shortcode('wpjb_single', 'wpjb_single');
