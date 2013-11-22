<?php
/*
Plugin Name: OpenSwimData Crawler
Description: OpenSwimData part: Crawler.
Version: 1.0
Author: Patrick Hesselberg
*/

class OSD_Crawler_Plugin {
	function __construct() {
		require plugin_dir_path( __FILE__ ) . 'lib/class-osd-generate-urls.php';

		$this->register_plugin_hooks();
	}

	function register_plugin_hooks() {
		register_activation_hook( __FILE__, array( &$this, 'activate' ) );
		register_deactivation_hook( __FILE__, array( &$this, 'deactivate' ) );
	}

	function activate() {
		add_option( 'osd_urls', array() );
		add_option( 'osd_style_urls', array() );
		add_option( 'osd_base_urls', array() );
	}

	function deactivate() {}
}

new OSD_Crawler_Plugin;
