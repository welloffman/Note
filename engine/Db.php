<?php
/**
 * Класс для работы с базой данных
 *
 * @author AAM
 */
class Db {
	private static $link = false;
	
	/**
	 * Делает запрос к базе и возвращает результат
	 * @param String $q - строка запроса
	 * @return Multiply - массив результатов или null если результат пустой
	 */
	public static function query($q) {
		self::dbConnect();
		$result = mysql_query($q);
		
		if (!$result) {
			echo "Could not successfully run query ($q) from DB: " . mysql_error();
			exit;
		}
		
		$arr = array();
		$data = mysql_fetch_assoc($result);
        while ($data) {
            $arr[] = $data;
            $data = mysql_fetch_assoc($result);
        }
		if(count($arr) == 0) return null;
		//else if(count($arr) == 1) return $arr[0];
		else return $arr;
		
		
		/*$row = 0;
		$res = array();
		$i = 0;
		while (!is_bool($result) && $row = mysql_fetch_assoc($result)) {
			$res[$i++] = $row;
		}
		
		*/
    }
	
	/**
	 * Делает обновляющий запрос к базе
	 * @param String $q - строка запроса
	 */
	public static function queryU($q) {
		self::dbConnect();
		$result = mysql_query($q);
		
		if (!$result) {
			echo "Could not successfully run query ($q) from DB: " . mysql_error();
			exit;
		}
    }
	
	/**
	 * Присоединение к базе
	 */
	public static function dbConnect() {
		if(!self::$link) {
			$conf = new Conf("db");
			$host = $conf->getVal("host");
			$login = $conf->getVal("user");
			$password = $conf->getVal("password");
			$name = $conf->getVal("dbname");
			
			self::$link = mysql_connect($host, $login, $password) or die("Could not connect to base: " . mysql_error());
			mysql_select_db($name, self::$link) or die ('Can\'t use ' . $name . ' : ' . mysql_error());
			mysql_query('SET NAMES utf8');
		}
	}
	
	/**
	 * Закрывает соединение
	 */
	public static function close() {
		if(self::$link) {
			mysql_close(self::$link);
			self::$link = false;
		}
	}
	
	/**
	 * Безопасное добавление переменных в запрос
	 * @param String $val - строка для вставки в запрос
	 * @return String 
	 */
	public static function val($val) {
		self::dbConnect();
		return mysql_real_escape_string($val);
	}
}

?>
