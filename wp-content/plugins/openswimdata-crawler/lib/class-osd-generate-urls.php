<?php
/*
Plugin Name: OpenSwimData Crawler
Description: OpenSwimData part: Crawler.
Version: 1.0
Author: Patrick Hesselberg
*/

class OSD_Generate_Urls {
	static public $PLUGIN_URL;
	static public $PLUGIN_DIR;

	function __construct() {
		require_once 'class-osd-crawler.php';
		require_once 'simple_html_dom.php';

		$this->make_base_urls();
		$this->make_style_urls();
	}

	function make_base_urls() {
		$urls = array();

		$terms_args = array( 'hide_empty' => false );
		$genders = get_terms( 'gender', $terms_args );
		$pools = get_terms( 'pool', $terms_args );
		$seasons = get_terms( 'season', $terms_args );

		foreach( $genders as $gender ) {
			foreach( $pools as $pool ) {
				foreach( $seasons as $season ) {
					$urls[] = $this->make_base_url( $gender, $pool, $season );
				}
			}
		}

		update_option( 'osd_base_urls', $urls );
	}

	function make_style_urls() {
		$base_urls = get_option( 'osd_base_urls' );
		$style_urls = array();
		$crawler = new OSD_Style_Crawler;
#$count = 0;
		foreach( $base_urls as $base_url ) {#$count++;
			$crawler->set_url( $base_url );
			$crawler->request();
			$style_urls[$base_url] = $crawler->get_styles_urls();#if( $count >= 10 ) die(krumo($style_urls));
		}die(krumo($style_urls));
	}

	function make_base_url( $gender, $pool, $season ) {
		$url = '&gender=';
		$url .= OSD_Taxonomies_Metaboxes::get_term_meta( 'gender', $gender->term_id );
		$url .= '&course=';
		$url .= OSD_Taxonomies_Metaboxes::get_term_meta( 'pool', $pool->term_id );
		$url .= '&season=';
		$url .= OSD_Taxonomies_Metaboxes::get_term_meta( 'season', $season->term_id );

		return $url;
	}
}

function dis_wp_loaded() {
	new OSD_Generate_Urls;
}
#add_action( 'wp_loaded', 'dis_wp_loaded' );

			
			