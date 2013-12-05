<?php
/*########################################################
 *
 *   Controller Admin
 *
#########################################################*/

class Admin extends Controller{

	////////////////////////////////////
	// CHARGEMENT DES MODELS UTILISES //
	////////////////////////////////////

        var $models = array('user');
        var $title = array('index' => 'Administration');

	////////////////////////////////////
	//          ACTION INDEX          //
	////////////////////////////////////
	
    function index(){
		$d = array();
		
		if(!Core::isAdmin())
			header('Location: '.DOMAIN.'/');
		
		$d['users'] = User::getUsers();
		
		$d['memusage'] = Tools::get_server_memory_usage();
		$d['cpuusage'] = Tools::get_server_cpu_usage();
		
        $this->set($d);
        $this->render('index');
    }
    
    function editUser($id){
		$d = array();
		
		if(!Core::isAdmin())
			header('Location: '.DOMAIN.'/');
		
		$user = new User($id);
		$d['user'] = $user->userData;
		
		if(!$d['user'])
			header('Location: '.DOMAIN.'/admin/');
			
		if($_GET['admin']){
			$sql = "SELECT COUNT(*) as nb FROM users WHERE admin = 1";
			$rst = $this->bdd->query($sql);
			$rslt = mysql_fetch_assoc($rst);
			
			if($rslt['nb'] <= 1 && $_GET['value'] == 0){
				header('Location: '.DOMAIN.'admin/?alert=adminUserFail');
				exit();
			}
			
			$user->isAdmin($_GET['value']);
			header('Location: '.DOMAIN.'admin/?alert=editUser');
		}
		
		if($this->post['newPass'] != ""){
			if($this->post['newPass'] == $this->post['newPass2']){
				$user->changeMdp($this->post['newPass']);
			}else{
				$d['error'] = "Les nouveaux mot de passe le correspondent pas !";
			}
		}
		
		if($this->post['mail'] != ""){
			$user->changeMail($this->post['mail']);
		}
		
		if(!$d['error'] && ($this->post['newPass'] != "" || $this->post['mail'] != ""))
			header('Location: '.DOMAIN.'admin/?alert=editUser');
		
        $this->set($d);
        $this->render('editUser');
    }
    
    function newUser(){
		$d = array();
		
		if(!Core::isAdmin())
			header('Location: '.DOMAIN.'/');
			
		if($this->post['password'] != '' || $this->post['password2'] != '' || $this->post['mail'] != '' || $this->post['login'] != ''){
			if($this->post['password'] != '' && $this->post['password2'] != '' && $this->post['mail'] != '' && $this->post['login'] != ''){
				if($this->post['password'] == $this->post['password2']){
				
					if(mkdir(ROOT_DOWNLOADS.$this->post['login'].'/')){
						$query = $this->bdd->query("SELECT port FROM users ORDER BY port DESC LIMIT 0,1");
						$donnees = mysql_fetch_assoc($query);
		
						$sql = "INSERT INTO users VALUES ('', '".$this->post['login']."', '".$this->post['mail']."', '".md5($this->post['password'])."', '".ROOT_DOWNLOADS.$this->post['login']."/', '54709f', '', '', '0', '".($donnees['port']+1)."', '0', '1', '1');";
						$this->bdd->query($sql);
					}else{
					$d['error'] = "Problème de création du dossier de la boxe !";
					}
				}else{
					$d['error'] = "Les mots de passes ne correspondent pas !";
				}
			}else{
				$d['error'] = "Veuillez remplir tout les champs !";
			}
		
			if(!$d['error'])
				header('Location: '.DOMAIN.'admin/?alert=newUser');
		}
        $this->set($d);
        $this->render('newUser');
    }
    
    function startTrans($id){
		$d = array();
		
		if(!Core::isAdmin())
			header('Location: '.DOMAIN);
			
		$user = new User($id);
		$d['user'] = $user->userData;
		
		if(!$d['user'])
			header('Location:'.DOMAIN.' admin/');
			
		$rst = exec(TRANSMISSION.' -C -x '.ROOT_DOWNLOADS.$d['user']['login'].'/.config/trans.pid --no-incomplete-dir --no-auth --download-dir '.ROOT_DOWNLOADS.$d['user']['login'].'/ -a 127.0.0.1 --no-utp -p '.$d['user']['port'].' -g '.ROOT_DOWNLOADS.$d['user']['login'].'/.config/');

		sleep(3);

		header('Location: '.DOMAIN.'admin/');
    }
    
    function addDays($id){
		$d = array();
		
		if(!Core::isAdmin())
			header('Location: '.DOMAIN);
			
		$user = new User($id);
		$user->addDays(30, 4.00);

		header('Location: '.DOMAIN.'admin/?alert=days');
    }
    
    function stopTrans($id){
		$d = array();
		
		if(!Core::isAdmin())
			header('Location: '.DOMAIN);
			
		$user = new User($id);
		$d['user'] = $user->userData;
		
		if(!$d['user'])
			header('Location: '.DOMAIN.'admin/');
			
		$rst = file_get_contents(ROOT_DOWNLOADS.$d['user']['login'].'/.config/trans.pid');
		
		if($rst){
			exec('kill '.$rst);
		}
		
		unlink(ROOT_DOWNLOADS.$d['user']['login'].'/.config/trans.pid');
		
		header('Location: '.DOMAIN.'admin/');
    }
    
   /* function supUser($id){
	    $d = array();
		
		if(!Core::isAdmin())
			header('Location: '.DOMAIN.'/');
		
		$user = new User($id);
		$d['user'] = $user->userData;
		
		if(!$d['user'])
			header('Location: '.DOMAIN.'/admin/');
			
		if($_GET['confirm']){
			$sql = "DELETE FROM actions WHERE idBoxe = '".$id."';";
			$this->bdd->query($sql);
			
			$sql = "DELETE FROM checkDownload WHERE idUser = '".$id."';";
			$this->bdd->query($sql);
			
			$sql = "DELETE FROM torrents WHERE idBoxe = '".$id."';";
			$rst = $this->bdd->query($sql);
			while($rslt = mysql_fetch_assoc($rst)){
				$torrent = new Torrent($rslt['id']);
				$torrent->delete();
			}
			
			$sql = "DELETE FROM files WHERE idBoxe = '".$id."';";
			$this->bdd->query($sql);
			
			$sql = "DELETE FROM lastSeen WHERE idCloud = '".$id."';";
			$this->bdd->query($sql);
			
			$sql = "DELETE FROM messagerie WHERE idUser = '".$id."' OR idUserTarget = '".$id."';";
			$this->bdd->query($sql);
			
			$sql = "DELETE FROM sessions WHERE idUser = '".$id."';";
			$this->bdd->query($sql);
			
			$sql = "DELETE FROM users WHERE id = '".$id."';";
			$this->bdd->query($sql);
			
			Tools::clearDir($d['user']['boxe']);
			
			header('Location: '.DOMAIN.'/admin/?alert=supUser');
		}
		
		$this->set($d);
        $this->render('supUser');
    }*/
}
?>
