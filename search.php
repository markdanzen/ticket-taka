<?php
/**
 * The template for displaying Search results.
 *
 * @package Genesis Block Theme
 */

get_header(); ?>

<div class="tt-content-search content-search-wrapper"  style="background-image: url('<?php echo esc_url( get_template_directory_uri() . '/assets/img/Ticket-Taka-Background-Image.webp' ); ?>');">

	<div class="tt-container">
		
		<div class="tt-outer-container">
			<div class="tt-inner-container">

				<div class="tt-result-area">

					<div class="tt-dropdown-select">
						<?php echo do_shortcode('[searchandfilter id="5284"]'); ?>
					</div>
					
					<?php
					if ( have_posts() ) :
						while ( have_posts() ) :
							the_post();

							get_template_part( 'template-parts/content-search' );
						endwhile;

						else :
							?>
							<p><?php esc_html_e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'genesis-block-theme' ); ?></p>
					<?php endif; ?>

					<?php the_posts_pagination(); ?>
				</div>
			
				<div class="tt-filter-area">
					<h2>FIND YOUR TICKETS</h2>
					<?php echo do_shortcode('[searchandfilter id="5151"]'); ?>
				</div>

			</div>
		</div>

	</div><!-- #main -->

</div><!-- #primary -->

<?php get_footer(); ?>
