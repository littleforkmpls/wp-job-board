<?php

if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/screen.php');
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

/**
 * Create a table for viewing logs.
 *
 * @link       http://example.com
 * @since      0.1.0
 *
 * @package    WP_Job_Board
 * @subpackage WP_Job_Board/includes
 */

/**
 * Build and configure log view table.
 *
 * @package    WP_Job_Board
 * @subpackage WP_Job_Board/includes
 * @author     Little Fork
 */

class WP_Job_Board_Log_Table extends WP_List_Table
{
    private $table_data;

    public function prepare_items(): void
    {
        if (isset($_POST['s'])) {
            $this->table_data = $this->get_table_data($_POST['s']);
        } else {
            $this->table_data = $this->get_table_data();
        }

        $columns               = $this->get_columns();
        $hidden                = array();
        $sortable              = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable, 'bh_id');

        usort($this->table_data, array($this, 'usort_reorder'));

        $per_page     = 50;
        $current_page = $this->get_pagenum();
        $total_items  = count($this->table_data);

        $this->table_data = array_slice($this->table_data, (($current_page -= 1) * $per_page), $per_page);

        $this->set_pagination_args(
            array(
                'total_items' => $total_items,
                'per_page'    => $per_page,
                'total_pages' => ceil($total_items / $per_page),
            )
        );

        $this->items = $this->table_data;
    }

    private function get_table_data($search = ''): array
    {
        global $wpdb;

        if (!empty($search)) {
            return $wpdb->get_results("
SELECT * FROM wp_job_board_log
WHERE bh_title LIKE '%{$search}%' OR bh_id LIKE '%{$search}%'
", ARRAY_A);
        } else {
            return $wpdb->get_results("SELECT * FROM wp_job_board_log", ARRAY_A);
        }
    }

    /**
     * Get our table columns.
     *
     * @return array
     */
    public function get_columns(): array
    {
        return array(
            'bh_id'     => __('Bullhorn ID'),
            'action'    => __('Action'),
            'bh_title'  => __('Job Title'),
            'timestamp' => __('Date'),
        );
    }

    protected function get_sortable_columns()
    {
        return array(
            'bh_id'     => array('bh_id', false),
            'action'    => array('action', false),
            'bh_title'  => array('bh_title', false),
            'timestamp' => array('timestamp', false),
        );
    }

    protected function column_default($item, $column_name)
    {
        switch ($column_name) {
            case 'timestamp':
                return date('m/d/Y h:i:s a', (int) $item[$column_name]);
            case 'bh_id':
            case 'action':
            case 'bh_title':
            default:
                return $item[$column_name];
        }
    }

    protected function get_table_classes()
    {
        $classes = parent::get_table_classes();
        if ($key = array_search('fixed', $classes) !== false) {
            unset($classes[$key]);
        }

        return $classes;
    }

    private function usort_reorder($a, $b)
    {
        // If no sort, default to user_login
        $orderby = ( ! empty($_GET['orderby'])) ? $_GET['orderby'] : 'timestamp';

        // If no order, default to asc
        $order = ( ! empty($_GET['order'])) ? $_GET['order'] : 'desc';

        // Determine sort order
        $result = strcmp($a[$orderby], $b[$orderby]);

        // Send final sort direction to usort
        return ($order === 'asc') ? $result : - $result;
    }
}
