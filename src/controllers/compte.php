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
        var $title = array('index' => LANG_TITLE_MY_ACCOUNT);

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
					$d['java'] = '<script>$.notification({ content: "'.LANG_PASSWORD_CHANGED.'", title: "'.LANG_PASSWORD_CHANGED.'", icon: "&#9749;" });</script>';
				}else{
					$d['error'] = LANG_PASSWORD_DIFERENT;
				}
			}else{
				$d['error'] = LANG_OLD_PASSWORD_DIFERENT;
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