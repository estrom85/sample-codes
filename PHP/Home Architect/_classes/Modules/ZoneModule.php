<?php
include_once HOME.'/_classes/Data/DataProvider.php';

class ZoneModule extends Module{
	private $data;
	public function init(){
		$this->addAction("show", "show");
		$this->setDefaultAction("show");
		$this->data = new DataProvider();
		
		$this->addAction("data", "listZones",self::GET);
		$this->addAction("data", "addZone",self::POST);
		$this->addAction("data", "editZone",self::PUT);
		$this->addAction("data", "removeZone",self::DELETE);
		
	}
	
	public function getModuleName(){
		return "zone";
	}
	
	public function displayTitle(){
		
	}
	
	public function show(){
		$this->setView("zones", array("homes"=>$this->data->listHomes()));
	}
	
	public function addZone($data, $param){
		$this->data->addZone($data['home'], $data['name']);
	}
	
	public function editZone($data, $param){
		//echo "edit";
		$this->data->editZone($param[0], $data['name']);
	}
	
	public function removeZone($data, $param){
		$this->data->removeZone($param[0]);
	}
	
	public function listZones($data, $param){
		echo json_encode($this->data->listZones($data['home']));
	}
}