<?php

class Controller
{
    /**
     * @var Db Dependency injection of DB Object
     */
    private $db;
    /**
     * @var Utilities Dependency injection of Utilities Object
     */
    protected $utilities;

    /**
     * @var reponse that will be returned of the request
     *
     */
    protected $response;
    /**
     * @var int response code for the request
     */
    protected $responseCode;
    protected $requestMethod;
    protected $requestInfo;

    /**
     * Controller constructor.
     * @param Db $db Instance of Db
     * @param Utilities $utilities Instance of utilities
     */
    function __construct(Db $db, Utilities $utilities) {
        $this->utilities = $utilities;
        $this->db = $db;
        $this->requestInfo = $this->getRequestInfo();
        $this->responseCode = 500;
    }


    /**
     * @param string $name name of the property
     * @return mixed return the property if exists, null if not
     */
    function __get(string $name)  {
        return isset($this->$name) ? $this->$name : null;
    }


    /**
     * @param string $name Name of the property
     * @param $value value to assign to the property
     * @return bool
     */
    function __set(string $name, $value)  {
        if (property_exists($this, $name)) {
            $this->$name = $value;
            return true;
        }
        return false;
    }

    /**
     * Return the code, printe the response as json_encoded and return the result in the original format
     * @param int $code used to for response code
     * @param mixed $response
     * @return array|reponse|null
     */
    public function printResponse($code = null, $response = null) {
        $responseCode = ($code === null) ? $this->responseCode : $code;
        $responseResult = ($response === null) ? $this->response : $response;
        $result = array();

        //Sen the code of the request
        http_response_code($responseCode);

        if (400 <= $responseCode && $responseCode < 600) {
            // Error!!:
            $this->setRequestHeaders();
            $result = $responseResult;
            echo json_encode(array('msg' => 'error', 'response' => $responseResult));
            //echo json_encode(false);

        } else {
            //All the methods will return the list of intervals in db except the GET/<id>, it return only 1
            if ($this->requestInfo->request_method !== 'GET') {
                $getDbIntervals = $this->db->getAllIntervals();
                $responseResult = Db::queryResultToArray($getDbIntervals);
            } else {
                $responseResult = $this->response;
            }

            foreach ((array)$responseResult as $intervalId => $interval) {
                $result[] = $interval;
            }
            $this->setRequestHeaders();
            echo json_encode($result);
        }

        return $result;
    }

    /**
     * Set the header based on the type of the request method
     */
    public function setRequestHeaders()  {
        $method = $_SERVER['REQUEST_METHOD'];
        switch (strtoupper($method)) {
            case 'GET':
                header("Content-Type: application/json; charset=UTF-8");
                //header("Access-Control-Allow-Origin: *");
                break;
            case 'POST':
                header("Content-Type: application/json; charset=UTF-8");
                //header("Access-Control-Allow-Origin: *");
                //header("Access-Control-Allow-Methods: POST");
                //header("Access-Control-Max-Age: 3600");
                //header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
                break;
            case 'PUT':
                header("Content-Type: application/json; charset=UTF-8");
                //header("Access-Control-Allow-Origin: *");
                //header("Access-Control-Allow-Methods: POST");
                //header("Access-Control-Max-Age: 3600");
                //header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

                break;
            case 'DELETE':
                header("Content-Type: application/json; charset=UTF-8");
                //header("Access-Control-Allow-Origin: *");
                //header("Access-Control-Allow-Methods: POST");
                //header("Access-Control-Max-Age: 3600");
                //header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

                break;
        }
    }

    /**
     * Get the information from the url and request, parse it and return a method with the properties.
     * Parse the information from browsers,, PostMan and PHP Unit
     * Object PropertiesS:
     *      controller      => Name of the controller to execute the reqquest
     *      method          => name of the method of the controller to execute the request, if wasn't sent, the index will be executed
     *      requestParams   => list of params sent in the body to the controller
     *      urlParams       => list of params sent in the URL to the controller
     *      request_method  => Name of the Method request
    *       body            => return request result in the body
     *
     * @return object
     */
    public static function getRequestInfo() {
        /*
            param[0]: not used
            param[1]: virtual folder (xampp/htdocs/<appFolderName>)
            param[2]: folder of source code (src)
            param[3]: index.php: Main file, is the principal controller/dispatch
            param[4]: controller name that will manage the petition.
            param[5]: (optional) method that is requested, if empty the index method will be called
            param[6]: (optional) first parameter to be sent to the method, usually is an id number
        */
        $splitUri = explode('?', $_SERVER['REQUEST_URI']);
        $requestUlr = rtrim($splitUri[0], '/');
        $requestSections = (explode('/', $requestUlr));
        $requestParams = array();

        $controller = isset($requestSections[4]) ? $requestSections[4] : null;
        $method = 'index';

        if (isset($requestSections[5])) {
            if (!is_numeric($requestSections[5]) && $requestSections[5] !== '*') {
                $method = $requestSections[5];
            } else {
                for ($requestParamIndex = 5; $requestParamIndex < count($requestSections); $requestParamIndex++) {
                    $requestParams[] = $requestSections[$requestParamIndex];
                }
            }
        }

        $urlParams = array();
        if (isset($splitUri[1])) {
            $splitParams = explode('&', $splitUri[1]);
            for ($urlParamIndex = 0; $urlParamIndex < count($splitParams); $urlParamIndex++) {
                $urlParams[] = $splitParams[$urlParamIndex];
            }
        }

        $parseBody = new Stdclass();
        $bodyParams = new Stdclass();
        if ($_SERVER['REQUEST_METHOD'] === 'POST' || 'PUT') {
            $bodyRequest = explode('?', file_get_contents("php://input"));
            if (gettype($bodyRequest[0]) === "string" && !empty($bodyRequest[0])) {
                //Check if the body is a json or array formated
                if (
                    (substr($bodyRequest[0], 0, 1) === '[' && substr($bodyRequest[0], -1) === ']') ||
                    (substr($bodyRequest[0], 0, 1) === '{' && substr($bodyRequest[0], -1) === '}')
                ) {
                    //Chrome and Postman Format
                    $parseBody = json_decode($bodyRequest[0]);
                } else {
                    //Parse for PHP Unit
                    $parseBodyParans = explode('&', $bodyRequest[0]);
                    foreach ((array)$parseBodyParans as $value) {
                        $bodyParts = explode('=', $value);
                        if( isset($bodyParts[0]) ){
                            $parseBody->{$bodyParts[0]} = isset($bodyParts[1]) ? $bodyParts[1] : null;
                        }
                    }
                }
            } else {
                $parseBody = array($bodyRequest[0]);
            }

            foreach ((array)$parseBody as $name => $value) {
                //$keyValue = explode('=', $value);
                $bodyParams->{$name} = Utilities::sanitizeInput($value);
            }
        } else {
            $bodyRequest = json_decode(file_get_contents("php://input"));
            foreach ((array)$bodyRequest as $name => $value) {
                $bodyParams->{$name} = Utilities::sanitizeInput($value);
            }
        }

        $result = (object)array(
            'controller' => ucfirst($controller),
            'method' => $method,
            'requestParams' => $requestParams,
            'urlParams' => $urlParams,
            'request_method' => $_SERVER['REQUEST_METHOD'],
            'body' => $bodyParams
        );
        return $result;
    }

}