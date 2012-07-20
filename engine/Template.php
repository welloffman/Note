<?php

/**
 * Класс для формирования и вывода шаблона
 *
 * @author AAM
 */
class Template {
	private $tpl = array();
	private $json_data;
	
	/**
	 * Сохраняет данные, которые нужно передать с шаблоном клиенту
	 * @param multiply $data Данные
	 */
	public function __construct($data = null) {
		$this->json_data = $data ? json_encode ($data) : "";
	}
	
	/**
	 * Проверяет имя шаблона и добавляет его в массив шаблонов для вывода единого шаблона собранного из кусочков
	 * @param string $tpl 
	 */
	public function addTpl($tpl) {
		$filename = "../templates/" . $tpl; 
		if(is_file($filename)) $this->tpl[] = $filename;
	}
	
	/**
	 * Определяет по параметру необходимый алгоритм вывода шаблона (Возможны значения type: "fullPage", "jsonData", "block")
	 */
	public function show($type = "jsonData") {
		if(method_exists($this, $type)) $this->$type();
		else echo "Неизвестный тип шаблона: $type";
	}
	
	/**
	 * Выводит шаблон с футером, хедером и данными для клиента
	 */
	private function fullPage() {
		require_once "../templates/tpl_header.php";
		
		foreach($this->tpl as $template) {
			require $template;
		}
		
		if($this->json_data) echo '<div id="json_block">' . $this->json_data . '</div>';
		
		require_once "../templates/tpl_footer.php";
		
	}
	
	/**
	 * Выводит данные в виде json строки
	 */
	private function jsonData() {
		echo $this->json_data;
	}
	
	/**
	 * Выводит фрагмент шаблона для динамической подгрузки ajax запросами и данные для клиента
	 */
	private function block() {
		foreach($this->tpl as $template) {
			require $template;
		}
		
		if($this->json_data) echo '<div id="json_block">' . $this->json_data . '</div>';
	}
}
