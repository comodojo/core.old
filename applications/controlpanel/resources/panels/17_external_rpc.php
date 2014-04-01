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

function get_external_rpc_mode() {
	return Array(
		Array("label"=>"PlainText", "id"=>'plain'),
		Array("label"=>"SharedKey", "id"=>'shared')
	);
}

function get_external_rpc_transport() {
	return Array(
		Array("label"=>"XML", "id"=>"XML"),
		Array("label"=>"JSON", "id"=>"JSON")
	);
}
 
$panels = Array(
	"external_rpc" => Array(
		"builder"	=>	"form",
		"icon"		=>	"external_rpc.png",
		"label"		=>	"ext_0",
		"table"		=>	"options",
		"where"		=>	Array("siteId","=",COMODOJO_UNIQUE_IDENTIFIER),
		"include"	=>	Array("EXTERNAL_RPC_SERVER","EXTERNAL_RPC_PORT","EXTERNAL_RPC_MODE","EXTERNAL_RPC_TRANSPORT","EXTERNAL_RPC_KEY")
	)
);

$options = Array(
	"EXTERNAL_RPC_SERVER"	=>	Array(
		"type"		=>	"TextBox",
		"label"		=>	"ext_1",
		"required"	=>	false,
		"onclick"	=>	false,
		"options"	=>	false
	),
	"EXTERNAL_RPC_PORT"		=>	Array(
		"type"		=>	"NumberTextBox",
		"label"		=>	"ext_2",
		"required"	=>	true,
		"onclick"	=>	false,
		"options"	=>	false,
		"min"		=>	1,
		"max"		=>	65535
	),
	"EXTERNAL_RPC_MODE"		=>	Array(
		"type"		=>	"Select",
		"label"		=>	"ext_3",
		"required"	=>	true,
		"onclick"	=>	false,
		"options"	=>	get_external_rpc_mode()
	),
	"EXTERNAL_RPC_TRANSPORT" 		=>	Array(
		"type"		=>	"Select",
		"label"		=>	"ext_4",
		"required"	=>	false,
		"onclick"	=>	false,
		"options"	=>	get_external_rpc_transport()
	),
	"EXTERNAL_RPC_KEY"	=>	Array(
		"type"		=>	"TextBox",
		"label"		=>	"ext_5",
		"required"	=>	false,
		"onclick"	=>	false,
		"options"	=>	false
	)
);

?>