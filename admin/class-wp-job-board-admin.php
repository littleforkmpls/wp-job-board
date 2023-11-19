<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
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
 * @author     Drew Brown <dbrown78@gmail.com>
 */
class WP_Job_Board_Admin
{

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

	/**
	 * The following are handled in an Options Object(the first const)
	 * and are handled by the plugin alone.
	 */
	const OPTION_ARRAY_KEY = 'wp_job_board_options';
	const OPTION_API_PASSWORD = 'wp_job_board_api_password';
	const OPTION_ACCESS_CODE_ENDPOINT = 'wp_job_board_access_code_url';
	const OPTION_ACCESS_TOKEN_ENDPOINT = 'wp_job_board_access_token_url';
	const OPTION_ACCESS_TOKEN_REFRESH_ENDPOINT = '';
	const OPTION_OAUTH_URL = 'wp_job_board_oauth_url';

	const PAGE_SLUG = 'wp_job_board';

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $wp_job_board The ID of this plugin.
	 */
	private $wp_job_board;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Bullhorn Manager Instance
	 *
	 * @since  1.0.0
	 * @access private
	 * @var WP_Job_Board_Bullhorn_Manager $bullhorn Bullhorn Manager Inst5ance
	 */
	private $bullhorn;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $wp_job_board The name of this plugin.
	 * @param string $version      The version of this plugin.
	 *
	 * @since    1.0.0
	 */
	public function __construct($wp_job_board, $version)
	{
		$this->wp_job_board = $wp_job_board;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{

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

		wp_enqueue_style($this->wp_job_board, plugin_dir_url(__FILE__) . 'css/wp-job-board-admin.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{

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

		wp_enqueue_script($this->wp_job_board, plugin_dir_url(__FILE__) . 'js/wp-job-board-admin.js', array('jquery'), $this->version, false);
	}

	public function add_submenu()
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

		add_options_page(
			'WP Job Board',
			'WP Job Board',
			'manage_options',
			'wp_job_board',
			array($this, 'render_options_page')
		);
	}

	public function render_options_page()
	{
		require_once plugin_dir_path(__FILE__) . 'partials/wp-job-board-admin-display.php';
	}

	public function trigger_sync()
	{
		try {
			if (!$this->bullhorn) {
				$this->bullhorn = new WP_Job_Board_Bullhorn_Manager();
			}
			$this->bullhorn->trigger_sync();
			wp_send_json_success(array('message' => 'Listings synced!',));
		} catch (\Throwable $exception) {
			wp_send_json_error(array('message' => $exception->getMessage()));
		}
	}

	public function refresh_log()
	{
		require_once plugin_dir_path(__FILE__) . 'partials/wp-job-board-activity-log.php';
	}
}
