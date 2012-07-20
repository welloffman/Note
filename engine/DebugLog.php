<?php
/**
 * Класс для ведения логов
 * Путь к лог-файлу необходимо указать в конструкторе
 *
 * @author AAM
 */
class DebugLog {
	/**
	 * Путь к лог-файлу
	 * @var String 
	 */
	private $file;
	
	public function __construct() {
		$this->file = "/var/log/notes.log";
	}
	
	/**
	 * Записывает строку в лог
	 * @param Stirng $data 
	 */
	public function write($data) {
		if (is_writable($this->file)) {
				$fp = fopen($this->file, "a+");
				fwrite($fp, date("M-d-y H:i:s", time()) . " --> " . $data . "\n");
				fclose($fp);
		}
	}
	
	/**
	 * Записывает дамп объекта в лог
	 * @param Multiply $var 
	 */
	public function writeDump($var) {
		ob_start();
		var_dump($var);
		$v = ob_get_clean();
		$dump = print_r($v, true);
		
		if (is_writable($this->file)) {
				$fp = fopen($this->file, "a+");
				fwrite($fp, date("M-d-y H:i:s", time()) . " --> " . $dump . "\n");
				fclose($fp);
		}
	}
	
	/**
	 * Очищает лог-файл
	 */
	public function clear() {
		$fp = fopen($this->file, 'a');
		ftruncate($fp, 0);
		fclose($fp);
	}
}
