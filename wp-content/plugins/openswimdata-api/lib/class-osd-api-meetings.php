<?php

class OSD_API_Meetings extends OSD_API {
	protected $base = '/meetings';
	protected $type = 'meeting';

	function register_routes( $routes ) {
		$routes = parent::registerRoutes( $routes );

		return $routes;
	}
}
