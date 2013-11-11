<?php
/**
 * Generates a metabox.
 * Call it from the Editor Buttons section of functions-admin.php
 *
 * @since v2
 * @package WordPress
 * @subpackage Bullet
 */
class Bullet_Shortcode_Button_Group {

	var $group_name; // Group name
	var $post_type; // Where to display the group
	var $options; // Individual button options

	/**
	 * Constructor - builds some vars and runs the filter for custom buttons
	 * @param array $info Box info
	 * @param array $options All the fields in the box
	 */
	function bullet_shortcode_button_group( $info, $options ) {

		// fill the vars with data
		$this->group_name = isset( $info['group_name'] ) ? $info['group_name'] : false;
		$this->post_type = isset( $info['post_type'] ) ? $info['post_type'] : false;
		$this->options = $options;

		// Conditionally load the button group to defined post type(s)
		if( is_array( $this->post_type ) ) {

			foreach( $this->post_type as $post_type ) {

				if( $post_type == $this->get_current_post_type() ) {
					add_action( 'admin_init', array( &$this, 'add_mce_filters' ) );
					break;
				}
			}

		} elseif( $post_type == $this->get_current_post_type() ) {
			add_action( 'admin_init', array( &$this, 'add_mce_filters' ) );
		}

		return false;
	}

	/**
	 * Adds the necessary filters for shortcode button
	 */
	function add_mce_filters() {
		add_filter( 'mce_buttons', array( &$this, 'append_buttons' ) );
		add_filter( 'mce_external_plugins', array( &$this, 'add_button_script' ) );
	}

	/**
	 * Appends buttons to existing TinyMCE buttons
	 */
	function append_buttons( $buttons ) {

		// loop through the options array to extract each button's name
		foreach( $this->options as $option ) {
			array_push( $buttons, $option['button_name'] );
		}
		return $buttons;
	}

	/**
	 * Adds shortcode buttons script
	 * Calls the js function in admin_scripts and passes args to it.
	 * Has to call an existing js file (even though it does nothing) to get around a WP restriction that all buttons in the array have a js file
	 */
	function add_button_script( $plugin_array ) {
		$plugin_array[$this->group_name] = get_bloginfo( 'template_url' ) . '/js/admin_scripts.js';
		echo '
		<script>
		jQuery(function() {
			MCEBUTTON.init(["' . $this->group_name . '", ' . json_encode( $this->options ) . ']);
			MCEBUTTON.addTheButtons();
		});
		</script>
		';

		return $plugin_array;
	}

	/**
	 * Helper function to get the current post type in the WordPress Admin
	 */
	function get_current_post_type() {
		global $post, $typenow, $current_screen;

		//we have a post so we can just get the post type from that
		if ( $post && $post->post_type )
			return $post->post_type;

		//check the global $typenow - set in admin.php
		elseif( $typenow )
			return $typenow;

		//check the global $current_screen object - set in sceen.php
		elseif( $current_screen && $current_screen->post_type )
			return $current_screen->post_type;

		//check the post_type querystring
		elseif( isset( $_REQUEST['post_type'] ) )
			return sanitize_key( $_REQUEST['post_type'] );

		// check the post data of the post var for edit post screens
		elseif( isset( $_GET['action'] ) && $_GET['action'] == 'edit' && isset( $_GET['post'] ) )
			return get_post($_GET['post'])->post_type;

		//we do not know the post type, assume it is posts!
		return 'post';
	}

}

