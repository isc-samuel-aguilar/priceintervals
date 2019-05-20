<?php
//used to force to open the url ndex.php/app
if (!strpos($_SERVER['REQUEST_URI'], 'index' )) {
    header("Location: index.php/app", true, 301);
    exit();
}

//The classes will be loaded with the autoload, and require that the name of the file have to be uppercase the first letter and the rest lower case
require 'autoload.php';

/**
 * Controller::getRequestInfo() is a static function to get the information from the request
 */


// Db class that is sent in the constructor to allow to get connexion and execute queries.
$db = new Db();


//Check that the controller exist in the endpoint
if(!empty(Controller::getRequestInfo()->controller) ){
    //Change the case of the request to Capitalize (and allow the autoload to detect it). It will be instanced    

    try{        
        // Get the controller from the url, after index.php/
        $controllerName = Controller::getRequestInfo()->controller;
        $interval = new Interval($db);

        /**
         * Create the controller called in the url and the objects for dependency injection
         */
        if ($controllerName === 'Priceinterval'){            
            $controller = new Priceinterval( $db, new Utilities($db), new Interval_mdl(new Utilities($db), $interval), new Interval($db)  );        
        } elseif($controllerName === 'App'){
            $controller = new App( $db, $interval, new Utilities($db) );
        }  elseif($controllerName === 'Test'){
            $controller = new Test( $db, new Utilities($db), new Casesguide($db));
        } else {
            $controller = new Controller( $db , new Utilities($db));
            $controller->printResponse(404, "Invalid request...");
            die();

        }
    } catch (Exception $e){
        error_log("Controller $controllerName not found");
    }

    //Check that the 4th parameter is a controller
    if ($controller && is_a($controller ,'Controller')){

        try{
            //Call the method of the controller requested in the url, if any was indicated it will call the index method
            $result = $controller->{ $controller->requestInfo->method }();
 
            
            //if the requested controller is the app, it will print the view, and don't have to print the common response (Defined in the requirements)
            // if is not the app controller, it will print the list of intervals in the database (Defined in the requirement )
            
            if( !is_a($controller,'App' ) ){
                $controller->printResponse();
            }       
            
        } catch (Exception $e){
            //If the controller can't be loaed, return a 404 issue
            $controller->responseCode = $controller->utilities->errorCodes[ $controller->requestInfo->request_method ];
            $controller->response = array('error'=>$e->getMessage());
            error_log($e->getMessage());
            $controller->printResponse($controller->responseCode, $e->getMessage());
        }
        
    } else {  
        //Return 404 error if the requested file that is not a controller
        $controller->printResponse(404, "Invalid request...");            
    }
} else {
    //Return 404 error if the controller was not indicated
    $controller = (new Controller($db, new Utilities($db)))->printResponse(404, "Invalid request...");            
}
