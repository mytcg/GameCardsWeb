<?php
class dbconnect
{
	var $host = 'localhost';
	var $user = 'mobidvzmrz_1';
	var $pass = 'C9KKLhi8';
	var $dbase = 'mobidvzmrz_db1';
	var $dbconnection;
	
	function __construct()
	{
		$this->dbconnection = mysql_connect($this->host, $this->user, $this->pass)
			or die (CRLF."* Unable to connect to Database Server");
		
		mysql_select_db($this->dbase, $this->dbconnection)
			or die (CRLF."* Unable to connec to Database -> ".$this->dbase);
	}
	
	function _myqu($sql)
	{
		if($result = mysql_query($sql, $this->dbconnection))
		{
			$data = array();
			while($row = mysql_fetch_assoc($result))
			{ 
				$data[] = $row;
			}
			return $data;
		}
		else
		{
			return false;
		}
	}
}
?>