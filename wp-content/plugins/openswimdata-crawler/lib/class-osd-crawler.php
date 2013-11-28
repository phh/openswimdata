<?php

class OSD_Crawler {

	function __construct() {
		require_once 'class-osd-url-crawler.php';
		require_once 'simple_html_dom.php';
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
			wp_schedule_single_event( $this->cron_time(), 'osd_crawler_style_url', array( $url ) );
		}
	}

	function make_style_url( $url ) {
		$style_urls = get_option( 'osd_style_urls' );
		$crawler = new OSD_Url_Crawler;

		$crawler->set_url( $url );
		$crawler->request();
		$crawler_styles_url = $crawler->get_styles_urls();

		if( array_key_exists( $url, $style_urls ) ) {
			if( $style_urls[$url] == $crawler_styles_url ) {
				return;
			}
		}

		//TODO Check if the url is already in there. &gender=1&course=SCM&season=2007 will return 2x200m freestyle.
		$style_urls[$url] = $crawler_styles_url;
		update_option( 'osd_style_urls', $style_urls );
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

	function make_urls() {
		$style_urls = get_option( 'osd_style_urls' );
		$new_urls = array();

		foreach( $style_urls as $base => $urls ) {
			wp_schedule_single_event( $this->cron_time(), 'osd_crawler_url', array( $base, $urls ) );
		}
	}

	function make_url( $base, $urls ) {
		$style_urls = get_option( 'osd_style_urls' );
		$osd_urls = get_option( 'osd_urls' );
		$crawler_urls = $osd_urls;

		foreach( $urls as $url ) {
			$crawler = new OSD_Url_Crawler;

			$crawler->set_url( $crawler::STYLE_BASE . $url->href, false );
			$crawler->request();
			$crawler_urls[$base][$url->href] = $crawler->get_urls();
		}

		if( array_key_exists( $base, $osd_urls ) ) {
			if( $osd_urls[$base] == $crawler_urls[$base] ) {
				return;
			}
		}

		update_option( 'osd_urls', $crawler_urls );
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
				wp_schedule_single_event( $this->cron_time(), 'osd_save_url', array( $base, $url ) );
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
			$crawler->set_url( $crawler_url, false );
			$crawler->request();
			$crawler->append_html( 'table.rankingList tr[class^=rankingList]' );
		}

		add_post_meta( $tmp, 'tmp_base', $base );
		add_post_meta( $tmp, 'tmp_style', $url->style );
		add_post_meta( $tmp, 'tmp_distance', $url->distance );
		add_post_meta( $tmp, 'tmp_data', $crawler->get_html() );
	}
}

