<?php
/**
 * Displays Woocommerce pages.
 *
 * @link http://docs.woothemes.com/documentation/plugins/woocommerce/
 * @package WordPress
 * @subpackage Bullet
 */
get_header(); ?>

		<div id="content">

			<div id="inner-content" class="wrap">

				<main>

					<?php if ( have_posts() ) : ?>
						<?php woocommerce_content(); ?>
					<?php else : ?>

					<article id="post-not-found">

						<h1><?php bullet_e( 'Not Found' ); ?></h1>

						<div class="post-content">
							<p><?php bullet_e( 'Sorry, but the requested resource was not found on this site.' ); ?></p>
						</div>

					</article>

					<?php endif; ?>

				</main> <!-- end main -->

				<?php get_sidebar(); ?>

			</div> <!-- end #inner-content -->

		</div> <!-- end #content -->
<?php get_footer(); ?>