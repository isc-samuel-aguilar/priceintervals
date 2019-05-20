<?php

class ParentTestClass extends PHPUnit_Framework_TestCase{
    //Main Url of the app
    protected $appUrl = "http://localhost/priceintervals/src/index.php/";

    protected $responseList;

    function __constructor(){
        parent::__constructor();
    }

    protected function requestToArrayWithoutId($response, $includeId = false){
        $result = array();
        if (isset($response->info)) unset($response->info);
        $requestToArray = (gettype($response->body) === 'string') ? json_decode($response->body) : $response->body;
        foreach((array)$requestToArray as $id => $interval){
            if ($includeId === true){                                
                $copyInterval = array(
                    'id'         => (int)$interval->id,
                    'date_start' => $interval->date_start,
                    'date_end'  => $interval->date_end,
                    'price'     => (float)$interval->price
                );
            } else {
                $copyInterval = array(
                    'date_start' => $interval->date_start,
                    'date_end'  => $interval->date_end,
                    'price'     => (float)$interval->price
                );
            }
            $result[] = $copyInterval;

        }
        return $result;
    }

    /**
     * @param bool $validateCode
     * @return Httprequester
     */
    protected function deleteAllRows($validateCode = false){
        $request = Httprequester::HTTPDelete($this->appUrl.'priceinterval/*');        
        $this->responseList[] = array('deleteAllRows' => $request->body);
        if($validateCode) $this->assertEquals(200,  $request->http_code );
        return $request;
    }

    protected function getAll($validateCode = false){
        $request = Httprequester::HTTPGet($this->appUrl.'priceinterval');
        if($validateCode) $this->assertEquals(200,  $request->http_code );
        return $request;        
    }
    
    protected function getByd($dbId, $validateCode = false){
        $request = Httprequester::HTTPGet($this->appUrl."priceinterval/$dbId");
        if($validateCode) $this->assertEquals(200,  $request->http_code );
        return $request;        
    }  
        
    
    protected function setOneInerval($interval, $validateCode = false ){
        $request = Httprequester::HTTPPost($this->appUrl.'priceinterval', $interval);       
        $this->responseList[] = array('setOneInerval' => $request->body);
        if ($validateCode) $this->assertEquals(200,  $request->http_code );      
        return $request;  
    }

    /**
     * Insert multiple intervals and compare with the expected result
     *
     * @param array $postList array of the expected intervals 
     * @param array $expected array with the expected result
     * @param bool $validateCode Indicate if the POSTS should be tested
     * @param string $testCase indicate the name of the name of the case, it's only to help to use conditional breakpoints
     * @return void
     */
    protected function postMultiple( $postList, $expected = array(), $validateCode = false, $testCase = "" ){
        $this->responseList['POST'][] = $this->deleteAll(false);        
        $responseListAsArrays = array();

        for ($i = 0; $i < count($postList); $i++){
            $this->responseList['POST'][] =  $response = $this->setOneInerval($postList[ $i ], $validateCode);
            if (substr($response->body,0,1) !== '[') {
                echo "\n *** Post $i ***\n Header \n {$response->header}{$response->header}\n";
            }
            $responseListAsArrays[] = $this->requestToArrayWithoutId($response);
        }
        unset($i);
        
        if ($validateCode !== false){
            //Get all the intervals in the database
            $this->responseList['POST'][] = $allIntervalsInDb = $this->getAll(false);    
            $allIntervalsInDbAsArray = $this->requestToArrayWithoutId($allIntervalsInDb);
            $this->assertEquals( count($expected)  ,  count( $allIntervalsInDbAsArray ));        
            $this->assertEquals( $expected,  $allIntervalsInDbAsArray);        
        }
    }

    //Delete rows
    function deleteAll($validateCode = false){       
        if ($validateCode !== false) echo "\n Case: deleteAll \n";    
        $deleteAllRequest = $this->deleteAllRows($validateCode);
        $dbIntervalsArray = $this->requestToArrayWithoutId($deleteAllRequest);
        $rowsAfterPost = count( $dbIntervalsArray );
        $result = $this->assertEquals(0,  $rowsAfterPost );        
        return $rowsAfterPost  === count( $dbIntervalsArray );
    }     

    protected function updateOneInerval( $interval, $validateCode = false ){
        $request = Httprequester::HTTPPut($this->appUrl."priceinterval", $interval);       
        $this->responseList['PUT'][] = array('setOneInerval' => $request->body);
        if ($validateCode) $this->assertEquals(200,  $request->http_code );      
        return $request;  
    }    
        
}