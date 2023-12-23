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

    if (file_exists(plugin_dir_path(__DIR__) . 'public/partials/wp-job-board-archive.php')) {
        if (is_archive() && get_post_type() == 'wjb_bh_job_order') {
            ob_start();
            include(plugin_dir_path(__DIR__) . 'public/partials/wp-job-board-archive.php');
            $html .= ob_get_clean();
        } else {
            $html = '<div style="color: red; font-weight: bold; font-size: 36px;">The wpjb_archive shortcode must be placed on an "archive" page.</div>';
        }
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

    if (file_exists(plugin_dir_path(__DIR__) . 'public/partials/wp-job-board-single.php')) {
        if (is_single() && get_post_type() == 'wjb_bh_job_order') {
            ob_start();
            include(plugin_dir_path(__DIR__) . 'public/partials/wp-job-board-single.php');
            $html .= ob_get_clean();
        } else {
            $html = '<div style="color: red; font-weight: bold; font-size: 36px;">The wpjb_single shortcode must be placed on an WordPress "single" page.</div>';
        }
    }
    return $html;
}

add_shortcode('wpjb_single', 'wpjb_single');
