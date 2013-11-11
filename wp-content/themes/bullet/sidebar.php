<?php
/**
 * Displays the sidebar, typicallly containing the primary widget area.
 *
 * @link http://codex.wordpress.org/Sidebars
 * @package WordPress
 * @subpackage Bullet
 */
?>
				<aside id="sidebar1" class="sidebar" role="complementary">

					<h1 class="visuallyHidden"><?php bullet_e( 'Other Content' ); ?></h1>

					<?php if ( is_active_sidebar( 'sidebar1' ) )
						dynamic_sidebar( 'sidebar1' ); ?>

				</aside>