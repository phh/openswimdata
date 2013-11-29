<?php
/*
Plugin Name: OpenSwimData Crawler
Description: OpenSwimData part: Crawler. Make the urls, crawl them and save it into to later get parsed.
Version: 1.0
Author: Patrick Hesselberg
*/

class OSD_Crawler_Plugin {
	function __construct() {
		require plugin_dir_path( __FILE__ ) . 'lib/class-osd-crawler.php';

		$this->register_plugin_hooks();
		$this->cron();
	}

	function register_plugin_hooks() {
		register_activation_hook( __FILE__, array( &$this, 'activate' ) );
		register_deactivation_hook( __FILE__, array( &$this, 'deactivate' ) );
	}

	function activate() {
		add_option( 'osd_base_urls', array() );
		add_option( 'osd_style_urls', array() );

		if ( ! wp_next_scheduled( 'osd_crawler_base_urls' ) ) {
			wp_schedule_event( strtotime( 'monday' ), 'weekly', 'osd_crawler_base_urls' );
		}
	}

	function deactivate() {
		wp_clear_scheduled_hook( 'osd_crawler_base_urls' );
		wp_clear_scheduled_hook( 'osd_crawler_style_urls' );
		wp_clear_scheduled_hook( 'osd_crawler_save_urls' );
		$this->wp_unschedule_hook( 'osd_crawler_save_url' );
	}

	function cron() {
		add_action( 'osd_crawler_base_urls', array( &$this, 'osd_crawler_base_urls' ) );
		add_action( 'osd_crawler_style_urls', array( &$this, 'osd_crawler_style_urls' ) );
		add_action( 'osd_crawler_save_urls', array( &$this, 'osd_crawler_save_urls' ) );
		add_action( 'osd_crawler_save_url', array( &$this, 'osd_crawler_save_url' ), 10, 2 );
	}

	/**
	 * http://core.trac.wordpress.org/ticket/18997 #cron.patch
	 */
	function wp_unschedule_hook( $hook ) {
		$crons = _get_cron_array();

		if ( empty( $crons ) ) {
			return;
		}

		foreach($crons as $timestamp => $args) {
			unset( $crons[$timestamp][$hook] );
		}

		_set_cron_array( $crons, 'removed' );
	}

	function make_style_urls_cron() {
		if ( ! wp_next_scheduled( 'osd_crawler_style_urls' ) ) {
			wp_schedule_event( time(), 'weekly', 'osd_crawler_style_urls' );
		}
	}

	function save_urls_cron() {
		if ( ! wp_next_scheduled( 'osd_crawler_save_urls' ) ) {
			wp_schedule_event( time(), 'weekly', 'osd_crawler_save_urls' );
		}
	}

	function osd_crawler_base_urls() {
		$crawler = new OSD_Crawler;
		$crawler->make_base_urls();

		$this->make_style_urls_cron();
	}

	function osd_crawler_style_urls() {
		$crawler = new OSD_Crawler;
		$crawler->make_style_urls();

		$this->save_urls_cron();
	}

	function osd_crawler_save_urls() {
		$crawler = new OSD_Crawler;
		$crawler->save_urls();
	}

	function osd_crawler_save_url( $base, $url ) {
		$crawler = new OSD_Crawler;
		$crawler->save_url( $base, $url );
	}
}

new OSD_Crawler_Plugin;

