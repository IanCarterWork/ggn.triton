<?php

namespace GGN\ARC;


Class Parameters{



    public $Settings;




    public function __construct(Object $Settings){

        $this->Settings = $Settings;
        
    }


    public function GetBody(){ 
        
        try{

            return \json_decode(\file_get_contents('php://input')); 

        } catch(Exception $e){}

        return null;

    }
    

    public function Get(Int $Key){

        return $this->Settings->Type == ':dynamic' ? ($this->Settings->Matches[1][$Key] ?: NULL) : NULL;

    }


    public function __toString(){

        return $this->Get(0) ?: '';

    }
    
    
}
