<?php
include_once HOME.'/_classes/Data/DataProvider.php';

class OverviewModule extends Module{
	private $data;
	
	public function init(){
		$this->addAction("show", "show");
		$this->setDefaultAction("show");
		
		$this->data = new DataProvider();
	}
	
	public function getModuleName(){
		return "overview";
	}
	
	public function displayTitle(){
		
	}
	
	public function show(){
		$this->setView("overview",array(
				"homes"=>$this->data->getNumberOfHomes(),
				"zones"=>$this->data->getNumberOfZones(),
				"rooms"=>$this->data->getNumberOfRooms(),
				"scenarios"=>8));
	}
}