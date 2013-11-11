<?php
/**
 * Displays the search results page.
 *
 * @link http://codex.wordpress.org/Template_Hierarchy#Search_Result_display
 * @package WordPress
 * @subpackage Bullet
 */
get_header(); ?>

		<div id="content">

			<div id="inner-content" class="wrap">

				<main>

					<h1 class="archive_title">
						<span><?php bullet_e( 'Search Results for:' ); ?></span> <?php echo esc_attr(get_search_query()); ?>
					</h1>

					<?php get_template_part( 'loop', 'search' ); ?>

				</main> <!-- end main -->

				<div id="sidebar1" class="sidebar">
					<?php get_search_form(); ?>
				</div>

			</div> <!-- end #inner-content -->

		</div> <!-- end #content -->

<?php get_footer(); ?>