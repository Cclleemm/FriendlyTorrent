<?php


class RssM extends Common{
	////////////////////////////////////
	// DECLARATION DES DONNEES MEMBRES//
	////////////////////////////////////
	
	var $rssData = null;
	
	////////////////////////////////
	// DECLARATION DU CONSTRUCTEUR//
	////////////////////////////////
	//Construction par ID ou par nom du membre : 
	//new User(1) ou new User("clement")
	
	function __construct($url){
		parent::__construct();
		
		if($url){
		
			$fluxrss=simplexml_load_file($url);
			
			/*echo '<pre>';
			print_r($fluxrss);
			echo "</pre>";*/
			
			foreach($fluxrss->channel->item as $item){
	            $sql = "SELECT * FROM cacheRss WHERE url = '".addslashes($item->link)."';";
	            $rst = $this->bdd->query($sql);
	            $rslt = mysql_fetch_assoc($rst);
	            
	            if($rslt['id']){
		            $id = $rslt['id'];
		            $nameFile = $rslt['nameFile'];
	            }else{
		            $h = get_headers($item->link);
		            $e = explode("\"", $h[3]);
		            $e2 = explode("\"", $e[1]);
		            $nameFile = $e2[0];
		            
		            if($nameFile != NULL) {
			            $sqlI = "INSERT INTO cacheRss VALUE('', '".addslashes($item->link)."', '".addslashes($nameFile)."');";
			            $this->bdd->query($sqlI);
			            $id = mysql_insert_id();
			        }
	            }
	            
	            $nameFile = str_replace(".torrent", "", $nameFile);
	            
		        $item->title=strip_tags($item->title);
	            
	            if($nameFile != NULL) {
		            $user = new User();
		            
		            $sql = "SELECT torrents.id, users.id as uid, users.login FROM torrents, users WHERE users.id = '".Core::idCo()."' AND users.id = torrents.idBoxe AND name = '".addslashes($nameFile)."-".md5($user->userData['id'])."';";
		            $rst = $this->bdd->query($sql);
		            $rslt = mysql_fetch_assoc($rst);
		            
		            if($rslt['id']){
		            	$etat = "Téléchargement en cours";
		            	$dl = false;
		            }else{
		            	$etat = "A télécharger";
		            	$dl = true;
		            }
		            
		            $this->rssData[] = array('name' => $item->title, 'description' => $item->description, 'id' => $id, 'etat' => $etat, 'isDwn' => $dl);
				}else{
					$this->rssData[] = array('name' => $item->title, 'description' => $item->description, 'id' => null, 'etat' => 'Fichier non torrent', 'isDwn' => false);
				}
	        }
		}
	}
}

?>