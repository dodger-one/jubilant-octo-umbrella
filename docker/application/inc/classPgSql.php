<?php

class PgSql
{
    private $db;       //The db handle
    public  $num_rows; //Number of rows
    public  $last_id;  //Last insert id
    public  $aff_rows; //Affected rows

    public function __construct()
    {
        $connectionstring = $_SERVER['DATABASE_URL'] ;
        $connectionarray = preg_split("/(:|@|\/)/", $connectionstring, -1, PREG_SPLIT_NO_EMPTY);
        $username = $connectionarray[1];
        $password = $connectionarray[2];
        $hostname = $connectionarray[3] ;
        $port = $connectionarray[4] ;
        $dbname = $connectionarray[5] ;
        $this->db = new PDO("pgsql:host=db;port=" . $port . ";dbname=" . $dbname . ";user=" . $username . ";password=" . $password);
        if (!$this->db) exit();
    }

    public function close()
    {
        //pg_close($this->db);
        $this->db= null;
    }


    // For SELECT
    // Returns one row as object
    public function getRows($sql)
    {
        //echo (' the query: ' . $sql);
        //$result = pg_query($this->db, $sql);
        $sth = $this->db->prepare($sql);
        $sth->execute();
        $ressult = $sth->fetchAll();
        //$row = pg_fetch_object($result);
        //if (pg_last_error()) exit(pg_last_error());
        return $ressult;
    }


    // 
    // just execute any sql
    public function execquery($sql)
    {
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sth = $this->db->prepare($sql);
	try{ 
        	$sth = $this->db->prepare($sql);
	    	$ressult = $sth->execute();
	} 
	catch(PDOException $exception){ 
		return $exception; 
	} 
	return 0;
    }
    

}
        
