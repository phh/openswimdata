<?php

class OSD_API_Meetings extends OSD_API {
	protected $base = '/meetings';
	protected $type = 'meeting';

	function register_routes( $routes ) {
		$routes = parent::registerRoutes( $routes );

		$routes[$this->base . '/results/(?P<id>\d+)' ] = array(
			array( array( $this, 'connected_get_results' ), WP_JSON_Server::READABLE )
		);

		return $routes;
	}

	function connected_get_results( $id, $context = 'view', $type = 'meetings_results' ) {
		return parent::connected_get_results( $id, $context, $type );
	}
}

