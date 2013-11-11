<?php
/**
 * Displays single posts.
 *
 * @package WordPress
 * @subpackage Bullet
 */


get_header(); ?>

		<div id="content">

			<div id="inner-content" class="wrap">

				<main>

					<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

					<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">

						<header>

							<h1 class="single-title entry-title" itemprop="name headline"><?php the_title(); ?></h1>

							<p class="meta">
								<time datetime="<?php echo the_time( 'Y-m-d' ); ?>" itemprop="datePublished" class="date updated"><?php the_time( 'j. F Y' ); ?></time>, <?php bullet_e( 'by' ); ?> <span itemprop="author" itemscope itemtype="http://schema.org/Person" class="vcard author"><span itemprop="name" class="fn"><?php the_author_posts_link(); ?></span></span>
							</p>

						</header> <!-- end article header -->

						<div class="post-content" itemprop="articleBody">

							<?php the_post_thumbnail( 'bullet-large' ); ?>

							<?php the_content(); ?>

						</div> <!-- end post content -->

						<footer>

							<?php the_tags( '<p class="tags"><span class="tags-title">' . bullet__( 'Tags:' ).'</span> ', ', ', '</p>' ); ?>

						</footer> <!-- end article footer -->

						<?php comments_template(); ?>

					</article> <!-- end article -->

					<?php endwhile; ?>

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

			</div> <!-- #inner-content -->

		</div> <!-- end #content -->
<?php get_footer(); ?>