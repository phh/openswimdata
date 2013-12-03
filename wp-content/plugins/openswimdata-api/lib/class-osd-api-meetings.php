<?php

class OSD_API_Meetings extends WP_JSON_CustomPostType {
	protected $base = '/meetings';
	protected $type = 'meeting';

	function register_routes( $routes ) {
		$routes = parent::registerRoutes( $routes );

		return $routes;
	}
}
