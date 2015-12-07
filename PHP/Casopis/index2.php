<?php
//error_reporting(E_ALL);
//ini_set("display_errors", 1); 

$homedir=  dirname(__FILE__)."/classes/";

require_once $homedir."display.php";
require_once $homedir."DisplayModule.php";
require_once $homedir."Menu.php";
require_once $homedir."News.php";

require_once dirname(__FILE__)."/redakcia/classes/utils/CDatabaza.php";

$label="Domov";
//pripojenie na databazu

$db= CDatabaza::getInstance();
if(empty($db))
    die("Nemozem sa pripojit na server");

$db->connect();

$menu=new Menu($db);
$news=new News($db);

$program=null;
$style=false;
if(isset($_GET['rubrika'])){
    require_once $homedir."modules/Topic.php";
    $program=new Topic($db);
    $program->setHome(dirname(__FILE__));
    $label=$program->label();
    $style="topics.css";
}

else if(isset($_GET['clanok'])){
    require_once $homedir."modules/Article_manager.php";
    $program=new Article_manager($db,  dirname(__FILE__));
    $label=$program->label();
    $style="articles.css";
}
else if(isset($_GET['rozne'])){
    if(!strcmp($_GET['rozne'],"herna")){
        require_once $homedir."modules/GameCenter.php";
        $program=new GameCenter();
        $label=$program->label();
        $style="games.css";
    }
    
}
/*
$label="";

$type="";
$module=null;

if(isset($_GET['typ'])) $type=$db->escape_string($_GET['typ']);

switch($type){
    case "tema":
        require_once $homedir."modules/Theme.php";
        $module=new Theme($db);
        break;
    case "rubrika":
        require_once $homedir."modules/Topic.php";
        $module=new Rubrika($db);
        break;
    case "clanok":
        require_once $homedir."modules/Article.php";
        $module=new Article($db);
        break;
    case "kniha_navstev":
        require_once $homedir."modules/GuestBook.php";
        $module=new GuestBook();
        break;
    case "herna":
        require_once $homedir."modules/GameCenter.php";
        $module=new GameCenter();
        break;
    case "o_nas":
        require_once $homedir."modules/AboutUs.php";
        $module=new AboutUs();
        break;
    default :
        require_once $homedir."modules/News.php";
        $module=new News($db);
        break;   
}

$label=$module->label();
*/
$db->close();


?>
<html>
  <head>
    <title>Sedemnástka - internetový časopis ZŠ 17. novembra v Sabinove</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" >
        <link rel="stylesheet" type="text/css" href="styles/casopis.css">
        <link rel="stylesheet" type="text/css" href="styles/news.css">
        <?php if($style){?>
        <link rel="stylesheet" type="text/css" href="styles/<?php echo $style;?>">
        <?php }?>
        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
        <script type="text/javascript" src="scripts/casopis.js"></script>
        <script type="text/javascript" src="scripts/quiz.js"></script>
        <script type="text/javascript" src="scripts/widgets.js"></script>
        <link rel="stylesheet" type="text/css" href="styles/widgets.css">
        <script type="text/javascript">
            <!--
            var mouseOnSubmenu=false;
            $(document).ready(function(){
                $(".sub_menu").hide();
                links_widget.add("ZŠ 17.nov, Sabinov","www.zsnovsab.edu.sk");
                links_widget.add("Školský časopis - Sedemnástka","localhost/casopis");
                $("#widgets")
                    .addWidget(dnesny_datum())
                    .addWidget(aktualny_cas())
                    .addWidget(meniny.getWidget())
                    .addWidget(kalendar.getWidget())
                    .addWidget(links_widget.display());
                
            });
            -->
        </script>
  </head>
  <body>
      <div id="main" class="main">
          <div id="header" class="header">
              <span id="label" class="label"><?php echo $label; ?></span>
              <div id="title">
              <span id="name" class="name">Sedemnástka</span><br>
              <span id="school" class="school">Internetový časopis ZŠ 17. novembra v Sabinove </span>
              </div>
          </div>
          <?php $menu->display();?>
          
          <div id="window_wrapper">
              <table width="100%" cellspacing="0" cellpadding="0">
                  <tr>
                      <td width="20%" style="vertical-align: top;">
                          <div class="news_label">Novinky:</div>
                              <?php echo $news->display();?></td>
                      <td width="60%" style="vertical-align: top; padding-left: 20px; padding-right: 20px;">
                          <?php if(!empty($program)) $program->display(); ?>
                      </td>
                      <td width="20%" style="vertical-align: top;"><div id='widgets'></div></td>
                  </tr>
              </table>
              
          </div>
          <div id="footer">
              <strong>Dnes je:</strong>
              <i>
              <?php echo date("d.m.Y", time());?>
              </i>
              <br/>
              <b>Design by:</b> <i>Ing. Martin Mačaj</i>; <b>PHP, MySQL Programming:</b> <i>Ing. Martin Mačaj</i>.
          </div>
      </div>
      

<?php


?>

  </body>
</html>
