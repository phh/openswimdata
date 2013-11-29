<?php
/**
 * Main class for the OSD Parser
 */

class OSD_Parser {
	var $tmp;
	var $html;
	var $swimmer;
	var $result;
	var $meeting;
	var $terms = array();

	function __construct() {
		require_once 'simple_html_dom.php';
	}

	function set_tmp( $id ) {
		$tmp = get_post( $id );
		$this->tmp = $tmp;
		$this->get_tmp( $id );
	}

	function get_tmp() {
		$this->set_base();
		$this->set_style();
		$this->set_distance();
		$this->set_data();
	}

	function parse() {
		if( !function_exists( 'file_get_html' ) ) {
			die( 'You need the simple html dom for this to work' );
		}

		$this->set_taxonomies( array(
			'distance' => 'distance',
			'gender' => 'gender',
			'pool' => 'course',
			'season' => 'season',
			'style' => 'style'
		), 'tmp' );

			$this->swimmer->post_id = 2659;
			$this->result->post_id = 3709;
			$this->meeting->post_id = 3866;

		foreach( $this->tmp->data as $row ) {
			$this->html = str_get_html( $row );
			$this->handle_row();
			$this->set_row_terms();
			#$this->save_swimmer();
			#$this->save_result();
			#$this->save_meeting();
			#$this->save_p2p();
		}
	}

	function set_taxonomies( $taxonomies, $property ) {return;
		foreach( $taxonomies as $taxonomy => $taxonomy_sr ) {
			$this->set_taxonomy( $taxonomy_sr, $taxonomy, $property );
		}
	}

	function set_taxonomy( $taxonomy_sr, $taxonomy, $property ) {
		$this->terms[$taxonomy] = ''; // Make sure we reset this.
		$terms_args = array( 'hide_empty' => false );
		$terms = get_terms( $taxonomy, $terms_args );

		foreach( $terms as $term ) {
			if( $this->$property->$taxonomy_sr == OSD_Taxonomies_Metaboxes::get_term_meta( $taxonomy, $term->term_id ) ) {
				// If the meta data exists just the one already created.
				$this->terms[$taxonomy] = intval( $term->term_id );
			}
		}
		// If the meta was not found, create a new one.
		if( empty( $this->terms[$taxonomy] ) ) {
			$term = wp_insert_term( $this->$property->$taxonomy, $taxonomy );
			$option = OSD_Taxonomies_Metaboxes::name_term_meta( $taxonomy, $term['term_id'] );

			add_option( $option, $this->$property->$taxonomy );

			$this->terms[$taxonomy] = intval( $term['term_id'] );
		}
	}

	function handle_row() {
		// Rows
		$row1 = $this->html->find( 'td', 0 )->children[0];
		$row2 = $this->html->find( 'td', 1 );
		$row3 = $this->html->find( 'td', 2 );
		$row4 = $this->html->find( 'td', 3 );
		$row5 = $this->html->find( 'td', 4 )->children[0];
		$row6 = $this->html->find( 'td', 7 );
		$row7 = $this->html->find( 'td', 8 )->children[0];
		$row8 = $this->html->find( 'td', 9 );
		$row9 = $this->html->find( 'td', 10 )->children[0];

		// Row Functions
		$this->set_id( $row1 );
		$this->set_name( $row1 );
		$this->set_year_born( $row2 );
		$this->set_national( $row3 );
		$this->set_club( $row4 );
		$this->set_time( $row5 );
		$this->set_split_times( $row5 );
		$this->set_result_id( $row5 );
		$this->set_rank_eu( $row6 );
		$this->set_rank_nat( $row7 );
		$this->set_date( $row8 );
		$this->set_city( $row9 );
		$this->set_event( $row9 );
		$this->set_meet_id( $row9 );
	}

	function set_row_terms() {
		$this->set_taxonomies( array(
			'club' => 'club',
			'national' => 'national',
			'year' => 'year'
		 ), 'swimmer' );
		$this->set_taxonomies( array(
			'club' => 'club',
			'date' => 'date',
			'year' => 'year'
		 ), 'result' );
		$this->set_taxonomies( array(
			'city' => 'city',
			'event' => 'event'
		 ), 'meeting' );
	}

	function save_swimmer() {
		$postarr = array(
			'post_title' => $this->swimmer_title(),
			'post_type' => 'swimmer',
			'post_status' => 'publish',
			'post_author' => 1,
			'tax_input' => array(
				'club' => array(
					$this->terms['club']
				),
				'gender' => array(
					$this->terms['gender']
				),
				'national' => array(
					$this->terms['national']
				),
				'year' => array(
					$this->terms['year']
				)
			)
		);

		$swimmer_id = wp_insert_post($postarr);
		add_post_meta( $swimmer_id, 'first_name', $this->swimmer->firstname );
		add_post_meta( $swimmer_id, 'last_name', $this->swimmer->lastname );
		add_post_meta( $swimmer_id, 'sr_id', $this->swimmer->athleteid );

		$this->swimmer->post_id = $swimmer_id;
	}

	function save_result() {
		$postarr = array(
			'post_title' => $this->result->time,
			'post_type' => 'result',
			'post_status' => 'publish',
			'post_author' => 1,
			'tax_input' => array(
				'club' => array(
					$this->terms['club']
				),
				'date' => array(
					$this->terms['date']
				),
				'distance' => array(
					$this->terms['distance']
				),
				'national' => array(
					$this->terms['national']
				),
				'pool' => array(
					$this->terms['pool']
				),
				'season' => array(
					$this->terms['season']
				),
				'style' => array(
					$this->terms['style']
				),
				'year' => array(
					$this->terms['year']
				)
			)
		);

		$result_id = wp_insert_post($postarr);
		add_post_meta( $result_id, 'time', $this->result->time );
		add_post_meta( $result_id, 'splits', $this->result->splits );
		add_post_meta( $result_id, 'rank_nat', $this->result->rank_nat );
		add_post_meta( $result_id, 'rank_eu', $this->result->rank_eu );
		add_post_meta( $result_id, 'sr_id', $this->result->result_id );

		$this->result->post_id = $result_id;
	}

	function save_meeting() {
		$postarr = array(
			'post_title' => $this->meeting_title(),
			'post_type' => 'meeting',
			'post_status' => 'publish',
			'post_author' => 1,
			'tax_input' => array(
				'city' => array(
					$this->terms['city']
				),
				'event' => array(
					$this->terms['event']
				)
			)
		);

		$meeting_id = wp_insert_post($postarr);
		add_post_meta( $meeting_id, 'meeting', $this->meeting_title() );
		add_post_meta( $meeting_id, 'sr_id', $this->meeting->meetid );

		$this->meeting->post_id = $meeting_id;
	}

	function save_p2p() {
		p2p_type( 'swimmers_results' )->connect( $this->swimmer->post_id, $this->result->post_id );
		p2p_type( 'meetings_results' )->connect( $this->result->post_id, $this->meeting->post_id );
	}

	function set_base() {
		$base = get_post_meta( $this->tmp->ID, 'tmp_base', true );

		parse_str( $base );

		$this->tmp->gender = $gender;
		$this->tmp->course = $course;
		$this->tmp->season = $season;
	}

	function set_style() {
		$this->tmp->style = get_post_meta( $this->tmp->ID, 'tmp_style', true );
	}

	function set_distance() {
		$this->tmp->distance = get_post_meta( $this->tmp->ID, 'tmp_distance', true );
	}

	function set_data() {
		$this->tmp->data = get_post_meta( $this->tmp->ID, 'tmp_data', true );
	}

	function set_id( $td ) {
		$href = htmlspecialchars_decode( $td->href );
		parse_str( $href );

		$this->swimmer->athleteid = $athleteId;
	}

	function set_name( $td ) {
		$name = ucwords( strtolower( $td->innertext ) );
		$name = explode( ',', $name );

		$this->swimmer->lastname = trim( $name[0] );
		$this->swimmer->firstname = trim( $name[1] );
	}

	function set_year_born( $td ) {
		$this->swimmer->year = $td->innertext;
	}

	function set_national( $td ) {
		$this->swimmer->national = $td->innertext;
		$this->result->national = $td->innertext;
	}

	function set_club( $td ) {
		$this->swimmer->club = $td->innertext;
		$this->result->club = $td->innertext;
	}

	function set_time( $td ) {
		$this->result->time = $td->innertext;
	}

	function set_split_times( $td ) {
		$splits = array();

		if( !empty( $td->attr['onmouseover'] ) ) {
			$mouseover = htmlspecialchars_decode( $td->attr['onmouseover'] );
			$html = str_get_html( $mouseover );

			foreach( $html->find( 'tr') as $tr ) {
				foreach( $tr->find( 'td[class!=splitsep]' ) as $split ) {
					if( $split->attr['class'] == 'split0' ) {
						$current = intval( $split->innertext );
						$splits[$current] = array();
					} elseif( $split->attr['class'] == 'split1' ) {
						$splits[$current]['current'] = $split->innertext;
					} else {
						$splits[$current]['split'] = $split->innertext;
					}
				}
			}
		}

		$this->result->splits = $splits;
	}

	function set_result_id( $td ) {
		$href = htmlspecialchars_decode( $td->href );
		parse_str( $href );

		$this->result->result_id = $id;
	}

	function set_rank_eu( $td ) {
		if( empty( $td->innertext ) || $td->innertext == '-' ) {
			$this->result->rank_eu = '';
		} else {
			$this->result->rank_eu = intval( $td->innertext );
		}
	}

	function set_rank_nat( $td ) {
		$this->result->rank_nat = intval( $td->innertext );
	}

	function set_date( $td ) {
		$date = iconv( 'UTF-8', 'ASCII//TRANSLIT', $td->innertext);
		$date = strtotime( $date );
		$this->result->date = date( 'd-m', $date );
		$this->result->year = date( 'Y', $date );
	}

	function set_city( $td ) {
		$this->meeting->city = $td->innertext;
	}

	function set_event( $td ) {
		$this->meeting->event = $td->title;
	}

	function set_meet_id( $td ) {
		$href = htmlspecialchars_decode( $td->href );
		parse_str( $href );

		$this->meeting->meetid = $meetId;
	}

	function swimmer_title() {
		$out = $this->swimmer->firstname;
		$out .= ' ';
		$out .= $this->swimmer->lastname;

		return $out;
	}

	function meeting_title() {
		$out = $this->meeting->event;
		$out .= ' - ';
		$out .= $this->meeting->city;

		return $out;
	}
}

