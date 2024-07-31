<?php 

function homepage_latest_news_section_shortcode() { ?>
    
<section class="event-news-section">

    <div class="news-listing">
        <div class="subheading">
            <h3>NEWS</h3>
        </div>

        <div class="items">
            <?php
            $args = array(
                'post_type' => 'news',
                'posts_per_page' => -1, // Display all posts
            );

            $query = new WP_Query($args);

            if ($query->have_posts()) :
                while ($query->have_posts()) : $query->the_post();
                    ?>
                    <article data-post-id="<?php echo get_the_ID(); ?>">
                        <h4><?php the_title(); ?></h4>
                        <div class="excerpt"><?php the_excerpt(); ?></div>
                        <div class="date">Posted on: <?php echo get_the_date(); ?></div>
                    </article>
                    <?php
                endwhile;
                wp_reset_postdata();
            else :
                echo 'No posts found';
            endif;
            ?>
        </div>
    </div>


    <div class="news-preview">
        <div class="news-inner-content">

        </div>
    </div>

    <div class="trustpilot">
    
    </div>

</section>

<script>
jQuery(document).ready(function($) {
    // Load the first article content on page load
    var firstArticle = $('.news-listing .items article').first();
    if (firstArticle.length) {
        var postId = firstArticle.data('post-id');
        
        $.ajax({
            type: 'POST',
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            data: {
                action: 'load_full_post_content',
                post_id: postId
            },
            success: function(response) {
                $('.news-inner-content').html(response).fadeIn(500);
            },
            error: function() {
                alert('An error occurred while loading the post content.');
            }
        });
    }

    $('.news-listing .items article').on('click', function() {
        var postId = $(this).data('post-id');
        
        $.ajax({
            type: 'POST',
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            data: {
                action: 'load_full_post_content',
                post_id: postId
            },
            success: function(response) {
                $('.news-inner-content').fadeOut(500, function() {
                    $(this).html(response).fadeIn(500);
                });
            },
            error: function() {
                alert('An error occurred while loading the post content.');
            }
        });
    });
});
</script>

<?php } 
add_shortcode('homepage_latest_news_section', 'homepage_latest_news_section_shortcode');

function load_full_post_content() {
    if (isset($_POST['post_id'])) {
        $post_id = intval($_POST['post_id']);
        $post = get_post($post_id);

        if ($post) {
            echo '<h2>' . esc_html($post->post_title) . '</h2>';
            echo '<div>' . apply_filters('the_content', $post->post_content) . '</div>';
        } else {
            echo 'Post not found';
        }
    } else {
        echo 'Invalid post ID';
    }
    wp_die(); // Always include this at the end of AJAX functions
}
add_action('wp_ajax_load_full_post_content', 'load_full_post_content');
add_action('wp_ajax_nopriv_load_full_post_content', 'load_full_post_content');

?>


