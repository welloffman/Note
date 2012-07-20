<?php
/**
 * Класс для авторизации
 *
 * @author AAM
 */
class Auth {
	public static $memcache = null;
	private $access = false;
		
	/**
	 * Конструктор - определяет по логину и хэшу есть ли права доступа у пользователя 
	 * или проверяет авторизационные данные и ставит куки
	 */
	public function __construct() {
		if( Request::getCookie("login") && Request::getCookie("hash") ) $auth = new AuthReqChecker();
		else if( Request::getPost("login") && Request::getPost("passwd") ) $auth = new AuthLoginChecker();
		else return;
		
		$this->access = $auth->check();
	}
	
	/**
	 * Проверка доступа
	 */
	public function getAccess() {
		return $this->access;
	}
	
	/**
	 * Возвращает Id пользователя 
	 */
	public function getId() {
		preg_match("~^\d+~", Request::getCookie("login"), $match);
		return $match[0];
	}
	
	/**
	 * Инициализируем мемкеш
	 */
	public static function setMemcache() {
		if(self::$memcache == null) {
			self::$memcache = new Memcache;
			self::$memcache->connect('127.0.0.1', 11211) or die ("Memcache could not connect");
		}
	}
	
	/**
	 * Выход
	 */
	public static function quit() {
		self::setMemcache();
		self::$memcache->set(Request::getCookie("login"), "", false, 0);
		setcookie("login", "", time() - 1, '/');
		setcookie("hash", "", time() - 1, '/');
	}
	
	/**
	 * Для генерации случайной строки
	 * @param Number $length
	 * @return String 
	 */
    public static function generateCode($length=6) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPRQSTUVWXYZ0123456789_";
        $code = "";
        $clen = strlen($chars) - 1;
        while (strlen($code) < $length) {
                $code .= $chars[mt_rand(0,$clen)];
        }
        return $code;
    }
}

?>
