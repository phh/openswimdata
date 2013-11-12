<?php
/*
Plugin Name: Swim Rankings
Description: Parse swimrankings.net and set up cron jobs to save the data
Version: 0.1
Author: Patrick Hesselberg
*/
return;
$time_start = time(); 

register_activation_hook( __FILE__, array( 'Swimrankings_Parser', 'activate' ) );

function sr_e( $text ) {
	_e( $text, 'swimrankings' );
}
function sr__( $text ) {
	return __( $text, 'swimrankings' );
}

class Swimrankings {
	static public $PLUGIN_URL;
	static public $PLUGIN_DIR;

	function __construct() {
		$this->init();
	}

	function init() {
		$this->set_vars();
		$this->get_libs();
	}

	function set_vars() {
		self::$PLUGIN_URL = plugins_url( '/', __FILE__ );
		self::$PLUGIN_DIR = plugin_dir_path( __FILE__ );
	}

	function get_libs() {
		if( WP_DEBUG )
			include self::$PLUGIN_DIR . 'lib/krumo/class.krumo.php';

		require_once self::$PLUGIN_DIR . 'lib/simple_html_dom.php';
		require_once self::$PLUGIN_DIR . 'lib/class.swimrankings-parser.php';
	}

	
}

new Swimrankings;

$sr = new Swimrankings_Parser;
$sr->set_url( Swimrankings_Parser::RANK_DEN );
$sr->request();

$urls = array();
$styles_urls = array();
foreach( $sr->getGendersParam() as $kgender => $gender ) {
	foreach( $sr->getPoolTypesParam() as $pool ) {
		foreach( $sr->getYearsParam() as $year ) {
			$urls[] = $sr::SWIMRANKING_BASE . '&' . $gender . '&' . $pool . '&' . $year;

			$sr->set_url( end( $urls ) );
			$sr->request();

			foreach( $sr->getStyles() as $kstyle => $style ) {
				$url = end( $urls ) . '&rankingClubId=' . $kstyle;
				$styles_urls[] = $url;

				#$sr->set_url( $url );
				#$sr->request();
				
				//------------------------------
				#$sr->parse();
				
				#foreach ($sr->get_swimmers() as $swimmer ) {}
			}
			die(krumo($styles_urls));
		}
	}
}
krumo( $styles_urls );

$time_end = time();
$execution_time = $time_end - $time_start;
echo '<b>Total Execution Time:</b> '.$execution_time.' Seconds';
die();
die();

