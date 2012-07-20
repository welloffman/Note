<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Conf
 *
 * @author Комп
 */
class Conf {
	/**
	 * Файл - конфиг
	 */
	const C_FILE = "../engine/config.xml";
	/**
	 * @var DOMElement - указатель на корень объекта с конфигом
	 */
	private $dom = null;
	/**
	 * @var DOMElement - элемент на котором стоит указатель
	 */
	private $cur_section = null;

	public function __construct($section_name = null) {
		$this->init();
		if($section_name != null) {
			$this->select($section_name);
		}
	}

	/**
	 * Выбирает первый дом - элемент объект с именем $section_name
	 * @param String $section_name - имя дом  - элемента
	 * @param Boolean $in_current - Искать ли в текущем элементе
	 */
	public function select($section_name, $in_current = false) {
		// Поиск в корневом узле
		if(!$in_current) {
			if($this->dom->getElementsByTagName($section_name)->length > 0)
			$this->cur_section = $this->dom->getElementsByTagName($section_name)->item(0);
		}
		// Поиск в текущем узле
		else {
			if($this->cur_section != null &&
				$this->cur_section->getElementsByTagName($section_name)->length > 0)
			$this->cur_section = $this->cur_section->getElementsByTagName($section_name)->item(0);
		}
	}

	/**
	 * Возвращает значение первого дом - элемента с именем $section_name
	 * @param String $section_name - имя дом - элемента
	 * @return String 
	 */
	public function getVal($section_name) {
		if($this->cur_section == null ||
			$this->cur_section->getElementsByTagName($section_name)->length == 0) return null;
		return $this->cur_section->getElementsByTagName($section_name)->item(0)->nodeValue;
	}

	/**
	 * Возвращает массив значений одноименных дом - элементов текущего элемента
	 * @param String $section_name - имя дом - элементов
	 * @return Array 
	 */
	public function getArray($section_name) {
		if($this->cur_section == null ||
			$this->cur_section->getElementsByTagName($section_name)->length == 0) return null;

		$result = array();
		$elems = $this->cur_section->getElementsByTagName($section_name);
		for ($i=0; $i < $elems->length; $i++){
			$result[] = $elems->item($i)->nodeValue;
		}
		return $result;
	}

	/**
	 * Возвращает массив значений вложенных дом - элементов в массиве одноименных элементов с именем $section_name
	 * @param String $section_name - имя дом - элементов
	 * @return Array 
	 */
	public function getNArray($section_name) {
		if($this->cur_section == null ||
			$this->cur_section->getElementsByTagName($section_name)->length == 0) return null;

		$result = array();
		$row = $this->cur_section->getElementsByTagName($section_name);
		for ($i = 0; $i < $row->length; $i++){
			$elems = $row->item($i)->getElementsByTagName("*");
			for($j = 0; $j < $elems->length; $j++) {
				$result[$i][$elems->item($j)->tagName] = $elems->item($j)->nodeValue;
			}
		}
		return $result;
	}

	/**
	 * Инициализация конфиг-файла
	 */
	private function init() {
		if($this->dom == null) {
			$this->dom = new DOMDocument;
			$this->dom->load(self::C_FILE);
		}
	}
}
?>
