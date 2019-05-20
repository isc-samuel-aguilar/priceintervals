<?php

class TestApiPostRequest extends ParentTestClass{

    function __construct(){
        parent::__construct();
        $this->responseList = array();
    }

    /* *************** Test Cases ************************************** */

    function testDeleteAll(){
        $this->deleteAll(true);
    }
    
    function testPostsCase_1(){   
        echo "\n Case: testPostsCase_1 \n";   
        $firstIntervalSent = array('date_start'=> '2019-01-01', 'date_end' => '2019-01-05', 'price' => 10);
        $this->postMultiple(
            array( 
                $firstIntervalSent,
                array('date_start'=> '2019-01-10', 'date_end' => '2019-01-15', 'price' => 10),                
                array('date_start'=> '2019-01-20', 'date_end' => '2019-01-25', 'price' => 10),
            ), 
            array(),
            false     
        );

        $firstIntervalRequest = $this->getByd(1, true);
        $auxBreak = "break here";
        $searchedInterval = $this->requestToArrayWithoutId($firstIntervalRequest);
        if (isset($searchedInterval[0])){
            $this->assertEquals($firstIntervalSent,  (array)( $searchedInterval[0]) ); 
        }
    }    
    
    
    function testPostsCase_2(){   
        echo "\n Case: testPostsCase_2 \n";   
        $this->postMultiple(
            array( 
                array('date_start'=> '2019-01-01', 'date_end' => '2019-01-10', 'price' => 10),                
                array('date_start'=> '2019-01-05', 'date_end' => '2019-01-20', 'price' => 15)
            ),
            array( 
                array('date_start'=> '2019-01-01', 'date_end' => '2019-01-04', 'price' => 10),
                array('date_start'=> '2019-01-05', 'date_end' => '2019-01-20', 'price' => 15)
            )                 
        );
    }

    function testPostsCase_3(){   
        echo "\n Case: testPostsCase_3 \n";   
        $this->postMultiple(
            array( 
                array('date_start'=> '2019-01-16', 'date_end' => '2019-01-20', 'price' => 10),
                array('date_start'=> '2019-01-01', 'date_end' => '2019-01-10', 'price' => 10),
                array('date_start'=> '2019-01-05', 'date_end' => '2019-01-20', 'price' => 15)
            ),
            array( 
                array('date_start'=> '2019-01-01', 'date_end' => '2019-01-04', 'price' => 10),
                array('date_start'=> '2019-01-05', 'date_end' => '2019-01-20', 'price' => 15)
            )                 
        );
    }    

    function testPostsCase_4(){   
        echo "\n Case: testPostsCase_4 \n";   
        $this->postMultiple(
            array( 
                array('date_start'=> '2019-01-01', 'date_end' => '2019-01-10', 'price' => 10),
                array('date_start'=> '2019-01-16', 'date_end' => '2019-01-25', 'price' => 10),
                array('date_start'=> '2019-01-05', 'date_end' => '2019-01-20', 'price' => 15)
            ),
            array( 
                array('date_start'=> '2019-01-01', 'date_end' => '2019-01-04', 'price' => 10),
                array('date_start'=> '2019-01-05', 'date_end' => '2019-01-20', 'price' => 15),
                array('date_start'=> '2019-01-21', 'date_end' => '2019-01-25', 'price' => 10),
            ),
            'case_4'
        );
    }   
    
    function testPostsCase_5(){   
        echo "\n Case: testPostsCase_5 \n";   
        $this->postMultiple(
            array( 
                array('date_start'=> '2019-01-05', 'date_end' => '2019-01-12', 'price' => 10),
                array('date_start'=> '2019-01-16', 'date_end' => '2019-01-25', 'price' => 10),
                array('date_start'=> '2019-01-05', 'date_end' => '2019-01-20', 'price' => 15)
            ),
            array(                 
                array('date_start'=> '2019-01-05', 'date_end' => '2019-01-20', 'price' => 15),
                array('date_start'=> '2019-01-21', 'date_end' => '2019-01-25', 'price' => 10),
            )
        );
    }    

    function testPostsCase_6(){   
        echo "\n Case: testPostsCase_6 \n";   
        $this->postMultiple(
            array( 
                array('date_start'=> '2019-01-10', 'date_end' => '2019-01-15', 'price' => 10),
                array('date_start'=> '2019-01-05', 'date_end' => '2019-01-20', 'price' => 15)
            ),
            array(                 
                array('date_start'=> '2019-01-05', 'date_end' => '2019-01-20', 'price' => 15)
            )
        );
    }

    function testPostsCase_7(){
        echo "\n Case: testPostsCase_7 \n";   
        $this->postMultiple(
            array( 
                array('date_start'=> '2019-01-03', 'date_end' => '2019-01-22', 'price' => 10),
                array('date_start'=> '2019-01-05', 'date_end' => '2019-01-20', 'price' => 15)
            ),
            array(                 
                array('date_start'=> '2019-01-03', 'date_end' => '2019-01-04', 'price' => 10),
                array('date_start'=> '2019-01-05', 'date_end' => '2019-01-20', 'price' => 15),
                array('date_start'=> '2019-01-21', 'date_end' => '2019-01-22', 'price' => 10)
            )
        );
    }

    function testPostsCase_8(){   
        echo "\n Case: testPostsCase_8 \n";   
        $this->postMultiple(
            array( 
                array('date_start'=> '2019-01-03', 'date_end' => '2019-01-20', 'price' => 10),                
                array('date_start'=> '2019-01-05', 'date_end' => '2019-01-20', 'price' => 15)
            ),
            array(                 
                array('date_start'=> '2019-01-03', 'date_end' => '2019-01-04', 'price' => 10),
                array('date_start'=> '2019-01-05', 'date_end' => '2019-01-20', 'price' => 15)
            )
        );
    }      

    function testPostsCase_9(){   
        echo "\n Case: testPostsCase_9 \n";   
        $this->postMultiple(
            array( 
                array('date_start'=> '2019-01-03', 'date_end' => '2019-01-20', 'price' => 10),                
                array('date_start'=> '2019-01-21', 'date_end' => '2019-01-25', 'price' => 15),       
                array('date_start'=> '2019-01-05', 'date_end' => '2019-01-20', 'price' => 15)
            ),
            array(                 
                array('date_start'=> '2019-01-03', 'date_end' => '2019-01-04', 'price' => 10),
                array('date_start'=> '2019-01-05', 'date_end' => '2019-01-25', 'price' => 15)
            )
        );
    }            



}

