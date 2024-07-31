<?php
/**
 * Filter products by event date range
 */

global $product;

function filter_products_by_event_date( $query ) {
    // Check if it's the main query and a product query
    if ( ! is_admin() && $query->is_main_query() && $query->is_post_type_archive( 'product' ) ) {

        // Get the start and end dates from the query parameters (or set default values)
        $start_date = isset( $_GET['event_date'] ) ? sanitize_text_field( $_GET['event_date'] ) : date( 'Y-m-d' );
        $end_date = isset( $_GET['event_date'] ) ? sanitize_text_field( $_GET['event_date'] ) : date( 'Y-m-d', strtotime( '+7 days' ) );

        // Build the meta query
        $meta_query = array(
            'relation' => 'AND',
            array(
                'key'     => 'event_date', // Replace with your custom field name for start date
                'value'   => array( $start_date, $end_date ),
                'compare' => 'BETWEEN',
                'type'    => 'DATE'
            ),
            array(
                'key'     => 'event_date', // Replace with your custom field name for end date
                'value'   => array( $start_date, $end_date ),
                'compare' => 'BETWEEN',
                'type'    => 'DATE'
            )
        );

        // Update the main query
        $query->set( 'meta_query', $meta_query );
        $query->set( 'orderby', 'meta_value' ); // Order by event start date
        $query->set( 'order', 'ASC' ); // Ascending order
        $query->set( 'meta_key', 'event_date' ); // Order by event start date
    }
}
add_action( 'pre_get_posts', 'filter_products_by_event_date' );