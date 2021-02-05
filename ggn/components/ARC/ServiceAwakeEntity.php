<?php

namespace ARC;


    use GGN\ARC\Entity;

    use GGN\ARC\Parameters as ARCParameters;
    
    use GGN\Services;
    
    // use Framework\Application\Boot;




    
/**
 * @Class ServiceAwakeEntity
 */

Class ServiceAwakeEntity extends Entity{


    /**
     * 
     * @Instance "ARC": "/ServicesAwake/*"
     * 
     */
    public function Start(Services\Provider $Service, ARCParameters $Params){

        // var_dump('Service Awake :', (String) $Request);exit;

        $Set = $Service->Init($Params);

        if(is_object($Set)){ 

          header("Content-Type:application/json");

          echo $Set->Build();

        }

        else{

          echo '{"Response":null, "Title":"Echec Service", "About":"Service:unvailable"}';
          
        }


        return true;

    }
    
    
}