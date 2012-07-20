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

		$query = "SELECT `id`, `password` FROM `user` WHERE `login`='" . Db::val($login) . "' LIMIT 1";
		$result = Db::query($query); 
		if($result != null && strcmp($result[0]['password'], $pass) == 0) {
			$access = true;

			$hash = Auth::generateCode(20);
			$session_name = $result[0]['id'] . "_" . $login . "-" . rand(0, 1000);
			Auth::$memcache->set($session_name, $hash, MEMCACHE_COMPRESSED, 60*60*24);

			$time = time()+60*60*24;
			setcookie('login', $session_name, $time, '/');
			setcookie('hash', $hash, $time, '/');
		}
		return $access;
	}
}

?>
