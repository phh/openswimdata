<?php
/**
 * Displays the header HTML.
 *
 * @link http://codex.wordpress.org/Stepping_into_Templates#Basic_Template_Files
 * @package WordPress
 * @subpackage Bullet
 */
?><!doctype html>
<!--[if IE 8]><html class="lt-ie9" <?php language_attributes(); ?>><![endif]-->
<!--[if gt IE 8]><!--><html <?php language_attributes(); ?>><!--<![endif]-->
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width,initial-scale=1">
	<title><?php wp_title( '' ); ?></title>
	<?php wp_head(); ?>
	<!--[if lt IE 9]>
		<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/css/ie.css">
		<script src="<?php echo get_template_directory_uri() . '/js/libs/html5shiv.js'; ?>"></script>
	<![endif]-->
</head>

<body <?php body_class(); ?>>

	<div id="container">

		<header role="banner" class="header">

			<div class="wrap">

				<div id="inner-header">

					<nav role="navigation" id="nav">
						<?php bullet_main_nav(); // Adjust using Menus in Wordpress Admin ?>
					</nav>

					<div itemscope itemtype="http://schema.org/Organization" id="logo">
						<a itemprop="url" href="<?php echo home_url(); ?>" rel="nofollow" title="<?php bullet_e('Home'); ?>">
							<img itemprop="logo" src="<?php echo get_template_directory_uri(); ?>/img/logo.png" alt="<?php bloginfo( 'name' ); ?>" />
						</a>
					</div>

				</div>

			</div> <!-- end #inner-header -->

		</header> <!-- end header -->
