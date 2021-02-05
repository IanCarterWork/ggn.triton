<?php

namespace ARC;


    use GGN\ARC\Entity;

    use GGN\ARC\Parameters as ARCParameters;
    
    use Framework\Application;



    
/**
 * @Class HelloWorld
 */

Class HelloWorld extends Entity{


    /**
     * @Instance "ARC": "/HelloWorld"
     * @Instance "ARC": "/HelloWorld/*"
     */
    public function Index(ARCParameters $View){

        $App = new Application('com.hello.world', '0.0.1');
        
        return $App->Boot("$View" ?: 'Index');

    }
    

    
    
}