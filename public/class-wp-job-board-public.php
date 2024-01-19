<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://example.com
 * @since      0.1.0
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
 * @author     Little Fork
 */
class WP_Job_Board_Public
{
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
    private WP_Job_Board_Bullhorn_Manager|null $bullhorn = null;

    /**
     * Initialize the class and set its properties.
     *
     * @param string $wp_job_board The name of the plugin.
     * @param string $version The version of this plugin.
     *
     * @since    0.1.0
     */
    public function __construct($wp_job_board, $version)
    {
        $this->wp_job_board = $wp_job_board;
        $this->version      = $version;

        add_action('wp_ajax_nopriv_filter_jobs', array($this, 'ajax_filter_jobs'));
        add_action('wp_ajax_filter_jobs', array($this, 'ajax_filter_jobs'));
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    0.1.0
     */
    public function enqueue_styles()
    {
        wp_enqueue_style(
            $this->wp_job_board,
            plugin_dir_url(__FILE__) . 'css/wp-job-board-public.css',
            array(),
            filemtime(plugin_dir_path(__FILE__) . 'css/wp-job-board-public.css'),
            'all'
        );
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    0.1.0
     */
    public function enqueue_scripts()
    {
        wp_enqueue_script(
            $this->wp_job_board,
            plugin_dir_url(__FILE__) . 'js/wp-job-board-public.js',
            array('jquery', 'wpjb-micromodal'),
            filemtime(plugin_dir_path(__FILE__) . 'js/wp-job-board-public.js'),
            true
        );

        wp_enqueue_script(
            'wpjb-micromodal',
            plugin_dir_url(__FILE__) . 'js/micromodal.min.js',
            null,
            filemtime(plugin_dir_path(__FILE__) . 'js/micromodal.min.js'),
            true
        );

        $translation_array = array(
            'ajax_url' => admin_url('admin-ajax.php')
        );
        wp_localize_script($this->wp_job_board, 'wpjb_ajax', $translation_array);
    }

    /**
     * Register custom post type to store job order data
     *
     * @since    0.1.0
     * @return void
     */
    public function register_job_order_post_type()
    {
        register_post_type(
            'wjb_bh_job_order',
            array(
                'labels' => array(
                    'name'                      => __('Jobs', 'wp_job_board'),
                    'singular_name'             => __('Job', 'wp_job_board'),
                    'add_new'                   => __('Add New Job', 'wp_job_board'),
                    'add_new_item'              => __('Add New Job', 'wp_job_board'),
                    'edit_item'                 => __('Edit Job', 'wp_job_board'),
                    'new_item'                  => __('New Job', 'wp_job_board'),
                    'view_item'                 => __('View Job', 'wp_job_board'),
                    'view_items'                => __('View Jobs', 'wp_job_board'),
                    'search_items'              => __('Search Jobs', 'wp_job_board'),
                    'not_found'                 => __('No Jobs found', 'wp_job_board'),
                    'not_found_in_trash'        => __('No Jobs found in Trash', 'wp_job_board'),
                    'parent_item_colon'         => __('Parent Jobs:', 'wp_job_board'),
                    'all_items'                 => __('Jobs', 'wp_job_board'),
                    'archives'                  => __('Job Archives', 'wp_job_board'),
                    'attributes'                => __('Job Attributes', 'wp_job_board'),
                    'insert_into_item'          => __('Insert into Job', 'wp_job_board'),
                    'uploaded_to_this_item'     => __('Uploaded to this Job', 'wp_job_board'),
                    'featured_image'            => __('Featured image', 'wp_job_board'),
                    'set_featured_image'        => __('Set featured image', 'wp_job_board'),
                    'remove_featured_image'     => __('Remove featured image', 'wp_job_board'),
                    'use_featured_image'        => __('Use as featured image', 'wp_job_board'),
                    'menu_name'                 => __('WP Job Board', 'wp_job_board'),
                    'filter_items_list'         => __('Filter Jobs list', 'wp_job_board'),
                    'filter_by_date'            => __('Filter by date', 'wp_job_board'),
                    'items_list_navigation'     => __('Jobs list navigation', 'wp_job_board'),
                    'items_list'                => __('Jobs list', 'wp_job_board'),
                    'item_published'            => __('Jobs published', 'wp_job_board'),
                    'item_published_privately'  => __('Job published privately', 'wp_job_board'),
                    'item_reverted_to_draft'    => __('Job reverted to draft', 'wp_job_board'),
                    'item_trashed'              => __('Job trashed', 'wp_job_board'),
                    'item_scheduled'            => __('Job scheduled', 'wp_job_board'),
                    'item_updated'              => __('Job updated', 'wp_job_board'),
                    'item_link'                 => __('Job Link', 'wp_job_board'),
                    'item_link_description'     => __('A link to a Job', 'wp_job_board')
                ),
                'menu_icon'             => 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiA/Pjxzdmcgdmlld0JveD0iMCAwIDI0IDI0IiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPjx0aXRsZS8+PHBhdGggZD0iTTE4Ljg3LDEyLjA3bC02LDkuNDdBMSwxLDAsMCwxLDEyLDIyYS45LjksMCwwLDEtLjI4LDBBMSwxLDAsMCwxLDExLDIxVjE1SDYuODJhMiwyLDAsMCwxLTEuNjktMy4wN2w2LTkuNDdBMSwxLDAsMCwxLDEyLjI4LDIsMSwxLDAsMCwxLDEzLDNWOWg0LjE4YTIsMiwwLDAsMSwxLjY5LDMuMDdaIiBmaWxsPSIjNDY0NjQ2Ii8+PC9zdmc+',
                'menu_position'         => 85,
                'public'                => true,
                'show_in_admin_bar'     => false,
                'show_in_rest'          => true,
                'rewrite'               => array('slug' => 'jobs'),
                'hierarchical'          => false,
                'has_archive'           => true,
                'can_export'            => false,
                'supports'              => array('title'),
                'register_meta_box_cb'  => array($this, 'show_meta_data')
            )
        );
    }

    /**
     * Display Meta Data
     */
    public function show_meta_data($post)
    {
        add_meta_box(
            'wjb_job_order_data',
            'Job Order Data',
            array($this, 'show_meta_boxes'),
            'wjb_bh_job_order',
            'normal',
            'core'
        );
    }

    /**
     * Display Meta Boxes
     */
    public function show_meta_boxes()
    {
        global $post;

        $bhData = get_post_meta($post->ID, 'wjb_bh_data', true);
        $data = json_decode($bhData, true);
        $data['publicDescription'] = htmlspecialchars($data['publicDescription']);

        if (!$data) {
            $error = json_last_error_msg();
        }

        $data_encoded = json_encode($data, JSON_PRETTY_PRINT);

        $output = "<pre class='wp_job_board_meta_sample'>{$data_encoded}</pre>";

        echo $output;
    }

    /**
     * Register Taxonomies that will hold job specific meta data
     */
    public function register_job_order_taxonomies()
    {
        /*
         * Register Taxonomy: Job Type
         */
        register_taxonomy(
            'wjb_bh_job_type_tax',
            ['wjb_bh_job_order'],
            array(
                'hierarchical'      => false,
                'labels'            => array(
                    'menu_name'         => __('&#8211; Type'),
                    'name'              => __('Job Types', 'taxonomy general name'),
                    'singular_name'     => __('Job Type', 'taxonomy singular name'),
                    'search_items'      => __('Search Job Types'),
                    'all_items'         => __('All Job Types'),
                    'parent_item'       => __('Parent Job Types'),
                    'parent_item_colon' => __('Parent Job Type:'),
                    'edit_item'         => __('Edit Job Type'),
                    'update_item'       => __('Update Job Type'),
                    'add_new_item'      => __('Add New Job Type'),
                    'new_item_name'     => __('New Job Type Name'),
                    'popular_items'     => __('Popular Job Types'),
                    'view_item'         => __('View Job Type'),
                    'not_found'         => __('No Job Types Found'),
                    'back_to_items'     => __('←︎︎ Back to Job Types')
                ),
                'show_ui'           => true,
                'show_admin_column' => true,
                'show_in_rest'      => true,
                'query_var'         => true,
                'rewrite'           => ['slug' => 'job-type']
            )
        );

        /*
         * Register Taxonomy: Job Location
         */
        register_taxonomy(
            'wjb_bh_job_location_tax',
            ['wjb_bh_job_order'],
            array(
                'hierarchical'      => false,
                'labels'            => array(
                    'menu_name'         => __('&#8211; Location'),
                    'name'              => __('Job Locations', 'taxonomy general name'),
                    'singular_name'     => __('Job Location', 'taxonomy singular name'),
                    'search_items'      => __('Search Job Locations'),
                    'all_items'         => __('All Job Locations'),
                    'parent_item'       => __('Parent Job Locations'),
                    'parent_item_colon' => __('Parent Job Location:'),
                    'edit_item'         => __('Edit Job Location'),
                    'update_item'       => __('Update Job Location'),
                    'add_new_item'      => __('Add New Job Location'),
                    'new_item_name'     => __('New Job Location Name'),
                    'popular_items'     => __('Popular Job Locations'),
                    'view_item'         => __('View Job Location'),
                    'not_found'         => __('No Job Locations Found'),
                    'back_to_items'     => __('←︎︎ Back to Job Locations')
                ),
                'show_ui'           => true,
                'show_admin_column' => true,
                'show_in_rest'      => true,
                'query_var'         => true,
                'rewrite'           => ['slug' => 'job-location']
            )
        );

        /*
         * Register Taxonomy: Job Category
         */
        register_taxonomy(
            'wjb_bh_job_category_tax',
            ['wjb_bh_job_order'],
            array(
                'hierarchical'      => false,
                'labels'            => array(
                    'menu_name'         => __('&#8211; Category'),
                    'name'              => __('Job Categories', 'taxonomy general name'),
                    'singular_name'     => __('Job Category', 'taxonomy singular name'),
                    'search_items'      => __('Search Job Categories'),
                    'all_items'         => __('All Job Categories'),
                    'parent_item'       => __('Parent Job Categories'),
                    'parent_item_colon' => __('Parent Job Category:'),
                    'edit_item'         => __('Edit Job Category'),
                    'update_item'       => __('Update Job Category'),
                    'add_new_item'      => __('Add New Job Category'),
                    'new_item_name'     => __('New Job Category Name'),
                    'popular_items'     => __('Popular Job Categories'),
                    'view_item'         => __('View Job Category'),
                    'not_found'         => __('No Job Categories Found'),
                    'back_to_items'     => __('←︎︎ Back to Job Categories')
                ),
                'show_ui'           => true,
                'show_admin_column' => true,
                'show_in_rest'      => true,
                'query_var'         => true,
                'rewrite'           => ['slug' => 'job-category']
            )
        );

        /*
         * Register Taxonomy: Job Industry
         */
        register_taxonomy(
            'wjb_bh_job_industry_tax',
            ['wjb_bh_job_order'],
            array(
                'hierarchical'      => false,
                'labels'            => array(
                    'menu_name'         => __('&#8211; Industry'),
                    'name'              => __('Job Industries', 'taxonomy general name'),
                    'singular_name'     => __('Job Industry', 'taxonomy singular name'),
                    'search_items'      => __('Search Job Industries'),
                    'all_items'         => __('All Job Industries'),
                    'parent_item'       => __('Parent Job Industries'),
                    'parent_item_colon' => __('Parent Job Industry:'),
                    'edit_item'         => __('Edit Job Industry'),
                    'update_item'       => __('Update Job Industry'),
                    'add_new_item'      => __('Add New Job Industry'),
                    'new_item_name'     => __('New Job Industry Name'),
                    'popular_items'     => __('Popular Job Industries'),
                    'view_item'         => __('View Job Industry'),
                    'not_found'         => __('No Job Industries Found'),
                    'back_to_items'     => __('←︎︎ Back to Job Industries')
                ),
                'show_ui'           => true,
                'show_admin_column' => true,
                'show_in_rest'      => true,
                'query_var'         => true,
                'rewrite'           => ['slug' => 'job-industry']
            )
        );
    }

    /**
     * Register REST API endpoint for resume submission
     *
     * @since    0.1.0
     * @return void
     */
    public function register_resume_endpoint()
    {
        register_rest_route(
            'wp-job-board/v1',
            '/submit-resume',
            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'permission_callback' => '__return_true',
                'callback'            => array($this, 'submit_resume'),
            )
        );
    }

    /**
     * Handler for our submit resume endpoint
     *
     * @since    0.1.0
     * @return void
     */
    public function submit_resume()
    {
        try {
            if (!$this->bullhorn) {
                $this->bullhorn = new WP_Job_Board_Bullhorn_Manager();
            }
            $result = $this->bullhorn->submit_resume();
            wp_send_json_success(array('message' => 'Application submitted!',));
        } catch (Throwable $exception) {
            wp_send_json_error(array('message' => $exception->getMessage(), 'trace' => $exception->getTrace()));
        }
    }


    public function ajax_filter_jobs()
    {
        $search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
        $industry = isset($_POST['industry']) ? $_POST['industry'] : array();
        $location = isset($_POST['location']) ? $_POST['location'] : array();
        $category = isset($_POST['category']) ? $_POST['category'] : array();
        $type = isset($_POST['type']) ? $_POST['type'] : array();
        $display_count = 2;
        $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
        $offset = ($page - 1) * $display_count;


        $tax_query = array('relation' => 'AND'); // use AND operator to combine conditions
        if (!empty($industry)) {
            $tax_query[] = array(
                'taxonomy' => 'wjb_bh_job_industry_tax',
                'field' => 'term_id', // can be 'term_id', 'slug' or 'name'
                'terms' => $industry,
                'operator' => 'IN' // IN = "inclusive", retrieve posts matching terms in $industry
            );
        }
        if (!empty($location)) {
            $tax_query[] = array(
                'taxonomy' => 'wjb_bh_job_location_tax',
                'field' => 'term_id',
                'terms' => $location,
                'operator' => 'IN'
            );
        }
        if (!empty($category)) {
            $tax_query[] = array(
                'taxonomy' => 'wjb_bh_job_category_tax',
                'field' => 'term_id',
                'terms' => $category,
                'operator' => 'IN'
            );
        }
        if (!empty($type)) {
            $tax_query[] = array(
                'taxonomy' => 'wjb_bh_job_type_tax',
                'field' => 'term_id',
                'terms' => $type,
                'operator' => 'IN'
            );
        }

        $args = array(
            'post_type' => 'wjb_bh_job_order',
            // 'posts_per_page' => 10,
            'tax_query' => $tax_query, // use tax_query from above to filter terms
            's' => $search,
            'paged' => $page,
            'offset' => $offset
        );

        $query = new WP_Query($args); // new instance of WP_Query class

        $results_count = $query->found_posts;
        $max_num_pages = $query->max_num_pages;

        ob_start(); // output buffer to capture HTML before sending it to the browser
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                include plugin_dir_path(__DIR__) . 'public/partials/wp-job-board-archive-card.php';
            }
        } else {
            echo '<h2>No Jobs Found</h2>';
        }
        $jobs = ob_get_clean(); //capture HTML from output buffer and store in $jobs variable

        wp_send_json_success(array(
            'html' => $jobs,
            'count' => $results_count,
            'max_num_pages' => $max_num_pages,
            'current_page' => $page
        )); //display HTML in browser
    }
}
