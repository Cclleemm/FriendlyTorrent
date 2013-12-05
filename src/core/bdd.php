<?php

class Bdd
{
	// Identifiants MYSQL
	private $server = "" ;
	private $user = "" ;
	private $pass = "" ;
	private $database = "" ;
	private $co;
    private $query = array();
	private $queryNb = array();
	private $timeStart = '';
	
	private $memcache = null;
        
    public $connected;

    /////////////////////////////////////
	// Déclaration des varables utiles //
	/////////////////////////////////////

	public function __construct(){
            
            //Déclaration pour memcached
            require(ROOT.'core/config/bdd.php');
            
            //Déclaration pour Mysql
            
            $this->server = $BDD_MYSQL_SERVER;
            $this->user = $BDD_MYSQL_LOGIN;
            $this->pass = $BDD_MYSQL_PASS;
            $this->database = $BDD_MYSQL_BDD;
            
            $this->memcache = new Memcache;
			$this->memcache->connect('localhost', 11211) or die ("Connexion impossible");
            
	}

    ////////////////////////////////////
	////////////////////////////////////
	//              MYSQL             //
	////////////////////////////////////
    ////////////////////////////////////
        
	public function clear(){
		$this->query = null;
	}
	
	public function getCache(){
		return $this->memcache;
	}
		
        //Fonction pour se connecter à la BDD MYSQL
	public function open(){
		// On vérifie avant si on est connecté ou pas
		if(!$this->connected){
			$this->co = mysql_connect($this->server, $this->user, $this->pass);
			
			$this->connected = true;
			
			mysql_select_db($this->database);
		}
	}
	
	// Fonction pour faire une requete MYSQL AVEC Cache pour éviter de faire la même requête mysql plusieure fois
	public function query($sql){
			
			if($this->query[($sql)] == NULL){
				$this->open();
				$temp = mysql_query($sql, $this->co) or die("Erreur: ".mysql_error()."<br />Requète: ".$sql);
				
				if($this->queryNb[($sql)]){
					$this->queryNb[($sql)][0]++;
					$this->queryNb[($sql)][1] = ($time - $this->timeStart);
				}else
					$this->queryNb[($sql)] =array(1, ($time - $this->timeStart));
				
				if($temp != 1 AND $temp != 0){
					$this->query[($sql)] = $temp;
					
					$num_rows = mysql_num_rows($temp);
					if($num_rows != false AND $num_rows != 0){
						mysql_data_seek($temp, 0);
					}
				}
			}else{
				$temp = $this->query[($sql)];
				$num_rows = mysql_num_rows($temp);
					if($num_rows != false AND $num_rows != 0){
						mysql_data_seek($temp, 0);
					}
			}
			return $temp;
	}
	
	// Fonction pour faire une requete MYSQL SANS CACHE
	public function queryNoCache($sql){	
		$this->open();
		$temp = mysql_query($sql, $this->co) or die("Erreur: ".mysql_error()."<br />Requète: ".$sql);		
            return $temp;
	}
	
	public function data($source){
		
		$result = array();
		
		while($rslt=mysql_fetch_assoc($source)){
			$return[]=$rslt;
		}
		
		return $return;
	}
}

?>