<?php
/*########################################################
 *
 *   Controller Downloads
 *
#########################################################*/

class Downloads extends Controller{

	////////////////////////////////////
	// CHARGEMENT DES MODELS UTILISES //
	////////////////////////////////////

        var $models = array('download', 'zip');
        var $title = array('file' => 'Download', 'creat' => 'Création d\'un téléchargement');

	////////////////////////////////////
	//          ACTION INDEX          //
	////////////////////////////////////
	
    
    function index($key, $file){
    	
    	$d = array();
		$this->setLayout('external');
		
		$download = new Download($key);
		
		$sql = "SELECT * FROM files WHERE id = '".$download->downloadData['idFichier']."';";
		$rst = $this->bdd->query($sql);
		$rslt = mysql_fetch_assoc($rst);
		
		if(!$download->downloadData){
			$d['error'] = true;
			
			$this->set($d);
			$this->render('index');
			
		}else{
			if($_GET['flv'] == true && $rslt['type'] == 'FILE'){
				// This prevents the script from getting killed off when running lengthy tar jobs.
				@ini_set("max_execution_time", 3600);
				$down = $download->downloadData['linkFile'];
				$tab = explode('/', $down);
				$name = $tab[count($tab)-1];

				$command = "ffmpeg -i ".$down." -ar 44100 -s vga -f flv -";
	

				@header("Cache-Control: no-cache");
				@header("Pragma: no-cache");
				// XSendfile not possible here (passthru)
				@header("Content-Description: File Transfer");
				@header("Content-Type: application/force-download");
				@header('Content-Disposition: attachment; filename="'.$name.'".flv');
				// write the session to close so you can continue to browse on the site.
				@session_write_close();
				// Make it a bit easier for tar/zip.
				passthru($command);

			}else if($rslt['type'] == 'FILE'){
			    $url = $download->downloadData['linkFile'];
				$tab = explode('/', $url);
				$name = $tab[count($tab)-1];
				$filename = $url;
				
				if (!is_file($filename) || !is_readable($filename)) {
				    header("HTTP/1.1 404 Not Found");
				    exit;
				}
				$size = filesize($filename);
				 
				if (ini_get("zlib.output_compression")) {
				    ini_set("zlib.output_compression", "Off");
				}
				 
				session_write_close();
				 
				header('Content-Description: File Transfer');
				header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
				header("Pragma: no-cache");
				header("Expires: 0");
				 
				header("Content-Type: application/octet-stream");
				header('Content-Disposition: attachment; filename="'.$name.'"');
				header('Content-Transfer-Encoding: binary');
				header('Pragma: public');

				// on indique au client la prise
				// en charge de l'envoi de données
				// par portion.
				header("Accept-Ranges: bytes");
				 
				// par défaut, on commence au début du fichier
				$start = 0;
				 
				// par défaut, on termine à la fin du fichier (envoi complet)
				$end = $size - 1;
				if (isset($_SERVER["HTTP_RANGE"])) {
				    // l'entête doit être dans un format valide
				    if (!preg_match("#bytes=([0-9]+)?-([0-9]+)?(/[0-9]+)?#i", $_SERVER['HTTP_RANGE'], $m)) {
				        header("HTTP/1.1 416 Requested Range Not Satisfiable");
				        exit;
				    }
				 
				    // modification de $start et $end
				    // et on vérifie leur validité
				    $start = !empty($m[1])?(int)$m[1]:null;
				    $end = !empty($m[2])?(int)$m[2]:$end;
				    if (!$start && !$end || $end !== null && $end >= $size
				        || $end && $start && $end < $start) {
				        header("HTTP/1.1 416 Requested Range Not Satisfiable");
				        exit;
				    }
				 
				    // si $start n'est pas spécifié,
				    // on commence à $size - $end
				    if ($start === null) {
				        $start = $size - $end;
				        $end -= 1;
				    }
				 
				    // indique l'envoi d'un contenu partiel
				    header("HTTP/1.1 206 Partial Content");
				 
				    // décrit quelle plage de données est envoyée
				    header("Content-Range: ".$start."-".$end."/".$size);
				}
				 
				// on indique bien la taille des données envoyées
				header("Content-Length: ".($end-$start+1));
				 
				    //echo '<br />';
				    //var_dump(headers_list());
				    //exit();
				
				// ouverture du fichier en lecture et en mode binaire
				$f = fopen($filename, "rb");
				 
				// on se positionne au bon endroit ($start)
				fseek($f, $start);
				 
				// cette variable sert à connaître le nombre
				// d'octet envoyé.
				$remainingSize = $end-$start+1;
				 
				// calcul la taille des lots de données
				// je choisi 4ko ou $remainingSize si plus
				// petit que 4ko.
				$length = $remainingSize < 4096?$remainingSize:4096;
				
				while (false !== $datas = fread($f, $length)) {
				    // envoie des données vers le client
				    echo $datas;
				 
				    // on a envoyé $length octets,
				    // on le soustrait alors du
				    // nombre d'octets restant.
				    $remainingSize -= $length;
				 
				    // si tout est envoyé, on quitte
				    // la boucle.
				    if ($remainingSize <= 0) {
				    	$download->downloaded();
				        break;
				    }
				 
				    // si reste moins de $length octets
				    // à envoyer, on le rédefinit en conséquence.
				    if ($remainingSize < $length) {
				        $length = $remainingSize;
				    }
				}
				fclose($f);
			}else{
				// This prevents the script from getting killed off when running lengthy tar jobs.
				@ini_set("max_execution_time", 3600);
				$down = $download->downloadData['linkFile'];

				// Find out if we're really trying to access a file within the
				// proper directory structure. Sadly, this way requires that $cfg["path"]
				// is a REAL path, not a symlinked one. Also check if $cfg["path"] is part
				// of the REAL path.
				if (is_dir($down)) {
					$sendname = basename($down);
		
					$dir_param = addcslashes($sendname,"\x00..\x2E!@\@\x7B..\xFF"); 
					$command = "zip -0r - ".$dir_param;
		
					// filenames in IE containing dots will screw up the filename
					$headerName = (strstr($_SERVER['HTTP_USER_AGENT'], "MSIE"))
						? preg_replace('/\./', '%2e', $sendname, substr_count($sendname, '.') - 1)
						: $sendname;
					@header("Cache-Control: no-cache");
					@header("Pragma: no-cache");
					// XSendfile not possible here (passthru)
					@header("Content-Description: File Transfer");
					@header("Content-Type: application/force-download");
					@header('Content-Disposition: attachment; filename="'.$headerName.'.zip"');
					// write the session to close so you can continue to browse on the site.
					@session_write_close();
					// Make it a bit easier for tar/zip.
					chdir(dirname($down));
					passthru($command);
					$download->downloaded();
				}
			}
		}
    }

}
?>