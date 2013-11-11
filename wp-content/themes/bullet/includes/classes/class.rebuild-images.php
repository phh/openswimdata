<?php
/**
 * Rebuilds the in-content image tags to...
 *  Remove any width or height attributes added via 'Add Media' and TinyMCE's image edit
 *  Wrap captioned images with <figure> and use <figcaption> for captions - instead of <div> and <p>
 * ...Also gives oembeds containers so they can be resized responsively
 *
 * @since v2
 * @link http://brianswebdesign.com/blog/wordpress/another-look-at-rebuilding-image-tags
 * @package WordPress
 * @subpackage Bullet
 */
class Bullet_Image_Rebuilder
{
	public $caption_class	 = 'wp-caption';
	public $caption_p_class = 'wp-caption-text';
	public $caption_id_attr = false;
	public $caption_padding = 8; // Double of the padding on $caption_class

	public function __construct()
	{
		add_filter( 'img_caption_shortcode', array( &$this, 'img_caption_shortcode' ), 1, 3 );
		add_filter( 'get_avatar', array( &$this, 'recreate_img_tag' ) );
		add_filter( 'the_content', array( &$this, 'the_content') );
		add_filter( 'post_thumbnail_html', array( &$this, 'featured_image_dimensions' ), 10 );
		add_filter( 'embed_oembed_html', array( &$this, 'embed_oembed_html' ), 9999, 3 );
	}

	public function recreate_img_tag( $tag )
	{
		// Supress SimpleXML errors
		libxml_use_internal_errors( TRUE );

		try
		{
			$x = new SimpleXMLElement( $tag );

			// We only want to rebuild img tags
			if( $x->getName() == 'img' )
			{
				// Get the attributes for the replacement tag
				$alt		= (string) $x->attributes()->alt;
				$src		= (string) $x->attributes()->src;
				$classes	= (string) $x->attributes()->class;

				// All images have a source and alt
				$img = '<img src="' . $src . '" alt="' . $alt . '" class="' . $classes . '"';

				// Finish up the img tag
				$img .= ' />';

				return $img;
			}
		}
		catch ( Exception $e ){}

			return $tag; // Tag not an img, so just return it untouched
	}

	/**
	 * Search post content for images to rebuild
	 */
	public function the_content( $html )
	{
		return preg_replace_callback(
			'|(<img.*/>)|',
			array( $this, 'the_content_callback' ),
			$html
		);
	}

	/**
	 * Rebuild an image in post content
	 */
	private function the_content_callback( $match )
	{
		return $this->recreate_img_tag( $match[0] );
	}

	/**
	 * Customize caption shortcode
	 */
	public function img_caption_shortcode( $output, $attr, $content )
	{
		// Not for feed
		if ( is_feed() )
			return $output;

		// Set up shortcode atts
		$attr = shortcode_atts( array(
			'align'	 => 'alignnone',
			'caption' => '',
			'width'	 => ''
		), $attr );

		// Add id and classes to caption
		$attributes = '';

		if( !empty( $attr['id'] ) )
			$attributes .= ' id="' . esc_attr( $attr['id'] ) . '"';

		$attributes .= ' class="' . $this->caption_class . ' ' . esc_attr( $attr['align'] ) . '"';

		// Create caption HTML
		$output = '
			<figure' . $attributes .'>' .
				do_shortcode( $content ) .
				'<figcaption class="' . $this->caption_p_class . '">' . $attr['caption'] . '</figcaption>' .
			'</figure>
		';

		return $output;
	}

	/**
	 * Removes image dimensions on the featured image
	 */
	function featured_image_dimensions( $html ) {
		$html = preg_replace( '/(width|height)=\"\d*\"\s/', '', $html );
		return $html;
	}

	/**
	 * Responsive Embeds in WordPress so embeds scale to container width
	 */
	function embed_oembed_html( $html, $url, $attr ) {
		// Only run this process for embeds that don't require fixed dimensions
		$resize = false;
		$accepted_providers = array(
			'youtube',
			'vimeo',
			'slideshare',
			'dailymotion',
			'viddler.com',
			'hulu.com',
			'blip.tv',
			'revision3.com',
			'funnyordie.com',
			'wordpress.tv',
			'scribd.com',
			'spotify.com'
		);

		// Check each provider
		foreach ( $accepted_providers as $provider ) {
			if ( strstr($url, $provider) ) {
				$resize = true;
				$this_type = $provider;
				break;
			}
		}

		// Not an accepted provider
		if ( !$resize )
			return $html;

		// Stop related vids if youtube, and make branding more discreet
		if( $this_type == 'youtube' )
			$html = str_replace( '?feature=oembed', '?feature=oembed&amp;rel=0&amp;showinfo=0&amp;modestbranding=1', $html );

		// Remove width and height attributes
		$attr_pattern = '/(width|height)="[0-9]*"/i';
		$whitespace_pattern = '/\s+/';
		$embed = preg_replace($attr_pattern, "", $html);
		$embed = preg_replace($whitespace_pattern, ' ', $embed); // Clean-up whitespace
		$embed = trim($embed);

		// Add container around the video, use a <p> to avoid conflicts with wpautop()
		$html = '<div class="rve-embed-container">';
		$html .= '<div class="rve-embed-container-inner">';
		$html .= $embed;
		$html .= "</div></div>";

		return $html;
	}
}

$image_rebuilder = new Bullet_Image_Rebuilder;

