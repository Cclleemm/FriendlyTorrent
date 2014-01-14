<?php
/*########################################################
 *
 *   Fichier execution Layout
 *
#########################################################*/


class Layoutdefault extends Common{
	
	var $vars = array();
	var $models = array('user');
	
	////////////////////////////////////
	//    FONCTION DE CONSTRUCTION    //
	////////////////////////////////////
	
    function __construct(){
		
		//Sert pour charger les models utilisé
        if(isset($this->models)){
            foreach($this->models as $v){
                $this->loadModel($v); 
            }
        }
        
        parent::__construct();
    }
	
	
	////////////////////////////////////
	// LISTE DES VARS ENVOYE AU LAYOUT//
	//         DES VAR ENVOYE         //
	//          AU LAYOUT             //
	////////////////////////////////////
	
	function vars(){
		
		$tab = array();
		
		$user = new User();
		
		$tab['user'] = $user->userData;
		
		$sql = "SELECT users.* FROM users WHERE login != '".$tab['user']['login']."';";
		$rst = $this->bdd->query($sql);
		
		$tab['other'] = $rst;
		
		$freespace = disk_free_space(ROOT_DOWNLOADS);
		$tab['freespace'] = $freespace;
		
		$this->set($tab);
	}
	
	
	////////////////////////////////////
	//    FONCTION DE DECLARATION     //
	//         VAR DU LAYOUT          //
	////////////////////////////////////
	
    function set($d){
        $this->vars = array_merge($this->vars,$d);
    }
	
	
    
    ////////////////////////////////////
	//    FONCTION DE Vu LAYOUT       //
	////////////////////////////////////
	
    function viewLayout($content){
		
		$this->vars();
		
		//Extraction pour le layout
		extract($this->vars);
		
		$content_for_layout = $content;
		
		//Affichage du layout
        require(ROOT.'views/layout/default.php');
		
    }

	////////////////////////////////////
	//    FONCTION DE LOAD MODEL      //
	////////////////////////////////////
	
    function loadModel($name){
        require_once(ROOT.'models/'.strtolower($name).'.php');
    }
	
}

?>