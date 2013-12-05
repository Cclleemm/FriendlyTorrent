<?php
/*########################################################
 *
 *   Controller Torrents
 *
#########################################################*/

class Torrents extends Controller{

	////////////////////////////////////
	// CHARGEMENT DES MODELS UTILISES //
	////////////////////////////////////

        var $models = array('xfer', 'payment');
        var $title = array('index' => 'Mes torrents');

	////////////////////////////////////
	//          ACTION INDEX          //
	////////////////////////////////////
	
    function index(){
		$d = array();
		
		$payment = new Payment(Core::idCo());
		$d['active'] = $payment->getActive();

        $this->set($d);
        $this->render('index');
    }

}
?>