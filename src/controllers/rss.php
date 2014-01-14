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

        var $models = array();
        var $title = array('index' => 'Flux RSS');

	////////////////////////////////////
	//          ACTION INDEX          //
	////////////////////////////////////
	
    function index(){
		$d = array();
		
		$d['lien'] = $this->user->userData['rss'];
		
        $this->set($d);
        $this->render('index');
    }

}
?>