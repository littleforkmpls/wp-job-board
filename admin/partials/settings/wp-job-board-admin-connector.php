<?php
/**
 * Provide the Connector Settings section of the admin area view for the plugin
 *
 * @package    WP_Job_Board
 * @subpackage WP_Job_Board/admin/partials
 */

add_settings_section(
    WP_Job_Board_Admin::SETTINGS_SECTION,
    'Bullhorn Settings',
    false,
    WP_Job_Board_Admin::PAGE_SLUG
);

add_settings_field(
    WP_Job_Board_Admin::SETTING_CLIENT_ID,
    __('Client ID', 'wp_job_board'),
    'render_input_field',
    WP_Job_Board_Admin::PAGE_SLUG,
    WP_Job_Board_Admin::SETTINGS_SECTION,
    array(
        'name' => WP_Job_Board_Admin::SETTING_CLIENT_ID,
    )
);

add_settings_field(
    WP_Job_Board_Admin::SETTING_CLIENT_SECRET,
    __('Client Secret', 'wp_job_board'),
    'render_input_field',
    WP_Job_Board_Admin::PAGE_SLUG,
    WP_Job_Board_Admin::SETTINGS_SECTION,
    array(
        'name' => WP_Job_Board_Admin::SETTING_CLIENT_SECRET,
    )
);

add_settings_field(
    WP_Job_Board_Admin::SETTING_API_USERNAME,
    __('API Username', 'wp_job_board'),
    'render_input_field',
    WP_Job_Board_Admin::PAGE_SLUG,
    WP_Job_Board_Admin::SETTINGS_SECTION,
    array(
        'name' => WP_Job_Board_Admin::SETTING_API_USERNAME,
    )
);

add_settings_field(
    WP_Job_Board_Admin::SETTING_API_PASSWORD,
    __('API Password', 'wp_job_board'),
    'render_input_field',
    WP_Job_Board_Admin::PAGE_SLUG,
    WP_Job_Board_Admin::SETTINGS_SECTION,
    array(
        'name' => WP_Job_Board_Admin::SETTING_API_PASSWORD,
    )
);

add_settings_field(
    WP_Job_Board_Admin::SETTING_ENABLE_CRON,
    __('Enable Cron', 'wp_job_board'),
    'render_checkbox_field',
    WP_Job_Board_Admin::PAGE_SLUG,
    WP_Job_Board_Admin::SETTINGS_SECTION,
    array(
        'name' => WP_Job_Board_Admin::SETTING_ENABLE_CRON,
    )
);

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

?>

<form method="post" action="options.php">
    <?php
        settings_fields(WP_Job_Board_Admin::SETTINGS_GROUP);
        do_settings_sections(WP_Job_Board_Admin::PAGE_SLUG);
    ?>
    <?php submit_button(); ?>
</form>
