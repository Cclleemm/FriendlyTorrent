<?php
/*########################################################
 *
 *   Controller Boxe
 *
#########################################################*/

class Boxe extends Controller{

	////////////////////////////////////
	// CHARGEMENT DES MODELS UTILISES //
	////////////////////////////////////

        var $models = array('cloud', 'download', 'xfer');
        var $title = array('index' => LANG_TITLE);

	////////////////////////////////////
	//          ACTION INDEX          //
	////////////////////////////////////
	
    function index(){
		$d = array();
		
		Tools::scanBoxe(Core::idCo());
		
		$boxe = new Cloud();
		$d['boxe'] = $boxe->boxeData;

		$xfer = new Xfer(Core::idCo());
		$xfer->scanTorrent();
		$stats = $xfer->getStat();
		
		$d['uploadTotal'] = $stats['up'];
		$d['downloadTotal'] = $stats['down'];

		$d['color'] = $d['boxe']['couleur'];
		
		$totalspace = disk_total_space(ROOT_DOWNLOADS);
		$freespace = disk_free_space(ROOT_DOWNLOADS);

		$free = 100;
		
		$d['pourcent'] = round((Tools::dirsize($boxe->boxeData['boxe']) / $totalspace)*100);
		$d['space'] = Tools::dirsize($boxe->boxeData['boxe']);
		
		$payment = new Payment(Core::idCo());
		$d['active'] = $payment->getActive();
		
        $this->set($d);
        $this->render('index');
    }
    
    function users($login){
		$d = array();
		
		$boxe = new Cloud($login);
		$d['boxe'] = $boxe->boxeData;	
		
		Tools::scanBoxe($boxe->boxeData['id']);
    	
    	$xfer = new Xfer($boxe->boxeData['id']);
		$xfer->scanTorrent();
		$stats = $xfer->getStat();
		
		$d['uploadTotal'] = $stats['up'];
		$d['downloadTotal'] = $stats['down'];
		
		$this->title['users'] = $login."'s box";
		$this->setTitle();
		
		$totalspace = disk_total_space(ROOT_DOWNLOADS);
		$freespace = disk_free_space(ROOT_DOWNLOADS);

		$free = 100;
		
		$d['pourcent'] = round((Tools::dirsize($boxe->boxeData['boxe']) / $totalspace)*100);
		$d['space'] = Tools::dirsize($boxe->boxeData['boxe']);
		
		
		$d['color'] = $d['boxe']['couleur'];
		
		$payment = new Payment(Core::idCo());
		$d['active'] = $payment->getActive();
		
        $this->set($d);
        $this->render('index');
    }

}
?>