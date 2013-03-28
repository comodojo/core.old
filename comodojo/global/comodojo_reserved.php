<?php

/**
 * Reserved "comodojo" application and mehtods.
 * 
 * It serves requests in the reserver comodojo application space, such as comodojo.login or comodojo.version
 *
 * @package		Comodojo ServerSide Core Packages
 * @author		comodojo.org
 * @copyright	__COPYRIGHT__ comodojo.org (info@comodojo.org)
 * @version		__CURRENT_VERSION__
 * @license		GPL Version 3
 */

comodojo_load_resource('application');

class comodojo_reserved extends application {
	
	public function init() {
		$this->add_application_method('login', 'login', Array('userName','userPass'), '',false);
		$this->add_application_method('logout', 'logout', Array(), '',false);
		$this->add_application_method('applications', 'applications', Array(), '',false);
		$this->add_application_method('version', 'version', Array(), '',false);
	}
	
	public function login($params) {
		if (is_null(COMODOJO_USER_ID)) throw new Exception("Unknown user or password mismatch", 2308);
		return Array(
			"userId" => COMODOJO_USER_ID,
			"userName" => COMODOJO_USER_NAME,
			"userRole" => COMODOJO_USER_ROLE,
			"completeName" => COMODOJO_USER_COMPLETE_NAME,
			"gravatar" => COMODOJO_USER_GRAVATAR,
			"email" => COMODOJO_USER_EMAIL,
			"birthday" => COMODOJO_USER_BIRTHDAY,
			"gender" => COMODOJO_USER_GENDER,
			"url" => COMODOJO_USER_URL
		);
	}
	
	public function logout($params) {
		return true;
	}
	
	public function applications($params) {
		comodojo_load_resource('role_mapper');
		$mapper = new role_mapper();
		return $mapper->get_allowed_applications();
	}
	
	public function version($params) {
		return comodojo_version(isset($params['v']) ? $params['v'] : 'ALL');
	}

	
}

?>