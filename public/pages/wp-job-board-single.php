<?php

if (!is_single() || get_post_type() !== 'wjb_bh_job_order') {
    throw new ErrorException('The wpjb_single shortcode must be placed within an WordPress compatible "single" template.');
}

$post_id  = get_the_ID();
$job_meta = get_job_meta($post_id);

if (empty($job_meta)) {
    throw new ErrorException('No job meta data could not be found.');
}

/*
 * Setup values for template usage
 *
 * Note:
 * since dateLastPublished can be null AND dateLastModified cannot
 * dateLastModified is used as fallback for dateLastPublished null scenarios
 */
$job_title                  = !empty($job_meta->title) ? $job_meta->title : get_the_title();
$job_description            = !empty($job_meta->publicDescription) ? $job_meta->publicDescription : 'No job description provided.';
$job_employment_type        = !empty($job_meta->employmentType) ? $job_meta->employmentType : '';
$job_location_city          = !empty($job_meta->address->city) ? $job_meta->address->city : '';
$job_location_state         = !empty($job_meta->address->state) ? $job_meta->address->state : '';
$job_location_postal_code   = !empty($job_meta->address->zip) ? $job_meta->address->zip : '';
$job_location_country_code  = !empty($job_meta->address->countryCode) ? $job_meta->address->countryCode : '';
$job_date_published         = !empty($job_meta->dateLastPublished) ? get_formatted_date($job_meta->dateLastPublished) : get_formatted_date($job_meta->dateLastModified);
$job_date_published_iso8601 = !empty($job_meta->dateLastPublished) ? get_iso8601_date($job_meta->dateLastPublished) : get_formatted_date($job_meta->dateLastModified);
$job_date_modified          = !empty($job_meta->dateLastModified) ? get_relative_date($job_meta->dateLastModified) : '';
$job_date_modified_iso8601  = !empty($job_meta->dateLastModified) ? get_iso8601_date($job_meta->dateLastModified) : '';

?>

<div id="wpjb">
    <div class="wpjb-single">
        <div class="wpjb-single__preface">
            <button class="wpjb-btn__back">
                <?php echo file_get_contents(plugin_dir_path(__DIR__) . 'images/fa-arrow-left.svg'); ?>
                <span>Back to All Jobs</span>
            </button>
        </div>
        <div class="wpjb-single__content">
            <?php require_once plugin_dir_path(__DIR__) . 'partials/wp-job-board-card.php'; ?>
            <?php require_once plugin_dir_path(__DIR__) . 'partials/wp-job-board-modal.php'; ?>
        </div>
    </div>
</div>

<?php require_once plugin_dir_path(__DIR__) . 'partials/wp-job-board-schema.php'; ?>
