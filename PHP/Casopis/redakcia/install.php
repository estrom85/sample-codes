<!--
To change this template, choose Tools | Templates
and open the template in the editor.
-->
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
    </head>
    <body>
        <?php
        error_reporting(E_ALL);
        ini_set("display_errors", 1);   
        include dirname(__FILE__).'/classes/utils/CDatabaza.php';
        CDatabaza::createDB("casopis", "root", "zelgadis", "localhost");
        ?>
    </body>
</html>
