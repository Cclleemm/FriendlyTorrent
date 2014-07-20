<?php


class Rss extends Common{
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
		
			$fluxrss=simplexml_load_file($url, 'SimpleXMLElement', LIBXML_NOCDATA);
			
			
			foreach($fluxrss->channel->item as $item){
	            $sql = "SELECT * FROM cacheRss WHERE url = '".addslashes($item->link)."';";
	            $rst = $this->bdd->query($sql);
	            $rslt = mysql_fetch_assoc($rst);
	            
	            if($rslt['id']){
		            $id = $rslt['id'];
		            $nameFile = $rslt['nameFile'];
	            }else{
			    $e = explode("/", $this->get_final_url($item->link));
		            $nameFile = $e[(count($e)-1)];
		            
		            if($nameFile){
		           	 	$sqlI = "INSERT INTO cacheRss VALUE('', '".addslashes($item->link)."', '".addslashes($nameFile)."');";
				   	 	$this->bdd->query($sqlI);
				   	 	$id = mysql_insert_id();
				   	}
	            }
	            
	            if($nameFile){
		            $nameFile = str_replace(".torrent", "", $nameFile);
		            
		            $user = new User();
		            
		            $sql = "SELECT torrents.id, users.id as uid, users.login FROM torrents, users WHERE users.id = torrents.idBoxe AND name = '".addslashes($nameFile)."-".md5($user->userData['id'])."';";
		            $rst = $this->bdd->query($sql);
		            $rslt = mysql_fetch_assoc($rst);
		            
		            if($rslt['id']){
		            	if($rslt['uid'] == Core::idCo()){
		            		$etat = "Téléchargement en cours";
		            		$dl = false;
		            	}else{
		            		$etat = "Téléchargement en cours par ".$rslt['login'];
		            		$dl = true;
		            	}
		            }else{
		            	$etat = "A télécharger";
		            	$dl = true;
		            }
		            
		            $this->rssData[] = array('name' => $item->title, 'id' => $id, 'etat' => $etat, 'isDwn' => $dl);
		        }else{
			        $this->rssData[] = array('name' => array('Flux Rss incorrect'), 'id' => 0, 'etat' => 'Erreur', 'isDwn' => 0);
		        }
	        }
		}
	}

	/**
	 * get_redirect_url()
	 * Gets the address that the provided URL redirects to,
	 * or FALSE if there's no redirect. 
	 *
	 * @param string $url
	 * @return string
	 */
	function get_redirect_url($url){
	    $redirect_url = null; 

	    $url_parts = @parse_url($url);
	    if (!$url_parts) return false;
	    if (!isset($url_parts['host'])) return false; //can't process relative URLs
	    if (!isset($url_parts['path'])) $url_parts['path'] = '/';

	    $sock = fsockopen($url_parts['host'], (isset($url_parts['port']) ? (int)$url_parts['port'] : 80), $errno, $errstr, 30);
	    if (!$sock) return false;

	    $request = "HEAD " . $url_parts['path'] . (isset($url_parts['query']) ? '?'.$url_parts['query'] : '') . " HTTP/1.1\r\n"; 
	    $request .= 'Host: ' . $url_parts['host'] . "\r\n"; 
	    $request .= "Connection: Close\r\n\r\n"; 
	    fwrite($sock, $request);
	    $response = '';
	    while(!feof($sock)) $response .= fread($sock, 8192);
	    fclose($sock);

	    if (preg_match('/^Location: (.+?)$/m', $response, $matches)){
	        if ( substr($matches[1], 0, 1) == "/" )
	            return $url_parts['scheme'] . "://" . $url_parts['host'] . trim($matches[1]);
	        else
	            return trim($matches[1]);

	    } else {
	        return false;
	    }

	}

	/**
	 * get_all_redirects()
	 * Follows and collects all redirects, in order, for the given URL. 
	 *
	 * @param string $url
	 * @return array
	 */
	function get_all_redirects($url){
	    $redirects = array();
	    while ($newurl = $this->get_redirect_url($url)){
	        if (in_array($newurl, $redirects)){
	            break;
	        }
	        $redirects[] = $newurl;
	        $url = $newurl;
	    }
	    return $redirects;
	}

	/**
	 * get_final_url()
	 * Gets the address that the URL ultimately leads to. 
	 * Returns $url itself if it isn't a redirect.
	 *
	 * @param string $url
	 * @return string
	 */
	function get_final_url($url){
	    $redirects = $this->get_all_redirects($url);
	    if (count($redirects)>0){
	        return array_pop($redirects);
	    } else {
	        return $url;
	    }
	}

}

?>
