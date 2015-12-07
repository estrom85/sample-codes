<?php
include_once HOME.'/_classes/Data/DataProvider.php';

class ActorsModule extends Module{
	private $data;
	
	public function init(){
		$this->data = new DataProvider();	
		$this->addAction("show", "show");
		$this->setDefaultAction("show");
	}

	public function show(){
		$this->setView("actors", array("homes" => $this->data->listHomes()));
	}




	public function getModuleName(){
		return "actors";
	}

	public function displayTitle(){

	}
}