<?php

$theusername = '';
$thedate = '';

// 
// Check that the Uri contains 'hello' in the correct position
// 
function hellocheck($theuri){
    if (isset($theuri[1]) && $theuri[1] != 'hello') {
        header("HTTP/1.1 404 Not Found");
        //echo ( 'First parameter should be \'hello\'' . "\n" ) ;
        http_response_code(404);
        exit();
    }
}

//
//Check that the username is valid (as requirements)
//
function checkUserName($theusername) {
        if ( ! $theusername || strlen($theusername) < 3) {
            //echo ('Username must have at least 2 letters ->' . $theusername .  "<-\n") ;
            return 1;
        } elseif ( ! ctype_alpha($theusername) ) {
            //echo ('Username should have only letters ->' . $theusername .  "<-\n") ;
            return 1;
        } else {
            return 0;
        }
}

//
//Check that the provisioned date is greater or not that current date
//
function dateGreater($thedate)
{
        $today = date('Y-m-d');
        if($thedate > $today) {
            //echo('greater');
            return 1;
        } else {
            //echo('lower');
            return 0;
        }
}



//
//Get user data from database checking the requirements
//
function getUserData($theuri) {
        $theusername = $theuri[2] ;
        $pg = new PgSql();
        $userexists = checkUserName($theusername);
        if ( $userexists == 0 ) {
            //echo ('Getting info from ' . $theusername . "\n" ) ;
            $sql = "SELECT * FROM revolut_test where thename = '" . $theusername . "';";
            $alldata = $pg->getRows($sql);
            if ( count($alldata) > 1 ) {
                //echo ('ERROR, more than 1 ressult found: ' . count($alldata)) ;
                header("HTTP/1.1 400 Bad Request");
                http_response_code(400);
                exit();
            } else if (count($alldata) == 0) {
                //echo ('User not found') ;
                header("HTTP/1.1 400 Bad Request");
                http_response_code(400);
                exit();
            } else {
                sayMessage($alldata[0]);
            }
        // this part can go inside testing part
        } else {
            //echo ('User does not exists or is not valid') ;
            header("HTTP/1.1 400 Bad Request");
            http_response_code(400);
            exit();
        }
}

//
//Return the json requested by the tests
//
function sayMessage($theinfo)
{
// Revolut test expected ressults
//{ ???message???: ???Hello, <username>! Your birthday is in N day(s)???
//}
//{ ???message???: ???Hello, <username>! Happy birthday!??? }
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
        http_response_code(200);
        exit();
}

//
//Write data to the database
//
function putUserData($theuri) {
        $pg = new PgSql();
        if ( !isset($theuri[2]) || ! preg_match("/^[a-zA-Z]{2,99}{\"dateOfBirth\":\"[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])\"}$/",$theuri[2]) ) {
            //echo ( 'Second parameter should be \'username{"dateOfBirth":"YYYY-MM-DD"}\'' . "\n" ) ;
            header("HTTP/1.1 400 Bad Request");
            http_response_code(400);
            exit();
        } else {
            $wholedata = preg_split("/({|}|:)/", $theuri[2], -1, PREG_SPLIT_NO_EMPTY) ;
            $theusername = $wholedata[0] ;
            $dateofbirth = $wholedata[1] ;
            $thedate = $wholedata[2] ;

            $userexists = checkUserName($theusername);
            if ( $userexists <> 0 ) {
                //echo ('Username invalid ' . "\n") ;
                header("HTTP/1.1 400 Bad Request");
                http_response_code(400);
                exit();
            }
            if ( $dateofbirth <> '"dateOfBirth"') {
                //echo ('you miss something important ->' . $dateofbirth . "<-\n" ) ;
                header("HTTP/1.1 400 Bad Request");
                http_response_code(400);

                exit();
            }
            if (preg_match("/^\"[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])\"$/",$thedate)) {
		//$thedate = str_replace('"', '\'', $thedate) ;
		$thedate = str_replace('"', '', $thedate) ;
                $datecheck = dateGreater($thedate);
                if ($datecheck <> 0) {
                    //echo ('Date should be lower than today: ' . $thedate);
                    header("HTTP/1.1 400 Bad Request");
                    http_response_code(400);
                    exit();
                }
                
                //echo ( 'theusername :' . $theusername . "\n");
                //echo ( 'thedate :' . $thedate . "\n") ;

                //$updateressult = $pg->insertOrUpdate($theusername, $thedate);
                $sql = 'INSERT INTO revolut_test(thename,date_of_birth) VALUES(\'' . $theusername . '\',\'' . $thedate . '\' ) ON CONFLICT (thename) DO UPDATE SET date_of_birth=\'' . $thedate . '\'' ;
                //echo $sql;
                $updateressult = $pg->execquery($sql);
		
                //echo ('Ressult:' . $updateressult);
                http_response_code(204);
            } else {
                echo ('Date of bith should be YYYY-MM-DD' . "\n" ) ;
                exit();
            }
        }
}
