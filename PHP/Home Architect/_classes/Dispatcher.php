<?php
class Dispatcher{
	private static $URL_OK = 0;
	
	private $controller;
	private $action;
	private $parameters;

	private $module;
	
	private $app;
	
	public function __construct(Application $app){
		$this->app = $app;
	}
	
	public function resolveURL($url){
		if(!$this->parseURL($url)){
			return false;
		}
		
		if(empty($this->controller) && !empty($_GET['control'])){
			$this->controller = $_GET['control'];
		}
		
		if(empty($this->action) && !empty($_GET['action'])){
			$this->action = $_GET['action'];
		}
		
		if(empty($this->parameters) && !empty($_GET['params'])){
			$this->parameters = $_GET['params'];
		}
		
		return true;
	}
	
	public function performAction($home){
		$this->module = ModuleManager::getModule($this->app, $this->controller);
		
		if($this->module == null){
			$this->displayErrorPage(401, $home);
		}
		if(!$this->module->execute($this->action, $this->parameters)){
			$this->displayErrorPage(404, $home);
		}
	}
	
	/**
	 * Extracts information from URL
	 * @param string $url
	 * @return boolean returns true if URL is valid else returns false
	 */
	private function parseURL($url){
		//remove GET parameters from URL
		$param_start = strpos($url, "?");
		if($param_start>0){
			$url = substr($url,0, $param_start);
		}
		
		//remove beginning and ending /
		$url = trim($url, "/");
		
		//transform URL to array
		$url_tokens = explode("/", $url);
		$url_cnt = count($url_tokens);
		
		//get Subfolder part of URL
		$subfolder_cnt = Location::getSubfolderCount();

		//check if request is in the same Subfolder then defined in application Base URL
		//check if subfolder level of request is greater than subfolder level of base URL
		if($url_cnt < $subfolder_cnt){
			return false;
		}
		
		//check if the request is for the same subfolder that defined in Base URL
		$subfolder_array = Location::getSubfolderArray();
		for($i = 0; $i<$subfolder_cnt; $i++){
			if(strcmp($url_tokens[$i],$subfolder_array[$i])!=0){
				return false;
			}
		}	
		
		$param_cnt = $url_cnt - $subfolder_cnt;
		//extract controller from URL
		if($param_cnt > 0){
			$this->controller = $url_tokens[$subfolder_cnt];
		}
		//extract action from URL
		if($param_cnt > 1){
			$this->action = $url_tokens[$subfolder_cnt + 1];
		}
		//extract parameters from URL
		if($param_cnt > 2){
			$this->parameters = array_slice($url_tokens,$subfolder_cnt+2);
		}
		
		return true;
	}
	
	
	
	public function displayErrorPage($error, $home){
		$file = $home.'/_views/error_page/'.$error.'.php';
		if(file_exists($file)){
			include $file;
		}else{
			die("could not display the page");
		}
	}
	
	public function getCurrentModuleName(){
		return $this->module->getModuleName();
	}
	
	public function getCurrentModuleId(){
		return $this->controller;
	}
}