<?php
function get_catetgory_listing_shortcode() {
    if (is_product_category()) {
        global $wp_query;
        $current_category = $wp_query->get_queried_object();

        // Display parent category
        if ($current_category->parent != 0) {
            $parent_category = get_term($current_category->parent, 'product_cat');
            echo '<h2>Parent Category: ' . $parent_category->name . '</h2>';
        }

        // Display current category
        echo '<h2>Current Category: ' . $current_category->name . '</h2>';

        // Get and display secondary categories (child categories)
        $child_categories = get_terms(array(
            'taxonomy' => 'product_cat',
            'child_of' => $current_category->term_id,
            'hide_empty' => false,
        ));


        if (!empty($child_categories)) {
            echo '<h3>Subcategories:</h3>';
            echo '<div class="event-container">';
            foreach ($child_categories as $child_category) {
                echo '<a href=" ' . get_term_link($child_category) . ' ">';
                echo '<li>' . $child_category->name . '</li>';

                echo '</a>';
            }
            echo '</div>';
        }

        // Get products in the current category

        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
        $args = array(
            'post_type' => 'product',
            'posts_per_page' => 10,
            'paged' => $paged,
            'tax_query' => array(
                array(
                    'taxonomy' => 'product_cat',
                    'field' => 'term_id',
                    'terms' => $current_category->term_id,
                ),
            ),
        );
        $products = new WP_Query($args);

        if ($current_category->parent != 0) {
            if ($products->have_posts()) {
                echo '<div class="product-titles">';
                echo '<h3>Products in ' . $current_category->name . ' Category:</h3>';

                echo '<ul>';
                while ($products->have_posts()) {
                    $products->the_post();
                    echo '<a href=" ' . get_the_permalink() . ' ">';
                    echo '<li>' . get_the_title() . '</li>';
                    echo '</a>';
                }
                echo '</ul>';

                // Add pagination before the product listing
                echo '<div class="pagination">';
                $big = 999999999;
                echo paginate_links(array(
                    'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
                    'format' => '?paged=%#%',
                    'current' => max(1, $paged),
                    'total' => $products->max_num_pages,
                    'prev_text' => '&laquo; Previous',
                    'next_text' => 'Next &raquo;',
                ));
                echo '</div>';

                echo '</div>';
                wp_reset_postdata();
            }
        }
    }
}
add_shortcode('get_catetgory_listing', 'get_catetgory_listing_shortcode');


// Hook into WooCommerce before shop loop
add_action('woocommerce_before_shop_loop', 'display_hierarchical_categories');

function display_hierarchical_categories() {
    if (is_product_category()) {
        // Get all top-level product categories
        $args = array(
            'taxonomy'   => 'product_cat',
            'orderby'    => 'name',
            'show_count' => 0,
            'pad_counts' => 0,
            'hierarchical' => 1,
            'hide_empty' => 0,
            'parent'     => 0
        );

        $parent_categories = get_categories($args);

        if ($parent_categories) {
            echo '<div class="parent-categories">';
            echo '<h2>Product Categories</h2>';
            echo '<ul>';

            foreach ($parent_categories as $parent_category) {
                echo '<li class="parent-category" data-term-id="' . $parent_category->term_id . '">';
                echo '<span>' . $parent_category->name . '</span>';
                echo '<ul class="child-categories" id="child-categories-' . $parent_category->term_id . '" style="display:none;">';

                // Get child categories
                $child_args = array(
                    'taxonomy'   => 'product_cat',
                    'child_of'   => $parent_category->term_id,
                    'hide_empty' => 0
                );
                $child_categories = get_categories($child_args);

                foreach ($child_categories as $child_category) {
                    echo '<li class="child-category" data-term-id="' . $child_category->term_id . '">';
                    echo '<span>' . $child_category->name . '</span>';
                    echo '</li>';
                }

                echo '</ul>'; // End child-categories
                echo '</li>'; // End parent-category
            }

            echo '</ul>';
            echo '</div>'; // End parent-categories
        }
    }
}