<?php
/**
 * Widgets
 *
 * @package WordPress
 * @subpackage Bullet
 *
 * @link http://codex.wordpress.org/Widgets_API
 */


/* Register sidebars * * * * * * * * * * * * * * * * * * */

/**
 * Registers sidebars & widget areas
 */
function bullet_sidebars() {
	register_sidebar( array(
		'id' => 'sidebar1',
		'name' => 'Sidebar 1',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h2 class="widgettitle">',
		'after_title' => '</h2>'
	) );
	register_sidebar( array(
		'id' => 'footer1',
		'name' => 'Footer',
		'before_widget' => '<div id="%1$s" class="footer-widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h2 class="widgettitle">',
		'after_title' => '</h2>'
	) );
}
add_action( 'widgets_init', 'bullet_sidebars' );


/* Widgets * * * * * * * * * * * * * * * * * * */

/**
 * Registers the widgets and unregisters the unused default ones
 */
function bullet_widgets() {
	/* Register Widgets * * * * * * * * * * * * * * * * * * */


	/* Unregister Widgets * * * * * * * * * * * * * * * * * * */

	// Default Widgets
	//unregister_widget( 'WP_Widget_Text' );
	//unregister_widget( 'WP_Widget_Pages' );
	//unregister_widget( 'WP_Widget_Calendar' );
	//unregister_widget( 'WP_Widget_Archives' );
	//unregister_widget( 'WP_Widget_Links' );
	//unregister_widget( 'WP_Widget_Categories' );
	//unregister_widget( 'WP_Widget_Recent_Posts' );
	//unregister_widget( 'WP_Widget_Search' );
	//unregister_widget( 'WP_Widget_Tag_Cloud' );
	//unregister_widget( 'WP_Widget_Meta' );
	//unregister_widget( 'WP_Widget_Recent_Comments' );
	//unregister_widget( 'WP_Widget_RSS' );
	//unregister_widget( 'WP_Nav_Menu_Widget' );

	// Plugins Widgets
	//unregister_widget( 'widget_akismet' );
}
add_action( 'widgets_init', 'bullet_widgets' );

