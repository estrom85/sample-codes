<?php
abstract class Module {
	const GET = 'GET';
	const POST = 'POST';
	const PUT = 'PUT';
	const DELETE = 'DELETE';
	
	/***************** Properties *********************/
	
	private $mActions;				//list of permitted actions
	private $mDefaultActionId;		//default action identificator
	private $method;
	private $data;
	
	private $App;					//reference to application handler


	
	/***************** Constructors ********************/
	
	public function __construct(Application $app) 
	{
		$this->App = $app;
		$this->mRenderMode = false;
		$this->method = $this->getMethod();
		$this->data = $this->getData($this->method);
		
	}
	
	/**************** Abstract methods *****************/
	
	public abstract function init();
	public abstract function getModuleName();
	public abstract function displayTitle();
	
	/****************** View methods *******************/
	
	/*
	 * These methods are used exclusively in templates and views
	 */
	
	public function execute($id, $params) {

		if(empty($id)&&!empty($this->defaultActionId)){
			$id = $this->defaultActionId;
		}
		
		if(empty($id)||empty($this->actions[$this->method][$id])){
			return false;
		}
		
		
		
		
		$action = $this->actions [$this->method][$id];
		
		
		$this->$action ( $this->data, $params );
		
		return true;
	}
	
	public function actionIsSet($id) {
		return ! empty ( $this->actions [$this->method][$id] );
	}
	
	protected function addAction($id, $method, $http_method = self::GET) {
		$this->actions [$http_method][$id] = $method;
	}
	
	protected function setDefaultAction($id) {
		if (empty ( $this->actions ['GET'][$id] ))
			return;
		$this->defaultActionId = $id;
	}
	
	protected function setView($view, $data = null, $role = View::ROLE_CONTENT) {
		$this->App->setView($view,$data,$role);
	}
	
	protected function setTemplate($name){
		$this->App->setTemplate($name);
	}
	
	protected function forceRendering(){
		$this->App->forceRendering();
	}
	
	protected function getApplication(){
		return $this->App;
	}
	
	protected function redirect($path){
		header("Location: " . $path);
		exit;
	}
	
	private function getMethod(){
		$method = $_SERVER['REQUEST_METHOD'];
		if($method == 'POST' && key_exists('HTTP_X_HTTP_METHOD', $_SERVER)){
			$method = $SERVER['HTTP_X_HTTP_METHOD'];
		}
		return $method;
	}
	private function getData($method){
		$output = null;
		switch($method){
			case 'GET':
				$output = $_GET;
				break;
			case 'POST':
				$output = $_POST;
				break;
			case 'PUT':
			case 'DELETE':
				$data = file_get_contents("php://input");
				parse_str($data,$output);
				break;
		}
		return $this->cleanData($output);
	}
	
	private function cleanData($data){
		$output = null;
		if(is_array($data)){
			foreach($data as $key=>$value){
				$output[$key] = $this->cleanData($value);
			}
		}else{
			$output = trim(strip_tags($data));
		}
		return $output;
	}
}