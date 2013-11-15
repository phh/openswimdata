<?php 
/**
 * Adds post types and their metaboxes . 
 *
 * @package WordPress
 * @subpackage OpenSwimData
 */ 
class OSD_Post_Types {
	protected $_post_types = array( 'swimmer', 'result', 'meeting' );

	function __construct() {
		$this->register_post_types();
	}

	function register_post_types() {
		foreach( $this->_post_types as $post_type ) {
			add_action( 'init', array( &$this, 'post_type_' . $post_type ) );			
		}
	}


	/* Post Types * * * * * * * * * * * * * * * * * * */

	function post_type_swimmer() {
		$labels = array(
			'name' => _osd__( 'Swimmers' ),
			'singular_name' => _osd__( 'Swimmer' ),
			'add_new' => _osd_x( 'Add New', 'Swimmer' ),
			'add_new_item' => _osd__( 'Add New Swimmer' ),
			'edit_item' => _osd__( 'Edit Swimmer' ),
			'new_item' => _osd__( 'New Swimmer' ),
			'view_item' => _osd__( 'View Swimmer' ),
			'search_items' => _osd__( 'Search Swimmers' ),
			'not_found' => _osd__( 'No swimmers found.' ),
			'not_found_in_trash' => _osd__( 'No swimmers found in Trash.' ),
			'all_items' => _osd__( 'All Swimmers' ),
			'menu_name' => _osd__( 'Swimmers' ),
		);

		$args = array(
			'labels' => $labels,
			'description' => '',
			'public' => true,
			'menu_icon' => '',
			'hierarchical' => false,
			'supports' => array( 'title', 'revisions' ),
			'taxonomies' => array(),
			'has_archive' => true
		);

		register_post_type( 'swimmer', $args );
	}

	function post_type_result() {
		$labels = array(
			'name' => _osd__( 'Results' ),
			'singular_name' => _osd__( 'Result' ),
			'add_new' => _osd_x( 'Add New', 'Result' ),
			'add_new_item' => _osd__( 'Add New Result' ),
			'edit_item' => _osd__( 'Edit Result' ),
			'new_item' => _osd__( 'New Result' ),
			'view_item' => _osd__( 'View Result' ),
			'search_items' => _osd__( 'Search Results' ),
			'not_found' => _osd__( 'No results found.' ),
			'not_found_in_trash' => _osd__( 'No results found in Trash.' ),
			'all_items' => _osd__( 'All Results' ),
			'menu_name' => _osd__( 'Results' ),
		);

		$args = array(
			'labels' => $labels,
			'description' => '',
			'public' => true,
			'menu_icon' => '',
			'hierarchical' => false,
			'supports' => array( 'title', 'revisions' ),
			'taxonomies' => array(),
			'has_archive' => true
		);

		register_post_type( 'result', $args );
	}

	function post_type_meeting() {
		$labels = array(
			'name' => _osd__( 'Meetings' ),
			'singular_name' => _osd__( 'Meeting' ),
			'add_new' => _osd_x( 'Add New', 'Meeting' ),
			'add_new_item' => _osd__( 'Add New Meeting' ),
			'edit_item' => _osd__( 'Edit Meeting' ),
			'new_item' => _osd__( 'New Meeting' ),
			'view_item' => _osd__( 'View Meeting' ),
			'search_items' => _osd__( 'Search Meeting' ),
			'not_found' => _osd__( 'No meetings found.' ),
			'not_found_in_trash' => _osd__( 'No meetings found in Trash.' ),
			'all_items' => _osd__( 'All Meetings' ),
			'menu_name' => _osd__( 'Meetings' ),
		);

		$args = array(
			'labels' => $labels,
			'description' => '',
			'public' => true,
			'menu_icon' => '',
			'hierarchical' => false,
			'supports' => array( 'title', 'revisions' ),
			'taxonomies' => array(),
			'has_archive' => true
		);

		register_post_type( 'meeting', $args );
	}
}

