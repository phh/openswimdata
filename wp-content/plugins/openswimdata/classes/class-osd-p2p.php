<?php 
/**
 * Adds post types and their metaboxes . 
 *
 * @package WordPress
 * @subpackage OpenSwimData
 */ 
class OSD_P2P {
	protected $_p2p = array( 'swimmers_results', 'meetings_results' );

	function __construct() {
		if ( !in_array( 'posts-to-posts/posts-to-posts.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
			return;
		}

		$this->register_p2p();
	}

	function register_p2p() {
		foreach( $this->_p2p as $p2p ) {
			add_action( 'p2p_init', array( &$this, 'p2p_' . $p2p ) );
		}
	}


	/* P2P Connections * * * * * * * * * * * * * * * * * * */

	function p2p_swimmers_results() {
		p2p_register_connection_type( array(
			'name' => 'swimmers_results',
			'from' => 'swimmer',
			'to' => 'result'
		) );
	}

	function p2p_meetings_results() {
		p2p_register_connection_type( array(
			'name' => 'meetings_results',
			'from' => 'meeting',
			'to' => 'result'
		) );
	}
}

