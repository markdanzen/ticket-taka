<?php
/**
 * The template for displaying product content in the single-product.php template
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-single-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.6.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

/**
 * Hook: woocommerce_before_single_product.
 *
 * @hooked woocommerce_output_all_notices - 10
 */
do_action( 'woocommerce_before_single_product' );

if ( post_password_required() ) {
	echo get_the_password_form(); // WPCS: XSS ok.
	return;
}
?>

<?php
    // Get product categories
    $product_categories = get_the_terms(get_the_ID(), 'product_cat');
    $category_names = array();
    if (!empty($product_categories)) {
        $category_names = array_reverse(array_column($product_categories, 'name'));
    }

    //ACF Fields
    $promoBanner = get_field('promotional_banner');
    $featBanner = get_field('featured_banner');
    $venue = get_field('venue_location');
    $date_time = get_field('event_date_picker');
?>

<div class="product-header">
    <div class="outer-container">
        <div class="inner-container flex-container">

            <?php if( !empty( $promoBanner ) ) :?>
                <div class="product-featured-image" style="background-image: url('<?php echo esc_url($promoBanner['url']); ?>');">
                    
                </div>
            <?php endif; ?>

            <div class="event-details" style="background-image: url('<?php echo esc_url( get_template_directory_uri() . '/assets/img/Event-Featured-Banner.jpg' ); ?>');">

                <?php if (!empty($category_names)) : ?>
                    <p class="event-category"><?php echo $category_names[count($category_names) - 1]; ?></p>
                <?php endif; ?>

                <p class="event-title"> <?php the_title(); ?></p>

                <?php 
                    // Check if the field has a value
                    if ($date_time) {
                        // Create a DateTime object from the field value
                        $date_time_obj = new DateTime($date_time);

                        // Format the date-time
                        echo '<p class="event-date">' . $date_time_obj->format('F j, Y | g:i a') . '</p>';
                    }
                ?>

                <?php if( $venue ): ?>
                    <p class="event-venue"><?php echo esc_html( $venue->post_title ); ?></p>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>

<div id="product-<?php the_ID(); ?>" <?php wc_product_class( '', $product ); ?>>

	<?php
	/**
	 * Hook: woocommerce_before_single_product_summary.
	 *
	 * @hooked woocommerce_show_product_sale_flash - 10
	 * @hooked woocommerce_show_product_images - 20
	 */
	do_action( 'woocommerce_before_single_product_summary' );
	?>

	<div class="summary entry-summary">
		<?php
		/**
		 * Hook: woocommerce_single_product_summary.
		 *
		 * @hooked woocommerce_template_single_title - 5
		 * @hooked woocommerce_template_single_rating - 10
		 * @hooked woocommerce_template_single_price - 10
		 * @hooked woocommerce_template_single_excerpt - 20
		 * @hooked woocommerce_template_single_add_to_cart - 30
		 * @hooked woocommerce_template_single_meta - 40
		 * @hooked woocommerce_template_single_sharing - 50
		 * @hooked WC_Structured_Data::generate_product_data() - 60
		 */
		do_action( 'woocommerce_single_product_summary' );
		?>
	</div>

	<?php
	/**
	 * Hook: woocommerce_after_single_product_summary.
	 *
	 * @hooked woocommerce_output_product_data_tabs - 10
	 * @hooked woocommerce_upsell_display - 15
	 * @hooked woocommerce_output_related_products - 20
	 */
	do_action( 'woocommerce_after_single_product_summary' );
	?>
    
</div>

<?php do_action( 'woocommerce_after_single_product' ); ?>
