<?php
$table = new WP_Job_Board_Log_Table();
?>

<div class="wpjba-dataTable">
    <form method="post">
        <?php
            $table->prepare_items();
            $table->search_box('search', 'search_id');
            $table->display();
        ?>
    </form>
</div>
