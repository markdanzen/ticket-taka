jQuery(document).ready(function($) {
    var paged = 1;
    var max_pages = 1;

    function loadProducts(paged) {
        $.ajax({
            url: ajax_object.ajaxurl, // WordPress AJAX URL
            type: 'POST',
            data: {
                action: 'load_products',
                paged: paged
            },
            success: function(response) {
                $('#product-listing-container').html(response.products);
                max_pages = response.max_pages;
                renderPagination(paged, max_pages);
            },
            error: function(xhr, status, error) {
                console.log(error);
            }
        });
    }

    function renderPagination(current_page, max_pages) {
        var paginationHTML = '';
        for (var i = 1; i <= max_pages; i++) {
            var active = (i === current_page) ? 'active' : '';
            paginationHTML += '<button class="pagination-button ' + active + '" data-page="' + i + '">' + i + '</button>';
        }
        $('#pagination-container').html(paginationHTML);
        $('.pagination-button').on('click', function() {
            paged = $(this).data('page');
            loadProducts(paged);
        });
    }

    loadProducts(paged);

});
