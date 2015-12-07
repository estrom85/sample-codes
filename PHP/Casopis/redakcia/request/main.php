<?php

require_once 'init.php';
//echo "pokus";
$className=  setClassName();
$program=new $className();
$program->display();

?>
