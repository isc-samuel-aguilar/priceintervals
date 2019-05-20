<?php

/**
 * Class Interval_mdl the business rules are declared in this model
 * and the db changes are applied in the Interval object methods called here
 */
class Interval_mdl {

    //Dependency Injection
    protected $interval;
    protected $utilities;

    /**
     * Interval_mdl constructor.
     * @param Utilities $utilities Instance of utilities
     * @param Interval $interval Instance of Interval
     */
    function __construct(Utilities $utilities, Interval $interval ){

        $this->interval = $interval;
        $this->utilities = $utilities;
    }

    /**
     * GET the list of the actual paramas in the database that will require a change based on the new interval like, delete or merge and apply the changes
     * @param Interval $interval
     * @param $dbAction Update | Insert if is an update, the interval have to exist
     * @return bool
     * @throws Exception
     */
    private function modifyAffectedIntervals(Interval $interval, $dbAction){
        $result = true;
        $dbAction = strtoupper($dbAction);
        
        $relatedIntervals = $interval->getRelatedIntervals();
        // Check if the provided id already exists with the same query (to reduce queries)
        if ($dbAction === 'UPDATE'){
            if(  !isset($relatedIntervals[$interval->id]) ){
                //If is an update and the id doesn't exist in the database, it's an error
                return false;
            } else {
                unset($relatedIntervals[$interval->id]); //Remove to loop only in the intervals that require action
            }    
        } 

        //if the request of existing intervals that will require modification executes correctly, false = error, array = executed correctly
        if ($relatedIntervals !== false){
            // Loop in the intervals that require modifcations and modify in the DB
            foreach ((array)$relatedIntervals as $dbIntervalId => $dbInterval){
    
                //$debugDates is used only help o to check short dates with format.
                $debugDates = array(
                    'start'=>$interval->__get('date_start')->format('Y-m-d'),
                    'end'=>$interval->__get('date_end')->format('Y-m-d'),
                    'dbStart'=>$dbInterval->__get('date_start')->format('Y-m-d'),
                    'dbEnd'=>$dbInterval->__get('date_end')->format('Y-m-d')
                );


                // Create a copy before changes the dates to help to compare the requested object,
                // with the intervals that end or start with the same date of the requested change
                $dayBeforeDbInterval = clone($dbInterval->__get('date_start'));
                /** @noinspection PhpUndefinedMethodInspection */
                $dayBeforeDbInterval->sub(new DateInterval('P1D'));
                $dayAfterDbInterval = clone($dbInterval->__get('date_end'));
                $dayAfterDbInterval->add(new DateInterval('P1D'));
    
    
                //Check if the db interbal is between the new interval, it will be deleted and added the newone
                if($interval->__get('date_start') <=  $dbInterval->__get('date_start') ){
                    //CASE newStart<=: newStartDate <= dbStartDate
                    if ($dbInterval->__get('date_end') <= $interval->__get('date_end')) {
                        //CASE newStart<= + (NDE>=: newEndTate >= dbStartDate)        
                        $updateResult = $dbInterval->delete();
                        $result = $updateResult && $result;                     
                    } elseif($dbInterval->__get('date_end') > $interval->__get('date_end')) {
                        if ($interval->__get('price') === $dbInterval->__get('price')){
                            $interval->__set('date_end', clone($dbInterval->__get('date_end'))) ;
                            $updateResult = $dbInterval->delete();
                            $result = $updateResult && $result; 
                        } else {
                            $dbInterval->__set('date_start', clone($interval->__get('date_end')) );
                            $dbInterval->__get('date_start')->add( new DateInterval('P1D') );
                            $updateResult = $dbInterval->update();
                            $result = $updateResult && $result;    
                        }
    
                    } else {
                        die('Case not contempled');
                    }
                }
                elseif($interval->__get('date_end') >= $dbInterval->__get('date_end') ) {
                    //newStart >= + (newEnd >= dbEnd)
                    if($interval->__get('price') === $dbInterval->__get('price')){
                        //Update dbInterval and
                        $interval->__set('date_start',$dbInterval->__get('date_start'));
                        $dbInterval->delete();
                    } else {
                        $dbInterval->__set('date_end', clone($interval->__get('date_start')) );
                        $dbInterval->__get('date_end')->sub(new DateInterval('P1D'));
                        $updateResult = $dbInterval->update();
                        $result = $updateResult && $result;
                    }
                }
                elseif( $interval->__get('date_end') <= $dbInterval->__get('date_end') ){
                    if($interval->__get('price') === $dbInterval->__get('price')){
    
                        
                        // Between db day Before
                        if ( $interval->__get('date_start') <= $dayBeforeDbInterval &&  
                            $dayBeforeDbInterval <= $interval->__get('date_start')
                        ){
                            $interval->__set('date_end', clone($dbInterval->__get('date_end')));
                        } else{
                            $interval->__set('date_end', clone($dbInterval->__get('date_end')) );
                        }
                        
                        //BETWEEN db day after
                        if ( $interval->__get('date_start') <= $dayAfterDbInterval &&  
                            $dayAfterDbInterval <= $interval->__get('date_start')
                        ){
                            $interval->__set('date_start', clone($dbInterval->__get('date_start')));
                        } else {
                            $interval->__set('date_start', clone($dbInterval->__get('date_start')));
                        }
    
                        $updateResult = $dbInterval->delete();
                        $result = $result && $updateResult;
                    } else {
                        //$originalDBInterval = clone($dbInterval);
                        $originalDBInterval = $dbInterval->copy();
                        $dbInterval->__set('date_end', clone($interval->__get('date_start')) );
                        $dbInterval->__get('date_end')->sub(new DateInterval('P1D'));
                        $updateResult = $dbInterval->update();
                        $result = $updateResult && $result;
    
                        if($interval->__get('date_end') < $originalDBInterval->__get('date_end') ){
                            $newStartDate = clone($interval->__get('date_end'));
                            $newStartDate = $newStartDate->add( new DateInterval('P1D') );

                            $extraInterval = $this->interval->newInstance();                            
                            $extraInterval->__set('date_start', $newStartDate);
                            $extraInterval->__set('date_end', $originalDBInterval->__get('date_end'));
                            $extraInterval->__set('price', $originalDBInterval->__get('price'));

                            $updateResult = $extraInterval->create();
                            $result = $result && ($updateResult !== false);
                            unset($newStartDate);
                        } 
                        else {
                            //TODO:remove this comment after ensure this case doesn't require action
                            error_log('Index->Check: Looks is not required to do something in this case');
                        }
                        unset($oldDbEndDate, $originalDBInterval);
                    }
                }
                else{
                    die('A): case not contempled?');
                }
            } //foreach $relatedIntervals     
        }     
    
        return $result;
    }

    /**
     * @param array $intervalId array of id(s) got get from the database
     * @return array|bool return array if execute correctly, return false if exist any error
     */
    public function getIntervalById($intervalId = array()){
        $result = array();         
                
        $intervalList = $this->interval->read($intervalId);
        if ($intervalList !== false){                            
            $result = array();
            foreach ($intervalList as $rowId => $row){
                $result[] = $row;
            }            
        } else {
            $result = false;
        }  
        return $result;
    }

    /**
     * @return bool|mysqli_result|null
     */
    public function deleteAllIntervals(){
        $deleteResult = null;
        try{
            return $this->deleteIntervalById('*');
        } catch (Exception $e){
            error_log("Error:" . $e->getMessage());
        }

    }

    /**
     * Delete an interval(s) in the database by id(s)
     * @param array $intervalId list of the ids to delete
     * @return bool|mysqli_result
     */
    public function deleteIntervalById($intervalId = array()){        
        $deleteResult = $this->interval->deleteById($intervalId);
        return $deleteResult;
    }

    /**
     * Get the required parameters to add a new interval in the DB and insert it
     * @param string $date_start string of date yyyy-mm-dd
     * @param string $date_end $date_start string of date yyyy-mm-dd
     * @param float $price
     * @return bool
     * @throws Exception
     */
    public function addInterval(string $date_start, $date_end, $price){
        $result = true;

        $this->interval->date_start = $date_start;
        $this->interval->date_end = $date_end;
        $this->interval->price = $price;
    
        $resultLog = array('method' => Controller::getRequestInfo()->request_method);
        $resultLog['modifyIntervals'] = $this->modifyAffectedIntervals($this->interval, 'insert');            
        if ($resultLog['modifyIntervals']){
            $createResult = $this->interval->create();
            $resultLog['createResult'] = $createResult;
            $resultLog['result'] = $createResult && $result;
            $result = $createResult && $result;
            $this->responseCode = ($result === true) ? 200 : $this->utilities->errorCodes[ Controller::getRequestInfo()->request_method ];
        } else {
            $this->responseCode = $this->utilities->errorCodes[ Controller::getRequestInfo()->request_method ];
            $result = false;
        }

        return $result;
    }

    /**
     * GET the interval by id and update with the new parameterss
     * @param int $id
     * @param string $date_start string of date yyyy-mm-dd
     * @param string $date_end $date_start string of date yyyy-mm-dd
     * @param float $price
     * @return bool
     * @throws Exception
     */
    public function updateInterval($id, $date_start, $date_end, $price){
        $result = true;
        $this->interval->id         = $id;
        $this->interval->date_start = $date_start;
        $this->interval->date_end   = $date_end;
        $this->interval->price      = $price;

        $relatedIntervals = $this->interval->getRelatedIntervals();
        if($relatedIntervals !== false){
            // Check if the provided id already exists with the same query (to reduce queries)
            if( !isset($relatedIntervals[$this->interval->id]) ){                
                $this->responseCode = $this->utilities->errorCodes[ Controller::getRequestInfo()->request_method ];
                $this->response = false;
                return false;
            } else {
                unset($relatedIntervals[$this->interval->id]); //Remove to loop only in the intervals that require update/delete
            }

            $resultLog['modifyIntervals'] = $this->modifyAffectedIntervals($this->interval, 'update');            
            if ($resultLog['modifyIntervals']){
                $updateResult = $this->interval->update();
                $resultLog['updateResult'] = $updateResult;
                $resultLog['result'] = $updateResult && $result;
                $result = $updateResult && $result;
                $this->responseCode = ($result === true) ? 200 : $this->utilities->errorCodes[ Controller::getRequestInfo()->request_method ];
            } else {
                $result = false;
            }
        } else {
            $this->responseCode = $this->utilities->errorCodes[ Controller::getRequestInfo()->request_method ];
            $result = false;
        }
        return $result;
            
    }

}