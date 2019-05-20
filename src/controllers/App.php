<?php

class App extends Controller{

    //Dependency injectionde
    protected $interval;
    protected $db;

    /**
     * App constructor.
     * @param Db $db Instance of DB
     * @param Interval $interval Instance of Interval
     * @param Utilities $utilities Instance of utilities
     */
    function __construct(Db $db, Interval $interval, Utilities $utilities){
        parent::__construct($db, $utilities);
        $this->interval = $interval;
        $this->db = $db;
    }

    function __destruct(){        

    }

    /**
     * used to start the application with the default view
     * @throws Exception
     */
    public function index(){

        $viewFile = 'interval_view';
        $htmlPath = "views/{$viewFile}.php";
        if (file_exists($htmlPath)){
            $this->includeWithVariables($htmlPath);
        } else {
            throw new Exception("Error 404: Page doesn't exist");
        }
    }

    /**
     * @param string $filePath
     * @param array $variables
     * @param bool $print
     * @return false|string|null
     */
    protected function includeWithVariables(string $filePath, array $variables = array(), bool $print = true)
    {
        $output = NULL;
        if(file_exists($filePath)){
            // Extract the variables to a local namespace
            extract($variables);
    
            // Start output buffering
            ob_start();
    
            // Include the template file
            include $filePath;
    
            // End buffering and return its contents
            $output = ob_get_clean();
        }
        if ($print) {
            print $output;
        }
        return $output;
    
    }    

}