<?php
/*
Plugin Name: OpenSwimData
Description: OpenSwimData will crawl, parse and deliver swimming statistics.
Version: 0.1
Author: Patrick Hesselberg
*/

class Openswimdata {
	static public $PLUGIN_URL;
	static public $PLUGIN_DIR;
	static public $PLUGIN_CLS_DIR;

	function __construct() {
		$this->load();
		$this->register_plugin_hooks();
		$this->register_post_types();
		$this->register_taxonomies();
		$this->register_post_metaboxes();
		$this->register_taxonomy_metaboxes();
		$this->enqueue();
	}

	function load() {
		self::$PLUGIN_URL = plugins_url( '/', __FILE__ );
		self::$PLUGIN_DIR = plugin_dir_path( __FILE__ );

		include self::$PLUGIN_DIR . 'classes/class-generate-metabox.php';
	}

	function register_plugin_hooks() {
		register_activation_hook( __FILE__, array( &$this, 'activate' ) );
		register_deactivation_hook( __FILE__, array( &$this, 'deactivate' ) );
	}

	function activate() {
		$this->post_type_swimmer();
		$this->post_type_result();
		$this->post_type_meeting();

		flush_rewrite_rules();
	}

	function deactivate() {
		flush_rewrite_rules();
	}

	function register_post_types() {
		add_action( 'init', array( &$this, 'post_type_swimmer' ) );
		add_action( 'init', array( &$this, 'post_type_result' ) );
		add_action( 'init', array( &$this, 'post_type_meeting' ) );
	}

	function register_taxonomies() {
		add_action( 'init', array( &$this, 'taxonomy_gender' ) );
		add_action( 'init', array( &$this, 'taxonomy_pool' ) );
		add_action( 'init', array( &$this, 'taxonomy_year' ) );
		add_action( 'init', array( &$this, 'taxonomy_style' ) );
		add_action( 'init', array( &$this, 'taxonomy_distance' ) );
		add_action( 'init', array( &$this, 'taxonomy_club' ) );
		add_action( 'init', array( &$this, 'taxonomy_city' ) );
	}

	function register_post_metaboxes() {
		new Bullet_Meta_Box( $this->metabox_swimmer_info(), $this->metabox_swimmer_options() );
		new Bullet_Meta_Box( $this->metabox_result_info(), $this->metabox_result_options() );
		new Bullet_Meta_Box( $this->metabox_meeting_info(), $this->metabox_meeting_options() );
	}

	function register_taxonomy_metaboxes() {
		$taxonomies = array( 'gender', 'pool', 'year', 'style', 'distance');

		foreach( $taxonomies as $taxonomy ) {
			add_action( $taxonomy . '_add_form_fields', array( &$this, 'sr_id_add_form_fields' ) );
			add_action( $taxonomy . '_edit_form_fields', array( &$this, 'sr_id_edit_form_fields' ) );
			add_action( 'created_' . $taxonomy, array( &$this, 'save_sr_id' ) );
			add_action( 'edited_' . $taxonomy, array( &$this, 'save_sr_id' ) );
		}
	}


	/* Post Types * * * * * * * * * * * * * * * * * * */

	function post_type_swimmer() {
		$labels = array(
			'name' => _osd__( 'Swimmers' ),
			'singular_name' => _osd__( 'Swimmer' ),
			'add_new' => _osd_x( 'Add New', 'Swimmer' ),
			'add_new_item' => _osd__( 'Add New Swimmer' ),
			'edit_item' => _osd__( 'Edit Swimmer' ),
			'new_item' => _osd__( 'New Swimmer' ),
			'view_item' => _osd__( 'View Swimmer' ),
			'search_items' => _osd__( 'Search Swimmers' ),
			'not_found' => _osd__( 'No swimmers found.' ),
			'not_found_in_trash' => _osd__( 'No swimmers found in Trash.' ),
			'all_items' => _osd__( 'All Swimmers' ),
			'menu_name' => _osd__( 'Swimmers' ),
		);
		$args = array(
			'labels' => $labels,
			'description' => '',
			'public' => true,
			'menu_icon' => '',
			'hierarchical' => false,
			'supports' => array( 'title', 'revisions' ),
			'taxonomies' => array(),
			'has_archive' => true
		);

		register_post_type( 'swimmer', $args );
	}

	function post_type_result() {
		$labels = array(
			'name' => _osd__( 'Results' ),
			'singular_name' => _osd__( 'Result' ),
			'add_new' => _osd_x( 'Add New', 'Result' ),
			'add_new_item' => _osd__( 'Add New Result' ),
			'edit_item' => _osd__( 'Edit Result' ),
			'new_item' => _osd__( 'New Result' ),
			'view_item' => _osd__( 'View Result' ),
			'search_items' => _osd__( 'Search Results' ),
			'not_found' => _osd__( 'No results found.' ),
			'not_found_in_trash' => _osd__( 'No results found in Trash.' ),
			'all_items' => _osd__( 'All Results' ),
			'menu_name' => _osd__( 'Results' ),
		);
		$args = array(
			'labels' => $labels,
			'description' => '',
			'public' => true,
			'menu_icon' => '',
			'hierarchical' => false,
			'supports' => array( 'title', 'revisions' ),
			'taxonomies' => array(),
			'has_archive' => true
		);

		register_post_type( 'result', $args );
	}

	function post_type_meeting() {
		$labels = array(
			'name' => _osd__( 'Meetings' ),
			'singular_name' => _osd__( 'Meeting' ),
			'add_new' => _osd_x( 'Add New', 'Meeting' ),
			'add_new_item' => _osd__( 'Add New Meeting' ),
			'edit_item' => _osd__( 'Edit Meeting' ),
			'new_item' => _osd__( 'New Meeting' ),
			'view_item' => _osd__( 'View Meeting' ),
			'search_items' => _osd__( 'Search Meeting' ),
			'not_found' => _osd__( 'No meetings found.' ),
			'not_found_in_trash' => _osd__( 'No meetings found in Trash.' ),
			'all_items' => _osd__( 'All Meetings' ),
			'menu_name' => _osd__( 'Meetings' ),
		);
		$args = array(
			'labels' => $labels,
			'description' => '',
			'public' => true,
			'menu_icon' => '',
			'hierarchical' => false,
			'supports' => array( 'title', 'revisions' ),
			'taxonomies' => array(),
			'has_archive' => true
		);

		register_post_type( 'meeting', $args );
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
				'label' => _osd__( 'Year' ),
				'name' => 'year'
			),
			array(
				'type' => 'text',
				'label' => _osd__( 'National' ),
				'name' => 'national'
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
				'label' => _osd__( 'Rank Europe' ),
				'name' => 'rank_eu'
			),
			array(
				'type' => 'text',
				'label' => _osd__( 'Rank National' ),
				'name' => 'rank_nat'
			),
			array(
				'type' => 'text',
				'label' => _osd__( 'Date' ),
				'name' => 'date'
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
				'label' => _osd__( 'City' ),
				'name' => 'city'
			),
			array(
				'type' => 'text',
				'label' => _osd__( 'Rank Nat' ),
				'name' => 'rank_nat'
			),
			array(
				'type' => 'text',
				'label' => _osd__( 'Date' ),
				'name' => 'date'
			),
			array(
				'type' => 'text',
				'label' => _osd__( 'Swimrankings meet ID' ),
				'name' => 'sr_id',
				'desc' => _osd__( 'Info for internal use only. This is the current meet id from swimrankings.' )
			)
		);
	}


	/* Taxonomies * * * * * * * * * * * * * * * * * * */

	 function taxonomy_gender() {
		$labels = array(
			'name' => _osd_x( 'Genders', 'taxonomy general name' ),
			'singular_name' => _osd__( 'Gender' ),
			'search_items' => _osd__( 'Search Genders' ),
			'popular_items' => _osd__( 'Popular Genders' ),
			'all_items' => _osd__( 'All Genders' ),
			'edit_item' => _osd__( 'Edit Gender' ),
			'view_item' => _osd__( 'View Gender' ),
			'update_item' => _osd__( 'Update Gender' ),
			'add_new_item' => _osd__( 'Add New Gender' ),
			'new_item_name' => _osd__( 'New Gender Name' ),
			'add_or_remove_items' => _osd__( 'Add or remove genders' ),
			'not_found' => _osd__( 'No genders found.' ),
			'menu_name' => _osd__( 'Genders' )
		);

		$args = array(
			'labels' => $labels,
			'public' => true,
			'show_admin_column' => true,
			'hierarchical' => true
		);

		$post_types = array( 'swimmer' );

	 	register_taxonomy( 'gender', $post_types, $args );
	 }

	function taxonomy_pool() {
		$labels = array(
			'name' => _osd_x( 'Pools', 'taxonomy general name' ),
			'singular_name' => _osd__( 'Pool' ),
			'search_items' => _osd__( 'Search Pools' ),
			'popular_items' => _osd__( 'Popular Pools' ),
			'all_items' => _osd__( 'All Pools' ),
			'edit_item' => _osd__( 'Edit Pool' ),
			'view_item' => _osd__( 'View Pool' ),
			'update_item' => _osd__( 'Update Pool' ),
			'add_new_item' => _osd__( 'Add New Pool' ),
			'new_item_name' => _osd__( 'New Pool Name' ),
			'add_or_remove_items' => _osd__( 'Add or remove pools' ),
			'not_found' => _osd__( 'No pools found.' ),
			'menu_name' => _osd__( 'Pools' )
		);

		$args = array(
			'labels' => $labels,
			'public' => true,
			'show_admin_column' => true,
			'hierarchical' => true
		);

		$post_types = array( 'result' );

	 	register_taxonomy( 'pool', $post_types, $args );
	}

	function taxonomy_year() {
		$labels = array(
			'name' => _osd_x( 'Years', 'taxonomy general name' ),
			'singular_name' => _osd__( 'Year' ),
			'search_items' => _osd__( 'Search Years' ),
			'popular_items' => _osd__( 'Popular Years' ),
			'all_items' => _osd__( 'All Years' ),
			'edit_item' => _osd__( 'Edit Year' ),
			'view_item' => _osd__( 'View Year' ),
			'update_item' => _osd__( 'Update Year' ),
			'add_new_item' => _osd__( 'Add New Year' ),
			'new_item_name' => _osd__( 'New Year Name' ),
			'add_or_remove_items' => _osd__( 'Add or remove years' ),
			'not_found' => _osd__( 'No years found.' ),
			'menu_name' => _osd__( 'Years' )
		);

		$args = array(
			'labels' => $labels,
			'public' => true,
			'show_admin_column' => true,
			'hierarchical' => true
		);

		$post_types = array( 'result' );

	 	register_taxonomy( 'year', $post_types, $args );
	}

	function taxonomy_style() {
		$labels = array(
			'name' => _osd_x( 'Styles', 'taxonomy general name' ),
			'singular_name' => _osd__( 'Style' ),
			'search_items' => _osd__( 'Search Styles' ),
			'popular_items' => _osd__( 'Popular Styles' ),
			'all_items' => _osd__( 'All Styles' ),
			'edit_item' => _osd__( 'Edit Style' ),
			'view_item' => _osd__( 'View Style' ),
			'update_item' => _osd__( 'Update Style' ),
			'add_new_item' => _osd__( 'Add New Style' ),
			'new_item_name' => _osd__( 'New Style Name' ),
			'add_or_remove_items' => _osd__( 'Add or remove styles' ),
			'not_found' => _osd__( 'No styles found.' ),
			'menu_name' => _osd__( 'Styles' )
		);

		$args = array(
			'labels' => $labels,
			'public' => true,
			'show_admin_column' => true,
			'hierarchical' => true
		);

		$post_types = array( 'result' );

	 	register_taxonomy( 'style', $post_types, $args );
	}

	function taxonomy_distance() {
		$labels = array(
			'name' => _osd_x( 'Distances', 'taxonomy general name' ),
			'singular_name' => _osd__( 'Distance' ),
			'search_items' => _osd__( 'Search Distances' ),
			'popular_items' => _osd__( 'Popular Distances' ),
			'all_items' => _osd__( 'All Distances' ),
			'edit_item' => _osd__( 'Edit Distance' ),
			'view_item' => _osd__( 'View Distance' ),
			'update_item' => _osd__( 'Update Distance' ),
			'add_new_item' => _osd__( 'Add New Distance' ),
			'new_item_name' => _osd__( 'New Distance Name' ),
			'add_or_remove_items' => _osd__( 'Add or remove distances' ),
			'not_found' => _osd__( 'No distances found.' ),
			'menu_name' => _osd__( 'Distances' )
		);

		$args = array(
			'labels' => $labels,
			'public' => true,
			'show_admin_column' => true,
			'hierarchical' => true
		);

		$post_types = array( 'result' );

	 	register_taxonomy( 'distance', $post_types, $args );
	}

	function taxonomy_club() {
		$labels = array(
			'name' => _osd_x( 'Clubs', 'taxonomy general name' ),
			'singular_name' => _osd__( 'Club' ),
			'search_items' => _osd__( 'Search Clubs' ),
			'popular_items' => _osd__( 'Popular Clubs' ),
			'all_items' => _osd__( 'All Clubs' ),
			'edit_item' => _osd__( 'Edit Club' ),
			'view_item' => _osd__( 'View Club' ),
			'update_item' => _osd__( 'Update Club' ),
			'add_new_item' => _osd__( 'Add New Club' ),
			'new_item_name' => _osd__( 'New Club Name' ),
			'add_or_remove_items' => _osd__( 'Add or remove clubs' ),
			'not_found' => _osd__( 'No clubs found.' ),
			'menu_name' => _osd__( 'Clubs' )
		);

		$args = array(
			'labels' => $labels,
			'public' => true,
			'show_admin_column' => true,
			'hierarchical' => true
		);

		$post_types = array( 'swimmer', 'result' );

	 	register_taxonomy( 'club', $post_types, $args );
	}

	function taxonomy_city() {
		$labels = array(
			'name' => _osd_x( 'Cities', 'taxonomy general name' ),
			'singular_name' => _osd__( 'City' ),
			'search_items' => _osd__( 'Search Cities' ),
			'popular_items' => _osd__( 'Popular Cities' ),
			'all_items' => _osd__( 'All Cities' ),
			'edit_item' => _osd__( 'Edit City' ),
			'view_item' => _osd__( 'View City' ),
			'update_item' => _osd__( 'Update City' ),
			'add_new_item' => _osd__( 'Add New City' ),
			'new_item_name' => _osd__( 'New City Name' ),
			'add_or_remove_items' => _osd__( 'Add or remove cities' ),
			'not_found' => _osd__( 'No cities found.' ),
			'menu_name' => _osd__( 'Cities' )
		);

		$args = array(
			'labels' => $labels,
			'public' => true,
			'show_admin_column' => true,
			'hierarchical' => true
		);

		$post_types = array( 'meeting' );

	 	register_taxonomy( 'city', $post_types, $args );
	}

	/* Taxonomies * * * * * * * * * * * * * * * * * * */

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


	/* Config * * * * * * * * * * * * * * * * * * */

	function enqueue() {
		add_action( 'admin_enqueue_scripts', array( &$this, 'css' ) );
	}

	function css() {
		wp_enqueue_style( 'osd-style', self::$PLUGIN_URL . 'assets/css/style.css' );
	}
}

function _osd_e( $text ) {
	_e( $text, 'openswimdata' );
}

function _osd__( $text ) {
	return __( $text, 'openswimdata' );
}

function _osd_x( $text, $context ) {
	return _x( $text, $context, 'openswimdata' );
}

function _osd_ex( $text, $context ) {
	return _ex( $text, $context, 'openswimdata' );
}

new Openswimdata;

