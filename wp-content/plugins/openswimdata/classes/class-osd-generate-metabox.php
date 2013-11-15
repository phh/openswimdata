<?php 
/**
 * Generates a metabox. 
 *
 * @package WordPress
 * @subpackage OpenSwimData
 */ 
class OSD_Generate_Metabox {

	var $box_title; // Title, displayed at the top of the meta box.
	var $post_types; // Where to display the meta box - post, page, custom post type, etc.
	var $box_name; // Unique name for the meta box
	var $context; // Context (position on the page e.g. normal, side)
	var $priority; // Priority (how far up the page e.g. high, core, default, low)
	var $options; // Array of options (fields) to display for the meta box

	/**
	 * Constructor - assigns params to vars and sets up add and save using those vars
	 * @param array $info Box info
	 * @param array $options All the fields in the box
	 */
	function osd_generate_metabox( $info, $options ) {
		$this->info = $info;
		$this->box_title = $info['title'];
		$this->post_types = $info['post_types'];
		$this->box_name = $info['box_name'];
		$this->context = $info['context'];
		$this->priority = $info['priority'];
		$this->options = $options;

		add_action( 'add_meta_boxes', array( &$this, 'add_metabox' ) );
		add_action( 'save_post', array( &$this, 'save_metabox' ) );
	}

	/**
	 * Adds a metabox to the right admin screen
	 */
	function add_metabox() {
		if ( is_array( $this->post_types ) ) {
			foreach( $this->post_types as $loc ) { // to support array of post_types
				add_meta_box(
					$this->box_name, // ID
					$this->box_title, // Title
					array( &$this, 'display_metabox' ), // Callback function to print HTML
					$loc, // Post type
					$this->context, // Context
					$this->priority // Priority
				);
			}
		}
	}

	/**
	 * Generates the code for the metabox
	 * @param object $post The post we're adding the custom fields to
	 */
	function display_metabox( $post ) {
	?>
		<div class="form-wrap">
		<?php wp_nonce_field( plugin_basename( __FILE__ ), $this->box_name . '_wpnonce', false, true ); // Security field for verification
		foreach ( $this->options as $field ) : ?>
			<div class="form-field form-required">
				<?php $this->choose_type( $field, $post->ID ); ?>
			</div>
		<?php endforeach; ?>
		</div><!-- //form_wrap -->
	<?php
	}

	/**
	 * Generates the custom fields depending on your type
	 * @param array field The field information
	 * @param int post_id The id of the post we're storing the metadata in
	 */
	function choose_type( $field, $post_id ) {
		// If there is already metadata saved to the post
		if ( get_post_meta( $post_id, $field['name'], true ) )
			$data = get_post_meta( $post_id, $field['name'], true );
		// If there is no metadata but there is a default value to the field
		elseif ( '' == get_post_meta( $post_id, $field['name'], true ) && !empty( $field['default'] ) )
			$data = $field['default'];
		else
			$data = '';

		switch( $field['type'] ) {
			case 'text' :
				$this->display_meta_text( $field, $data );
				break;
			case 'textarea' :
				$this->display_meta_textarea( $field, $data );
				break;
			case 'select':
				$this->display_meta_select( $field, $data );
				break;
			case 'checkbox':
				$this->display_meta_checkbox( $field, $data );
				break;
			case 'checkboxes':
				$this->display_meta_checkboxes( $field, $data );
				break;
			case 'radio':
				$this->display_meta_radio( $field, $data );
				break;
			case 'file':
				$this->display_meta_file( $field, $data );
				break;
			case 'custom' :
				$this->display_meta_custom( $field, $data );
		}
	}

	/**
	 * Gives us a prefix for our radio and checkboxes
	 * @param string $name
	 * @param string $key
	 */
	function prefix( $name, $key ) {
		echo $name . '_' . $key;
	}

	/**
	 * Generates the code for a text field
	 * @param array field The field information
	 * @param mixed data Any existing data stored for this field
	 */
	function display_meta_text( $field, $data ) {
	?>
		<label for="<?php echo $field['name']; ?>">
			<strong><?php echo $field['label']; ?></strong>
		</label>
		<input type="text" id="<?php echo $field['name']; ?>" name="<?php echo $field['name']; ?>" value="<?php echo esc_html( $data ); ?>" />
		<?php if( isset( $field[ 'desc' ] ) ) : ?>
			<p><?php echo $field['desc']; ?></p>
		<?php endif;
	}

	/**
	 * Generates the code for a textarea field
	 * @param array field The field information
	 * @param mixed data Any existing data stored for this field
	 */
	function display_meta_textarea( $field, $data ) {
	?>
		<label for="<?php echo $field['name']; ?>">
			<strong><?php echo $field['label']; ?></strong>
		</label>
		<textarea id="<?php echo $field['name']; ?>" name="<?php echo $field['name']; ?>"><?php echo stripslashes( $data ); ?></textarea>
		<?php if( isset( $field[ 'desc' ] ) ) : ?>
			<p><?php echo $field['desc']; ?></p>
		<?php endif;
	}

	/**
	 * Generates the code for a select field
	 * @param array field The field information
	 * @param mixed data Any existing data stored for this field
	 */
	function display_meta_select( $field, $data ) {
	?>
		<label for="<?php echo $field['name']; ?>">
			<strong><?php echo $field['label']; ?></strong>
		</label>
		<select name="<?php echo $field['name']; ?>" id="<?php echo $field['name']; ?>" >
			<?php foreach ( $field['options'] as $key => $label ) : ?>
			<option value="<?php echo $key; ?>" <?php selected( $data, $key ); ?>>
				<?php echo $label; ?>
			</option>
			<?php endforeach; ?>
		</select>
		<?php if( isset( $field[ 'desc' ] ) ) : ?>
			<p><?php echo $field['desc']; ?></p>
		<?php endif;
	}

	/**
	 * Generates the code for a checkbox field
	 * @param array field The field information
	 * @param mixed data Any existing data stored for this field
	 */
	function display_meta_checkbox( $field, $data ) {
	?>
		<input type="checkbox" style="float:left;width:auto;margin:3px 5px 0 0;" value="1" name="<?php echo $field['name']; ?>" id="<?php echo $field['name']; ?>" <?php checked( $data, 1 ); ?> />
		<label for="<?php echo $field['name']; ?>">
			<strong><?php echo $field['label']; ?></strong>
		</label>
		<?php if( isset( $field[ 'desc' ] ) ) : ?>
			<p><?php echo $field['desc']; ?></p>
		<?php endif;
	}

	/**
	 * Generates the code for a group of checkboxes
	 * @param array field The field information
	 * @param mixed data Any existing data stored for this field
	 */
	function display_meta_checkboxes( $field, $data ) {
		?>
			<strong><?php echo $field['label']; ?></strong><br />
			<?php foreach( $field['options'] as $key => $label ) : ?>
				<input type="checkbox" id="<?php $this->prefix( $field['name'], $key ); ?>" style="float:left;width:auto;margin:3px 5px 0 0;" value="<?php echo $key; ?>" name="<?php echo $field['name']; ?>[]" <?php if($data) checked( in_array( $key, $data ) ); ?> />
				<label for="<?php $this->prefix( $field['name'], $key ); ?>"><?php echo $label; ?></label>
			<?php endforeach; ?>
			<?php if( isset( $field[ 'desc' ] ) ) : ?>
				<p><?php echo $field['desc']; ?></p>
			<?php endif;
		}

	/**
	 * Generates the code for a select field
	 * @param array field The field information
	 * @param mixed data Any existing data stored for this field
	 */
	function display_meta_radio( $field, $data ) {
		// If there is no data or default the first input will be checked
		if( empty( $field['default'] ) && empty( $data ) )
			$data = key( $field['options'] );
	?>
		<strong><?php echo $field['label']; ?></strong><br />
		<?php foreach( $field['options'] as $key => $label ) : ?>
			<input type="radio" id="<?php $this->prefix( $field['name'], $key ); ?>" style="float:left;width:auto;margin:3px 5px 0 0;" value="<?php echo $key; ?>" name="<?php echo $field['name']; ?>" <?php checked( $key, $data ); ?> />
			<label for="<?php $this->prefix( $field['name'], $key ); ?>"><?php echo $label; ?></label>
		<?php endforeach; ?>
		<?php if( isset( $field[ 'desc' ] ) ) : ?>
			<p><?php echo $field['desc']; ?></p>
		<?php endif;
	}

	/** Generates the code for a file field
	 * @param array field The field information
	 * @param mixed data Any existing data stored for this field
	 */
	function display_meta_file( $field, $data ) { ?>
		<label for="<?php echo $field['name']; ?>">
			<strong><?php echo $field['label']; ?></strong>
		</label>
		<input type="button"<?php if( esc_html( $data ) ) echo ' style="display: none;"'; ?> class="add-file_button" name="<?php echo $field['name']; ?>_button" data-fieldid="<?php echo $field['name']; ?>" value="<?php bullet_e( 'Browse...' ); ?>" />
		<div class="file-display-box" data-fieldid="<?php echo $field['name']; ?>">
			<?php if( esc_html( $data ) ) {
				$att = get_post( $data );
				echo '<a href="' . wp_get_attachment_url( $data ) . '" target="_blank">' . $att->post_title . '</a>';
			}
			?>
		</div>
		<input type="hidden" id="<?php echo $field['name']; ?>" name="<?php echo $field['name']; ?>" value="<?php echo esc_html( $data ); ?>" />
		<a <?php if( !$data ) echo 'style="display: none;"'; ?>class="remove-file_link" data-fieldid="<?php echo $field['name']; ?>" href="javascript:void(0)"><?php bullet_e( 'Remove file' ); ?></a>
		<?php if( isset( $field[ 'desc' ] ) ) : ?>
			<p><?php echo $field['desc']; ?> </p>
		<?php endif;
	}

	/**
	 * Handle the custom callback
	 * @param array field The field information
	 * @param mixed data Any existing data stored for this field
	 */
	function display_meta_custom( $field, $data ) {
		if( empty( $field['cb'] ) )
			return $this->error( 'Custom metabox callback not specified!' );
		if( !is_callable( $field['cb'] ) )
			return $this->error( 'Not a valid callback: ' . $field['cb'] );

		call_user_func( $field['cb'], $field, $data );
	}

	/**
	 * Display an error wrapped in <strong>
	 * Accepts the translated string
	 * @param string $text
	 */
	function error( $text ) {
		echo '<strong>';
		echo $text;
		echo '</strong>';
	}

	/**
	 * Saves the metabox
	 */ 
	function save_metabox( $post_id ) {
		// Only save it if the form has been submitted
		if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return;

		// If the form nonce doesn't match, cancel
		if( !isset( $_POST[ $this->box_name . '_wpnonce'] ) || !wp_verify_nonce( $_POST[ $this->box_name . '_wpnonce' ], plugin_basename( __FILE__ ) ) )
			return;

		// If the user does not have write permissions, cancel
		if ( !current_user_can( 'edit_post', $post_id ) )
			return;

		// Otherwise, for each field in the box, check and save
		foreach( $this->options as $meta_field ) {
			// Get the posted data.
			$new_meta_value = isset( $_POST[$meta_field['name']] ) ? $_POST[$meta_field['name']] : '';

			// Get the meta value of the field.
			$meta_value = get_post_meta( $post_id, $meta_field['name'], true );

			// If there is a new posted value and the meta value has not been set, add it.
			if ( $new_meta_value && '' == $meta_value )
				add_post_meta( $post_id, $meta_field['name'], $new_meta_value, true );

			// If the meta value exists and the posted value is not equal to the meta value, update it
			elseif ( $new_meta_value && $new_meta_value != $meta_value )
				update_post_meta( $post_id, $meta_field['name'], $new_meta_value );

			// Speciel for checkboxes - set to '0' if not checked
			elseif( ( $meta_field['type'] == 'checkbox' || $meta_field['type'] == 'checkboxes' ) && '' == $new_meta_value )
				update_post_meta( $post_id, $meta_field['name'], 0 );

			// If the posted value is empty and the old meta value still exists, delete it.
			elseif ( '' == $new_meta_value && $meta_value )
				delete_post_meta( $post_id, $meta_field['name'], $meta_value );
		}
	}
}

