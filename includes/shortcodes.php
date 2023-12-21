<?php

/**
 * WP Shortcodes for use with WP Job Board
 */

function wpjb_archive($attr)
{
    $html = '';

    if (file_exists(plugin_dir_path(__DIR__) . 'public/partials/wp-job-board-archive.php')) {
        ob_start();
        include(plugin_dir_path(__DIR__) . 'public/partials/wp-job-board-archive.php');
        $html .= ob_get_clean();
    }
    return $html;
}

add_shortcode('wpjb_archive', 'wpjb_archive');

function wpjb_single($attr)
{
    $html = '';

    if (file_exists(plugin_dir_path(__DIR__) . 'public/partials/wp-job-board-single.php')) {
        ob_start();
        include(plugin_dir_path(__DIR__) . 'public/partials/wp-job-board-single.php');
        $html .= ob_get_clean();
    }
    return $html;
}

add_shortcode('wpjb_single', 'wpjb_single');
