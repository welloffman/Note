<?php

/**
 * Модель для работы с пользователями
 *
 * @author AAM
 */
class ModelUser extends ModelOrm {
	protected $login;
	protected $password;
	protected $created;
	protected $email;
	
	public function initByLogin($login) {
		$mapper = new ModelMapper();
		$result = $mapper->find( "ModelUser", array("login"=>$login), array("options"=>"LIMIT 1") );
		
		if(isset($result[0])) {
			$user = $result[0];
		
			$this->id = $user->id;
			$this->login = $user->login;
			$this->password = $user->password;
			$this->created = $user->created;
			$this->email = $user->email;
		}
	}
	
	/**
	 * Проверка допустимости логина
	 * @param String $login
	 * @return boolean 
	 */
	public static function validLogin($login) {
		if(strlen($login) > 3 && strlen($login) <= 50 && preg_match("~^[\w@\.]+$~", $login)) return true;
		else return false;
	}
	
	/**
	 * Проверка допустимости пароля
	 * @param String $pass
	 * @return boolean 
	 */
	public static function validPass($pass) {
		if(strlen($pass) >= 6 && strlen($pass) <= 20 && preg_match("~^[\w@\.\-\+]+$~", $pass)) return true;
		else return false;
	}
}

?>
