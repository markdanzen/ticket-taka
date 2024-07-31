<?php
function enqueue_tickettaka_css() {

    //Styling
    wp_enqueue_style( 'tickettaka-custom-styling', get_template_directory_uri() . '/assets/css/custom-styling.css', array(), '1.0', 'all' );
    wp_enqueue_style( 'owl-carousel-css', get_template_directory_uri() . '/assets/lib/css/owl.carousel.min.css', array(), '1.0', 'all' );

    // Scripts
    wp_enqueue_script( 'owl-carousel-js', get_template_directory_uri() . '/assets/lib/js/owl.carousel.min.js', array(),'1.0', true );
    wp_enqueue_script( 'tickettaka-global-script', get_template_directory_uri() . '/assets/js/global-script.js', array(),'1.0', true );
    wp_enqueue_script( 'add-to-cart-script', get_template_directory_uri() . '/assets/js/add-to-cart.js', array(),'1.0', true );

    if (is_cart()) {
        wp_enqueue_script( 'cart-auto-update', get_template_directory_uri() . '/assets/js/cart-subtotal.js', array(),'1.0', true );
        wp_localize_script('cart-auto-update', 'cart_auto_update_params', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'update_cart_nonce' => wp_create_nonce('update-cart'),
        ));
    }
}
add_action( 'wp_enqueue_scripts', 'enqueue_tickettaka_css' );

// Update cart subtotal when adjusting quantity selector
function cart_quantity_update_handler() {
    // Check nonce for security
    if (!isset($_POST['update_cart_nonce']) || !wp_verify_nonce($_POST['update_cart_nonce'], 'update-cart')) {
        wp_send_json_error('Invalid nonce');
        wp_die();
    }

    $cart_updated = false;

    // Loop through cart items
    foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
        $quantity = isset($_POST['cart'][$cart_item_key]['qty']) ? wc_stock_amount($_POST['cart'][$cart_item_key]['qty']) : 0;
        if ($quantity != $cart_item['quantity']) {
            WC()->cart->set_quantity($cart_item_key, $quantity, true);
            $cart_updated = true;
        }
    }

    if ($cart_updated) {
        WC()->cart->calculate_totals();
    }

    WC_AJAX::get_refreshed_fragments();
    wp_die();
}
add_action('wp_ajax_cart_quantity_update', 'cart_quantity_update_handler');
add_action('wp_ajax_nopriv_cart_quantity_update', 'cart_quantity_update_handler');

function tt_event_times_meta() {
    add_meta_box( 'event-times', 'Event Times', function () {
        global $post;
        $end = get_post_meta( $post->ID, 'end', true );
        ?>
        <label for="start">Start Date: </label>
        <input type="datetime-local" id="start" name="start" value="<?= get_post_meta( $post->ID, 'start', true ); ?>">
        <?php
        if ( ! $end ) {
            return;
        }
        ?>
        <label for="end">End Date: </label>
        <input type="datetime-local" id="end" name="end" value="<?= $end; ?>">
        <?php
    }, 'product' );
}
add_action( 'add_meta_boxes', 'tt_event_times_meta' );

function tt_event_teams_meta() {
    add_meta_box( 'event-teams', 'Event Teams', function () {
        global $post;
        $homeTeam = get_post_meta( $post->ID, 'team', true );
        $awayTeam = get_post_meta( $post->ID, 'awayTeam', true );
        $teams    = get_posts( [ 'post_type' => 'team', 'numberposts' => - 1 ] );
        foreach ( [ 'home' => (int) $homeTeam, 'away' => (int) $awayTeam ] as $side => $Team ) {
            ?>
            <label for="<?= $side ?>"><?= ucfirst( $side ); ?> Team: </label>
            <select id="<?= $side ?>" name="<?= $side ?>">
                <option value="">no team</option>
                <?php foreach ( $teams as $team ) { ?>
                    <option value="<?= $team->ID ?>"<?= $team->ID === $Team ? ' selected' : ''; ?>><?= $team->post_title; ?></option>
                <?php } ?>
            </select>
            <?php
        }
    }, 'product' );
}
add_action( 'add_meta_boxes', 'tt_event_teams_meta' );

function tt_edit_match_teams( $post_id ) {
    $post = get_post( $post_id );
    if ( $post->post_type !== 'product' ) {
        return;
    }
    if ( array_key_exists( 'home', $_POST ) ) {
        update_post_meta( $post_id, 'team', $_POST['home'] );
    }
    if ( array_key_exists( 'away', $_POST ) ) {
        update_post_meta( $post_id, 'awayTeam', $_POST['away'] );
    }
}
add_action( 'save_post', 'tt_edit_match_teams' );


// Register ACF Option Page
if ( function_exists( 'acf_add_options_page' ) ) {
    acf_add_options_page( [
        'page_title' => 'Site Settings',
        'menu_title' => 'Site Settings',
        'menu_slug'  => 'site-settings',
        'capability' => 'edit_posts',
        'redirect'   => false,
    ] );
    
    acf_add_options_sub_page( [
        'page_title'  => 'Package options',
        'menu_title'  => 'Package options',
        'parent_slug' => 'site-settings',
    ] );
    acf_add_options_sub_page( [
        'page_title'  => 'Address/Social',
        'menu_title'  => 'Address/Social',
        'parent_slug' => 'site-settings',
    ] );
    acf_add_options_sub_page( [
        'page_title'  => 'Product settings',
        'menu_title'  => 'Product settings',
        'parent_slug' => 'site-settings',
    ] );
    acf_add_options_sub_page( [
        'page_title'  => 'Mega Menu Settings',
        'menu_title'  => 'Mega Menu Settings',
        'parent_slug' => 'site-settings',
    ] );
}


function tt_add_custom_field_to_variations( $loop, $variation_data, $variation ) {
    woocommerce_wp_text_input( array(
            'id'            => 'package-category[' . $loop . ']',
            'wrapper_class' => 'form-field form-row form-row-first',
            'label'         => __( 'Package Category: ', 'woocommerce' ),
            'value'         => get_post_meta( $variation->ID, 'package-category', true )
    ) );
    woocommerce_wp_text_input( array(
            'id'            => 'seat-name[' . $loop . ']',
            'wrapper_class' => 'form-field form-row  form-row-last',
            'label'         => __( 'Seat Name: ', 'woocommerce' ),
            'value'         => get_post_meta( $variation->ID, 'seat-name', true )
    ) );
}
add_action( 'woocommerce_variation_options_pricing', 'tt_add_custom_field_to_variations', 10, 3 );


function tt_save_package_category_variations( $variation_id, $i ) {
    $package_category = $_POST['package-category'][ $i ];
    if ( isset( $package_category ) ) {
        update_post_meta( $variation_id, 'package-category', esc_attr( $package_category ) );
    }
    $seat_name = $_POST['seat-name'][ $i ];
    if ( isset( $seat_name ) ) {
        update_post_meta( $variation_id, 'seat-name', esc_attr( $seat_name ) );
    }
    $seat_price = $_POST['seat-price'][ $i ];
    if ( isset( $seat_price ) ) {
        update_post_meta( $variation_id, 'event-price', esc_attr( $seat_price ) );
    }
}
add_action( 'woocommerce_save_product_variation', 'tt_save_package_category_variations', 10, 2 );


function freshlondon_admin_pages() {
    add_menu_page( 'tt general settings', 'TT Admin', 'manage_options', 'tt-admin', function () {
        tt_admin_menu();
    }, 'dashicons-superhero', 2 );
}
add_action( 'admin_menu', 'freshlondon_admin_pages' );


function events_carousel_get_acf_shortcode() { 

    $get_venue = get_field('venue_location');
    $event_date = get_field('event_date_picker');
    $formatted_date = date('M j Y', strtotime($event_date));
    $month = date('M', strtotime($event_date));
    $date = date('j', strtotime($event_date));
    $year = date('Y', strtotime($event_date));

    ?>
    <div class="ue-acf">
        <?php if ( $event_date ) : ?>
        <div class="ue-acf-item">
            <div class="svg-wrapper">
                <img src="<?php echo get_stylesheet_directory() ?>/themes/genesis-block-theme/assets/img/test.svg" >
            </div>
            <p class="event-date"><?= $month; $date; ?>, <?= $year; ?> | <?= date('H:i', strtotime($event_date)) ?> </p>
        </div>
        <?php endif; ?>

        <?php if ( $get_venue ) : ?>
        <div class="ue-acf-item">
            <div class="svg-wrapper">
                <img src="<?php echo get_stylesheet_directory() ?>/themes/genesis-block-theme/assets/img/test.svg" >
            </div>
            <p class="event-venue"><?php echo esc_html( $get_venue->post_title ); ?></p>
        </div>
        <?php endif; ?>
    </div>
    
<?php }
add_shortcode('event_carousel_get_acf', 'events_carousel_get_acf_shortcode');