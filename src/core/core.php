<?php


class Core {
    
    public $bdd;
    private $co = false;
    private $idUser;
    private $admin = false;
    
    /**
    * @var Singleton
    * @access private
    * @static
    */
    private static $_instance = null;
    /**
    * Constructeur de la classe
    *
    * @param void
    * @return void
    */
    private function __construct($token) {
        
        require(ROOT.'core/bdd.php');
		
		//Language switcher
		if(LANGUAGE=="fr"){
			require(ROOT.'core/languages/fr.php');
		else
			require(ROOT.'core/languages/en.php');
        
        //INITIALISATION de la base de donnée
        $this->bdd=new BDD;
		
		
		if($token){
			$sql = "SELECT sessions.*, users.admin FROM sessions, users WHERE users.id = sessions.idUser AND sessions.cle = '".$token."';";
			$query = $this->bdd->query($sql);
			$donnees = mysql_fetch_assoc($query);
			if($donnees['idUser']){
				$this->co = true;
				$this->idUser = $donnees['idUser'];
				
				$sql = "UPDATE sessions SET lastTime = '".time()."' WHERE cle = '".$token."';";
				$query = $this->bdd->query($sql);
				
				if($donnees['time'] < time()-(24*60*60)){
					$sql = "UPDATE sessions SET time = '".time()."' WHERE cle = '".$token."';";
					$query = $this->bdd->query($sql);
				
					setcookie('SEED_connect', $_COOKIE['SEED_connect'], (time() + 2592000), '/');
				}
				
				$this->admin = $donnees['admin'];
			}
		}
    }
    /**
    * Méthode qui crée l'unique instance de la classe
    * si elle n'existe pas encore puis la retourne.
    *
    * @param void
    * @return Singleton
    */
    public static function getInstance($token = '') {
    
    if(!$token)
    	$token = $_COOKIE['SEED_connect'];
    	
    if(is_null(self::$_instance)) {
    self::$_instance = new Core($token);
    }
    return self::$_instance;
    }
	
	/////////////////////
	// METHODES STATIC //
	/////////////////////
	
	static function isCo(){
		$Core = Core::getInstance();
		
		if($Core->co){
			return true;
		}else{
			return false;
		}
	}
	
	static function isAdmin(){
		$Core = Core::getInstance();
		if($Core->admin){
			return true;
		}else{
			return false;
		}
	}
	
	static function idCo(){
		$Core = Core::getInstance();
		if($Core->idUser){
			return $Core->idUser;
		}else{
			return false;
		}
		
	}
}

// PARENT A toutes les autres class ou lang et bdd est utile
class Common {
    
   protected $bdd;
    
   function __construct(){
        // on utilise la seule instance du coeur ...
        $Core = Core::getInstance();


        //INITIALISATION de la base de donnée
        $this->bdd = $Core->bdd;

    }
}

?>
