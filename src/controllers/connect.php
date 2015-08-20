<?php
/*########################################################
 *
 *   Controller Connect
 *
#########################################################*/

class Connect extends Controller{

	////////////////////////////////////
	// CHARGEMENT DES MODELS UTILISES //
	////////////////////////////////////

        var $models = array('user');
        var $title = array('index' => LANG_CONNECTION);

	////////////////////////////////////
	//          ACTION INDEX          //
	////////////////////////////////////
	
    function index(){
		$d = array();
		
		$this->setLayout('external');
		
		$user = new User();
		
		if($_POST['login'] != '' OR $_POST['mdp'] != ''){
			if($user->connect($_POST['login'], $_POST['mdp'])){
				header('Location: '.WEBROOT);
			}
		}
			
		
        $this->set($d);
        $this->render('index');
    }

}
?>