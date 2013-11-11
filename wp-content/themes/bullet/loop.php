<?php
/**
 * Displays the post loop.
 *
 * @link http://codex.wordpress.org/The_Loop
 * @package WordPress
 * @subpackage Bullet
 */


if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> role="article">

	<header>

		<?php echo ( is_home() ) ? '<h1 class="h2 entry-title">' : '<h2>'; ?>
			<a href="<?php the_permalink() ?>" rel="bookmark">
				<?php the_title(); ?>
			</a>
		<?php echo ( is_home() ) ? '</h1>' : '</h2>'; ?>

		<p class="meta">
			<time datetime="<?php the_time( 'Y-m-d' ); ?>" class="date updated"><?php the_time( 'j. F Y' ); ?></time>, <?php bullet_e( 'by' ); ?> <span class="vcard author"><span class="fn"><?php the_author_posts_link(); ?></span></span>
		</p>

	</header> <!-- end article header -->

	<div class="post-content">

		<?php if( has_post_thumbnail() ) : ?>
		<a href="<?php the_permalink() ?>" rel="bookmark">
			<?php the_post_thumbnail( 'bullet-small' ); ?>
		</a>
		<?php endif; ?>
		<?php echo bullet_excerpt(); ?>

	</div> <!-- end article content -->

</article> <!-- end article -->

<?php endwhile; ?>

<?php if ( function_exists( 'bullet_page_navi' ) ) : // if better page navigation is active ?>
	<?php bullet_page_navi(); // use the page navi function ?>
<?php else : // display regular wp prev & next links ?>
<nav class="wp-prev-next">
	<ul>
		<li class="prev-link"><?php next_posts_link( bullet__( '&laquo; Older Entries' ) ); ?></li>
		<li class="next-link"><?php previous_posts_link( bullet__( 'Newer Entries &raquo;' ) ); ?></li>
	</ul>
</nav>
<?php endif; ?>

<?php else : ?>

<article id="post-not-found">

	<h1><?php bullet_e( 'Nothing Found' ); ?></h1>

	<div class="post-content">
		<p><?php bullet_e( 'Sorry. There are no posts to show.' ); ?></p>
	</div>

</article>

<?php endif; ?>