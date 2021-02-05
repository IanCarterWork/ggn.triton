<?php

namespace Database;


    use Database\Driver;


trait Automatic {


    // var $Settings = [];

    // protected $Tables = [];

    // protected $Fields = [];


    public $LastMaker;

    public $LastInsertId;




    public function AutoInvoke(?Array $Tables = null, ?Array $Fields = null){

        if($Tables){

            foreach($Tables as $Name => $Table){ $this->Settings->{$Name} = $Table; }
            
        }

        if($Fields){

            foreach($Fields as $Field => $Type){ $this->Fields[$Field] = $Type; }
            
        }

        $this->Settings = (Object) array_merge($this->Settings, (Array) \GGN\Settings::Get('Apps/Database.Connect') );
        
    }







    public function UpdateTable(
        
        ?String $Name

        , Array $Wheres
        
        , Array $Entries
        
    ): ?Bool{

        $Prepare = [];

        $Queries = [];

        $QWhere = [];




        foreach($Wheres as $Key => $Where){

            $Prepare[$Key] = $Where;

            $QWhere[] = "`$Key` = :$Key";
            
        }


        foreach($Entries as $Field => $Value){

            $Prepare[$Field] = $Value;

            $Queries[] = "`$Field` = :$Field";
            
        }



        $Make = Database\Driver::Query("UPDATE", $this->Settings->{"Qi:DB:WSD:Users:Identity"}, [

            "Query" => "SET " . (\implode(",", $Queries)) . " WHERE " . implode(" AND ", $QWhere) . ""

            , "Prepare" => $Prepare

        ]);


        
        if(is_object($Make)){ 

            $this->LastMaker = $Make;
            
            return true; 
        
        }


        return false;
        
    }







    public function InsertIntoTable(
        
        ?String $Name
        
        , Array $Entries
        
    ): ?Object{

        // var_dump($this->Settings->{ $this->Tables[$Name] });exit;

        if(isset($this->Tables[$Name]) && is_string($this->Settings->{ $this->Tables[$Name] }) ){

            $Prepare = [];

            $Queries = [];


            foreach($Entries as $Entry){

                $Queries[] = '?';

                // $Entry = $Entries[$Field] ?: null;

                $Prepare[] = $Entry;
                
            }


            $Prepare[] = date('Y-m-d H:i:s'); $Queries[] = '?';
            
            $Prepare[] = 1; $Queries[] = '?';
            

            $Make = Driver::Query(
                
                "INSERT INTO"
                
                , $this->Settings->{ $this->Tables[$Name] }
                
                , [

                    "Query" => "VALUES(" . \implode(",", $Queries) . ")"

                    , "Prepare" => $Prepare

                ]
            
            );

            

            if(is_object($Make)){ 

                $this->LastInsertId = $Make->Connexion->lastInsertId();

                $this->LastMaker = $Make;
                
                return $this; 
            
            }

            echo("[Database\Automatics::Insert] Request Failed to : " . ($this->Tables[$Name] ?: $this->Settings->{$Name} ?: $Name));

            var_dump($Make);
            
            var_dump( \count($Queries) );
            
            var_dump($Prepare);

            exit;

        }

        
        exit("[Database\Automatics::Insert] Table introuvable : " . ($this->Tables[$Name] ?: $this->Settings->{$Name} ?: $Name));

        return null;

        
    }




    public function SelectFromTable(
        
        String $Name
        
        , ?String $Select = null
        
        , String $Query = ""
        
        , Array $Prepare = []
        
        , ?Int $Row = null
        
        , ?Array $Rows = null
        
    ): ?Object{

        if(isset($this->Tables[$Name]) && is_string($this->Settings->{ $this->Tables[$Name] }) ){

            $Limit = "";


            if(\is_array($Rows)){

                if(isset($Rows[0])){ $Lim .= "LIMIT " . ($Rows[0] ?: 0); }

                if(isset($Rows[1])){ $Lim .= ", " . ($Rows[1] ?: 10); }
                
            }

            $Make = Driver::Query(
                
                "SELECT " . ($Select ?: "*") . " FROM"
                
                , $this->Settings->{ $this->Tables[$Name] }
                
                , [

                    "Query" => $Query . " " . $Limit

                    , "Prepare" => $Prepare

                ]

            );

            // var_dump($Make);

            if(\is_object($Make)){

                $Data = $Make->State->fetchAll();
            
                if(is_array($Data) && !empty($Data) ){

                    return (Object) ((\is_numeric($Row)) ? $Data[$Row] : $Data);
                    
                }

                return null;


            }

            exit("[Database\Automatics::SelectFrom] DMake failed : " . ($this->Tables[$Name] ?: $this->Settings->{$Name} ?: $Name));

        }

        exit("[Database\Automatics::SelectFrom] Table introuvable : " . ($this->Tables[$Name] ?: $this->Settings->{$Name} ?: $Name));

        return null;

    }
    


}