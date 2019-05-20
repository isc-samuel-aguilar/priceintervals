<?php

class Casesguide {
    private $db;
    
    function __construct(Db $db){
        $this->db = $db;
    }

    private function prepareDateDB(array $arrangeInfoStart, array $arrangeInfoEnd){
        $result = array();
        $result['delete'] = $this->db->query("DELETE FROM intervals;");
        $result[1] = $this->db->query("
                INSERT INTO intervals(id, date_start, date_end, price) 
                VALUES (1,'2019-02-{$arrangeInfoStart['start_day']}','2019-02-{$arrangeInfoStart['end_day']}',{$arrangeInfoStart['price']});
        ");
        $result[2] = $this->db->query("
                INSERT INTO intervals(id, date_start, date_end, price) 
                VALUES (2,'2019-02-{$arrangeInfoEnd['start_day']}','2019-02-{$arrangeInfoEnd['end_day']}',{$arrangeInfoEnd['price']});
        ");        
        return $result;
    }

    public function setDBCase($caseName){
        $result = array();
        switch(strtoupper(trim($caseName))){
            case 'CASE.A':
                $result = $this->prepareDateDB(
                    array('start_day'=>1,'end_day'=>10,'price'=>10), 
                    array('start_day'=>27,'end_day'=>28,'price'=>30) 
                );
            break;

            case 'CASE.B':
                $result = $this->prepareDateDB(
                    array('start_day'=>16,'end_day'=>20,'price'=>10), 
                    array('start_day'=>27,'end_day'=>28,'price'=>30) 
                );
            break;
            case 'CASE.C':
                $result = $this->prepareDateDB(
                    array('start_day'=>16,'end_day'=>25,'price'=>10), 
                    array('start_day'=>27,'end_day'=>28,'price'=>30) 
                );                
            break;
            case 'CASE.D':
                $result = $this->prepareDateDB(
                    array('start_day'=>5,'end_day'=>12,'price'=>10), 
                    array('start_day'=>27,'end_day'=>28,'price'=>30) 
                );                
            break;            
            case 'CASE.E':
                $result = $this->prepareDateDB(
                    array('start_day'=>10,'end_day'=>15,'price'=>10), 
                    array('start_day'=>27,'end_day'=>28,'price'=>30) 
                );                
            break;       
            case 'CASE.F':
                $result = $this->prepareDateDB(
                    array('start_day'=>3,'end_day'=>22,'price'=>10), 
                    array('start_day'=>27,'end_day'=>28,'price'=>30) 
                );                
            break;                           
            case 'CASE.G':
                $result = $this->prepareDateDB(
                    array('start_day'=>3,'end_day'=>20,'price'=>10), 
                    array('start_day'=>27,'end_day'=>28,'price'=>30) 
                );                
            break;                           
            case 'CASE.H':
                $result = $this->prepareDateDB(
                    array('start_day'=>5,'end_day'=>25,'price'=>10), 
                    array('start_day'=>27,'end_day'=>28,'price'=>30) 
                );                
            break;                           


        }        

        return $this->imrpoveResponse($caseName, $result);

    }

    private function imrpoveResponse($caseName, $result){        
        $intervals = Db::queryResultToArray( $this->db->getAllIntervals() );
        return array('case'=> strtoupper($caseName), 'intervals'=>$intervals, 'result'=> $result );
    }
    
}

