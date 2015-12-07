<?php
include_once HOME.'/_classes/Data/DataProvider.php';

class HomeModule extends Module{
	private $data;
	public function init(){
		$this->addAction("show", "show");
		$this->setDefaultAction("show");
		$this->addAction("data", "listHomes");
		$this->addAction("data", "addHome",self::POST);
		$this->addAction("data", "editHome",self::PUT);
		$this->addAction("data", "removeHome",self::DELETE);
		
		$this->data = new DataProvider();
	}
	
	public function getModuleName(){
		return "home";
	}
	
	public function displayTitle(){
	
	}
	
	public function show(){
		$this->setView("home", array("homes" => $this->data->listHomes()));
	}
	
	public function addHome($data){
		$error = NULL;
		$output;
		if($this->data->addHome($data['name'],$error)){
			$output['status'] = 'OK';
		}else{
			$output = array(
				"status" => "ERROR",
				"error" => $error
			);
		}
		echo json_encode($output);
	}
	
	public function editHome($data,$param){
		$error = NULL;
		$output;
		if($this->data->editHome($param[0], $data['name'],$error)){
			$output['status'] = 'OK';
		}else{
			$output = array(
				"status" => "ERROR",
				"error" => $error
			);
		}
		echo json_encode($output);
	}
	
	public function removeHome($data,$id){
		$error;
		$force = false;
		if(strcmp($data['force'], 'true') == 0){
			$force = true;
		}
		$output = null;
		$res = $this->data->removeHome($id[0],$error,$force);
		switch($res){
			case DataProvider::REMOVE_OK:
				$output['status'] = 'OK';
				break;
			case DataProvider::REMOVE_UNRESOLVED_DEPENDENCES:
				$output['status'] = 'REF_ERR';
				break;
			case DataProvider::REMOVE_ERROR:
				$output = array("status" => 'ERROR', "error" => $error);
				break;
		}
		echo json_encode($output);
	}
	
	public function listHomes($data){
		echo json_encode($this->data->listHomes());
	}
}