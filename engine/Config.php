<?php

/**
 * Конфиг системы
 *
 * @author AAM
 */
class Config {
	private static $conf = array(
		"salt" => "lfgdfg4;5gerge^_sdfsdf-sdfsd_",
		"dbhost" => "localhost",
		"dbname" => "note",
		"dbuser" => "note",
		"dbpass" => "zras2ACW2BSep2A5"
	);
	
	/**
	 * Возвращает параметр конфига
	 * @param string $param Индекс параметра
	 * @return string Значение параметра или null, если такого параметра нет
	 */
	public static function get($param) {
		if( isset(self::$conf[$param]) ) return self::$conf[$param];
		else return null;
	}
}

?>
