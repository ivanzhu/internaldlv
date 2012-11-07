<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of mysql
 *
 * @author Ivan Zhu <wenhua.ivan@gmail.com>
 */
class MySQL{

	private $host;
	private $userName;
	private $userPass;
	public $link;
//	private $link;
	private $dbName;
	
	public $sql;
	public $order = array();
	public $limit;
	
	
	private $errors = array();

	public function __construct($host, $userName, $userPass, $dbName = '', $charset = ''){
		$this->host = $host;
		$this->userName = $userName;
		$this->userPass = $userPass;

		$this->link = mysql_connect($this->host, $this->userName, $this->userPass);
		if(!$this->link){
			$this->errors[] = 'Connect failed : ErrorNum:' . mysql_errno() . ' ' . mysql_error();
			exit();
		}
		
		if(!empty($dbName))
			$this->connectDb($dbName);
	}

	public function connectDb($dbName){
		$this->dbName = $dbName;
		
		if(mysql_select_db($dbName, $this->link)){
			$this->errors[] = 'Select Database ' . $dbName . 'failed : ErrorNum:' . mysql_errno() . ' ' . mysql_error();
			return FALSE;
			exit();
		}else{
			return TRUE;
		}
			
	}
	
	public function executeSQL($sql){
//		$sql_escaped = mysql_escape_string($sql);
//		mysql_
		
	}
	

	public function __destruct(){
		if(count($this->errors))
			$this->displayError();

		if($this->link)
			mysql_close($this->link);
	}
	
	/**
	 * display mysql errors
	 */
	private function displayError(){
		$errorStr = '';
		foreach($this->errors as $value){
			$temp = $value . '<br/>';
			$errorStr .= $temp;
		}

		$errorNum = count($this->errors);

		$errorStr .= 'Error Num  ' . $errorNum;

		echo $errorStr;
	}

}

$mysql = new MySQL('192.168.1.169', 'root', 'zhuwenhua');

echo mysql_client_encoding($mysql->link);