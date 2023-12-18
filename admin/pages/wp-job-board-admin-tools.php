<?php
    $page = $_GET['page'];
    $view = !empty($_GET['view']) ? $_GET['view'] : 'data-operations';

    $tools_base_url = admin_url() . 'edit.php?post_type=wjb_bh_job_order&page=wp-job-board-tools';
?>


<div class="wrap" id="wp_job_board_admin">
    <div class="wpjba">
        <div class="wpjba__hd">
            <h1>
                <span class="wpjba-highlight">WP Job Board</span>
                <span>Tools</span>
            </h1>
        </div>
        <div class="wpjba__menu">
            <ul class="wpjba-tabs">
                <li>
                    <a href="<?php echo $tools_base_url; ?>&view=data-operations" class="<?= $view === 'data-operations' ? 'wpjba-tabs-isActive' : ''?>">
                        Data Operations
                    </a>
                </li>
                <li>
                    <a href="<?php echo $tools_base_url; ?>&view=activity-log" class="<?= $view === 'activity-log' ? 'wpjba-tabs-isActive' : ''?>">
                        Activity Log
                    </a>
                </li>
            </ul>
        </div>
        <div class="wpjba__bd">
            <div class="wpjba-message" data-wpjba-message-node="true"></div>
            <?php
                // load the partial named the same as $view
                require_once plugin_dir_path(__FILE__) . "../partials/tools/wp-job-board-admin-{$view}.php";
            ?>
        </div>
    </div>
</div>
