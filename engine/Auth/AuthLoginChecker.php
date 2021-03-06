<?php
/**
 * Проверяет авторизацию при получении формы входа
 *
 * @author AAM
 */
class AuthLoginChecker implements AuthChecker {
	
	/**
	 * Проверяем запрос авторизации
	 * @return boolean 
	 */
	public function check() {
		Auth::setMemcache();
		$access = false;
		
		$login = Request::getPost("login");
		$pass = Request::getPost("passwd");

		$user = new ModelUser();
		$user->initByLogin($login);
		
		if($user->getPassword() && strcmp($user->getPassword(), md5(Config::get("salt") . $pass)) == 0) {
			$access = true;

			$hash = Auth::generateCode(20);
			$session_name = $user->getId() . "_" . $login . "-" . rand(0, 1000);
			Auth::$memcache->set($session_name, $hash, MEMCACHE_COMPRESSED, 60*60*24*30);

			$time = time()+60*60*24*30;
			setcookie('login', $session_name, $time, '/');
			setcookie('hash', $hash, $time, '/');
		}
		return $access;
	}
}

?>
