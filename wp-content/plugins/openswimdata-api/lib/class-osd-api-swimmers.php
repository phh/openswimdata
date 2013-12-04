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

	function json_prepare_meta_club_data( $metas ) {
		if( array_key_exists( 'club_data', $metas ) ) {
			$this->test_data();

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
