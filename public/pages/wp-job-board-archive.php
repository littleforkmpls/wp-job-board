<?php

if (is_archive() && get_post_type() == 'wjb_bh_job_order') {
    $post_id  = get_the_ID();
    $job_meta = get_job_meta($post_id);
} else {
    throw new Exception('The wpjb_archive shortcode must be placed within an WordPress compatible "archive" template.');
}

global $wp_query;

$results_count  = $wp_query->found_posts;

$industry_terms = get_filter_terms('wjb_bh_job_industry_tax');
$category_terms = get_filter_terms('wjb_bh_job_category_tax');
$location_terms = get_filter_terms('wjb_bh_job_location_tax');
$type_terms     = get_filter_terms('wjb_bh_job_type_tax');

?>

<div id="wpjb">
    <div class="wpjb-archive">
        <div class="wpjb-grid">
            <div class="wpjb-grid__item">
                <div class="wpjb-facet">
                    <div class="wpjb-facet__hd">
                        <form method="get" action="<?php echo site_url("/jobs/") ?>">
                        <input type="search" name="s" class="wpjb-search__text-input" id="wpjbSearchTextInput" placeholder="🔍 Search"  value="<?php the_search_query(); ?>"/>
                        <input type="submit" class="wpjb-search__submit" id="wpjbSearchSubmit" value="Search" />
                        </form>
                        <button class="wpjb-btn__clearSettings">Clear Search Settings</button>
                        <button class="wpjb-btn btn__filter">Filters +</button>
                    </div>
                    <div class="wpjb-facet__section-container">
                        <?php if ($industry_terms) : ?>
                            <div class="wpjb-facet__section">
                                <details open>
                                    <summary>
                                        Industry
                                    </summary>
                                    <ul class="wpjb-facet__section__list">
                                        <?php foreach ($industry_terms as $industry_term) : ?>
                                            <?php
                                            $industry_id = esc_attr($industry_term->term_id);
                                            $industry_name = esc_html($industry_term->name);
                                            ?>
                                            <li>
                                                <label>
                                                    <input type="checkbox" value="<?php echo $industry_id; ?>" />
                                                    <?php echo $industry_name; ?>
                                                </label>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </details>
                            </div>
                        <?php endif; ?>
                        <?php if ($category_terms) : ?>
                            <div class="wpjb-facet__section">
                                <details open>
                                    <summary>
                                        Category
                                    </summary>
                                    <ul class="wpjb-facet__section__list">
                                        <?php foreach ($category_terms as $category_term) : ?>
                                            <?php
                                            $category_id = esc_attr($category_term->term_id);
                                            $category_name = esc_html($category_term->name);
                                            ?>
                                            <li>
                                                <label>
                                                    <input type="checkbox" value="<?php echo $category_id; ?>" />
                                                    <?php echo $category_name; ?>
                                                </label>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </details>
                            </div>
                        <?php endif; ?>
                        <?php if ($location_terms) : ?>
                            <div class="wpjb-facet__section">
                                <details open>
                                    <summary>
                                        Location
                                    </summary>
                                    <ul class="wpjb-facet__section__list">
                                        <?php foreach ($location_terms as $location_term) : ?>
                                            <?php
                                            $location_id = esc_attr($location_term->term_id);
                                            $location_name = esc_html($location_term->name);
                                            ?>
                                            <li>
                                                <label>
                                                    <input type="checkbox" value="<?php echo $location_id; ?>" />
                                                    <?php echo $location_name; ?>
                                                </label>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </details>
                            </div>
                        <?php endif; ?>
                        <?php if ($type_terms) : ?>
                            <div class="wpjb-facet__section">
                                <details open>
                                    <summary>
                                        Employment Type
                                    </summary>
                                    <ul class="wpjb-facet__section__list">
                                        <?php foreach ($type_terms as $type_term) : ?>
                                            <?php
                                            $type_id = esc_attr($type_term->term_id);
                                            $type_name = esc_html($type_term->name);
                                            ?>
                                            <li>
                                                <label>
                                                    <input type="checkbox" value="<?php echo $type_id; ?>" />
                                                    <?php echo $type_name; ?>
                                                </label>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </details>
                            </div>
                        <?php endif; ?>
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

                                $job_title                  = $job_meta->title;
                                $job_description            = wp_trim_words($job_meta->publicDescription, 55);
                                $job_employment_type        = $job_meta->employmentType;
                                $job_location_city          = $job_meta->address->city;
                                $job_location_state         = $job_meta->address->state;
                                $job_location_postal_code   = $job_meta->address->zip;
                                $job_location_country_code  = $job_meta->address->countryCode;
                                $job_date_published         = get_formatted_date($job_meta->dateLastPublished);
                                $job_date_modified          = get_relative_date($job_meta->dateLastModified);
                                $job_date_published_iso8601 = get_iso8601_date($job_meta->dateLastPublished);
                                $job_date_modified_iso8601  = get_iso8601_date($job_meta->dateLastModified);

                                ?>
                                <div id="archive">
                                    <div class="wpjb-card">
                                        <div class="wpjb-card__hd">
                                            <h3 class="wpjb-card__title">
                                                <?php echo $job_title; ?>
                                            </h3>
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
                                            <h4>
                                                About the job
                                            </h4>
                                        </div>
                                        <div class="wpjb-card__bd">
                                            <div class="wpjb-userContent">
                                                <?php echo $job_description; ?>
                                            </div>
                                        </div>
                                        <button class="wpjb-btn btn__moreInfo">
                                            Full Job Description
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
    </div>
</div>
