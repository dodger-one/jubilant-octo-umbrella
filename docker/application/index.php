<?php
require __DIR__ . "/inc/bootstrap.php";
 
//phpinfo();
//exit();
//$pg = new PgSql();

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode( '/', $uri );
 
if (isset($uri[1]) && $uri[1] != 'hello') {
    //header("HTTP/1.1 404 Not Found");
    echo ( 'First parameter should be \'hello\'' . "\n" ) ;
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];

switch ($method)
{
        case 'GET':
                //echo "GET request method\n";
                //echo print_r($_GET, true);
                getUserData($uri);
                break;
//        case 'POST':
//                echo "POST request method\n";
//                echo print_r($_POST, true);
//                break;
        case 'PUT':
                //$_PUT = parseInput();
        
                echo "PUT request method\n";
                //echo print_r($_PUT, true);
                putUserData($uri);
                break;
//        case 'DELETE':
//                //$_DELETE = parseInput();
//        
//                echo "DELETE request method\n";
//                echo print_r($_DELETE, true);
//                break;
        default:
                echo "Unknown request method.";
                break;
}

?>
