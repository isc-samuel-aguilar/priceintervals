<?php
class Interval {
 
    // database connection and table name
    public $db;
    private $table_name = "intervals";
 
    // object properties
    public $id; //auto_inc
    private $date_start; //date
    private $date_end; //date
    public $price; //float


    /**
     * Interval constructor.
     * @param Db db Instance of Db
     * @param int $id
     * @param string | DateTime $date_start
     * @param string | DateTime $date_end
     * @param float $price
     * @throws Exception
     */
    public function __construct(Db $db, $id = 0, $date_start = '',  $date_end = '', float $price = 0  ){
        $this->db = $db;
        $this->id = $id;
        $this->__set('date_start', $date_start);
        $this->__set('date_end', $date_end);
        $this->__set('price', $price);
        $this->__set('price', $price);
        $this->price = $price;
    }

    /**
     * @param string $name
     * @param $value
     * @return null
     * @throws Exception
     */
    public function __set(string $name, $value){
        $dateValue = $value;

        if ($name === 'date_start' || $name === 'date_end') {
            if (is_a($value, 'DateTime')){
                $dateValue = $value;
            } elseif(gettype($value) ==='string'){
                $dateValue = new DateTime($value);
            }
        }
        if (
            $value !== null &&
            ( $name === 'date_end' && $this->date_start !== null && $this->date_start > $dateValue ) || 
            ( $name === 'date_start' && $this->date_end !== null && $this->date_end < $dateValue )
        ){
            throw new Exception ("Start date can not be higher than end date");
        } 

        if($name === 'price' && $value !== null && $value < 0){
            throw new Exception ("Price can not be lower that 0");
        }
        
        if(property_exists($this, $name)){
            
            if($name ==='date_start' || $name === 'date_end'){
                $this->$name = $dateValue;
            }else {
                $this->$name = $value;
            }
            
            //$this->$name = $value;
            return $value;
        }
        return null;
    }

    /**
     * @param string $name Name of the property
     * @return float|mixed
     */
    function __get(string $name) {
        $result = isset($this->$name) ? $this->$name : null;
        if ($name === 'price'){
            $result = (float)$result;
        } 
        return $result;
    }

    /**
     * @param array|int|string array of ids of intervals or single id of interval or '*' for all
     * @return array|bool
     */
    public function read($ids = array()){
        $result = array();
        $queryFilter = (empty($ids) || $ids ==='*'  ) ?  "" : " WHERE {$this->table_name}.id IN (". implode(',',(array) $ids) .") ";
        $strQuery = "
          SELECT * 
          FROM {$this->table_name} $queryFilter 
          ORDER BY date_start, date_end
        ";
        $queryResult = $this->db->query($strQuery);
        $result = (!$queryResult) ? false : Db::queryResultToArray($queryResult);
        //return an asociative array to improve performance when try to search by id
        return $result ;        
    }

    /**
     * Used to create the new interval based on the values of the object values, date_start, date_end, price can't be empty
     * @return bool
     */
    public function create(){
        if ($this->__get('date_start') === null || $this->__get('date_end') === null || $this->price === null){
            return false;
        }

        $date_start = $this->__get('date_start')->format('Y-m-d');
        $date_end = $this->__get('date_end')->format('Y-m-d');
        $price = $this->price;

        $strQuery = " 
            INSERT INTO {$this->table_name}(date_start, date_end, price) 
            VALUES ('{$date_start}', '$date_end', $price)
        ";
        
        $queryResult = $this->db->query($strQuery); //Assigned to debug
        if ($queryResult !== false){
            $strQuery = /** @lang text */
                "
                SELECT MAX(id) AS last_id
                FROM {$this->table_name} 
                WHERE 
                    date_start = CAST('{$date_start}' AS DATE) AND
                    date_end = CAST('{$date_end}' AS DATE) AND
                    price = {$price}
            ";

            $queryResult = $this->db->query($strQuery);
            $lastInterval = Db::queryResultToArray( $queryResult, 'last_id' );
            $result = isset(array_keys($lastInterval)[0]) ? array_keys($lastInterval)[0] : false;
        } else {
            $result = false;
        }

        return $result;

    }

    /**
     * Delete the interval by id, the id had to be assigned before
     * @return bool|mysqli_result
     */
    public function delete(){
        $id = (!empty($this->id)) ? $this->id : 0;
        return $this->deleteById($id);
    }


    /**
     * Delete a single inter by id or all the intervals if '*'  as string has been passed
     * @param $id|string
     * @return bool|mysqli_result
     */
    public function deleteById($id){
        $id = (!empty($id)) ? $id : 0;
        $queryFilter = ($id === '*') ? "": " WHERE id = $id"  ; 
        $strQuery = "DELETE FROM {$this->table_name} {$queryFilter}";
        $queryResult = $this->db->query($strQuery);
        $result = $queryResult;
        
        if ($queryResult === true && $this->db->conn->affected_rows === 0){
            $allIntervals = $this->read('*');
            if (count($allIntervals) > 0) $queryResult = false;
        }

        if ($id === '*'){
            $strQuery = "ALTER TABLE {$this->table_name} AUTO_INCREMENT = 1;";
            $queryResult = $this->db->query($strQuery);
        }


        return $queryResult;
    }

    /**
     * Update the interval by this->id property previously assigned
     * @return bool return true if interval has been updated or false if not
     */
    public function update(){
        // update query
        $strQuery = "
            UPDATE {$this->table_name} 
            SET
                date_start = CAST(? AS DATE) ,
                date_end = CAST(? AS DATE),
                price = ?
            WHERE
                    id = ?
        ";
    
        // prepare query statement
        $stmt = $this->db->conn->prepare($strQuery);     
    
        // bind new values
        $stmt->bind_param('ssdi',$date_start, $date_end, $price, $id);
        $date_start = $this->__get('date_start')->format('Y-m-d');
        $date_end = $this->__get('date_end')->format('Y-m-d');
        $price = $this->price;
        $id = empty($this->id) ? 0 : $this->id;
    
        // execute the query
        try{
            if($stmt->execute()){
                return true;
            }
    
        } catch (Exception $e){
            error_log('Update Error');
        }
    
        return false;        
    }

    /**
     * GET the list of the intervals that require a modification if a interval is inserted or updated
     * @return array|bool
     * @throws Exception
     */
    public function getRelatedIntervals(){
        $result = array(); //Will contain the return value
        $queryId = (int)$this->id ;
        $price = $this->__get('price');
        $cast_date_start =  $this->__get('date_start')->format('Y-m-d');
        $cast_date_end =  $this->__get('date_end')->format('Y-m-d');
        
        $strQuery = "
            SELECT * 
            FROM $this->table_name 
            WHERE 
            (
                CAST('$cast_date_start' AS DATE) BETWEEN date_start AND date_end
                OR CAST('$cast_date_end' AS DATE) BETWEEN date_start AND date_end
            ) 
            OR (
                date_start BETWEEN CAST('$cast_date_start' AS DATE) AND CAST('$cast_date_end' AS DATE)
                OR date_end BETWEEN CAST('$cast_date_start' AS DATE) AND CAST('$cast_date_end' AS DATE)
            )

            OR (
                CAST($price AS DECIMAL(15,2)) = price AND	(
                    (date_end   + INTERVAL '1' DAY) BETWEEN CAST('$cast_date_start' AS DATE) AND CAST('$cast_date_end' AS DATE) OR 
                    (date_start - INTERVAL '1' DAY) BETWEEN CAST('$cast_date_start' AS DATE) AND CAST('$cast_date_end' AS DATE)	
                )
            )            
            OR $this->table_name.id = $queryId
            ORDER BY date_start, date_end
        ";
        $queryResult = $this->db->query($strQuery);
        //$result = $this->queryResultToArray($queryResultl);

        if (!$queryResult){
            return false;
        }

        //return an asociative array to improve performance when try to search by id
        while ($column = mysqli_fetch_array( $queryResult )){
            $intervalId = $column['id'];
            $result[ $intervalId ] = new Interval(
                $this->db,
                $intervalId,
                $column['date_start'],
                $column['date_end'],
                $column['price']
            );    

        }
        return $result;
    }


    /**
     * return a new instance of Interval with the actual values
     * @return Interval
     */
    public function copy(){
        $copySelf = new $this(
            $this->db,
            $this->id,
            $this->__get('date_start'),
            $this->__get('date_end'),
            $this->__get('price')
        );
        return $copySelf;
    }

    /**
     * @return mixedreturn a new EMPTY instance of Interval
     */
    public function newInstance(){
        return new $this($this->db);       
    }

}

