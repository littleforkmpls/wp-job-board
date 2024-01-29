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
                        <button class="wpjb-btn wpjb-btn__clearSettings">🅇 Clear Search and Filter Settings</button>
                        <div class="wpjb-search__container">
                            <form class="wpjb-search__form" method="get" action="<?php echo site_url("/jobs/") ?>">
                                <input type="search" name="s" class="wpjb-search__text-input" id="wpjbSearchTextInput" placeholder="" value="<?php the_search_query(); ?>" />
                                <input type="submit" class="wpjb-btn wpjb-search__submit" id="wpjbSearchSubmit" value="Search" />
                        </div>
                        <div class="wpjb-search__filter">
                            <button class="wpjb-btn btn__filter">Filters <span class="btn__filter--plus">+</span></button>
                            <h2 class="wpjb-results__title--small">
                                <?php echo $results_count; ?> Open Positions
                            </h2>
                        </div>
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
                    </form>
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
                                require plugin_dir_path(__DIR__) . 'partials/wp-job-board-archive-card.php';
                                ?>
                            <?php endwhile; ?>
                        <?php else : ?>
                            No jobs found.
                        <?php endif; ?>
                    </div>

                </div>
            </div>
        </div>

        <ul class="wpjb-pagination">
            <?php
            $big = 999999999; // need an unlikely integer

            echo paginate_links(array(
                'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
                'format' => '?paged=%#%',
                'current' => max(1, get_query_var('paged')),
                'total' => $wp_query->max_num_pages,
                'end_size' => 1,
                'mid_size' => 1,
                'prev_next' => true,
                'prev_text' => __('Previous'),
                'next_text' => __('Next'),
                'type' => 'plain',
                'add_args' => false,
                'add_fragment' => '',
            ));
            ?>
        </ul>

        <ul class="wpjb-pagination__filtered"></ul>

    </div>
</div>
