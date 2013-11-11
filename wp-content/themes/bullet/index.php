<?php
/**
 * The main template file. Acts as a fallback if more specific templates don't exist.
 *
 * @link http://codex.wordpress.org/Template_Hierarchy
 * @package WordPress
 * @subpackage Bullet
 */


get_header(); ?>

		<div id="content">

			<div id="inner-content" class="wrap">

				<main>

					<?php get_template_part( 'loop', 'index' ); ?>

				</main> <!-- end main -->

				<?php get_sidebar(); ?>

			</div> <!-- end #inner-content -->

		</div> <!-- end #content -->

<?php get_footer(); ?>