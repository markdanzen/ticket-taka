<?php

function event_owl_carousel_shortcode() { ?>
    <div class="event-carousel-wrapper">
        <?php
        $args = array(
            'post_type' => 'post', // Post type
            'posts_per_page' => 5, // Number of posts to retrieve
            'order' => 'DESC', // Order of posts
            'orderby' => 'date' // Order by date
        );

        $query = new WP_Query($args);
        // Start the Loop
        if ($query->have_posts()) :
            while ($query->have_posts()) : $query->the_post();
        ?>
            <div class="event-carousel-item">

                <?php $url = wp_get_attachment_url( get_post_thumbnail_id($product->ID), 'thumbnail' ); ?>
                <div id="event-feat-img" style="background-image: url('<?php echo $url ?>')">

                </div>

                <?php the_title(); ?>
            </div>
        <?php 
            endwhile;
        endif;
        ?>
    </div>


<?php }
add_shortcode ('event_owl_carousel', 'event_owl_carousel_shortcode');

?>