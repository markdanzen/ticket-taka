<?php
/**
 * Search & Filter Pro 
 *
 * Sample Results Template
 * 
 * @package   Search_Filter
 * @author    Ross Morsali
 * @link      https://searchandfilter.com
 * @copyright 2018 Search & Filter
 * 
 * Note: these templates are not full page templates, rather 
 * just an encaspulation of the your results loop which should
 * be inserted in to other pages by using a shortcode - think 
 * of it as a template part
 * 
 * This template is an absolute base example showing you what
 * you can do, for more customisation see the WordPress docs 
 * and using template tags - 
 * 
 * http://codex.wordpress.org/Template_Tags
 *
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $products, $post;


if ( $query->have_posts() ) {
	while ($query->have_posts()) {
		$query->the_post();
		
			// Get the ACF field data
			$fields = get_field('team_flag', $query->get_id()); // Replace 'team_flag' with your field group name

			// Access the fields inside the 'team_flag' field group
			$home_team_id = isset($fields['home_team']) ? $fields['home_team'] : '';
			$away_team_id = isset($fields['away_team']) ? $fields['away_team'] : '';

			// Access other fields inside the 'team_flag' field group
			$use_colors_or_image = isset($fields['use_colors_or_image']) ? $fields['use_colors_or_image'] : '';
			$color_1 = isset($fields['color_1']) ? $fields['color_1'] : '';
			$color_2 = isset($fields['color_2']) ? $fields['color_2'] : '';
            $home_team_title = get_the_title($home_team_id);
            $away_team_title = get_the_title($away_team_id);

            $event_id = $post->ID;
            $home_team = get_post_meta( $event_id, 'team', true );
            $away_team = get_post_meta( $event_id, 'awayTeam', true );

			$event_date = get_field('event_date_picker');
			$formatted_date = date('M j Y', strtotime($event_date));
			$month = date('M', strtotime($event_date));
			$date = date('j', strtotime($event_date));
			$year = date('Y', strtotime($event_date));


			$get_venue = get_field('venue_location');

            // Get product categories
			$product_categories = get_the_terms(get_the_ID(), 'product_cat');
			$category_names = array();
			if (!empty($product_categories)) {
				$category_names = array_reverse(array_column($product_categories, 'name'));
			}

            // Get product image
            $product_image = wp_get_attachment_image_src(get_post_thumbnail_id($query->get_id()), 'full');

		?>

		<?php if ('product' === get_post_type() && 'publish' === get_post_status() ) { ?>


		<div class="event-container">

			<div class="bookmark-divider">

			</div>

			<div class="event-date">
				<div class="date-format">
					<span class="month"><?= $month; ?></span>
					<span class="date"><?= $date; ?></span>
					<span class="year"><?= $year; ?></span>
					<div class="divider"></div> <!-- Divider -->
					<span class="event-time"><?= date('H:i', strtotime($event_date)) ?></span>
				</div>
			</div>

			<div class="event-details">

				<div class="event-details-group">

					<div class="event-team">
						<div class="pb-top">
							<div class="pb-home pb-side" onclick="location.href='<?= get_the_permalink( $home_team ); ?>';">
								<div class="pb-team-icon">
									<?php $team_flag_home = get_field( 'team_flag', $home_team ); ?>
									<?php 
										if ( $team_flag_home ) {
											if ( $team_flag_home['use_colors_or_image'] ) { 
												$team_image = isset($team_flag_home['team_image']) ? $team_flag_home['team_image'] : ''; 
												if ($team_image) {
													if (is_array($team_image)) {
														// If it's an array, it's likely an image object
														$image_url = $team_image['url'];
													} else {
														// If it's not an array, it might be just the URL
														$image_url = $team_image;
													}
												?>
												<img class="team-logo" src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr(get_the_title($home_team)); ?>">

												<?php
												} else {
													echo "No team image available";
												}
												?>
											<?php } else { ?>
												<div class="pb-team-icon-wrap">
													<div class="pb-team-icon-left" style="background-color: <?= $team_flag_home['color_1']; ?>"></div>
													<div class="pb-team-icon-right" style="background-color: <?= $team_flag_home['color_2']; ?>"></div>
												</div>
											<?php }
										} 
									?>
								</div>
							</div>
							<div class="versus">VS</div>
							<div class="pb-away pb-side" onclick="location.href='<?= get_the_permalink( $away_team ); ?>';">
								<div class="pb-team-icon">
									<?php $team_flag_away = get_field( 'team_flag', $away_team ); ?>
										<?php if ( $team_flag_away ) {
											if ( $team_flag_away['use_colors_or_image'] ) { 
												$team_image = isset($team_flag_away['team_image']) ? $team_flag_away['team_image'] : ''; 
												if ($team_image) {
													if (is_array($team_image)) {
														// If it's an array, it's likely an image object
														$image_url = $team_image['url'];
													} else {
														// If it's not an array, it might be just the URL
														$image_url = $team_image;
													}
												?>
												<img src="<?= $team_image; ?>" alt="<?= get_the_title( $away_team ); ?>">

												<?php
												} else {
													echo "No team image available";
												}
												?>
											<?php } else { ?>
												<div class="pb-team-icon-wrap">
													<div class="pb-team-icon-left" style="background-color: <?= $team_flag_away['color_1']; ?>"></div>
													<div class="pb-team-icon-right" style="background-color: <?= $team_flag_away['color_2']; ?>"></div>
												</div>
											<?php }
										} ?>
								</div>
							</div>
						</div>
					</div>


					<div class="event-information">
						<?php if (!empty($category_names)) : ?>
							<p class="product-categories"><?php echo $category_names[count($category_names) - 1]; ?></p>
						<?php endif; ?>

						<a href="<?= the_permalink(); ?>">
							<h3><?php the_title(); ?></h3>
						</a>
						
						<div class="event-info">
							<?php if ( $get_venue ) : ?>
								<p class="event-location"><?php echo esc_html( $get_venue->post_title ); ?></p>
							<?php endif; ?>
						</div>

					</div>

				</div>

				<div class="product-cta">
					<a class="button" href="<?= the_permalink($query->get_id()); ?>">Buy Tickets</a>
				</div>


			</div>


			
		</div>
		
		<?php } ?>

		<?php
	}
	?>
	Page <?php echo $query->query['paged']; ?> of <?php echo $query->max_num_pages; ?><br />
	
	<div class="pagination">
		
		<?php
        $big = 999999999; // need an unlikely integer
        echo paginate_links( array(
            'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
            'format' => '?paged=%#%',
            'current' => max( 1, get_query_var('paged') ),
            'total' => $query->max_num_pages,
            'prev_text' => '&laquo; Previous',
            'next_text' => 'Next &raquo;',
        ) );
        ?>

		<?php
			/* example code for using the wp_pagenavi plugin */
			if (function_exists('wp_pagenavi'))
			{
				echo "<br />";
				wp_pagenavi( array( 'query' => $query ) );
			}
		?>
	</div>
	<?php
}
else
{
	echo "No Results Found";
}
?>