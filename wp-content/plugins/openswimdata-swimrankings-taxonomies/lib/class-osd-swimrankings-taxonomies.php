<?php
/**
 * This object will include all stuff from the danish rankingslist on swimrankings.net.
 * It will convert genders, pools, seasons, distances and styles into WordPress taxonomies.
 */
class OSD_Swimrankings_Taxonomies {
	var $swimstyles = array();
	var $html = '';
	var $taxonomies = array( 'gender', 'pool', 'season', 'distance', 'style' );
	var $dropdowns = array( 'gender' => 'gender', 'pool' => 'course', 'season' => 'season' );

	const RANK_DEN = 'http://www.swimrankings.net/index.php?page=rankingDetail&club=DEN&language=us';

	function __construct() {
		require_once 'simple_html_dom.php';

		$this->request();
		$this->set_taxonomies();
	}

	function request() {
		if( !function_exists( 'file_get_html' ) )
			die( 'You need the simple html dom for this to work' );

		// swimrankings.net write bad html so we gotta make it prettier...
		$remote = wp_remote_get( $this::RANK_DEN );
		$dom = new DOMDocument;
		@$dom->loadHTML( $remote['body'] );
		$clean_html = $dom->saveHTML();

		$request = str_get_html( $clean_html );
		$this->html = $request;
	}

	function set_taxonomies() {
		foreach( $this->taxonomies as $taxonomy ) {
			$terms_args = array( 'hide_empty' => false );
			$terms = get_terms( $taxonomy, $terms_args );
			$term_meta = array();

			foreach( $terms as $k => $term ) {
				$terms[$k]->$taxonomy = OSD_Taxonomies_Metaboxes::get_term_meta( $taxonomy, $term->term_id );
				$term_meta[] = $terms[$k]->$taxonomy;
			}

			foreach( $this->get_terms( $taxonomy ) as $key => $value ) {
				if( !in_array( $key, $term_meta ) ) {
					$new_term = wp_insert_term( $value, $taxonomy );

					if( !is_wp_error( $new_term ) ) {
						add_option( OSD_Taxonomies_Metaboxes::name_term_meta( $taxonomy, $new_term['term_id'] ), $key );
					}
				}
			}
		}
	}


	/* Getters * * * * * * * * * * * * * * * * * * */

	function get_dropdown( $taxonomy ) {
		$taxonomy = $this->dropdowns[$taxonomy];
		$items = array();

		foreach( $this->html->find( 'select[name=' . $taxonomy . ']' ) as $option ) {
			foreach( $option->children as $key => $name ) {
				$items[$name->value] = $name->innertext;
			}
		}

		return $items;
	}

	function get_swimstyles() {
		if( !empty( $this->swimstyles ) )
			return $this->swimstyles;

		$swimstyles = array();

		foreach( $this->html->find( 'table.rankingList td[class=swimstyle] a' ) as $swimstyle ) {
			$swimstyles[] = $swimstyle->innertext;
		}

		$this->swimstyles = $swimstyles;

		return $swimstyles;
	}

	function get_distances() {
		$distances = array();

		foreach( $this->get_swimstyles() as $swimstyle ) {
			$distance = $this->get_swimstyle_distance( $swimstyle );

			if( in_array( $distance, $distances ) ) {
				continue;
			}

			$distances[$distance] = $distance;
		}

		return $distances;
	}

	function get_styles() {
		$styles = array();

		foreach( $this->get_swimstyles() as $swimstyle ) {
			$style = $this->get_swimstyle_style( $swimstyle );

			if( in_array( $style, $styles ) ) {
				continue;
			}

			$styles[$style] = $style;
		}

		return $styles;
	}

	function get_terms( $taxonomy ) {
		if( array_key_exists( $taxonomy, $this->dropdowns ) ) {
			return $this->get_dropdown( $taxonomy );
		}

		$method_name = 'get_' . $taxonomy . 's';

		if( !method_exists( $this, $method_name ) )
			die( $method_name . ' method does not exist' );

		return call_user_func( array( &$this, $method_name ) );
	}


	/* Helpers * * * * * * * * * * * * * * * * * * */

	function get_swimstyle_distance( $swimstyle ) {
		return intval( $swimstyle );
	}

	function get_swimstyle_style( $swimstyle ) {
		$swimstyle = explode( ' ', $swimstyle );
		array_shift( $swimstyle );
		$distance = implode( ' ', $swimstyle );

		return $distance;
	}
}

