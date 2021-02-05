<?php

namespace GGN\ARC;

Class Hit{

    public function __construct(Object $Config){

        foreach($Config as $Name => $Value){

            $this->{$Name} = $Value;
            
        }
        
    }

    public function __toString() {
        
        return 'GGN Hit';
        
    }

}