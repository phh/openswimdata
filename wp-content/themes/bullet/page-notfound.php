<?php
/**
 * Template Name: 404 Not Found
 *
 * @link http://codex.wordpress.org/Page_Templates
 * @package WordPress
 * @subpackage Bullet
 */
get_header(); ?>

		<div id="content">

			<div id="inner-content" class="wrap">

				<main>

					<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

					<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">

						<h1 class="page-title" itemprop="headline"><?php the_title(); ?></h1>

						<div class="post-content" itemprop="articleBody">

							<?php the_post_thumbnail( 'bullet-large' ); ?>

							<?php the_content(); ?>

						</div> <!-- end article section -->

					</article> <!-- end article -->

					<?php endwhile; ?>
					<?php endif; ?>

				</main> <!-- end main -->

				<?php get_sidebar(); ?>

			</div> <!-- end #inner-content -->

		</div> <!-- end #content -->
<?php get_footer(); ?>