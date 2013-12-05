<?php
/*########################################################
 *
 *   Fichier execution Layout
 *
#########################################################*/


class Layoutexternal extends Common{
	
	var $vars = array();
	var $models = array();
	
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
        require(ROOT.'views/layout/external.php');
		
    }

	////////////////////////////////////
	//    FONCTION DE LOAD MODEL      //
	////////////////////////////////////
	
    function loadModel($name){
        require_once(ROOT.'models/'.strtolower($name).'.php');
    }
	
}

?>