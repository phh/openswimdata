<?php

class OSD_API_Results extends WP_JSON_CustomPostType {
	protected $base = '/results';
	protected $type = 'result';

	function register_routes( $routes ) {
		$routes = parent::registerRoutes( $routes );

		return $routes;
	}
}
