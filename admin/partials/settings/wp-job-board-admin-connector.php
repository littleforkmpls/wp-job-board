<?php
/**
 * Provide the Connector Settings section of the admin area view for the plugin
 *
 * @package    WP_Job_Board
 * @subpackage WP_Job_Board/admin/partials
 */

add_settings_section(
    WP_Job_Board_Admin::SETTINGS_SECTION,
    '',
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
<div class="wpjba-grid">
    <div class="wpjba-grid__item">
        <div class="wpjba-card">
            <div class="wpjba-card__hd">
                <h3 class="wpjba-p0 wpjba-m0">Bullhorn Settings</h3>
            </div>
            <div class="wpjba-card__bd">
                <p class="wpjba-p0 wpjba-m0">These options can be found in your Bullhorn account.</p>
            </div>
            <div class="wpjba-card__ft">
                <form id="wpjba-connection-form" method="post" action="options.php">
                    <?php
                        settings_fields(WP_Job_Board_Admin::SETTINGS_GROUP);
                        do_settings_sections(WP_Job_Board_Admin::PAGE_SLUG);
                    ?>
                    <div class="wpjba-btnGroup">
                        <div class="wpjba-btnGroup__item">
                            <?php
                                submit_button(
                                    'Save Changes',
                                    'primary',
                                    'submit',
                                    false,
                                    null
                                );
                            ?>
                        </div>
                        <div class="wpjba-btnGroup__item">
                            <button
                                type="button"
                                class="button button-secondary"
                                data-wpjb-ajax="test_connection"
                                data-wpjb-ajax-form="wpjba-connection-form"
                            >
                                Test Connection
                            </button>
                            <span class="spinner" aria-hidden="true"></span>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
