<?php
/**
 * Provide the Settings section of the admin area view for the plugin
 *
 * @package    WP_Job_Board
 * @subpackage WP_Job_Board/admin/partials
 */
 ?>


<?php
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

    function render_input_field($args) {
        $defaults = array(
            'type' => 'text',
            'name' => '',
        );
        $args     = array_merge($defaults, $args);
        if (empty($args['name'])) {
            return;
        }
        $value = get_option($args['name']);
        ?>
        <input type="<?php echo esc_attr($args['type']); ?>"
               name="<?php echo esc_attr($args['name']); ?>"
               value="<?php echo esc_attr($value); ?>"
               class="regular-text"
        />
        <?php if ( ! empty($args['description'])) : ?>
            <p class="description"><?php echo wp_kses_post($args['description']); ?></p>
        <?php
        endif;
    }

    function render_checkbox_field($args) {
        $defaults = array(
            'type' => 'checkbox',
            'name' => '',

        );
        $args = array_merge($defaults, $args);
        if (empty($args['name'])) {
            return;
        }
        echo '<input type="checkbox" name="'.$args['name'].'" value="1" ' . checked( 1, get_option( $args['name'] ), false ) . ' />';
    }

?>

<form method="post" action="options.php">
    <?php
        settings_fields(WP_Job_Board_Admin::SETTINGS_GROUP);
        do_settings_sections(WP_Job_Board_Admin::PAGE_SLUG);
    ?>
    <?php submit_button(); ?>
</form>
<div>
    <button type="button" id="wp_job_board_trigger_sync" class="button button-secondary">Trigger Sync</button>
    <span class="spinner"></span>
</div>
