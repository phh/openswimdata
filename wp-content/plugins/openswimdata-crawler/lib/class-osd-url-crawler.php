<?php

class OSD_Url_Crawler {
	var $url;
	var $html;

	const BASE = 'http://www.swimrankings.net/index.php?page=rankingDetail&language=us&rankingClubId=';

	function set_url( $url ) {
		$url = $this::BASE . $url;

		$this->url = $url;
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

		$elements = $this->html->find( 'table.navigation td[class=navigation]', -1 )->innertext;
		$elements = (int) end( explode( ' ', $elements ) );

		for( $i = 1; $i <= $elements; $i += 25 ) {
			$urls[] = $this->url . '&firstPlace=' . $i;
		}

		return $urls;
	}
}

