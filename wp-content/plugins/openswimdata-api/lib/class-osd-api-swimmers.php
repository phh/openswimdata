<?php

class OSD_API_Swimmers extends OSD_API {
	protected $base = '/swimmers';
	protected $type = 'swimmer';

	function register_routes( $routes ) {
		$routes = parent::registerRoutes( $routes );

		return $routes;
	}

	function club_data( $post ) {
		$club_data = $this->post_meta( $post['ID'], 'club_data' );

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
}
