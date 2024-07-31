<?php
defined( 'ABSPATH' ) || exit;

if ( $max_value && $min_value === $max_value ) {
    ?>
    <div class="quantity hidden">
        <input type="hidden" id="<?php echo esc_attr( $input_id ); ?>" class="qty" name="<?php echo esc_attr( $input_name ); ?>" value="<?php echo esc_attr( $min_value ); ?>" />
    </div>
    <?php
} else {
    /* translators: %s: Quantity. */
    $labelledby = ! empty( $args['product_name'] ) ? sprintf( esc_html__( '%s quantity', 'woocommerce' ), wp_strip_all_tags( $args['product_name'] ) ) : '';
    ?>
    <div class="quantity">
        <select
            id="<?php echo esc_attr( $input_id ); ?>"
            class="<?php echo esc_attr( join( ' ', (array) $classes ) ); ?>"
            name="<?php echo esc_attr( $input_name ); ?>"
            title="<?php echo esc_attr_x( 'Qty', 'Product quantity input tooltip', 'woocommerce' ); ?>"
            <?php if ( ! empty( $labelledby ) ) { ?>
                aria-labelledby="<?php echo esc_attr( $labelledby ); ?>"
            <?php } ?>
        >
            <?php for ( $count = $min_value; $count <= $max_value; $count++ ) : ?>
                <option value="<?php echo esc_attr( $count ); ?>" <?php selected( $input_value, $count ); ?>>
                <?php echo esc_html( $count ) . ' ' . ($count > 1 ? 'Tickets' : 'Ticket'); ?>
                </option>
            <?php endfor; ?>
        </select>
    </div>
    <?php
}