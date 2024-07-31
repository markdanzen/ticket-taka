<?php
function tt_competitions_menu_content_shortcode() { 

    ?>
    <div class="competition-panel mega-menu-content">

        <?php if( have_rows('menu_container', 'option') ): ?>
        <div class="grid-menu">
            <?php while( have_rows('menu_container', 'option') ): the_row(); ?>
                <div class="grid-item">
                    <?php if( have_rows('menu_item', 'option') ): ?>
                        <?php while( have_rows('menu_item', 'option') ): the_row(); ?>

                            <h2><?php the_sub_field('menu_header', 'option'); ?></h2>
                            <?php
                            // Replace 123 with the ID of your specific product category

                            $get_cat_id = get_sub_field('category_id');
                            $parent_category_id = $get_cat_id;

                            $args = array(
                                'taxonomy'   => 'product_cat',
                                'child_of'   => $parent_category_id,
                                'hide_empty' => false,
                            );

                            $child_categories = get_terms($args);

                            if (!empty($child_categories) && !is_wp_error($child_categories)) {
                                echo "<ul>";
                                foreach ($child_categories as $child_category) {
                                    // Get the category image
                                    $thumbnail_id = get_term_meta($child_category->term_id, 'thumbnail_id', true);
                                    $image = wp_get_attachment_url($thumbnail_id);
                                    
                                    // Get the category permalink
                                    $category_link = get_term_link($child_category->term_id, 'product_cat');
                                    
                                    echo "<li>";
                                    echo "<a href='" . esc_url($category_link) . "'>";
                                    if ($image) {
                                        echo "<img src='" . esc_url($image) . "' alt='" . esc_attr($child_category->name) . "'>";
                                    }
                                    echo esc_html($child_category->name);
                                    echo "</a>";
                                    echo "</li>";
                                }
                                echo "</ul>";
                            } else {
                                echo "No child categories found.";
                            }
                            ?>
                        
                        <?php endwhile; ?>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
            </div>
        <?php endif; ?>
    </div>
<?php }
add_shortcode('tt_competitions_menu_content', 'tt_competitions_menu_content_shortcode'); 
?>


<?php
function tt_teams_menu_content_shortcode() { ?>

    <div class="all-teams-panel mega-menu-content">

        <?php if( have_rows('teams_menu', 'option') ): ?>
            <div class="grid-menu">
                <?php while( have_rows('teams_menu', 'option') ) : the_row(); 
                    $get_featured_team = get_sub_field('team', 'option');
                    
                    // Get the team_flag group field
                    $team_flag = get_field('team_flag', $get_featured_team->ID);
                    
                    // Get the team_image from within the team_flag group
                    $team_image = $team_flag['team_image'];
                ?>
                    <div class="grid-item">

                        <?php if ($team_image) : ?>
                            <img src="<?php echo esc_url($team_image); ?>" alt="<?php echo esc_attr($get_featured_team->post_title); ?>">
                        <?php endif; ?>

                        <?php echo esc_html($get_featured_team->post_title); ?>

                    </div>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>

    </div>

<?php }
add_shortcode('tt_teams_menu_content', 'tt_teams_menu_content_shortcode'); 
?>