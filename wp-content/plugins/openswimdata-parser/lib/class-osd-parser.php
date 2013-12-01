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
	var $posts = array();
	var $data = array();

	function __construct() {
		require_once 'simple_html_dom.php';
	}

	function parse_urls() {
		$posts = get_posts( array(
			'post_type' => 'tmp',
			'post_status' => 'draft',
			'posts_per_page' => -1
		) );

		foreach( $tmps as $tmp ) {
			wp_schedule_single_event( OSD_Crawler::cron_time(), 'osd_parser_url', array( $tmp->ID ) );
		}
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
		$this->set_post_types( array(
			'swimmer',
			'result',
			'meeting'
		) );

		$count = 0;
		end( $this->tmp->data );
		$last = key( $this->tmp->data );
		reset( $this->tmp->data );

		foreach( $this->tmp->data as $key => $row ) {
			$count++;

			$this->html = str_get_html( $row );
			$this->handle_row();
			$this->set_row_terms();
			$this->save_swimmer();
			$this->save_result();
			$this->save_meeting();
			$this->save_p2p();

			if( $key == $last ) {
				// If its the last entry make sure the data is not getting parsed anymore until there is new data.
				wp_update_post( array( 'ID' => $this->tmp->ID, 'post_status' => 'publish' ) );

				return;
			} elseif( $count == 300 ) {
				// If we reach the count 300, split the rest up in another array.
				$rest = array_slice( $this->tmp->data, $key+1 );
				wp_schedule_single_event( strtotime( '+2 minutes' ), 'osd_parser_url', array( $this->tmp->ID, $rest ) );

				return;
			}
		}
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

	function set_rest_data( $rest ) {
		$this->data = $rest;
	}

	function set_taxonomies( $taxonomies, $property ) {
		foreach( $taxonomies as $taxonomy => $taxonomy_sr ) {
			$this->set_taxonomy( $taxonomy_sr, $taxonomy, $property );
		}
	}

	function set_taxonomy( $taxonomy_sr, $taxonomy, $property ) {
		$this->terms[$property][$taxonomy] = ''; // Make sure we reset this.
		$terms_args = array( 'hide_empty' => false );
		$terms = get_terms( $taxonomy, $terms_args );

		foreach( $terms as $term ) {
			if( $this->$property->$taxonomy_sr == OSD_Taxonomies_Metaboxes::get_term_meta( $taxonomy, $term->term_id ) ) {
				// If the meta data exists just the one already created.
				$this->terms[$property][$taxonomy] = intval( $term->term_id );
			}
		}
		// If the meta was not found, create a new one.
		if( empty( $this->terms[$property][$taxonomy] ) ) {
			$term = wp_insert_term( $this->$property->$taxonomy, $taxonomy );
			$option = OSD_Taxonomies_Metaboxes::name_term_meta( $taxonomy, $term['term_id'] );

			add_option( $option, $this->$property->$taxonomy );

			$this->terms[$property][$taxonomy] = intval( $term['term_id'] );
		}
	}

	function set_post_types( $post_types ) {
		foreach( $post_types as $post_type ) {
			$this->set_post_type( $post_type );
		}
	}

	function set_post_type( $post_type ) {
		$this->posts[$post_type] = array();
		$posts_args = array( 'hide_empty' => false );
		$posts = get_posts( array( 'post_type' => $post_type, 'numberposts' => -1 ) );

		foreach( $posts as $post ) {
			$sr_id = get_post_meta( $post->ID, 'sr_id', true );
			$this->posts[$post_type][$sr_id] = $post->ID;
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
			'national' => 'national',
			'year' => 'year'
		 ), 'swimmer' );
		$this->set_taxonomies( array(
			'club' => 'club',
			'date' => 'date',
			'national' => 'national',
			'year' => 'year'
		 ), 'result' );
		$this->set_taxonomies( array(
			'city' => 'city',
			'event' => 'event'
		 ), 'meeting' );
	}

	function save_swimmer() {
		if( !array_key_exists( $this->swimmer->athleteid, $this->posts['swimmer'] ) ) {
			// If it's not there, create a new post

			$postarr = array(
				'post_title' => $this->swimmer_title(),
				'post_type' => 'swimmer',
				'post_status' => 'publish',
				'post_author' => 1
			);

			// Post
			$swimmer_id = wp_insert_post( $postarr );
			$this->set_clubdata( $swimmer_id );
			$this->set_taxonomies( array( 'club' => 'club' ), 'swimmer' );

			// Post Meta
			add_post_meta( $swimmer_id, 'first_name', $this->swimmer->firstname );
			add_post_meta( $swimmer_id, 'last_name', $this->swimmer->lastname );
			add_post_meta( $swimmer_id, 'club_data', $this->swimmer->clubdata );
			add_post_meta( $swimmer_id, 'sr_id', $this->swimmer->athleteid );

			// Terms
			wp_set_object_terms( $swimmer_id, array( $this->terms['swimmer']['club'] ), 'club' );
			wp_set_object_terms( $swimmer_id, array( $this->terms['tmp']['gender'] ), 'gender' );
			wp_set_object_terms( $swimmer_id, array( $this->terms['swimmer']['national'] ), 'national' );
			wp_set_object_terms( $swimmer_id, array( $this->terms['swimmer']['year'] ), 'year' );
		} else {
			// If exists, possible update club and clubdata.
			$swimmer_id = $this->posts['swimmer'][$this->swimmer->athleteid];
			$this->set_clubdata( $swimmer_id );
			$this->set_taxonomies( array( 'club' => 'club' ), 'swimmer' );

			$current_club = current( wp_get_post_terms( $swimmer_id, 'club' ) );
			if( $current_club->term_id != $this->terms['swimmer']['club'] ) {
				wp_remove_object_terms( $swimmer_id, array( intval( $current_club->term_id ) ), 'club' );
				wp_set_object_terms( $swimmer_id, array( $this->terms['swimmer']['club'] ), 'club' );
			}

			update_post_meta( $swimmer_id, 'club_data', $this->swimmer->clubdata );
		}

		$this->swimmer->post_id = $swimmer_id;
		$this->posts['swimmer'][$this->swimmer->athleteid] = $swimmer_id;
	}

	function save_result() {
		if( !array_key_exists( $this->result->result_id, $this->posts['result'] ) ) {
			// If it's not there, create a new post
			$postarr = array(
				'post_title' => $this->result->time,
				'post_type' => 'result',
				'post_status' => 'publish',
				'post_author' => 1
			);

			// Post
			$result_id = wp_insert_post($postarr);

			// Post Meta
			add_post_meta( $result_id, 'time', $this->result->time );
			add_post_meta( $result_id, 'splits', $this->result->splits );
			add_post_meta( $result_id, 'rank_nat', $this->result->rank_nat );
			add_post_meta( $result_id, 'rank_eu', $this->result->rank_eu );
			add_post_meta( $result_id, 'sr_id', $this->result->result_id );

			// Terms
			wp_set_object_terms( $result_id, array( $this->terms['result']['club'] ), 'club' );
			wp_set_object_terms( $result_id, array( $this->terms['result']['date'] ), 'date' );
			wp_set_object_terms( $result_id, array( $this->terms['tmp']['distance'] ), 'distance' );
			wp_set_object_terms( $result_id, array( $this->terms['result']['national'] ), 'national' );
			wp_set_object_terms( $result_id, array( $this->terms['tmp']['pool'] ), 'pool' );
			wp_set_object_terms( $result_id, array( $this->terms['tmp']['season'] ), 'season' );
			wp_set_object_terms( $result_id, array( $this->terms['tmp']['style'] ), 'style' );
			wp_set_object_terms( $result_id, array( $this->terms['result']['year'] ), 'year' );
		} else {
			// If exists, possible update everything exept sr_id.
			$result_id = $this->posts['result'][$this->result->result_id];

			// Post Meta
			update_post_meta( $result_id, 'time', $this->result->time );
			update_post_meta( $result_id, 'splits', $this->result->splits );
			update_post_meta( $result_id, 'rank_nat', $this->result->rank_nat );
			update_post_meta( $result_id, 'rank_eu', $this->result->rank_eu );

			// Terms
			$this->change_terms( $result_id, 'club', 'result' );
			$this->change_terms( $result_id, 'date', 'result' );
			$this->change_terms( $result_id, 'distance', 'tmp' );
			$this->change_terms( $result_id, 'national', 'result' );
			$this->change_terms( $result_id, 'pool', 'tmp' );
			$this->change_terms( $result_id, 'season', 'tmp' );
			$this->change_terms( $result_id, 'style', 'tmp' );
			$this->change_terms( $result_id, 'year', 'result' );
		}

		$this->result->post_id = $result_id;
		$this->posts['result'][$this->result->result_id] = $result_id;
	}

	function save_meeting() {
		static $count = 0;
		$count++;

		if( !array_key_exists( $this->meeting->meetid, $this->posts['meeting'] ) ) {
			// If it's not there, create a new post
			$postarr = array(
				'post_title' => $this->meeting_title(),
				'post_type' => 'meeting',
				'post_status' => 'publish',
				'post_author' => 1
			);

			// Post
			$meeting_id = wp_insert_post( $postarr );

			// Post Meta
			add_post_meta( $meeting_id, 'meeting', $this->meeting_title() );
			add_post_meta( $meeting_id, 'sr_id', $this->meeting->meetid );

			// Terms
			wp_set_object_terms( $meeting_id, array( $this->terms['meeting']['city'] ), 'city' );
			wp_set_object_terms( $meeting_id, array( $this->terms['meeting']['event'] ), 'event' );
		} else {
			// If exists, possible update everything exept sr_id.
			$meeting_id = $this->posts['meeting'][$this->meeting->meetid];

			// Terms
			$this->change_terms( $meeting_id, 'city', 'meeting' );
			$this->change_terms( $meeting_id, 'event', 'meeting' );
		}

		$this->meeting->post_id = $meeting_id;
		$this->posts['meeting'][$this->meeting->meetid] = $meeting_id;
	}

	function save_p2p() {
		p2p_type( 'swimmers_results' )->connect( $this->swimmer->post_id, $this->result->post_id );
		p2p_type( 'meetings_results' )->connect( $this->meeting->post_id, $this->result->post_id );
	}

	function change_terms( $id, $taxonomy, $property ) {
		$current = current( wp_get_post_terms( $id, $taxonomy ) );

		if( $current->term_id != $this->terms[$property][$taxonomy] ) {
			wp_remove_object_terms( $id, array( intval( $current->term_id ) ), $taxonomy );
			wp_set_object_terms( $id, array( $this->terms[$property][$taxonomy] ), $taxonomy );
		}
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
		if( empty( $this->data ) ) {
			$this->tmp->data = get_post_meta( $this->tmp->ID, 'tmp_data', true );
		} else {
			$this->tmp->data = $this->data;
		}
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
		$this->result->club = html_entity_decode( $td->innertext );
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
						$splits[$current]['turn'] = $split->innertext;
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
		$date = str_replace( '&nbsp;', ' ', $td->innertext );
		$date = strtotime( $date );
		$this->result->date = date( 'd-m', $date );
		$this->result->year = date( 'Y', $date );
		$this->result->date_raw = $date;
	}

	function set_city( $td ) {
		$this->meeting->city = html_entity_decode( str_replace( '&nbsp;', ' ', $td->innertext ) );
	}

	function set_event( $td ) {
		$this->meeting->event = html_entity_decode( str_replace( '&nbsp;', ' ', $td->title ) );
	}

	function set_meet_id( $td ) {
		$href = htmlspecialchars_decode( $td->href );
		parse_str( $href );

		$this->meeting->meetid = $meetId;
	}

	function set_clubdata( $swimmer_id = false ) {
		if( $swimmer_id ) {
			$clubdata = get_post_meta( $swimmer_id, 'club_data', true );
		}

		// Make sure there is an array to work with
		if( empty( $clubdata ) ) {
			$clubdata = array();
		}

		// If the there is nothing, make club array and add first/last = 0
		if( !array_key_exists( $this->result->club, $clubdata ) ) {
			$clubdata[$this->result->club] = array( 'first' => 0, 'last' => 0 );
		}

		// Check if lower than first or 0
		if( $this->result->date_raw < $clubdata[$this->result->club]['first'] || $clubdata[$this->result->club]['first'] == 0 ) {
			$clubdata[$this->result->club]['first'] = $this->result->date_raw;
		}

		// Check if higher than last
		if( $this->result->date_raw > $clubdata[$this->result->club]['last'] ) {
			$clubdata[$this->result->club]['last'] = $this->result->date_raw;
		}

		uasort( $clubdata, array( &$this, 'sort_by_last' ) );
		$this->swimmer->club = key( $clubdata );
		$this->swimmer->clubdata = $clubdata;
	}

	/**
	 * http://stackoverflow.com/questions/2699086/sort-multidimensional-array-by-value-2
	 */
	function sort_by_last( $a, $b ) {
		return $b['last'] - $a['last'];
	}

	function swimmer_title() {
		$out = $this->swimmer->firstname;
		$out .= ' ';
		$out .= $this->swimmer->lastname;

		return $out;
	}

	function meeting_title() {
		$out = $this->meeting->event;
		$out .= ': ';
		$out .= $this->meeting->city;

		return $out;
	}
}

