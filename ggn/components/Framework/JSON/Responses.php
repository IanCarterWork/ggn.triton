<?php

namespace Framework\JSON;



Class Responses{


    public $Response;

    public $Title;

    public $About;


    /**
     * Nouveau Construct
     */
    public function __construct(String $Title){

        $this->Title = $Title;
        
    }


    /**
     * Définir une reponse
     */
    public function Set(?Bool $Response = null, ?String $About = null){

        $this->Response = $Response;
        
        $this->About = $About;

        return $this;
        
    }


    /**
     * Obtention de la reponse
     */
    public function Get(){

        return ($this);
        
    }

    
    
}