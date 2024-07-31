<?php
/**
 * The template used for displaying search results
 *
 * @package Genesis Block Theme
 */

 global $product, $post;

if ( is_archive() || is_page() ) {
	$team_name = '';
} else {
	$team_name = $post->post_title;
}

$product = wc_get_product(get_the_ID());
$product_url = get_permalink();

// Get the ACF field data
$home_team_id = get_field('home_team');
$away_team_id = get_field('away_team');
$home_team_title = $home_team_id ? get_the_title($home_team_id) : '';
$away_team_title = $away_team_id ? get_the_title($away_team_id) : '';

$event_id = $post->ID;
$home_team = get_post_meta($event_id, 'team', true);
$away_team = get_post_meta($event_id, 'awayTeam', true);

// Working Fields
$get_venue = get_field('venue_location');

$event_date = get_field('event_date_picker');
$formatted_date = date('M j Y', strtotime($event_date));
$month = date('M', strtotime($event_date));
$date = date('j', strtotime($event_date));
$year = date('Y', strtotime($event_date));

?>

<div class="event-container">

	<div class="event-date">
		<div class="date-format">
			<span class="month"><?= $month; ?></span>
			<span class="date"><?= $date; ?></span>
			<span class="year"><?= $year; ?></span>
		</div>
	</div>

    <div class="event-details">

        <div class="event-details-group">

            <div class="event-team">
                <div class="pb-top">
                    <div class="pb-home pb-side" onclick="location.href='<?= get_the_permalink($home_team_id); ?>';">
                        <div class="pb-team-icon">
                            <?php
                            $team_flag_home = get_field('team_flag', $home_team_id); ?>
                                                        
                            <?php if ($team_flag_home) {
                                if ($team_flag_home['use_colors_or_image']) {
                                    ?>
                                    <img src="<?= esc_url($team_flag_home['team_image']); ?>" alt="<?= esc_attr(get_the_title($home_team_id)); ?>">
                                <?php } else { ?>
                                    <div class="pb-team-icon-wrap">
                                        <div class="pb-team-icon-left" style="background-color: <?= esc_attr($team_flag_home['color_1']); ?>"></div>
                                        <div class="pb-team-icon-right" style="background-color: <?= esc_attr($team_flag_home['color_2']); ?>"></div>
                                    </div>
                                <?php }
                                // Display color values
                                echo '<p>Color 1: ' . esc_html($team_flag_home['color_1']) . '</p>';
                                echo '<p>Color 2: ' . esc_html($team_flag_home['color_2']) . '</p>';
                            } else {
                                // Debug: Field not found
                                echo 'Team flag for home team not found';
                            }
                            ?>
                        </div>
                    </div>
                    <div class="versus">VS</div>
                    <div class="pb-away pb-side" onclick="location.href='<?= get_the_permalink($away_team_id); ?>';">
                        <div class="pb-team-icon">
                            <?php
                            $team_flag_away = get_field('team_flag', $away_team_id);
                            if ($team_flag_away) {
                                if ($team_flag_away['use_colors_or_image']) {
                                    ?>
                                    <img src="<?= esc_url($team_flag_away['team_image']); ?>" alt="<?= esc_attr(get_the_title($away_team_id)); ?>">
                                <?php } else { ?>
                                    <div class="pb-team-icon-wrap">
                                        <div class="pb-team-icon-left" style="background-color: <?= esc_attr($team_flag_away['color_1']); ?>"></div>
                                        <div class="pb-team-icon-right" style="background-color: <?= esc_attr($team_flag_away['color_2']); ?>"></div>
                                    </div>
                                <?php }
                                // Display color values
                                echo '<p>Color 1: ' . esc_html($team_flag_away['color_1']) . '</p>';
                                echo '<p>Color 2: ' . esc_html($team_flag_away['color_2']) . '</p>';
                            } else {
                                // Debug: Field not found
                                echo 'Team flag for away team not found';
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="event-information">

                <div class="search-result-category">
                    <?php
                    $terms = get_the_terms(get_the_ID(), 'product_cat');
                    if ($terms && !is_wp_error($terms)) {
                        // Get the term with the highest depth (last child category)
                        $last_child_category = null;
                        foreach ($terms as $term) {
                            if (!$last_child_category || term_is_ancestor_of($last_child_category, $term, 'product_cat')) {
                                $last_child_category = $term;
                            }
                        }
                        echo esc_html($last_child_category->name);
                    }
                    ?>
                </div>

                <a href="<?= the_permalink(); ?>">
                    <h3><?php the_title(); ?></h3>
                </a>

				<div class="event-info">
					<p class="event-time"><strong><?= date('H:i', strtotime($event_date)) ?> </strong></p>
					<?php if ( $get_venue ) : ?>
						<p class="event-location"><?php echo esc_html( $get_venue->post_title ); ?></p>
					<?php endif; ?>
				</div>

            </div>

        </div>

        <div class="product-cta">
            <a class="button" href="<?= the_permalink(); ?>">Buy Tickets</a>
        </div>

    </div>

</div>
