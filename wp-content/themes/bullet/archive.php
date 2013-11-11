<?php
/**
 * Displays archives.
 *
 * @link http://codex.wordpress.org/Creating_an_Archive_Index
 * @package WordPress
 * @subpackage Bullet
 */
get_header(); ?>

		<div id="content">

			<div id="inner-content" class="wrap">

				<main itemscope="itemscope" itemtype="http://schema.org/Blog">

					<?php if ( is_category() ) : ?>
					<h1 class="archive_title">
						<span><?php bullet_e( 'Category:' ); ?></span> <?php single_cat_title(); ?>
					</h1>

					<?php elseif ( is_tax() ) : ?>
					<h1 class="archive_title">
						<span><?php bullet_e( 'Taxonomy:' ); ?></span> <?php single_term_title(); ?>
					</h1>

					<?php elseif ( is_tag() ) : ?>
					<h1 class="archive_title">
						<span><?php bullet_e( 'Tag:' ); ?></span> <?php single_tag_title(); ?>
					</h1>

					<?php elseif ( is_post_type_archive() ) : ?>
					<h1 class="archive_title">
						<span><?php bullet_e( 'Archive:' ); ?></span> <?php post_type_archive_title(); ?>
					</h1>

					<?php elseif ( is_day() ) : ?>
					<h1 class="archive_title">
						<span><?php bullet_e( 'Archive:' ); ?></span> <?php the_time( 'l, F j, Y' ); ?>
					</h1>

					<?php elseif ( is_month() ) : ?>
					<h1 class="archive_title">
						<span><?php bullet_e( 'Archive:' ); ?></span> <?php the_time( 'F Y' ); ?>
					</h1>

					<?php elseif ( is_year() ) : ?>
					<h1 class="archive_title">
						<span><?php bullet_e( 'Archive:' ); ?></span> <?php the_time( 'Y' ); ?>
					</h1>

					<?php endif; ?>

					<?php get_template_part( 'loop', 'archive' ); ?>

				</main> <!-- end main -->

				<?php get_sidebar(); ?>

			</div> <!-- end #inner-content -->

		</div> <!-- end #content -->
<?php get_footer(); ?>