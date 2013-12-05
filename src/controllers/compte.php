<?php
/*########################################################
 *
 *   Controller Compte
 *
#########################################################*/

class Compte extends Controller{

	////////////////////////////////////
	// CHARGEMENT DES MODELS UTILISES //
	////////////////////////////////////

        var $models = array();
        var $title = array('index' => 'Mon compte');

	////////////////////////////////////
	//          ACTION INDEX          //
	////////////////////////////////////
	
    function index(){
		$d = array();

		$d['user'] = $this->user->userData;
		
		if($this->post['oldPass'] != "" && $this->post['newPass'] != "" ){
			if(md5($this->post['oldPass']) == $d['user']['password']){
				if($this->post['newPass'] == $this->post['newPass2']){
					$this->user->changeMdp($this->post['newPass']);
					$d['java'] = '<script>$.notification({ content: "Le mot de passe a bien été changé !", title: "Mot de passe changé !", icon: "&#9749;" });</script>';
				}else{
					$d['error'] = "Les nouveaux mot de passe le correspondent pas !";
				}
			}else{
				$d['error'] = "L'ancien mot de passe ne correspond pas !";
			}
		}
		
		if($this->post['rss'] != $d['user']['rss'] && $this->post['rss']){
			$this->user->changeRss($this->post['rss']);
			$d['user']['rss'] = $this->post['rss'];
		}

        $this->set($d);
        $this->render('index');
    }

}
?>