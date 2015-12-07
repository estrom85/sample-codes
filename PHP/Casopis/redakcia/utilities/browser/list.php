<?php
//session_start();
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
    echo "Nemáte oprávnenie na prezeranie obsahu. Prístup zamietnutý.";  
    $data->close();
    exit;
}
$data->close();
require_once dirname(__FILE__).'/classes/ImageList.php';
$images=new ImageList(dirname(__FILE__)."/../../..",$_GET['article_id']);
$images->display();
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
