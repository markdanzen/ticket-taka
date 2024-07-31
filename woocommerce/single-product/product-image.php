<?php
/**
 * Single Product Image
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/product-image.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 7.8.0
 */

defined( 'ABSPATH' ) || exit;

// Note: `wc_get_gallery_image_html` was added in WC 3.3.2 and did not exist prior. This check protects against theme overrides being used on older versions of WC.
if ( ! function_exists( 'wc_get_gallery_image_html' ) ) {
	return;
}

global $product;


// Get product categories
$product_categories = get_the_terms(get_the_ID(), 'product_cat');
$category_names = array();
if (!empty($product_categories)) {
    $category_names = array_reverse(array_column($product_categories, 'name'));
}

//ACF Fields
$promoBanner = get_field('promotional_banner');
$venue = get_field('venue_location');
$date_time = get_field('event_date_picker');


$columns           = apply_filters( 'woocommerce_product_thumbnails_columns', 4 );
$post_thumbnail_id = $product->get_image_id();
$wrapper_classes   = apply_filters(
	'woocommerce_single_product_image_gallery_classes',
	array(
		'woocommerce-product-gallery',
		'woocommerce-product-gallery--' . ( $post_thumbnail_id ? 'with-images' : 'without-images' ),
		'woocommerce-product-gallery--columns-' . absint( $columns ),
		'images',
	)
);
?>


<div class="<?php echo esc_attr( implode( ' ', array_map( 'sanitize_html_class', $wrapper_classes ) ) ); ?>" data-columns="<?php echo esc_attr( $columns ); ?>" style="opacity: 0; transition: opacity .25s ease-in-out;">

    <div class="woocommerce-featured-image-wrapper">

        <?php if( $venue ): ?>
            <p class="event-venue"><?php echo esc_html( $venue->post_title ); ?></p>
        <?php endif; ?>

        <div class="woocommerce-product-gallery__wrapper">
            <?php
            if ( $post_thumbnail_id ) {
                $html = wc_get_gallery_image_html( $post_thumbnail_id, true );
            } else {
                $html  = '<div class="woocommerce-product-gallery__image--placeholder">';
                $html .= sprintf( '<img src="%s" alt="%s" class="wp-post-image" />', esc_url( wc_placeholder_img_src( 'woocommerce_single' ) ), esc_html__( 'Awaiting product image', 'woocommerce' ) );
                $html .= '</div>';
            }

            echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', $html, $post_thumbnail_id ); // phpcs:disable WordPress.XSS.EscapeOutput.OutputNotEscaped

            do_action( 'woocommerce_product_thumbnails' );
            ?>
        </div>


        <?php 
            global $product;

            // Get the current product ID dynamically
            $product_id = get_the_ID(); // Example of how you might get the product ID in a WordPress context

            if ($product_id) {
                $product = wc_get_product($product_id); // Get the product object

                if ($product && $product->is_type('variable')) {
                    $variations = $product->get_available_variations(); 
                    $var_data = []; 

                    // Collect unique attribute combinations
                    foreach ($variations as $variation) {
                        $attributes = $variation['attributes'];
                        $price = $variation['display_price'];
                        $attribute_key = implode('-', array_values($attributes));
                        //$var_data[$attribute_key] = $attributes;

                        // Update or initialize max price for this attribute combination
                        if (!isset($var_data[$attribute_key])) {
                            $var_data[$attribute_key] = [
                                'attributes' => $attributes,
                                'max_price' => $price,
                            ];
                        } else {
                            $var_data[$attribute_key]['max_price'] = max($var_data[$attribute_key]['max_price'], $price);
                        }
                    }

                    // Output each attribute combination with max price
                    echo '<div class="product-variations-wrapper">';

                    $css_classes = array();
                    foreach ($var_data as $data) {
                        foreach ($data['attributes'] as $attrName => $var_name) {
                            
                            $css_classes = array();
                            $sanitized_value = sanitize_html_class(strtolower($var_name));
                            if (!empty($sanitized_value)) {
                                $css_classes[] = $sanitized_value;
                            }
                            $class_attribute = esc_attr(implode(' ',  $css_classes)) ;

                            echo '<button class="product-variations-item filter-btn" data-filter="' . $class_attribute . '" >';
                            echo $var_name . ' - ' . wc_price($data['max_price']) . '<br>';
                            echo '</button>';
                            
                        }
                    }
                    echo '</div>';
                    
                } else {
                    echo 'Product is not a variable product or does not exist.';
                }
            } else {
                echo 'No product ID detected.';
            }
        ?>
    </div>


    <!-- CUSTOM HTML AREA -->
    
    <div class="event-details-wrapper">

        <div class="inner-container event-header flex-container">
            <h3>Event Details</h3>
        </div>

        <div class="event-description-wrapper">
            <div class="inner-container event-description flex-container">

                <?php if( !empty( $promoBanner ) ) :?>
                    <div class="product-featured-image">
                        <img src="<?php echo esc_url($promoBanner['url']); ?>" alt="" class="featured-image">
                    </div>
                <?php endif; ?>

                <div class="event-details">

                    <?php if (!empty($category_names)) : ?>
                        <p class="product-categories"><?php echo $category_names[count($category_names) - 1]; ?></p>
                    <?php endif; ?>

                    <h1 class="product-title"><?php the_title(); ?></h1>

                    <?php 
                        // Check if the field has a value
                        if ($date_time) {
                            // Create a DateTime object from the field value
                            $date_time_obj = new DateTime($date_time);
                            // Format the date-time
                            echo '<p class="event-date">' . $date_time_obj->format('F j, Y | g:i') . '</p>';
                        }
                    ?>

                    <?php if( $venue ): ?>
                        <p class="event-venue"><?php echo esc_html( $venue->post_title ); ?></p>
                    <?php endif; ?>

                </div>
            </div>

            <div class="event-content-area">
                <?php 
                $event_description = $product->get_description();
                $allowed_html = array(
                    'a' => array(
                        'href' => array(),
                        'title' => array()
                    ),
                    'br' => array(),
                    'em' => array(),
                    'strong' => array(),
                    'p' => array(),
                    'ul' => array(),
                    'li' => array(),
                    // Add more allowed tags as needed
                );
                echo wp_kses( $event_description, $allowed_html );
                ?>
            </div>
        </div><!-- EVENT DESCRIPTION WRAPPER -->

    </div>


</div>

