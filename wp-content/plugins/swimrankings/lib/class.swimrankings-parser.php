<?php

class Swimrankings_Parser {
	var $url;
	var $get_html;
	var $swimmers;

	const SWIMRANKING_BASE = 'http://www.swimrankings.net/index.php?page=rankingDetail&clubId=95&language=us';
	const RANK_DEN = 'http://www.swimrankings.net/index.php?page=rankingDetail&club=DEN&language=us';

	function set_url( $url ) {
		$parsed = parse_url( $url );

		if( $parsed['host'] != 'www.swimrankings.net' )
			die( 'Url does not match <strong>www.swimrankings.net</strong>' );

		$this->url = $url;
	}

	function build_url( $args ) {
		if( !is_array( $args ) )
			die( 'Cannot build that URL with those args' );

		extract( $args );

		if( !isset( $page ) )
			$page = 'rankingDetail';

		if( !isset( $id ) )
			die( 'No ID defined' );

		$url_args = array( 'page' => $page, 'rankingClubId' => $id, 'language' => 'us' );
		$url = $this->build_url_string( $url_args, true );

		$this->url = $url;
	}

	function build_url_string( $args, $default = false ) {
		$base = $default === false ? $this::SWIMRANKING_BASE : $this::RANK_DEN;
		$params = array();

		foreach( $args as $k => $v )
			$args[$k] = $k . '=' . $v;

		$url = $base . implode( '&', $args );

		return $url;
	}

	function request() {
		if( empty( $this->url ) )
			die( 'Url is not set' );

		if( !function_exists( 'file_get_html' ) )
			die( 'You need the simple html dom for this to work' );

		//TODO not setting ?language will return a php error. Check for that. Maybe even set default lang!

		// Caus' swimrankings.net write bad html we have to make it prettier...
		require_once 'htmlpurifier/library/HTMLPurifier.auto.php';
		$config = HTMLPurifier_Config::createDefault();
		$config->set('HTML.Trusted', true);
		$purifier = new HTMLPurifier( $config );
		$clean_html = $purifier->purify( file_get_contents( $this->url ) );

		$request = str_get_html( $clean_html );
		$this->get_html = $request;
	}

	function getYears() {
		$years = array();

		foreach( $this->get_html->find( 'select[name=season]' ) as $option ) {
			foreach( $option->children as $year ) {
				$years[] = $year->innertext;
			}
		}
		#&season=
		return $years;
	}

	function getYearsParam() {
		$years = array();

		foreach ( $this->getYears() as $year ) {
			$years[] = 'season=' . $year;
		}

		return $years;
	}

	function getPoolTypes() {
		$pools = array();

		foreach( $this->get_html->find( 'select[name=course]' ) as $option ) {
			foreach( $option->children as $key => $name ) {
				$years[$name->value] = $name->innertext;
			}
		}

		return $years;
	}

	function getPoolTypesParam() {
		$pools = array();

		foreach ( $this->getPoolTypes() as $kpool => $pool ) {
			$pools[] = 'course=' . $kpool;
		}

		return $pools;
	}

	function getGenders() {
		return array( 'Male' => 1, 'Female' => 2 );
	}

	function getGendersParam() {
		$genders = array();

		foreach ( $this->getGenders() as $gender ) {
			$genders[] = 'gender=' . $gender;
		}

		return $genders;
	}

	function getStyles() {
		$styles = array();

		foreach( $this->get_html->find( 'table.rankingList td[class=swimstyle] a' ) as $style ) {
			$link = htmlspecialchars_decode( $style->href );
			parse_str( $link, $args );
			$link = $args['rankingClubId'];
			
			$styles[$link] = $style->innertext;
		}

		return $styles;
	}

	function getNationals() {
		return array(
			'DEN',
			'FAR'
		);
	}

	function parse() {
		$first_place = 1;
		$first_place_fgh = $this->get_html->find( 'input[name=firstPlace]', 0 )->attr['value'];
		$swimmers = array();
		
		while( true ) {
			$first_place += 25;
		
			foreach( $this->get_html->find( 'table.rankingList tr[class^=rankingList]' ) as $tr ) {
				if( !empty( $skip ) ) {
					static $skip_count = 0;
					static $count = 0;
		
					if( $skip_count < $skip ) {
						$skip_count++;
						continue;
					}
				}

				$swimmers[] = $this->handle_swimmer( $tr );
			}

			//TODO wow.. make this alot smarter
			$url = $this->url . '&firstPlace=' . $first_place;
			// Caus' swimrankings.net write bad html we have to make it prettier...
			require_once 'htmlpurifier/library/HTMLPurifier.auto.php';
			$config = HTMLPurifier_Config::createDefault();
			$config->set('HTML.Trusted', true);
			$purifier = new HTMLPurifier( $config );
			$clean_html = $purifier->purify( file_get_contents( $url ) );
			$request = str_get_html( $clean_html );
			$this->get_html = $request;
			$first_place_fgh = $this->get_html->find( 'input[name=firstPlace]', 0 )->attr['value'];
		
			if( $first_place != $first_place_fgh ) {
				$skip = $first_place - $first_place_fgh;
			}

			if( !empty( $skip_count ) ) {
				$this->swimmers = $swimmers;
				break;
			}
		}
	}

	function handle_swimmer( $row ) {
		if( !in_array( $row->children[2]->innertext, $this->getNationals() ) )
			return;

		$swimmer = array();

		$swimmer = $this->get_id( $row, $swimmer );
		$swimmer = $this->get_name( $row, $swimmer );
		$swimmer = $this->get_year( $row, $swimmer );
		$swimmer = $this->get_national( $row, $swimmer );
		$swimmer = $this->get_club( $row, $swimmer );
		$swimmer = $this->get_time( $row, $swimmer );
		$swimmer = $this->get_finascore( $row, $swimmer );
		$swimmer = $this->get_rank_europe( $row, $swimmer );
		$swimmer = $this->get_rank_national( $row, $swimmer );
		$swimmer = $this->get_date( $row, $swimmer );
		$swimmer = $this->get_event( $row, $swimmer );
		$swimmer = $this->get_info( $row, $swimmer );

		return $swimmer;
	}

	function get_id( $row, $swimmer ) {
		$link = $row->children[0]->children[0]->href;
		$link_split = explode( '=', $link );
		$id = end( $link_split );

		$swimmer['aid'] = $id;

		return $swimmer;
	}

	function get_name( $row, $swimmer ) {
		$name = $this->pretty_name( $row->children[0]->children[0]->innertext );
		$name_split = explode( ',', $name );

		$swimmer['last_name'] = current( $name_split );
		$swimmer['first_name'] = end( $name_split );

		return $swimmer;
	}

	function pretty_name( $name ) {
		return ucwords( strtolower( $name ) );
	}

	function get_year( $row, $swimmer ) {
		$swimmer['year'] = $row->children[1]->innertext;

		return $swimmer;
	}
	function get_national( $row, $swimmer ) {
		$swimmer['national'] = $row->children[2]->innertext;

		return $swimmer;
	}
	function get_club( $row, $swimmer ) {
		$swimmer['club'] = $row->children[3]->innertext;

		return $swimmer;
	}
	function get_time( $row, $swimmer ) {
		$swimmer['time'] = $row->children[4]->children[0]->innertext;

		$link = $row->children[4]->children[0]->href;
		$link_split = explode( '=', $link );
		$id = end( $link_split );
		$swimmer['tid'] = $id;

		return $swimmer;
	}
	function get_finascore( $row, $swimmer ) {
		$swimmer['fina'] = $row->children[5]->innertext;

		return $swimmer;
	}
	function get_rank_europe( $row, $swimmer ) {
		$rank = $row->children[7]->innertext;

		if( empty( $rank ) || $rank == '-' )
			'301+';
		else
			$swimmer['rank_eu'] = $rank;

		return $swimmer;
	}
	function get_rank_national( $row, $swimmer ) {
		$swimmer['rank_national'] = (int) $row->children[8]->children[0]->innertext;

		return $swimmer;
	}
	function get_date( $row, $swimmer ) {
		$swimmer['date'] = html_entity_decode( $row->children[9]->innertext );

		return $swimmer;		
	}

	function get_event( $row, $swimmer ) {
		$swimmer['city'] = html_entity_decode( $row->children[10]->children[0]->innertext );

		return $swimmer;
	}

	function get_info( $row, $swimmer ) {
		$meet = htmlspecialchars_decode( $row->children[10]->children[0]->href );
		parse_str( $meet, $args );

		// Sid and gender prolly always the same
		$swimmer['eid'] = $args['meetId'];
		$swimmer['sid'] = $args['styleId'];
		$swimmer['gender'] = $args['gender'];

		return $swimmer;
	}

	function get_swimmers() {
		return $this->swimmers;
	}
}

