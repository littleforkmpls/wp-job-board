<?php
$post_id  = get_the_ID();
$job_meta = get_job_meta($post_id);

$job_title = !empty($job_meta->title) ? $job_meta->title : get_the_title();
$job_description = !empty($job_meta->publicDescription) ? wp_trim_words($job_meta->publicDescription, 45) : 'No job description provided.';
$job_employment_type = !empty($job_meta->employmentType) ? $job_meta->employmentType : '';
$job_location_city = !empty($job_meta->address->city) ? $job_meta->address->city : '';
$job_location_state = !empty($job_meta->address->state) ? $job_meta->address->state : '';
$job_location_postal_code = !empty($job_meta->address->zip) ? $job_meta->address->zip : '';
$job_location_country_code = !empty($job_meta->address->countryCode) ? $job_meta->address->countryCode : '';
$job_date_published = !empty($job_meta->dateLastPublished) ? get_formatted_date($job_meta->dateLastPublished) : get_formatted_date($job_meta->dateLastModified);
$job_date_published_iso8601 = !empty($job_meta->dateLastPublished) ? get_iso8601_date($job_meta->dateLastPublished) : get_formatted_date($job_meta->dateLastModified);
$job_date_modified = !empty($job_meta->dateLastModified) ? get_relative_date($job_meta->dateLastModified) : '';
$job_date_modified_iso8601 = !empty($job_meta->dateLastModified) ? get_iso8601_date($job_meta->dateLastModified) : '';

$permalink = get_permalink($post_id);
?>

<div class="wpjb-archive">
    <div class="wpjb-card">
        <div class="wpjb-card__hd">
            <h3 class="wpjb-card__title">
                <?php echo $job_title; ?>
            </h3>
            <span class="wpjb-card__meta-item__subtitle">
                <?php echo $job_location_city; ?>, <?php echo $job_location_state; ?> | <?php echo $job_employment_type; ?>
            </span>
            <div class="wpjb-card__meta">
                <span class="wpjb-card__meta-item">
                    <span>Updated <?php echo $job_date_modified; ?></span>
                </span>
            </div>
        </div>

        <div class="wpjb-card__bd">
            <div class="wpjb-userContent">
                <?php echo $job_description; ?>
            </div>
        </div>

        <button class="wpjb-btn btn__moreInfo">
            <a href="<?php echo $permalink = get_permalink($post_id); ?>">View Full Details </a>
        </button>
    </div>
</div>
