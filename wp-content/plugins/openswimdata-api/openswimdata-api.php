<?php
/*
Plugin Name: OpenSwimData API
Description: OpenSwimData part: API. Deliver the data in form of a JSON REST API.
Version: 1.0
Author: Patrick Hesselberg
*/

include_once( dirname( __FILE__ ) . '/lib/class-osd-api.php' );
include_once( dirname( __FILE__ ) . '/lib/class-osd-api-swimmers.php' );
include_once( dirname( __FILE__ ) . '/lib/class-osd-api-results.php' );
include_once( dirname( __FILE__ ) . '/lib/class-osd-api-meetings.php' );
include_once( dirname( __FILE__ ) . '/lib/class-osd-api-taxonomies.php' );

function osd_api() {
	global $wp_json_server;

	$swimmers = new OSD_API_Swimmers( $wp_json_server );
	$results = new OSD_API_Results( $wp_json_server );
	$meetings = new OSD_API_Meetings( $wp_json_server );
	$taxonomies = new OSD_API_Taxonomies;

	add_filter( 'json_endpoints', array( $swimmers, 'register_routes' ) );
	add_filter( 'json_endpoints', array( $results, 'register_routes' ) );
	add_filter( 'json_endpoints', array( $meetings, 'register_routes' ) );
}
add_action( 'wp_json_server_before_serve', 'osd_api' );

