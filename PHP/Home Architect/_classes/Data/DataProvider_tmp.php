<?php
class DataProvider{
	public function __construct(){
		if(!empty($_SESSION['homes'])) return;
		$_SESSION['counters']['homes'] = 0;
		$_SESSION['counters']['zones'] = 0;
		$_SESSION['counters']['rooms'] = 0;
		
		$id = $this->addHome("home1");
		$this->addZone($id, "zone1");
		
		$id = $this->addHome("home2");
		$this->addZone($id, "zone2");
		$this->addZone($id, "zone3");
		
		$id = $this->addHome("home3");
		$this->addZone($id, "zone4");
		$this->addZone($id, "zone5");
		$this->addZone($id, "zone6");
		
		
	}
	
	public function addHome($name){
		$_SESSION['counters']['homes']++;
		$_SESSION['homes'][]['name'] = $name;
		end($_SESSION['homes']);
		$i = key($_SESSION['homes']);
		$_SESSION['homes'][$i]['id'] = $i;
		return $i;
	}
	
	public function deleteHome($id){
		if(empty($_SESSION['homes'][$id])) return;
		
		$zones = $_SESSION['homes'][$id]['zones'];
		foreach($zones as $zone){
			$this->removeZone($zone['id']);
		}
		unset($_SESSION['homes'][$id]);
		$_SESSION['counters']['homes']--;
	}
	
	public function editHome($id, $name){
		if(empty($_SESSION['homes'][$id])) return;
		$_SESSION['homes'][$id]['name'] = $name;
	}
	
	public function listHomes(){
		return $_SESSION['homes'];
	}
	
	public function getNumberOfHomes(){
		return $_SESSION['counters']['homes'];
	}
	
	public function addZone($home_id, $name){
		if(empty($_SESSION['homes'][$home_id])) return;
		$_SESSION['zones'][] = array("name"=>$name, "home_id"=>$home_id);
		end($_SESSION['zones']);
		$id = key($_SESSION['zones']);
		$_SESSION['zones'][$id]['id'] = $id;
		
		$_SESSION['homes'][$home_id]['zones'][$id] = &$_SESSION['zones'][$id];
		
		$_SESSION['counters']['zones']++;
		return $id;
	}
	
	public function editZone($id, $name){
		if(empty($_SESSION['zones'][$id])) return;
		$_SESSION['zones'][$id]['name'] = $name;
	}
	
	public function removeZone($id){
		$home_id = $_SESSION['zones'][$id]['home_id'];
		unset($_SESSION['zones'][$id]);
		unset($_SESSION['homes'][$home_id]['zones'][$id]);
		$_SESSION['counters']['zones']--;
	}
	
	public function listZones($home_id){
		if(empty($_SESSION['homes'][$home_id]['zones'])) return null;
		//print_r($_SESSION['homes']);
		//print_r($_SESSION['zones']);
		return $_SESSION['homes'][$home_id]['zones'];
	}
	
	public function getNumberOfZones(){
		return $_SESSION['counters']['zones'];
	}
	
	public function addRoom($zone_id, $name){
		if(empty($_SESSION['zones'][$zone_id])) return;
		$_SESSION['rooms'][] = array("name" => $name, "zone_id" => $zone_id);
		end($_SESSION['rooms']); 
		$id = key($_SESSION['rooms']);
		$_SESSION['rooms'][$id]['id'] = $id;
		$_SESSION['zones'][$zone_id]['rooms'][$id] = &$_SESSION['rooms'][$id];
		
		$_SESSION['counters']['rooms']++;
	}
	
	public function editRoom($room_id, $name){
		if(empty($_SESSION['rooms'][$room_id])) return;
		$_SESSION['rooms'][$room_id]['name'] = $name;
	}
	
	public function removeRoom($id){
		$zone_id = $_SESSION['rooms'][$id]['zone_id'];
		unset($_SESSION['rooms'][$id]);
		unset($_SESSION['zones'][$zone_id]['rooms'][$id]);
		$_SESSION['counters']['rooms']--;
	}
	
	public function listRoomsByHomeId($home_id){
		$output;
		foreach($_SESSION['homes'][$home_id]['zones'] as $zone){
			
		}
		
	}
	
	public function listRoomsByZoneId($zone_id){
		
	}
}