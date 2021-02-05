<?php

namespace Framework\Application;



    use Framework\Application;

    use GGN\Security;

    use GGN\Settings;



Class Manager{


    public $App;
    
    

    public function __construct(String $iD, ?Object $Manifest = null){

        global $GGN;

        $Register = self::Register();

        $this->App = $Register->{'Apps:Packages'}->{$iD} ?: null;

    }
    

    
    static public function RegisterPath(){

        return 'Apps/Register';
        
    }

    
    static public function Register(){

        return Settings::Get(self::RegisterPath());
        
    }

    
    static private function UpdateRegister(Settings $Register){

        $Register->{"LastUpdated"} = date('Y-m-d H:i:s');

        return $Register->Save(self::RegisterPath());
        
    }

    
    static public function AddToRegister(Object &$Manifest, String $ARC, String $InstallDir = null){

        $Register = self::Register();

        $App = $Manifest;

        $App->{'App:ARC'} = $ARC;

        $App->{'App:Install:Dir'} = $InstallDir;

        
        $Register->{'Apps:Packages'} = $Register->{'Apps:Packages'} ?: (Object) [];

        $Register->{'Apps:PiD'} = $Register->{'Apps:PiD'} ?: (Object) [];


        $Register->{'Apps:Packages'}->{$Manifest->{'App:iD'}} = $App;

        $Register->{'Apps:PiD'}->{$Manifest->{'App:iD'}} = $Register->{'Apps:PiD'}->{$Manifest->{'App:iD'}} ?: (count((Array) $Register->{'Apps:Packages'}) * 1000);


        $Manifest->{'App:PiD'} = $Register->{'Apps:PiD'}->{$Manifest->{'App:iD'}};

        return self::UpdateRegister($Register);
        
    }
    



    /**
     * Fonction Application Instancié
     */

    public function Link(?String $View = null, ?String $Domain = null){

        if(is_object($this->App)){

            global $GGN;

            return ($Domain ?: $GGN->{'Http:Host'}) 
            
                . $this->App->{"App:ARC"} 
                
                . (
                    
                    ($View?:false) ? (( $this->App->{'Navigation:UsePathMode'} ? '' : '#' ) . $View) : ''
                    
                )
                
            ;

        }

        else{ return null; }

    }
    


    public function Assets(String $Path, ?String $File = '', ?String $Domain = null){

        if(is_object($this->App)){

            global $GGN;

            $this->App->Assets = $this->App->Assets ?: new Assets($this->App->{'Asset:iD'}, $Domain);

            return $this->App->Assets->Get($Path, $File);

        }

        else{ return null; }

    }
    
    
}
