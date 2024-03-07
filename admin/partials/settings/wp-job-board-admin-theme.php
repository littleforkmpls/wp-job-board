<?php

add_settings_section(
    WP_Job_Board_Admin::SETTINGS_SECTION_UI,
    '',
    false,
    WP_Job_Board_Admin::PAGE_SLUG
);

add_settings_field(
    WP_Job_Board_Admin::SETTING_UI_TEMPLATE,
    __('Select a Template', 'wp_job_board'),
    'render_dropdown_field',
    WP_Job_Board_Admin::PAGE_SLUG,
    WP_Job_Board_Admin::SETTINGS_SECTION_UI,
    array(
        'name'    => WP_Job_Board_Admin::SETTING_UI_TEMPLATE,
        'options' => array(
            'default'   => 'Default Template',
            'custom'    => 'Custom Template',
        )
    )
);
?>

<div class="wpjba-grid">
    <div class="wpjba-grid__item">
        <div class="wpjba-card">
            <div class="wpjba-card__hd">
                <h3 class="wpjba-p0 wpjba-m0">Interface Settings</h3>
            </div>
            <div class="wpjba-card__bd">
                <p class="wpjba-p0 wpjba-m0">Configure the look & feel of the job board.</p>
            </div>
            <div class="wpjba-card__ft">
                <form method="post" action="options.php">
                    <?php
                    settings_fields(WP_Job_Board_Admin::SETTINGS_GROUP_UI);
                    do_settings_sections(WP_Job_Board_Admin::PAGE_SLUG);
                    ?>
                    <?php submit_button(); ?>
                </form>
            </div>
        </div>
    </div>
</div>
