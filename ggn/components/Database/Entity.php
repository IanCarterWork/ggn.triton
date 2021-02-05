<?php

namespace Database;


    // use GGN\Patterns\Annotations;

    use App\Connect;

    use GGN\Patterns\CamelCase;


class Entity {


    use Transactions;


    public function Main(Object $Param = null){

        $Main = $Param ?: (Object) [];
        
        $Main->Driver = $Main->Driver ?: (new Driver());
        
        $Main->Features = $Main->Features ?: new \ReflectionClass($this);
        
        $Main->TableRaw = $Main->TableRaw ?: CamelCase::From($Main->Features->name);
        
        $Main->Table = $Main->Table ?: $Main->Driver->Settings->{'DB:Prefix'} . $Main->TableRaw;
        
        return $Main;
        
    }
    

    public function Set(String $Name, $Value){

        if(property_exists($this, $Name)){

            $Entity = new \ReflectionClass($this);

            $Prop = $Entity->getProperty($Name);
    
            $Prop->setAccessible(true);
            
            $Prop->setValue($this, $Value);
    
            // var_dump($Name, $Value, $this);
    
        }
        
        return $this;
        
    }

    public function Get(String $Name){
        
        $Entity = new \ReflectionClass($this);

        $Prop = $Entity->getProperty($Name);

        $Prop->setAccessible(true);
        
        return (isset($this->{$Name})) ? $this->{$Name} : NULL;
    
    }

    public function BindInsertion(){return null;}

    public function BindUpdating(){return null;}



    public function __construct($iD = null, Bool $ReleaseFields = true){


        if($iD){

            if(is_string($iD) || is_numeric($iD)){
                
                $Find = $this->FindOneBy('iD', $iD);

                if(is_object($Find)){

                    foreach($Find as $Key => $Value){

                        if(\property_exists($this, $Key)){

                            $this->Set($Key, $Value);

                        }

                    }
                    
                }

            }

            

            if(is_array($iD)){
                
                $Find = $this->FindOneBy($iD[0], $iD[1]);

                if(is_object($Find)){

                    foreach($Find as $Key => $Value){

                        if(\property_exists($this, $Key)){

                            $this->Set($Key, $Value);

                        }

                    }
                    
                }

            }


            if($ReleaseFields === true){

                $this->ReleaseEntityFields();

            }

            
            
        }





    }





    public function ConvertObjectToEntity(?Object $Object = null):?Entity{

        $Manager = new EntityManager($this);

        $Entity = null;


        if($Object){

            if(\is_array($Manager->Fields)){
                
                $Entity = clone $this;
                
                foreach($Manager->Fields as $Field){

                    if(isset( $Object->{$Field->name} )){

                        $Entity->Set($Field->name, $Object->{$Field->name});
                        
                    }

                }

            }

            
        }


        return $Entity;

    }






    public function ReleaseEntityFields(){

        $Manager = new EntityManager($this);


        if(\is_array($Manager->Fields)){

            foreach($Manager->Fields as $Field){


                switch(strtolower($Field->type[0][0])){



                    case 'uuid':

                        $Connect = new Connect\Master();

                        $Get = $this->Get($Field->name);

                        $Find = $Connect->GetByUUiD($Get);

                        if(is_object($Find)){
                            
                            $this->Set($Field->name, $Connect::Fullname($Find->firstname, $Find->lastname));
                            
                        }

                    break;



                    case 'datetime':

                        $Get = $this->Get($Field->name) ?: null;

                        if($Get){

                            $this->Set($Field->name, date('d/m/Y h:i:s', strtotime($Get) ) );

                        }

                    break;



                    case 'json':

                        $Get = $this->Get($Field->name) ?: null;

                        if($Get){

                            $this->Set($Field->name, json_decode($Get) ?: $Get );

                        }

                    break;



                    default:

                        /**
                         * Chemin de Fichier
                         */
                        
                        $Path = \explode(':', $Field->type[0][0] ?: []);

                        if($Path[0] == 'path'){

                            if($Field->uploading?:false){

                                if($Field->uploading->dir?:false){

                                    $Values = explode('|',$this->Get($Field->name));

                                    $Set = [];

                                    foreach($Values as $Value){

                                        $Set[] = (($Value) ? (($Field->uploading->dir?:'') . $Value) : null);

                                    }

                                    $this->Set($Field->name,  implode('|', $Set));
                                        
                                }

                            }
                            
                        }

                    break;



                }
                
                
                $Value = $this->Get($Field->name) ?: null;

                $Value = (\is_string($Value)) ? stripslashes(utf8_decode($Value)) : $Value;

                $this->Set($Field->name, $Value);


            }
            

        }


        // exit;

        return $this;

    }





    public function FindOneBy(String $iD, $Value){

        $Main = $this->Main();

        return $this->SelectFromTable(
            
            $Main->TableRaw
            
            , '*'
            
            , " WHERE `" . $iD . "` = :" . $iD . " "

            , [

                $iD => $Value

            ]

            , 0

            , [1]
        
        );
        
    }
    





    public function FindBy(String $iD, $Value, ?Int $Row = null, ?Int $Rows = null){

        $Main = $this->Main();

        return $this->SelectFromTable(
            
            $Main->TableRaw
            
            , '*'
            
            , " WHERE `" . $iD . "` = :" . $iD . " "

            , [

                $iD => $Value

            ]

            , $Row

            , $Rows

        );
        
    }
    
    





    public function FindIn(String $iD, $Value, ?Int $Row = null, ?Int $Rows = null){

        $Main = $this->Main();

        return $this->SelectFromTable(
            
            $Main->TableRaw
            
            , '*'
            
            , " WHERE `" . $iD . "` IN :" . $iD . " "

            , [

                $iD => '(' . $Value . ')'

            ]

            , $Row

            , $Rows
        
        );
        
    }
    
    





    public function FindLike(String $iD, $Value, ?Int $Row = null, ?Int $Rows = null){

        $Main = $this->Main();

        return $this->SelectFromTable(
            
            $Main->TableRaw
            
            , '*'
            
            , " WHERE `" . $iD . "` LIKE :" . $iD . " "

            , [

                $iD => '(' . $Value . ')'

            ]

            , $Row

            , $Rows
        
        );
        
    }
    






    public function FindAll(?Int $Start = 0, ?Int $End = 10, ?Array $Filter = null, ?Array $Order = []){

        $Main = $this->Main();

        $Manager = new EntityManager($this);


        $Queries = [];

        $Prepare = [];


        if(\is_array($Filter)){

            $Q = [];

            foreach($Filter as $FieldName => $Value){
                
                foreach($Manager->Fields as $Field){

                    $Type = ($Field->name == $FieldName) ? $Field->type[0][0] : null;

                    if($Type === null){ continue;}


                    if(isset($Field->relationShip)){

                        // $tEntity = $Field->relationShip[0];

                        // $tField = trim($Field->relationShip[1]?:null);
        
                        $tRelation = $Field->relationShip[2]?:'OneToMany';
        
                        // $tLabel = trim($Field->relationShip[3]?:'title');


                        if($tRelation){

                            $tQ = [];

                            $tP = [];

                            foreach(explode(',', $Value . '') as $vKey => $Val){

                                $vKey = trim($vKey);

                                $Val = trim($Val);

                                $QueryName = $FieldName . $vKey;

                                if(strchr($tRelation, 'OneTo')){

                                    $tQ[] = " ( `" . $FieldName . "` = :" . $FieldName . " ) ";

                                    $tP[$FieldName] = ($Val ?: NULL);
                                    
                                    continue;

                                }

                                else{

                                    $tQ[] = " ( `" 
                                    
                                        . $FieldName . "` = :" . $QueryName . " OR `" 
                                        
                                        . $FieldName . "` LIKE :" . $QueryName . "Ak OR `" 
                                        
                                        . $FieldName . "` LIKE :" . $QueryName . "Lk OR  `" 
                                        
                                        . $FieldName . "` LIKE :" . $QueryName . "Rk ) ";


                                    $tP[$QueryName] = ($Val ?: NULL);

                                    $tP[$QueryName . 'Lk'] = "%," . ($Val ?: NULL);
            
                                    $tP[$QueryName . 'Rk'] = ($Val ?: NULL) . ",%";

                                    $tP[$QueryName . 'Ak'] = "%," . ($Val ?: NULL) . ",%";

                                    continue;
                                    
                                }
                                

                            }

                            $Q[] = implode(' OR ', $tQ);

                            $Prepare = array_merge($Prepare, $tP);
                            
                            continue;

                        }
                        

                        // echo '<pre>';
                        // var_dump('FindAll', $Field->relationShip );

                        // exit;

                        
    
                    }
                    

                    if($Value === NULL){

                        $Q[] = " `" . $FieldName . "` IS NULL";

                        continue;
                        
                    }

                    if($Value !== NULL){

                        $Q[] = " `" . $FieldName . "` = :" . $FieldName . "";
                    
                        $Prepare[$FieldName] = $Value ?: NULL;
                        
                        continue;
                    }

                }
            
            }


            
            $Queries[] = " WHERE " . implode(" AND ", $Q);

        }
        

        if(\is_array($Order)){

            $Queries[] = " ORDER BY " . ($Order['By'] ?: 'created') . " " . ($Order['Sort'] ?: 'DESC') . "";
            
        }
        
        // echo '<pre>';
        // var_dump('Queries', $Filter, $Queries, $Prepare, '<br>');
        // echo '</pre>';
            
        // var_dump((is_numeric($Start) && is_numeric($End) && $Start !== null && $End !== null) ? [$Start ?: 0, $End ?: 10] : null);exit;

        $Make = $this->SelectFromTable(
            
            $Main->TableRaw
            
            , '*'
            
            , implode(' ', $Queries)

            , $Prepare

            , null

            , (is_numeric($Start) && is_numeric($End) && $Start !== null && $End !== null) ? [$Start ?: 0, $End ?: 10] : null
        
        );


        // var_dump($Make, $Prepare, $Queries, $Prepare, $Manager->Fields);

        if(\is_object($Make)){

            $Entries = [];

            foreach($Make as $Row){
                
                $Entry = [];
                
                foreach($Manager->Fields as $Field){

                    $Entry[$Field->name] = $Row[$Field->name] ?: null;
    
                    // if($Field->type[0][0] == 'json'){

                    //     $Entry[$Field->name] = \json_decode($Entry[$Field->name]);

                    // }

                    // else{
                        
                        if(\is_string($Entry[$Field->name]) ){
        
                            $Entry[$Field->name] = stripslashes(utf8_decode($Entry[$Field->name]));
                            
                        }    
                        
                    // }

                    
                }

                if(!empty($Entry)){

                    $Entries[] = (Object) $Entry;

                }

            }

            return (Object) $Entries;
            

        }


        return $Make;
        
        
    }






}