<?php

class OSD_Url_Crawler {
	var $url;
	var $html;
	var $base;
	var $number_count = 25;
	var $get_html = array();
	var $urls = array();

	const BASE =       'http://www.swimrankings.net/index.php?page=rankingDetail&clubId=95&language=us';
	const STYLE_BASE = 'http://www.swimrankings.net/index.php?page=rankingDetail&language=us&rankingClubId=';

	function set_url( $url ) {
		if( !empty( $this->base ) ) {
			$url = $this->base . $url;
		} else {
			$url = $this::BASE . $url;
		}

		$this->url = $url;
	}

	function set_base( $base = '' ) {
		$this->base = $base;
	}

	function get_html() {
		if( !empty( $this->get_html ) ) {
			return $this->get_html;
		}

		return false;
	}

	function request() {
		if( !function_exists( 'file_get_html' ) ) {
			die( 'You need the simple html dom for this to work' );
		}

		// swimrankings.net write bad html so we gotta make it prettier...
		$remote = wp_remote_get( $this->url );
		$dom = new DOMDocument;
		@$dom->loadHTML( $remote['body'] );
		$clean_html = $dom->saveHTML();

		$request = str_get_html( $clean_html );
		$this->html = $request;
	}

	function get_styles_urls() {
		$urls = array();

		foreach( $this->html->find( 'table.rankingList td[class=swimstyle] a' ) as $style ) {
			$url = new stdClass;
			$url->href = $this->get_ranking_id( $style->href );
			$url->distance = $this->get_distance( $style->innertext );
			$url->style = $this->get_style( $style->innertext );

			if( !in_array( $url, $urls ) ) {
				$urls[] = $url;
			}
		}

		return $urls;
	}

	function get_urls() {
		$urls = array();

		$elements = $this->get_number_elements();

		for( $i = 1; $i <= $elements; $i += 25 ) {
			$urls[] = $this->url . '&firstPlace=' . $i;
		}

		$this->urls = $urls;
	}

	function get_ranking_id( $link ) {
		$link = htmlspecialchars_decode( $link );
		parse_str( $link, $args );
		$link = $args['rankingClubId'];

		return $link;
	}

	function get_distance( $swimstyle ) {
		return intval( $swimstyle );
	}

	function get_style( $swimstyle ) {
		$swimstyle = explode( ' ', $swimstyle );
		array_shift( $swimstyle );
		$distance = implode( ' ', $swimstyle );

		return $distance;
	}

	function get_number_elements() {
		$elements = $this->html->find( 'table.navigation td[class=navigation]', -1 )->innertext;
		$elements = (int) end( explode( ' ', $elements ) );

		return $elements;
	}

	function append_html( $element ) {
		foreach( $this->html->find( $element ) as $tr ) {
			if( !in_array( $tr->innertext, $this->get_html ) ) {
				$this->get_html[] = $tr->innertext;
			}
		}

		return true;
	}
}

