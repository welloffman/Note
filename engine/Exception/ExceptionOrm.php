<?php
/**
 * Обработка ошибок при работе с базой данных
 * @author AAM
 *
 * Зависимость: требуется класс DebugLog производящий запись строки в лог-файл
 */
class ExceptionOrm extends Exception {
	/**
	 * Описание ошибки
	 * @var String
	 */
	protected $errTitle;

	/**
	 * @param String $errTitle - описание ошибки
	 */
	public function __construct($errTitle = null) {
		$this->errTitle = $errTitle;
	}

	/**
	 * Записывает ошибку в лог-файл
	 */
	public function toLog() {
		$error = $this->errTitle ? $this->errTitle : $this->getMessage();
		$log_string = "Ошибка ORM: " . $error;

		$log = new DebugLog();
		$log->write($log_string);
	}
}