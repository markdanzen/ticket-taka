<?php

function my_theme_scripts() {
    wp_enqueue_script('ajax-script', get_template_directory_uri() . '/assets/js/event-listing.js', array('jquery'), null, true);
    wp_localize_script('ajax-script', 'ajax_object', array(
        'ajaxurl' => admin_url('admin-ajax.php')
    ));
}
add_action('wp_enqueue_scripts', 'my_theme_scripts');

require_once( ABSPATH . 'wp-content/plugins/advanced-custom-fields-pro/acf.php' );

/**
 * Ajax function to retrieve products
 */
function get_products() {

    // Load the ACF plugin
    require_once( ABSPATH . 'wp-content/plugins/advanced-custom-fields-pro/acf.php' );
    add_filter('acf/settings/enqueue_js', '__return_true');

	global $products, $post;

    if ( is_archive() ) {
        $team_name = '';
    } else {
        $team_name = $post->post_title;
    }

    $paged = isset($_POST['paged']) ? $_POST['paged'] : 1;
    $category_id = isset($_POST['category_id']) ? intval($_POST['category_id']) : 0;
    

    $args = array(
        'post_type' => 'product',
		'post_status' => 'publish',
        'posts_per_page' => 10,
        'paged' => $paged,
    );

    if ($category_id) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'product_cat',
                'field'    => 'term_id',
                'terms'    => $category_id,
            ),
        );
    }

    ob_start();

    $product_query = new WP_Query($args);

    if ($product_query->have_posts()) {
        while ($product_query->have_posts()) {

            $product_query->the_post();
            $product = wc_get_product(get_the_ID());
            $product_url = get_permalink($product->get_id());
	
            // Get the ACF field data
            $fields = get_fields('team_flag', $product->get_id()); // Replace 'team_flag' with your field group name

            // Access the home team and away team data
            $home_team_id = isset($fields['home_team']);
            $away_team_id = isset($fields['away_team']);
            $home_team_title = get_the_title($home_team_id);
            $away_team_title = get_the_title($away_team_id);

            $event_id       = $post->ID;
            $home_team      = get_post_meta( $event_id, 'team', true );
            $away_team      = get_post_meta( $event_id, 'awayTeam', true );

            // Get product categories
			$product_categories = get_the_terms(get_the_ID(), 'product_cat');
			$category_names = array();
			if (!empty($product_categories)) {
				$category_names = array_reverse(array_column($product_categories, 'name'));
			}

            // Get product image
            $product_image = wp_get_attachment_image_src(get_post_thumbnail_id($product->get_id()), 'full');
			
            ?>

			<div class="event-container">

				<div class="event-date">
					<div class="date-format">
						<span class="month"><?= date( 'M', strtotime( get_post_meta( $product->get_id(), 'start', true ) ) ); ?></span>
						<span class="date"><?= date( 'j', strtotime( get_post_meta( $product->get_id(), 'start', true ) ) ); ?></span>
						<span class="year"><?= date( 'Y', strtotime( get_post_meta( $product->get_id(), 'start', true ) ) ); ?></span>
					</div>
				</div>

				<div class="event-details">

                    <div class="event-details-group">

                        <div class="event-team">
                            <div class="pb-top" style="background-image: url('<?= $event_image['url']; ?> ');">

                                <div class="pb-home pb-side" onclick="location.href='<?= get_the_permalink($home_team_id); ?>';">
                                    <div class="pb-team-icon">
                                        <?php
                                        $team_flag_home = get_field('team_flag', $home_team_id);
                                        if ($team_flag_home) {
                                            if ($team_flag_home['use_colors_or_image']) { ?>
                                                <img src="<?= esc_url($team_flag_home['team_image']); ?>" alt="<?= esc_attr(get_the_title($home_team_id)); ?>">
                                            <?php } else { ?>
                                                <div class="pb-team-icon-wrap">
                                                    <div class="pb-team-icon-left" style="background-color: <?= esc_attr($team_flag_home['color_1']); ?>"></div>
                                                    <div class="pb-team-icon-right" style="background-color: <?= esc_attr($team_flag_home['color_2']); ?>"></div>
                                                </div>
                                            <?php }
                                        } else {
                                            // Debug: Field not found
                                            echo 'Team flag for away team not found';
                                        }
                                        ?>
                                    </div>
                                </div>
                                <div class="versus">VS</div>
                                <div class="pb-away pb-side" onclick="location.href='<?= get_the_permalink( $away_team ); ?>';">
                                    <div class="pb-team-icon">
                                        <?php $team_flag_away = get_field( 'team_flag', $away_team );
                                            if ( $team_flag_away ) {
                                                if ( $team_flag_away['use_colors_or_image'] ) { ?>
                                                    <img src="<?= $team_flag_away['team_image']; ?>" alt="<?= get_the_title( $away_team ); ?>">
                                                <?php } else { ?>
                                                    <div class="pb-team-icon-wrap">
                                                        <div class="pb-team-icon-left" style="background-color: <?= $team_flag_away['color_1']; ?>"></div>
                                                        <div class="pb-team-icon-right" style="background-color: <?= $team_flag_away['color_2']; ?>"></div>
                                                    </div>
                                                <?php }
                                            } ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="event-information">
                            <?php if (!empty($category_names)) : ?>
                                <p class="product-categories"><strong> <?php echo $category_names[count($category_names) - 1]; ?> </strong></p>
                            <?php endif; ?>

                            <a href="<?= get_permalink($product->get_id()); ?>">
                                <h3><?php echo $product->get_name(); ?></h3>
                            </a>
                            
                            <div class="event-info">
                                <p class="event-time"><strong><?= date( 'H:i', strtotime( get_post_meta( $product->get_id(), 'start', true ) ) ); ?></strong></p>
                                <p class="event-location"><?= get_post( get_post_meta( $product->get_id(), 'venue', true ) )->post_title; ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="product-cta">
					    <a class="button" href="<?= get_permalink($product->get_id()); ?>">Buy Tickets</a>
                    </div>

				</div>


			</div>

            <!-- End Event Details Row -->
            <?php
        }
    } else {
        echo 'No products found.';
    }

    $max_pages = $product_query->max_num_pages;
    wp_reset_postdata();
    $data = ob_get_clean();

    wp_send_json_success(array(
        'products' => $data,
        'max_pages' => $max_pages
    ));
    wp_die();
}
add_action('wp_ajax_get_products', 'get_products');
add_action('wp_ajax_nopriv_get_products', 'get_products');

/**
 * Shortcode to display products with AJAX pagination
 */
function ajax_products_shortcode($atts) {
    // Enqueue the necessary scripts
    wp_enqueue_script('ajax-script');

    // Start output buffering
    ob_start();
    ?>

    <?php
    // Fetch product categories
    $product_categories = get_terms(array(
        'taxonomy' => 'product_cat',
        'hide_empty' => false,
    ));
    ?>

    <select id="product-category-dropdown">
        <option value="">Select Category</option>
        <?php foreach ($product_categories as $category) : ?>
            <option value="<?php echo esc_attr($category->term_id); ?>"><?php echo esc_html($category->name); ?></option>
        <?php endforeach; ?>
    </select>

    <div class="ajax-products-container">
        <div id="product-results" class="products"></div>
        <div class="pagination"></div>
    </div>
    <?php

    // Get the HTML output
    $output = ob_get_clean();


    // Return the HTML output
    return $output;
}
add_shortcode('ajax_products', 'ajax_products_shortcode');


// Custom AJAX handler for autocomplete search
function custom_autocomplete_search() {
    // Retrieve search query from AJAX request
    $query = isset($_POST['query']) ? sanitize_text_field($_POST['query']) : '';

    // Perform Algolia search
    $results = apply_filters('algolia_autocomplete_results', array(), $query);

    // Return search results as JSON
    wp_send_json_success($results);
    wp_die();
}
add_action('wp_ajax_custom_autocomplete_search', 'custom_autocomplete_search');
add_action('wp_ajax_nopriv_custom_autocomplete_search', 'custom_autocomplete_search');

function homepage_latest_news_section() {
    
}
add_shortcode('homepage_latest_news_shortcode', 'homepage_latest_news_section');