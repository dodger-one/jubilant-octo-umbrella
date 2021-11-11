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
//        print_r ($connectionarray) ;
//        echo ( ' host = ' . $hostname ) ;
//        exit();
        //$this->db = new PDO("pgsql:host=" . $hostname . ";port=" . $port . ";dbname=" . $dbname . ";user=" . $username . ";password=" . $password);
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

    //public function Upsert($theuser, $thedate)
    public function insertOrUpdate($theusername, $thedate)
    //public function insertOrUpdate($sql)
    {
        //echo ('Username: ' . $theusername);
        $sql = 'INSERT INTO revolut_test(thename,date_of_birth) VALUES(:theuser,:thedate) ON CONFLICT (thename) DO UPDATE SET date_of_birth=:thedate ';
        $sth = $this->db->prepare($sql);
        $sth->bindValue(':theuser', $theusername);
        $sth->bindValue(':thedate', $thedate);
        $sth->execute();
        return 0;
    }

        // For INSERT
    // Returns last insert $id
    public function insert_old($sql, $id='id')
    {
        $sql = rtrim($sql, ';');
        $sql .= ' RETURNING '.$id;
        $result = pg_query($this->db, $sql);
        if (pg_last_error()) exit(pg_last_error());
        $this->last_id = pg_fetch_result($result, 0);
        return $this->last_id;
    }

    // For UPDATE, DELETE and CREATE TABLE
    // Returns number of affected rows
    public function exec($sql)
    {
        $result = pg_query($this->db, $sql);
        if (pg_last_error()) exit(pg_last_error());
        $this->aff_rows = pg_affected_rows($result);
        return $this->aff_rows;
    }

}
        
