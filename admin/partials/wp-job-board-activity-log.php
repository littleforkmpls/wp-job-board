<?php
$table = new WP_Job_Board_Log_Table();
?>
<div class="wrap" id="wp_job_board_admin">
    <?php
    require_once plugin_dir_path(__FILE__) . 'wp-job-board-settings-menu.php'
    ?>
    <div id="wp_job_board_activity_log">
        <form method="post">
            <?php
            $table->prepare_items();
            $table->search_box('search', 'search_id');
            $table->display();
            ?>
        </form>
    </div>
</div>

