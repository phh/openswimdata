<?php
/**
 * Displays image attachments.
 *
 * @link http://codex.wordpress.org/Using_Image_and_File_Attachments#Usage_in_Themes
 * @package WordPress
 * @subpackage Bullet
 */

// Usually we don't use this so redirect to the relevant post
wp_redirect(get_permalink($post->post_parent));

// otherwise remove the above line and do this...
get_header(); ?>

			<div id="content">

				<div id="inner-content" class="wrap">

					<main>

						<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

						<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

							<h1>
								<a href="<?php echo get_permalink( $post->post_parent ); ?>" rev="attachment"><?php echo get_the_title( $post->post_parent ); ?></a> &raquo; <?php the_title(); ?>
							</h1>

							<div class="post-content">
								<p class="attachment">
									<a href="<?php echo wp_get_attachment_url( $post->ID ); ?>"><?php echo wp_get_attachment_image( $post->ID, 'medium' ); ?></a>
								</p>
								<p class="caption">
									<?php if ( !empty($post->post_excerpt) ) the_excerpt(); // this is the "caption" ?>
								</p>

								<?php the_content( bullet__( 'Read more &raquo;' ) ); ?>
							</div>

							<footer>
								<nav class="prev-next-links">
									<ul>
										<li><?php previous_image_link() ?></li>
										<li><?php next_image_link() ?></li>
									</ul>
								</nav>
							</footer>

						</article>

						<?php endwhile;
						else : ?>

						<div class="help">
							<p><?php bullet_e( 'Sorry, no attachments matched your criteria.' ); ?></p>
						</div>

						<?php endif; ?>

					</main> <!-- end main -->

					<?php get_sidebar(); ?>

				</div> <!-- end #inner-content -->

			</div> <!-- end #content -->
<?php get_footer(); ?>