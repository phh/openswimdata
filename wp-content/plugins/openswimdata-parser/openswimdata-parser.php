<?php
/*
Plugin Name: OpenSwimData Parser
Description: OpenSwimData part: Parser. Parsing the data from the crawler and adds it to the taxonomies we've already created.
Version: 1.0
Author: Patrick Hesselberg
*/

class OSD_Parser_Plugin {
	function __construct() {
		require plugin_dir_path( __FILE__ ) . 'lib/class-osd-parser.php';

		$this->register_plugin_hooks();
		$this->cron();
	}

	function delete() {
		$args = array( 'post_type' => array( 'swimmer', 'result', 'meeting' ), 'posts_per_page' => -1 );
		$posts = get_posts( $args );

		foreach( $posts as $post ) {
			wp_delete_post( $post->ID, true );
		}

		$args = array( 'hide_empty' => false );
		foreach( array( 'club', 'city', 'date', 'event', 'year', ) as $taxonomy ) {
			foreach( get_terms( $taxonomy, $args ) as $term ) {
				wp_delete_term( $term->term_id, $taxonomy );
			}
		}
	}

	function register_plugin_hooks() {
		register_activation_hook( __FILE__, array( &$this, 'activate' ) );
		register_deactivation_hook( __FILE__, array( &$this, 'deactivate' ) );
	}

	function activate() {
		if ( ! wp_next_scheduled( 'osd_parser_urls' ) ) {
			$tmps = get_posts( array( 'post_type' => 'tmp', 'post_status' => 'draft', 'numberposts' => 1 ) );

			if( !empty( $tmps ) ) {
				wp_schedule_event( time(), 'weekly', 'osd_parser_urls' );
			}
		}
	}

	function deactivate() {
		wp_clear_scheduled_hook( 'osd_parser_urls' );
		OSD_Crawler_Plugin::wp_unschedule_hook( 'osd_parser_url' );
	}

	function cron() {
		add_action( 'osd_parser_urls', array( &$this, 'osd_parser_urls' ) );
		add_action( 'osd_parser_url', array( &$this, 'osd_parser_url' ), 10, 2 );
	}

	function osd_parser_urls() {
		$crawler = new OSD_Parser;
		$crawler->parse_urls();
	}

	function osd_parser_url( $id, $rest = array() ) {
		$parser = new OSD_Parser;

		if( !empty( $rest ) ) {
			$parser->set_rest_data( $rest );
		}

		$parser->set_tmp( $id );

		$parser->parse();
	}
}

new OSD_Parser_Plugin;
