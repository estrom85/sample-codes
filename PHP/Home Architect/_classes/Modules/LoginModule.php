<?php
require_once HOME.'/_classes/Data/DataProvider.php';

class LoginModule extends Module{
	private $data;
	
	public function init(){
		$this->addAction("show", "show");
		$this->setDefaultAction("show");
		$this->addAction("login", "login",self::POST);
		$this->addAction("logout", "logout");
		
		$this->data = new DataProvider();
	}
	
	public function getModuleName(){
		return "login";
	}
	
	public function displayTitle(){
	
	}
	
	public function show(){
		if(!empty($_SESSION['user'])){
			$this->redirect(Location::getBaseUrl()."overview");
		}else{
			$this->setView("login");
		}
	}
	
	public function login($data){
		$error = NULL;
		$usr_data = NULL;
		if($this->data->verify_user($data['login'], $data['psswd'], $usr_data, $error)){
			$_SESSION['user'] = $usr_data;
			$this->redirect(Location::getBaseUrl());
		}else{
			$this->setView('login',array("error" => $error));
		}
		/*
		if(strcmp($data['login'],"admin")==0 && strcmp($data['psswd'],"admin") == 0){
			$_SESSION['user']['id'] = 1;
			$_SESSION['user']['type'] = USR_ADMIN;
			
		}else{
			
		}
		*/
	}
	
	public function logout(){
		session_destroy();
		$this->redirect(Location::getBaseUrl());
	}
}