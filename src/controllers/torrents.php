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

        var $models = array('xfer');
        var $title = array('index' => 'Mes torrents');

	////////////////////////////////////
	//          ACTION INDEX          //
	////////////////////////////////////
	
    function index(){
		$d = array();

        $this->set($d);
        $this->render('index');
    }

}
?>