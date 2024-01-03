<div class="wpjb-card" id="wpjb-card">
    <div class="wpjb-card__hd">
        <h1 class="wpjb-card__title">
            <?php echo $job_title; ?>
        </h1>
    </div>
    <div class="wpjb-card__meta">
        <span class="wpjb-card__meta-item">
            <?php echo file_get_contents(plugin_dir_path(__DIR__) . 'images/fa-calendar-days.svg'); ?>
            <span>Posted <?php echo $job_date_published; ?></span>
        </span>
        <span class="wpjb-card__meta-item">
            <?php echo file_get_contents(plugin_dir_path(__DIR__) . 'images/fa-clock.svg'); ?>
            <span>Updated <?php echo $job_date_modified; ?></span>
        </span>
        <span class="wpjb-card__meta-item">
            <?php echo file_get_contents(plugin_dir_path(__DIR__) . 'images/fa-location-dot.svg'); ?>
            <span><?php echo $job_location_city; ?>, <?php echo $job_location_state; ?></span>
        </span>
        <span class="wpjb-card__meta-item">
            <?php echo file_get_contents(plugin_dir_path(__DIR__) . 'images/fa-briefcase-blank.svg'); ?>
            <span> <?php echo $job_employment_type; ?></span>
        </span>
    </div>
    <div class="wbjb-card__sub-hd">
        <h3 class="wpjb-card__sub-title">
            About the job
        </h3>
    </div>
    <div class="wpjb-card__bd">
        <div class="wpjb-userContent">
            <?php echo $job_description; ?>
        </div>
    </div>
    <div class="wpjb-card__ft">
        <div class="wpjb-utilityNav">
            <button class="wpjb-utilityNav__btn">
                <?php echo file_get_contents(plugin_dir_path(__DIR__) . 'images/fa-envelope.svg'); ?>
            </button>
            <button class="wpjb-utilityNav__btn" onclick="printContent()">
                <?php echo file_get_contents(plugin_dir_path(__DIR__) . 'images/fa-printer.svg'); ?>
            </button>
        </div>
    </div>
    <div class="wpjb-btn__container">
        <button data-micromodal-trigger="modal-apply" class="btn">Apply</button>
    </div>
</div>
