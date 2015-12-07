<?php
require_once HOME . '/_classes/Data/DbHandler.php';
class DataProvider{
	private $db;
	
	public function __construct(){
		$this->db = (new DbHandler())->connect();
	}
	
	public function __destruct(){
		$this->db->close();
	}
	
	/************************** Users *************************************/
	
	public function verify_user($user, $password ,&$array, &$error){
		$error = false;
		
		if($this->db->connect_error){
			$error = "Could not connect database. ";
			return false;
		}
		
		$prepared_statement = "SELECT " . UserContract::_ID . "," . UserContract::COL_NAME . "," . UserContract::COL_USER_TYPE 
				." FROM " . UserContract::TABLE . " WHERE " 
				. UserContract::COL_NAME . "=? AND " . UserContract::COL_PASSWORD . "=?";
		
		$stmt = $this->db->prepare($prepared_statement);
		if(!$stmt){
			$error = "Error preparing statement. " . $this->db->error;
			return false;
		}
		
		if(!$stmt->bind_param('ss', $this->db->escape_string($user), sha1($password))){
			$error = "Error binding parameter" . $this->db->error;
			return false;
		}
		
		if(!$stmt->execute()){
			$error = "Error statement execution. " . $this->db->error;
			return false;
		}
		
		$stmt->store_result();
		
		if($stmt->num_rows() != 1){
			$error = "Incorrect login or password.";
			return false;
		}
		
		$usr_id = NULL;
		$usr_name = NULL;
		$usr_type = NULL;
		
		if(!$stmt->bind_result($usr_id, $usr_name, $usr_type)){
			$error = "Error binding results. " . $this->db->error;
			return false;
		}
		
		$stmt->fetch();
		
		if($usr_id == NULL || $usr_name == NULL || $usr_type == NULL){
			$error = "Error fetching data from db.";
			return false;
		}
		
		$array = array("id" => $usr_id, "name" => $usr_name, "type" => $usr_type);
		return true;
	}
	
	/*************************** Homes ***********************************************/
	public function listHomes(){
		$user_id = $_SESSION['user']['id'];
		
		$sql = "SELECT * FROM " . HomeContract::TABLE . " WHERE " . HomeContract::COL_USER_ID . " =". $user_id;
		$output = null;
		$res = $this->db->query($sql);
		if ($res == null) return null;
		
		while($data = $res->fetch_assoc()){
			$id = $data[HomeContract::_ID];
			$output[$id] = array(
				'id' => $id,
				'name' => $data[HomeContract::COL_NAME]
			);
		}
		
		return $output;
	}
	
	public function addHome($name, &$error){
		return $this->addData(
				HomeContract::TABLE, 
				array(HomeContract::COL_USER_ID, HomeContract::COL_NAME), 
				array($this->getUserId(),$name), 
				"is", 
				$error);
	}
	
	public function editHome($id, $name, &$error){
		return $this->editData(
				HomeContract::TABLE, 
				array(HomeContract::COL_NAME), 
				array($name), 
				's', 
				HomeContract::_ID . "=" . $this->db->escape_string($id) . " AND " 
					.HomeContract::COL_USER_ID . "=" . $this->getUserId(), 
				$error);
	}
	
	const REMOVE_OK = 0;
	const REMOVE_UNRESOLVED_DEPENDENCES = 1;
	const REMOVE_ERROR = 2;
	
	
	public function removeHome($id, &$error, $force = false){
		if($this->db->connect_error){
			$error = "Could not connect database. ";
			return self::REMOVE_ERROR;
		}
		
		if(empty($_SESSION['user'])){
			$error =  "User is not logged in";
			return self::REMOVE_ERROR;
		}
		
		$user_id = $this->getUserId();
		
		//check references
		
		$num_zones = 0;
		$num_rooms = 0;
		
		$sql = "SELECT " . ZoneContract::_ID . 
					" FROM " . ZoneContract::TABLE . 
					" WHERE " . ZoneContract::COL_HOME_ID . "=" . $id;
		
		$res = $this->db->query($sql);
		
		if(!$res){
			$error = $this->db->error;
			return self::REMOVE_ERROR;
		}
		
		$num_zones = $res->num_rows;
		
		$sql = "SELECT " . RoomContract::_ID .
					" FROM " . RoomContract::TABLE .
					" WHERE " . RoomContract::COL_HOME_ID . "=" .$id;
		
		if(!$res){
			$error = $this->db->error;
			return self::REMOVE_ERROR;
		}
		
		$num_rooms = $res->num_rows;
		
		if($num_rooms > 0 || $num_zones > 0){
			if(!$force){
				$error = "Unresolved dependences";
				return self::REMOVE_UNRESOLVED_DEPENDENCES;
			}
			
			if($num_rooms > 0){
				$sql = "DELETE FROM " . RoomContract::TABLE . " WHERE " . RoomContract::COL_HOME_ID . "=" . $id;
				if(!$this->db->query($sql)){
					$error = "Could not remove rooms";
					return self::REMOVE_ERROR;
				}
			}
			
			if($num_zones > 0){
				$sql = "DELETE FROM " . ZoneContract::TABLE . " WHERE " . ZoneContract::COL_HOME_ID . "=" . $id;
				if(!$this->db->query($sql)){
					$error = "Could not remove rooms";
					return self::REMOVE_ERROR;
				}
			}
		}
		
		$sql = "DELETE FROM " . HomeContract::TABLE . " WHERE " 
					. HomeContract::_ID . "=" . $id . " AND "
					. HomeContract::COL_USER_ID . "=" . $user_id;
		
		if(!$this->db->query($sql)){
			$error = "Home was not removed";
			return self::REMOVE_ERROR;
		}
		return self::REMOVE_OK;
	}
	
	public function getNumberOfHomes(){
		if($this->db->connect_error){
			return 0;
		}
		
		if(empty($_SESSION['user'])){
			return 0;
		}
		
		$user_id = $_SESSION['user']['id'];
		
		$sql = "SELECT Count(*) FROM " . HomeContract::TABLE . " WHERE " . HomeContract::COL_USER_ID . "=" . $user_id;
	
		$res = $this->db->query($sql);
		
		if(!$res) return 0;
		
		$count = $res->fetch_array();
		
		return $count[0];
	}
	
	/******************************** Zones *****************************************/
	
	public function listZones($home_id){
		$user_id = $this->getUserId();
		
		$sql = "SELECT * FROM " . ZoneContract::TABLE . " WHERE " . ZoneContract::COL_HOME_ID . "=" . $home_id;
		
		$res = $this->db->query($sql);
		
		if(!$res){
			return null;
		}
		
		$output = null;
		
		while($data = $res->fetch_assoc()){
			$id = $data[ZoneContract::_ID];
			$output[$id] = array(
				"id" => $id,
				"name" =>$data[ZoneContract::COL_NAME]
			);
		}
		
		return $output;
	}
	
	public function addZone($home_id, $name, $error){
		return $this->addData(
				ZoneContract::TABLE, 
				array(ZoneContract::COL_HOME_ID , ZoneContract::COL_NAME),
				array($home_id, $name), 
				"is", 
				$error);
	}
	
	public function editZone($id, $name, $error){
		return $this->editData(
				ZoneContract::TABLE, 
				array(ZoneContract::COL_NAME), 
				array($name), 
				"s", 
				ZoneContract::_ID . "=" . $id, 
				$error);
	}
	
	public function removeZone($id, $name, $error){
		if($this->db->connect_error){
			return "Could not connect database. ";
		}
		
		if(empty($_SESSION['user'])){
			return "User is not logged in";
		}
		
		$sql = "UPDATE " . RoomContract::TABLE . " SET " . RoomContract::COL_ZONE_ID . "=NULL WHERE "
				. RoomContract::COL_ZONE_ID . "=" .$id;
		
		$this->db->query($sql);
		
		
		$user_id = $_SESSION['user']['id'];
		
		$sql = "DELETE FROM " . ZoneContract::TABLE . " WHERE "
				. ZoneContract::_ID . "=" . $id;
		
		if($this->db->query($sql)){
			return "Home was not removed";
		}
		return null;
	}
	
	public function getNumberOfZones(){
		if($this->db->connect_error){
			return 0;
		}
		
		if(empty($_SESSION['user'])){
			return 0;
		}
		
		$user_id = $_SESSION['user']['id'];
		
		$sql = "SELECT Count(*) FROM " . ZoneContract::TABLE . " z " .
					" INNER JOIN " . HomeContract::TABLE . " h " .
					" ON h." . HomeContract::_ID . "=z." . ZoneContract::COL_HOME_ID .
					" WHERE h." . HomeContract::COL_USER_ID . "=" . $user_id;
		
		//echo $sql;
		$res = $this->db->query($sql);
		
		if(!$res) return 0;
		
		$count = $res->fetch_array();
		
		return $count[0];
	}
	
	/*********************************** Rooms **************************************/
	
	public function listRoomsByHomeId($home_id){
		$user_id = $this->getUserId();
		
		$sql = "SELECT "
				. RoomContract::COL_NAME . " room, "
				. RoomContract::_ID . " id "
				. "FROM " . RoomContract::TABLE 
				. " WHERE " . RoomContract::COL_HOME_ID . "=" . $home_id ;
		
		//echo ($sql);
		$res = $this->db->query($sql);
		
		if(!$res){
			return null;
		}
		
		$output = null;
		
		while($data = $res->fetch_assoc()){
			$id = $data["id"];
			$output[$id] = array(
					"id" => $id,
					"name" =>$data["room"]
			);
		}
		
		return $output;
	}
	
	public function listRoomsByZoneId($zone_id){
		$user_id = $this->getUserId();
		
		$sql = "SELECT "
				. " r." . RoomContract::COL_NAME . " room, "
				. " r." . RoomContract::_ID . " id "
				. " FROM " . RoomContract::TABLE . " r "
				. " INNER JOIN " . RoomZoneContract::TABLE . " rz "
					. " ON rz." . RoomZoneContract::FK_ROOM_ID . "= r." . RoomContract::_ID
				. " INNER JOIN " . ZoneContract::TABLE . " z "
					. " ON z." . ZoneContract::_ID . "= rz." . RoomZoneContract::FK_ZONE_ID
				. " WHERE z." . ZoneContract::_ID . "=" . $zone_id ;
		
		//echo ($sql);
		$res = $this->db->query($sql);
		
		if(!$res){
			return null;
		}
		
		$output = null;
		
		while($data = $res->fetch_assoc()){
			$id = $data["id"];
			$output[$id] = array(
					"id" => $id,
					"name" =>$data["room"]
			);
		}
		
		return $output;
	}
	
	private function listRooms($column, $value){
		$user_id = $this->getUserId();
		
		$sql = "SELECT "
					. "r." . RoomContract::COL_NAME . " room, "
					. "r." . RoomContract::_ID . " id, "
					. "z." . ZoneContract::COL_NAME . " zone"
					. " FROM " . RoomContract::TABLE . " r "
					. " LEFT JOIN " . ZoneContract::TABLE . " z ON "
					. "r." .RoomContract::COL_ZONE_ID . " = z." . ZoneContract::_ID
					. " WHERE r." . $column . "=" . $value ;
		
		//echo ($sql);
		$res = $this->db->query($sql);
		
		if(!$res){
			return null;
		}
		
		$output = null;
		
		while($data = $res->fetch_assoc()){
			$id = $data["id"];
			$output[$id] = array(
					"id" => $id,
					"name" =>$data["room"],
					"zone" =>$data["zone"]
			);
		}
		
		return $output;
	}
	
	public function addRoom($home_id, $zone_id, $name, $error){
		$keys = array(RoomContract::COL_HOME_ID,RoomContract::COL_NAME);
		$values = array($home_id,$name);
		$value_types = "is";
		if($zone_id != null){
			$keys[] = RoomContract::COL_ZONE_ID;
			$values[] = $zone_id;
			$value_types .= 'i';
		}
		return $this->addData(
				RoomContract::TABLE, 
				$keys, 
				$values, 
				$value_types, 
				$error);
	}
	
	public function editRoom($id, $name, $error){
		return $this->editData(
				RoomContract::TABLE, 
				array(RoomContract::COL_NAME), 
				array($name), 
				's', 
				RoomContract::_ID . "=" . $id, 
				$error);
	}
	
	public function changeRoomZone($id, $zone_id, $error){
		return $this->editData(
				RoomContract::TABLE,
				array(RoomContract::COL_ZONE_ID),
				array($zone_id),
				'i',
				RoomContract::_ID . "=" . $id,
				$error);
	}
	
	public function removeRoom($id){
		if($this->db->connect_error){
			return "Could not connect database. ";
		}
		
		if(empty($_SESSION['user'])){
			return "User is not logged in";
		}
		
		$user_id = $_SESSION['user']['id'];
		
		$sql = "DELETE FROM " . RoomContract::TABLE . " WHERE "
				. RoomContract::_ID . "=" . $id;
		
		if(!$this->db->query($sql)){
			return "Room was not removed";
		}
		
		$sql = "DELETE FROM " . RoomZoneContract::TABLE . " WHERE "
				. RoomZoneContract::FK_ROOM_ID . "=" . $id;
		
		if(!$this->db->query($sql)){
			return "Room was not removed";
		}
		
		return null;
	}
	
	public function getNumberOfRooms(){
		if($this->db->connect_error){
			return 0;
		}
		
		if(empty($_SESSION['user'])){
			return 0;
		}
		
		$user_id = $this->getUserId();
		
		$sql = "SELECT Count(*) FROM " . RoomContract::TABLE . " r " .
				" INNER JOIN " . HomeContract::TABLE . " h " .
				" ON h." . HomeContract::_ID . "=r." . RoomContract::COL_HOME_ID .
				" WHERE h." . HomeContract::COL_USER_ID . "=" . $user_id;
		
		//echo $sql;
		$res = $this->db->query($sql);
		
		if(!$res) return 0;
		
		$count = $res->fetch_array();
		
		return $count[0];
	}
	
	/********************************* Helper functions *****************************/
	
	private function getUserId(){
		if(empty($_SESSION['user'])){
			return null;
		}
		return $_SESSION['user']['id'];
	}
	
	private function addData($table, $keys, $values, $valueTypes, &$error) {
		if($this->db->connect_error){
			$error = "Could not connect database";
			return false;
		}
		
		if(empty($_SESSION['user'])){
			$error = "User is not logged in";			
			return false;
		}
		
		//build sql parametrized statement
		$keys_string = "";
		$params = "";
		foreach($keys as $key){
			$keys_string .= $key.",";
			$params .= "?,";
		}
		
		$keys_string = substr($keys_string, 0, strlen($keys_string) - 1);
		$params = substr($params, 0, strlen($params) - 1);
		
		$prep = "INSERT INTO " . $table
					. "(" . $keys_string . ")"
					. " VALUES (" . $params . ")";
		$stmt = $this->db->prepare($prep);
		if(!$stmt){
			$error = $this->db->error;
			return false;
		}
		
		//build argument array for $stmt->bind_param function
		$val[] = $stmt;
		$val[] = $valueTypes;
		foreach($values as $key => $value){
			$val[] = &$values[$key];
		}
		//print_r($val);
		//$stmt->bind_param(, $user_id, $name);
		if(!call_user_func_array('mysqli_stmt_bind_param', $val)){
			$error = "Could not bind values";
			return false;
		}
		
		if(!$stmt->execute()){
			$error = "Data were not entered.";
			return false;
		}
		return true;
	}

	private function editData($table, $keys, $values, $valueTypes, $selection, &$error){
		if($this->db->connect_error){
			$error = "Could not connect database";
			return false;
		}
		
		if(empty($_SESSION['user'])){
			$error = "User is not logged in";
			return false;
		}
		
		
		$prep = "UPDATE  " . $table . " SET ";
		
		foreach($keys as $key){
			$prep .= $key ."=?,";
		}
		
		$prep = substr($prep, 0, strlen($prep)-1);
		
		if(!empty($selection)){
			$prep .= " WHERE " . $selection;
		}
		
		//echo ($prep);
		$stmt = $this->db->prepare($prep);
		
		if(!$stmt){
			$error = $this->db->error;
			return false;
		}
		
		//build argument array for $stmt->bind_param function
		$val[] = $stmt;
		$val[] = $valueTypes;
		foreach($values as $key => $value){
			$val[] = &$values[$key];
		}
		
		if(!call_user_func_array('mysqli_stmt_bind_param', $val)){
			$error = "Could not bind values";
			return false;
		}
		
		if(!$stmt->execute()){
			$error = "Home was not changed.";
			return false;
		}
		return true;
	}
}