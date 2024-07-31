<?php

function single_product_featured_banner_shortcode() {

// Ensure we are on a single product page
    if (!is_product()) {
        return '';
    }

    // Get the global product object
    global $product;

    // Start output buffering
    ob_start();

    // Check if ACF is active
    if (function_exists('get_field')) {
        // Get custom fields (replace 'your_custom_field' with your actual ACF field names)
        $venueLoc = get_field('venue_location', $product->get_id());
        $eventDatePicker = get_field('event_date_picker', $product->get_id());
        $promoBanner = get_field('promotional_banner', $product->get_id());
        $featBanner = get_field('featured_banner', $product->get_id());
        ?>
        <div class="product-custom-info">
            <h1><?php echo get_the_title($product->get_id()); ?></h1>
            <?php if ($custom_field_1) : ?>
                <p><?php echo esc_html($custom_field_1); ?></p>
            <?php endif; ?>
            <?php if ($custom_field_2) : ?>
                <p><?php echo esc_html($custom_field_2); ?></p>
            <?php endif; ?>
            <!-- Add more fields as needed -->
        </div>
        <?php
    } else {
        echo '<p>ACF is not active.</p>';
    }

    // Return the buffered output
    return ob_get_clean();

}
add_shortcode('single_product_featured_banner', 'single_product_featured_banner_shortcode');



  
