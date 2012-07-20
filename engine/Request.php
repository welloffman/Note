<?php

/**
 * Класс для обработки запросов от клиента
 *
 * @author AAM
 */
class Request {
	
	/**
	 * Ид пользователя
	 * @var number 
	 */
	public static $uid = null;
	
	/**
	 * Возвращает строку с указанием действия (класс и метод для выполнения)
	 * @return String 
	 */
	public static function getPostAction() { 
		if(array_key_exists("area", $_POST) && array_key_exists("action", $_POST)) {
			return trim(strip_tags($_POST["area"])) . "__" . trim(strip_tags($_POST["action"]));
		}
		else return "";
	}
	
	/**
	 * Возвращает post параметр по ключу
	 * @param String $key
	 * @return String или null при отсутствии ключа 
	 */
	public static function getPost($key) {
		if(array_key_exists($key, $_POST)) {
			if(is_array($_POST[$key])) return self::checkArray($_POST[$key]);
			else return trim(strip_tags($_POST[$key]));
		}
		else return null;
	}
	
	/**
	 * Возвращает session параметр по ключу
	 * @param String $key
	 * @return String или null при отсутствии ключа 
	 */
	public static function getSession($key) {
		if(array_key_exists($key, $_SESSION)) return trim(strip_tags($_SESSION[$key]));
		else return null;
	}
	
	/**
	 * Возвращает cookie параметр по ключу
	 * @param String $key
	 * @return String или null при отсутствии ключа 
	 */
	public static function getCookie($key) {
		if(array_key_exists($key, $_COOKIE)) return trim(strip_tags($_COOKIE[$key]));
		else return null;
	}
	
	/**
	 * Возвращает get параметр по ключу
	 * @param String $key
	 * @return String или null при отсутствии ключа 
	 */
	public static function getGet($key) {
		if(array_key_exists($key, $_GET)) return trim(strip_tags($_GET[$key]));
		else return null;
	}
	
	/**
	 * Возвращает текст с разрешенными html тегами переданный через post
	 * @return String или null 
	 */
	public static function getPostAllowedHtml($key) {
		if(array_key_exists($key, $_POST)) {
			return trim(strip_tags($_POST[$key],'<strong><em><span><p><address><pre><h1><h2><h3><h4><h5><h6><br><ul><ol><li>'));
		}
		else return null;
	}
	
	/**
	 * Обрабатывает входящие от пользователя массивы
	 * @param Array $array - входящий массив
	 * @return Array 
	 */
	private static function checkArray($array) {
		foreach($array as $i=>$item) {
			if(is_array($item)) {
				$array[$i] = $this->checkArray($item);
			}
			else $array[$i] = trim(strip_tags($item));
		}
		return $array;
	}
	
	/**
	 * Безопасное получение переменной переданной от пользователя
	 * @param $string Строка от польозователя
	 */
	public static function getVar($string) {
		$matches = array();
		preg_match("/^[\w\/]+/", trim(strip_tags($string)), $matches);
		$result = count($matches) > 0 ? $matches[0] : "";
		return $result;
	}
}

?>
