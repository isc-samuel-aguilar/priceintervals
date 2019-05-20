<?php

/**
 * This class will contain the general functions that are not part of a class, 
 * This class is like a helper or generic library
 */
class Utilities {    

    //Db object to execute in functionalities that require database (if apply)
    public $db;

    public $errorCodes = array(
        'PUT'    => 400,
        'PATCH'  => 400,
        'DELETE' => 400,
        'GET'    => 400,
        'POST'  => 400
    );

    
    /**
     * Undocumented function
     *
     * @param Db $db Instance of Db class to execute queries.
     */
    function __construct(Db $db){        
        $this->db = $db;
    }

    /**
     * Sanatize inputs to prevent a XSS or SQL Inyection
     *
     * @param mixed $inputVal input value that require to be sanitized
     * @return mixed return the value passed removing the special characters
     */
    static function sanitizeInput($inputVal){
        //This function could escape, remove, etc characters, the logic should be updated only here
        return htmlspecialchars(strip_tags($inputVal));
    }    

    static function sanitizParam($inputVal){
        //This function could escape, remove, etc characters, the logic should be updated only here
        return htmlspecialchars(strip_tags($inputVal));
    }    




}