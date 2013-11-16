<?php 
/**
 * Adds post types and their metaboxes . 
 *
 * @package WordPress
 * @subpackage OpenSwimData
 */ 
class OSD_Post_Types_Metabox extends OSD_Post_Types {

	function __construct() {
		include Openswimdata::$PLUGIN_DIR . 'classes/class-osd-generate-metabox.php';

		$this->register_post_metaboxes();
	}

	function register_post_metaboxes() {
		new OSD_Generate_Metabox( $this->metabox_swimmer_info(), $this->metabox_swimmer_options() );
		new OSD_Generate_Metabox( $this->metabox_result_info(), $this->metabox_result_options() );
		new OSD_Generate_Metabox( $this->metabox_meeting_info(), $this->metabox_meeting_options() );
	}


	/* Post Metaboxes * * * * * * * * * * * * * * * * * * */

	function metabox_swimmer_info() {
		return array(
			'box_name' => 'swimmer_info',
			'title' => _osd__( 'Info' ),
			'post_types' => array( 'swimmer' ),
			'context' => 'normal',
			'priority' => 'high'
		);
	}

	function metabox_result_info() {
		return array(
			'box_name' => 'result_info',
			'title' => _osd__( 'Info' ),
			'post_types' => array( 'result' ),
			'context' => 'normal',
			'priority' => 'high'
		);
	}

	function metabox_meeting_info() {
		return array(
			'box_name' => 'meeting_info',
			'title' => _osd__( 'Info' ),
			'post_types' => array( 'meeting' ),
			'context' => 'normal',
			'priority' => 'high'
		);
	}

	function metabox_swimmer_options() {
		return array(
			array(
				'type' => 'text',
				'label' => _osd__( 'First name' ),
				'name' => 'first_name'
			),
			array(
				'type' => 'text',
				'label' => _osd__( 'Last name' ),
				'name' => 'last_name'
			),
			array(
				'type' => 'text',
				'label' => _osd__( 'Swimrankings athlete ID' ),
				'name' => 'sr_id',
				'desc' => _osd__( 'Info for internal use only. This is the current athlete id from swimrankings.' )
			)
		);
	}

	function metabox_result_options() {
		return array(
			array(
				'type' => 'text',
				'label' => _osd__( 'Time' ),
				'name' => 'time'
			),
			array(
				'type' => 'text',
				'label' => _osd__( 'Rank National' ),
				'name' => 'rank_nat'
			),
			array(
				'type' => 'text',
				'label' => _osd__( 'Rank Europe' ),
				'name' => 'rank_eu'
			),
			array(
				'type' => 'text',
				'label' => _osd__( 'Swimrankings result ID' ),
				'name' => 'sr_id',
				'desc' => _osd__( 'Info for internal use only. This is the current result id from swimrankings.' )
			)
		);
	}

	function metabox_meeting_options() {
		return array(
			array(
				'type' => 'text',
				'label' => _osd__( 'Meeting' ),
				'name' => 'meeting'
			),
			array(
				'type' => 'text',
				'label' => _osd__( 'Swimrankings meet ID' ),
				'name' => 'sr_id',
				'desc' => _osd__( 'Info for internal use only. This is the current meet id from swimrankings.' )
			)
		);
	}
}

