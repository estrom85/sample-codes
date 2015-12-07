<?php
class Location{
	private static $BASE_URL;
	private static $SUBFOLDER;
	private static $SUBFOLDER_CNT;
	
	public static function setBaseURL($url){
		if(empty($url)) return;
		
		$url = trim($url, "/");
		$url_tokens = explode("/", $url);
		
		$subfolder_index = 0;
		
		if(strcmp($url_tokens[0], "http:") !=0 && strcmp($url_tokens[0],"https:")!=0){
			Location::$BASE_URL = "http://".$url;
			$subfolder_index = 1;
		}else{
			Location::$BASE_URL = $url;
			$subfolder_index = 3;
		}
		
		Location::$BASE_URL .= "/";
		
		$len = count($url_tokens);
		
		Location::$SUBFOLDER = array();
		Location::$SUBFOLDER_CNT = 0;
		
		for($i = $subfolder_index; $i<$len; $i++){
			Location::$SUBFOLDER[]=$url_tokens[$i];
			Location::$SUBFOLDER_CNT++;
		}
	}
	
	public static function getBaseUrl(){
		if(empty(Location::$BASE_URL)) return "";
		return Location::$BASE_URL;
	}
	
	public static function getSubfolderArray(){
		if(empty(Location::$SUBFOLDER)) return array();
		return Location::$SUBFOLDER;

	}
	
	public static function getSubfolderCount(){
		if(empty(Location::$SUBFOLDER_CNT)) return 0;
		return Location::$SUBFOLDER_CNT;
	}
	
}