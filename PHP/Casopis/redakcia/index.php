<?php
//inicializácia aplikácie
require_once "request/init.php";
/*
 * Základné moduly potrebné pre správny chod aplikácie
 */
include_once dirname(__FILE__)."/classes/utils/Menu.php"; //načíta zdrojový kód menu
include_once dirname(__FILE__)."/classes/utils/Header.php"; //načíta modul hlavičky
include_once dirname(__FILE__)."/classes/utils/Footer.php"; //načíta modul päty

$menu=new Menu();       //vytvorí hlavné menu programu
$header=new Header();   //vytvorí hlavičku programu
$footer= new Footer();  //vytvorí pätu programu
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <base href="../">
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Redakcia internetového časopisu Sedemnástka</title>
        <link rel="stylesheet" type="text/css" href="redakcia/styles/redakcia.css">
        <link rel="stylesheet" type="text/css" href="styles/widgets.css">
        <link rel="stylesheet" type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.10/themes/humanity/jquery-ui.css">
        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.10/jquery-ui.min.js"></script>
        <script type="text/javascript" src="redakcia/scripts/redakcia.js"></script>
        <script type="text/javascript" src="redakcia/scripts/validateForm.js"></script>  
        <script type="text/javascript" src="scripts/widgets.js"></script>  
        <script type="text/javascript">
            /*
             * Inicializácia skriptov a hlavného programu
             */
            $(document).ready(function(){
                incializujDialogoveOkno();
                scriptloader.clear();
                nastavProgram(0);
                links_widget.add("ZŠ 17.nov, Sabinov","www.zsnovsab.edu.sk");
                links_widget.add("Školský časopis - Sedemnástka","localhost/casopis");
                $("#widgets")
                    .addWidget(dnesny_datum())
                    .addWidget(aktualny_cas())
                    .addWidget(links_widget.display());
            });
            
        </script>
    </head>
    <body>
        <div id="main" class="main">
          <div id="header" class="header">
              <?php $header->display(); //zobrazí hlavičku?>
          </div>
            <?php 
            $menu->display();   //zobrazí menu                   
            ?>
          <div id="window" class="window">
              <table id="window_layout" class="window_layout" cellspacing="0" cellpadding="0">
                  <tbody>
                      
                      <tr>
                          <td width="15%" id="toolbox"> 
                              <!-- Tu sa zobrazi panel nastrojov -->
                          </td>
                          <td id="main_window">
                              <!-- Tu sa zobrazi pracovna plocha modulu -->
                          </td>
                          <td id="widgets" width="15%">
                              <!-- Tu sa zobrazia zasuvne moduly -->
                              
                          </td>
                      </tr>
                  </tbody>
              </table>
          </div>
          <div id="footer" class="footer">
              <?php $footer->display(); //zobrazí patu programu?>
          </div>
        </div>
        <div id="dialog"></div>
    </body>
</html>
