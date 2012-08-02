<?php

/**
 * Класс для регистрации нового пользователя
 *
 * @author AAM
 */
class ControllerRegister extends Controller {
	
	protected $access_list = array(
		"defaultAction" => "freeAccess",
		"reg" => "freeAccess"
	);
	
	/**
	 * Формирование страницы регистрации 
	 */
	public function defaultAction() {
		$this->type_template = "fullPage";
		$this->template[] = "tpl_navbar.php";
		$this->template[] = "tpl_main_register.php";
		
		return null;
	}
	
	/**
	 * Регистрация нового пользователя 
	 * @return String - json строка
	 */
	public function reg() {
		$login = Request::getPost("login");
		$password = Request::getPost("passwd");
		
		if(!$login || !$password) return array("error"=>"invalid_data");
		
		if(!ModelUser::validLogin($login)) return array("error"=>"login");
		if(!ModelUser::validPass($password)) return array("error"=>"pass");
		
		$mapper = new ModelMapper();
		$user_exists = $mapper->find("ModelUser", array('login'=>$login));
		if(count($user_exists) > 0) return array("error"=>"alredy_exists");
		
		$user = new ModelUser();
		$user->setLogin($login);
		$user->setPassword(md5(Config::get("salt") . $password));
		$user->setCreated(date("Y-m-d H:i:s"));
		$user->save();
		
		$general_dir = new ModelDir();
		$general_dir->setTitle("Главный раздел");
		$general_dir->setPath("");
		$general_dir->setUser_id($user->getId());
		$general_dir->save();
		
		Auth::setMemcache();
		$hash = Auth::generateCode(20);
		$session_name = $user->getId() . "_" .$login . "-" . rand(0, 1000);
		
		Auth::$memcache->set($session_name, $hash, MEMCACHE_COMPRESSED, 60*60*24);

		$time = time()+60*60*24;
		setcookie('login', $session_name, $time, '/');
		setcookie('hash', $hash, $time, '/');
	
		return array("result"=>"ok");
	}
}

?>
