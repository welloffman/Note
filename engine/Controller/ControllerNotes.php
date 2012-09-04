<?php
/**
 * Страница записи
 *
 * @author AAM
 */
class ControllerNotes extends Controller {

	protected $access_list = array(
		"defaultAction" => "authRequired",
		"refreshDir" => "authRequired",
		"addDir" => "authRequired",
		"getNoteContent" => "authRequired",
		"addNote" => "authRequired",
		"editNote" => "authRequired",
		"deleteItem" => "authRequired",
		"renameDir" => "authRequired",
		"renameNote" => "authRequired",
		"copy" => "authRequired",
		"cut" => "authRequired"
	);

	/**
	 * Выводит страницу записи
	 * @return Array
	 */
	protected function defaultAction() {
		$root_dir = new ModelDir();
		$root_dir->initRoot();

		$dirs_data = $this->refreshDir($root_dir->getId());

		$this->type_template = "fullPage";
		$this->template[] = "tpl_navbar_auth.php";
		$this->template[] = "tpl_notes.php";
		$this->template[] = "tpl_modal_window.php";
		
		return $dirs_data;
	}

	/**
	 * Возвращает массив с данными по разделу
	 * @param Number $id - id раздела
	 * @return Array
	 */
	protected function refreshDir($id = null) {
		$dir_id = $id ? $id : Request::getPost("dir_id");

		$d = new ModelDir();
		if(!$d->initById($dir_id)) return array("dirs_data" => "", "crumbs" => "", "notes_data"=>"");

		// Получаем хлебные крошки
		$crumbs = $d->getBreadCrumbs();

		// Получаем дочерние разделы
		$cur_dirs = $d->getChildDirs();
		$dirs_data = array();
		foreach($cur_dirs as $dir) $dirs_data[] = $dir->getAllVars();

		// Получаем дочерние записи
		$notes = $d->getChildNotes();
		$notes_data = array();
		foreach($notes as $note) $notes_data[] = $note->getTitlesData();

		return array("dirs_data" => $dirs_data, "crumbs" => $crumbs, "notes_data"=>$notes_data, "dir_title"=>$d->getTitle());
	}

	/**
	 * Добавление нового раздела
	 */
	protected function addDir() {
		$parent_dir = new ModelDir();
		if( !$parent_dir->initById(Request::getPost("parent_dir")) ) return array("dir_id" => "");

		$path = $parent_dir->getPath() ? json_decode($parent_dir->getPath()) : array();
		$path[] = $parent_dir->getId();

		$dir = new ModelDir();
		$dir->setUser_id(Request::$uid);
		$dir->setPath(json_encode($path));
		$dir->setTitle(Request::getPost("title"));
		$dir->save();

		return array("dir_id" => $dir->getId());
	}

	/**
	 * Добавление новой записи
	 */
	protected function addNote() {
		$dir = new ModelDir();
		
		if( $dir->initById(Request::getPost("parent_dir")) ) {
			$note = new ModelNote();
			$note->setDir_id($dir->getId());
			$note->setTitle(Request::getPost("title") ? Request::getPost("title") : "Новая запись");
			$note->setContent(Request::getPostAllowedHtml("content"));
			$note->save();
		}
		
		return array("dir_id" => Request::getPost("parent_dir"));
	}

	/**
	 * Изменение записи
	 */
	protected function editNote() {
		$note = new ModelNote();
		$dir = new ModelDir();
		
		if( $note->initById(Request::getPost("id")) && $dir->initById(Request::getPost("parent_dir")) ) {
			$note->setDir_id($dir->getId());
			$note->setTitle(Request::getPost("title") ? Request::getPost("title") : "Запись");
			$note->setContent(Request::getPostAllowedHtml("content"));
			$note->save();
		}
		
		return array("dir_id" => Request::getPost("parent_dir"));
	}

	/**
	 * Получение контента записи
	 */
	protected function getNoteContent() {
		$note = new ModelNote();
		if( $note->initById(Request::getPost("id")) ) return $note->getContent();
		else return "";
	}

	/**
	 * Удаление записей и разделов
	 */
	protected function deleteItem() {
		
		$dirs = is_array(Request::getPost("dirs")) ? Request::getPost("dirs") : array();
		foreach($dirs as $dir_id) {
			$dir = new ModelDir();
			if($dir->initById($dir_id)) {
				$dir->delete();
			}
		}

		$notes = is_array(Request::getPost("notes")) ? Request::getPost("notes") : array();
		foreach($notes as $note_id) {
			$note = new ModelNote();
			if($note->initById($note_id)) {
				$note->delete();
			}
		}

		return array();
		
	}

	/**
	 * Изменяет заголовок раздела
	 */
	protected function renameDir() {
		$dir = new ModelDir();
		if( $dir->initById(Request::getPost("id")) ) {
			$dir->setTitle(Request::getPost("title"));
			$dir->save();
			return $dir->getParentId();
		}
		else return "";
	}

	/**
	 * Изменяет заголовок записи
	 */
	protected function renameNote() {
		$note = new ModelNote();
		if( $note->initById(Request::getPost("id")) ) {
			$note->setTitle(Request::getPost("title"));
			$note->save();
			return $note->getDir_id();
		}
		else return "";
	}
	
	/**
	 * Производит копирование разделов и записей
	 */
	protected function copy() {
		$new_parent_id = Request::getPost("new_parent_id");
		$dirs = Request::getPost("dirs");
		$notes = Request::getPost("notes");

		$new_parent_dir = new ModelDir();
		if( $new_parent_dir->initById($new_parent_id) ) {
			foreach($dirs as $dir_id) {				
				$dir = new ModelDir();
				if($dir->initById($dir_id)) $dir->cloneDir($new_parent_dir);
			}

			foreach($notes as $note_id) {
				$note = new ModelNote();
				if($note->initById($note_id)) {
					$new_note = new ModelNote();
					$new_note->setDir_id($new_parent_dir->getId());
					$new_note->setTitle($note->getTitle());
					$new_note->setContent($note->getContent());
					$new_note->save();
				}
			}
		}
		return array();
	}
	
	/**
	 * Производит перенос разделов и записей
	 */
	protected function cut() {
		
		$new_parent_id = Request::getPost("new_parent_id");
		$dirs = Request::getPost("dirs") ? Request::getPost("dirs") : array();
		$notes = Request::getPost("notes") ? Request::getPost("notes") : array();

		$new_parent_dir = new ModelDir();
		if($new_parent_dir->initById($new_parent_id)) {
			foreach($dirs as $dir_id) {
				$dir = new ModelDir();
				if($dir->initById($dir_id)) $dir->transfer($new_parent_dir);
			}

			foreach($notes as $note_id) {
				$note = new ModelNote();
				if( $note->initById($note_id)) {
					$note->setDir_id($new_parent_id);
					$note->save();
				}
			}
		}
		return array();
	}
}