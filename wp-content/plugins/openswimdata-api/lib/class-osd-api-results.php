<?php

class OSD_API_Results extends OSD_API {
	protected $base = '/results';
	protected $type = 'result';

	function __construct( $server ) {
		parent::__construct( $server );

		add_filter( 'osd_api_' . $this->type . '_links', array( &$this, 'osd_api_result_link' ), 10, 2 );
		add_filter( 'json_prepare_meta', array( &$this, 'json_prepare_meta_splits' ) );
	}

	function register_routes( $routes ) {
		$routes = parent::registerRoutes( $routes );

		return $routes;
	}

	function osd_api_result_link( $links, $post_id ) {
		$links['swimmer'] = $this->get_related( 'swimmers_results', $post_id, 'swimmers/' );
		$links['swimmer_results'] = $this->get_related( 'swimmers_results', $post_id, 'swimmers/results/' );
		$links['meeting'] = $this->get_related( 'meetings_results', $post_id, 'meetings/' );
		$links['meeting_results'] = $this->get_related( 'meetings_results', $post_id, 'meetings/results/' );

		return $links;
	}

	function json_prepare_meta_splits( $metas ) {
		if( array_key_exists( 'splits', $metas ) ) {
			foreach( $metas['splits'] as $key => $split ) {
				$metas['splits'][$key] = maybe_unserialize( $split );
			}
		}

		return $metas;
	}
}
