<?php

/** 
 * controlpanel panel definition
 *
 * @package		Comodojo Core Applications
 * @author		comodojo.org
 * @copyright	__COPYRIGHT__ comodojo.org (info@comodojo.org)
 * @version		__CURRENT_VERSION__
 * @license		GPL Version 3
 */

$panels = Array(
	"debug" => Array(
		"builder"	=>	"form",
		"icon"		=>	"debug.png",
		"label"		=>	"0130",
		"table"		=>	"options",
		"where"		=>	Array("siteId","=",COMODOJO_UNIQUE_IDENTIFIER),
		"include"	=>	Array("JS_DEBUG","JS_DEBUG_POPUP","JS_DEBUG_DEEP"),
		"note"		=>	Array("name"=>"debug_note","type"=>"info","content"=>"0134")
	)
);

$options = Array(
	"JS_DEBUG"	=>	Array(
		"type"		=>	"OnOffSelect",
		"label"		=>	"0131",
		"required"	=>	true,
		"onclick"	=>	false,
		"options"	=>	false
	),
	"JS_DEBUG_POPUP"		=>	Array(
		"type"		=>	"OnOffSelect",
		"label"		=>	"0132",
		"required"	=>	true,
		"onclick"	=>	false,
		"options"	=>	false
	),
	"JS_DEBUG_DEEP"	=>	Array(
		"type"		=>	"OnOffSelect",
		"label"		=>	"0133",
		"required"	=>	true,
		"onclick"	=>	false,
		"options"	=>	false
	)
);

?>