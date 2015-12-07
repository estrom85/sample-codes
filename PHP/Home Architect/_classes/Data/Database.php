<?php
abstract class Database{
	public static $HOST;
	public static $DATABASE;
	public static $USERNAME;
	public static $PASSWORD;
	public static $PORT = 3306;
	
	private $db;
	private $connected;
	public static $CONNECT_ERROR;
	
	public function connect(){
		$this->db = new mysqli(self::$HOST, self::$USERNAME, self::$PASSWORD, self::$DATABASE, self::$PORT);
		self::$CONNECT_ERROR = $this->db->connect_error;
		$this->connected = !self::$CONNECT_ERROR;
		return $this->db;
	}
	
	public function close(){
		if($this->connected){
			$this->db->close();
		}
	}
	
	public abstract function create();
	public abstract function delete();
	
	public function getDb(){
		return $this->db;
	}
	
	protected function isConnected(){
		return $this->connected;
	}
	
	protected function dropTable($table){
		if(!$this->isConnected()) return;
		$sql = sprintf("DROP TABLE %s",$table);
		$success = $this->db->query($sql);
		if($success){
			printf("Table %s dropped. <br>", $table);
		}else{
			printf("Error: %d - %s <br>", $this->db->errno, $this->db->error);
		}
		return $success;
	}
	
	protected function query($sql){
		if(!$this->isConnected()) return false;
		return $this->db->query($sql);
	}
	
	
}