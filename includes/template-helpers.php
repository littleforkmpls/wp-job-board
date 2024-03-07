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
 * Helper for generating a set of checkboxes for all terms within a specific taxonomy
 *
 * @return string HTML
 */
function get_taxonomy_filters($taxonomy = false, $current_term_id = 0)
{
    if (empty($taxonomy)) {
        return;
    }

    $terms = get_terms(
        array(
            'taxonomy'   => $taxonomy,
            'orderby' => 'name',
            'order' => 'ASC',
            'hide_empty' => true,
        )
    );

    if (is_wp_error($terms) || (count($terms) < 1 )) {
        return;
    }

    $output = '<ul class="wpjb-facet__section__list">';

    foreach ($terms as $term) {
        $term_id        = esc_attr($term->term_id);
        $term_name      = esc_html($term->name);
        $term_checked   = ($current_term_id == $term_id) ? 'checked' : '';

        $checkbox_name = $taxonomy . '[]';

        $output .= "
            <li>
                <label>
                    <input type='checkbox' name='{$checkbox_name}' value='{$term_id}' {$term_checked} />
                    {$term_name}
                </label>
            </li>
        ";
    }

    $output .= '</ul>';

    return $output;
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

/**
 * Helper for rendering a choice field in the admin
 * @param $args
 *
 * @return void
 */
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

    $output = "<select name='{$name}' class='regular-text'>";

    foreach ($args['choices'] as $key => $choice) {
        $selected = $value === $key ? 'selected' : '';
        $output   .= "<option value='{$key}' {$selected}>{$choice}</option>";
    }

    $output .= '</select>';

    echo $output;
}

/**
 * Helper for rendering a dropdown field in the admin
 * @param $args
 *
 * @return void
 */
function render_dropdown_field($args)
{
    $defaults = array(
        'type'    => 'select',
        'name'    => '',
        'options' => array(),
    );

    $args = array_merge($defaults, $args);

    if (empty($args['name']) || empty($args['options'])) {
        return;
    }

    $name  = esc_attr($args['name']);
    $value = esc_attr(get_option($args['name']));

    $output = "<select name='{$name}' class='regular-text'>";

    foreach ($args['options'] as $key => $option) {
        $selected = $value === $key ? 'selected' : '';
        $output   .= "<option value='{$key}' {$selected}>{$option}</option>";
    }

    $output .= '</select>';

    echo $output;
}
