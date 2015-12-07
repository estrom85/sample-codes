<?php
require_once HOME.'/_classes/Data/DataContract/DbContract.php';
class DbHandler extends Database{
	
	
	public function create(){
		$db = $this->connect();
		if(!$this->isConnected()){
			echo "Could not connect to database. " . $db->connect_error . "<br>";
			return;
		}
		
		$success_str_format = "Table '%s' created.<br>";
		$failed_str_format = "Creation of table '%s' failed. %s <br>";
				
		$sql = "CREATE TABLE " . UserContract::TABLE
				. "("
				. UserContract::_ID . " INT NOT NULL PRIMARY KEY AUTO_INCREMENT,"
				. UserContract::COL_NAME . " VARCHAR(50) NOT NULL,"
				. UserContract::COL_PASSWORD . " VARCHAR(50) NOT NULL,"
				. UserContract::COL_USER_TYPE . " INT NOT NULL"
				. ")";
		$success = $db->query($sql);
		if($success){
			printf($success_str_format,UserContract::TABLE);
			$sql = "INSERT INTO " . UserContract::TABLE . "(" 
					. UserContract::COL_NAME . "," 
					. UserContract::COL_PASSWORD . ","
					. UserContract::COL_USER_TYPE . ") VALUES ("
					. "'admin','".sha1("admin")."',".USR_ADMIN.")";
			if($db->query($sql)){
				echo "Admin account created <br>";
			}else{
				echo "Admin account creation failed. ".$db->error." <br>";
			}
		}else{
			printf($failed_str_format,UserContract::TABLE,$db->error);
		}
		
		$sql = "CREATE TABLE " . HomeContract::TABLE
				. "("
				. HomeContract::_ID . " INT NOT NULL PRIMARY KEY AUTO_INCREMENT,"
				. HomeContract::COL_USER_ID . " INT NOT NULL,"
				. HomeContract::COL_NAME . " VARCHAR(50) NOT NULL"
				. ")";
		$success = $db->query($sql);
		if($success){
			printf($success_str_format, HomeContract::TABLE);
		}else{
			printf($failed_str_format, HomeContract::TABLE,$db->error);
		}
		
		$sql = "CREATE TABLE " . ZoneContract::TABLE
				. "("
				. ZoneContract::_ID . " INT NOT NULL PRIMARY KEY AUTO_INCREMENT,"
				. ZoneContract::COL_HOME_ID . " INT NOT NULL,"
				. ZoneContract::COL_NAME . " VARCHAR(50) NOT NULL"
				. ")";
		$success = $db->query($sql);
		if($success){
			printf($success_str_format, ZoneContract::TABLE);
		}else{
			printf($failed_str_format, ZoneContract::TABLE,$db->error);
		}
		
		$sql = "CREATE TABLE " . RoomContract::TABLE
				. "("
				. RoomContract::_ID . " INT NOT NULL PRIMARY KEY AUTO_INCREMENT,"
				. RoomContract::COL_HOME_ID . " INT NOT NULL,"
				. RoomContract::COL_ZONE_ID . " INT,"
				. RoomContract::COL_NAME . " VARCHAR(50) NOT NULL"
				. ")";
		$success = $db->query($sql);
		if($success){
			printf($success_str_format, ZoneContract::TABLE);
		}else{
			printf($failed_str_format, ZoneContract::TABLE,$db->error);
		}
		
		$this->close();
	}
	
	public function delete(){
		$db = $this->connect();
		if(!$this->isConnected()){
			echo "Could not connect to database. " . $db->connect_error . "\n";
			return;
		}
		
		
		$this->dropTable(UserContract::TABLE);
		$this->dropTable(HomeContract::TABLE);
		$this->dropTable(ZoneContract::TABLE);
		$this->dropTable(RoomContract::TABLE);
		$this->close();
	}
	
	
}