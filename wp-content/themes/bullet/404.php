<?php
/**
 * Displays the 404 page (Not Found).
 *
 * @link http://codex.wordpress.org/Creating_an_Error_404_Page
 * @package WordPress
 * @subpackage Bullet
 */
get_header(); ?>

		<div id="content">

			<div id="inner-content" class="wrap">

				<main>

					<article id="post-not-found">

					<?php $args = array(
						'post_type' => 'page',
						'meta_key' => '_wp_page_template',
						'meta_value' => 'page-notfound.php'
					); ?>
					<?php $the_query = new WP_Query( $args ); ?>
					<?php if ( $the_query->have_posts() ) : ?>

						<?php while ( $the_query->have_posts() ) : $the_query->the_post(); ?>

						<h1><?php the_title(); ?></h1>
						<div class="post-content">
							<?php the_post_thumbnail( 'bullet-large' ); ?>
							<?php the_content(); ?>
						</div>
						<?php break; ?>
						<?php endwhile; ?>

					<?php else : ?>

						<h1><?php bullet_e( '404 - Not Found' ); ?></h1>

						<div class="post-content">

							<p><?php bullet_e( 'The content you were looking for may have been moved or unpublished' ); ?></p>

						</div> <!-- end article section -->

					<?php endif; ?>

					</article> <!-- end article -->

				</main> <!-- end main -->

			</div> <!-- end #inner-content -->

		</div> <!-- end #content -->
<?php get_footer(); ?>