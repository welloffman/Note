<?php
/**
 * Класс для взаимодействия объектов с базой (объект должен храниться только в одной таблице)
 */
class ModelMapper {
	
    private $pdo = null;
    
    /**
     * @param Array $operators - Операторы для запроса
     */
    private $operators = array(
	    "comparison" => "=",
	    "union" => "AND",
	    "options" => ""
    );
    
    /**
     * @var Array Карта объектов в базе
     */
    private $map = array(
        'user' => array('id', 'login', 'password', 'created'),
        'dir' => array('id', 'user_id', 'path', 'title'),
        'note' => array('id', 'dir_id', 'title', 'content')
    );
    
    
    
    /**
     * Создает новые записи в таблице из объекта
     * @param Object $obj 
     */
    public function create($obj) {
	
        $this->dbConnect();
        $table_name = strtolower( str_replace("Model", "", get_class($obj)) );

        try {
            if(!isset($this->map[$table_name])) throw new ExceptionOrm("Класс" . get_class($obj) . "не найден в маппере!");
        }
        catch(ExceptionOrm $e) {
            $e->toLog();
            return "";
        }

        // Формируем массивы полей содержащий имя, значение и шаблон для PDO
        $fields = array();
        $vals = array();
        $pattern = array();
        foreach($this->map[$table_name] as $field) {
            if($field == 'id') continue;

            $fields[] = $field;
            $pattern[] = "?";

            $get = "get" . ucfirst($field);
            $vals[] = $obj->$get();
        }

        // Формируем подготовленное выражение
        $query = "INSERT INTO `" . $table_name . "` (`" . implode("`,`", $fields) . "`) VALUES (" . implode(",", $pattern) . ")";
        $expression = $this->pdo->prepare($query);
        $expression->execute($vals);
        
        // Присваиваем Id объекту
		$result = $this->pdo->query("SELECT LAST_INSERT_ID() as `id`");
        $data = $result->fetch();
        $obj->setId($data['id']);
    }
    
    /**
     * Обновляет запись в таблице из объекта
     * @param Object $obj 
     */
    public function update($obj) {
        $this->dbConnect();
        $table_name = strtolower( str_replace("Model", "", get_class($obj)) );

        try {
            if(!isset($this->map[$table_name])) throw new ExceptionOrm("Класс" . get_class($obj) . "не найден в маппере!");
            if(!$obj->getId()) throw new ExceptionOrm("Не передан id при обновлении записи!");
        }
        catch(ExceptionOrm $e) {
            $e->toLog();
            return "";
        }
        
        // Формируем часть запроса с шаблоном для ORM и массив значений
        $p = array();
        $vals = array();
        foreach($this->map[$table_name] as $field) {
            if($field == 'id') continue;

            $p[] .= "`" . $field . "`=?";

            $get = "get" . ucfirst($field);
            $vals[] = $obj->$get();
        }
        $vals[] = $obj->getId();
        $pattern = implode(", ", $p);
        
        // Формируем подготовленное выражение
        $query = "UPDATE `$table_name` SET $pattern WHERE `id`=?";
        $expression = $this->pdo->prepare($query);
        $expression->execute($vals);
    }

    /**
	 * Удаляет объект из базы
	 * @param Object $obj 
	 */
	public function delete($obj) {
        $this->dbConnect();
        $table_name = strtolower( str_replace("Model", "", get_class($obj)) );

        try {
            if(!isset($this->map[$table_name])) throw new ExceptionOrm("Класс" . get_class($obj) . "не найден в маппере!");
            if(!$obj->getId()) throw new ExceptionOrm("Не передан id при обновлении записи!");
        }
        catch(ExceptionOrm $e) {
            $e->toLog();
            return "";
        }
        
        // Формируем подготовленное выражение
        $query = "DELETE FROM `$table_name` WHERE `id`=:id LIMIT 1";
        $expression = $this->pdo->prepare($query);
        $expression->bindValue(':id', $obj->getId());
        $expression->execute();
	}
    
    /**
     * Выборка объектов из базы
     * @param String $class_name Имя класса объекта
     * @param Array $params Ассоциативный массив параметров для поиска
     * @param Array $opers Массив операторов и параметров для запроса
     */
    public function find($class_name, $params, $opers = array()) {
        $this->dbConnect();
        $table_name = strtolower(str_replace("Model", "", $class_name));
        
        try {
            if(!isset($this->map[$table_name])) throw new ExceptionOrm("Класс $class_name не найден в маппере!");
            if(!is_array($params) || count($params) == 0) throw new ExceptionOrm("Не переданы обязательные параметры для выборки объектов из базы!");
        }
        catch(ExceptionOrm $e) {
            $e->toLog();
            return "";
        }
        
        // Изменяем операторы для запроса по умолчанию
		foreach($opers as $ind=>$val) $this->operators[$ind] = $val;
        
        // Формируем часть запроса с шаблоном для ORM и массив значений
        $p = array();
        $vals = array();
        foreach($params as $name=>$value) {
            $vals[] = $value;
            
            $p[] = "`" . $name . "` " . $this->operators["comparison"] . " ?";
        }
        $pattern = implode(" " . $this->operators["union"] . " ", $p);
        
        // Формируем подготовленное выражение
        $query = "SELECT * FROM `$table_name` WHERE $pattern" . " " . $this->operators["options"];
        $expression = $this->pdo->prepare($query);
        $expression->execute($vals);
        
        // Формируем массив объектов из найденных записей в базе
        $result = array();
        while($row = $expression->fetch()) {
            $result[] = $this->getObject($class_name, $row);
        }
        
        return $result;
    }
    
   /**
    * Присоединение к базе
    */
    private function dbConnect() {
        if(empty($this->pdo)) {
            try {
                $this->pdo = new PDO('mysql:host=localhost;dbname=note','note','zras2ACW2BSep2A5');
                $this->pdo->exec('SET NAMES utf8');
            }
                catch(PDOException $e) {
                die("Error: " . $e->getMessage());
            }
        }
    }
    
    /**
	 * Возвращает объект типа $class_name со свойствами из массива $data
	 * @param String $class_name - имя класса для объекта
	 * @param Array $data - ассоциативный массив со свойствами объекта
	 * @return Object
	 */
	private function getObject($class_name, $data) {
        $table_name = strtolower( str_replace("Model", "", $class_name) );
        
        try {
            if(!isset($this->map[$table_name])) throw new ExceptionOrm("Класс $class_name не найден в маппере!");
        }
        catch(ExceptionOrm $e) {
            $e->toLog();
            return "";
        }
      
        // Заполняем поля объекта
        $ob = new $class_name();
        foreach($this->map[$table_name] as $field) {
			$set = "set" . ucfirst($field);
			$ob->$set($data[$field]);
		}
		return $ob;
	}
}