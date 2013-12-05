<?php


class Payment extends Common{
	////////////////////////////////////
	// DECLARATION DES DONNEES MEMBRES//
	////////////////////////////////////
	
	private $isActive = false;
	
	////////////////////////////////
	// DECLARATION DU CONSTRUCTEUR//
	////////////////////////////////
	//Construction par ID ou par nom du membre : 
	//new User(1) ou new User("clement")
	
	function getActive(){
		return $this->isActive;
	}
	
	function __construct($user = null){
		parent::__construct();
		
		if($user){
			$user = new User($user);
			
			if($user->userData['nbrJours'] > 0){
				$this->isActive = true;
			}
		}
	}
}

?>
