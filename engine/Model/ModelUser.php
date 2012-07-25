<?php

/**
 * Модель для работы с пользователями
 *
 * @author AAM
 */
class ModelUser extends ModelOrm {
	protected $login;
	protected $password;
	protected $created;
	
	public function initByLogin($login) {
		$mapper = new ModelMapper();
		$result = $mapper->find( "ModelUser", array("login"=>$login), array("options"=>"LIMIT 1") );
		
		if(isset($result[0])) {
			$user = $result[0];
			
			$this->id = $user->id;
			$this->login = $user->login;
			$this->password = $user->password;
			$this->created = $user->created;
		}
	}
}

?>
