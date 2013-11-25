<?php
/*
Plugin Name: OpenSwimData Crawler
Description: OpenSwimData part: Crawler.
Version: 1.0
Author: Patrick Hesselberg
*/

class OSD_Generate_Urls {
	const BASE = 'http://www.swimrankings.net/index.php?page=rankingDetail&rankingClubId=42463339';

	function __construct() {
		require_once 'class-osd-crawler.php';
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
			wp_schedule_single_event( $this->cron_time( 5 ), 'osd_crawler_style_url', array( $url ) );
		}
	}

	function make_style_url( $url ) {
		$style_urls = get_option( 'osd_style_urls' );
		$crawler = new OSD_Style_Crawler;

		$crawler->set_url( $url );
		$crawler->request();
		$crawler_styles_url = $crawler->get_styles_urls();

		if( array_key_exists( $url, $style_urls ) ) {
			if( $style_urls[$url] == $crawler_styles_url ) {
				return;
			}
		}

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
			wp_schedule_single_event( $this->cron_time( 15 ), 'osd_crawler_url', array( $base, $urls ) );
		}
	}

	function make_url( $base, $urls ) {
		$style_urls = get_option( 'osd_style_urls' );
		$osd_urls = get_option( 'osd_urls' );
		$crawler_urls = $osd_urls;

		foreach( $urls as $url ) {
			$crawler = new OSD_Url_Crawler;

			$crawler->set_url( $url );
			$crawler->request();
			$crawler_urls[$base][$url] = $crawler->get_urls();
		}

		if( array_key_exists( $base, $osd_urls ) ) {
			if( $osd_urls[$base] == $crawler_urls[$base] ) {
				return;
			}
		}

		update_option( 'osd_urls', $crawler_urls );
	}

	function cron_time( $minutes ) {
		static $crontime = 0;

		if( $crontime < 1 ) {
			$crontime = time();
		} else {
			$crontime += $minutes * MINUTE_IN_SECONDS;
		}

		return $crontime;
	}

	function save_urls() {
		$osd_urls = get_option( 'osd_urls' );
		$html = array();

		foreach( $osd_urls as $base => $style_urls ) {
			foreach( $style_urls as $urls ) {
				$last_url = end( $urls );

				wp_schedule_single_event( $this->cron_time( 2 ), 'osd_save_url', array( $base, $urls, $last_url ) );
			}
		}
	}

	function save_url( $base, $urls, $last_url ) {
		$crawler = new OSD_Url_Crawler;
		$crawler->set_last_url( $last_url );

		foreach( $urls as $url ) {
			$crawler->set_url( $url, false );
			$crawler->request();
			$crawler->save_html( 'table.rankingList' );
		}
	}
}
			
			