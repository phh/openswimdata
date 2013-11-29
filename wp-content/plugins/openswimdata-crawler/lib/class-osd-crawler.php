<?php

class OSD_Crawler {
	var $style_urls;

	function __construct() {
		require_once 'class-osd-url-crawler.php';
		require_once 'simple_html_dom.php';

		$this->style_urls = get_option( 'osd_style_urls' );
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
		$style_urls = get_option( 'osd_style_urls' );
		$new_style_urls = array();

		foreach( $base_urls as $url ) {
			$this->make_style_url( $url );
		}

		update_option( 'osd_style_urls', $this->style_urls );
	}

	function make_style_url( $url ) {
		$crawler = new OSD_Url_Crawler;
		$crawler->set_url( $url );
		$crawler->request();
		$crawler_styles_url = $crawler->get_styles_urls();

		if( array_key_exists( $url, $this->style_urls ) ) {
			if( $this->style_urls[$url] == $crawler_styles_url ) {
				return;
			}
		}

		$this->style_urls[$url] = $crawler_styles_url;
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

	function cron_time() {
		static $crontime = 0;

		if( $crontime < 1 ) {
			$crontime = time();
		} else {
			$crontime += 2 * MINUTE_IN_SECONDS;
		}

		return $crontime;
	}

	function save_urls() {
		$osd_urls = get_option( 'osd_style_urls' );
		$html = array();
		foreach( $osd_urls as $base => $style_urls ) {
			foreach( $style_urls as $url ) {
				wp_schedule_single_event( $this->cron_time(), 'osd_crawler_save_url', array( $base, $url ) );
			}
		}
	}

	function save_url( $base, $url ) {
		$crawler = new OSD_Url_Crawler;
		$crawler->set_base( $crawler::STYLE_BASE );
		$crawler->set_url( $url->href );
		$crawler->request();
		$crawler->get_urls();

		$postarr = array(
			'post_title' => $base . $url->style . $url->distance,
			'post_type' => 'tmp',
			'post_status' => 'draft',
			'post_author' => 1
		);
		$tmp = wp_insert_post( $postarr );

		foreach( $crawler->urls as $crawler_url ) {
			$crawler->set_url( $crawler_url );
			$crawler->request();
			$crawler->append_html( 'table.rankingList tr[class^=rankingList]' );
		}

		add_post_meta( $tmp, 'tmp_base', $base );
		add_post_meta( $tmp, 'tmp_style', $url->style );
		add_post_meta( $tmp, 'tmp_distance', $url->distance );
		add_post_meta( $tmp, 'tmp_data', $crawler->get_html() );
	}
}

