<?php
/**
 * The template used for displaying standard post content
 *
 * @package Genesis Block Theme
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> aria-label="<?php the_title_attribute(); ?>">
	<div class="post-content">


		<div class="entry-content">

			<?php
			if ( ! is_single() && has_excerpt() ) {
				the_excerpt();
			} else {
				// Get the content.
				the_content( esc_html__( 'Read More', 'genesis-block-theme' ) . ' <span class="screen-reader-text">' . __( 'about ', 'genesis-block-theme' ) . get_the_title() . '</span>' );
			}

			// Post pagination links.
			wp_link_pages(
				array(
					'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'genesis-block-theme' ),
					'after'  => '</div>',
				)
			);

			if ( is_single() ) {
				// Post meta sidebar.
				get_template_part( 'template-parts/content-meta' );
			}
			?>
		</div><!-- .entry-content -->
	</div><!-- .post-content-->

</article><!-- #post-## -->
