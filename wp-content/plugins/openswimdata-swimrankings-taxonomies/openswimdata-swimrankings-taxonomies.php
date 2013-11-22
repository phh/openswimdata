<?php
/*
Plugin Name: OpenSwimData Swimrankings Taxonomies
Description: OpenSwimData part: Swimrankings Taxonomies.
Version: 1.0
Author: Patrick Hesselberg
*/

class OSD_Swimrankings_Taxonomies_Plugin {

	function __construct() {
		require plugin_dir_path( __FILE__ ) . 'lib/class-osd-swimrankings-taxonomies.php';

		$this->register_plugin_hooks();
		$this->cron();
	}

	function register_plugin_hooks() {
		register_activation_hook( __FILE__, array( &$this, 'activate' ) );
		register_deactivation_hook( __FILE__, array( &$this, 'deactivate' ) );
	}

	function activate() {
		if ( ! wp_next_scheduled( 'osd_swimrankings_taxonomies' ) ) {
			wp_schedule_event( strtotime( 'monday' ), 'weekly', 'osd_swimrankings_taxonomies' );
		}
	}

	function deactivate() {
		wp_clear_scheduled_hook( 'osd_swimrankings_taxonomies' );
	}

	function cron() {
		add_action( 'osd_swimrankings_taxonomies', array( &$this, 'osd_swimrankings_taxonomies' ) );
	}

	function osd_swimrankings_taxonomies() {
		// Handles everything in a single class. Just make a new instance of it.
		new OSD_Swimrankings_Taxonomies;
	}
}

new OSD_Swimrankings_Taxonomies_Plugin;

