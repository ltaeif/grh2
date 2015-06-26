<?php
class Database 
{
	private static $dbName = 'gestioncarriere' ; 
	private static $dbHost = 'localhost' ;
	private static $dbUsername = 'root';
	private static $dbUserPassword = '';
	
	
	private static $cont  = null;
	
	public function __construct() {
		exit('Init function is not allowed');
	}
	
	public static function connect()
	{
	   // One connection through whole application
       if ( null == self::$cont )
       {      
        try 
        {
		  $dsn=  "mysql:host=".self::$dbHost.";"."dbname=".self::$dbName;
		  
          //self::$cont =  new PDO($dsn , self::$dbUsername, self::$dbUserPassword);  
		  
		  self::$cont =  new PDO($dsn, self::$dbUsername, self::$dbUserPassword, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		  
		  self::$cont->exec("SET CHARACTER SET utf8"); // <-- HERE
        }
        catch(PDOException $e) 
        {
          die($e->getMessage());  
        }
       } 
       return self::$cont;
	}
	
	public static function disconnect()
	{
		self::$cont = null;
	}
}
?>