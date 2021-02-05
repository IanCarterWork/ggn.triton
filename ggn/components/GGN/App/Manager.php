<?php


namespace GGN\App;

    use GGN\Settings;


Class Manager{



    public function __construct(String $iD){

        global $GGN;

        $this->iD = $iD;

        $this->Dir = $GGN->{'Dir:Apps'} . '' . $this->iD . '/';

        $this->ManifestPath = $this->Dir . 'Manifest.json';

        $this->Manifest();

    }


    public function Manifest(){

        global $GGN;

        if(\is_file($this->ManifestPath)){

            $Manifest = \json_encode(\file_get_contents($this->ManifestPath)) ?: ((Object) []);

            $Manifest->{'App:Dir'} = $this->Dir;

            return $Manifest;

        }

        return null;

    }


    public function Framework(String $Path, $Args = null){

        $this->AllowInstance = false;

        $File = $this->Dir . 'framework/' . $Path . '.php';

        if(is_file($File)){

            if(\is_array($Args) || \is_object($Args)){ \extract($Args); }

            include $File;

        }
        
        return $this->AllowInstance;

    }




    
    static public function Packages(){

        return Settings::Get('Apps/Manager');
        
    }


    static public function Get(String $iD){

        return new self($iD);

    }



}