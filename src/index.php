<?php
// Afficher les erreurs à l'écran
//error_reporting("E_ALL & ~ E_NOTICE");
//ini_set('display_errors', 1);

// Définition des variables globales
define('WEBROOT',str_replace('index.php','',$_SERVER['SCRIPT_NAME']));
define('ROOT',str_replace('index.php','',$_SERVER['SCRIPT_FILENAME']));
define('DOMAIN','http://'.$_SERVER['SERVER_NAME'].WEBROOT);

require(ROOT.'core/config/global.php');

//Coeur du site
require(ROOT.'core/core.php');
require(ROOT.'core/controller.php');
require(ROOT.'core/tools.php');
require(ROOT.'models/user.php');
require(ROOT.'models/torrents.php');
require(ROOT.'models/cloud.php');
require(ROOT.'models/stats.php');
require(ROOT.'models/bdecode.php');

// Décompose la variable envoyé à l'index
//   De type URL_SITE/NOM_MODULE/NOM_ACTION/VAR_EN_PLUS

if($_GET['p'] == ''){
	echo '/!\\ Error 404 /!\\'; 
	exit();
}

$params = explode('/',$_GET['p']);
$controller = $params[0];
$nameController = $params[0];
$action = $params[1] != '' ? $params[1] : 'index';

//--------MODULES AUTORIES-------//
$allowModule="|accueil|connect|boxe|action|downloads|torrents|compte|messagerie|rss|admin|stats|";

if(!strstr($allowModule, "|".$controller."|")){
	echo '/!\\ Error 404 /!\\'; 
	exit();
}

//Gestion de l'affichage des Layout (Là pas d'affichage si en Ajax :: ajax=true)
if($_GET['ajax'] == true) $notLayout = true;
else $notLayout = false;

// Exceptions
if($controller != 'connect' && ($controller != 'downloads') && ($controller != 'action' && $action != 'refreshTorrent') && ($controller != 'action' && $action != 'deleteOneDayForAllUser')){
    if(!Core::isCo()) header('Location: '.DOMAIN.'connect/');
}

if($controller == 'accueil'){
    header('Location: '.DOMAIN.'boxe/');
}


// Instancie le controller corespondant, ainsi que la fonction corespondant à l'action
require('controllers/'.$controller.'.php');
$controller = new $controller($notLayout);

if(method_exists($controller, $action)){
    unset($params[0]); unset($params[1]);
    $controller->action = $action;
    $controller->setTitle();
    call_user_func_array(array($controller,$action),$params);
    //$controller->$action();
	
	$paramsString = '';
	foreach($params as $key=>$value){
		$paramsString .= '/'.$value;
	}
	//$core->bdd->sendReport($nameController.'/'.$action.$paramsString);
}
else{
    echo '/!\\ Error 404 /!\\'; 
}
?>