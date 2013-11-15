<?php 
/**
 * Adds post types and their metaboxes . 
 *
 * @package WordPress
 * @subpackage OpenSwimData
 */ 
class OSD_Taxonomies_Metaboxes extends OSD_Taxonomies {

	function __construct() {
		$this->register_taxonomy_metaboxes();
	}

	function register_taxonomy_metaboxes() {
		foreach( $this->_taxonomies as $taxonomy ) {
			add_action( $taxonomy . '_add_form_fields', array( &$this, 'sr_id_add_form_fields' ) );
			add_action( $taxonomy . '_edit_form_fields', array( &$this, 'sr_id_edit_form_fields' ) );
			add_action( 'created_' . $taxonomy, array( &$this, 'save_sr_id' ) );
			add_action( 'edited_' . $taxonomy, array( &$this, 'save_sr_id' ) );
		}
	}


	/* Meta Boxes * * * * * * * * * * * * * * * * * * */

	function sr_id_add_form_fields( $taxonomy ) {
		?>
		<div class="form-field">
			<label for="sr-id"><?php printf( _osd_x( 'Swimrankings %s ID', 'Taxonomy Slug' ), $taxonomy ); ?></label>
			<input name="sr_<?php echo $taxonomy; ?>" id="sr-id" type="text" value="" size="40" />
			<p><?php printf( _osd__( 'Info for internal use only. This is the current %s id from swimrankings.' ), $taxonomy ); ?></p>
		</div>
		<?php
	}

	function sr_id_edit_form_fields( $tag ) {
		$taxonomy = $tag->taxonomy;
		$term_id = $tag->term_id;
		?>
		<tr class="form-field">
			<th scope="row" valign="top">
				<label for="sr-id"><?php printf( _osd_x( 'Swimrankings %s ID', 'Taxonomy Slug' ), $taxonomy ); ?></label>
			</th>
			<td>
				<input name="sr_<?php echo $taxonomy; ?>" id="sr-id" type="text" value="<?php echo $this->get_term_meta( $taxonomy, $term_id ); ?>" size="40" />
				<p class="description"><?php printf( _osd__( 'Info for internal use only. This is the current %s id from swimrankings.' ), $taxonomy ); ?></p>
			</td>
		</tr>
		<?php
	}

	function save_sr_id( $term_id ) {
		$taxonomy = $_POST['taxonomy'];
		$key = 'sr_' . $taxonomy;
		$value = $_POST[$key];

		if( empty( $_POST[$key] ) )
			return;

		$term_meta = $this->get_term_meta( $taxonomy, $term_id );

		if( $term_meta == $value )
			return;

		$term_option = $this->name_term_meta( $taxonomy, $term_id );

		update_option( $term_option, $value );
	}


	/* Helpers * * * * * * * * * * * * * * * * * * */

	function name_term_meta( $taxonomy, $term_id ) {
		return $taxonomy . $term_id;
	}

	function get_term_meta( $taxonomy, $term_id ) {
		$option = $this->name_term_meta( $taxonomy, $term_id  );

		return get_option( $option );
	}
}

