<?php
//starts the session on server, important for all modules
session_start();

/***********************************************************************/
/************************ Dependencies *********************************/
/***********************************************************************/

require_once HOME.'/_classes/Utilities/Location.php';
require_once HOME.'/_classes/Utilities/UserRights.php';
require_once HOME.'/_classes/Data/Database.php';
require_once HOME.'/_classes/View.php';
require_once HOME.'/_classes/Modules/Module.php';
require_once HOME.'/_classes/ModuleManager.php';
require_once HOME.'/_classes/Dispatcher.php';
require_once HOME.'/_classes/Application.php';

/***********************************************************************/
/*********************** Initial settings ******************************/
/***********************************************************************/


require_once HOME.'/_configuration/configuration.php';
require_once HOME.'/_configuration/load_modules.php';





if(isset($_SESSION['user_agent'])){
	if($_SESSION['user_agent']!=md5($_SERVER['HTTP_USER_AGENT'])){
		session_destroy();
	}
}
else{
	$_SESSION['user_agent']=md5($_SERVER['HTTP_USER_AGENT']);
}

/************************************************************************/
/*************************** Initialization *****************************/
/************************************************************************/
