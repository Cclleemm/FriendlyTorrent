<?php


class Xfer extends Common{
	////////////////////////////////////
	// DECLARATION DES DONNEES MEMBRES//
	////////////////////////////////////
	
	private $xfer;
	
	////////////////////////////////
	// DECLARATION DU CONSTRUCTEUR//
	////////////////////////////////
	//Construction par ID ou par nom du membre : 
	//new User(1) ou new User("clement")
	
	function __construct($user = null){
		parent::__construct();
		
		if($user){
			$user = new User($user);
			
			if($user->userId){
				
				$year = date('Y');
				$month = date('n');
				
				$sql = "SELECT * FROM xferUser WHERE idUser = '".$user->userId."' AND `year` = '".$year."' AND `month` = '".$month."';";
				$rst = $this->bdd->query($sql);
				$rslt = mysql_fetch_assoc($rst);
				
				if(!$rslt['idUser']){
					$sql = "INSERT INTO xferUser VALUES ('".$user->userId."', '".$year."', '".$month."', '0', '0');";
					$rst = $this->bdd->query($sql);
				}
				
				$this->xfer = $user->userId;
			}
		}
	}
	
	function addValue($up = 0, $down = 0){
		$year = date('Y');
		$month = date('n');
		
		if($up != 0 || $down != 0){
			$sql = "UPDATE xferUser SET totalUp = totalUp + '".$up."', totalDown = totalDown + '".$down."' WHERE idUser = '".$this->xfer."' AND year = '".$year."' AND month = '".$month."';";
			$rst = $this->bdd->query($sql);
		}
	}
	
	function getStat($year = 0, $month = 0){
	
		if(!$year)
			$year = date('Y');
		if(!$month)
			$month = date('n');
		
		$sql = "SELECT * FROM xferUser WHERE idUser = '".$this->xfer."' AND year = '".$year."' AND month = '".$month."';";
		$rst = $this->bdd->query($sql);
		$rslt = mysql_fetch_assoc($rst);
		
		return (array('down' => $rslt['totalDown'], 'up' => $rslt['totalUp']));
	}
	
	function getStatTotal($year = 0, $month = 0){
	
		if(!$year)
			$year = date('Y');
		if(!$month)
			$month = date('n');
		
		$sql = "SELECT SUM(totalDown) as totalDown, SUM(totalUp) as totalUp FROM xferUser WHERE year = '".$year."' AND month = '".$month."';";
		$rst = $this->bdd->query($sql);
		$rslt = mysql_fetch_assoc($rst);
		
		return (array('down' => $rslt['totalDown'], 'up' => $rslt['totalUp']));
	}
	
	function scanTorrent($id = null){
	
		if($id)
			$sqlPlus = ' id = "'.$id.'" AND';
		else
			$sqlPlus = '';
		
		$sql = "SELECT id, name FROM torrents WHERE".$sqlPlus." idBoxe = '".$this->xfer."';";
		$rst = $this->bdd->query($sql);
		while($rslt = mysql_fetch_assoc($rst)){
		
			$stat = new StatFile(ROOT_DOWNLOADS.'.transferts/'.$rslt['name']);
			
			if($stat->running == 1){
				$sql45 = "SELECT * FROM xferTorrent WHERE idTorrent = '".$rslt['id']."';";
				$rst45 = $this->bdd->query($sql45);
				$rslt45 = mysql_fetch_assoc($rst45);
					if(!$rslt45['idTorrent']){
						$upAdd = $stat->uptotal;
						$downAdd = $stat->downtotal;
						
						$sql55 = "INSERT INTO xferTorrent VALUES ('".$rslt['id']."', '".$stat->uptotal."', '".$stat->downtotal."');";
						$this->bdd->query($sql55);
					}else{
						if($stat->uptotal != $rslt45['lastUp'] || $stat->downtotaltotal != $rslt45['lastDown']){
							$upAdd = $stat->uptotal - $rslt45['lastUp'];
							$downAdd = $stat->downtotal - $rslt45['lastDown'];
							
							$sql55 = "UPDATE xferTorrent SET lastUp = '".$stat->uptotal."', lastDown = '".$stat->downtotal."' WHERE idTorrent = '".$rslt['id']."';";
							$this->bdd->query($sql55);
						}
					}
				$this->addValue($upAdd, $downAdd);
			}
		}
	}
	
	function deleteTorrent($id){
		$this->scanTorrent($id);
		$sql = "DELETE FROM xferTorrent WHERE idTorrent = '".$id."';";
		$this->bdd->query($sql);
	}
	
}

?>