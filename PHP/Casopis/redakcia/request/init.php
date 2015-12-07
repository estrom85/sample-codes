<?php
/*
 * vytvori vsetky triedy dolezite pre beh aplikacie
 */
session_start();
error_reporting(E_ALL);
ini_set("display_errors", 1); 
if(!isset($_SESSION['user'])){
    header("Location: login.php");
    exit;
}

$homedir=dirname(__FILE__)."/..";
//Trieda ProgramManager - obsahuje dolezite informacie o jednotlivych moduloch
require_once $homedir."/classes/utils/ProgramManager.php";
//Trieda Database - ma na starosti komunikaciu s databazou
require_once $homedir."/classes/utils/CDatabaza.php";
//Rozhranie module predstavuje zakladny komunikacny protokol medzi aplikaciou a modulmi
require_once $homedir."/classes/modules/Module.php";

require_once $homedir."/classes/utils/UserRights.php";

require_once $homedir."/classes/utils/DBQuery.php";

require_once $homedir."/classes/utils/FormMaker.php";

require_once $homedir."/classes/utils/Nonce.php";

/*
 * Inicializacie programovych modulov
 */

//nastavenie domovskeho adresara pre vsetky moduly
ProgramManager::setHomeDir($homedir);
//modul prihlasovania
ProgramManager::addProgram(1158,"Login","classes/modules/user/Login.php"); 
//uvodna stranka programu
ProgramManager::addProgram(4527,"Intro","classes/modules/Intro.php");

ProgramManager::addProgram(9724, "Topics", "classes/modules/core/Topics.php");

ProgramManager::addProgram(3274, "Users", "classes/modules/user/Users.php");

ProgramManager::addProgram(6598,"User_info","classes/modules/user/User_info.php");

ProgramManager::addProgram(5274, "Article_list", "classes/modules/core/Article_list.php");
//ProgramManager::addProgram(5874, "Themes", "classes/modules/core/Themes.php");
ProgramManager::addProgram(9999, "Article", "classes/modules/core/Article/Article.php");

ProgramManager::addProgram(4552, "Article_story", "classes/modules/core/Article/Article_story.php");

ProgramManager::addProgram(8374, "Article_quiz", "classes/modules/core/Article/Article_quiz.php");

ProgramManager::addProgram(2876, "Article_interview", "classes/modules/core/Article/Article_interview.php");

ProgramManager::addProgram(3972, "Article_post", "classes/modules/core/Article/Article_post.php");

ProgramManager::addProgram(5489, "Article_galery", "classes/modules/core/Article/Article_galery.php");

function setClassName(){
    $className="Intro";
if(isset($_GET['id']))
{
    $className=  ProgramManager::getProgramName($_GET['id']);
}

if(!$className)
    $className="Intro";
ProgramManager::includeProgram($className);
return $className;
}

?>
