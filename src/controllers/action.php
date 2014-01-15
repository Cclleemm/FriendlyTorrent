<?php
/*########################################################
 *
 *   Controller Action
 *   Permet la gestion du module action
 *
#########################################################*/

class Action extends Controller{

	////////////////////////////////////
	// CHARGEMENT DES MODELS UTILISES //
	////////////////////////////////////

        var $models = array('cloud', 'torrents', 'torrent', 'rss', 'xfer');

	////////////////////////////////////
	//          ACTION INDEX          //
	////////////////////////////////////
	
    function delete($idFile){
    	if(!Core::idCo()){
    		echo '<script>$.pnotify({title: \''.LANG_ERROR.'\',text: \''.LANG_ERROR_DISCONNECTED.'\',type: \'error\'});</script>';
    		exit();
    	}
    	
    	$boxe = new Cloud();
		
		$sql = "SELECT * FROM files WHERE id = '".$idFile."';";
	    $query = mysql_query($sql);
	    $rst = mysql_fetch_assoc($query);
    	
    	if($rst['idBoxe'] != Core::idCo()){
    		echo '<script>$.pnotify({title: \''.LANG_ERROR.'\',text: \''.LANG_ERROR_DELETEFORBIDDEN.'\',type: \'error\'});</script>';
    		exit();
    	}
	    
	    $tmp = $rst['link'];
	    $tmp = str_replace($boxe->boxeData['boxe'], '', $tmp);
	    $exp = explode('/', $tmp);
	    
	    $sql45 = "SELECT * FROM torrents WHERE datapath = '".addslashes($exp[1])."' AND idBoxe = '".Core::idCo()."';";
        $rst45 = mysql_query($sql45);
        $rslt45 = mysql_fetch_assoc($rst45);
        
        if($rslt45['id']){
	        $torrent = new Torrent($rslt45['id']);
	        $torrent->delete();
        }
	    
	    if($rst['type'] == 'FOLDER')
	       $bool = Tools::clearDir($rst['link']);
	
	    if($rst['type'] == 'FILE')
	       $bool = unlink($rst['link']);
	
	    if($bool){
	
	      $boxe->scan();
	      
	      echo '<script>$("#'.$idFile.'").hide();</script>';
	      
	    }else{
	      echo '<script>$.pnotify({title: \''.LANG_ERROR.'\',text: \''.LANG_ERROR_CANTDELETE.'\',type: \'error\'});</script>';
	    }
    }
    
    function disconnect(){
	    $this->user->disconnect();
	    header('Location : /');
    }
    
    function bulleSpace(){
	    $sql45 = "UPDATE `users` SET `bulleSpace`= 1 WHERE id = '".Core::idCo()."';";
		mysql_query($sql45);
    }
    
    function bulleAbo(){
	    $sql45 = "UPDATE `users` SET `bulleAbo`= 1 WHERE id = '".Core::idCo()."';";
		mysql_query($sql45);
    }

    function listeTorrent(){
    	if(!Core::idCo())
    		echo '<script>$.pnotify({title: \''.LANG_ERROR.'\',text: \''.LANG_ERROR_DISCONNECTED.' !\',type: \'error\'});</script>';
    		
    	$torrents = new TorrentsM();
    	$final = $torrents->myTorrents();
    	echo json_encode($final);
    }
    
    function refreshTorrent(){
    	$s = 60;
    	
    	while($s > 0){
    		$timestart=microtime(true);
    		
	    	$torrents = new TorrentsM();
	    	$final = $torrents->refresh();

	    	sleep(5);
	    	$timeend=microtime(true);
			$time=$timeend-$timestart;
			$s = $s - $time;
	    }
    }

     function listeTorrentAdmin(){
     	if(!Core::idCo())
    		echo '<script>$.pnotify({title: \''.LANG_ERROR.'\',text: \''.LANG_ERROR_DISCONNECTED.'\',type: \'error\'});</script>';

    	$torrents = new TorrentsM();
    	$final = $torrents->torrents();
    	echo json_encode($final);
    }
    
    function listeRss(){
    	if(!Core::idCo())
    		echo '<script>$.pnotify({title: \''.LANG_ERROR.'\',text: \''.LANG_ERROR_DISCONNECTED.'\',type: \'error\'});</script>';
    		
    	if($this->user->userData['rss']){
    		$rss = new Rss($this->user->userData['rss']);
    		$final = $rss->rssData;
    	}
    	echo json_encode($final);
    }

    function start($id){
    	if(!Core::idCo())
    		echo '<script>$.pnotify({title: \''.LANG_ERROR.'\',text: \''.LANG_ERROR_DISCONNECTED.'\',type: \'error\'});</script>';
    		
    	$torrent = new Torrent($id);
    	if(!$torrent->start())
   			echo '<script>$.pnotify({title: \'Démarrage\',text: \'Le torrent ne peut être démarré !\',type: \'error\'});</script>';
    }

    function stop($id){
    	if(!Core::idCo())
    		echo '<script>$.pnotify({title: \''.LANG_ERROR.'\',text: \''.LANG_ERROR_DISCONNECTED.'\',type: \'error\'});</script>';
    		
    	$torrent = new Torrent($id);
    	if(!$torrent->stop())
   			echo '<script>$.pnotify({title: \'Démarrage\',text: \'Le torrent ne peut être stoppé !\',type: \'error\'});</script>';
    }
    
    function startRss($id){
    	if(!Core::idCo())
    		echo '<script>$.pnotify({title: \''.LANG_ERROR.'\',text: \''.LANG_ERROR_DISCONNECTED.'\',type: \'error\'});</script>';
    		
    	$freespace = disk_free_space(ROOT_DOWNLOADS);
    	if(round(($freespace / 1024)/1024/1024, 2) < 50)
    		echo '<script>$.pnotify({title: \'Erreur\',text: \'Il reste plus assez d\'espace sur le serveur !\',type: \'error\'});</script>';

	    	$sql = "SELECT * FROM cacheRss WHERE id = '".$id."';";
		    $rst = $this->bdd->query($sql);
		    $rslt = mysql_fetch_assoc($rst);
		    
		    $nameFile = str_replace(".torrent", "", $rslt['nameFile']);
		    $nameFile = str_replace('\'', '', $nameFile);
		    $nameFile = str_replace('"', '', $nameFile);
		    
		    $user = new User();
	
	    	$handler = fopen($rslt['url'], "r"); 
			$contents = ''; 
			if($handler) 
			while(!feof($handler)) 
			$contents .= fread($handler, 8192); 
			fclose($handler); 
			$handlew = fopen(ROOT_DOWNLOADS.".transferts/".$nameFile."-".md5($user->userData['id'])."", "w"); 
			fwrite($handlew, $contents); 
			fclose($handlew);
	    	
	    	$sql = "SELECT torrents.id, users.login FROM torrents, users WHERE torrents.idBoxe = users.id AND torrents.name = '".addslashes($nameFile."-".md5($user->userData['id']))."';";
					$rst = $this->bdd->query($sql);
					$rslt = mysql_fetch_assoc($rst);
	
					if($rslt['id'] == NULL || $rslt['id'] != NULL && $rslt['idBoxe'] != Core::idCo()){
				    	if(Torrent::creat($nameFile."-".md5($user->userData['id']), $nameFile))
							echo '<script>$.pnotify({title: \'Ajout\',text: \'Ajout du torrent effectué !\',type: \'success\',nonblock: true});refreshRss()</script>';
				  		else
				  			echo '<script>$.pnotify({title: \'Ajout\',text: \'Ajout impossible de ce torrent !\',type: \'error\'});</script>';
				  	}
			      	else
			      		echo '<script>$.pnotify({title: \'Ajout\',text: \'Ajout impossible de ce torrent car vous le téléchargez déjà !\',type: \'error\'});</script>';
    }

    function sup($id){
    	$torrent = new Torrent($id);
    	if(!$torrent->delete())
   			echo '<script>$.pnotify({title: \'Suppression\',text: \'Le torrent ne peut être supprimé !\',type: \'success\'});</script>';
    }

    function addTorrent(){
    	if(!Core::idCo()){
    		echo '<script>$.pnotify({title: \'Erreur\',text: \'Vous avez été déconnecté !\',type: \'error\'});</script>';
    		exit();
    	}
    	
    	$freespace = disk_free_space(ROOT_DOWNLOADS);
    	if(round(($freespace / 1024)/1024/1024, 2) < 50)
    		echo '<script>$.pnotify({title: \'Erreur\',text: \'Il reste plus assez d\'espace sur le serveur !\',type: \'error\'});</script>';
    		
    	if (!empty($_FILES)) {
			$tempFile = $_FILES['Filedata']['tmp_name'];
			$targetPath = $_SERVER['DOCUMENT_ROOT'] . $targetFolder;
			$targetFile = rtrim($targetPath,'/') . '/' . $_FILES['Filedata']['name'];
			
			// Validate the file type
			$fileTypes = array('torrent'); // File extensions
			$fileParts = pathinfo($_FILES['Filedata']['name']);
			
			$user = new User();
			
			if (in_array($fileParts['extension'],$fileTypes)) {
				
				$nameFile = $fileParts['filename'];
				$nameFile = str_replace('\'', '', $nameFile);
			    $nameFile = str_replace('"', '', $nameFile);
				
				$sql = "SELECT torrents.id, users.login, torrents.idBoxe FROM torrents, users WHERE torrents.idBoxe = users.id AND torrents.name = '".$nameFile."-".md5($user->userData['id'])."';";
				$rst = $this->bdd->query($sql);
				$rslt = mysql_fetch_assoc($rst);
				
				if($rslt['id'] == NULL || $rslt['id'] != NULL && $rslt['idBoxe'] != Core::idCo()){
					move_uploaded_file($tempFile,ROOT_DOWNLOADS.".transferts/".$nameFile."-".md5($user->userData['id']));
					if(Torrent::creat($nameFile."-".md5($user->userData['id']), $nameFile))
						echo '<script>$.pnotify({title: \'Ajout\',text: \'Ajout du torrent effectué !\',type: \'success\', nonblock: true});</script>';
		      		else{
		      			$sf = new StatFile(ROOT_DOWNLOADS.".transferts/".$nameFile."-".md5($user->userData['id']), $user);
		      			echo '<script>$.pnotify({title: \'Ajout\',text: \'Ajout impossible de ce torrent ! -- '.$sf->time_left.'\',type: \'error\'});</script>';
		      		}
		      	}
		      	else
		      		echo '<script>$.pnotify({title: \'Ajout\',text: \'Ajout impossible de ce torrent car vous le téléchargez déjà !\',type: \'error\'});</script>';
			} else {
				echo '<script>$.pnotify({title: \'Ajout\',text: \'Veuillez choisir un fichier .torrent !\',type: \'error\'});</script>';
		}
		}else{
			echo '<script>$.pnotify({title: \'Ajout\',text: \'Ajout impossible de ce torrent !\',type: \'error\'});</script>';
		}
    }

    function space(){
    	if(!Core::idCo())
    		echo '<script>$.pnotify({title: \'Erreur\',text: \'Vous avez été déconnecté !\',type: \'error\'});</script>';
    		
    	$espaces = array();
    	$colors = array();
    	
    	$totalspace = disk_total_space(ROOT_DOWNLOADS);
		$freespace = disk_free_space(ROOT_DOWNLOADS);

		$free = 100;

    	$sql = "SELECT * FROM users;";
		$rst = $this->bdd->query($sql);
		while($rslt = mysql_fetch_assoc($rst)){

			$pourcent = round((Tools::dirsize($rslt['boxe']) / $totalspace)*100);
			$free = $free - $pourcent;
			$espaces[] = array("label" => $rslt['login'], "data" => $pourcent);
			$colors[] = '#'.$rslt['couleur'];
		}
		
		$espaces[] = array("label" => 'Espace libre', "data" => $free);
		$colors[] = '#029ec6';
		
    	echo json_encode(array('space' => $espaces, 'colors' => $colors));
    }
    
    function useBand(){
    	if(!Core::idCo())
    		echo '<script>$.pnotify({title: \'Erreur\',text: \'Vous avez été déconnecté !\',type: \'error\'});</script>';
    		
    	$usersUp = array();
    	$usersDown = array();
    	
    	$colors = array();
    	
    	
    	$sql = "SELECT * FROM users;";
		$rst = $this->bdd->query($sql);
		while($rslt = mysql_fetch_assoc($rst)){
	    	$year = date('Y');
			$month = date('n');
			
			$tmp1 = array();
			$tmp2 = array();
			
			$i = 0;
			while($i <= 5){
		
					$xfer = new Xfer($rslt['id']);
					$stats = $xfer->getStat($year, $month);
					
					if(!isset($stats['up']))
						$stats['up'] = 0;
					
					if(!isset($stats['down']))
						$stats['down'] = 0;
				
						$tmp1[] = array(mktime(0, 0, 0, $month, 1, $year)*1000, intval($stats['up'])/1024/1024/1024);
						$tmp2[] = array(mktime(0, 0, 0, $month, 1, $year)*1000, intval($stats['down'])/1024/1024/1024);
						
				if($month == 1){
					--$year;
					$month = 12;
				}else{
					--$month;
				}
				++$i;
			}
			
			$colors[] = '#'.$rslt['couleur'];
			
			$userUp[] = array('label' => $rslt['login'], 'data' => $tmp1);
			$userDown[] = array('label' => $rslt['login'], 'data' => $tmp2);
		}

    	echo json_encode(array('up' => $userUp, 'down' => $userDown, 'colors' => $colors));
    }
    
    function stats(){
	    $rateUp = 0;
	    $rateDown = 0;
	    $sql = "SELECT * FROM users;";
		$rst = $this->bdd->query($sql);
		while($rslt = mysql_fetch_assoc($rst)){
			if($this->bdd->getCache()->get($rslt['id'].'Stats')){
				$stat = $this->bdd->getCache()->get($rslt['id'].'Stats');
				$rateUp += $stat['rateUp'];
				$rateDown += $stat['rateDown'];
			}
		}
		
		echo round($rateDown/1024/1024, 2).'|'.round($rateUp/1024/1024, 2);
    }
    
    function nbNotif(){
    	if(!Core::idCo())
    		echo '<script>$.pnotify({title: \'Erreur\',text: \'Vous avez été déconnecté !\',type: \'error\'});</script>';
    		
		$sql = "SELECT COUNT(*) as nb FROM messagerie WHERE idUserTarget = '".Core::idCo()."' AND seen = 0;";
		$rst = $this->bdd->query($sql);
		$rslt = mysql_fetch_assoc($rst);
		
    	echo json_encode(array('mess' => $rslt['nb']));
    }
}
?>