<?php

class OSD_API_Taxonomies extends WP_JSON_Taxonomies {
	function __construct() {
		global $wp_json_taxonomies;

		remove_filter( 'json_endpoints',      array( $wp_json_taxonomies, 'registerRoutes' ), 2 );
		remove_filter( 'json_post_type_data', array( $wp_json_taxonomies, 'add_taxonomy_data' ), 10, 2 );
		remove_filter( 'json_prepare_post',   array( $wp_json_taxonomies, 'add_term_data' ), 10, 3 );

		#add_filter( 'json_endpoints',      array( &$this, 'registerRoutes' ), 2 );
		#add_filter( 'json_post_type_data', array( &$this, 'add_taxonomy_data' ), 10, 2 );
		#add_filter( 'json_prepare_post',   array( &$this, 'add_term_data' ), 10, 3 );
	}
}
