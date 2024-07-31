<?php
/**
 * The Template for displaying all single posts.
 *
 * @package Genesis Block Theme
 */

get_header();
?>

	<div class="content-area single-product-main" style="background-image: url('<?php echo esc_url( get_template_directory_uri() . '/assets/img/Ticket-Taka-Background-Image.webp' ); ?>');"> <!--<div id="primary" class="content-area"> -->
		
		<main id="main" class="site-main">
			<div class="tt-container">
				<?php
					while ( have_posts() ) :
						the_post();

						// Post content template.
						get_template_part( 'template-parts/content' );
					endwhile;
				?>
			</div>
		</main><!-- #main -->
	</div><!-- #primary -->

<?php get_footer(); ?>
