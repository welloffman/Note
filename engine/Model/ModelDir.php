<?php

/**
 * Модель для работы с разделами
 * @author AAM
 */
class ModelDir extends ModelOrm {
	protected $user_id;
	protected $path;
	protected $title;

	/**
	 * Задает заголовок раздела
	 * @param String $title
	 */
	public function setTitle($title) {
		$this->title = $title ? $title : "Новый раздел";
	}

	/**
	 * Возвращает массив с данными для хлебных крошек раздела
	 */
	public function getBreadCrumbs() {
		$result = array();
		$path = $this->path ? json_decode($this->path) : array();
		foreach($path as $id) {
			$dir = new ModelDir();
			if(!$dir->initById($id)) continue;
			$result[] = array("id" => $dir->id, "title" => $dir->title);
		}
		$result[] = array("id" => $this->id, "title" => $this->title);
		return $result;
	}

	/**
	 * Удаляет раздел вместе с записями и вложенными разделами
	 * @return type
	 */
	public function delete() {
		if(!isset($this->id)) return;

		$mapper = new ModelMapper();
		// Удаляем записи раздела
		$child_notes = $mapper->find( "ModelNote", array('dir_id'=>$this->id) );
		foreach($child_notes as $note) {
			$note->delete();
		}

		// Удаляем вложенные разделы, находящиеся непосредственно в данном разделе
		$path = '%"' . $this->id . '"]';
		$direct_child_dirs = $mapper->find( "ModelDir", array('path'=>$path), array("comparison" => "LIKE") );
		foreach($direct_child_dirs as $d) {
			$d->delete();
		}

		parent::delete();
	}

	/**
	 * Возвращает Id раздела - родителя
	 * @return Number
	 */
	public function getParentId() {
		$parents = json_decode($this->path);
		return count($parents) > 0 ? $parents[count($parents) - 1] : null;
	}
	
	/**
	 * Получает корневой раздел пользователя
	 */
	public function initRoot() {
		$mapper = new ModelMapper();
		$dir = $mapper->find( "ModelDir", array('path'=>"", "user_id"=>Request::$uid) );
		
		if(count($dir) == 0) return false;
		
		$this->copyProperties($dir[0]);
	}
	
	/**
	 * Переопределяем для проверки прав доступа к разделу в модели
	 */
	public function initById($dir_id) {		
		try {
			if(!$dir_id)
				throw new ExceptionAccess("ModelDir::initById - не передан идентификатор раздела!");
			
			$mapper = new ModelMapper();
			$result = $mapper->find( "ModelDir", array('id'=>$dir_id) );
		
			if(count($result) == 0 || !Request::$uid || !$result[0]->hasAccess())
				throw new ExceptionAccess("Раздел id=$dir_id не существует или доступ к нему запрещен!");
		}
		catch (ExceptionAccess $e) {
			$e->toLog();
			return false;
		}

		$this->copyProperties($result[0]);
		return true;
	}
	
	/**
	 * Возвращает дочерние разделы
	 * @return array 
	 */
	public function getChildDirs() {
		$mapper = new ModelMapper();
		$path = '%"' . $this->getId() . '"]';
		$dirs = $mapper->find( "ModelDir", array('path'=>$path, "user_id"=>Request::$uid), array("comparison" => "LIKE") );
		return $dirs;
	}
	
	/**
	 * Возвращает дочерние записи
	 * @return array 
	 */
	public function getChildNotes() {
		$mapper = new ModelMapper();
		$notes = $mapper->find( "ModelNote", array("dir_id"=>$this->getId()) );
		return $notes;
	}

	/**
	 * Прверяет доступ пользователь с Ид $user_id к разделу
	 * @return Boolean
	 */
	public function hasAccess() {
		if($this->user_id == Request::$uid) return true;
		return false;
	}
	
	/**
	 * Перенос раздела в другой раздел
	 * @param ModelDir $new_parent_dir Новый родительский раздел
	 */
	public function transfer($new_parent_dir) {
		
		// Проверяем перенос главного раздела
		if(!$this->getPath()) return false;
		// Проверяем на перенос в самого себя
		if($this->getId() == $new_parent_dir->getId()) return false;
		// Проверяем на перенос в своих потомков
		$p = json_decode($new_parent_dir->getPath());
		foreach($p as $id) if($id == $this->getId()) return false;
		// Проверяем перенос в текущий раздел
		if($this->getParentId() == $new_parent_dir->getId()) return false;
		
		// Получаем дочерние разделы
		$mapper = new ModelMapper();
		$path = '%"' . $this->id . '"]';
		$child_dirs = $mapper->find( "ModelDir", array('path'=>$path, "user_id"=>Request::$uid), array("comparison" => "LIKE") );

		// Меняем путь
		$new_path = $new_parent_dir->getPath() ? json_decode($new_parent_dir->getPath()) : array();
		$new_path[] = $new_parent_dir->getId();
		$this->path = json_encode($new_path);
		$this->save();
		
		// Меняем пути во вложенных разделах
		foreach($child_dirs as $cd) {
			$cd->transfer($this);
		}
		
		return true;
	}
	
	/**
	 * Копирование раздела в другой раздел
	 * @param ModelDir $new_parent_dir Новый родительский раздел
	 */
	public function cloneDir($new_parent_dir) {
		
		// Проверяем копирование главного раздела
		if(!$this->getPath()) return false;
		// Проверяем на копирование в самого себя
		if($this->getId() == $new_parent_dir->getId()) return false;
		// Проверяем на копирование в своих потомков
		$p = json_decode($new_parent_dir->getPath());
		foreach($p as $id) if($id == $this->getId()) return false;
		
		// Формируем новый путь
		$new_path = $new_parent_dir->getPath() ? json_decode($new_parent_dir->getPath()) : array();
		$new_path[] = $new_parent_dir->getId();
		
		// Создаем новый раздел
		$new_dir = new ModelDir();
		$new_dir->title = $this->title;
		$new_dir->user_id = $this->user_id;
		$new_dir->path = json_encode($new_path);
		$new_dir->save();
		
		// Получаем дочерние записи и копируем их в новый раздел
		$mapper = new ModelMapper();
		$notes = $mapper->find( "ModelNote", array("dir_id"=>$this->id) );
		foreach($notes as $note) {
			$new_note = new ModelNote();
			$new_note->setDir_id($new_dir->getId());
			$new_note->setTitle($note->getTitle());
			$new_note->setContent($note->getContent());
			$new_note->save();
		}
		
		// Получаем дочерние разделы и копируем их в новый раздел
		$path = '%"' . $this->id . '"]';
		$child_dirs = $mapper->find( "ModelDir", array('path'=>$path, "user_id"=>Request::$uid), array("comparison" => "LIKE") );
		foreach($child_dirs as $cd) {
			$cd->cloneDir($new_dir);
		}
		
		return true;
	}
	
	/**
	 * Копирует свойства из $source
	 * @param ModelDir $source Копируемый объект
	 */
	private function copyProperties($source) {
		$this->id = $source->getId();
		$this->setUser_id($source->getUser_id());
		$this->setPath($source->getPath());
		$this->setTitle($source->getTitle());
	}
}

?>
