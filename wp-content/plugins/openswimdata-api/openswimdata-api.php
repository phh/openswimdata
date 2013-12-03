<?php
/*
Plugin Name: OpenSwimData API
Description: OpenSwimData part: API. Deliver the data in form of a JSON REST API.
Version: 1.0
Author: Patrick Hesselberg
*/

function osd_api() {
	global $osd_api, $wp_json_server;

	$osd_api = new OSD_API($wp_json_server);
	add_filter( 'json_endpoints', array( $osd_api, 'register_routes' ) );
}
add_action( 'wp_json_server_before_serve', 'osd_api' );

class OSD_API extends WP_JSON_CustomPostType {
	protected $base = '/swimmers';
	protected $type = 'swimmer';

	function register_routes( $routes ) {
		$routes = parent::registerRoutes( $routes );

		return $routes;
	}
}
