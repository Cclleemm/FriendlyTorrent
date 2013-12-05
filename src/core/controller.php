<?php


class Controller extends Common{

    var $vars = array();
    var $layout = '';
	var $post ='';
	private $filename;
	private $Layout;
	private $notLayout;
	public $title;
	public $action = '';
	
	protected $user;

	////////////////////////////////////
	//    FONCTION DE CONSTRUCTION    //
	////////////////////////////////////
	
    function __construct($notLayout){
	
	//Récupération de l'envoi en POST
        if(isset($_POST)){
            $this->post = $_POST;
        }
		
	//Sert pour charger les models utilisé
        if(isset($this->models)){
            foreach($this->models as $v){
                $this->loadModel($v); 
            }
        }
		
		if($notLayout == false){
			$this->setLayout('default');
			$this->notLayout = false;
		}else{
			$this->notLayout = true;
		}
		
		if(Core::isCo()){
			$this->user = new User();
        }
		
        parent::__construct();
    }

	function virtual($adresse){

		$params = explode('/',$adresse);
		$controller = $params[0];
		$action = $params[1] != '' ? $params[1] : 'index';
	
		// Instancie le controller corespondant, ainsi que la fonction corespondant à l'action
		require(ROOT.'controllers/'.$controller.'.php');
		$controller = new $controller(true);

		if(method_exists($controller, $action)){
			unset($params[0]); unset($params[1]);
			$controller->action = $action;
			call_user_func_array(array($controller,$action),$params);
		}
		else{
			return false;
		}
	}
	
	////////////////////////////////////
	//    FONCTION DE DECLARATION     //
	//  d'envoi de valeurs au render  //
	////////////////////////////////////
	
    function set($d){
        $this->vars = array_merge($this->vars,$d);
    }
    
    ////////////////////////////////////
	//    FONCTION DE DEFINITION      //
    //            DE LAYOUT           //
	////////////////////////////////////
	
    function setLayout($layout){
	
		//Exception ajax
		if($layout != '' AND $this->notLayout == false){
			$this->layout = $layout;
			
			include(ROOT.'controllers/layout/'.$this->layout.'.php');
			
			$aff = 'Layout'.$this->layout;
			$this->Layout = '';
			$this->Layout = new $aff;
		}else{
			$this->Layout = '';
		}
    }
    
    function setTitle(){
    		
    		$d['title'] = $this->title[$this->action];
    		if(!$this->notLayout)
    			$this->Layout->set($d);
    		$this->set($d);

    }

    function setHeadMore($head){
    	if(!$this->notLayout){
    		$d['headMore'] = $head;
    		$this->Layout->set($d);
    	}
    }
	
	////////////////////////////////////
	//   FONCTION DE VU SANS LAYOUT   //
	////////////////////////////////////
	
    function viewSimple(){
		extract($this->vars);
		$lang = $this->lang;
		
        ob_start(); 
        require(ROOT.'views/'.get_class($this).'/'.$this->filename.'.php');
        return ob_get_clean();
    }

	////////////////////////////////////
	//      FONCTION D'AFFICHAGE      //
	//   affichage du render demandé  // 
	////////////////////////////////////
	
    function render($filename){
	
		$this->filename = $filename;
	
		//Si Pas de layout on met les varables en places et on affiche JUSTE le contenu !
        if($this->layout==false){
			echo $this->viewSimple();
			if($this->title[$this->action] != ''){
				echo '<script>titre("'.strip_tags(addslashes($this->title[$this->action])).'");</script>';
			}
			
        }else{ // Sinon affichage spéciale sans layout
			$this->Layout->viewLayout($this->viewSimple());
        }
    }

	////////////////////////////////////
	//    FONCTION DE LOAD MODEL      //
	////////////////////////////////////
	
    function loadModel($name){
        require_once(ROOT.'models/'.strtolower($name).'.php');
        
        // On instancie à la main !
        //$this->$name = new $name(); 
    }
}
?>