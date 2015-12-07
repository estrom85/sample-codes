<?php
include_once HOME.'/_classes/Data/DataProvider.php';

class RoomsModule extends Module{
	private $data;
	
	public function init(){
		$this->data = new DataProvider();
		
		$this->addAction("show", "show");
		$this->setDefaultAction("show");
		$this->addAction('data', 'listRooms',self::GET);
		$this->addAction('data', "addRoom", self::POST);
		$this->addAction('data', 'editRoom',self::PUT);
		$this->addAction('data', 'removeRoom',self::DELETE);
	}

	public function getModuleName(){
		return "rooms";
	}

	public function displayTitle(){

	}

	public function show(){
		$this->setView("rooms",array("homes"=>$this->data->listHomes()));
	}
	
	public function listRooms($data,$params){
		//print_r($data);
		$list;
		if(empty($data['zone'])){
			$list = $this->data->listRoomsByHomeId($data['home']);
		}else{
			$list = $this->data->listRoomsByZoneId($data['zone']);
		}
		echo json_encode($list);
	}
	
	public function addRoom($data, $params){
		$zone = null;
		if(!empty($data['zone'])){
			$zone = $data['zone'];
		}
		$error = NULL;
		$output;
		if($this->data->addRoom($data['home'], $zone, $data['name'], $error)){
			$output['status'] = 'OK';
		}else{
			$output = array("status"=>"ERROR", "error"=>$error);
		}
		echo json_encode($output);
	}
	
	public function editRoom($data, $params){
		$error = NULL;
		if(!empty($data['name'])){
			$this->data->editRoom($params[0], $data['name'], $error);
		}
		
		if(!empty($data['zone'])){
			$this->data->changeRoomZone($params[0], $data['zone'],$error);
		}
	}
	public function removeRoom($data, $params){
		//echo "test";
		$this->data->removeRoom($params[0]);
	}
}