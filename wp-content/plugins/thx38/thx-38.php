<?php
/*
Plugin Name: THX_38
Plugin URI:
Description: THX stands for THeme eXperience. A plugin that rebels against their rigidly controlled themes.php in search for hopeful freedom in WordPress 3.8, or beyond.
Version: 0.9.1
Author: THX_38 Team
*/

class THX_38 {

	function __construct() {

		add_action( 'load-themes.php',  array( $this, 'themes_screen' ) );
		add_action( 'admin_print_scripts-themes.php', array( $this, 'enqueue' ) );

	}

	/**
	 * The main template file for the themes.php screen
	 *
	 * Replaces entire contents of themes.php
	 * @require admin-header.php and admin-footer.php
	 */
	function themes_screen() {

		// Bail if user has no capabilities
		if ( ! current_user_can( 'switch_themes' ) && ! current_user_can( 'edit_theme_options' ) )
			wp_die( __( 'Cheatin&#8217; uh?' ) );

		// Actions
		self::get_actions();

		// Help tabs
		self::help_tabs();

		// Admin header
		require_once( ABSPATH . 'wp-admin/admin-header.php' );

		// Display relevant messages
		self::update_messages();

		do_action( 'themes_admin_head' );

		?>
		<div id="appearance" class="wrap">
			<?php do_action( 'themes_admin_head_before' ); ?>
			<h2><?php esc_html_e( 'Themes' ); ?>
				<span id="theme-count" class="theme-count"></span>
				<a href="<?php echo admin_url( 'theme-install.php' ); ?>" class="add-new-h2"><?php echo esc_html( _x( 'Add New', 'Add new theme' ) ); ?></a>
			</h2>
			<?php do_action( 'themes_admin_head_after' ); ?>
		</div>
		<?php

		do_action( 'themes_admin_footer' );

		// Get the templates
		self::print_templates();

		// Admin footer
		require( ABSPATH . 'wp-admin/admin-footer.php');
		exit;
	}

	/**
	 * Get the themes and prepare the JS object
	 * Sets attributes 'id' 'name' 'screenshot' 'description' 'author' 'version' 'active' ...
	 *
	 * @uses wp_get_themes self::get_current_theme
	 * @return array theme data
	 */
	protected function get_themes() {
		$themes = wp_get_themes( array(
			'allowed' => true
		) );

		$data = array();

		foreach( $themes as $slug => $theme ) {
			$data[] = apply_filters( 'themes_admin_theme_data', array(
				'id'           => $slug,
				'name'         => $theme->display( 'Name' ),
				'screenshot'   => self::get_multiple_screenshots( $theme ),
				'description'  => $theme->display( 'Description' ),
				'author'       => $theme->get( 'Author' ),
				'authorURI'    => $theme->get( 'AuthorURI' ),
				'version'      => $theme->Version,
				'tags'         => $theme->Tags,
				'parent'       => self::display_parent_theme( $theme ),
				'active'       => ( $slug == self::get_current_theme() ) ? true : null,
				'hasUpdate'    => (bool) self::theme_update( $theme ),
				'update'       => self::theme_update( $theme ),
				'actions'      => array(
					'activate' => wp_nonce_url( 'themes.php?action=activate&amp;template=' . urlencode( $theme->Template ) . '&amp;stylesheet=' . urlencode( $slug ), 'switch-theme_' . $slug ),
					'customize'=> admin_url( 'customize.php?theme=' . $slug ),
					'delete'   => wp_nonce_url( 'themes.php?action=delete&amp;stylesheet=' . urlencode( $slug ), 'delete-theme_' . $slug ),
				),
			), $theme );
		}

		$themes = $data;
		return apply_filters( 'themes_admin_themes', $themes );
	}

	/**
	 * Get current theme
	 * @uses wp_get_theme
	 * @return string theme slug
	 */
	protected function get_current_theme() {
		return get_stylesheet();
	}

	/**
	 * If a theme is a child theme display its parent
	 * @param theme
	 * @return string
	 */
	protected function display_parent_theme( $theme ) {
		if ( ! $theme->parent() )
			return false;

		$parent = sprintf( __( 'This is a Child Theme of <strong>%s</strong>.' ), $theme->parent()->display( 'Name' ) );
		return $parent;
	}

	/**
	 * Processes $_GET actions
	 * @uses self::switch_theme self::delete_theme
	 */
	protected function get_actions() {
		// Make sure we have capabilities
		if ( ! current_user_can( 'switch_themes' ) || ! isset( $_GET['action'] ) )
			return;

		self::switch_theme();
		self::delete_theme();
	}

	/**
	 * Switch theme action with $_GET['action']
	 * Redirects back to themes.php?activated=true on success
	 */
	protected function switch_theme() {
		if ( 'activate' == $_GET['action'] ) {
			check_admin_referer( 'switch-theme_' . $_GET['stylesheet'] );
			$theme = wp_get_theme( $_GET['stylesheet'] );

			// Check the theme exists and is allowed to use
			if ( ! $theme->exists() || ! $theme->is_allowed() )
				apply_filters( 'themes_admin_switch_theme_error', wp_die( __( 'Cheatin&#8217; uh?' ) ) );

			do_action( 'themes_admin_switch_theme' );
			switch_theme( $theme->get_stylesheet() );
			wp_redirect( admin_url( 'themes.php?activated=true' ) );
			exit;
		}
	}

	/**
	 * Delete theme action with $_GET['action']
	 * Redirects back to themes.php?deleted=true on success
	 */
	protected function delete_theme() {
		if ( 'delete' == $_GET['action'] ) {
			check_admin_referer( 'delete-theme_' . $_GET['stylesheet'] );
			$theme = wp_get_theme( $_GET['stylesheet'] );

			// Check user has capabilities for this action
			// and that the theme exists
			if ( ! current_user_can( 'delete_themes' ) || ! $theme->exists() )
				wp_die( __( 'Cheatin&#8217; uh?' ) );

			do_action( 'themes_admin_delete_theme' );
			delete_theme( $_GET['stylesheet'] );
			wp_redirect( admin_url( 'themes.php?deleted=true' ) );
			exit;
		}
	}

	/**
	 * Displays messages based on which action was performed
	 *
	 * @uses wp_get_theme
	 * @return html messages
	 */
	public function update_messages() {
		$theme = wp_get_theme();

		// Error message if theme is not valid
		if ( ! validate_current_theme() || isset( $_GET['broken'] ) ) {
			printf( '<div id="message1" class="updated"><p>' . __( 'The active theme is broken. Reverting to the default theme.' ) . '</p></div>' );
		}
		// Activation messages
		if ( isset( $_GET['activated'] ) ) {

			if ( isset( $_GET['previewed'] ) ) {
				$message = sprintf( __( 'Settings saved and theme activated. <a href="%s">Visit site</a>.' ), home_url( '/' ) );
				printf( '<div id="message2" class="updated"><p>' . $message . '</p></div>' );
			} else {
				$message = sprintf( __( '%s theme <strong>activated</strong>. <a href="%s">Visit site</a>.' ), $theme->name, home_url( '/' ) );
				printf( '<div id="message2" class="updated"><p>' . $message . '</p></div>' );
			}
		}
		// Theme deleted
		if ( isset( $_GET['deleted'] ) ) {
			$message = sprintf( __( 'Theme successfully <strong>deleted</strong>.' ) );
			printf( '<div id="message3" class="updated"><p>' . $message . '</p></div>' );
		}
	}

	/**
	 * Forks theme_update_available from wp-admin/includes/theme.php
	 * so we can pass update messages to the Backbone views.
	 * This coule hopefully become a native method somewhere else.
	 */
	protected function theme_update( $theme ) {
		static $themes_update;

		if ( ! current_user_can( 'update_themes' ) )
			return;

		if ( ! isset( $themes_update ) )
			$themes_update = get_site_transient( 'update_themes' );

		if ( ! is_a( $theme, 'WP_Theme' ) )
			return;

		$stylesheet = $theme->get_stylesheet();

		if ( isset( $themes_update->response[ $stylesheet ]) ) {
			$update = $themes_update->response[ $stylesheet ];
			$theme_name = $theme->display( 'Name' );
			$update_url = wp_nonce_url( 'update.php?action=upgrade-theme&amp;theme=' . urlencode($stylesheet), 'upgrade-theme_' . $stylesheet);
			$update_onclick = 'onclick="if ( confirm(\'' . esc_js( __("Updating this theme will lose any customizations you have made. 'Cancel' to stop, 'OK' to update.") ) . '\') ) {return true;}return false;"';

			if ( !is_multisite() ) {
				if ( ! current_user_can('update_themes') )
					$html = sprintf( '<p><strong>' . __('There is a new version of %1$s available. <a href="%2$s" class="thickbox" title="%1$s">View version %3$s details</a>.') . '</strong></p>', $theme_name, $update['url'], $update['new_version']);
				else if ( empty($update['package']) )
					$html = sprintf( '<p><strong>' . __('There is a new version of %1$s available. <a href="%2$s" class="thickbox" title="%1$s">View version %3$s details</a>. <em>Automatic update is unavailable for this theme.</em>') . '</strong></p>', $theme_name, $update['url'], $update['new_version']);
				else
					$html = sprintf( '<p><strong>' . __('There is a new version of %1$s available. <a href="%2$s" class="thickbox" title="%1$s">View version %3$s details</a> or <a href="%4$s" %5$s>update now</a>.') . '</strong></p>', $theme_name, $update['url'], $update['new_version'], $update_url, $update_onclick );
			}

			return $html;
		}
	}

	/**
	 * Enqueue scripts and styles
	 */
	public function enqueue() {

		// Relies on Backbone.js
		wp_enqueue_script( 'thx-38', plugins_url( 'thx-38.js', __FILE__ ), array( 'wp-backbone' ), '20130817', true );
		wp_enqueue_style( 'thx-38', plugins_url( 'thx-38.css', __FILE__ ), array(), '20130817', 'screen' );

		// Passes the theme data and settings
		// These are the bones of the application
		wp_localize_script( 'thx-38', '_THX38', array(
			'themes'   => $this->get_themes(),
			'settings' => array(
				'install_uri'   => admin_url( 'theme-install.php' ),
				'customizeURI'  => ( current_user_can( 'edit_theme_options' ) ) ? wp_customize_url() : null,
				'confirmDelete' => __( "Are you sure you want to delete this theme?\n\nClick 'Cancel' to go back, 'OK' to confirm the delete." ),
				'root'          => apply_filters( 'themes_admin_router_root', '/wp-admin/themes.php' ),
				'container'     => apply_filters( 'themes_admin_dom_container', '#appearance' ),
				'extraRoutes'   => apply_filters( 'themes_admin_dom_container', '' ),
			),
			'i18n' => apply_filters( 'themes_admin_i18n', array(
				'active'          => __( 'Current Theme' ),
				'add_new'         => __( 'Add New Theme' ),
				'customize'       => __( 'Customize' ),
				'activate'        => __( 'Activate' ),
				'preview'         => __( 'Preview' ),
				'delete'          => __( 'Delete' ),
				'updateAvailable' => __( 'Update Available' ),
			) ),
		) );
	}

	/**
	 * Method to get an array of all the screenshots a theme has
	 * It checks for files in the form of 'screenshot-n' at the root
	 * of a theme directory.
	 *
	 * Hardcoded to pngs for now.
	 *
	 * @param a theme object
	 * @returns array screenshot urls (first element is default screenshot)
	 */
	protected function get_multiple_screenshots( $theme ) {
		$base = $theme->get_stylesheet_directory_uri();
		$set = array( 2, 3, 4, 5 );

		// Screenshots array starts with default screenshot at position [0]
		$screenshots = array( $theme->get_screenshot() );

		// Check how many other screenshots a theme has
		foreach ( $set as $number ) {
			// Hard-coding file path for pngs...
			$file = '/screenshot-' . $number . '.png';
			$path = $theme->template_dir . $file;

			if ( ! file_exists( $path ) )
				continue;

			$screenshots[] = $base . $file;
		}

		// If there are no screenshots use a default image
		if ( ! $screenshots[0] )
			$screenshots[0] = self::get_default_screenshot();

		return $screenshots;
	}

	/**
	 * Theme action links.
	 * @todo check for capabilities and set up a more robust solution.
	 *
	 * @echo html
	 */
	function theme_actions_links() {
		$sections = array(
			'widgets.php' => __( 'Widgets' ),
			'nav-menus.php' => __( 'Menus' ),
		);
		foreach ( $sections as $url => $title ) {
			echo '<a class="button button-secondary" href="' . admin_url( $url ) . '">' . $title . '</a>';
		}
	}

	/**
	 * Gets default screenshot path
	 *
	 * @return url string
	 */
	public function get_default_screenshot() {
		return apply_filters( 'themes_admin_default_screenshot_uri', plugins_url( 'default.png', __FILE__ ) );
	}

	/**
	 * Help and documentation tabs
	 */
	function help_tabs() {

		// Main overview
		$help_overview  = '<p>' . __( 'This screen is used for managing your installed themes. Aside from the default theme included with your WordPress installation, themes are designed and developed by third parties.' ) . '</p>';
		$help_overview .= '<p>' . __( 'From this screen you can:' ) . '</p>';
		$help_overview .= '<ul><li>' . __( 'Hover or tap to see Activate and Preview buttons' ) . '</li>';
		$help_overview .= '<ul><li>' . __( 'Click on the theme to see the theme name, version, author, description, tags, and the Delete link' ) . '</li>';
		$help_overview .= '<ul><li>' . __( 'Click Customize for the current theme or Preview for any other theme to see a live preview' ) . '</li></ul>';
		$help_overview .= '<p>' . __( 'The current theme is the first listed.' ) . '</p>';

		get_current_screen()->add_help_tab( array(
			'id'      => 'overview',
			'title'   => __( 'Overview' ),
			'content' => $help_overview
		) );

		// Installing themes
		if ( current_user_can( 'install_themes' ) ) :
			if ( is_multisite() ) {
				$help_install = '<p>' . __('Installing themes on Multisite can only be done from the Network Admin section.') . '</p>';
			} else {
				$help_install = '<p>' . sprintf( __('If you would like to see more themes to choose from, click on the &#8220;Install Themes&#8221; tab and you will be able to browse or search for additional themes from the <a href="%s" target="_blank">WordPress.org Theme Directory</a>. Themes in the WordPress.org Theme Directory are designed and developed by third parties, and are compatible with the license WordPress uses. Oh, and they&#8217;re free!'), 'http://wordpress.org/themes/' ) . '</p>';
			}

			get_current_screen()->add_help_tab( array(
				'id'      => 'adding-themes',
				'title'   => __('Adding Themes'),
				'content' => $help_install
			) );
		endif;

		if ( current_user_can( 'edit_theme_options' ) ) :
			$help_customize =
				'<p>' . __( 'Tap or hover on any theme then click the "Preview" button to see a live preview of that theme and change theme options in a separate, full-screen view. You can also find a "Preview" button at the bottom of the theme details screen. Any installed theme can be previewed and customized in this way.' ) . '</p>'.
				'<p>' . __( 'The theme being previewed is fully interactive &mdash; navigate to different pages to see how the theme handles posts, archives, and other page templates.' ) . '</p>' .
				'<p>' . __( 'In the left-hand pane you can edit the theme settings. The settings will differ, depending on what theme features the theme being previewed supports. To accept the new settings and activate the theme all in one step, click the "Save &amp; Activate" button at the top of the left-hand pane.' ) . '</p>' .
				'<p>' . __( 'When previewing on smaller monitors, you can use the "Collapse" icon at the bottom of the left-hand pane. This will hide the pane, giving you more room to preview your site in the new theme. To bring the pane back, click on the Collapse icon again.' ) . '</p>';

			get_current_screen()->add_help_tab( array(
				'id'		=> 'customize-preview-themes',
				'title'		=> __( 'Previewing and Customizing' ),
				'content'	=> $help_customize
			) );
		endif;

		get_current_screen()->set_help_sidebar(
			'<p><strong>' . __( 'For more information:' ) . '</strong></p>' .
			'<p>' . __( '<a href="http://codex.wordpress.org/Using_Themes" target="_blank">Documentation on Using Themes</a>' ) . '</p>' .
			'<p>' . __( '<a href="http://wordpress.org/support/" target="_blank">Support Forums</a>' ) . '</p>'
		);
	}


	/**
	 * ------------------------
	 * Underscore.js Templates
	 * ------------------------
	 */
	private function print_templates() {
		apply_filters( 'themes_admin_theme_template',        self::theme_template() );
		apply_filters( 'themes_admin_search_template',       self::search_template() );
		apply_filters( 'themes_admin_theme_single_template', self::theme_single_template() );
		do_action( 'themes_admin_print_template' );
	}

	/**
	 * Template for rendering each Theme element
	 */
	public function theme_template() {
		?>
		<script id="tmpl-theme" type="text/template">
			<?php do_action( 'theme_template_before' ); ?>

			<div class="theme-screenshot">
				<img src="{{ data.screenshot[0] }}" alt="" />
			</div>
			<div class="theme-author"><?php _e( 'By' ); ?> {{ data.author }}</div>
			<h3 class="theme-name">{{ data.name }}</h3>
			<div class="theme-actions">

			<# if ( data.active ) { #>
				<span class="current-label">{{ wp.themes.data.i18n['active'] }}</span>
				<# if ( wp.themes.data.settings['customizeURI'] ) { #>
					<a class="button button-primary hide-if-no-customize" href="{{ wp.themes.data.settings['customizeURI'] }}">{{ wp.themes.data.i18n['customize'] }}</a>
				<# } #>
			<# } else { #>
				<a class="button button-primary activate" href="{{{ data.actions['activate'] }}}">{{ wp.themes.data.i18n['activate'] }}</a>
				<a class="button button-secondary preview" href="{{{ data.actions['customize'] }}}">{{ wp.themes.data.i18n['preview'] }}</a>
			<# } #>

			</div>

			<# if ( data.hasUpdate ) { #>
				<a class="theme-update">{{ wp.themes.data.i18n['updateAvailable'] }}</a>
			<# } #>

			<# if ( ! data.active ) { #>
				<a href="{{{ data.actions.delete }}}" class="delete-theme"></a>
			<# } #>

			<?php do_action( 'theme_template_after' ); ?>
		</script>
		<?php
	}

	/**
	 * Template to render the search form
	 */
	public function search_template() {
		?>
		<script id="tmpl-theme-search" type="text/template">
			<?php do_action( 'theme_search_template_before' ); ?>
			<input type="text" name="theme-search" id="theme-search" placeholder="<?php esc_attr_e( 'Search...' ); ?>" />
			<?php do_action( 'theme_search_template_after' ); ?>
		</script>
	<?php
	}

	/**
	 * Template for single Theme views
	 * Displays full theme information, including description,
	 * author, version, larger screenshots.
	 */
	public function theme_single_template() {
		?>
		<script id="tmpl-theme-single" type="text/template">
			<?php do_action( 'theme_single_template_before' ); ?>

			<div class="theme-backdrop"></div>
			<div class="theme-wrap">
				<div class="theme-utility">
					<div alt="Close overlay" class="back dashicons dashicons-no"></div>
					<div alt="Show previous theme" class="left dashicons dashicons-no"></div>
					<div alt="Show next theme" class="right dashicons dashicons-no"></div>
				</div>

				<div class="theme-screenshots" id="theme-screenshots">
					<div class="screenshot first"><img src="{{ data.screenshot[0] }}" alt="" /></div>
				<#
					if ( _.size( data.screenshot ) > 1 ) {
						_.each ( data.screenshot, function( image ) {
				#>
						<div class="screenshot thumb"><img src="{{ image }}" alt="" /></div>
				<#
						});
					}
				#>
				</div>

				<div class="theme-info">
					<# if ( data.active ) { #>
						<span class="current-label">{{ wp.themes.data.i18n['active'] }}</span>
					<# } #>
					<h3 class="theme-name">{{ data.name }}<span class="theme-version">v{{ data.version }}</span></h3>
					<h4 class="theme-author">By <a href="{{ data.authorURI }}">{{ data.author }}</a></h4>

					<# if ( data.hasUpdate ) { #>
					<div class="theme-update-message">
						<a class="theme-update">{{ wp.themes.data.i18n['updateAvailable'] }}</a>
						<p>{{{ data.update }}}</p>
					</div>
					<# } #>
					<p class="theme-description">{{{ data.description }}}</p>

					<# if ( data.parent ) { #>
						<p class="parent-theme">{{{ data.parent }}}</p>
					<# } #>

					<# if ( data.tags.length !== 0 ) { #>
						<p class="theme-tags">
							<span><?php _e( 'Tags:' ); ?></span>
							{{{ data.tags.join( ', ' ).replace( /-/g, ' ' ) }}}
						</p>
					<# } #>
				</div>
			</div>

			<div class="theme-actions">
				<div class="active-theme">
					<a href="{{{ wp.themes.data.settings.customizeURI }}}" class="button button-primary hide-if-no-customize">{{ wp.themes.data.i18n['customize'] }}</a>
					<?php self::theme_actions_links(); ?>
					<?php do_action( 'themes_admin_action_links' ); ?>
				</div>
				<div class="inactive-theme">
					<a href="{{{ data.actions.activate }}}" class="button button-primary">{{ wp.themes.data.i18n['activate'] }}</a>
					<a href="{{{ data.actions.customize }}}" class="button button-secondary">{{ wp.themes.data.i18n['preview'] }}</a>
				</div>

				<# if ( ! data.active ) { #>
					<a href="{{{ data.actions.delete }}}" class="delete-theme">{{ wp.themes.data.i18n['delete'] }}</a>
				<# } #>
			</div>

			<?php do_action( 'theme_single_template_after' ); ?>
		</script>
	<?php
	}

}

/**
 * Initialize
 */
new THX_38;