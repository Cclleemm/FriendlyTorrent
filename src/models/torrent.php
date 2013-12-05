<?php

require_once(ROOT."models/Transmission.php");
require_once(ROOT."models/rpc.php");

class Torrent extends Common{
	////////////////////////////////////
	// DECLARATION DES DONNEES MEMBRES//
	////////////////////////////////////
	
	var $torrentData = null;
	
	////////////////////////////////
	// DECLARATION DU CONSTRUCTEUR//
	////////////////////////////////
	//Construction par ID ou par nom du membre : 
	//new User(1) ou new User("clement")
	
	function __construct($id){
		parent::__construct();
		
		if($id){
			$sql = "SELECT torrents.*, users.login FROM torrents, users WHERE torrents.idBoxe = users.id AND torrents.id = '".$id."';";
			$rst = $this->bdd->query($sql);
			$this->torrentData = mysql_fetch_assoc($rst);
			
			$stat = new StatFile(ROOT_DOWNLOADS.'.transferts/'.$this->torrentData['name']);
				
			$this->torrentData['percent_done'] = $stat->percent_done ;
			$this->torrentData['running'] = $stat->running ;
			$this->torrentData['up_speed'] = $stat->up_speed ;
			$this->torrentData['down_speed'] = $stat->down_speed ;
			$this->torrentData['time_left'] = $stat->time_left ;
			$this->torrentData['statut'] = $stat->statut ;
			$this->torrentData['downtotal'] = Tools::convertFileSize($stat->downtotal) ;
			$this->torrentData['uptotal'] = Tools::convertFileSize($stat->uptotal) ;
			$this->torrentData['size'] = Tools::convertFileSize($stat->size) ;
			$this->torrentData['sharing'] = $stat->sharing ;
			$this->torrentData['eta'] = $stat->eta ;
			$this->torrentData['seeds'] = $stat->seeds ;
			$this->torrentData['peers'] = $stat->peers ;
			$this->torrentData['cons'] = $stat->cons ;
			$this->torrentData['time'] = Tools::date_fr_texte($this->torrentData['time']);
			$this->torrentData['peersList'] = $stat->peersList ;
			//$this->torrentData['files'] = $stat->files ;
			
			$tab = explode('-', $this->torrentData['name']);
			//print_r($tab);
			$name = '';
			for($t = 0; $t < count($tab)-1; $t++){
				$name .= $tab[$t].'-';
			}
			
			$name = substr($name,0, -1);
			
			$this->torrentData['nameTorrent'] = $name;
			
			// $show_run + $statusStr
			if($this->torrentData['percent_done'] >= 100) {
				$statusStr = (($this->torrentData['running'] == 1) && (trim($this->torrentData['up_speed']) != "")) ? 'Envoi' : 'Fini';
			} else if ($this->torrentData['running'] == 0) {
				$statusStr = 'Arrêté';
			} else {
				$statusStr = (($this->torrentData['eta'] == -1)) ? 'Vérification' : 'Téléchargement';
			}
			
			//if($stat->error == 1)
			//	$statusStr = "Erreur";

			$this->torrentData['statut'] = $statusStr;

		}
	}

	function stop(){
		$sf = new StatFile(ROOT_DOWNLOADS.'.transferts/'.$this->torrentData['name']);
		$user= new User($this->torrentData['idBoxe']);
		$transmission = new Transmission($user->configRPC());

		if (!$transmission->isRunning()) {
			// write error to stat
			$sf->time_left = 'Error: RPC down';
			$sf->write();

			// return
			return false;
		}

		if (stopTransmissionTransfer($this->torrentData['hash'], $transmission)) {
			$sf->stop();
			return true;
		}else{
			return false;
		}
	}
	
	function delete(){
		$sf = new StatFile(ROOT_DOWNLOADS.'.transferts/'.$this->torrentData['name']);

		$user= new User($this->torrentData['idBoxe']);
		$transmission = new Transmission($user->configRPC());
		

		if(deleteTransmissionTransfer($this->torrentData['hash'], false, $transmission)){
			$xfer = new Xfer(Core::idCo());
			$xfer->deleteTorrent($this->torrentData['id']);
		
			unlink(ROOT_DOWNLOADS.'.transferts/'.$this->torrentData['name'].".stat");
			unlink(ROOT_DOWNLOADS.'.transferts/'.$this->torrentData['name']);

			if($this->torrentData['percent_done'] < 100 && $this->torrentData['datapath'] != ""){
				if(is_dir(ROOT_DOWNLOADS.$user->userData['login'].'/'.$this->torrentData['datapath'])){
					Tools::clearDir(ROOT_DOWNLOADS.$user->userData['login'].'/'.$this->torrentData['datapath']);
				}else{
					Tools::clearDir(ROOT_DOWNLOADS.$user->userData['login'].'/'.$this->torrentData['datapath']);
				}
			}

			$sql = "DELETE FROM torrents WHERE id = '".$this->torrentData['id']."';";
			$this->bdd->query($sql);

			return true;
		}else{
			return false;
		}
	}

	function start(){
		if(!Core::isCo())
			return false;
			
		$user= new User($this->torrentData['idBoxe']);
		$transmission = new Transmission($user->configRPC());

		$sf = new StatFile(ROOT_DOWNLOADS.'.transferts/'.$this->torrentData['name']);

		if (!empty($this->torrentData['hash'])) {
			$result = $transmission->add(ROOT_DOWNLOADS.'.transferts/'.$this->torrentData['name']);

			if (is_array($result) && $result["result"] == "duplicate torrent") {
				$res = (int) startTransmissionTransfer($this->torrentData['hash'], false, array(), $transmission);
				$sf->start();

				if (!$res) {
					$sf->time_left = 'Error : not restarting';
					$sf->write();

					return false;
				}

				return true;
			}else{
				return true;
			}
		}else{
			$core = Core::getInstance(); 
			$hash = Torrent::getTransferHash($this->torrentData['name']);

			if(!$hash)
				return false;

			$result = $transmission->add(ROOT_DOWNLOADS.'.transferts/'.$this->torrentData['name']);
			$sql = "UPDATE torrents SET hash = '".$hash."' WHERE id = '".$this->torrentData['id']."';";
			$core->bdd->query($sql);

			$xfer = new Xfer(Core::idCo());
			$xfer->deleteTorrent($this->torrentData['id']);

			return true;
		}
	}

	/**
	 * gets datapath of a transfer.
	 *
	 * @param $transfer name of the torrent
	 * @return var with transfer-datapath or empty string
	 */
	static function getTransferDatapath($transfer) {
			// this is a torrent-client
			$ftorrent = ROOT_DOWNLOADS.".transferts/".$transfer;
			$alltorrent = @file_get_contents($ftorrent);
			if ($alltorrent == "") return "";
			
			$btmeta = @BDecode($alltorrent);

			$datapath = (empty($btmeta['info']['name']))
				? ""
				: trim($btmeta['info']['name']);
		return $datapath;
	}

	/**
	 * gets metainfo of a torrent as string
	 *
	 * @param $transfer name of the torrent
	 * @return string with torrent-meta-info
	 */
	static function getTorrentMetaInfo($transfer) {
		$tornadoBin = ROOT."tornado/btshowmetainfo.py";
		$pyCmd = "/usr/bin/python -OO";
		$ftorrent = ROOT_DOWNLOADS.".transferts/".$transfer;

		return shell_exec($pyCmd." ".$tornadoBin." '".$ftorrent."'");
	}

	/**
	 * gets hash of a transfer
	 *
	 * @param $transfer name of the transfer
	 * @return transfer-hash
	 */
	static function getTransferHash($transfer) {
		
		// this is a torrent-client
		$metainfo = Torrent::getTorrentMetaInfo($transfer);
		if (empty($metainfo)) {
			$hash = "";
		} else {
			$resultAry = explode("\n", $metainfo);
			$hashAry = array();
			if (isset($resultAry[3]))
				$hashAry = explode(":", trim($resultAry[3]));
			$hash = (isset($hashAry[1])) ? trim($hashAry[1]) : "";
		}
		return $hash;
	}

	static function creat($name, $nameSimple){
		if(!Core::isCo())
			return false;

		$userTmp = new User();
		$user = $userTmp->userData['login'];
		$torrent = ROOT_DOWNLOADS.".transferts/".$name;

		$transmission = new Transmission($userTmp->configRPC());

		$sf = new StatFile($torrent, $user);

		if (!$transmission->isRunning()) {
			// write error to stat
			$sf->time_left = 'Error: RPC down';
			$sf->write();

			// return
			return false;
		}

		$hash = Torrent::getTransferHash($name);
		
		if (empty($hash) || !isTransmissionTransfer($hash, $transmission)) {
			$result = $transmission->add($torrent);

			if (is_array($result) && $result["result"] == "duplicate torrent") {
				return false;
			}else{
				$core = Core::getInstance(); 
			
				$sql = "INSERT INTO torrents VALUES ('', '".Core::idCo()."', '".time()."', '".addslashes($name)."', '".addslashes(torrent::getTransferDatapath($name))."', '".$hash."');";
				if($core->bdd->query($sql))
					$sf->start();
					return true;
			}
		}else{
			$sf->time_left = 'Error : torrent exist';
			$sf->write();

			return false;
		}
	}	


}

?>
