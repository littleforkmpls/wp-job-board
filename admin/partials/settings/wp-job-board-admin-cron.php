<?php

add_settings_section(
    WP_Job_Board_Admin::SETTINGS_CRON_SECTION,
    '',
    false,
    WP_Job_Board_Admin::PAGE_SLUG
);

add_settings_field(
    WP_Job_Board_Admin::SETTING_ENABLE_CRON,
    __('Enable Cron', 'wp_job_board'),
    'render_checkbox_field',
    WP_Job_Board_Admin::PAGE_SLUG,
    WP_Job_Board_Admin::SETTINGS_CRON_SECTION,
    array(
        'name' => WP_Job_Board_Admin::SETTING_ENABLE_CRON,
    )
);

add_settings_field(
    WP_Job_Board_Admin::SETTING_CRON_CADENCE,
    __('Cadence', 'wp_job_board'),
    'render_choice_field',
    WP_Job_Board_Admin::PAGE_SLUG,
    WP_Job_Board_Admin::SETTINGS_CRON_SECTION,
    array(
        'name'    => WP_Job_Board_Admin::SETTING_CRON_CADENCE,
        'choices' => array(
            ''     => 'Please select a cadence',
            '5m'   => '5 Minutes',
            '30m'  => '30 Minutes',
            '60m'  => '60 Minutes',
            '120m' => '120 Minutes',
        )
    )
);
$scheduled = wp_next_scheduled(WP_Job_Board_Admin::CRON_SYNC_JOBS);
?>

<div class="wpjba-grid">
    <div class="wpjba-grid__item">
        <div class="wpjba-card">
            <div class="wpjba-card__hd">
                <h3 class="wpjba-p0 wpjba-m0">Cron Settings</h3>
            </div>
            <div class="wpjba-card__bd">
                <p class="wpjba-p0 wpjba-m0">Configure the frequency new Job Orders are synced from the plaform.</p>
            </div>
            <div class="wpjba-card__ft">
                <form method="post" action="options.php">
                    <?php
                    settings_fields(WP_Job_Board_Admin::SETTINGS_GROUP_CRON);
                    do_settings_sections(WP_Job_Board_Admin::PAGE_SLUG);
                    ?>
                    <?php submit_button(); ?>
                </form>
                <p><strong>Next scheduled run:</strong> <?= $scheduled ? date('h:ia', $scheduled) : 'not scheduled' ?></p>
            </div>
        </div>
    </div>
</div>
