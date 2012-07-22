<?php
/**
 * Класс для взаимодействия объектов с базой (объект должен храниться только в одной таблице)
 */
class ModelMapper {
	private $map = array(
		'modeluser' => array (
			'id' => 'user__id',
			'login' => 'user__login',
			'password' => 'user__password',
			'created' => 'user__created'
		),
		'modeldir' => array (
			'id' => 'dir__id',
			'user_id' => 'dir__user_id',
			'path' => 'dir__path',
			'title' => 'dir__title'
		),
		'modelnote' => array (
			'id' => 'note__id',
			'dir_id' => 'note__dir_id',
			'title' => 'note__title',
			'content' => 'note__content'
		)
	);

	/**
	 * @param Array $operators - Операторы для запроса
	 */
	private $operators = array(
		"comparison" => "=",
		"union" => "AND",
		"options" => ""
	);
	
	// Дескриптор соединения с базой данных
	private $lnk;
	
	/**
	 * Создает новые записи в таблице из объекта (если объекты будут храниться в разных таблицах нужно оптимизировать)
	 * @param Object $obj 
	 */
	public function create($obj) {
		foreach($this->insert($obj) as $query) {
			$this->query($query);
		}
		
		// Присваиваем Id объекту
		$data = mysql_fetch_assoc($this->query("SELECT LAST_INSERT_ID() as `id`"));
		$obj->setId($data['id']);
	}
	
	/**
	 * Возвращает из базы объекты соответствующие параметрам
	 * @param String $class_name - имя класса объекта
	 * @param Array $params - параметры для поиска вида: ['свойство']=>'значение'
	 * @param Array $opers - операторы для sql запроса
	 * @return Array
	 */
	public function find($class_name, $params, $opers = array()) {
		// Изменяем операторы для запроса по умолчанию
		foreach($opers as $ind=>$val) $this->operators[$ind] = $val;
		
		$query = $this->select($class_name, $params);
		$result = $this->query($query);
		
		// Формируем массив объектов
		$ob = array();
		$data = mysql_fetch_assoc($result);
        while ($data) {
            $ob[] = $this->getObject($class_name, $data);
            $data = mysql_fetch_assoc($result);
        }
		return $ob;
	}
	
	/**
	 * Удаляет объект из базы
	 * @param Object $obj 
	 */
	public function delete($obj) {
		// Получаем имя таблицы
		$class_name = strtolower( get_class($obj) );
		preg_match("/^(.+)__/", $this->map[$class_name]["id"], $match);
		$tbl_name = $match[1];
		$query = "DELETE FROM `$tbl_name` WHERE `id`=" . $obj->getId();
		
		$this->query($query);
	}
	
	/**
	 * Произвольный запрос в базу
	 */
	public function freeQuery($query) {
		if(!$result = $this->query($query)) return false;
		
		// Формируем массив из результата
		$arr = array();
		$data = mysql_fetch_assoc($result);
        while ($data) {
            $arr[] = $data;
            $data = mysql_fetch_assoc($result);
        }
		return $arr;
	}

	/**
	 * Подготавливает массив sql запросов для записи объекта в базу
	 * @param Object $obj - объект произвольного класса который описан в массиве $map
	 * @return Array
	 */
	private function insert($obj) {
		// Формируем массив вида: [имя_таблицы][имя_поля] => 'значение'
		$class_name = strtolower( get_class($obj) );
		$ob_map = $this->map[$class_name];
		$fld = array();
		foreach($ob_map as $prop=>$val) {
			if( !$table_data = $this->getMapData($class_name, $prop) ) continue;
			$get = "get".$prop;
			$fld[ $table_data[0] ][ $table_data[1] ] = $obj->$get();
		}
		
		// Формируем массив sql запросов
		$query = array();
		foreach($fld as $t_name => $table) {
			$flds = array();
			$vals = array();
			foreach($table as $field => $value) {
				if($value) {
					$flds[] = $field;
					$vals[] = $this->val($value);
				}
			}
			// Если у объекта нет Id, записываем в базу новый объект, если объекту присвоен Id - считаем что объект уже есть в базе и пытаемся его обновить
			if(!in_array("id", $flds)) {
				$query[] = "INSERT INTO `" . $t_name . "` (`" . implode("`,`", $flds) . "`) VALUES ('" . implode("','", $vals) . "')";
			}
			else {
				$query[] = "UPDATE `" . $t_name . "` SET " . $this->getUpd($flds, $vals) . " WHERE `id`='" . $obj->getId() . "'";
			}
		}
		return $query;
	}
	
	/**
	 * Формирует строку для поиска объекта по базе
	 * @param String $obj_name - имя произвольного класса который описан в массиве $map
	 * @param Array $params - ассоциативный массив вида "prop_name"=>"prop_value" для поиска по полям
	 * @return String
	 */
	private function select($obj_name, $params) {
		try {
			$obj_name = strtolower($obj_name);
			if(!array_key_exists($obj_name, $this->map)) throw new OrmException("Класс $obj_name не найден маппере!");
			$ob_map = $this->map[$obj_name];
			$fld = array();
			// Формируем массив с таблицами содержащими поля
			foreach($ob_map as $prop=>$tbl_fld) {
				if( !$table_data = $this->getMapData($obj_name, $prop) ) continue;
				$fld[ $table_data[0] ][ $table_data[1] ] = $prop;
			}

			// Если объект хранится только в одной таблице формируем простой select
			if(count($fld) == 1) {
				// Формируем список нужных объекту полей
				foreach($fld as $table => $fields) {
					$f = array();
					foreach($fields as $field) {
						$f[] = $field;
					}
				}

				// Формирует список условий where
				$w = array();
				foreach($params as $prop => $val) {
					if( !$table_data = $this->getMapData($obj_name, $prop) ) continue;
					$w[] = "`" . $table_data[1] . "` " . $this->operators["comparison"] . " '" . $this->val($val) . "'";
				}

				$query = "SELECT `" . implode("`,`", $f) . "` FROM `" . $table . "` WHERE " . implode(" " . $this->operators["union"] . " ", $w) . " " . $this->operators["options"];
			} 
			// Хранение объектов в разных таблицах не поддерживается
			return $query;
		}
		catch(OrmException $e) {
			$e->toLog();
			return "";
		}
	}
	
	/**
	 * Возвращает имя таблицы и поля в котором хранится значение свойства класса
	 * @param String $class_name - имя класса объекта
	 * @param String $prop_name - свойство объекта
	 * @return Array - первый элемент содержит имя таблицы, второй - имя поля. В случае неудачи поиска вернет false
	 */
	private function getMapData($class_name, $prop_name) {
		if(!array_key_exists($class_name, $this->map)) {
			echo "Класс $class_name не найден в карте классов!";
			return false;
		}
		if(!array_key_exists($prop_name, $this->map[$class_name])) {
			echo "Свойство $prop_name не найдено в карте классов класса $class_name!";
			return false;
		}
		$table_data = explode("__", $this->map[$class_name][$prop_name]);
		return $table_data;
	}
	
	/**
	 * Возвращает объект типа $class_name со свойствами из массива $data
	 * @param String $class_name - имя класса для объекта
	 * @param Array $data - ассоциативный массив со свойствами объекта
	 * @return Object
	 */
	private function getObject($class_name, $data) {
		$ob = new $class_name();
		foreach($this->map[ strtolower($class_name) ] as $prop=>$tab_row) {
			$set = "set$prop";
			$ob->$set( $data[preg_replace("/^\w+__/", "", $tab_row)] );
		}
		return $ob;
	}
	
	/**
	 * Возвращает часть строки для UPDATE запроса на основе синхронизированых массивов полей и значений
	 * @param Array $flds - массив полей таблици
	 * @param Array $vals - значений для таблицы
	 * @return String 
	 */
	private function getUpd($flds, $vals) {
		$result = array();
		foreach ($flds as $i=>$v) {
			if($flds[$i] != "id") $result[] .= "`" . $flds[$i] . "`='" . $vals[$i] . "'";
		}
		return implode(", ", $result);
	}
	
	/**
	 * Выполняет запрос в базу данных
	 * @param String $query - строка запроса в базу
	 * @return resource - дескриптор результата запроса или BOOLEAN в зависимости от запроса и результата
	 */
	private function query($query) { //echo $query; exit;
		$this->dbConnect();
		if(!$result = mysql_query($query)) {
			echo "Ошибка в запросе: " . mysql_error() . "\n" . $query;
			exit;
		}
		return $result;
	}
	
	/**
	 * Присоединение к базе
	 */
	private function dbConnect() {
		if(!$this->lnk) {
			$conf = new Conf("db");
			$host = $conf->getVal("host");
			$login = $conf->getVal("user");
			$password = $conf->getVal("password");
			$name = $conf->getVal("dbname");
			
			$this->lnk = mysql_connect($host, $login, $password) or die("Could not connect to base: " . mysql_error());
			mysql_select_db($name, $this->lnk) or die ('Can\'t use ' . $name . ' : ' . mysql_error());
			mysql_query('SET NAMES utf8');
		}
	}
	
	/**
	 * Закрывает соединение
	 */
	private function close() {
		if($this->lnk) {
			mysql_close($this->lnk);
			$this->lnk = false;
		}
	}
	
	/**
	 * Безопасное добавление переменных в запрос
	 * @param String $val - строка для вставки в запрос
	 * @return String 
	 */
	private function val($val) {
		$this->dbConnect();
		return mysql_real_escape_string($val);
	}
}
?>
