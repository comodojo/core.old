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

function get_cron_notification_options() {
	return Array(
		Array(
			"name"	=>	"Never",
			"value"	=>	"DISABLED"
		),
		Array(
			"name"	=>	"Always (each cycle)",
			"value"	=>	"ALWAYS"
		),
		Array(
			"name"	=>	"On failures",
			"value"	=>	"FAILURES"
		)
	);
}

$panels = Array(
	"cron" => Array(
		"builder"	=>	"form",
		"icon"		=>	"cron.png",
		"label"		=>	"0200",
		"table"		=>	"options",
		"where"		=>	Array("siteId","=",COMODOJO_UNIQUE_IDENTIFIER),
		"include"	=>	Array("CRON_ENABLED","CRON_MULTI_THREAD_ENABLED","CRON_NOTIFICATION_MODE","CRON_NOTIFICATION_ADDRESSES")
	)
);

$options = Array(
	"CRON_ENABLED"		=>	Array(
		"type"		=>	"OnOffSelect",
		"label"		=>	"0201",
		"required"	=>	true,
		"onclick"	=>	false,
		"options"	=>	false
	),
	"CRON_MULTI_THREAD_ENABLED"		=>	Array(
		"type"		=>	"OnOffSelect",
		"label"		=>	"0202",
		"required"	=>	true,
		"onclick"	=>	false,
		"options"	=>	false
	),
	"CRON_NOTIFICATION_MODE" =>	Array(
		"type"		=>	"Select",
		"label"		=>	"0203",
		"required"	=>	false,
		"onclick"	=>	false,
		"options"	=>	get_cron_notification_options()
	),
	"CRON_NOTIFICATION_ADDRESSES"=>	Array(
		"type"		=>	"EmailTextBox",
		"label"		=>	"0204",
		"required"	=>	false,
		"onclick"	=>	false,
		"options"	=>	false
	)
);

?>