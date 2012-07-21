<?php

/**
 * Базовый класс для классов страниц
 *
 * @author user1
 */
class Controller {
	/**
	 * Массив имен файлов шаблонов
	 * @var Array 
	 */
	protected $template = array();
	
	/**
	 * Тип шаблона (целая страница, часть страницы или просто json - данные)
	 * @var string ("fullPage", "jsonData", "block")
	 */
	protected $type_template = "jsonData";
	
	/**
	 * Список функций с указанием нужна ли им авторизация
	 * @var Array 
	 */
	protected $access_list = array();
	
	/**
	 * Для хранения ид пользователя
	 * @var Number 
	 */
	protected $auth_uid = null;
	
	/**
	 * Точка входа в класс, определяет какой метод выполнять 
	 */
	public function doAction($action) {
		if(!$action || !array_key_exists($action, $this->access_list))  $action = "defaultAction";
		
		$method = $this->access_list[$action];
	
		// todo: сделать обработку иключения, если в access_list не найден action
		if(!$method) {
			header("Location: /notes");
			return null;
		}
		
		$result = $this->$method($action);

		$template = new Template($result);
		foreach($this->template as $tpl) {
			$template->addTpl($tpl);
		}
		$template->show($this->type_template);
	}
	
	/**
	 * Запускает метод с проверкой прав доступа
	 * @param String $action 
	 */
	private function authRequired($action) {
		$a = new Auth();
		if($a->getAccess()) {
			$this->auth_uid = $a->getId();
			Request::$uid = $a->getId();
			return $this->$action();
		}
		else {
			$this->type_template = "fullPage";
			$this->template[] = "tpl_navbar.php";
			$this->template[] = "tpl_main_view.php";
			return null;
		}
	}
	
	/**
	 * Запускает метод без проверки прав доступа
	 * @param String $action 
	 */
	private function freeAccess($action) {
		return $this->$action();
	}
	
	/**
	 * Действие по умолчанию, если не пришел action 
	 */
	protected function defaultAction() {
		$this->type_template = "fullPage";
		$this->template[] = "tpl_navbar.php";
		$this->template[] = "tpl_main_view.php";
		return null;
	}
}
?>