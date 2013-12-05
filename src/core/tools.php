<?php

class Tools
{
    
	////////////////////////////////////
	//          RECUPERE IP           //
	////////////////////////////////////
    
	static function ip(){
		return $_SERVER["REMOTE_ADDR"];
	}
        
	////////////////////////////////////
	//    PROTECTION    POST & GET    //
	////////////////////////////////////
        
    static function protection($valeur) {
            $valeur = addslashes($valeur);
            $valeur = str_replace('<script>', '', $valeur);
            $valeur = str_replace('</script>', '', $valeur);
            return $valeur;
        }
        
	////////////////////////////////////
	//              RANDOM            //
	////////////////////////////////////
        
    static function random($car) {
		$string = "";
		$chaine = "abcdefghijklmnpqrstuvwxyAZERTYUIOPQQSDFGHJKLMWXCVBN0123456789";
		srand((double)microtime()*1000000);
		for($i=0; $i<$car; $i++) {
		$string .= $chaine[rand()%strlen($chaine)];
		}
		return $string;
	}
	
	static function zero($val){
		$val = round($val);
	
		if($val < 10){
			return "0".$val;
		}else{
			return $val;
		}
	
	}

	static function dateWeek($fr){
	
		$time=time();
		$d = date('d', $time);
		$D = date('D', $time);
		$m = date('m', $time);
		$y = date('y', $time);
	
		$jour_c=array();
		$jour_c['Mon'] = 1;
		$jour_c['Tue'] = 2;
		$jour_c['Wed'] = 3;
		$jour_c['Thu'] = 4;
		$jour_c['Fri'] = 5;
		$jour_c['Sat'] = 6;
		$jour_c['Sun'] = 7;
	
		$time_j=mktime(0, 0, 1, $m, $d, $y);
		
		$time_tomorrow=$time_j+(24 * 60 * 60);
	
		$time_f_sd=$time_j-($jour_c[$D] * 60 * 60 * 24);
		$time_d_sd=$time_f_sd - (7 * 24 * 60 * 60);
	
		$time_d_s=$time_f_sd;
		$time_f_s=$time_d_s + (7 * 24 * 60 * 60);
	
		$time_d_sp=$time_f_s;
		$time_f_sp=$time_d_sp + (7 * 24 * 60 * 60);
	
		if($fr == "today"){
			return $time_j;
		}elseif($fr == "tomorrow"){
			return $time_tomorrow;
		}elseif($fr == "endOfLastWeek"){
			return $time_f_sd;
		}elseif($fr == "startOfLastWeek"){
			return $time_d_sd;
		}elseif($fr == "startOfWeek"){
			return $time_d_s;
		}elseif($fr == "endOfWeek"){
			return $time_f_s;
		}elseif($fr == "startOfNextWeek"){
			return $time_d_sp;
		}elseif($fr == "endOfNextWeek"){
			return $time_f_sp;
		}
	
	}
	
	static function date_fr_texte ($date, $today = 0) {
	
		$time=time();
		$d = date('d', $time);
		$D = date('D', $time);
		$m = date('m', $time);
		$y = date('y', $time);
		$time_j2=(mktime(23, 59, 59, $m, $d, $y)-((23*60*60)+60*60))-((23*60*60)+60*60);
			
		$date_dif=time()-$date;
		$date_dif2 = time()-Tools::dateWeek("today");
		
		if($date > time()){
			$date_ok="Bientôt";
		}elseif($today == 1 && $date_dif < $date_dif2){
			$date_ok="Aujourd'hui";
		}elseif($today == 0 && $date_dif < $date_dif2){
			if ($date_dif<60){
				$date_ok="À l'instant";
			}
			elseif ($date_dif<120){
				$date_ok="Il y a ".round($date_dif/60)." min";
			}
			elseif ($date_dif<3600){
				$date_ok="Il y a ".round($date_dif/60)." min";
			}
			elseif ($date_dif<3*60*60){
				$heure= floor($date_dif / 3600);
				$minutes = round(($date_dif % 3600)/60);
				if($minutes >1){
					$plus="s";
				}
				if($heure >1){
					$plus2="s";
				}
				$date_ok="Il y a ".$heure." heure".$plus2/*." et ".$minutes." minute".$plus*/;
			}
			elseif ($date_dif < $date_dif2){
				$date_ok="À ".date('H', $date).'h'.date('i', $date);
			}
		}
		elseif($today == 1){
			if($date < $time_j2){
				$date_ok = Tools::date($date, 'jour');
			}else{
				$date_ok = "Hier";
			}
		}else{
			if($date < $time_j2){
				$date_ok = "Le ".date("d/m/Y", $date);
			}else{
				$date_ok = "Hier"." à ".date('H', $date).'h'.date('i', $date);
			}
		}
	
		return $date_ok;
	}
	
	static function date($time, $type){
	
		$month=array();
		$month['01']="Janv.";
		$month['02']="Fev.";
		$month['03']="Mars";
		$month['04']="Avril";
		$month['05']="Mai";
		$month['06']="Juin";
		$month['07']="Juil.";
		$month['08']="Aout";
		$month['09']="Sept.";
		$month['10']="Oct.";
		$month['11']="Nov.";
		$month['12']="Dec.";
	
		$jour=array();
		$jour['Mon'] = "Lundi";
		$jour['Tue'] = "Mardi";
		$jour['Wed'] = "Mercredi";
		$jour['Thu'] = "Jeudi";
		$jour['Fri'] = "Vendredi";
		$jour['Sat'] = "Samedi";
		$jour['Sun'] = "Dimanche";
	
		$jour2=array();
		$jour2['Mon'] = "LUN.";
		$jour2['Tue'] = "MAR.";
		$jour2['Wed'] = "MER.";
		$jour2['Thu'] = "JEU.";
		$jour2['Fri'] = "VEN.";
		$jour2['Sat'] = "SAM.";
		$jour2['Sun'] = "DIM.";
	
		$jour_c=array();
		$jour_c['Mon'] = 1;
		$jour_c['Tue'] = 2;
		$jour_c['Wed'] = 3;
		$jour_c['Thu'] = 4;
		$jour_c['Fri'] = 5;
		$jour_c['Sat'] = 6;
		$jour_c['Sun'] = 7;
	
		if($time == -1){
			return 'Pas de date disponible';
		}elseif($type == "nbr"){
	
			$date = date('D', $time);
			$date2 = date('d', $time);
			$datem = date('m', $time);
			$datey = date('Y', $time);
			return $date2."/".$datem."/".$datey;
				
		}elseif($type == "fr"){
				
			$date = date('D', $time);
			$date2 = date('d', $time);
			$datem = date('m', $time);
			$datey = date('Y', $time);
			return $jour[$date]." ".$date2." ".$month[$datem]." ".$datey;
				
		}elseif($type == "fr2"){
				
			$date = date('D', $time);
			$date2 = date('d', $time);
			$datem = date('m', $time);
			$datey = date('Y', $time);
			return $jour2[$date]." ".$date2." ".$month[$datem];
				
		}elseif($type == "JOUR"){
				
			$date = date('D', $time);
			return $jour2[$date];
				
		}elseif($type == "jour"){
				
			$date = date('D', $time);
			return $jour[$date];
				
		}
	
	}
	
	static function isValidPseudo($pseudo){
		$caracteres = "abcdefghijklmnopqrstuvwxyz_-1234567890";
	
		$split = str_split($pseudo);
	
		foreach ($split as $cle=>$val){
			if(!stristr($caracteres, $val)){
				return false;
			}
		}
		return true;
	}
	
	static function VerifierAdresseMail($adresse)
	{
		$Syntaxe='#^[\w.-]+@[\w.-]+\.[a-zA-Z]{2,6}$#';
		if(preg_match($Syntaxe,$adresse))
			return true;
		else
			return false;
	}
	
	//fonction limitation de mots
	static function debutchaine($chaine, $nblettres, $option = '') { // 1er argument : chaîne - 2e argument : nombre de mots
		$chaine = preg_replace('!<br.*>!iU', "", $chaine); // remplacement des BR par des espaces
		$chaine = strip_tags($chaine);
		$chaine = preg_replace('/\s\s+/', ' ', $chaine); // retrait des espaces inutiles
		
		if($option == ''){
			if (strlen($chaine)>$nblettres){ $affiche=substr($chaine,0,$nblettres-3)." ...";}else{$affiche=$chaine;}
		}else{
			if (strlen($chaine)>$nblettres){ $affiche=substr($chaine,0,$nblettres);}else{$affiche=$chaine;}
		}
		return $affiche;
	}
	
	//GERE L'AFFICHAGE DES ERREURS
	static function error($string, $type=""){
		if($type=="erreur")
			$return = "Aïe aïe aïe";
		elseif($type=="neutre")
			$return = "<div class='error-neutre'>".$string."</div>";
		elseif($type=="calendar"||$type=="no-statut"||$type=="serie"||$type=="news"||$type=="challenge")
			$return = "<div class='error-empty $type'>".$string."</div>";
		else
			$return = "<div class='error-caution'>".$string."</div>";
		return $return;
		
		
	}
	
	/**
	 * Get either a Gravatar URL or complete image tag for a specified email address.
	 *
	 * @param string $email The email address
	 * @param string $s Size in pixels, defaults to 80px [ 1 - 2048 ]
	 * @param string $d Default imageset to use [ 404 | mm | identicon | monsterid | wavatar ]
	 * @param string $r Maximum rating (inclusive) [ g | pg | r | x ]
	 * @param boole $img True to return a complete IMG tag False for just the URL
	 * @param array $atts Optional, additional key/value attributes to include in the IMG tag
	 * @return String containing either just a URL or a complete image tag
	 * @source http://gravatar.com/site/implement/images/php/
	 */
	static function get_gravatar( $email, $s = 80, $d = 'mm', $r = 'g', $img = false, $atts = array() ) {
		$url = 'http://www.gravatar.com/avatar/';
		$url .= md5( strtolower( trim( $email ) ) );
		$url .= "?s=$s&d=$d&r=$r";
		if ( $img ) {
			$url = '<img src="' . $url . '"';
			foreach ( $atts as $key => $val )
				$url .= ' ' . $key . '="' . $val . '"';
			$url .= ' />';
		}
		return $url;
	}
	
	static function scanAllBoxe(){
	    $sql = "SELECT * FROM users;";
	    $rst = mysql_query($sql);
	    while($rslt = mysql_fetch_assoc($rst)){
	      $user = new Cloud($rslt['id']);
	      $user->scan();
	    }
	  }
	  
	static function scanBoxe($id){
	    $sql = "SELECT * FROM users WHERE id = '".$id."';";
	    $rst = mysql_query($sql);
	    while($rslt = mysql_fetch_assoc($rst)){
	      $user = new Cloud($rslt['id']);
	      $user->scan();
	    }
	  }
	  
	static function convertFileSize($bytes)
    {

    	    if($bytes == "")
    	$bytes = 0;
    
    if ($bytes >= 1024*1024*1024)
    // Go
    return round(($bytes / 1024)/1024/1024, 2) ." Go";

    elseif ($bytes >= 1024*1024)
    // Mo
    return round(($bytes / 1024)/1024, 2) ." Mo";


    elseif ($bytes >= 1024)
    // ko
    return round(($bytes / 1024), 2) ." Ko";

    else
    // octets
    return $bytes ." O";
    }

    static function convertAccueil($bytes)
    {
    if ($bytes >= 1024*1024*1024)
    // Go
    return "<strong style='font-size: 20pt;' >".round(($bytes / 1024)/1024/1024, 2) ."</strong> To";
    if ($bytes >= 1024*1024)
    // Go
    return "<strong style='font-size: 20pt;' >".round(($bytes / 1024)/1024)."</strong> Go";

    elseif ($bytes >= 1024)
    // Mo
    return "<strong style='font-size: 20pt;' >".round(($bytes / 1024)/1024) ."</strong> Mo";


    else
    // ko
    return "<strong style='font-size: 20pt;' >".round(($bytes / 1024)) ."</strong> ko";
    }
    
     static function convertBoxe($bytes)
    {
    if ($bytes >= 1024*1024*1024*1024)
    // Go
    return "<strong>".round(($bytes / 1024)/1024/1024/1024, 2) ."</strong> To";
    if ($bytes >= 1024*1024*1024)
    // Go
    return "<strong>".round(($bytes / 1024)/1024/1024) ."</strong> Go";

    elseif ($bytes >= 1024*1024)
    // Mo
    return "<strong>".round(($bytes / 1024)/1024) ."</strong> Mo";


    elseif ($bytes >= 1024*1024*1024)
    // ko
    return "<strong>".round(($bytes / 1024)) ."</strong> ko";
    else
    	return "<strong>0</strong> o";
    }
    
    static function zipDir($path,&$zip)
	{
	   
	   if (!is_dir($path)) return;
	   
	   if (!($dh = @opendir($path))) {
	      return false;
	   }
	   while ($file = readdir($dh)) {
	     
	      if ($file == "." || $file == "..") continue; // Throw the . and .. folders
	      if (is_dir($path."/".$file)) { // Recursive call
	         zipDir($path."/".$file,$zip,$i);   
	      } elseif (is_file($path."/".$file)) { // If this is a file then add to the zip file
	         
	         $zip->addFile(file_get_contents($path."/".$file),$path."/".$file);
	      }
	      }
	}
    
    static function clearDir($dossier) {
	    $ouverture=@opendir($dossier);
	    if (!$ouverture){
	    	unlink($dossier);
	    }
	    while($fichier=readdir($ouverture)) {
	      if ($fichier == '.' || $fichier == '..') continue;
	        if (is_dir($dossier."/".$fichier)) {
	          $r=Tools::clearDir($dossier."/".$fichier);
	          if (!$r) return false;
	        }
	        else {
	          $r=unlink($dossier."/".$fichier);
	          if (!$r) return false;
	        }
	    }
	  closedir($ouverture);
	  $r=@rmdir($dossier);
	  if (!$r) return false;
	    return true;
	  }

	static function dirsize($rep)
	{
	    $r = @opendir($rep);
	    while( $dir=@readdir($r) )
	    {
	        if( !in_array($dir, array("..", ".")) )
	        {
	            if( is_dir("$rep/$dir") )
	            {
	                $t += Tools::dirsize("$rep/$dir");
	            }
	            else
	            {
	                $t += @filesize("$rep/$dir");
	            }
	        }
	    }
	    @closedir($r);
	    return $t;
	}
	
	static function get_server_memory_usage(){
	 
		$free = shell_exec('free');
		$free = (string)trim($free);
		$free_arr = explode("\n", $free);
		$mem = explode(" ", $free_arr[1]);
		$mem = array_filter($mem);
		$mem = array_merge($mem);
		$memory_usage = round(($mem[2]-$mem[5]-$mem[4])/$mem[1]*100);
	 
		return $memory_usage;
	}
	
	static function get_server_cpu_usage(){
 
		$load = sys_getloadavg();
		return $load[0];
	 
	}
	
	static function getDownloadFtpLogUsers($srchFile, $user) {
		
		$ftplog = "/var/log/pure-ftpd/transferXfer.log";
	
		$cmdLog = "cat $ftplog|/bin/grep ".str_replace(' ','_',$srchFile);
	
		$dlInfos=trim( @ shell_exec($cmdLog) );
		if ($dlInfos) {
			$ftpusers = explode("\n", $dlInfos);
			foreach ($ftpusers as $key=>$value) {
			
				$lineWords=explode(' ',$value);
				$username=$lineWords[13];
				$hostname=$lineWords[6];
				$complete=$lineWords[17];
				$size=0.0+($lineWords[7]);
	
				if ($complete=="c") {
	
					if ($username == $user) {
						
						$sql = "SELECT * FROM files WHERE link LIKE '".$srchFile."';";
						$rst = mysql_query($sql);
						$rslt = mysql_fetch_assoc($rst);
						
						if($rslt['id']){
							$sql = "INSERT INTO checkDownload VALUE('".$rslt['id']."', '".Core::idCo()."', '".time()."');";
							mysql_query($sql);
							
							return true;
						}
					}
				}
			}
		}
		return false;
	}
	
	
}
?>
