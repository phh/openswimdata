<?php
/**
 * Displays author archives.
 *
 * @link http://codex.wordpress.org/Author_Templates
 * @package WordPress
 * @subpackage Bullet
 */
get_header(); ?>

		<div id="content">

			<div id="inner-content" class="wrap">

				<main>

					<h1 class="archive_title h2">
						<span><?php bullet_e( 'Posts By:' ); ?></span>
						<?php // google+ rel=me function
						$curauth = ( get_query_var( 'author_name' ) ) ? get_user_by( 'slug', get_query_var( 'author_name' ) ) : get_userdata( get_query_var( 'author' ) );
						$google_profile = get_the_author_meta( 'google_profile', $curauth->ID );

						if ( $google_profile )
							echo '<a href="' . esc_url( $google_profile ) . '" rel="me"></a>' . $curauth->display_name;
						else
							echo get_the_author_meta( 'display_name', $curauth->ID );
						?>
					</h1>

					<?php get_template_part( 'loop', 'author' ); ?>

				</main> <!-- end #main -->

				<?php get_sidebar(); ?>

			</div> <!-- end #inner-content -->

		</div> <!-- end #content -->
<?php get_footer(); ?>