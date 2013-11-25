<?php

class OSD_Url_Crawler {
	var $url;
	var $html;
	var $base;
	var $last_url;
	var $number_count = 25;

	const BASE = 'http://www.swimrankings.net/index.php?page=rankingDetail&language=us&rankingClubId=';

	function set_url( $url, $base = true ) {
		if( $base ) {
			$url = $this::BASE . $url;
		}

		$this->url = $url;
	}

	function set_base( $base ) {
		$this->base = $base;
	}

	function set_last_url( $last_url ) {
		$this->last_url = $last_url;
	}

	function request() {
		if( !function_exists( 'file_get_html' ) ) {
			die( 'You need the simple html dom for this to work' );
		}

		// swimrankings.net write bad html so we gotta make it prettier...
		require_once 'htmlpurifier/library/HTMLPurifier.auto.php';
		$config = HTMLPurifier_Config::createDefault();
		$config->set('HTML.Trusted', true);
		$purifier = new HTMLPurifier( $config );
		$clean_html = $purifier->purify( file_get_contents( $this->url ) );

		$request = str_get_html( $clean_html );
		$this->html = $request;
	}

	function get_urls() {
		$urls = array();

		$elements = $this->get_number_elements();

		for( $i = 1; $i <= $elements; $i += 25 ) {
			$urls[] = $this->url . '&firstPlace=' . $i;
		}

		return $urls;
	}

	function get_number_elements() {
		$elements = $this->html->find( 'table.navigation td[class=navigation]', -1 )->innertext;
		$elements = (int) end( explode( ' ', $elements ) );

		return $elements;
	}

	function get_firstplace() {
		parse_str( $this->url, $parsed );

		return $parsed['firstPlace'];
	}

	function get_number_skip() {
		$url_firstplace = $this->get_firstplace();
		$real_firstplace = $this->html->find( 'input[name=firstPlace]', 0 )->attr['value'];
		$skip = $url_firstplace - $real_firstplace;

		return $skip;
	}

	function save_html( $element ) {
		$html = $this->html->find( $element, -1 )->outertext;

		if( empty( $html ) ) {
			return false;
		}
		$postarr = array(
			'post_title' => $this->url,
			'post_type' => 'tmp',
			'post_status' => 'draft',
			'post_author' => 1
		);
		$tmp = wp_insert_post( $postarr );

		add_post_meta( $tmp, 'tmp_data', $html );
		add_post_meta( $tmp, 'tmp_base', $this->base );

		if( $this->last_url == $this->url ) {
			add_post_meta( $tmp, 'tmp_skip', $this->get_number_skip() );
		}

		return true;
	}
}

