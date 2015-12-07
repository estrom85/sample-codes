<?php
class Application{
	
	/* Properties */
	
	private $mDispatcher;
	private $mHome;
	private $mRunning;
	private $mViews;
	private $mTemplate;
	private $mAppName;
	private $mForceRendering;
	private $mRendering;
	
	/* Constructors */
	
	public function __construct($name, $home, $template = null){
		$this->mHome = $home;
		$this->mAppName = $name;
		$this->mRunning = false;
		$this->mForceRendering = false;
		$this->setTemplate($template);
		$this->mRendering = false;
	}
	
	/* Public methods */
	
	public function run(){
		if($this->mRunning)
		{
			return;
		}
	
		$this->mRunning = true;
	
		$this->mDispatcher = new Dispatcher($this);
		if(!$this->mDispatcher->resolveURL($_SERVER['REQUEST_URI'])){
			include $this->mDispatcher->displayErrorPage(404, $this->mHome);
		}
	
		$this->mDispatcher->performAction($this->mHome);
		$this->render();
	
		$this->mRunning = false;
	}
	
	/* Setters */
	
	public function setView($view, $data = null, $role = View::ROLE_CONTENT){
		$path = $this->getHomeDir() . "/_views/" . $view . ".php";
		if(!file_exists($path))
		{
			return false;
		}
		
		$this->mViews[$role]['NAME'] = $view;
		$this->mViews[$role]['DATA'] = $data;
		$this->mViews[$role]['PATH'] = $path;
		
		return true;
	}
	
	public function setApplicationName($name){
		if(empty($name))
		{
			return;
		}
		
		$this->mAppName = $name;
	}
	
	public function setTemplate($name){
		if(empty($name))
		{
			return false;
		}
		
		$path = $this->getHomeDir() . "/_templates/" . $name . ".php";
		
		if(!file_exists($path))
		{
			return false;
		}
		
		$this->mTemplate = $path;
	}
	
	public function forceRendering(){
		$this->mForceRendering = true;
	}
	
	/* Getters */
	
	public function getHomeDir(){
		return $this->mHome;
	}
	
	/* Template methods */
	
	public function showView($role = View::ROLE_CONTENT){
		if(empty($this->mViews[$role]))
		{
			return;
		}
		
		$DATA = $this->mViews[$role]['DATA'];
		include $this->mViews[$role]['PATH'];
	}
	
	public function getActiveModuleName(){
		return $this->mDispatcher->getCurrentModuleName();
	}
	
	public function getActiveModuleId(){
		return $this->mDispatcher->getCurrentModuleId();
	}
	public function hasView($role = View::ROLE_CONTENT){
		return !empty($this->mViews[$role]);
	}
	
	public function getModuleList(){
		return ModuleManager::getListOfModules();
	}
	
	public function getTitle(){
		if(!$this->mRunning){
			return;
		}
		return $this->getApplicationName() .  " - " . $this->mDispatcher->getCurrentModuleName();
	}
	
	public function getApplicationName(){
		return $this->mAppName;
	}
	
	public function getScriptList(){
		$scripts = null;
		
		foreach($this->mViews as $view){
			$dir = "scripts/". $view['NAME'] . "/";
			foreach(self::glob_r($this->getHomeDir() . "/" . $dir . "*.{js,ref}",GLOB_BRACE) as $script){
				if(strcmp(strrchr($script, "."), ".ref")==0){
					$file = fopen($script, 'r');
					while(!feof($file)){
						$line = trim(fgets($file));
						if(!empty($line)){
							if(!in_array($line, $scripts))
								$scripts[] = $line;
						}
					}
					fclose($file);
				}else{
					$file = strstr($script, $dir);
					$scripts[] = $file;
				}
			}	
		}
		
		return $scripts;
	}
	
	public function getStyleList(){
		$styles = null;
		
		foreach($this->mViews as $view){
			foreach(self::glob_r($this->getHomeDir() . "/styles/" . $view['NAME'] . "/*.css") as $style){
				$file = basename($style);
				$styles[] = "styles/" . $view['NAME'] . "/" . $file;
			}
		}
		
		return $styles;
	}
	
	public function printScriptTags(){
		$scriptList = $this->getScriptList();
		if(empty($scriptList)){
			return;
		}
		foreach($scriptList as $script){
			printf("<script type='text/javascript' src='%s'></script>", $script);
		}
	}
	
	public function printStyleTags(){
		$styleList = $this->getStyleList();
		if(empty($styleList)){
			return;
		}
		foreach($styleList as $style){
			printf("<link rel='stylesheet' type='text/css' href='%s'>",$style);
		}
	}
	
	/* Private methods */
	
	private function render(){
		//check if rendering is possible

		if(empty($this->mViews[View::ROLE_CONTENT]) &&
				(!$this->mForceRendering || empty($this->mTemplate))){
				return;
		}
		$this->mRendering = true;
		
		$APP = $this;
		if(empty($this->mTemplate)){
			$this->showView();
		}
		else{
			include $this->mTemplate;
		}
		
		$this->mRendering = false;
	}
	
	public static function glob_r($pattern, $flags = 0){
		$files = glob($pattern, $flags);
		foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir)
		{
			$files = array_merge($files, self::glob_r($dir.'/'.basename($pattern), $flags));
		}
		return $files;
	}
}
?>