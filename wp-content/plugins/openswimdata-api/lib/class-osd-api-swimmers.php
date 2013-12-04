<?php

class OSD_API_Swimmers extends OSD_API {
	protected $base = '/swimmers';
	protected $type = 'swimmer';

	function __construct( $server ) {
		parent::__construct( $server );

		add_filter( 'json_prepare_meta', array( &$this, 'json_prepare_meta_club_data' ) );
	}

	function register_routes( $routes ) {
		$routes = parent::registerRoutes( $routes );

		$routes[$this->base . '/results/(?P<id>\d+)' ] = array(
			array( array( $this, 'connected_get_results' ), WP_JSON_Server::READABLE )
		);

		return $routes;
	}

	function connected_get_results( $id, $context = 'view', $type = 'swimmers_results' ) {
		return parent::connected_get_results( $id, $context, $type );
	}

	function json_prepare_meta_club_data( $metas ) {
		if( array_key_exists( 'club_data', $metas ) ) {
			foreach( $metas['club_data'] as $key => $clubs ) {
				$clubs = maybe_unserialize( $clubs );

				foreach( $clubs as $club => $data ) {
					$clubs[$club] = array(
						'first' => date( 'd-m-Y', $data['first'] ),
						'last' => date( 'd-m-Y', $data['last'] )
					);
				}

				$metas['club_data'][$key] = $clubs;
			}
		}

		return $metas;
	}
}

