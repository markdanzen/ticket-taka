jQuery(document).ready(function($) {    
    $('.woocommerce').on('change', 'select.qty', function() {
        var cart_item_key = $(this).attr('name').replace(/cart\[(.*)\]\[qty\]/, '$1');
        var quantity = $(this).val();

        $.ajax({
            type: 'POST',
            url: cart_auto_update_params.ajax_url,
            data: {
                action: 'cart_auto_update_quantity',
                cart_item_key: cart_item_key,
                quantity: quantity,
                security: cart_auto_update_params.update_cart_nonce
            },
            success: function(response) {
                $(document.body).trigger('updated_wc_div');
            }
        });
    });
});