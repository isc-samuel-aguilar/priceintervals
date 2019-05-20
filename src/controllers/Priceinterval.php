<?php

/**
 * Controller to manage the requests of Itenval Object
 */

class Priceinterval extends Controller{

    /**
     * @var Interval_mdl used for Dependency Injection
     */
    protected $interval_mdl;
    /**
     * @var Interval used for Dependency Injection
     */
    protected $interval;


    /**
     * Priceinterval constructor.
     * @param Db $db used to sent to the parent controller
     * @param Utilities $utilities used to sent to the parent controller
     * @param Interval_mdl $interval_mdl
     * @param Interval $interval
     */
    function __construct(Db $db, Utilities $utilities, Interval_mdl $interval_mdl, Interval $interval  ){
        parent::__construct($db, $utilities);        
        $this->interval = $interval; 
        $this->interval_mdl = $interval_mdl; 
    }

    /**
     * Act as router controller based on the request_method
     * @return array|bool
     * @throws Exception
     */
    public function index(){
        //CRUD
        $result = true; //return tru if all ok or false if any error
        $resultLog = array();
        $request_method = $this->requestInfo->request_method;
        
        switch ($request_method){
            //CREATE a NEW object
            case 'POST': 
                if($this->requestInfo->method === 'index' || empty($this->requesInfo->method) === ''){
                    $result = $this->interval_mdl->addInterval(
                        $this->requestInfo->body->date_start,
                        $this->requestInfo->body->date_end,
                        (float)$this->requestInfo->body->price
                    );                                  
                } else {
                    $result = false;
                }                
                $this->responseCode = ($result !== false) ? 200 : $this->utilities->errorCodes[ $request_method ];                            

            break;

            //READ
            case 'GET':     
                if($this->requestInfo->method === 'index' || empty($this->requesInfo->method) === ''){
                    $id = isset($this->requestInfo->requestParams[0]) ? $this->requestInfo->requestParams[0] : array();
                    $result = $this->interval_mdl->getIntervalById($id);
                } else {
                    $result = false;
                }                
                $this->responseCode = ($result !== false) ? 200 : $this->utilities->errorCodes[ $request_method ];
            break;        

            //UPDATE completly an object
            case 'PUT': 
                if($this->requestInfo->method === 'index' || $this->requestInfo->method === ''){
                    $result = $this->interval_mdl->updateInterval(
                        $this->requestInfo->body->id,
                        $this->requestInfo->body->date_start,
                        $this->requestInfo->body->date_end,
                        (float)$this->requestInfo->body->price
                    );          
                } else {
                    $result = false;
                }                
                $this->responseCode = ($result !== false) ? 200 : $this->utilities->errorCodes[ $request_method ];

            break;
    
            //DELETE
            case 'DELETE':
                if($this->requestInfo->method === 'index' || empty($this->requesInfo->method) === ''){
                    $id = isset($this->requestInfo->requestParams[0]) ? $this->requestInfo->requestParams[0] : array();                    
                    $result = (empty($id)) ? $this->interval_mdl->deleteIntervalById($id) : $this->interval_mdl->deleteAllIntervals();                
                } else {
                    $result = false;
                }                
                $this->responseCode = ($result !== false) ? 200 : $this->utilities->errorCodes[ $request_method ];                            
                
            break;
        }        
        
        //Assing the result to the Controler->response, the Controller->printResponse will use to print the response
        $this->response = $result; 
        if ($this->responseCode === null) $this->responseCode = ($result === true) ? 200 : $this->utilities->errorCodes[ $request_method ];
        return $result;
    }

}