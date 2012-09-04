<?php

/**
 * Пользовательские настройки
 *
 * @author AAM
 */
class ControllerSettings extends Controller {
    
	protected $access_list = array(
		"defaultAction" => "authRequired",
		"change" => "authRequired"
	);
	
	/**
	 * Выводит страницу настроек
	 */
	public function defaultAction() {
		$user = new ModelUser();
		$user->initById(Request::$uid);
		
		$this->type_template = "fullPage";
		$this->template[] = "tpl_navbar_auth.php";
		$this->template[] = "tpl_settings.php";
		$this->css_file[] = "/css/reset.css";
		$this->css_file[] = "/css/main_style.css";
		$this->css_file[] = "/css/dark_navbar.css";
		$this->css_file[] = "/css/settings_style.css";
		
		return array('login'=>$user->getLogin(), 'email'=>$user->getEmail());
	}
	
	/**
	 * Меняет параметры учетной записи
	 */
	public function change() {
		$user = new ModelUser();
		$user->initById(Request::$uid);
		
		$old_pass = md5(Config::get("salt") . Request::getPost("old_pass"));
		
		try {
			if(strcmp($user->getPassword(), $old_pass) != 0) throw new Exception("oldpass_invalid");

			$login = Request::getPost("login");
			$email = Request::getPost("email");
			$password = Request::getPost("new_pass");

			if($email) $user->setEmail($email);

			$mapper = new ModelMapper();
			$user_exists = $mapper->find("ModelUser", array('login'=>$login));
			if(count($user_exists) > 0 && $user->getLogin() != $login) throw new Exception("login_alredy_used");
			
			if(!$login || !ModelUser::validLogin($login)) throw new Exception("login_invalid");
			$user->setLogin($login);

			if($password && !ModelUser::validPass($password)) throw new Exception("newpass_invalid");
			if($password) $user->setPassword(md5(Config::get("salt") . $password));

			$user->save();
			return array("result"=>"");
		}
		catch (Exception $e) {
			return array("result"=>$e->getMessage());
		}
	}
}
