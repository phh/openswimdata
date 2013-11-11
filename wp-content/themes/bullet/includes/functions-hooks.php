<?php
/**
 * Hooks for the frontend
 *
 * @package WordPress
 * @subpackage Bullet
 *
 * @link http://codex.wordpress.org/Plugin_API/Filter_Reference
 * @link http://codex.wordpress.org/Plugin_API/Action_Reference
 */


/* Page assets (Css and JS) * * * * * * * * * * * * * * * * * * */

/**
 * Loads css and js in the head
 */
function bullet_queue_high_js_and_css() {
	// Register bullet stylesheet
	wp_register_style( 'bullet-css', get_template_directory_uri() . '/css/styles.css', array(), '1.0', 'all' );
	wp_enqueue_style( 'bullet-css' );

	// Responsive stylesheet for those browsers that can read it
	wp_register_style( 'bullet-responsive', get_template_directory_uri() . '/css/media-queries.css', array(), false, '(min-width:20.125em)' );
	wp_enqueue_style( 'bullet-responsive' );
}
add_action( 'wp_enqueue_scripts', 'bullet_queue_high_js_and_css', 1 );

/**
 * Loads css and js at the end of body
 */
function bullet_queue_low_js_and_css() {
	// Own jQuery
	wp_deregister_script( 'jquery' ); // remove default
	wp_register_script( 'jquery', get_template_directory_uri() . '/js/libs/jquery-1.10.2.min.js', array(), '1.10.2', true );
	wp_enqueue_script( 'jquery' );

	// Adding scripts file in the footer
	wp_register_script( 'bullet-js', get_template_directory_uri() . '/js/scripts.js', array( 'jquery' ), '1.0', true );
	wp_enqueue_script( 'bullet-js' );

	// Inbuilt comment reply
	if ( is_singular() && comments_open() && ( get_option( 'thread_comments' ) == 1 ) )
		wp_enqueue_script( 'comment-reply' );
}
add_action( 'wp_enqueue_scripts', 'bullet_queue_low_js_and_css', 999 );


/* Post and post nav * * * * * * * * * * * * * * * * * * */

/**
 * Fixes the Read More in the Excerpts - changes the […] to a Read More link
 * @param string $more Existing read more content
 */
function bullet_excerpt_more( $more ) {
	global $post;
	return '...  <a href="' . get_permalink( $post->ID ) . '" title="' . bullet__( 'Read' ) .' ' . get_the_title( $post->ID ) . '">' . bullet__( 'Read more &raquo;' ) . '</a>';
}
add_filter( 'excerpt_more', 'bullet_excerpt_more' );

/**
 * Removes the p from around imgs
 * @see http://css-tricks.com/snippets/wordpress/remove-paragraph-tags-from-around-images/
 * @param string $content HTML from the WYSIWYG
 */
function bullet_images_ptag( $content ) {
   return preg_replace( '/<p>\s*(<a .*>)?\s*(<img .* \/>)\s*(<\/a>)?\s*<\/p>/iU', '\1\2\3', $content );
}
add_filter( 'the_content', 'bullet_images_ptag' );

/**
 * Removes links around post imgs
 */
function bullet_images_link_none() {
	return 'none';
}
add_action( 'pre_option_image_default_link_type', 'bullet_images_link_none' );


/* Responsive images and embeds and galleries * * * * * * * * * * * * * * * * * * */

/**
 * Makes inline images output with the desired html and without dimensions
 */
get_template_part( 'includes/classes/class.rebuild-images' );

/**
 * Hook into the gallery
 * Maybe use flexslider
 * @param unknown_type $output
 * @param unknown_type $attr
 * @return string
 */
function bullet_post_gallery( $output, $attr ) {

	// VARS - set up, maybe with php conditionals if you need different galliery types in different places

	// avoid the hard cropping of portrait slider images
	$use_flexible_sizing = false;

	// Use flexslider
	$use_flexslider = false;
	$use_flexslider_carousel = false; // show thumbnails underneath
	$use_flexslider_carousel_flexible_sizing = false; // avoid hard cropping in thumbnails.

	// make these the image sizes from functions.php that you want to use
	$size = 'bullet-gallery';
	$thumb_size = 'bullet-gallery-thumb';
	$alt_size = 'bullet-gallery-nocrop'; // used if flexible sizing is true
	$alt_thumb_size = 'bullet-gallery-thumb-nocrop'; // used if flexible sizing is true

	// extract the args from the shortcode and set defaults
	extract( shortcode_atts( array(
		'order'      => 'ASC',
		'orderby'    => 'menu_order',
		'itemtag'    => 'div',
		'icontag'    => 'figure',
		'captiontag' => 'figcaption',
		'ids'        => '',
		'size'       => $size
	), $attr ) );

	// if there are no attachments on the shortcode, stop
	if ( empty( $ids ) )
		return false;

	// Ensure orderby is valid, if given
	if ( isset( $attr['orderby'] ) ) {
		$attr['orderby'] = sanitize_sql_orderby( $attr['orderby'] );
		if ( !$attr['orderby'] )
			unset( $attr['orderby'] );
	}

	// get the attachment data, images only
	$args = array(
		'include' => $ids,
		'post_status' => 'inherit',
		'post_type' => 'attachment',
		'post_mime_type' => 'image',
		'order' => $order,
		'orderby' => $orderby
	);
	$_attachments = get_posts( $args );

	// recreate the array with the attachment ID as the array keys
	$attachments = array();
	foreach ( $_attachments as $key => $val )
		$attachments[$val->ID] = $_attachments[$key];

	// again if there's nothing here, stop
	if ( empty( $attachments ) )
		return false;

	// if it is a feed, just spit out the images
	if ( is_feed() ) {
		$output = "\n";
		foreach ( $attachments as $att_id => $attachment )
			$output .= wp_get_attachment_link( $att_id, $size, true ) . "\n";
		return $output;
	}

	// sanitize the tag and class names
	$itemtag = tag_escape( $itemtag );
	$icontag = tag_escape( $icontag );
	$captiontag = tag_escape( $captiontag );
	$size = sanitize_html_class( $size );

	// output the main gallery
	if( $use_flexslider ) {
		$output .= '<aside class="flexslider slider-wrapper">';
		$output .= '<div class="slides">';
	} else {
		$output .= '<aside class="gallery">';
	}

	foreach ( $attachments as $id => $attachment ) {
		$link = wp_get_attachment_image_src( $id, $size );
		$output .= '<' . $itemtag . ' class="fig">';
		$output .= '<' . $icontag . ' class="gallery-item">';
		$output .= '<img src="' . $link[0] . '" alt="' . get_post_meta( $id, '_wp_attachment_image_alt', true ) . '" />';

		if ( $captiontag && trim( $attachment->post_excerpt ) ) {
			$output .= '<' . $captiontag . ' class="wp-caption-text gallery-caption">';
			$output .= wptexturize( $attachment->post_excerpt );
			$output .= '</' . $captiontag . '>';
		}
		$output .= '</' . $icontag . '>';
		$output .= '</' . $itemtag . '>' . "\n\n";
	}

	if( $use_flexslider )
		$output .= '</div></aside>' . "\n";
	else
		$output .= '</aside>' . "\n";

	// show the carousel flexslider nav if requested
	if( $use_flexslider_carousel ) {
		$output .= '<div id="carousel" class="flexslider"><ul class="slides">';
		foreach ( $attachments as $id => $attachment ) {
			$thumb = wp_get_attachment_image_src( $id, $thumbsize );
			$output .= '<li><img src="' . $thumb[0] . '" alt="" /></li>';
		}
		$output .= '</ul></div>';
	}

	return $output;
}
add_filter( 'post_gallery', 'bullet_post_gallery', 10, 2 );


/* Misc. * * * * * * * * * * * * * * * * * * */

/**
 * Avoids returning random page if empty search string
 */
function bullet_search_filter( $query_vars ) {
	if( isset( $_GET['s'] ) && empty( $_GET['s'] ) )
		$query_vars['s'] = ' ';

	return $query_vars;
}
add_filter( 'request', 'bullet_search_filter' );

