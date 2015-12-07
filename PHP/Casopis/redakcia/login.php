<!--
To change this template, choose Tools | Templates
and open the template in the editor.
-->
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <link rel="stylesheet" type="text/css" href="styles/login.css">
        <title></title>
    </head>
    <body>
        <?php
        /*
         * Nastavenie zobrazovania chybových hlásení - pre účely ladenia
         */
            error_reporting(E_ALL);
            ini_set("display_errors", 1); 
            //štart sekcie - nastavi registrovanie premenných sekcie
            session_start();
            
            /*
             * Načíta moduly potrebné pre beh prihlasovacieho programu
             */
            require_once dirname(__FILE__).'/classes/modules/Module.php';       //abstraktna trieda zodpovedna za beh programu
            require_once dirname(__FILE__).'/classes/utils/ProgramManager.php'; //prepínač programov
            require_once dirname(__FILE__).'/classes/utils/CDatabaza.php';      //trieda zodpovedná za pripojenie k databáze
            require_once dirname(__FILE__).'/classes/modules/user/Login.php';   //trieda zodpovedná za prihlasovanie sa
            
            $login=new Login(); //vytvorí prihlasovací modul
         
            $login->execute(); //spustí zvolené funkcie prihlasovacieho modulu
            //$login->displayMsg();
            $login->display(); //zobrazí výsledok
        ?>
    </body>
</html>
