<?php
define("HOME",dirname(__FILE__));
include HOME.'/_configuration/init.php';
include HOME.'/_classes/Data/DbHandler.php';

$db = new DbHandler();
$db->delete();
$db->create();