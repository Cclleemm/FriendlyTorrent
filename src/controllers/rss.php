<?php
/*########################################################
 *
 *   Controller Rss
 *
#########################################################*/

class Rss extends Controller{

	////////////////////////////////////
	// CHARGEMENT DES MODELS UTILISES //
	////////////////////////////////////

        var $models = array('payment');
        var $title = array('index' => 'Flux RSS');

	////////////////////////////////////
	//          ACTION INDEX          //
	////////////////////////////////////
	
    function index(){
		$d = array();
		
		$d['lien'] = $this->user->userData['rss'];
		
		$payment = new Payment(Core::idCo());
		$d['active'] = $payment->getActive();
		
        $this->set($d);
        $this->render('index');
    }

}
?>