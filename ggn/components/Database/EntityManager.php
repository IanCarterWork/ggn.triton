<?php

namespace Database;


    use GGN\Patterns\Annotations;

    use GGN\Patterns\CamelCase;


class EntityManager {



    /**
     * @Type \Database\Entity
     */

    public $Entity;




    /**
     * @Type Array
     */

    private $_Blueprints = [];




    /**
     * @Type Array
     */

    private $_Prepares = [

        'Insert' => []

        , 'Update' => []

        , 'Delete' => []
        
    ];

    private $_Queries = [

        'Insert' => []

        , 'Update' => []

        , 'Delete' => []
        
    ];





    /**
     * @Trait \Database\Transactions
     */

    use Transactions;




    /**
     * @Type \Database\Blueprints
     */

    private $Blueprints = [

        'Table:Create' => "CREATE TABLE IF NOT EXISTS `%TABLE%` ( %FIELDS% ) ENGINE=InnoDB DEFAULT CHARSET=%CHARSET%;"

        , 'Row:Insert' => "INSERT INTO `%TABLE%` (%FIELDS%) VALUES (%VALUES%);"

        , 'Row:Update' => "UPDATE `%TABLE%` SET %SET% WHERE %WHERE%;"

        , 'Row:Delete' => "DELETE FROM `%TABLE%` WHERE %WHERE%;"
        
    ];




    /**
     * @Construct \Database\EntityManager
     */
    public function __construct(?Entity &$Entity = null){

        $this->Entity = $Entity;

        $this->Invoke();

    }
    
    
    


    /**
     * @Type \Object
     */
    private function FieldTypes(){

        return (Object)[

            'integer' => 'int'

            , 'string' => 'varchar'

            , 'uuid' => 'varchar'

            , 'text' => 'longtext'

            , 'url' => 'text'

            , 'path:image' => 'text'

            , 'path:images' => 'longtext'

            , 'datetime' => 'datetime'

            // , 'json' => 'json'
            
        ];
        
    }
    



    /**
     * @Type \Invoker
     */
    private function Invoke(){

        $this->Driver = (new Driver());
        
        $this->Features = new \ReflectionClass($this->Entity);
        
        $this->TableRaw = CamelCase::From($this->Features->name);
        
        $this->Table = $this->Driver->Settings->{'DB:Prefix'} . $this->TableRaw;
        
        $this->Fields = $this->Features->getProperties();
        


        if(is_array($this->Fields)){

            foreach($this->Fields as $Field){

                $Type = Annotations::Type(( 
                    
                    new \ReflectionProperty($this->Entity, $Field->name)
                        
                )->getDocComment());

                $Field->type = $Type;



                $Input = Annotations::Find(( 
                    
                    new \ReflectionProperty($this->Entity, $Field->name)
                        
                )->getDocComment(), '@InputField');

                $Field->input = $Input;



                $Uploading = Annotations::Find(( 
                    
                    new \ReflectionProperty($this->Entity, $Field->name)
                        
                )->getDocComment(), '@Uploading');



                $RelationShip = Annotations::Find(( 
                    
                    new \ReflectionProperty($this->Entity, $Field->name)
                        
                )->getDocComment(), '@RelationShip') ?: null;

                if(is_array($RelationShip)){

                    $Field->relationShip = rtrim(ltrim($RelationShip[0]?:'')) ;

                    $Field->relationShip = explode(',', $Field->relationShip);

                }


                // $Field->uploading = $Uploading;

                if(\is_array($Uploading)){

                    $UpType = explode('path:', $Field->type[0][0]);

                    $Upload = explode(',', $Uploading[0]);

                    $Field->uploading = (Object) [

                        'mimes' =>  explode(',', ($UpType[1]?:'image') . '/' . implode(', ' . ($UpType[1]?:'image') . '/', explode('|', $Upload[0]?:[])))

                        , 'sizes' =>  rtrim(ltrim($Upload[1] ?: null))

                        , 'dir' =>  rtrim(ltrim($Upload[2] ?: null))
                        
                    ];

                }



            }
            
        }

        return $this;
        
    }








    /**
     * 
     */

    static public function TranscribeValueFromType($Type, &$Value){


        switch(strtolower($Type)){

            case 'datetime': $Value = "'" . (new \DateTime($Value))->format('Y-m-d H:i:s') . "'"; break;

            case 'json': $Value = \json_encode($Value); break;

            case 'integer': $Value = ($Value * 1); break;

            default:

                if(is_string($Value)){ $Value = "'" . addslashes(utf8_encode($Value)) . "'"; }
                
                else{ $Value = $Value ?: "NULL"; }

            break;

        }

        return $Value;

    }








    /**
     * @Type \Database\EntityManager
     */
    public function Insert(?Entity $Entity = null){


        $this->_Blueprints['Insert'] = $this->_Blueprints['Insert'] ?: [];

        if(\is_array($this->Fields)){

            $Entity = $Entity ?: $this->Entity;

            $_Fields = [];

            $_Values = [];

            $Prepares = [];

            $Queries = [];
        

            foreach($this->Fields as $Field){

                if($Field->type){

                    $_Fields[] = "`" . $Field->name . "`";

                    $Value = $Entity->Get($Field->name);

                    
                    
                    $Value = self::TranscribeValueFromType($Field->type[0][0], $Value);
                    
                    $Prepares[$Field->name] = $Value;

                    $Queries[] = " `" . $Field->name . "` = :" . $Field->name . " ";

                    $_Values[] = $Value;
                    
                }

                
            }


            $this->_Prepares['Insert'][] = $Prepares;

            $this->_Queries['Insert'][] = $Queries;


            $Query = $this->Blueprints['Row:Insert'];

            $Query = \str_replace('%TABLE%', $this->Table, $Query );

            $Query = \str_replace('%FIELDS%', implode(', ', $_Fields), $Query );

            $Query = \str_replace('%VALUES%', implode(', ', $_Values), $Query );
            

            // var_dump('Query', $Query, $_Values);exit;


            $this->_Blueprints['Insert'][] = $Query;

        }

        return $this;
        
    }






    
    /**
     * @Type \Database\EntityManager
     */
    public function CleanEntity(){
        
        if(is_object($this->Fields) && $this->Entity && method_exists($this->Entity, 'Set')){

            foreach($this->Fields as $Field){

                $this->Entity->Set($Field->name, NULL);

            }

        }

        return $this;
        
    }
    






    /**
     * @Type Int
     */
    public function GetPrimaryKey(){
        
        $Found = null;
        
        if($this->Entity){

            foreach( $this->Fields as $Field){

                if(strtolower($Field->name) == 'id'){

                    $Found = $Field->name;

                    break;

                }
                
            }
           
        }

        return $Found;
        
    }






    /**
     * @Type \Database\EntityManager
     * @Var Array
     */
    
    public function InsertRow(Array $Row){
        

        if(\is_array($Row) && $this->Entity && method_exists($this->Entity, 'Set') ){

            $Clone = new $this->Entity;

            foreach( $Row as $Name => $Value){

                $Clone->Set($Name, htmlentities($Value));
                
            }

            $this->Insert($Clone);

            $this->CleanEntity();

        }

        return $this;
        
    }
    






    /**
     * @Type \Database\EntityManager
     * @Var Array
     */
    public function Update(Int $iD, Array $Updater){
        
        $this->_Blueprints['Update'] = $this->_Blueprints['Update'] ?: [];

        
        if($this->Entity && \is_array($this->Fields)){

            $Key = $this->GetPrimaryKey();

            $Query = $this->Blueprints['Row:Update'];
            
            $Query = \str_replace('%TABLE%', $this->Table, $Query );
            
            $Query = \str_replace('%WHERE%', ("`" . $Key . "` = " . $iD . " "), $Query );


            $Set = [];

            $Prepares = [];

            $Queries = [];
        

            
            foreach($this->Fields as $Field){

                foreach($Updater as $Name => $Value){

                    if($Field->name != $Name){continue;}


                    // $Value = self::TranscribeValueFromType($Field->type[0][0], $Value);
                    

                    if(\is_array($Value) || \is_object($Value)){
                        
                        $Set[] = ("`" . addslashes($Name) . "` = '" . (json_encode($Value)) . "' ");

                    }

                    else{

                        $Set[] = ("`" . addslashes($Name) . "` = '" . addslashes(utf8_encode($Value)) . "' ");
                        
                    }
                    

                    $Prepares[$Name] = $Value;

                    $Queries[] = " `" . $Name . "` = :" . $Name . " ";

                    
                }
                
            }



            $this->_Prepares['Update'][] = $Prepares;

            $this->_Queries['Update'][] = $Queries;


            $Query = \str_replace('%SET%', implode(', ', $Set), $Query );


            // var_dump('Update ///////////', $Query );

            $this->_Blueprints['Update'][] = $Query;

        }

        return $this;
        
    }
    






    /**
     * @Type \Database\EntityManager
     * @Var Mixed
     */
    public function Delete($Entry){
        
        $this->_Blueprints['Delete'] = $this->_Blueprints['Delete'] ?: [];

        if($this->Entity){

            
            
            $Query = $this->Blueprints['Row:Delete'];
            
            $Query = \str_replace('%TABLE%', $this->Table, $Query );


            if(\is_string($Entry) || \is_numeric($Entry)){
                
                $Key = $this->GetPrimaryKey();
                
                $Query = \str_replace('%WHERE%', ("`" . $Key . "` = " . $Entry . " "), $Query );
    
            }


            if(\is_array($Entry)){

                $Multiple = [];

                foreach($Entry as $Name => $Value){

                    $Multiple[] = (" `" . $Name . "` = " . $Value . " ");
                    
                }

                $Query = \str_replace('%WHERE%', (implode(" AND ", $Multiple)), $Query );
                
            }
            
            // var_dump($Query);

            $this->_Blueprints['Delete'][] = $Query;

        }

        return $this;
        
    }








    /**
     * @Type \Database\EntityManager
     */
    public function Apply(Bool $WithEvent = false, ?Object $Provider = null){

        $this->Traces = $this->Traces ?: (Object) [];

        $this->Traces->Insert =  $this->Traces->Insert ?: [];

        $this->Traces->Delete = $this->Traces->Delete ?: [];

        $this->Traces->Update = $this->Traces->Update ?: [];



        if(\is_array($this->_Blueprints['Insert'])){

            foreach( $this->_Blueprints['Insert'] as $Key => $Blueprint){

                if(is_object($Insert = $this->Driver->Execute($Blueprint))){

                    $this->Traces->Insert[] = $Insert;

                    if(\method_exists($this->Entity, 'BindInsertion') && $WithEvent === true){

                        $this->Entity->BindInsertion(
                            
                            $Insert->Connexion->lastInsertId()
                            
                            , $this->_Prepares['Insert'][$Key] ?: null

                            , $Provider
                        
                        );
                        
                    }

                }
                
            }

            $this->_Blueprints['Insert'] = [];
            
        }



        if(\is_array($this->_Blueprints['Delete'])){

            foreach( $this->_Blueprints['Delete'] as $Key => $Blueprint){

                if(is_object($Delete = $this->Driver->Execute($Blueprint))){

                    $this->Traces->Delete[] = $Delete;

                }

            }

            $this->_Blueprints['Delete'] = [];
            
        }



        if(\is_array($this->_Blueprints['Update'])){

            foreach( $this->_Blueprints['Update'] as $Key => $Blueprint){

                if(is_object($Update = $this->Driver->Execute($Blueprint))){

                    $this->Traces->Update[] = $Update;


                    
                    if(\method_exists($this->Entity, 'BindUpdating') && $WithEvent === true){
                        
                        $this->Entity->BindUpdating(
                            
                            $this->_Prepares['Update'][$Key]['iD']
                            
                            , $this->_Prepares['Update'][$Key] ?: null

                            , $Provider
                        
                        );
                        
                    }


                }
                
            }

            $this->_Blueprints['Update'] = [];
            
        }


        return $this;
        
    }








    /**
     * @Type \Database\Transactions
     */
    public function CreateTable(){


        $_Fields = [];

        $Types = $this->FieldTypes();

        $iD = $this->Fields[0]->name ?: null;

        
        $Blueprint = $this->Blueprints['Table:Create'];
        
        $Blueprint = \str_replace('%TABLE%', $this->Table, $Blueprint);
        
        $Blueprint = \str_replace('%CHARSET%', $this->Driver->Settings->{'DB:Charset'}, $Blueprint);

        foreach($this->Fields as $Field){

            if($Field->type){

                $Type = $Types->{ $Field->type[0][0] } ?: $Field->type[0][0] ?: 'varchar';


                $Length = ($Field->type[0][1] ?: false) 
                    
                    ? "(" . $Field->type[0][1] . ")"

                    : ""
                    
                ;

                $Nullable = ($Field->type[0][2] ?: false) 
                    
                    ? "DEFAULT " . $Field->type[0][2] . ""

                    : "NOT NULL"
                    
                ;


                $AutoInc = (strtolower($Field->name) == 'id')
                    
                    ? "AUTO_INCREMENT PRIMARY KEY"

                    : ""
                    
                ;
            
                $_Fields[] = "`" . $Field->name . "` " . $Type . "" . $Length . " " . $Nullable . " " . $AutoInc . " ";
                
            }


        }
        
        $Blueprint = \str_replace('%FIELDS%', \PHP_EOL . implode(',' . \PHP_EOL, $_Fields), $Blueprint);


        return $this->Driver->Execute($Blueprint);
        
    }



}