<?php

class OSD_API extends WP_JSON_CustomPostType {
	function __construct( $server ) {
		parent::__construct( $server );

		add_filter( 'json_prepare_meta', array( &$this, 'json_prepare_meta_sr_id' ) );
	}

	function registerRoutes( $routes ) {
		$routes[ $this->base ] = array(
			array( array( $this, 'getPosts' ), WP_JSON_Server::READABLE )
		);

		$routes[ $this->base . '/(?P<id>\d+)' ] = array(
			array( array( $this, 'getPost' ),    WP_JSON_Server::READABLE )
		);
		return $routes;
	}

	function prepare_post( $post, $context ) {
		$type = $post['post_type'] != $this->type ? $post['post_type'] : $this->type;
		// So bah and dirty. But it works. Split em up tomorrow..
		$base = $post['post_type'] != $this->type ? '/' . $post['post_type'] . 's' : false;
		$_post = array();

		$_post[ $type . '_id'] = (int) $post['ID'];
		$_post['title'] = get_the_title( $post['ID'] );
		$_post['slug'] = $post['post_name'];

		$_post = array_merge( $_post, $this->prepare_meta( $post['ID'] ) );

		$_post['meta']['links'] = $this->meta_link( $post['ID'], $base );

		return apply_filters( 'json_prepare_post', $_post, $post, $context );
	}

	function json_prepare_meta_sr_id( $metas ) {
		if( array_key_exists( 'sr_id', $metas ) ) {
			unset( $metas['sr_id'] );
		}

		return $metas;
	}

	function connected_get_results( $id, $context = 'view', $type = '' ) {
		$post = parent::getPost( $id, $context = 'view' );
		$post['results'] = array();
		$related_posts = $this->get_related_posts( $type, $id );

		foreach( $related_posts as $related ) {
			$post['results'][] = $this->prepare_related( $related );
		}

		return $post;
	}

	function prepare_related( $related ) {
		if( !is_object( $related ) ) {
			return false;
		}

		$post = get_post( $related->ID, ARRAY_A );
		$_post = $this->prepare_post( $post, 'view' );

		return $_post;
	}

	function prepare_meta( $post_id ) {
		$metas = array();
		$meta = parent::prepare_meta( $post_id );

		foreach( $meta as $name => $field ) {
			$metas[$name] = current( $field );
		}

		return $metas;
	}


	function meta_link( $post_id, $base = false ) {
		$type = get_post_type( $post_id );
		$base = $base ? $base : $this->base;

		$meta_links = array(
			'self'       => json_url( $base . '/' . $post_id ),
			'collection' => json_url( $base )
		);

		$meta_links = array_merge( $meta_links, apply_filters( 'osd_api_' . $this->type . '_links', array(), $post_id ) );

		return $meta_links;
	}

	function get_related_posts( $type, $post_id ) {
		$connected = get_posts( array(
			'connected_type' => $type,
			'connected_items' => $post_id,
			'nopaging' => true,
			'suppress_filters' => false
		) );

		if( empty( $connected ) ) {
			return array();
		}

		return $connected;
	}

	function get_related( $type, $post_id, $base ) {
		$connected = get_posts( array(
			'connected_type' => $type,
			'connected_items' => $post_id,
			'nopaging' => true,
			'suppress_filters' => false
		) );

		if( empty( $connected ) ) {
			return false;
		}

		$item = json_url( $base . current( $connected )->ID );

		return $item;
	}

	function test_data() {
		header("Content-Type:text/html");
		#?filter[post_status]=draft&filter[s]=foo
	}
}

