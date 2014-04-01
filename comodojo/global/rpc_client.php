<?php

/** 
 * The RPC client. It's able to talk in XML, JSON and YAML.
 * 
 * Client can be forced to use special JSON-ENCRYPTED mode with other comodojo installations.
 * 
 * @package		Comodojo PHP Backend
 * @author		comodojo.org
 * @copyright	__COPYRIGHT__ comodojo.org (info@comodojo.org)
 * @version		__CURRENT_VERSION__
 * @license		GPL Version 3
 */

 /*
  * 
  * 2003 Unknown transport
  * 2004 Wrong method (not scalar)
  * 2005 Bad parameters (not array)
  * 2006 Invalid request ID
  * 2007 Invalid response ID:
  * 2008 Error processing request: 
  */
 
comodojo_load_resource('http');
 
class rpc_client {
	
	private $server = false;
	
	private $port = 80;
	
	private $mode = 0;
	
	private $key = false;
	
	private $transport = 'XML';
	
	private $http_method = 'POST';
	
	private $is_native_rpc = false;
	
	private $id = true;
	
	public function __construct($server=COMODOJO_EXTERNAL_RPC_SERVER, $port=COMODOJO_EXTERNAL_RPC_PORT, $http_method = 'POST', $mode=COMODOJO_EXTERNAL_RPC_MODE, $transport=COMODOJO_EXTERNAL_RPC_TRANSPORT, $key=COMODOJO_EXTERNAL_RPC_KEY) {
		
		switch (strtoupper($transport)) {
			case 'XML':
				$this->transport = 'XML';
				if (!function_exists('xmlrpc_encode_request')) {
					comodojo_load_resource('xmlRpcEncoder');
					comodojo_load_resource('xmlRpcDecoder');
					$this->is_native_rpc = false;
				}
				else $this->is_native_rpc = true;
			break;
				
			case 'JSON':
				$this->transport = 'JSON';
			break;
			
			case 'YAML':
				$this->transport = 'YAML';
			break;
			
			default:
				throw new Exception("Unknown transport", 1);
			break;
		}
		
		$this->server = $server;
		$this->port = $port;
		$this->http_method = $http_method;
		
		if ($mode === 'shared') {
			comodojo_load_resource('Crypt/AES.php');
			$this->mode = $mode;
			$this->key = $key;
		}
			
/*
			 include('Crypt/AES.php');
 *
 *    $aes = new Crypt_AES();
 *
 *    $aes->setKey('abcdefghijklmnop');
 *
 *    $size = 10 * 1024;
 *    $plaintext = '';
 *    for ($i = 0; $i < $size; $i++) {
 *        $plaintext.= 'a';
 *    }
 *
 *    echo $aes->decrypt($aes->encrypt($plaintext));
 */
		
	}
	
	public function send($method, $parameters, $id=true) {
		
		if (!is_scalar($method)) {
			comodojo_debug("Wrong method (not scalar)","ERROR","rpc_client");
			throw new Exception("Wrong method (not scalar)", 2004);
		}
		
		if (!is_array($parameters)) {
			comodojo_debug("Bad parameters (not array)","ERROR","rpc_client");
			throw new Exception("Bad parameters (not array)", 2005);
		}
		
		try {
			switch (strtoupper($transport)) {
				case 'XML':
					$response = $this->send_xml($method,$parameters);
				break;
					
				case 'JSON':
					$response = $this->send_json($method,$parameters,$id);
				break;
				
				case 'YAML':
					$response = $this->send_yaml($method,$parameters,$id);
				break;
				
				default:
					comodojo_debug("Unknown transport","ERROR","rpc_client");
					throw new Exception("Unknown transport", 2003);
				break;
			}
		}
		catch (Exceptionx $e) {
			throw $e;
		}
		
		return $response;
		
	}
	
	private function send_xml($method, $parameters) {
		if ($this->_nativeRPC) {
			$request = xmlrpc_encode_request($method,$params,array('encoding',COMODOJO_DEFAULT_ENCODING));
		}
		else {
			$encoder = new xmlRpcEncoder($method);
			$encoder->auto_add_values($parameters);
			$request = $encoder->getData();
		}
		
		try {
			$received = $this->send_data($request, 'text/xml');
		}
		catch (Exceptionx $e) {
			throw $e;
		}
		
		$result = explode("<methodResponse>", $received);
		$result = "<methodResponse>".$result[1];
		
		if ($this->_nativeRPC) {
			$decoded = xmlrpc_decode($result);
		    if (is_array($decoded) && xmlrpc_is_fault($decoded)) throw new Exception('RPC Conversation error: ('.$response['faultCode'].') '.$response['faultString'], 2003);
		}
		else {
			$decoder = new xmlRpcDecoder();
			$decoded = $decoder->decode($result);
			if (is_numeric($decoded) AND @intval($decoded) == -1) throw new Exception('RPC Conversation error: (-) '.$decoder->getFault(), 2003);
		}
		return $decoded;
		
	}
	
	private function send_json($method, $parameters, $id) {
		
		switch (true) {
			case ($id === true): $this->id = random(16); break; // <= RANDOM ID HERE
			case (is_null($id)): $this->id = null; break; // <= NOTIFICATION HERE
			case (is_integer($id)): $this->id = $id; break; // <= STATIC ID HERE
			default:
				comodojo_debug("Invalid request ID","ERROR","rpc_client");
				throw new Exception("Invalid request ID", 2006);
			break;
		}
		
		$request = is_null($this->id) ? array2json(array('jsonrpc' => '2.0', 'method' => $method, 'params' => $parameters)) : array2json(array('jsonrpc' => '2.0', 'method' => $method, 'params' => $parameters, 'id' => $this->id));
		
		try {
			$received = $this->send_data($request, 'application/json');
		}
		catch (Exceptionx $e) {
			throw $e;
		}
		
		if (!is_null($this->id)) {
			
			$response = json2array($received);
			
			if ($response['id'] != $this->id) {
				comodojo_debug("Invalid response ID: sent ".$this->id." received ".$response['id'],"ERROR","rpc_client");
				throw new Exception("Invalid response ID: sent ".$this->id." received ".$response['id'], 2007);
			}
			if (isset($response['error'])) {
				comodojo_debug("Error processing request: (".$response['error']['code'].") ".$response['error']['message']." - ".(isset($response['error']['data']) ? $response['error']['data'] : ""),"ERROR","rpc_client");
				throw new Exception("Error processing request: (".$response['error']['code'].") ".$response['error']['message']." - ".(isset($response['error']['data']) ? $response['error']['data'] : ""), 2008);
			}
			
			return $response['result'];
			
		} else return true;
		
	}
	
	private function send_yaml($method, $parameters, $id) {
		
	}

	private function send_data($data, $contentType) {
		
		if ($this->mode === 'shared') {
			$aes = new Crypt_AES();
			$aes->setKey($this->key);
			//$data = Array('comodojo.encrypted'=>true,'transport'=>$this->transport, 'payload'=>$aes->encrypt($data));
			$data = 'comodojo_encrypted_envelope-'.$aes->encrypt($data);
		}
		
		try {
			$sender = new http();
			$sender->address = $this->server;
			$sender->port = $this->port;
			$sender->method = strtoupper($this->http_method);
			$sender->encoding = COMODOJO_DEFAULT_ENCODING;
			if (strtoupper($this->http_method) == 'GET') $sender->contentType = $contentType;
			$received = $sender->send($data);
		}
		catch (Exception $e) {
			comodojo_debug("Cannot init sender: ".$e->getMessage(),"ERROR","rpc_client");
			throw new Exception("Cannot init sender: ".$e->getMessage(), 2002);
		}
		if ($this->mode === 'shared') {
			return $aes->decrypt($received);
		}
		else return $received;
	}

}

/**
 * Sanity check for CoMoDojo loader
 * 
 * @define function loadHelper_rpc_client
 */
 function loadHelper_rpc_client() { return false; }

?>