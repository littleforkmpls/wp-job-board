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
?>

<form method="post" action="options.php">
    <?php
        settings_fields(WP_Job_Board_Admin::SETTINGS_GROUP);
        do_settings_sections(WP_Job_Board_Admin::PAGE_SLUG);
    ?>
    <?php submit_button(); ?>
</form>
