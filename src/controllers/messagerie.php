<?php
/*########################################################
 *
 *   Controller Messagerie
 *
#########################################################*/

class messagerie extends Controller{

	////////////////////////////////////
	// CHARGEMENT DES MODELS UTILISES //
	////////////////////////////////////

        var $models = array('user');
        var $title = array('index' => 'Messagerie');

	////////////////////////////////////
	//          ACTION INDEX          //
	////////////////////////////////////
	
    function index(){
		$d = array();

		
		$sql = "SELECT users.login, users.id, messagerie.idUser, messagerie.idUserTarget, COUNT(messagerie.id) as nb FROM messagerie, users WHERE ((idUserTarget = '".Core::idCo()."' AND idUser != '".Core::idCo()."' AND users.id = messagerie.idUser) OR (idUser = '".Core::idCo()."' AND idUserTarget != '".Core::idCo()."' AND users.id = messagerie.idUserTarget) OR (idUser = '".Core::idCo()."' AND idUserTarget = '".Core::idCo()."' AND users.id = messagerie.idUser)) GROUP BY users.login ;";
		$messages = $this->bdd->query($sql);
		
		$messagesFinal = array();
		
		while($rslt = mysql_fetch_assoc($messages)){
			$sql = "SELECT messagerie.text, messagerie.time, messagerie.idUserTarget, messagerie.seen FROM messagerie WHERE ((idUserTarget = '".$rslt['idUserTarget']."' AND idUser = '".$rslt['idUser']."') OR (idUser = '".$rslt['idUserTarget']."' AND idUserTarget = '".$rslt['idUser']."')) ORDER BY messagerie.time DESC LIMIT 0, 1;";
			$message = $this->bdd->query($sql);
			$messageRst = mysql_fetch_assoc($message);
			
			$rslt['time'] = intval($messageRst['time']);
			$rslt['text'] = $messageRst['text'];
			
			if($messageRst['idUserTarget'] == Core::idCo())
				$rslt['seen'] = $messageRst['seen'];
			else
				$rslt['seen'] = 1;
			
			$messagesFinal[] = $rslt;
		}
		usort($messagesFinal, function($a, $b) {
			
		    if ($a['time'] == $b['time']) {
		        return 0;
		    }
		    
		    return ($a['time'] > $b['time']) ? -1 : 1;
		});
		
		$d['messages'] = $messagesFinal;
		
        $this->set($d);
        $this->render('index');
    }

    
    function chat($idUser){
		$d = array();
		
		$sql = "SELECT messagerie.id, messagerie.text, users.login, users.mail, messagerie.time, messagerie.seen FROM messagerie, users, users as ut WHERE users.id = messagerie.idUser AND ut.id = messagerie.idUserTarget AND (idUserTarget = '".Core::idCo()."' AND idUser = '".$idUser."' OR idUser = '".Core::idCo()."' AND idUserTarget = '".$idUser."') ORDER BY messagerie.time DESC;";
		$d['messages'] = $this->bdd->query($sql);
		
		$sql = "UPDATE messagerie SET seen = 1 WHERE (idUserTarget = '".Core::idCo()."' AND idUser = '".$idUser."');";
		$rst = $this->bdd->query($sql);
		
		$user = new User($idUser);
		
		$d['idUser'] = $user->userData['id'];
		
		$this->title['chat'] = "Conversation avec ".$user->userData['login'];
		$this->setTitle();
		
        $this->set($d);
        $this->render('chat');
    }
    
    function nouveau($idRep){
		$d = array();
		
		$user = new User();
		
		if($this->post['to_user'] && $this->post['message']){

			//NEW message
			$sql = "INSERT INTO messagerie VALUES ('', '".Core::idCo()."', '".$this->post['to_user']."', '".addslashes($this->post['message'])."', 0, '".time()."');";
			$this->bdd->query($sql);
		}
		header('Location: '.WEBROOT.'messagerie/chat/'.$this->post['to_user'].'/');
    }
    
    function delete($id){
	    $sql = "DELETE FROM messagerie WHERE id = '".$id."';";
	    $this->bdd->query($sql);
	    header('Location: '.WEBROOT.'messagerie/');
    }

}
?>