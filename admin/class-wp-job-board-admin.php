<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    WP_Job_Board
 * @subpackage WP_Job_Board/admin
 * @author     Little Fork
 */
class WP_Job_Board_Admin
{
    /**
     * The following are handled through the Settings API and are
     * client facing on the admin site.
     */
    public const SETTINGS_GROUP         = 'wp_job_board_settings_group';
    public const SETTINGS_GROUP_CRON    = 'wp_job_board_settings_group_cron';
    public const SETTINGS_SECTION       = 'wp_job_board_settings_section';
    public const SETTINGS_SECTION_CRON  = 'wp_job_board_settings_section_cron';
    public const SETTING_CLIENT_ID      = 'wp_job_board_client_id';
    public const SETTING_CLIENT_SECRET  = 'wp_job_board_client_secret';
    public const SETTING_API_USERNAME   = 'wp_job_board_api_username';
    public const SETTING_API_PASSWORD   = 'wp_job_board_api_password';
    public const SETTING_ENABLE_CRON    = 'wp_job_board_cron_enable';
    public const SETTING_CRON_CADENCE   = 'wp_job_board_cron_cadence';
    public const SETTING_PLUGIN_VERSION = 'wp_job_board_version';

    /**
     * The following are handled in an Options Object(the first const)
     * and are handled by the plugin alone.
     */
    public const OPTION_ARRAY_KEY = 'wp_job_board_options';
    public const PAGE_SLUG        = 'wp_job_board';
    public const CRON_SYNC_JOBS   = 'wp_job_board_sync_jobs_cron';

    /**
     * The ID of this plugin.
     *
     * @since    0.1.0
     * @access   private
     * @var      string $wp_job_board The ID of this plugin.
     */
    private $wp_job_board;

    /**
     * The version of this plugin.
     *
     * @since    0.1.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;

    /**
     * Bullhorn Manager Instance
     *
     * @since  0.1.0
     * @access private
     * @var WP_Job_Board_Bullhorn_Manager $bullhorn Bullhorn Manager Instance
     */
    private $bullhorn;

    /**
     * Initialize the class and set its properties.
     *
     * @param string $wp_job_board The name of this plugin.
     * @param string $version The version of this plugin.
     *
     * @since    0.1.0
     */
    public function __construct($wp_job_board, $version)
    {
        $this->wp_job_board = $wp_job_board;
        $this->version      = $version;
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    0.1.0
     */
    public function enqueue_styles()
    {
        wp_enqueue_style(
            $this->wp_job_board,
            plugin_dir_url(__FILE__) . 'css/wp-job-board-admin.css',
            array(),
            filemtime(plugin_dir_path(__FILE__) . 'css/wp-job-board-admin.css'),
            'all'
        );
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    0.1.0
     */
    public function enqueue_scripts()
    {
        wp_enqueue_script(
            $this->wp_job_board,
            plugin_dir_url(__FILE__) . 'js/wp-job-board-admin.js',
            array('jquery'),
            filemtime(plugin_dir_path(__FILE__) . 'js/wp-job-board-admin.js'),
            true
        );
    }

    /**
     * Register settings.
     *
     * @since    0.1.0
     */
    public function register_settings()
    {
        register_setting(
            self::SETTINGS_GROUP,
            self::SETTING_CLIENT_ID,
            array(
                'sanatize_callback' => 'sanitize_text_field',
            )
        );
        register_setting(
            self::SETTINGS_GROUP,
            self::SETTING_CLIENT_SECRET,
            array(
                'sanatize_callback' => 'sanitize_text_field',
            )
        );
        register_setting(
            self::SETTINGS_GROUP,
            self::SETTING_API_USERNAME,
            array(
                'sanatize_callback' => 'sanitize_text_field',
            )
        );
        register_setting(
            self::SETTINGS_GROUP,
            self::SETTING_API_PASSWORD,
            array(
                'sanatize_callback' => 'sanitize_text_field',
            )
        );
        register_setting(
            self::SETTINGS_GROUP_CRON,
            self::SETTING_ENABLE_CRON,
            array()
        );
        register_setting(
            self::SETTINGS_GROUP_CRON,
            self::SETTING_CRON_CADENCE,
        );
    }

    /**
     * Register Settings & create admin menu pages.
     *
     * @since    0.1.0
     */
    public function build_admin_menu()
    {
        add_submenu_page(
            'edit.php?post_type=wjb_bh_job_order',
            'WP Job Board Tools',
            'Tools',
            'manage_options',
            'wp-job-board-tools',
            array($this, 'render_tools_page')
        );

        add_submenu_page(
            'edit.php?post_type=wjb_bh_job_order',
            'WP Job Board Settings',
            'Settings',
            'manage_options',
            'wp-job-board-settings',
            array($this, 'render_settings_page')
        );

        remove_submenu_page(
            'edit.php?post_type=wjb_bh_job_order',
            'post-new.php?post_type=wjb_bh_job_order'
        );
    }

    public function render_settings_page()
    {
        require_once plugin_dir_path(__FILE__) . 'pages/wp-job-board-admin-settings.php';
    }

    public function render_tools_page()
    {
        require_once plugin_dir_path(__FILE__) . 'pages/wp-job-board-admin-tools.php';
    }

    public function trigger_sync()
    {
        try {
            if (!$this->bullhorn) {
                $this->bullhorn = new WP_Job_Board_Bullhorn_Manager();
            }
            $force = false;
            if (isset($_POST['force']) && $_POST['force'] === 'true') {
                $force = true;
            }
            $this->bullhorn->trigger_sync(null, $force);
            wp_send_json_success(array('message' => 'Job data synced successfully.',));
        } catch (Throwable $exception) {
            wp_send_json_error(array('message' => $exception->getMessage()));
        }
    }

    public function test_connection() {
        try {
            if (!$this->bullhorn) {
                $this->bullhorn = new WP_Job_Board_Bullhorn_Manager($_POST);
            }
            $this->bullhorn->test_connection();
            wp_send_json_success(array('message' => 'Connected successfully.',));
        } catch (Throwable $exception) {
            wp_send_json_error(array('message' => $exception->getMessage()));
        }
    }

    public function clear_logs()
    {
        global $wpdb;

        $wpdb->query($wpdb->prepare('TRUNCATE TABLE wp_job_board_log'));
        wp_send_json_success(array('message' => 'Logs cleared successfully.'));
    }

    public function add_cron()
    {
        // Get our data
        $timestamp = wp_next_scheduled(WP_Job_Board_Admin::CRON_SYNC_JOBS);
        $enabled = get_option(WP_Job_Board_Admin::SETTING_ENABLE_CRON);
        $unset = false;
        $cadence = get_option(WP_Job_Board_Admin::SETTING_CRON_CADENCE);
        $should_happen = strtotime('now')+((int)str_replace('m', '', $cadence)*60);

        // If we are scheduled too far into the future, or we are not enabled mark to unset.
        if ($timestamp) {
            $unset = ($timestamp > $should_happen) || !$enabled;
        }

        // If we need to unset do so.
        if ($unset) {
            wp_clear_scheduled_hook(WP_Job_Board_Admin::CRON_SYNC_JOBS);
        }

        // If we need to schedule, do so.
        if ($enabled && !$timestamp) {
            $scheduled = wp_schedule_event($should_happen, $cadence, WP_Job_Board_Admin::CRON_SYNC_JOBS);
        }
    }

    public function add_cron_intervals($schedules)
    {
        $schedules['5m'] = array(
            'interval' => 300,
            'display'  => esc_html__('Every 5 minutes'),
        );
        $schedules['30m'] = array(
            'interval' => 1800,
            'display'  => esc_html__('Every 30 minutes'),
        );
        $schedules['60m'] = array(
            'interval' => 3600,
            'display'  => esc_html__('Every 60 minutes'),
        );
        $schedules['120m'] = array(
            'interval' => 7200,
            'display'  => esc_html__('Every 120 minutes'),
        );
        return $schedules;
    }

    public function check_version() {
        $storedVersion = get_option(self::SETTING_PLUGIN_VERSION);
        if (!$storedVersion || $this->version !== $storedVersion) {
            require_once plugin_dir_path(__FILE__) . '../includes/class-wp-job-board-activator.php';
            WP_Job_Board_Activator::activate();
        }
    }
}
