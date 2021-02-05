<?php

namespace Framework\Application;



    use Framework\Application;

    use GGN\Security;

    use GGN\Settings;



Class Assets{


    public $App;
    
    

    public function __construct(String $AiD, ?String $Domain = null){

        global $GGN;

        $this->AiD = $AiD;
        
        $this->Base = ($Domain ?: $GGN->{'Http:Host'}) . 'assets/';

    }
    

    public function Get(String $Path, ?String $File = ''){

        global $GGN;

        return ($this->Base) .($Path) . '/' . ($this->AiD?:'') . '/' . ($File);

    }
    

    
    
    
}
