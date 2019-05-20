<?php

/**
 * Class Db This class allow to execute string queries,
 */
class Db extends Exception{
    // Connexion parameters: without framework, those will be assigned as property of connection class

    /**
     * @var mysqli|null property of the class that contains the connexion and is used o sent as parameter
     */
    public $conn = null;

    private $username = "root";
    private $password = "";
    private $host = "localhost";
    private $database = "price_interval";

    /**
     * Db constructor.
     * Database configuration usually could placed in config file and get in the framework.
     */
    function __construct(){
        parent::__construct();

        // Create connection
        try{
            $conn = @new mysqli($this->host, $this->username, $this->password, $this->database    );
            if (@$conn->connect_error) {
                http_response_code(503);                
                die("Connection failed: " . $conn->connect_error);
            }           
            $this->conn = $conn;    
    
        } catch (Exception $e){
            error_log("Connexion couldn't be stablished");
        }
        // Check connection

        return $this->conn;
    }

    /**
     * Close the connexion at the end of the request
     */
    public function __destruct(){
        //close connexion
        if (isset($this->conn) && $this->conn->connect_errno) {            
            mysqli_close( $this->conn );
        }
    }

    /**
     * @param string $strQuery method to execute string queries
     * @return bool|mysqli_result if query fails, return false, else, return a mysqli_result object
     */
    public function query(string $strQuery){
        $result = false;
        try{
            //execute query
            $queryResult = mysqli_query( $this->conn, $strQuery );
            //Check result, if error, return false, if true, return asociative array
            if (!$queryResult){ // Assigned to help debub
                $result = false;
            } else {
                //Format result into array of objects
                $result = $queryResult;
            }

        } catch (Exception $e){
            error_log ('Query Error:{$consulta}', 0);
        }
        return $result;        
    }

    /**
     * Get the list of all the intervals in the database
     * @return bool|mysqli_result
     */
    public function getAllIntervals(){
        $query = "SELECT * FROM intervals ORDER BY date_start, date_end";
        $queryResult = $this->query($query);
        return $queryResult ;
    }


    /**
     * @param mysqli_result $queryResult
     * @param string $keyName
     * @return array|bool
     */
    public static function queryResultToArray($queryResult, string $keyName = 'id'){
        $result = array();
        if (!$queryResult){
            return false;
        }

        //return an associative array to improve performance when try to search by id
        while ($column = mysqli_fetch_array( $queryResult )){      
            if(!$column[$keyName]) continue;
            $result[ $column[ $keyName ] ] = new StdClass();
            foreach($column as $columnName => $value){
                if(!is_int($columnName)){
                    //$key = is_numeric($column[ $keyName ]) ? (int)($column[ $keyName ]) : $column[ $keyName ];
                    $result[ $column[ $keyName ] ]->{ $columnName } = $column[$columnName];
                }
            }     
        }

        return $result ;       
    }    

   
}