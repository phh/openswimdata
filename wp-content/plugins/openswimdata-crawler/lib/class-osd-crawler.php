<?php

class OSD_Style_Crawler {
	var $url;
	var $html;

	const SWIMRANKING_BASE = 'http://www.swimrankings.net/index.php?page=rankingDetail&clubId=95&language=us';
	const RANK_DEN = 'http://www.swimrankings.net/index.php?page=rankingDetail&club=DEN&language=us';

	function set_url( $url ) {
		$url = $this::SWIMRANKING_BASE . $url;

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

	function get_styles_urls() {
		$urls = array();

		foreach( $this->html->find( 'table.rankingList td[class=swimstyle] a' ) as $style ) {
			$link = htmlspecialchars_decode( $style->href );
			parse_str( $link, $args );
			$link = $args['rankingClubId'];

			$urls[] = $link;
		}

		return $urls;
	}
}

