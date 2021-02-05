<?php

namespace Database;





class Driver{



    private $Host;

    private $Name;

    private $User;

    private $Password;



    public $Charset;

    public $Prefix;



    var $Settings;
    



    public function __construct(){

        $this->Settings = (Object) array_merge( (Array) \GGN\Settings::Get('Apps/Database.Connect') );


        $this->Host = $this->Host ?: $this->Settings->{"DB:Host"} ?: 'localhost';

        $this->Name = $this->Name ?: $this->Settings->{"DB:Name"} ?: null;

        $this->User = $this->User ?: $this->Settings->{"DB:User"} ?: 'root';

        $this->Password = $this->Password ?: $this->Settings->{"DB:Pass"} ?: $this->Settings->{"DB:Password"} ?: '';

        $this->Charset = $this->Charset ?: $this->Settings->{"DB:Charset"} ?: 'utf8mb4';

        $this->Prefix = $this->Prefix ?: $this->Settings->{"DB:Prefix"} ?: 'ggn_';



        unset($this->Settings->{"DB:Host"});

        unset($this->Settings->{"DB:Name"});

        unset($this->Settings->{"DB:User"});

        unset($this->Settings->{"DB:Pass"});

        unset($this->Settings->{"DB:Password"});
        
        
    }


    static public function Connect(){

        global $GGN;

        return \is_object($GGN->{'Database:Connect'}) ? $GGN->{'Database:Connect'} : null;
        
    }


    static public function Query($Operation, String $Table = '', Array $Setter = []):?Object{

        return (new self())->_Query($Operation, $Table, $Setter);
        
    }


    public function _Query($Operation, String $Table, Array $Setter):?Object {

        global $GGN;

        $Prepare = $Setter['Prepare'] ?? [];

        $Query = $Setter['Query'] ?? "";


        try {

            $IsConnected = self::Connect($GGN->{'Database:Connect'});

            if($IsConnected){ $Co = $GGN->{'Database:Connect'}; }

            if(!$IsConnected){

                $Co = new \PDO(
                    
                    "mysql:host=" . $this->Host . ";dbname=" . $this->Name
                        
                        . ";charset=" . ($this->Charset ?: 'utf8mb4')
                        
                    , $this->User
                    
                    , $this->Password
                
                );

                $GGN->{'Database:Connect'} = $Co;

            }



            $Co->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            
            // $Co->setAttribute(\PDO::PARAM_NULL, false);

            $Co->setAttribute(\PDO::ATTR_EMULATE_PREPARES, FALSE);

            $Co->setAttribute(\PDO::ATTR_STRINGIFY_FETCHES, FALSE);


            $State = $Co->prepare($Operation . " " . (($this->Settings->{'DB:Prefix'} ?: '') . $Table) . "  " . $Query . " ");

            // $IsPrepare = \is_array($Prepare) || \is_object($Prepare);

            $State->execute($Prepare);


            $return = (Object) [
                
                'State'=> $State

                , 'Connexion'=> $Co
            
            ];

        }

        catch(\PDOException $e){

            echo $e->getMessage();

            $return = null;

        }

        return $return;

    }


    public function Execute($Query) {

        global $GGN;

        $Prepare = $Setter['Prepare'] ?? [];


        try {

            $IsConnected = self::Connect($GGN->{'Database:Connect'});

            if($IsConnected){ $Co = $GGN->{'Database:Connect'}; }

            if(!$IsConnected){

                $Co = new \PDO(
                    
                    "mysql:host=" . $this->Host . ";dbname=" . $this->Name
                        
                        . ";charset=" . ($this->Charset ?: 'utf8mb4')
                        
                    , $this->User
                    
                    , $this->Password
                
                );

                $GGN->{'Database:Connect'} = $Co;

            }


            $Co->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            
            // $Co->setAttribute(\PDO::PARAM_NULL, false);

            $Co->setAttribute(\PDO::ATTR_EMULATE_PREPARES, FALSE);

            $Co->setAttribute(\PDO::ATTR_STRINGIFY_FETCHES, FALSE);


            $State = $Co->prepare($Query);

            // $IsPrepare = \is_array($Prepare) || \is_object($Prepare);

            $State->execute($Prepare);


            $return = (Object) [
                
                'State'=> $State

                , 'Connexion'=> $Co
            
            ];

        }

        catch(\PDOException $e){

            $return = $e->getMessage();

        }

        return $return;

    }






    
}




?>