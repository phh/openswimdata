<?php
/*
Plugin Name: OpenSwimData Parser
Description: OpenSwimData part: Parser. Parsing the data that the crawler got us and inserting it into the taxonomies we've already created.
Version: 1.0
Author: Patrick Hesselberg
*/

class OSD_Parser_Plugin {
	function __construct() {
		require plugin_dir_path( __FILE__ ) . 'lib/class-osd-parser.php';

		$this->parser();
	}

	function parser() {
		$parser = new OSD_Parser;
		#$parser->parse();
	}
}

new OSD_Parser_Plugin;

