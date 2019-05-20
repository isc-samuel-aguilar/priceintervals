<?php

class HTTPRequester {


    /** Resource is passed to be executed and return an object with the information and result of the request
     * @param resource $ch
     * @param string $method_request name of the request to check the parameter to close the connexion
     * @return object return a object with the request and response information
     */
    private static function formatResponse($ch, $method_request = ''){
        $response = curl_exec($ch);
        $responseInfo = curl_getinfo($ch);
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = $response; //substr($response, 0, $header_size);
        if (
            gettype($header) === "string" && !empty($header) &&
            ( substr($header,0,1) === '[' && substr($header,-1)===']' ) || 
            ( substr($header,0,1) === '{' && substr($header,-1)==='}' ) 
        ){
            $body = $header;
        } else {
            $body = substr($response, $header_size);
        }

        
        if (($responseInfo['content_type'] === "application/json; charset=UTF-8")){
            $bodyFormated = json_decode($body);
        } else {
            $bodyFormated = substr($response, $header_size);
        }

        $responseFormated = (object)array(
            'http_code'     => $responseInfo['http_code'],            
            'content_type'  => $responseInfo['content_type'], 
            'header'        => substr($response, 0, $responseInfo['header_size'] ),
            'body'          => $body,    
            'response'      => $bodyFormated,        
            'url'           => $responseInfo['url']
            ,'info'         => $responseInfo
        );

        $response =  $method_request === 'DELETE' ? \curl_close($ch) : curl_close($ch);
        
        return $responseFormated;

    }

    /**
     * @description Make HTTP-GET call
     * @param       $url
     * @param       array $params
     * @return      StdClass with response information
     */
    public static function HTTPGet($url, $params = array()) {
        $query = http_build_query($params); 
        $ch    = curl_init($url.'?'.$query);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $objResponse = self::formatResponse($ch);
        return $objResponse;
    }
    /**
     * @description Make HTTP-POST call
     * @param       $url
     * @param       array $params
     * @return      Object body or an empty string if the request fails or is empty
     */
    public static function HTTPPost($url, $params = array()) {
        $query = http_build_query($params); 
        $ch    = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
        $objResponse = self::formatResponse($ch, 'POST');
        return $objResponse;

    }
    /**
     * @description Make HTTP-PUT call
     * @param       $url
     * @param       array $params
     * @return      StdClass with response information
     */
    public static function HTTPPut($url, $params = array()) {
        $query = \http_build_query($params);
        $ch    = \curl_init();
        \curl_setopt($ch, \CURLOPT_RETURNTRANSFER, true);
        \curl_setopt($ch, \CURLOPT_HEADER, false);
        \curl_setopt($ch, \CURLOPT_URL, $url);
        \curl_setopt($ch, \CURLOPT_CUSTOMREQUEST, 'PUT');
        \curl_setopt($ch, \CURLOPT_POSTFIELDS, $query);
        /*
        $response = \curl_exec($ch);
        $objResponse = curl_getinfo($ch);
        \curl_close($ch);
        return $objResponse;
        */
        $objResponse = self::formatResponse($ch, 'PUT');
        return $objResponse;

    }
    /**
     * @category Make HTTP-DELETE call
     * @param    $url
     * @param    array $params
     * @return      StdClass with response information
     */
    public static function HTTPDelete($url, array $params = array()) {
        $query = \http_build_query($params);
        $ch    = \curl_init();
        \curl_setopt($ch, \CURLOPT_RETURNTRANSFER, true);
        \curl_setopt($ch, \CURLOPT_HEADER, false);
        \curl_setopt($ch, \CURLOPT_URL, $url);
        \curl_setopt($ch, \CURLOPT_CUSTOMREQUEST, 'DELETE');
        \curl_setopt($ch, \CURLOPT_POSTFIELDS, $query);
        //-------------------------
       
        /*
        $response = \curl_exec($ch);
        $objResponse = curl_getinfo($ch);
        \curl_close($ch);
        return $objResponse;
        */
        $objResponse = self::formatResponse($ch);
        return $objResponse;

    }
}