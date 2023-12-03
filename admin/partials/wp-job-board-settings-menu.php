<?php
$page = $_GET['page'];
?>
<h2 id="wp_job_board_title"><?php esc_html_e('WP Job Board', 'wp_job_board'); ?></h2>
<h2 class="nav-tab-wrapper">
    <a class="nav-tab <?= $page === 'wp_job_board' ? 'nav-tab-active' : ''?>" href="<?php echo admin_url() ?>/options-general.php?page=wp_job_board">Settings</a>
    <a class="nav-tab <?= $page === 'wp_job_board_log' ? 'nav-tab-active' : ''?>" href="<?php echo admin_url() ?>/options-general.php?page=wp_job_board_log">Activity Log</a>
</h2>
