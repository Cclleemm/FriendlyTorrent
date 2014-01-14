<?php


class User extends Common{
	////////////////////////////////////
	// DECLARATION DES DONNEES MEMBRES//
	////////////////////////////////////
	
	var $userId;
	var $userLogin;
	var $userData;

	
	////////////////////////////////
	// DECLARATION DU CONSTRUCTEUR//
	////////////////////////////////
	//Construction par ID ou par nom du membre : 
	//new User(1) ou new User("clement")
	
	function __construct($id=null){
		parent::__construct();
		
		//Par défaut si il n'y a pas d'ID/login on construit le membre connecté
		if($id == ''){
			if(Core::isCo()){
				$id=Core::idCo();
				$sql="SELECT * FROM users WHERE id = '".$id."';";
			}
		//Si $id est ou ID ou un login
		}elseif (is_numeric($id)){
			$sql="SELECT * FROM users WHERE id = '".$id."';";
		}elseif (gettype($id)=="string")
			$sql="SELECT * FROM users WHERE login = '".$id."';";
		
		//Récupération des données du membre
		if(Core::isCo() OR is_numeric($id) OR gettype($id)=="string") $rslt=mysql_fetch_assoc($this->bdd->query($sql));
		
		if($rslt['id'] != NULL){
			$this->userId = $rslt['id'];
			$this->userLogin = $rslt['login'];
			$this->userData = $rslt;	
		}
	}

	function configRPC(){
		$_cfg = array(
			'transmission_rpc_host' => '127.0.0.1',
			'transmission_rpc_port' => $this->userData['port']
		);

		return $_cfg;
	}
	
	function addSession($idUser){
		$retry = true;
		
		while($retry){
			$key = Tools::random(20);
			$sql = "SELECT * FROM sessions WHERE cle = '".$key."';";
			$query = $this->bdd->query($sql);
			$donnees = mysql_fetch_assoc($query);
			if(!$donnees['idUser'])
				$retry = false;
		}
		
		$userAgent = serialize($_SERVER["HTTP_USER_AGENT"]);
		
		$localisation = file_get_contents('http://api.ipinfodb.com/v3/ip-country/?key=89ce8a55e61bf597a379c0184004bdc9a829c2ff3e167dc6473ba4a0113be5dc&ip='.$_SERVER['REMOTE_ADDR']);
		$pays = explode(';', $localisation);
		$pay = $pays[count($pays)-2].':'.$pays[count($pays)-1];
		
		$sql = "INSERT INTO sessions SET cle = '".$key."', idUser = '".$idUser."', time = '".time()."', ip = '".$_SERVER['REMOTE_ADDR']."', user_agent = '".addslashes($userAgent)."', ipLocalisation = '".$pay."';";
		$query = $this->bdd->query($sql);
		
		return $key;
	}

	/////////////////////////////
	// DECLARATION DES METHODES//
	/////////////////////////////
		
	// $login = PSEUDO OU EMAIL
	function connect($login, $pass){
		
		//Cryptage mdp
		$passCrypt=md5($pass);

		//Vérification de la concordance AVEC PSEUDO
		$query = $this->bdd->query("SELECT id,login,password FROM users WHERE login='".$login."'");
		$donnees = mysql_fetch_assoc($query);

		$idLogin = $donnees['id'];
		$passBdd = $donnees['password'];

		if($passCrypt == $passBdd)
		{
			
			$this->userId = $idLogin;
			$this->setSession();
			
			return true;
		}else{
			return false;
		}
	}
	
	function changeMdp($new){
		$this->bdd->query("UPDATE users SET password = '".md5($new)."' WHERE id = '".$this->userId."';")or die(mysql_error());
	}
	
	function changeCouleur($new){
		$this->bdd->query("UPDATE users SET couleur = '".$new."' WHERE id = '".$this->userId."';")or die(mysql_error());
	}
	
	function changeMail($new){
		$this->bdd->query("UPDATE users SET mail = '".$new."' WHERE id = '".$this->userId."';")or die(mysql_error());
	}
	
	function changeRss($new){
		$this->bdd->query("UPDATE users SET rss = '".$new."' WHERE id = '".$this->userId."';")or die(mysql_error());
	}
	
	function isAdmin($value){
		$this->bdd->query("UPDATE users SET admin = '".$value."' WHERE id = '".$this->userId."';")or die(mysql_error());
	}
	
	function setSession(){
		$key = $this->addSession($this->userId);
		
		//Création du cookie
		setcookie('SEED_connect', $key, (time() + 2592000), '/');
	}
	
	function disconnect(){

		$this->bdd->query("DELETE FROM sessions WHERE cle = '".$_COOKIE['SEED_connect']."';");
		setcookie('SEED_connect', '', 0, '/');
		
		header('Location: /');
	}
	
	function getSessions(){
		$sql = "SELECT * FROM sessions WHERE idUser = '".$this->userId."' ORDER BY lastTime DESC;";
		$query = $this->bdd->query($sql);
		
		return $this->bdd->data($query);

	}
	
	function timeLastCloud($idCloud){
		$sql = "SELECT time FROM lastSeen WHERE idUser = '".$this->userId."' AND idCloud = '".$idCloud."';";
		$query = $this->bdd->query($sql);
		$rslt = mysql_fetch_assoc($query);
		
		if(!$rslt['time'])
			$rslt['time'] = (24*60*60);
		
		return $rslt['time'];
	}
	
	function setTimeLastCloud($idCloud){
		$sql = "SELECT time FROM lastSeen WHERE idUser = '".$this->userId."' AND idCloud = '".$idCloud."';";
		$query = $this->bdd->query($sql);
		$rslt = mysql_fetch_assoc($query);
		
		if(!$rslt['time']){
			$sql = "INSERT INTO lastSeen VALUES('".$this->userId."', '".$idCloud."', '".time()."');";
			$query = $this->bdd->query($sql);
		}else{
			$sql = "UPDATE lastSeen SET time = '".time()."' WHERE idUser = '".$this->userId."' AND idCloud = '".$idCloud."';";
			$query = $this->bdd->query($sql);
		}
	}
	
	static function getUsers(){
		$users = array();
		
		$Core = Core::getInstance();
	
		$sql = "SELECT * FROM users;";
		$query = $Core->bdd->query($sql);
		while($rslt = mysql_fetch_assoc($query)){
			$users[] = $rslt;
		}
		
		return $users;
	}
}

?>