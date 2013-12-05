<?php

class Cloud extends Common{
	////////////////////////////////////
	// DECLARATION DES DONNEES MEMBRES//
	////////////////////////////////////
	
	var $boxeData;

	
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
			$this->boxeData = $rslt;	
		}
	}
	
	function scanDossier($rep){

    $racine=@opendir($rep);
    $taille=0;
    
    while($dossier=@readdir($racine)){
      if(!in_array($dossier, array("..", ".", ".config"))){

      	$stat = stat("$rep/$dossier");
      	
      

      	if(is_dir("$rep/$dossier")){
          $this->scanDossier("$rep/$dossier");
          $type = 'FOLDER';
          $taille = 0;
        }else{
          $taille=@filesize("$rep/$dossier");
          $tab = explode('.', $dossier);
          $ext = $tab[count($tab)-1];
          $type = 'FILE';
        }

		//if($stat['mtime'] >= $this->boxeData['lastScan']){
          $sql = "SELECT * FROM files WHERE link = '".addslashes($rep).'/'.addslashes($dossier)."' AND type = '".$type."' AND idBoxe = '".$this->boxeData['id']."';";
          $rst = mysql_query($sql);
          $rslt = mysql_fetch_assoc($rst);

          if(!$rslt['id']){
            mysql_query('INSERT INTO files VALUES("", "'.addslashes($rep).'/'.addslashes($dossier).'", "'.$type.'", "'.$this->boxeData['id'].'", "'.$taille.'", "'.time().'");');
            $id = mysql_insert_id();
		  }
      //}
      }
    }
    @closedir($racine);
    
    mysql_query('UPDATE users SET lastScan = "'.time().'" WHERE id = "'.$this->boxeData['id'].'"');
  }
  
  function scanDossierSup(){

    $sql = "SELECT * FROM files WHERE idBoxe = '".$this->boxeData['id']."'";
    $rst = mysql_query($sql);
    while($rslt = mysql_fetch_assoc($rst)){
      if (!file_exists(stripcslashes($rslt['link']))){
         mysql_query('DELETE FROM files WHERE id = "'.$rslt['id'].'"');
         mysql_query('DELETE FROM actions WHERE idFile = "'.$rslt['id'].'"');
         mysql_query('DELETE FROM downloads WHERE idFichier = "'.$rslt['id'].'"');
      }
    }
  }
  
  function scan(){
	  $this->scanDossier($this->boxeData['boxe']);
	  $this->scanDossierSup();
  }
	
}

?>