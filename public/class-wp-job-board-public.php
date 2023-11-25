<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    WP_Job_Board
 * @subpackage WP_Job_Board/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    WP_Job_Board
 * @subpackage WP_Job_Board/public
 * @author     Drew Brown <dbrown78@gmail.com>
 */
class WP_Job_Board_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $wp_job_board    The ID of this plugin.
	 */
	private $wp_job_board;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;
    private WP_Job_Board_Bullhorn_Manager|null $bullhorn = null;

    /**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $wp_job_board       The name of the plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $wp_job_board, $version ) {

		$this->wp_job_board = $wp_job_board;
		$this->version      = $version;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
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

		wp_enqueue_style( $this->wp_job_board, plugin_dir_url( __FILE__ ) . 'css/wp-job-board-public.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
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

		wp_enqueue_script( $this->wp_job_board, plugin_dir_url( __FILE__ ) . 'js/wp-job-board-public.js', array( 'jquery' ), $this->version, false );
	}

	public function register_job_order_post_type() {
		register_post_type(
			'wjb_bh_job_order',
			array(
				'labels'      => array(
					'name'          => __( 'BH Job Orders', 'wp_job_board' ),
					'singular_name' => __( 'BH Job Order', 'wp_job_board' ),
				),
				'description' => 'Job Order pulled from Bullhorn\'s REST API',
				'public'      => false,
				'has_archive' => true,
				'rewrite'     => array( 'slug' => 'bh-job-orders' ),
			)
		);
	}

    public function register_resume_endpoint() {
        register_rest_route(
            'wp-job-board/v1',
            '/submit-resume',
            array(
                'methods' => \WP_REST_Server::CREATABLE,
                'permission_callback' => '__return_true',
                'callback' => array($this, 'submit_resume'),
            )
        );
    }

    public function submit_resume()
    {
        try {
            if (!$this->bullhorn) {
                $this->bullhorn = new WP_Job_Board_Bullhorn_Manager();
            }
            $result = $this->bullhorn->submit_resume();
            wp_send_json_success(array('message' => 'Listings synced!',));
        } catch (\Throwable $exception) {
            wp_send_json_error(array('message' => $exception->getMessage()));
        }
    }
}
