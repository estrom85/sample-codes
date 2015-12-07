<?php
class SensorModule extends Module{
	public function init(){
		$this->addAction("show", "show");
		$this->setDefaultAction("show");
	}
	
	public function show(){
		$this->setView("sensors");
	}
	
	
	
	
	public function getModuleName(){
		return "sensor";
	}
	
	public function displayTitle(){
		
	}
}