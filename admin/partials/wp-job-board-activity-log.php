<?php

global $wpdb;

$results = $wpdb->get_results('SELECT * FROM wp_job_board_log ORDER BY timestamp DESC ', ARRAY_A);

foreach ($results as $item) {
	?>
<div><?=$item['action'] ?> - <?=$item['bh_title'] ?>(<?=$item['bh_id'] ?>) - <?= date('m/d/Y h:i:s a', $item['timestamp']) ?></div>
<?php
}
