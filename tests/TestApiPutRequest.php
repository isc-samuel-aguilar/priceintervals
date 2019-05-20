<?php

class TestApiPostRequest extends ParentTestClass{

    function __construct(){
        parent::__construct();
        $this->responseList = array();
    }  

    private function getIntervalByIdFormated($id){
        $searchedIntervalRequest = $this->getByd(1, true);
        $searchedInterval = $this->requestToArrayWithoutId($searchedIntervalRequest, true);       

        return (isset($searchedInterval[0])) ? (array)( $searchedInterval[0]) : $searchedInterval;    }

    /* *************** Test Cases ************************************** */

    function testDeleteAll(){
        $this->deleteAll(true);
    }
    
    function testPutCase_1(){   
        echo "\n Case: testPutCase_1 \n";   
        //Initial interval with id = 1, this will be updated
        $firstIntervalSent = array('date_start'=> '2019-01-27', 'date_end' => '2019-01-28', 'price' => 10);
        $idToUpdate = 1;
        $this->deleteAll();
        $this->postMultiple(array( $firstIntervalSent, array('date_start'=> '2019-01-01', 'date_end' => '2019-01-10', 'price' => 10) ) );
        //Value(s) that should exist to produce a merge if apply
        $intervalNewValues = array('id' => $idToUpdate , 'date_start'=> '2019-01-20', 'date_end' => '2019-01-25', 'price' => 10);
        $this->updateOneInerval($intervalNewValues, true);
        $getUpatedInterval = $this->getIntervalByIdFormated($idToUpdate);
        $this->assertEquals(array('id'=>$idToUpdate, 'date_start'=>'2019-01-20','date_end'=>'2019-01-25','price'=>10), $getUpatedInterval ); 
    }   

    function testPutCase_2(){   
        echo "\n Case: testPutCase_2 \n";   
        //Initial interval with id = 1, this will be updated
        $firstIntervalSent = array('date_start'=> '2019-01-27', 'date_end' => '2019-01-28', 'price' => 10);
        $idToUpdate = 1;
        $this->deleteAll();
        $this->postMultiple(array( $firstIntervalSent, array('date_start'=> '2019-01-01', 'date_end' => '2019-01-10', 'price' => 10) ) );
        //Value(s) that should exist to produce a merge if apply
        $intervalNewValues = array('id' => $idToUpdate , 'date_start'=> '2019-01-05', 'date_end' => '2019-01-20', 'price' => 15);
        $this->updateOneInerval($intervalNewValues, true);
        $getUpatedInterval = $this->getIntervalByIdFormated($idToUpdate);
        $this->assertEquals(array('id'=>$idToUpdate, 'date_start'=>'2019-01-05','date_end'=>'2019-01-20','price'=>15), $getUpatedInterval ); 
    }      
    
    function testPutCase_3(){   
        echo "\n Case: testPutCase_3 \n";   
        //Initial interval with id = 1, this will be updated
        $firstIntervalSent = array('date_start'=> '2019-01-27', 'date_end' => '2019-01-28', 'price' => 10);
        $idToUpdate = 1;
        $this->deleteAll();
        $this->postMultiple(array( $firstIntervalSent, array('date_start'=> '2019-01-01', 'date_end' => '2019-01-10', 'price' => 10) ) );
        //Value(s) that should exist to produce a merge if apply
        $intervalNewValues = array('id' => $idToUpdate , 'date_start'=> '2019-01-05', 'date_end' => '2019-01-20', 'price' => 10);
        $this->updateOneInerval($intervalNewValues, true);
        $getUpatedInterval = $this->getIntervalByIdFormated($idToUpdate);
        $this->assertEquals(array('id'=>$idToUpdate, 'date_start'=>'2019-01-01','date_end'=>'2019-01-20','price'=>10), $getUpatedInterval ); 
    }       

    function testPutCase_4(){   
        echo "\n Case: testPutCase_4 \n";   
        //Initial interval with id = 1, this will be updated
        $firstIntervalSent = array('date_start'=> '2019-01-05', 'date_end' => '2019-01-12', 'price' => 10);
        $idToUpdate = 1;
        $this->deleteAll();
        $this->postMultiple(array( $firstIntervalSent, array('date_start'=> '2019-01-16', 'date_end' => '2019-01-25', 'price' => 10) ) );
        //Value(s) that should exist to produce a merge if apply
        $intervalNewValues = array('id' => $idToUpdate , 'date_start'=> '2019-01-05', 'date_end' => '2019-01-20', 'price' => 10);
        $this->updateOneInerval($intervalNewValues, true);
        $getUpatedInterval = $this->getIntervalByIdFormated($idToUpdate);
        $this->assertEquals(array('id'=>$idToUpdate, 'date_start'=>'2019-01-05','date_end'=>'2019-01-25','price'=>10), $getUpatedInterval ); 
    }       

    function testPutCase_5(){   
        echo "\n Case: testPutCase_5 \n";   
        //Initial interval with id = 1, this will be updated
        $firstIntervalSent = array('date_start'=> '2019-01-05', 'date_end' => '2019-01-12', 'price' => 10);
        $idToUpdate = 1;
        $this->deleteAll();
        $this->postMultiple(array( $firstIntervalSent, array('date_start'=> '2019-01-16', 'date_end' => '2019-01-25', 'price' => 10) ) );
        //Value(s) that should exist to produce a merge if apply
        $intervalNewValues = array('id' => $idToUpdate , 'date_start'=> '2019-01-05', 'date_end' => '2019-01-20', 'price' => 15);
        $this->updateOneInerval($intervalNewValues, true);
        $getUpatedInterval = $this->getIntervalByIdFormated($idToUpdate);
        $this->assertEquals(array('id'=>$idToUpdate, 'date_start'=>'2019-01-05','date_end'=>'2019-01-20','price'=>15), $getUpatedInterval ); 
        
        $this->responseList['PUT'][] = $allIntervalsInDb = $this->getAll(false);    
        $allIntervalsInDbAsArray = $this->requestToArrayWithoutId($allIntervalsInDb);        
        $this->assertEquals( 
        array(
            array('date_start'=> '2019-01-05', 'date_end' => '2019-01-20', 'price' => 15),
            array('date_start'=> '2019-01-21', 'date_end' => '2019-01-25', 'price' => 10)
        ),  
            $allIntervalsInDbAsArray
        ); 
    }    

    function testPutCase_6(){   
        echo "\n Case: testPutCase_6 \n";   
        //Initial interval with id = 1, this will be updated
        $firstIntervalSent = array('date_start'=> '2019-01-27', 'date_end' => '2019-01-28', 'price' => 15);
        $idToUpdate = 1;
        $this->deleteAll();
        $this->postMultiple(array( $firstIntervalSent, array('date_start'=> '2019-01-16', 'date_end' => '2019-01-25', 'price' => 10),
            array('date_start'=> '2019-01-05', 'date_end' => '2019-01-12', 'price' => 10)
         ) );
        //Value(s) that should exist to produce a merge if apply
        $intervalNewValues = array('id' => $idToUpdate , 'date_start'=> '2019-01-05', 'date_end' => '2019-01-20', 'price' => 15);
        $this->updateOneInerval($intervalNewValues, true);
        $getUpatedInterval = $this->getIntervalByIdFormated($idToUpdate);
        $this->assertEquals(array('id'=>$idToUpdate, 'date_start'=>'2019-01-05','date_end'=>'2019-01-20','price'=>15), $getUpatedInterval ); 
        
        $this->responseList['PUT'][] = $allIntervalsInDb = $this->getAll(false);    
        $allIntervalsInDbAsArray = $this->requestToArrayWithoutId($allIntervalsInDb);        
        $this->assertEquals( 
        array(
            array('date_start'=> '2019-01-05', 'date_end' => '2019-01-20', 'price' => 15),
            array('date_start'=> '2019-01-21', 'date_end' => '2019-01-25', 'price' => 10)
        ),  
            $allIntervalsInDbAsArray
        ); 
    }         


}

