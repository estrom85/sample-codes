<?php 
define('HOME', dirname(__FILE__));
$HOME = dirname(__FILE__);

require_once '_configuration/init.php';

$app = new Application("Home Architect", $HOME, "main_template");
$app->run();
//echo "test";
?>