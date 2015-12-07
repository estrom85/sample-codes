<?php
session_start();
error_reporting(E_ALL);
ini_set("display_errors", 1); 
if(!isset($_COOKIE['user'])){        
    echo "Nie je prihlásený žiaden užívateľ.";        
    exit;
}
if(!isset($_GET['article_id'])){
    echo "Nie je zadaný žiaden článok";            
    exit;
}
require dirname(__FILE__)."/../../classes/utils/CDatabaza.php";
require dirname(__FILE__)."/../../classes/utils/UserRights.php";
        
$data=CDatabaza::getInstance();
if(!$data)
        {
            exit ("Nemozem sa pripojit na databazu");
        }
$data->connect();
$user=new UserRights($data,$_COOKIE['user']);
$clanok=$data->escape_string($_GET['article_id']);
$sql="SELECT * FROM Clanok_uzivatel WHERE clanok_id=$clanok AND uzivatel_id=".$_COOKIE['user'];
$query=$data->query($sql);
if(!$query){           
    echo "Chyba v pripojení na databázu";  
    $data->close();
    exit;
}
if(!$query->num_rows&&!$user->approved('EDIT_ALL')){        
    echo "Nemáte oprávnenie na upload obrazkov.";  
    $data->close();
    exit;
}
$file=$_GET['filename'];   

$path=dirname(__FILE__)."/../../../$file";
if(is_file($path)){
    if(unlink($path))
        echo "Súbor vymazaný";

    else
        echo "Nepodarilo sa vymazať súbor: $path";
    }
else
    echo "Subor neexistuje";
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
