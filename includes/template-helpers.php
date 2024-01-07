<?php

/**
 * Template Helpers for use with WP Job Board
 */

/**
 * Helper for getting Job Meta Data
 *
 * @since  0.1.8
 * @return string JSON
 */
function get_job_meta($id)
{
    $metadata = get_post_meta($id, 'wjb_bh_data', true);
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

/**
 * Helper for converting timestamps to a ISO8691 formatted date
 *
 * @return string
 */
function get_iso8601_date($timestamp, $milliseconds = true)
{
    if ($milliseconds == true) {
        $timestamp = floor($timestamp / 1000);
    }

    $iso8601_date = date('c', $timestamp);

    return $iso8601_date;
}

/**
 * Helper for getting full list of terms for job filters
 *
 * @return array
 */
function get_filter_terms($taxonomy)
{
    $terms = get_terms(
        array(
            'taxonomy'   => $taxonomy,
            'orderby' => 'name',
            'order' => 'ASC',
            'hide_empty' => true,
        )
    );

    // if get_terms returns an error or no terms return an empty string
    if (is_wp_error($terms) || (count($terms) < 1 )) {
        $terms = '';
    }

    return $terms;
}

/**
 * Helper for rendering an input field in the admin
 *
 * @param $args
 *
 * @return void
 */
function render_input_field($args)
{
    $defaults = array(
        'type' => 'text',
        'name' => '',
    );

    $args     = array_merge($defaults, $args);

    if (empty($args['name'])) {
        return;
    }

    $value = esc_attr(get_option($args['name']));
    $type  = esc_attr($args['type']);
    $name  = esc_attr($args['name']);

    $output = "<input type='{$type}' name='{$name}' value='{$value}' class='regular-text' />";

    if (!empty($args['description'])) {
        $desc = wp_kses_post($args['description']);

        $output .= "<p class='description'>{$desc}</p>";
    }

    echo $output;
}

/**
 * Helper for rendering a checkbox field in the admin
 * @param $args
 *
 * @return void
 */
function render_checkbox_field($args)
{
    $defaults = array(
        'type' => 'checkbox',
        'name' => '',
    );

    $args = array_merge($defaults, $args);

    if (empty($args['name'])) {
        return;
    }

    $name    = esc_attr($args['name']);
    $checked = checked(1, get_option($args['name']), false);

    $output = "<input type='checkbox' name='{$name}' value='1' {$checked} />";

    echo $output;
}

function render_choice_field($args)
{
    $defaults = array(
        'type'    => 'checkbox',
        'name'    => '',
        'choices' => array(),
    );

    $args = array_merge($defaults, $args);

    if (empty($args['name']) || empty($args['choices'])) {
        return;
    }

    $name  = esc_attr($args['name']);
    $value = esc_attr(get_option($args['name'], '30m'));

    $output = "<select name='{$name}'>";

    foreach ($args['choices'] as $key => $choice) {
        $selected = $value === $key ? 'selected':'';
        $output   .= "<option value='{$key}' {$selected}>{$choice}</option>";
    }

    $output .= '</select>';

    echo $output;
}

