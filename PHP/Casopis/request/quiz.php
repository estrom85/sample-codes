<?php
error_reporting(E_ALL);
ini_set("display_errors", 1); 

require_once dirname(__FILE__)."/../classes/display.php";
require_once dirname(__FILE__)."/../classes/modules/Art_quiz.php";
require_once dirname(__FILE__)."/../redakcia/classes/utils/CDatabaza.php";
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
if(empty($_GET['clanok']))
    exit("Chyba0");
$db= CDatabaza::getInstance();
if(empty($db))
    die("Chyba1:Nemozem sa pripojit na server");

$db->connect();
$clanok=$db->escape_string($_GET['clanok']);
$sql="SELECT * FROM Clanok WHERE zobrazit=1 AND clanok_id=".$clanok;
$query=$db->query($sql);
$db->close();
if(!$query)
    die("Chyba1");
if($query->num_rows==0)
    die("Chyba2");

$quiz=new Art_quiz($clanok,dirname(__FILE__)."/..");

$quiz->printAnswers();

?>
