<?php 
/**
 * Adds post types and their metaboxes . 
 *
 * @package WordPress
 * @subpackage OpenSwimData
 */ 
class OSD_P2P {

	function __construct() {
		if ( !in_array( 'posts-to-posts/posts-to-posts.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
			return;
		}

		$this->register_p2p();
	}

	function register_p2p() {
		add_action( 'p2p_init', array( &$this, 'p2p_swimmer_result' ) );
	}


	/* P2P Connections * * * * * * * * * * * * * * * * * * */

	function p2p_swimmer_result() {
		p2p_register_connection_type( array(
			'name' => 'swimmers_results',
			'from' => 'swimmer',
			'to' => 'result'
		) );
	}
}

