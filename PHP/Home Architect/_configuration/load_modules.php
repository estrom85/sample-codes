<?php
ModuleManager::registerModule("login", "Login", "LoginModule", "LoginModule.php");
ModuleManager::setDefaultModule("login");
ModuleManager::registerModule("overview", "Overview", "OverviewModule", "OverviewModule.php",USR_ADMIN);
ModuleManager::registerModule("home", "Home", "HomeModule", "HomeModule.php",USR_ADMIN);
ModuleManager::registerModule("zones", "Zones", "ZoneModule", "ZoneModule.php",USR_ADMIN);
ModuleManager::registerModule("rooms", "Rooms", "RoomsModule", "RoomsModule.php",USR_ADMIN);
ModuleManager::registerModule("sensors", "Sensors", "SensorModule", "SensorModule.php",USR_ADMIN);
ModuleManager::registerModule("actors", "Actors", "ActorsModule", "ActorsModule.php",USR_ADMIN);
ModuleManager::registerModule("scenarios", "Scenarios", "ScenariosModule", "ScenariosModule.php",USR_ADMIN);