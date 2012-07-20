<?php
	error_reporting(E_ALL);
	set_include_path(get_include_path().":../templates");

	spl_autoload_register('autoload');

	function autoload($classname) {
		$path = "..";
		// Разбиваем имя класса на слова состоящие из заглавной буквы и последующих маленьких
		preg_match_all("~([A-Z][a-z\d]*)~", $classname, $matches);
		// Добавляем корневую папку для всех классов
		array_unshift($matches[0], "engine");
		foreach($matches[0] as $item) {
			$path .= "/" . $item;
			if(is_file("$path/$classname.php")) {
				require_once "$path/$classname.php";
				break;
			}
		}
	}

	$router = new Router();
	$router->forward();
?>