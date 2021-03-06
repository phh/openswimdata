<?php
/*
Plugin Name: OpenSwimData
Description: OpenSwimData will crawl, parse and deliver swimming statistics.
Version: 1.0
Author: Patrick Hesselberg
*/

class Openswimdata {
	public $_post_types;
	public $_taxonomies;
	public $_p2p;

	static public $PLUGIN_URL;
	static public $PLUGIN_DIR;
	static public $PLUGIN_CLS_DIR;

	function __construct() {
		$this->load();
		$this->register_plugin_hooks();
		$this->enqueue();
		$this->cron_schedules();
	}

	function load() {
		self::$PLUGIN_URL = plugins_url( '/', __FILE__ );
		self::$PLUGIN_DIR = plugin_dir_path( __FILE__ );

		$this->inc();
		$this->_post_types = new OSD_Post_Types;
		$this->_taxonomies = new OSD_Taxonomies;
		$this->_post_types_metabox = new OSD_Post_Types_Metabox;
		$this->_taxonomies_metabox = new OSD_Taxonomies_Metaboxes;
		$this->_p2p = new OSD_P2P;
	}

	function inc() {
		include self::$PLUGIN_DIR . 'classes/class-osd-post-types.php';
		include self::$PLUGIN_DIR . 'classes/class-osd-post-types-metabox.php';
		include self::$PLUGIN_DIR . 'classes/class-osd-taxonomies.php';
		include self::$PLUGIN_DIR . 'classes/class-osd-taxonomies-metabox.php';
		include self::$PLUGIN_DIR . 'classes/class-osd-p2p.php';
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


	/* Cron * * * * * * * * * * * * * * * * * * */

	function cron_schedules() {
		add_filter( 'cron_schedules', array( &$this, 'cron_schedule_weekly' ) );
		add_filter( 'cron_schedules', array( &$this, 'cron_schedule_30_days' ) );
	}

	function cron_schedule_weekly( $schedules ) {
		$schedules['weekly'] = array(
			'interval' => WEEK_IN_SECONDS,
			'display' => _osd__( 'Once Weekly' )
		);

		return $schedules;
	}

	function cron_schedule_30_days( $schedules ) {
		$schedules['30_days'] = array(
			'interval' => 30 * DAY_IN_SECONDS,
			'display' => _osd__( '30 Days' )
		);

		return $schedules;
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

