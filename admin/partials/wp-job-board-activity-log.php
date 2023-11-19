<?php
$table = new WP_Job_Board_Log_Table();
echo '<form method="post">';
$table->prepare_items();
$table->search_box('search', 'search_id');
$table->display();
echo '</form>';

