<?php

/*
 *
 * This is the main class file for connecting to the database
 * It provides support for prepared statements and transactions
 * using the mysqli class
 * This is just a loose extraction of the super class
 *
 */


// a class for connecting to the database
class dbConnect {

	// database variables
	private $host;
	private $username;
	private $password;
	private $database;

	// connection variable for MySQLi
	private $con;
	
	public function __construct(){
		$this->host = "localhost";
		$this->username = "root";
		$this->password = "";
		$this->database = "westeros";
		$this->con = "";
	}
	
	
	// connect to the database
	function connect(){
		$this->con = mysqli_connect($this->host, $this->username, $this->password, $this->database);
		
		if ($this->con->connect_error)
			return false;
		else
			return true;
	}
	
	function get_insert_id(){
	    return $this->con->insert_id;
	}
	
	// old way of executing queries
	function exec_query($query){
		
		$result_set = $this->con->query($query);
		
		if ($result_set == false){ // register an error
			return false;
		} else
			return $result_set;	
	}
	
	// using PDO
	// this uses querys like:
	// "SELECT * FROM table WHERE id = ?"
	function prepare($query){
		$rs = $this->con->prepare($query);
		if ($rs == false)
			trigger_error('Wrong SQL: ' . $query . ' Error: ' . $this->con->error, E_USER_ERROR);
		
		return $rs;
	}
	
	// execute prepared statement
	function execute($query){
		return $query->execute();
	}
	
	// free up memory on the server
	function free($temp){
		$temp->free();
	}
	
	// escape the strings 
	function escape($string){
		return $this->con->escape_string($string);
	}
	
	function set_commit($bool){
		$this->con->autocommit($bool); // false for use with transactions
	}
	
	// just in case the original connection is needed
	// for things like transactions
	function get_con(){
		return $this->con;
	}
	
	// sanatize input from the user
	function input($string){
		return mysqli_real_escape_string($this->con, htmlspecialchars($string));
	}
	
	// close the database
	function close(){
		$this->con->close();
	}

}


?>