<?php
/**
 * Обработка ошибки дооступа к записи или разделу и других
 * @author AAM
 *
 * Зависимость: требуется класс DebugLog производящий запись строки в лог-файл
 */
class ExceptionAccess extends Exception {
	
	/**
	 * Описание ошибки
	 * @var String
	 */
	private $message;

	/**
	 * @param String $errTitle - описание ошибки
	 */
	public function __construct($errTitle = null) {
		$this->message = $errTitle;
	}

	/**
	 * Записывает ошибку в лог-файл если оно создано
	 */
	public function toLog() {
		if($this->message) {
			$log_string = "Пользователь: " . Request::$uid . ".  Ошибка: " . $this->message;

			$log = new DebugLog();
			$log->write($log_string);
		}
	}
}