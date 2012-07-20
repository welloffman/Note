<?php
/**
 * Класс для главной страницы
 *
 * @author AAM
 */
class ControllerMain extends Controller {
	
	protected $access_list = array(
		"defaultAction" => "freeAccess"
	);
	
	/**
	 * Формирование данных для главной страницы
	 */
	public function defaultAction() {
		$this->type_template = "fullPage";
		$this->template[] = "tpl_navbar.php";
		$this->template[] = "tpl_main_view.php";
		return null;
	}
}

?>
