<?php

/**
 * Класс для работы с записью
 *
 * @author AAM
 */
class ModelNote extends ModelOrm {
	protected $dir_id;
	protected $title;
	protected $content;

	/**
	 * Возвращает массив с ид и заголовком записи
	 * @return Array
	 */
	public function getTitlesData() {
		return array("id"=>$this->id, "title"=>$this->title, "dir_id"=>$this->dir_id);
	}

	/**
	 * Возвращает ид пользователя которому принадлежит запись или null если пользователь не определен
	 * @return Number или null
	 */
	public function getOwnerId() {
		if(!is_numeric($this->id)) return null;

		$dir = new ModelDir();
		if(!$dir->initById($this->dir_id)) return null;

		return $dir->getUser_id();
	}

	/**
	 * Прверяет доступ пользователь с Ид $user_id к записи
	 * @return Boolean
	 */
	public function hasAccess() {
		if($this->getOwnerId() == Request::$uid) return true;
		return false;
	}
	
	/**
	 * Переопределяем для проверки прав доступа к записи в модели
	 */
	public function initById($id) {
		try {
			if(!$id)
				throw new AccessException("ModelNote::initById - не передан идентификатор записи!");
			
			$mapper = new ModelMapper();
			$result = $mapper->find( "ModelNote", array('id'=>$id) );

			if(count($result) == 0 || !Request::$uid || !$result[0]->hasAccess())
				throw new ExceptionAccess("Запись id=$id не существует или доступ к ней запрещен!");
		}
		catch (ExceptionAccess $e) {
			$e->toLog();
			return false;
		}
		
		$this->copyProperties($result[0]);
		return true;
	}
	
	/**
	 * Копирует свойства из $source
	 * @param ModelNote $source Копируемый объект
	 */
	private function copyProperties($source) {
		$this->id = $source->getId();
		$this->setDir_id($source->getDir_id());
		$this->setTitle($source->getTitle());
		$this->setContent($source->getContent());
	}
	
	/**
	 * Возвращает позицию записи на страници 
	 */
	public function getPos() {
		$mapper = new ModelMapper();
		$result = $mapper->find( "ModelPosition", array('item_id'=>$this->id, 'item_type'=>'note'), array("options" => "LIMIT 1") );
		if(isset($result[0])) return $result[0]->getPos();
		else return null;
	}
	
	public function delete() {
		// Удаляем запись из таблицы с позицией
		$mapper = new ModelMapper();
		$pos = $mapper->find( "ModelPosition", array('item_id'=>$this->id, 'item_type'=>'note'), array("options" => "LIMIT 1") );
		if(isset($pos[0])) $pos[0]->delete();
		
		parent::delete();
	}
}

?>
