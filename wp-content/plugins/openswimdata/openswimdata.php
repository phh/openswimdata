<?php
/*
Plugin Name: OpenSwimData
Description: OpenSwimData will crawl, parse and deliver swimming statistics.
Version: 0.1
Author: Patrick Hesselberg
*/

class Openswimdata {
	public $_post_types;
	public $_taxonomies;

	static public $PLUGIN_URL;
	static public $PLUGIN_DIR;
	static public $PLUGIN_CLS_DIR;

	function __construct() {
		$this->load();
		$this->register_plugin_hooks();
		$this->enqueue();
	}

	function load() {
		self::$PLUGIN_URL = plugins_url( '/', __FILE__ );
		self::$PLUGIN_DIR = plugin_dir_path( __FILE__ );

		$this->inc();
		$this->_post_types = new OSD_Post_Types;
		$this->_taxonomies = new OSD_Taxonomies;
		$this->_post_types_metabox = new OSD_Post_Types_Metabox;
		$this->_taxonomies_metabox = new OSD_Taxonomies_Metaboxes;
	}

	function inc() {
		include self::$PLUGIN_DIR . 'classes/class-osd-post-types.php';
		include self::$PLUGIN_DIR . 'classes/class-osd-post-types-metabox.php';
		include self::$PLUGIN_DIR . 'classes/class-osd-taxonomies.php';
		include self::$PLUGIN_DIR . 'classes/class-osd-taxonomies-metabox.php';
	}

	function register_plugin_hooks() {
		register_activation_hook( __FILE__, array( &$this, 'activate' ) );
		register_deactivation_hook( __FILE__, array( &$this, 'deactivate' ) );
	}

	function activate() {
		$this->_post_types->post_type_swimmer();
		$this->_post_types->post_type_result();
		$this->_post_types->post_type_meeting();
		$this->_taxonomies->taxonomy_city();
		$this->_taxonomies->taxonomy_club();
		$this->_taxonomies->taxonomy_distance();
		$this->_taxonomies->taxonomy_gender();
		$this->_taxonomies->taxonomy_pool();
		$this->_taxonomies->taxonomy_style();
		$this->_taxonomies->taxonomy_year();

		flush_rewrite_rules();
	}

	function deactivate() {
		flush_rewrite_rules();
	}


	/* Config * * * * * * * * * * * * * * * * * * */

	function enqueue() {
		add_action( 'admin_enqueue_scripts', array( &$this, 'css' ) );
	}

	function css() {
		wp_enqueue_style( 'osd-style', self::$PLUGIN_URL . 'assets/css/style.css' );
	}
}

function _osd_e( $text ) {
	_e( $text, 'openswimdata' );
}

function _osd__( $text ) {
	return __( $text, 'openswimdata' );
}

function _osd_x( $text, $context ) {
	return _x( $text, $context, 'openswimdata' );
}

function _osd_ex( $text, $context ) {
	return _ex( $text, $context, 'openswimdata' );
}

new Openswimdata;

