<?php
/*########################################################
 *
 *   Controller Accueil
 *   Permet la gestion du module Accueil (connecté)
 *
#########################################################*/

class Stats extends Controller{

	////////////////////////////////////
	// CHARGEMENT DES MODELS UTILISES //
	////////////////////////////////////

        var $models = array('xfer', 'payment');
        var $title = array('index' => 'Statistiques du serveur');

	////////////////////////////////////
	//          ACTION INDEX          //
	////////////////////////////////////
	
    function index(){
		$d = array();
		
		$total = 0;
		$totalOff = 0;
		$me = 0;
		
		$sql = "SELECT * FROM torrents;";
		$rst = $this->bdd->query($sql);

		while($rslt = mysql_fetch_assoc($rst)){
			++$totalOff;
			if($rslt['idBoxe'] == Core::idCo())
				++$me;
		}

		$d['total'] = $total;
		$d['totalOff'] = $totalOff;
		$d['me'] = $me;

        $totalspace = disk_total_space(ROOT_DOWNLOADS);
		$freespace = disk_free_space(ROOT_DOWNLOADS);

		// Ne rien changer par la suite
		$usedspace = $totalspace - $freespace;
		$pourcent = round(($usedspace / $totalspace)*100);

		$d['usedspace'] = $usedspace;
		$d['totalspace'] = $totalspace;
		$d['pourcent'] = $pourcent;
		$d['space'] = round(($freespace / $totalspace)*100);
		$d['free'] = Tools::convertFileSize($freespace);

		$sql = "SELECT users.login, users.mail, users.couleur, sessions.lastTime FROM sessions, users WHERE sessions.idUser = users.id AND lastTime >= '".(time()-(5*60))."' GROUP BY users.login ORDER BY sessions.lastTime DESC;";
		$rst = $this->bdd->query($sql);

		$d['connect'] = $rst;

		
		$xfer = new Xfer();
		$stats = $xfer->getStatTotal();
		
		$d['uploadTotal'] = $stats['up'];
		$d['downloadTotal'] = $stats['down'];
		
        $this->set($d);
        $this->render('stats');
    }

}
?>