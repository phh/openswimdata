<?php
/**
 * Everything that modifies the administration screens
 *
 * @package WordPress
 * @subpackage Bullet
 */

/* Theme Options * * * * * * * * * * * * * * * * * * */

// Loads the theme options include
//get_template_part( 'includes/theme-options' ); // Theme options


/* Meta Boxes * * * * * * * * * * * * * * * * * * */

// Loads meta box class
//get_template_part( 'includes/classes/class.generate-metabox' );
// See dropbox for config examples


/* Editor Buttons * * * * * * * * * * * * * * * * * * */

// Load the shortcode button include
//get_template_part( 'includes/classes/class.generate-shortcode-buttons' );
// See dropbox for config examples


/* Backend customisations * * * * * * * * * * * * * * * * * * */

/**
 * Adds custom js and css for admin panel
 */
function bullet_queue_admin_js() {
	wp_enqueue_script( 'admin-js', get_template_directory_uri() . '/js/admin_scripts.js' );
	wp_enqueue_style( 'admin-css', get_template_directory_uri() . '/css/admin_styles.css' );
}
add_action( 'admin_enqueue_scripts', 'bullet_queue_admin_js', 1 );

/**
 * Removes image dimensions on send to editor
 */
function bullet_remove_editor_image_dimensions( $html ) {
	$html = preg_replace( '/(width|height)=\"\d*\"\s/', '', $html );
	return $html;
}
add_filter( 'image_send_to_editor', 'bullet_remove_editor_image_dimensions', 10 );


/* Dashboard Widgets * * * * * * * * * * * * * * * * * * */

/**
 * Disables default dashboard widgets
 * Decide for each project to include or remove these widgets by commenting and uncommenting
 */
function bullet_dashboard_widgets() {
	// Wordpress dashboard widgets
	//remove_action( 'welcome_panel', 'wp_welcome_panel' );					// Welcome panel
	//remove_meta_box( 'dashboard_right_now', 'dashboard', 'core' );		// Right Now Widget
	//remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'core' );	// Comments Widget
	//remove_meta_box( 'dashboard_incoming_links', 'dashboard', 'core' );	// Incoming Links Widget
	//remove_meta_box( 'dashboard_plugins', 'dashboard', 'core' );			// Plugins Widget
	//remove_meta_box( 'dashboard_quick_press', 'dashboard', 'core' );		// Quick Press Widget
	//remove_meta_box( 'dashboard_recent_drafts', 'dashboard', 'core' );	// Recent Drafts Widget
	//remove_meta_box( 'dashboard_primary', 'dashboard', 'core' );			// Wordpress Blog Feed
	//remove_meta_box( 'dashboard_secondary', 'dashboard', 'core' );		// Other Wordpress News

	// Plugins dashboard widgets
}
add_action( 'admin_menu', 'bullet_dashboard_widgets' );


/* Admin Menu * * * * * * * * * * * * * * * * * * */

/**
 * Removes unwanted menu items in the admin panel
 * Add more using the filename being called as the argument
 */
function bullet_hide_menu_pages() {
	remove_menu_page( 'link-manager.php' ); // you can also do this for subitems: remove_submenu_page( $menu_slug, $submenu_slug );
}
add_action( 'admin_menu', 'bullet_hide_menu_pages' );

