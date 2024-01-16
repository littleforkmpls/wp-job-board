<?php

if (is_archive() && get_post_type() == 'wjb_bh_job_order') {
    $post_id  = get_the_ID();
    $job_meta = get_job_meta($post_id);
} else {
    throw new Exception('The wpjb_archive shortcode must be placed within an WordPress compatible "archive" template.');
}

global $wp_query;

$results_count  = $wp_query->found_posts;

$current_term_id = !empty(get_queried_object()->term_taxonomy_id) ? get_queried_object()->term_taxonomy_id : 0;
?>

<div id="wpjb">
    <div class="wpjb-archive">
        <div class="wpjb-grid">
            <div class="wpjb-grid__item">
                <div class="wpjb-facet">
                    <div class="wpjb-facet__hd">
                        <button class="wpjb-btn__clearSettings">🅇 Clear Search and Filter Settings</button>
                        <div class="wpjb-search__container">
                            <form class="wpjb-search__form" method="get" action="<?php echo site_url("/jobs/") ?>">
                                <input type="search" name="s" class="wpjb-search__text-input" id="wpjbSearchTextInput" placeholder="" value="<?php the_search_query(); ?>" />
                                <input type="submit" class="wpjb-search__submit" id="wpjbSearchSubmit" value="Search" />
                            </form>
                        </div>
                        <button class="wpjb-btn btn__filter">Filters +</button>
                    </div>
                    <div class="wpjb-facet__section-container">
                        <div class="wpjb-facet__section">
                            <details open>
                                <summary>
                                    Industry
                                </summary>
                                <?php echo get_taxonomy_filters('wjb_bh_job_industry_tax', $current_term_id); ?>
                            </details>
                        </div>
                        <div class="wpjb-facet__section">
                            <details open>
                                <summary>
                                    Category
                                </summary>
                                <?php echo get_taxonomy_filters('wjb_bh_job_category_tax', $current_term_id); ?>
                            </details>
                        </div>
                        <div class="wpjb-facet__section">
                            <details open>
                                <summary>
                                    Location
                                </summary>
                                <?php echo get_taxonomy_filters('wjb_bh_job_location_tax', $current_term_id); ?>
                            </details>
                        </div>
                        <div class="wpjb-facet__section">
                            <details open>
                                <summary>
                                    Employment Type
                                </summary>
                                <?php echo get_taxonomy_filters('wjb_bh_job_type_tax', $current_term_id); ?>
                            </details>
                        </div>
                    </div>
                </div>
            </div>
            <div class="wpjb-grid__item">
                <div class="wpjb-results">
                    <div class="wpjb-results__hd">
                        <h2 class="wpjb-results__title">
                            <?php echo $results_count; ?> Open Positions
                        </h2>
                    </div>
                    <div class="wpjb-results__bd">
                        <?php if (have_posts()) : ?>
                            <?php while (have_posts()) : ?>
                                <?php
                                the_post();
                                $post_id  = get_the_ID();
                                $job_meta = get_job_meta($post_id);

                                /*
                                 * Setup values for template usage
                                 *
                                 * Note:
                                 * since dateLastPublished can be null AND dateLastModified cannot
                                 * dateLastModified is used as fallback for dateLastPublished null scenarios
                                 */
                                $job_title                  = !empty($job_meta->title) ? $job_meta->title : get_the_title();
                                $job_description            = !empty($job_meta->publicDescription) ? wp_trim_words($job_meta->publicDescription, 45) : 'No job description provided.';
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
                            <?php endwhile; ?>

                            

                        <?php else : ?>
                            No jobs found.
                        <?php endif; ?>
                    </div>

                </div>
            </div>
        </div>
        <div class="wpjb-pagination">
            <?php echo the_posts_pagination(array(
                'type' => 'list'
            )); ?>
        </div>
    </div>
</div>
