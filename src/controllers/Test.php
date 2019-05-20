<?php

class Test extends Controller{

    protected $casesGuide;
    //private $testCases;
    protected $runMode = '';
    protected $case = "case.f";

    function __construct(Db $db, Utilities $utilities, CasesGuide $casesGuide,  $params = ""){
        
        parent::__construct($db, $utilities);
        //$this->testCases = new Testcases($this->db) ;
        $this->casesGuide = $casesGuide;

        $params = isset($_SERVER['REQUEST_URI']) ? explode('/', $_SERVER['REQUEST_URI']) : array();
        if (isset($params[6])) {
            $this->case = $params[6];
        }     
        
    }

    public function index(){

    }

    public function prepare($caseName = "", $writeRespose = true){
        if (!isset($this->case) && empty($caseName)) return false;
        $case = empty($caseName) ? $this->case : $caseName;
        
        switch ($_SERVER['REQUEST_METHOD']){
            //Creae
            case 'GET': 
            case 'PUT': 
            case 'POST':
            case 'DELETE':  
                $result = $this->casesGuide->setDBCase($case);
            break;
        }     

        if ($writeRespose === true){
            $this->responseCode =  ($result) ? 200 : 400;
            $this->response = $result;
        }
    
        return $result;   
    }
}