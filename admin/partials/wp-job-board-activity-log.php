<?php
$table = new WP_Job_Board_Log_Table();
?>
<div class="wrap" id="wp_job_board_admin">
    <div id="wp_job_board_activity_log" class="wp_job_board_tab">
        <form method="post">
            <?php
            $table->prepare_items();
            $table->search_box('search', 'search_id');
            $table->display();
            ?>
        </form>
    </div>
</div>

