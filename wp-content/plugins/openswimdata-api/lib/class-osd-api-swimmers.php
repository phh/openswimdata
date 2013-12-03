<?php

class OSD_API_Swimmers extends WP_JSON_CustomPostType {
	protected $base = '/swimmers';
	protected $type = 'swimmer';

	function register_routes( $routes ) {
		$routes = parent::registerRoutes( $routes );

		return $routes;
	}
}
