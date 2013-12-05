<?php


class Download extends Common{
	////////////////////////////////////
	// DECLARATION DES DONNEES MEMBRES//
	////////////////////////////////////
	
	var $downloadData = null;
	var $idFile = 0;
	
	////////////////////////////////
	// DECLARATION DU CONSTRUCTEUR//
	////////////////////////////////
	//Construction par ID ou par nom du membre : 
	//new User(1) ou new User("clement")
	
	function __construct($id=null, $idFichier=null){
		parent::__construct();
		
		$this->idFile = $idFichier;
		
		if($id){
			$sql = "SELECT * FROM downloads WHERE clef = '".$id."';";
			$rst = $this->bdd->query($sql);
			$this->downloadData = mysql_fetch_assoc($rst);
		}else if($idFichier){
			$sql = "SELECT * FROM downloads WHERE idFichier = '".$idFichier."';";
			$rst = $this->bdd->query($sql);
			$this->downloadData = mysql_fetch_assoc($rst);
			if(!$this->downloadData){
				$clef = $this->creatDownloads($idFichier);
				$this->bdd->clear();
				$sql = "SELECT * FROM downloads WHERE idFichier = '".$idFichier."';";
				$rst = $this->bdd->query($sql);
				$this->downloadData = mysql_fetch_assoc($rst);
			}
		}
	}
	
	function ifDownloaded(){
	
		$sql = "SELECT * FROM checkDownload WHERE idFile = '".$this->idFile."' AND idUser = '".Core::idCo()."';";
		$rst = $this->bdd->query($sql);
		$rslt = mysql_fetch_assoc($rst);
		
		if($rslt['idFile']){
			return true;
		}else{
			$sql45 = "SELECT * FROM files WHERE id = '".$this->idFile."';";
			$rst45 = mysql_query($sql45);
			$rslt45 = mysql_fetch_assoc($rst45);
			$user = new User();
			
			return Tools::getDownloadFtpLogUsers($rslt45['link'], $user->userLogin);
		}
	}
	
	function downloaded(){
		if(Core::isCo()){
			$sql = "INSERT INTO checkDownload VALUE('".$this->downloadData['idFichier']."', '".Core::idCo()."', '".time()."');";
			$this->bdd->query($sql);
		}else{
			$ip = $_SERVER['REMOTE_ADDR'];
			$sql = "SELECT * FROM sessions WHERE ip = '".$ip."' ORDER BY lastTime DESC LIMIT 0,1;";
			$rst = $this->bdd->query($sql);
			if($rslt['idUser']){
				$sql = "INSERT INTO checkDownload VALUE('".$this->downloadData['idFichier']."', '".$rslt['idUser']."', '".time()."');";
				$this->bdd->query($sql);
			}
		}
	}
	
	function creatDownloads($idFichier){
		
		$sql = "SELECT * FROM files WHERE id = '".$idFichier."';";
		$rst = $this->bdd->query($sql);
		$rslt = mysql_fetch_assoc($rst);
		
		if($rslt['id']){

			$clef = md5($rslt['link']);
		
			$link = $rslt['link'];
		
			$sql = "INSERT INTO downloads VALUE('".$clef."', '".$rslt['id']."', '".addslashes($link)."');";
			$rst = $this->bdd->query($sql);
			
			return $clef;
		}else{
			return false;
		}
	}
		
}

?>