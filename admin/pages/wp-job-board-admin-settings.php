<?php
    $page = $_GET['page'];
    $view = !empty($_GET['view']) ? $_GET['view'] : 'connector';

    $tools_base_url = admin_url() . 'edit.php?post_type=wjb_bh_job_order&page=wp-job-board-settings';
?>


<div class="wpbody" id="wp_job_board_admin">
    <div class="wpjba">
        <div class="wpjba__hd">
            <h1>
                <span class="wpjba-highlight">WP Job Board</span>
                <span>Settings</span>
            </h1>
        </div>
        <div class="wpjba__menu">
            <ul class="wpjba-tabs">
                <li>
                    <a href="<?php echo $tools_base_url; ?>&view=connector" class="<?= $view === 'connector' ? 'wpjba-tabs-isActive' : ''?>">
                        Data Connector
                    </a>
                </li>
                <li>
                    <a href="<?php echo $tools_base_url; ?>&view=cron" class="<?= $view === 'cron' ? 'wpjba-tabs-isActive' : ''?>">
                        Cron Schedule
                    </a>
                </li>
                <li>
                    <a href="<?php echo $tools_base_url; ?>&view=theme" class="<?= $view === 'theme' ? 'wpjba-tabs-isActive' : ''?>">
                        Theme
                    </a>
                </li>
            </ul>
        </div>
        <div class="wpjba__bd">
            <?php
                // load the partial named the same as $view
                require_once plugin_dir_path(__FILE__) . "../partials/settings/wp-job-board-admin-{$view}.php";
            ?>
        </div>
    </div>
</div>
