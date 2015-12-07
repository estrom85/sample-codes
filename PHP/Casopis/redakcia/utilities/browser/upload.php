<?php
session_start();
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
$data->close();
echo "Upload povoleny<br>";

if(empty($_FILES))
    echo "Neexistuje subor";
//print_r($_FILES);
$allowedExts = array("jpg", "jpeg", "gif", "png", "JPG", "JPEG", "GIF", "PNG");
$extension = end(explode(".", $_FILES["file"]["name"]));
if ((($_FILES["file"]["type"] == "image/gif")
|| ($_FILES["file"]["type"] == "image/jpeg")
|| ($_FILES["file"]["type"] == "image/pjpeg"))
&& ($_FILES["file"]["size"] < 100000)
&& in_array($extension, $allowedExts))
  {
  if ($_FILES["file"]["error"] > 0)
    {
    echo "Return Code: " . $_FILES["file"]["error"] . "<br />";
    }
  else
    {
    echo "Upload: " . $_FILES["file"]["name"] . "<br />";
    echo "Type: " . $_FILES["file"]["type"] . "<br />";
    echo "Size: " . ($_FILES["file"]["size"] / 1024) . " Kb<br />";
    echo "Temp file: " . $_FILES["file"]["tmp_name"] . "<br />";
    $path=dirname(__FILE__)."/../../../articles/".$_GET['article_id']."/pics";
    if (file_exists($path."/" . $_FILES["file"]["name"]))
      {
      echo $_FILES["file"]["name"] . " už existuje. ";
      }
    else
      {
      move_uploaded_file($_FILES["file"]["tmp_name"],
      $path."/" . $_FILES["file"]["name"]);
      echo "Ulozene na: " . $path."/". $_FILES["file"]["name"];
      }
    }
  }
else
  {
  echo "Nespravny subor";
  }
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
