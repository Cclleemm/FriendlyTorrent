<?php
require_once(ROOT."models/Transmission.php");
require_once(ROOT."models/rpc.php");

class TorrentsM extends Common{
	////////////////////////////////////
	// DECLARATION DES DONNEES MEMBRES//
	////////////////////////////////////
	
	
	////////////////////////////////
	// DECLARATION DU CONSTRUCTEUR//
	////////////////////////////////
	//Construction par ID ou par nom du membre : 
	//new User(1) ou new User("clement")
	
	function __construct(){
		parent::__construct();
	}

	function updateStatFiles($idUser) {

		$user= new User($idUser);
		try {
		   	$rpc = new Transmission($user->configRPC());
		} catch (Exception $e) {
		    echo "unable to connect to transmission-daemon\n";
			return;
		}

		$tfs = $rpc->torrent_get_tf();

		if (empty($tfs)) {
			return;
		}

		$sql = "SELECT hash, name FROM torrents WHERE idBoxe = '".$idUser."'";

		$hashes = array("''");
		foreach ($tfs as $hash => $t) {
			$hashes[] = "'".strtolower($hash)."'";
		}
		$sql .= " AND hash IN (".implode(',',$hashes).")";

		$rst = $this->bdd->query($sql);

		$hashes=array();
		$sharekills=array();
		while ($rslt = mysql_fetch_assoc($rst)) {
			$hash = strtolower($rslt['hash']);
			$hashes[$hash] = $rslt['name'];

			if (!isset($tfs[$hash])) {
				$sf = new StatFile(ROOT_DOWNLOADS.'.transferts/'.$rslt['name']);
				$sf->running = 0;
				$sf->write();
			}
		}
		
		$totalRateUp = 0;
		$totalRateDown = 0;
		$nbUpdate=0;
		$missing=array();
		foreach ($tfs as $hash => $t) {
			$transfer = $hashes[$hash];

			//file_put_contents($cfg["path"].'.Transmission/'."updateStatFiles4.log",serialize($t));
			$sf = new StatFile(ROOT_DOWNLOADS.'.transferts/'.$transfer);

				$sf->running = $t['running'];
				$sf->eta = $t['eta'];
				
				if ($sf->running) {

					if ($t['eta'] > 0) {
						$sf->time_left = convertTimeText($t['eta']);
					}else{
						$sf->time_left = '-';
					}

					$sf->percent_done = $t['percentDone'];
					$sf->sharing = round($t['sharing'],1);

					if ($t['status'] != 9 && $t['status'] != 5) {
						$sf->peers = $t['peers'];
						$sf->seeds = $t['seeds'];
					}

					if ($t['seeds'] >= 0)
						$sf->seeds = $t['seeds'];

					if ($t['peers'] >= 0)
						$sf->peers = $t['peers'];
						
					$sf->peersList = $t['peersList'];
					$sf->files = $t['files'];
						
					if ($t['cons'] >= 0)
						$sf->cons = $t['cons'];

					if ((float)$t['speedDown'] >= 0.0)
						$sf->down_speed = formatBytesTokBMBGBTB($t['speedDown'])."/s";
					if ((float)$t['speedUp'] >= 0.0)
						$sf->up_speed = formatBytesTokBMBGBTB($t['speedUp'])."/s";
						
					$totalRateUp += $t['speedUp'];
					$totalRateDown += $t['speedDown'];

					if ($t['status'] == 8) {
						$sf->percent_done = 100 + $t['sharing'];
						$sf->down_speed = "&nbsp;";
						if (trim($sf->up_speed) == '')
							$sf->up_speed = "&nbsp;";
					}
					if ($t['status'] == 9) {
						$sf->percent_done = 100 + $t['sharing'];
						$sf->up_speed = "&nbsp;";
						$sf->down_speed = "&nbsp;";
					}

					if($t['trackerStats'][0]['lastAnnounceSucceeded'] != 1){
						$sf->time_left = $t['trackerStats'][0]['lastAnnounceResult'];
						$sf->error = 1;
					}

					/*echo '<pre>';
					print_r($t);
					echo '</pre>';*/

					/*if($t['error']){
						$sf->time_left = $t['errorString'];
						$sf->error = 1;
					}*/

				} else {
					//Stopped or finished...

					$sf->down_speed = "";
					$sf->up_speed = "";
					$sf->peers = "";
					$sf->time_left = "-";
					if ($t['eta'] < -1) {
						$sf->time_left = "Done in ".convertTimeText($t['eta']);
					} elseif ($sf->percent_done >= 100 && strpos($sf->time_left, 'Done') === false && strpos($sf->time_left, 'Finished') === false) {
						$sf->percent_done = 100;
						$sf->time_left = "Done!";
					}

					if ($sf->sharing == 0)
						$sf->sharing = round($t['sharing'],1);
				}

				$sf->downtotal = $t['downTotal'];
				$sf->uptotal = $t['upTotal'];

				if ($sf->size == 0)
					$sf->size = $t['size'];
					
				echo $sf->seeds.'|';
					
				if ($sf->write()) {
					$nbUpdate++;
				}
			
		}
		
		$this->bdd->getCache()->set($idUser."Stats", array('rateUp' => $totalRateUp, 'rateDown' => $totalRateDown), false, 10);
	}

	function refresh(){
		$sql = "SELECT id FROM users;";
		$rst = $this->bdd->query($sql);
		while($rslt = mysql_fetch_assoc($rst)){
			$this->updateStatFiles($rslt['id']);
		}
	}

	function myTorrents(){

		$torrents = array();

		$sql = "SELECT id FROM torrents WHERE idBoxe = '".Core::idCo()."' ORDER BY time DESC;";
		$rst = $this->bdd->query($sql);
		while($rslt = mysql_fetch_assoc($rst)){
			$torrent = new Torrent($rslt['id']);
			$torrents[] = $torrent;
		}

		return $torrents;
	}
	
	function torrents(){

		$torrents = array();

		$sql = "SELECT id FROM torrents ORDER BY time DESC;";
		$rst = $this->bdd->query($sql);
		while($rslt = mysql_fetch_assoc($rst)){
			$torrent = new Torrent($rslt['id']);
			$torrents[] = $torrent;
		}

		return $torrents;
	}
		
}

?>