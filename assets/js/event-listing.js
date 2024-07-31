jQuery(document).ready(function($) {
    var $ajaxProductsContainer = $('.ajax-products-container');
    if ($ajaxProductsContainer.length) {
        var $productsContainer = $ajaxProductsContainer.find('.products');
        var $paginationContainer = $ajaxProductsContainer.find('.pagination');
        var paged = 1;
        var max_pages = 1;
        var categoryId = $(this).val();

        function load_products() {
            $('#product-category-dropdown').change(function() {
                var category_id = $(this).val();
                $.ajax({
                    url: ajax_object.ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'get_products',
                        category_id: category_id,
                    },
                    success: function(response) {
                        $productsContainer.html(response.data.products);
                    }
                });
            });

            $.ajax({
                url: ajax_object.ajaxurl,
                type: 'POST',
                data: {
                    action: 'get_products',
                    paged: paged,
                    category_id: categoryId
                },
                before: function() {
                    $('#loading-gif').show();
                },
                success: function(response) {
                    $('#loading-gif').hide();
                    $('.products').show();

                    
                    if(response.success) {
                        $productsContainer.html(response.data.products);

                        max_pages = response.data.max_pages;
                        // Append pagination links
                        var pagination_html = '';
                        if (paged > 1) {
                            pagination_html += '<a href="#" class="page-link" data-page="' + (paged - 1) + '">Prev</a> ';
                        }
                        for (var i = 1; i <= max_pages; i++) {
                            pagination_html += '<a href="#" class="page-link ' + (i === paged ? 'active' : '') + '" data-page="' + i + '">' + i + '</a> ';
                        }
                        if (paged < max_pages) {
                            pagination_html += '<a href="#" class="page-link" data-page="' + (paged + 1) + '">Next</a> ';
                        }
                        $paginationContainer.html(pagination_html);
                    } else {
                        $('$#products-container').html('<p>No products found.</p>');
                    }

                    // if (response.success) {
                    //     $productsContainer.html(response.data.products);
                    //     max_pages = response.data.max_pages;
                    //     // Append pagination links
                    //     var pagination_html = '';
                    //     if (paged > 1) {
                    //         pagination_html += '<a href="#" class="page-link" data-page="' + (paged - 1) + '">Prev</a> ';
                    //     }
                    //     for (var i = 1; i <= max_pages; i++) {
                    //         pagination_html += '<a href="#" class="page-link ' + (i === paged ? 'active' : '') + '" data-page="' + i + '">' + i + '</a> ';
                    //     }
                    //     if (paged < max_pages) {
                    //         pagination_html += '<a href="#" class="page-link" data-page="' + (paged + 1) + '">Next</a> ';
                    //     }
                    //     $paginationContainer.html(pagination_html);
                    // }

                }
            });
        }


        load_products();


        $(document).on('click', '.page-link', function(e) {
            e.preventDefault();
            paged = $(this).data('page');
            load_products();
        });
    }

    $('#search-filter-form-5284 > .sf-input-select').change(function() {
        $('#search-filter-form-5151 > .sf-input-select').prop('selectedIndex', 0); // Reset secondary dropdown
    });

    $('#search-filter-form-5151 > .sf-input-select').change(function() {
        $('#search-filter-form-5284 > .sf-input-select').prop('selectedIndex', 0); // Reset primary dropdown
    });

});

