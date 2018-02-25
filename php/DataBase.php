<?php
class DataBase {
	private $host = "localhost";
	private $user = "root";
	private $password = "";
	private $dbName = "CleverBot";
	public $conn;

	public function getConnection(){
		$this->conn = NULL;
		$this->conn = new mysqli($this->host, $this->user, $this->password, $this->dbName);
		mysqli_set_charset($this->conn,"utf8");
		return $this->conn;
	}
}
?>