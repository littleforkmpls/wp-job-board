<div class="wpjb-card" id="wpjb-card">
    <div class="wpjb-card__hd">
        <h1 class="wpjb-card__title">
            <?php echo $job_title; ?>
        </h1>
    </div>

    <span class="wpjb-card__meta-item__subtitle">
        <span><?php echo $job_location_city; ?>, <?php echo $job_location_state; ?> | <?php echo $job_employment_type; ?></span>
    </span>
    <div class="wpjb-card__meta">
        <span class="wpjb-card__meta-item">
        <?php echo file_get_contents(plugin_dir_path(__DIR__) . 'images/fa-calendar-days.svg'); ?>

            <span>Posted <?php echo $job_date_published; ?></span>
        </span>
        <span class="wpjb-card__meta-item">
        <?php echo file_get_contents(plugin_dir_path(__DIR__) . 'images/fa-clock.svg'); ?>

            <span class="wpjb-card__meta-item__update">Updated <?php echo $job_date_modified; ?></span>
        </span>
    </div>
    <!-- <hr class="wpjb-card__divider" /> -->
    <div class="wpjb-card__divider-top"></div>
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
            <button class="wpjb-utilityNav__btn">
                <?php echo file_get_contents(plugin_dir_path(__DIR__) . 'images/fa-printer.svg'); ?>
            </button>
        </div>
    </div>
    <div class="wpjb-card__divider"></div>
    <div class="wpjb-btn__container">
        <button data-micromodal-trigger="modal-apply" class="btn">Apply</button>
    </div>
</div>
