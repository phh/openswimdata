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
		$_post = array();

		$_post[ $this->type . '_id'] = (int) $post['ID'];
		$_post['title'] = get_the_title( $post['ID'] );
		$_post['slug'] = $post['post_name'];

		$_post = array_merge( $_post, $this->prepare_meta( $post['ID'] ) );

		$_post['meta']['links'] = $this->meta_link( $post['ID'] );

		return apply_filters( 'json_prepare_post', $_post, $post, $context );

		$_post['pool'] = $this->term_data( $post['ID'], 'pool' );
		$_post['distance'] = $this->term_data( $post['ID'], 'distance' );
		$_post['style'] = $this->term_data( $post['ID'], 'style' );
		$_post['result'] = $this->post_meta( $post['ID'], 'time' );
		$_post['club'] = $this->term_data( $post['ID'], 'club' );
		$_post['date'] = $this->term_data( $post['ID'], 'date' );
		$_post['year'] = $this->term_data( $post['ID'], 'year' );
		$_post['season'] = $this->term_data( $post['ID'], 'season' );
		$_post['national'] = $this->term_data( $post['ID'], 'national' );

		return $_post;
	}

	function json_prepare_meta_sr_id( $metas ) {
		if( array_key_exists( 'sr_id', $metas ) ) {
			unset( $metas['sr_id'] );
		}

		return $metas;
	}

	function json_prepare_meta_featured_image( $metas ) {
		if( array_key_exists( 'featured_image', $metas ) ) {
			unset( $metas['featured_image'] );
		}

		return $metas;
	}

	function base_post( $post_id ) {


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

	function term_data( $post_id, $term ) {
		$terms = wp_get_post_terms( $post_id, $term, array( 'fields' => 'names' ) );

		if( empty( $terms ) ) {
			return '';
		}

		return current( $terms );
	}

	function term_id( $post_id, $term ) {
		$terms = wp_get_post_terms( $post_id, $term, array( 'ids' => 'names' ) );

		if( empty( $terms ) ) {
			return '';
		}

		return current( $terms );
	}

	function post_meta( $post_id, $meta ) {
		if( empty( $post_id ) ) {
			return false;
		}

		if( empty( $meta ) ) {
			return false;
		}

		return get_post_meta( $post_id, $meta, true );
	}

	function meta_link( $post_id ) {
		$meta_links = array(
			'self'       => json_url( $this->base . '/' . $post_id ),
			'collection' => json_url( $this->base )
		);

		$meta_links = array_merge( $meta_links, apply_filters( 'osd_api_' . $this->type . '_links', array(), $post_id ) );

		return $meta_links;
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