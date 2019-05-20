<?php 

/**
 * Create a autoload functionality based on the path files, this function doesn't require any change
 * All the load files require to start with upercase and the rest of the letters in lowercase like "Interval"
 */
spl_autoload_register (/**
 * @param $class
 */
    function ($class) {
    //List of the paths of the files
    $sources = array(
        "models/$class.php ", 
        "views/$class.php ", 
        "controllers/$class.php",         
        "objects/$class.php", 
        "helpers/$class.php " ,
        "config/$class.php " ,
        "../tests/$class.php ",

        
        "src/helpers/$class.php " , /* for PHPUnit only */
        "tests/$class.php ",

    );

    //$listOfFiles = scandir('../tests/');
    
    foreach ($sources as $source) {
        if (file_exists($source)) {
            /** @var string $source */
            require_once $source;
        } 
    } 
});