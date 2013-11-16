<?php 
/**
 * Adds post types and their metaboxes . 
 *
 * @package WordPress
 * @subpackage OpenSwimData
 */ 
class OSD_Taxonomies {
	protected $_taxonomies = array( 'city', 'club', 'date', 'distance', 'event', 'gender', 'national', 'pool', 'season', 'style', 'year' );

	function __construct() {
		$this->register_taxonomies();
	}

	function register_taxonomies() {
		foreach( $this->_taxonomies as $taxonomy ) {
			add_action( 'init', array( &$this, 'taxonomy_' . $taxonomy ) );
		}
	}


	/* Taxonomies * * * * * * * * * * * * * * * * * * */

	function taxonomy_city() {
		$labels = array(
			'name' => _osd_x( 'Cities', 'taxonomy general name' ),
			'singular_name' => _osd__( 'City' ),
			'search_items' => _osd__( 'Search Cities' ),
			'popular_items' => _osd__( 'Popular Cities' ),
			'all_items' => _osd__( 'All Cities' ),
			'edit_item' => _osd__( 'Edit City' ),
			'view_item' => _osd__( 'View City' ),
			'update_item' => _osd__( 'Update City' ),
			'add_new_item' => _osd__( 'Add New City' ),
			'new_item_name' => _osd__( 'New City Name' ),
			'add_or_remove_items' => _osd__( 'Add or remove cities' ),
			'not_found' => _osd__( 'No cities found.' ),
			'menu_name' => _osd__( 'Cities' )
		);

		$args = array(
			'labels' => $labels,
			'public' => true,
			'show_admin_column' => true,
			'hierarchical' => true
		);

		$post_types = array( 'meeting' );

		register_taxonomy( 'city', $post_types, $args );
	}

	function taxonomy_club() {
		$labels = array(
			'name' => _osd_x( 'Clubs', 'taxonomy general name' ),
			'singular_name' => _osd__( 'Club' ),
			'search_items' => _osd__( 'Search Clubs' ),
			'popular_items' => _osd__( 'Popular Clubs' ),
			'all_items' => _osd__( 'All Clubs' ),
			'edit_item' => _osd__( 'Edit Club' ),
			'view_item' => _osd__( 'View Club' ),
			'update_item' => _osd__( 'Update Club' ),
			'add_new_item' => _osd__( 'Add New Club' ),
			'new_item_name' => _osd__( 'New Club Name' ),
			'add_or_remove_items' => _osd__( 'Add or remove clubs' ),
			'not_found' => _osd__( 'No clubs found.' ),
			'menu_name' => _osd__( 'Clubs' )
		);

		$args = array(
			'labels' => $labels,
			'public' => true,
			'show_admin_column' => true,
			'hierarchical' => true
		);

		$post_types = array( 'swimmer', 'result' );

		register_taxonomy( 'club', $post_types, $args );
	}

	function taxonomy_date() {
		$labels = array(
			'name' => _osd_x( 'Dates', 'taxonomy general name' ),
			'singular_name' => _osd__( 'Date' ),
			'search_items' => _osd__( 'Search Dates' ),
			'popular_items' => _osd__( 'Popular Dates' ),
			'all_items' => _osd__( 'All Dates' ),
			'edit_item' => _osd__( 'Edit Date' ),
			'view_item' => _osd__( 'View Date' ),
			'update_item' => _osd__( 'Update Date' ),
			'add_new_item' => _osd__( 'Add New Date' ),
			'new_item_name' => _osd__( 'New Year Date' ),
			'add_or_remove_items' => _osd__( 'Add or remove dates' ),
			'not_found' => _osd__( 'No dates found.' ),
			'menu_name' => _osd__( 'Dates' )
		);

		$args = array(
			'labels' => $labels,
			'public' => true,
			'show_admin_column' => true,
			'hierarchical' => true
		);

		$post_types = array( 'result' );

		register_taxonomy( 'date', $post_types, $args );
	}

	function taxonomy_distance() {
		$labels = array(
			'name' => _osd_x( 'Distances', 'taxonomy general name' ),
			'singular_name' => _osd__( 'Distance' ),
			'search_items' => _osd__( 'Search Distances' ),
			'popular_items' => _osd__( 'Popular Distances' ),
			'all_items' => _osd__( 'All Distances' ),
			'edit_item' => _osd__( 'Edit Distance' ),
			'view_item' => _osd__( 'View Distance' ),
			'update_item' => _osd__( 'Update Distance' ),
			'add_new_item' => _osd__( 'Add New Distance' ),
			'new_item_name' => _osd__( 'New Distance Name' ),
			'add_or_remove_items' => _osd__( 'Add or remove distances' ),
			'not_found' => _osd__( 'No distances found.' ),
			'menu_name' => _osd__( 'Distances' )
		);

		$args = array(
			'labels' => $labels,
			'public' => true,
			'show_admin_column' => true,
			'hierarchical' => true
		);

		$post_types = array( 'result' );

		register_taxonomy( 'distance', $post_types, $args );
	}

	function taxonomy_event() {
		$labels = array(
			'name' => _osd_x( 'Events', 'taxonomy general name' ),
			'singular_name' => _osd__( 'Event' ),
			'search_items' => _osd__( 'Search Events' ),
			'popular_items' => _osd__( 'Popular Events' ),
			'all_items' => _osd__( 'All Events' ),
			'edit_item' => _osd__( 'Edit Event' ),
			'view_item' => _osd__( 'View Event' ),
			'update_item' => _osd__( 'Update Event' ),
			'add_new_item' => _osd__( 'Add New Event' ),
			'new_item_name' => _osd__( 'New Event Name' ),
			'add_or_remove_items' => _osd__( 'Add or remove events' ),
			'not_found' => _osd__( 'No events found.' ),
			'menu_name' => _osd__( 'Events' )
		);

		$args = array(
			'labels' => $labels,
			'public' => true,
			'show_admin_column' => true,
			'hierarchical' => true
		);

		$post_types = array( 'meeting' );

		register_taxonomy( 'event', $post_types, $args );
	}

	function taxonomy_gender() {
		$labels = array(
			'name' => _osd_x( 'Genders', 'taxonomy general name' ),
			'singular_name' => _osd__( 'Gender' ),
			'search_items' => _osd__( 'Search Genders' ),
			'popular_items' => _osd__( 'Popular Genders' ),
			'all_items' => _osd__( 'All Genders' ),
			'edit_item' => _osd__( 'Edit Gender' ),
			'view_item' => _osd__( 'View Gender' ),
			'update_item' => _osd__( 'Update Gender' ),
			'add_new_item' => _osd__( 'Add New Gender' ),
			'new_item_name' => _osd__( 'New Gender Name' ),
			'add_or_remove_items' => _osd__( 'Add or remove genders' ),
			'not_found' => _osd__( 'No genders found.' ),
			'menu_name' => _osd__( 'Genders' )
		);

		$args = array(
			'labels' => $labels,
			'public' => true,
			'show_admin_column' => true,
			'hierarchical' => true
		);

		$post_types = array( 'swimmer' );

		register_taxonomy( 'gender', $post_types, $args );
	 }

	function taxonomy_national() {
		$labels = array(
			'name' => _osd_x( 'Nationals', 'taxonomy general name' ),
			'singular_name' => _osd__( 'National' ),
			'search_items' => _osd__( 'Search Nationals' ),
			'popular_items' => _osd__( 'Popular Nationals' ),
			'all_items' => _osd__( 'All Nationals' ),
			'edit_item' => _osd__( 'Edit National' ),
			'view_item' => _osd__( 'View National' ),
			'update_item' => _osd__( 'Update National' ),
			'add_new_item' => _osd__( 'Add New National' ),
			'new_item_name' => _osd__( 'New National Name' ),
			'add_or_remove_items' => _osd__( 'Add or remove nationals' ),
			'not_found' => _osd__( 'No nationals found.' ),
			'menu_name' => _osd__( 'Nationals' )
		);

		$args = array(
			'labels' => $labels,
			'public' => true,
			'show_admin_column' => true,
			'hierarchical' => true
		);

		$post_types = array( 'swimmer', 'result' );

		register_taxonomy( 'national', $post_types, $args );
	}

	function taxonomy_pool() {
		$labels = array(
			'name' => _osd_x( 'Pools', 'taxonomy general name' ),
			'singular_name' => _osd__( 'Pool' ),
			'search_items' => _osd__( 'Search Pools' ),
			'popular_items' => _osd__( 'Popular Pools' ),
			'all_items' => _osd__( 'All Pools' ),
			'edit_item' => _osd__( 'Edit Pool' ),
			'view_item' => _osd__( 'View Pool' ),
			'update_item' => _osd__( 'Update Pool' ),
			'add_new_item' => _osd__( 'Add New Pool' ),
			'new_item_name' => _osd__( 'New Pool Name' ),
			'add_or_remove_items' => _osd__( 'Add or remove pools' ),
			'not_found' => _osd__( 'No pools found.' ),
			'menu_name' => _osd__( 'Pools' )
		);

		$args = array(
			'labels' => $labels,
			'public' => true,
			'show_admin_column' => true,
			'hierarchical' => true
		);

		$post_types = array( 'result' );

		register_taxonomy( 'pool', $post_types, $args );
	}

	function taxonomy_season() {
		$labels = array(
			'name' => _osd_x( 'Seasons', 'taxonomy general name' ),
			'singular_name' => _osd__( 'Season' ),
			'search_items' => _osd__( 'Search Seasons' ),
			'popular_items' => _osd__( 'Popular Seasons' ),
			'all_items' => _osd__( 'All Seasons' ),
			'edit_item' => _osd__( 'Edit Season' ),
			'view_item' => _osd__( 'View Season' ),
			'update_item' => _osd__( 'Update Season' ),
			'add_new_item' => _osd__( 'Add New Season' ),
			'new_item_name' => _osd__( 'New Season Name' ),
			'add_or_remove_items' => _osd__( 'Add or remove seasons' ),
			'not_found' => _osd__( 'No seasons found.' ),
			'menu_name' => _osd__( 'Seasons' )
		);

		$args = array(
			'labels' => $labels,
			'public' => true,
			'show_admin_column' => true,
			'hierarchical' => true
		);

		$post_types = array( 'result' );

		register_taxonomy( 'season', $post_types, $args );
	}

	function taxonomy_style() {
		$labels = array(
			'name' => _osd_x( 'Styles', 'taxonomy general name' ),
			'singular_name' => _osd__( 'Style' ),
			'search_items' => _osd__( 'Search Styles' ),
			'popular_items' => _osd__( 'Popular Styles' ),
			'all_items' => _osd__( 'All Styles' ),
			'edit_item' => _osd__( 'Edit Style' ),
			'view_item' => _osd__( 'View Style' ),
			'update_item' => _osd__( 'Update Style' ),
			'add_new_item' => _osd__( 'Add New Style' ),
			'new_item_name' => _osd__( 'New Style Name' ),
			'add_or_remove_items' => _osd__( 'Add or remove styles' ),
			'not_found' => _osd__( 'No styles found.' ),
			'menu_name' => _osd__( 'Styles' )
		);

		$args = array(
			'labels' => $labels,
			'public' => true,
			'show_admin_column' => true,
			'hierarchical' => true
		);

		$post_types = array( 'result' );

		register_taxonomy( 'style', $post_types, $args );
	}

	function taxonomy_year() {
		$labels = array(
			'name' => _osd_x( 'Years', 'taxonomy general name' ),
			'singular_name' => _osd__( 'Year' ),
			'search_items' => _osd__( 'Search Years' ),
			'popular_items' => _osd__( 'Popular Years' ),
			'all_items' => _osd__( 'All Years' ),
			'edit_item' => _osd__( 'Edit Year' ),
			'view_item' => _osd__( 'View Year' ),
			'update_item' => _osd__( 'Update Year' ),
			'add_new_item' => _osd__( 'Add New Year' ),
			'new_item_name' => _osd__( 'New Year Name' ),
			'add_or_remove_items' => _osd__( 'Add or remove years' ),
			'not_found' => _osd__( 'No years found.' ),
			'menu_name' => _osd__( 'Years' )
		);

		$args = array(
			'labels' => $labels,
			'public' => true,
			'show_admin_column' => true,
			'hierarchical' => true
		);

		$post_types = array( 'swimmer', 'result' );

		register_taxonomy( 'year', $post_types, $args );
	}
}

