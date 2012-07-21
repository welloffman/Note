<?php
/**
 * Маршрутизатор
 *
 * @author AAM
 */
class Router {
	/**
	 * Имя класса
	 * @var String 
	 */
	private $class_name;
	/**
	 * Имя метода
	 * @var String 
	 */
	private $method_name;

	public function __construct() { 
		$uri_data = $this->getUriData();
		$this->class_name = "Controller" . ucfirst($uri_data["area"]);
		$this->method_name = $uri_data["action"];
		
		if(!class_exists($this->class_name)) $this->class_name = "ControllerMain";
	}
	
	/**
	 * Создает экземпляр класса и запускает метод переданый от клиента
	 */
	public function forward() {
		$class = $this->class_name;
		
		$obj = new $class();
		$obj->doAction($this->method_name);
	}
	
	/**
	 * Распарсивает адресную строку и возвращает результат в виде массива
	 * @return Array 
	 */
	private function getUriData() {
		$uri_data = preg_split('/\//', $_SERVER['REQUEST_URI'], 3, PREG_SPLIT_NO_EMPTY);
		$count = count($uri_data);

		$data =  array();
		$data["area"] = ($count > 0 && !empty($data["area"])) ? Request::getVar($uri_data[0]) : "notes";
		$data["action"] = $count > 1 ? Request::getVar($uri_data[1]) : "";
		return $data; 
	}
}

?>
