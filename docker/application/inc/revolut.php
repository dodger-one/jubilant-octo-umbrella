<?php
//phpinfo();
//exit();
//$pg = new PgSql();

function getUserData($theuri) {
        $theusername = $theuri[2] ;
        $pg = new PgSql();
        if ( ! $theusername || strlen($theusername) < 3) {
            echo ('Username must have at least 2 letters ->' . $theusername .  "<-\n") ;
            exit();
        } elseif ( ! ctype_alpha($theusername) ) {
        //if ( !isset($uri[2]) || ! preg_match("/^[a-zA-Z]{2,99}$/",$uri[2]) ) {
            echo ('Username should have only letters ->' . $theusername .  "<-\n") ;
            exit();
        } else {
            //echo ('Getting info from ' . $theusername . "\n" ) ;
            $sql = "SELECT * FROM revolut_test where thename = '" . $theusername . "';";
            $alldata = $pg->getRows($sql);
            if ( count($alldata) > 1 ) {
                echo ('ERROR, more than 1 ressult found: ' . count($alldata)) ;
                exit();
            } else if (count($alldata) == 0) {
                echo ('User not found') ;
                exit();
            } else {
                sayMessage($alldata[0]);
            }
        }
}

function sayMessage($theinfo)
{
// Revolut test expected ressults
//{ “message”: “Hello, <username>! Your birthday is in N day(s)”
//}
//{ “message”: “Hello, <username>! Happy birthday!” }
        $birthday           = $theinfo['date_of_birth'];
        $today              = date('Y-m-d');
        list($ybirth, $mbirth, $dbirth) = explode('-', $birthday);
        list($ytoday, $mtoday, $dtoday) = explode('-', $today);
        $today = date_create(date('Y-m-d'));
        $today->setTime(24,0,0);
        $birthday = date_create($ytoday . '-' . $mbirth . '-' . $dbirth);
        $birthday->setTime(24,0,0);
        $diff=date_diff($today,$birthday);
        $daysdiff=$diff->days;
        if ($daysdiff == 0) {
                $message = array("message"=>"Hello, ". $theinfo['thename'] . '! Happy Birthday! ');
        } else {
                //if ($m2 > $m1 || ($m2 == $m1 && $d2 > $d1) ) {
                if ($mtoday > $mbirth || ($mtoday == $mbirth && $dtoday > $dbirth) ) {
                        // birthday has pass this year
                        $daysdiff=365-$daysdiff;
                }
                $message = array("message"=>"Hello, ". $theinfo['thename'] . '! Your birthday is in ' . $daysdiff . ' day(s)');

        }
        echo json_encode($message) . "\n";
//        exit();
}

function putUserData($theuri) {
        $pg = new PgSql();
        //echo ( $uri[2] . "\n" . '<br>' . "\n" ) ;
        if ( !isset($theuri[2]) || ! preg_match("/^[a-zA-Z]{2,99}{\"dateOfBirth\":\"[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])\"}$/",$theuri[2]) ) {
            echo ( 'Second parameter should be \'username{"dateOfBirth":"YYYY-MM-DD"}\'' . "\n" ) ;
            exit();
        } else {
            $wholedata = preg_split("/({|}|:)/", $theuri[2], -1, PREG_SPLIT_NO_EMPTY) ;
            //print_r($wholedata);
            $theusername = $wholedata[0] ;
            $dateofbirth = $wholedata[1] ;
            $thedate = $wholedata[2] ;
            if ( ! ctype_alpha($theusername) ) {
                echo ('Username should have only letters' . "\n") ;
                exit();
//            } else {
//                echo ( '#################' . $theusername . "\n");
            }
            if ( $dateofbirth <> '"dateOfBirth"') {
                echo ('you miss something important ->' . $dateofbirth . "<-\n" ) ;
                exit();
            }
            if (preg_match("/^\"[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])\"$/",$thedate)) {
                echo ( 'theusername :' . $theusername . "\n");
                echo ( 'thedate :' . $thedate . "\n") ;

                $updateressult = $pg->insertOrUpdate($theusername, $thedate);
                //$sql = 'INSERT INTO revolut_test(thename,date_of_birth) VALUES(' . $theusername . ',' . $thedate . ' ) ON CONFLICT (thename) DO UPDATE SET date_of_birth=' . $thedate ;
                //echo $sql;
                //$updateressult = $pg->insertOrUpdate($sql);
                echo ('Ressult:' . $updateressult);
            } else {
                echo ('Date of bith should be YYYY-MM-DD' . "\n" ) ;
                exit();
            }
        }
}
// *************************************************************************************
// *************************************************************************************
// *************************************************************************************
//things to do
//  * extract all the functions from index.php (create a revolut.php?)
//  * the function insertOrUpdate should do raw upsert, the logic must be on revolut.php
//  * the date of birth should be checked (not greater than actual date...
// *************************************************************************************
// *************************************************************************************
// *************************************************************************************
