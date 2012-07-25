<?php
/*
 * Класс родитель для классов связаных с базой
 */
class ModelOrm {
	protected $id;

	/**
	 * Универсальный геттер/сеттер
	 * @param String $name - имя неопределенного метода
	 * @param String $args - параметры неопределенного метода
	 * @return Multiply
	 */
	public function __call($name, $args) {
		if (preg_match('/^(get|set)(\w+)/', strtolower($name), $match) && $attribute = $this->validateAttribute($match[2])) {
			if ('get' == $match[1]) {
				return $this->$attribute;
			} 
			else {
				$this->$attribute = $args[0];
			}
		} 
		else {
			throw new Exception('Call to undefined method ' . get_class($this) . '::'.$name.'()');
		}
	}
	
	/**
	 * Проверка на существование свойства в классе
	 * @param String $name - имя свойства
	 * @return String 
	 */
	protected function validateAttribute($name) {
		if (in_array( strtolower($name), array_keys( get_class_vars(get_class($this)) ) )) {
			return strtolower($name);
		}
	}
	
	/**
	 * Защита от перезаписи Id
	 * @param String $id - Id записи
	 */
	public function setId($id) {
		if (!$this->id) {
			$this->id = $id;
		}
	}
	
	/**
	 * Запись объекта в базу
	 */
	public function save() {
		$mapper = new ModelMapper();
        if($this->getId()) $mapper->update($this);
        else $mapper->create($this);
	}
	
	/**
	 * Удаляет объект из базы
	 */
	public function delete() {
		if($this->getId()) {
			$mapper = new ModelMapper(); 
			$mapper->delete($this);
		}
		else {
			echo "Невозможно определить Id объекта!";
		}
	}
	
	/**
	 * Возвращает все нестатические свойства указанного объекта
	 * @return Array 
	 */
	public function getAllVars() {
		return get_object_vars($this);
	}
	
	/**
	 * Инициализация объекта
	 * @param Number $id
	 * @return Boolean 
	 */
	public function initById($id) {
		$mapper = new ModelMapper();
		$result = $mapper->find( get_class($this), array('id'=>$id) );
		
		if(count($result) == 0) return false;
		
		$vars = get_object_vars($result[0]);
		foreach($vars as $prop_name=>$value) {
			$method = "set" . $prop_name;
			$this->$method($value);
		}
		
		return true;
	}
}
?>
