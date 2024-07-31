    
jQuery(document).ready(function($) {    
    
    // Filter function
    $('.filter-btn').click(function() {
        var $this = $(this);
        var filter = $this.data('filter');
        
        if ($this.hasClass('active')) {
            // If the clicked button is already active, reset the filter
            $('.filter-btn').removeClass('active');
            $('.filter-item').fadeIn(300);
        } else {
            // If it's not active, apply the filter
            $('.filter-btn').removeClass('active');
            $this.addClass('active');
            
            $('.filter-item').fadeOut(100, function() {
                $('.filter-item').addClass('hidden');
                $('.filter-item[data-category="' + filter + '"]').removeClass('hidden').fadeIn(300);
            });
        }
    });

    // Sort function
    $('.sort-btn').click(function() {
        var sortOrder = $(this).data('sort');
        var $content = $('#content');
        var $items = $content.children('.item').get();

        $items.sort(function(a, b) {
            var aVal = parseInt($(a).data('value'));
            var bVal = parseInt($(b).data('value'));
            
            if (sortOrder === 'asc') {
                return aVal - bVal;
            } else {
                return bVal - aVal;
            }
        });


        $content.fadeOut(300, function() {
            $.each($items, function(index, item) {
                $content.append(item);
            });
            $content.fadeIn(300);
        });

    });

    // Detect changes in quantity input fields
    $('.woocommerce').on('change', 'input.qty', function(){
        // Delay to allow WooCommerce to update cart
        setTimeout(function() {
            $('[name="update_cart"]').trigger('click');
        }, 500);
    });

    // Update subtotal after AJAX request completes
    $(document.body).on('updated_cart_totals', function(){
        updateSubtotal();
    });

    function updateSubtotal() {
        var subtotal = 0;
        $('.cart_item').each(function() {
            var price = parseFloat($(this).find('.product-price .amount').text().replace(/[^0-9.-]+/g,""));
            var quantity = parseInt($(this).find('.product-quantity input.qty').val());
            subtotal += price * quantity;
        });
        $('.cart-subtotal .amount').text(formatCurrency(subtotal));
    }

    function formatCurrency(number) {
        return number.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
    }

});