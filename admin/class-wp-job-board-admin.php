<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      0.1.0
 *
 * @package    WP_Job_Board
 * @subpackage WP_Job_Board/admin
 */

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
class WP_Job_Board_Admin {

    /**
     * The following are handled through the Settings API and are
     * client facing on the admin site.
     */
    const SETTINGS_GROUP = 'wp_job_board_settings_group';
    const SETTINGS_SECTION = 'wp_job_board_settings_section';
    const SETTING_CLIENT_ID = 'wp_job_board_client_id';
    const SETTING_CLIENT_SECRET = 'wp_job_board_client_secret';
    const SETTING_API_USERNAME = 'wp_job_board_api_username';
    const SETTING_API_PASSWORD = 'wp_job_board_api_password';
    const SETTING_ENABLE_CRON = 'wp_job_board_enable_cron';

    /**
     * The following are handled in an Options Object(the first const)
     * and are handled by the plugin alone.
     */
    const OPTION_ARRAY_KEY = 'wp_job_board_options';

    const PAGE_SLUG = 'wp_job_board';
    const CRON_SYNC_JOBS = 'wp_job_board_sync_jobs_cron';

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
     * @var WP_Job_Board_Bullhorn_Manager $bullhorn Bullhorn Manager Inst5ance
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
    public function __construct($wp_job_board, $version) {
        $this->wp_job_board = $wp_job_board;
        $this->version      = $version;
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    0.1.0
     */
    public function enqueue_styles() {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in WP_Job_Board_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The WP_Job_Board_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

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
    public function enqueue_scripts() {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in WP_Job_Board_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The WP_Job_Board_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_script(
            $this->wp_job_board,
            plugin_dir_url(__FILE__) . 'js/wp-job-board-admin.js',
            array('jquery'),
            filemtime(plugin_dir_path(__FILE__) . 'js/wp-job-board-admin.js'),
            false
        );
    }

    public function add_menu() {
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
            self::SETTINGS_GROUP,
            self::SETTING_ENABLE_CRON,
            array()
        );

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

    public function render_settings_page() {
        require_once plugin_dir_path(__FILE__) . 'pages/wp-job-board-admin-settings.php';
    }

    public function render_tools_page() {
        require_once plugin_dir_path(__FILE__) . 'pages/wp-job-board-admin-tools.php';
    }

    public function trigger_sync() {
        try {
            if ( ! $this->bullhorn) {
                $this->bullhorn = new WP_Job_Board_Bullhorn_Manager();
            }
            $force = false;
            if (isset($_POST['force']) && $_POST['force'] === 'true') {
                $force = true;
            }
            $this->bullhorn->trigger_sync(null, $force);
            wp_send_json_success(array('message' => 'Listings synced!',));
        } catch (Throwable $exception) {
            wp_send_json_error(array('message' => $exception->getMessage()));
        }
    }

    public function refresh_log() {
        require_once plugin_dir_path(__FILE__) . 'partials/wp-job-board-activity-log.php';
    }

    public function add_cron() {
        if (!get_option(WP_Job_Board_Admin::SETTING_ENABLE_CRON)) {
            $timestamp = wp_next_scheduled( WP_Job_Board_Admin::CRON_SYNC_JOBS );
            if ($timestamp) {
                wp_unschedule_event( $timestamp, WP_Job_Board_Admin::CRON_SYNC_JOBS );
            }
            return;
        }
        $schedules = wp_get_schedules();

        if (($timestamp = wp_next_scheduled(WP_Job_Board_Admin::CRON_SYNC_JOBS)) === false) {
            $scheduled = wp_schedule_event(time(), '30m', WP_Job_Board_Admin::CRON_SYNC_JOBS);
        }
    }

    public function add_30m_interval($schedules) {
        $schedules['30m'] = array(
            'interval' => '1800',
            'display'  => esc_html__( 'Every 30 minutes' ),
        );
        return $schedules;
    }
}
