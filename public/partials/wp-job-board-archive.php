<?php

global $wp_query;

$results_count  = $wp_query->found_posts;

$industry_terms = get_filter_terms('wjb_bh_job_industry_tax');
$category_terms = get_filter_terms('wjb_bh_job_category_tax');
$location_terms = get_filter_terms('wjb_bh_job_location_tax');
$type_terms     = get_filter_terms('wjb_bh_job_type_tax');

?>

<div id="wpjb">
    <div class="wpjb-grid">
        <div class="wpjb-grid__item">
            <div class="wpjb-facet">
                <div class="wpjb-facet__section">
                    <button>Reset</button>
                    <input type="search" />
                </div>
                <?php if ($industry_terms) : ?>
                    <div class="wpjb-facet__section">
                        <details>
                            <summary>
                                Filter by Industry
                            </summary>
                            <ul>
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
                        <details>
                            <summary>
                                Filter by Catgory
                            </summary>
                            <ul>
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
                        <details>
                            <summary>
                                Filter by Location
                            </summary>
                            <ul>
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
                        <details>
                            <summary>
                                Filter by Employment Type
                            </summary>
                            <ul>
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
        <div class="wpjb-grid__item">
            <div class="wpjb-results">
                <div class="wpjb-results__hd">
                    <h2>
                        <?php echo $results_count; ?> Open Positions
                    </h2>
                </div>
                <div class="wpjb-results__bd">
                    <?php if (have_posts()) : ?>
                        <?php while (have_posts()) : ?>
                            <?php the_post(); ?>
                            <div class="wpjb-card">
                                <div class="wpjb-card__hd">
                                    <h3>
                                        <?php the_title(); ?>
                                    </h3>
                                </div>
                                <div class="wpjb-card__meta">

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
