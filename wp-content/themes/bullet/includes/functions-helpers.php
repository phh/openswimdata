<?php
/**
 * Useful helper functions to call elsewhere (No hooks please!)
 *
 * @package WordPress
 * @subpackage Bullet
 */

/* Theme Localization * * * * * * * * * * * * * * * * * * */

/**
 * Retrieves the translation of $text, together with the domain
 * @param string $text Text to translate
 */
function bullet__( $text ) {
	return __( $text, 'bullet' );
}

/**
 * Displays the translation of $text, together with the domain
 * @param string $text Text to translate
 */
function bullet_e( $text ) {
	return _e( $text, 'bullet' );
}

/**
 * Retrieve translated string with gettext context
 * @param string $text Text to translate
 */
function bullet_x( $text, $context ) {
	return _x( $text, $context, 'bullet' );
}

/**
 * Retrieve translated string with gettext context
 * @param string $text Text to translate
 */
function bullet_n( $single, $plural, $number ) {
	return _n( $single, $plural, $number, 'bullet' );
}


/* Menus  * * * * * * * * * * * * * * * * * * */

// Loads our custom walker class
get_template_part( 'includes/classes/class.walker-nav-menu' );

/**
 * Returns the main menu, used in header.php
 */
function bullet_main_nav() {
	if( has_nav_menu( 'main_nav' ) ) {
		$walker = new Bullet_Walker_Nav_Menu;

		wp_nav_menu( array(
			'menu' => 'main_nav', // id of menu
			'theme_location' => 'main_nav', // where in the theme it's assigned
			'container' => false,
			'walker' => $walker // customizes the output of the menu
		) );
	}
}


/* Content helpers * * * * * * * * * * * * * * * * * * */

/**
 * Special excerpt
 * Logic - use custom excerpt if exists, if not check for and use 'read more' content, if not use auto excerpt
 * @param int post_id The id of the post
 */
function bullet_excerpt( $post_id = false ) {
	if( !$post_id ) {
		global $post;
		$post_id = $post->ID;
	}

	if( has_excerpt( $post_id ) )
		$excerpt = get_the_excerpt() . ' <span class="read-more"><a href="' . get_permalink( $post_id ) . '">' . bullet__( 'Read more &raquo;' ) . '</a></span>';
	elseif( bullet_has_more() )
		$excerpt = get_the_content( '<span class="read-more">' . bullet__( 'Read more &raquo;' ) . '</span>' );
	else
		$excerpt = get_the_excerpt();

	return '<p>' . $excerpt . '</p>';
}

/**
 * Checks a post to see if it has a read more tag
 */
function bullet_has_more() {
	global $post;

	// Check we're in the right context
	if ( empty( $post ) )
		return;

	// Parse the post content for a more tag
	return (bool) preg_match( '/<!--more(.*?)?-->/', $post->post_content );
}

/**
 * Numeric page navigation instead of just previous/next
 * @param object $q Alternative query object to use if not global wp_query
 * @param string $before Text before the opening nav element
 * @param string $after Text after the opening nav element
 */
function bullet_page_navi( $q = false, $before = '', $after = '' ) {
	if ( $q ) {
		$request = $q->request;
		$posts_per_page = intval( $q->get( 'posts_per_page' ) );
		$paged = intval( $q->get( 'paged' ) );
		$numposts = $q->found_posts;
		$max_page = $q->max_num_pages;
	}
	else {
		global $wp_query;
		$request = $wp_query->request;
		$posts_per_page = intval( get_query_var( 'posts_per_page' ) );
		$paged = intval( get_query_var( 'paged' ) );
		$numposts = $wp_query->found_posts;
		$max_page = $wp_query->max_num_pages;
	}

	if ( $numposts <= $posts_per_page )
		return;

	if( empty( $paged ) || $paged == 0 )
		$paged = 1;

	$pages_to_show = 7;
	$pages_to_show_minus_1 = $pages_to_show-1;
	$half_page_start = floor( $pages_to_show_minus_1/2) ;
	$half_page_end = ceil( $pages_to_show_minus_1/2 );
	$start_page = $paged - $half_page_start;
	if( $start_page <= 0 ) {
		$start_page = 1;
	}

	$end_page = $paged + $half_page_end;
	if( ( $end_page - $start_page ) != $pages_to_show_minus_1 ) {
		$end_page = $start_page + $pages_to_show_minus_1;
	}

	if( $end_page > $max_page ) {
		$start_page = $max_page - $pages_to_show_minus_1;
		$end_page = $max_page;
	}

	if( $start_page <= 0 ) {
		$start_page = 1;
	}

	echo $before . '<nav class="page-navigation"><ol class="bullet_page_navi">';

	if ( $start_page >= 2 && $pages_to_show < $max_page) {
		$first_page_text = "First";
		echo '<li class="bpn-first-page-link"><a href="' . get_pagenum_link() . '" title="' . $first_page_text . '">' . $first_page_text . '</a></li>';
	}

	echo '<li class="bpn-prev-link">';
		previous_posts_link( '<<' );
	echo '</li>';

	for( $i = $start_page; $i  <= $end_page; $i++ ) {
		if( $i == $paged )
			echo '<li class="bpn-current">' . $i . '</li>';
		else
			echo '<li><a href="' . get_pagenum_link( $i ) . '">' . $i . '</a></li>';
	}

	echo '<li class="bpn-next-link">';
		next_posts_link( '>>' );
	echo '</li>';

	if ( $end_page < $max_page ) {
		$last_page_text = "Last";
		echo '<li class="bpn-last-page-link"><a href="' . get_pagenum_link( $max_page ) . '" title="' . $last_page_text . '">' . $last_page_text . '</a></li>';
	}
	echo '</ol></nav>' . $after;
}

/**
 * Related Posts
 * @param int $num Number of related posts to display
 * @param mixed $post_type String or array of post_type(s) to match
 */
function bullet_related_posts( $num = 5, $post_type = 'any' ) {
	global $post;
	$tags = wp_get_post_tags( $post->ID ); // get this posts tags

	if( empty( $tags ) )
		return false;

	$tag_arr = '';

	foreach( $tags as $tag )
		$tag_arr .= $tag->slug . ',';

	$args = array(
		'tag' => $tag_arr,
		'numberposts' => $num,
		'post__not_in' => array( $post->ID )
	);

	if( $post_type )
		$args['post_type'] = $post_type;

	$related_posts = get_posts( $args ); // gets the related posts by tag

	if( empty( $related_posts ) ) {
		wp_reset_postdata();
		return false;
	}

	$output = '<ul id="related_post-list">';
	foreach ( $related_posts as $post ) : setup_postdata( $post );
		$output .= '<li><a href="' . get_permalink() . '" title="' . the_title_attribute( 'echo=0' ) . '">' . get_the_title() . '</a></li>';
	endforeach;
	$output .= '</ul>';
	wp_reset_postdata();
	return $output;
}


/* Other helpers * * * * * * * * * * * * * * * * * * */

/**
 * Tests if current page is the login page
 */
function bullet_is_login_page() {
	return in_array( $GLOBALS['pagenow'], array( 'wp-login.php', 'wp-register.php' ) );
}

