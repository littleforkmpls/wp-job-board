<?php

/**
 * Template Helpers for use with WP Job Board
 */

/**
 * Helper for getting Job Meta Data
 *
 * @since  0.1.7
 * @return string JSON
 */
function get_job_meta($id)
{
    $metadata = get_post_meta($id, 'wp_job_board_bh_data', true);
    $metadata_decoded = json_decode($metadata);

    return $metadata_decoded;
}

/**
 * Helper for converting timestamps to a formatted date
 *
 * @return string
 */
function get_formatted_date($timestamp, $milliseconds = true)
{
    if ($milliseconds == true) {
        $timestamp = floor($timestamp / 1000);
    }

    $formatted_date = date('F j, Y', $timestamp);

    return $formatted_date;
}

/**
 * Helper for converting timestamps to a relative date
 *
 * https://css-tricks.com/snippets/php/time-ago-function/
 *
 * @return string
 */
function get_relative_date($timestamp, $milliseconds = true)
{
    if ($milliseconds == true) {
        $timestamp = floor($timestamp / 1000);
    }

    $periods   = array('second', 'minute', 'hour', 'day', 'week', 'month', 'year', 'decade');
    $lengths   = array('60', '60', '24', '7', '4.35', '12', '10');

    $now = time();

    $difference     = $now - $timestamp;
    $tense          = 'ago';

    for ($j = 0; $difference >= $lengths[$j] && $j < count($lengths) - 1; $j++) {
        $difference /= $lengths[$j];
    }

    $difference = round($difference);

    if ($difference != 1) {
        $periods[$j] .= 's';
    }

    $relative_date = $difference . ' ' . $periods[$j] . ' ' . $tense;

    return $relative_date;
}
