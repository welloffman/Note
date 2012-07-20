<?php

/**
 * Контроллер для авторизации
 *
 * @author AAM
 */
class ControllerAuth extends Controller {
	
	protected $access_list = array(
		"defaultAction" => "freeAccess",
		"logout" => "authRequired",
		"login" => "freeAccess"
	);
	
	/**
	 * Формирование страницы авторизации 
	 */
	public function defaultAction() {
		$this->type_template = "fullPage";
		$this->template[] = "tpl_navbar.php";
		$this->template[] = "tpl_main_login.php";
		
		return null;
	}
	
	public function login() {
		$a = new Auth();
		if($a->getAccess()) header("Location: /notes");
		else header("Location: /auth/login");
	}
	
	public function logout() {
		Auth::quit();
		header("Location: /");
	}
}
