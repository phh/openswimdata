<?php

class OSD_API_Taxonomies extends WP_JSON_Taxonomies {
	function __construct() {
		global $wp_json_taxonomies;

		remove_filter( 'json_prepare_post', array( $wp_json_taxonomies, 'add_term_data' ), 10, 3 );
		add_filter( 'json_prepare_post', array( &$this, 'add_term_data' ), 10, 3 );
	}

	function add_term_data( $data, $post, $context ) {
		$data = parent::add_term_data( $data, $post, $context );
		$terms = array();
		ksort( $data['terms'] );

		foreach( $data['terms'] as $name => $term ) {
			$term = current( $term );
			$data[$name] = $term['name'];
		}

		$meta = $data['meta'];
		unset( $data['meta'] );
		unset( $data['terms'] );
		$data['meta'] = $meta;

		return $data;
	}
}

