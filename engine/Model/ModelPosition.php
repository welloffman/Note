<?php

/**
 * Класс для работы с позицией записей и разделов
 *
 * @author AAM
 */
class ModelPosition extends ModelOrm {
	protected $item_id;
	protected $item_type;
	protected $pos;
	
	/**
	 * Заполняет объект данными по $id и типу
	 * @param Integer $id - ид записи или раздела
	 * @param String $type - тип (dir или note)
	 * @return boolean
	 */
	public function init($id, $type) {
		$mapper = new ModelMapper();
		$result = $mapper->find( "ModelPosition", array('item_id'=>$id, 'item_type'=>$type), array("options" => "LIMIT 1") );
		if(isset($result[0])) {
			$this->copyProperties ($result[0]);
			return true;
		}
		else return false;
	}
	
	/**
	 * Копирует свойства из $source
	 * @param ModelPosition $source Копируемый объект
	 */
	private function copyProperties($source) {
		$this->id = $source->getId();
		$this->setItem_id($source->getItem_id());
		$this->setItem_type($source->getItem_type());
		$this->setPos($source->getPos());
	}
}