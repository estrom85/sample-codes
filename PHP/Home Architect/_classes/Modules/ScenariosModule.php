<?php
class ScenariosModule extends Module{
	public function init(){
		$this->addAction("show", "show");
		$this->setDefaultAction("show");
	}

	public function getModuleName(){
		return "scenarios";
	}

	public function displayTitle(){

	}

	public function show(){
		$this->setView("scenarios");
	}
}