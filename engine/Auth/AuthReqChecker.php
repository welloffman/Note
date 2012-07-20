<?php

/**
 * Для проверки допуска к запросам от авторизованного клиента
 * @author AAM
 */
class AuthReqChecker implements AuthChecker {
	
	/**
	 * Проверяем авторизованного пользователя
	 * @return boolean 
	 */
	public function check() {
		Auth::setMemcache();
		$access = false;
		
		$login = Request::getCookie("login");
		$hash = Request::getCookie("hash");
		$m_login = Auth::$memcache->get($login);

		if(!empty($m_login) && strcmp($m_login, $hash) == 0) $access = true;
		else {
			setcookie("login", "", time() - 1);
			setcookie("hash", "", time() - 1);
		}
		
		return $access;
	}
}

?>
