<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://example.com
 * @since      0.1.0
 *
 * @package    WP_Job_Board
 * @subpackage WP_Job_Board/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      0.1.0
 * @package    WP_Job_Board
 * @subpackage WP_Job_Board/includes
 * @author     Little Fork
 */
class WP_Job_Board
{
    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    0.1.0
     * @access   protected
     * @var      WP_Job_Board_Loader $loader Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    0.1.0
     * @access   protected
     * @var      string $wp_job_board The string used to uniquely identify this plugin.
     */
    protected $wp_job_board;

    /**
     * The current version of the plugin.
     *
     * @since    0.1.0
     * @access   protected
     * @var      string $version The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    0.1.0
     */
    public function __construct()
    {
        $this->version = WP_JOB_BOARD_VERSION;

        $this->wp_job_board = 'wp-job-board';

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
        $this->start_session();
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - WP_Job_Board_Loader. Orchestrates the hooks of the plugin.
     * - WP_Job_Board_i18n. Defines internationalization functionality.
     * - WP_Job_Board_Admin. Defines all hooks for the admin area.
     * - WP_Job_Board_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    0.1.0
     * @access   private
     */
    private function load_dependencies()
    {
        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path(__DIR__) . 'includes/class-wp-job-board-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path(__DIR__) . 'includes/class-wp-job-board-i18n.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path(__DIR__) . 'admin/class-wp-job-board-admin.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path(__DIR__) . 'public/class-wp-job-board-public.php';

        /**
         * Utility Files
         */
        require_once plugin_dir_path(__DIR__) . 'includes/shortcodes.php';
        require_once plugin_dir_path(__DIR__) . 'includes/template-helpers.php';

        /**
         * API Managers
         */
        require_once plugin_dir_path(__DIR__) . 'includes/class-wp-job-board-api-manager-base.php';
        require_once plugin_dir_path(__DIR__) . 'includes/class-wp-job-board-bullhorn-manager.php';

        /**
         * Other Helpers
         */
        require_once plugin_dir_path(__DIR__) . 'includes/class-wp-job-board-log-table.php';
        require_once plugin_dir_path(__DIR__) . 'includes/class-wp-job-board-updater.php';

        $this->loader = new WP_Job_Board_Loader();
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the WP_Job_Board_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    0.1.0
     * @access   private
     */
    private function set_locale()
    {
        $plugin_i18n = new WP_Job_Board_I18n();

        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    0.1.0
     * @access   private
     */
    private function define_admin_hooks()
    {
        $updater = new WP_Job_Board_Updater();

        $plugin_admin = new WP_Job_Board_Admin($this->get_wp_job_board(), $this->get_version());

        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
        $this->loader->add_action('admin_menu', $plugin_admin, 'register_settings');
        $this->loader->add_action('admin_menu', $plugin_admin, 'build_admin_menu');
        $this->loader->add_action('admin_post_trigger_sync', $plugin_admin, 'trigger_sync');
        $this->loader->add_action('wp_ajax_trigger_sync', $plugin_admin, 'trigger_sync');
        $this->loader->add_action('wp_ajax_clear_logs', $plugin_admin, 'clear_logs');
        $this->loader->add_action('wp_ajax_test_connection', $plugin_admin, 'test_connection');
        $this->loader->add_filter('cron_schedules', $plugin_admin, 'add_cron_intervals');
        $this->loader->add_action(WP_Job_Board_Admin::CRON_SYNC_JOBS, $plugin_admin, 'trigger_sync');
        $this->loader->add_action('init', $plugin_admin, 'add_cron');
        $this->loader->add_action('admin_init', $plugin_admin, 'check_version');
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @return    string    The name of the plugin.
     * @since     0.1.0
     */
    public function get_wp_job_board()
    {
        return $this->wp_job_board;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @return    string    The version number of the plugin.
     * @since     0.1.0
     */
    public function get_version()
    {
        return $this->version;
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    0.1.0
     * @access   private
     */
    private function define_public_hooks()
    {
        $plugin_public = new WP_Job_Board_Public($this->get_wp_job_board(), $this->get_version());

        if (is_custom_template() !== true) {
            $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
            $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
        }

        $this->loader->add_action('init', $plugin_public, 'register_job_order_post_type');
        $this->loader->add_action('init', $plugin_public, 'register_job_order_taxonomies');

        $this->loader->add_action('rest_api_init', $plugin_public, 'register_resume_endpoint');
    }

    private function start_session()
    {
        if (!session_id()) {
            session_start([
                'read_and_close' => true,
            ]);
        }
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    0.1.0
     */
    public function run()
    {
        $this->loader->run();
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @return    WP_Job_Board_Loader    Orchestrates the hooks of the plugin.
     * @since     0.1.0
     */
    public function get_loader()
    {
        return $this->loader;
    }
}
