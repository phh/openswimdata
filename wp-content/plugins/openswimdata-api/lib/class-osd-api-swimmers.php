<?php

class OSD_API_Swimmers extends WP_JSON_CustomPostType {
	protected $base = '/swimmers';
	protected $type = 'swimmer';

	function register_routes( $routes ) {
		$routes = parent::registerRoutes( $routes );

		return $routes;
	}

	function prepare_post( $post, $context ) {
		$_post = array();

		// Post Data
		$_post['swimmer_id'] = (int) $post['ID'];
		$_post['swimmer'] = get_the_title( $post['ID'] );
		$_post['first_name'] = get_post_meta( $post['ID'], 'first_name', true );
		$_post['last_name'] = get_post_meta( $post['ID'], 'last_name', true );
		$_post['year'] = $this->term_data( $post['ID'], 'year' );
		$_post['club'] = $this->club( $post );
		$_post['clubdata'] = $this->club_data( $post );
		$_post['gender'] = $this->term_data( $post['ID'], 'gender' );
		$_post['national'] = $this->term_data( $post['ID'], 'national' );
		$_post['slug'] = $post['post_name'];
		#?filter[post_status]=draft&filter[s]=foo

		return $_post;
	}

	function club( $post ) {
		$club_data = get_post_meta( $post['ID'], 'club_data', true );

		if( empty( $club_data ) ) {
			return '';
		}

		reset( $club_data );
		return key( $club_data);
	}

	function club_data( $post ) {
		$club_data = get_post_meta( $post['ID'], 'club_data', true );

		if( empty( $club_data ) ) {
			return '';
		}

		$clubs = array();
		foreach( $club_data as $club => $data ) {
			$clubs[$club]['first'] = date( 'd-m-Y', $data['first'] );
			$clubs[$club]['last'] = date( 'd-m-Y', $data['last'] );
		}

		return $clubs;
	}

	function term_data( $post_id, $term ) {
		$terms = wp_get_post_terms( $post_id, $term, array( 'fields' => 'names' ) );

		if( empty( $terms ) ) {
			return '';
		}

		return current( $terms );
	}
}
