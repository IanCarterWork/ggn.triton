<?php

	namespace GGN;



	/**
     * Gestionnaire des Paramètres
     *
    */

    if(!class_exists("\\" . __NAMESPACE__ . "\Settings")){

        class Settings{

          	//var $Failed;

          	public function __construct($Data = null){

              	if(is_array($Data) || is_object($Data)){

                  	foreach($Data as $Key => $Value){

                      	$this->{$Key} = $Value;

                    }

                }

              	else{

                  	$this->__FAILED__ = TRUE;

                }

            }



            public function Save($Path){

                global $GGN;

                $iPath = $GGN->{'Dir:Settings'} . $Path . '.settings';

                $Dir = dirname($iPath);

                if(!\is_dir($Dir)){

                    \mkdir($Dir, 0777, true);
                    
                }

                // var_dump($Path, $iPath, \json_encode($this) );exit;

                return \file_put_contents($iPath, \json_encode($this));
                
            }
            
            

            static public function Parse($Data, ?Array $Inject = null, Bool $Trace = false){

                global $GGN;

                $Out = [];

                // var_dump('Parse ///', $Data);

                if(\is_object($Data) || is_array($Data)){

                    foreach($Data as $Key => $Value){

                        // var_dump($Key, $Value);

                        if(!is_array($Value) && !\is_object($Value)){
    
                            foreach($GGN as $k => $v ){
    
                                if(!is_array($v) && !\is_object($v)){

                                    if($Trace == true){

                                        var_dump('--->', $Key, $k,$v, $Value, str_replace('{{' . $k . '}}', ($v ?: ''), $Value) );
    
                                    }
    
                                    $Out[$Key] = str_replace('{{' . $k . '}}', ($v ?: ''), $Value);
        
                                }

                                if(!(!is_array($v) && !\is_object($v))){

                                    $Out[$Key] = $Value;
        
                                }
    
                            }
    
                        }
    
                        else{
    
                            if( (\is_object($Value) || is_array($Value)) ){
    
                                if(\is_object($Value)){
    
                                    $Out[$Key] = (Object) self::Parse((Array) $Value, $Inject, $Trace);
                                
                                    if(\is_object($Inject)){
    
                                        foreach($Inject as $k => $v ){
    
                                            $Out[$Key] = str_replace('{{' . $k . '}}', $v ?: '', $Value);
                    
                                        }
    
                                    }
    
                                }
    
                                else{
    

                                    $Out[$Key] = self::Parse($Value, $Inject, $Trace);
                                
                                    if(\is_object($Inject)){
    
                                        foreach($Inject as $k => $v ){
    
                                            // var_dump('--->', $Key, $k,$v, $Value);
    
                                            $Out[$Key] = str_replace('{{' . $k . '}}', $v ?: '', $Value);
                    
                                        }
    
                                    }
    
                                }
                                
                            }
    
                            else{

                                $Out[$Key] = $Value;
                            
                            }
                            
                        }
                        
                    }
    
                }

                if(!(\is_object($Data) || is_array($Data)) ){

                    $Out = $Data;
                    
                }



                if(\gettype($Data) == 'object'){ $Out = (Object) $Out; }


                return $Out;
                
            }


            static public function ParseString(String $String){

                global $GGN;

                foreach($GGN as $k => $v ){
    
                    if(is_string($v)){
                        
                        $String = str_replace('{{' . $k . '}}', ($v ?: ''), $String);

                    }
                    

                }

                return $String;
                
            }

            
            static public function Get(String $Name){

                global $GGN;

                $Dat = NULL;
                  
              	$Data = [];

                $Path = $GGN->{'Dir:Settings'} . $Name . '.settings';


                // echo '<pre>';
                // var_dump($Name, $Path);
                // echo '</pre>';

                // echo '<pre>';

                if(is_file($Path)){

                    $Dat = json_decode(file_get_contents($Path));

                    $Type = \gettype($Dat);


                    foreach($Dat as $Key => $Value){

                        // var_dump($Key, $Value);

                        if($Value != null && (\is_object($Value) || is_array($Value))){

                            if(\is_object($Value)){

                                $Data[$Key] = (Object) self::Parse((Array) $Value);
    
                            }
    
                            else{
    
                                $Data[$Key] = self::Parse((Array) $Value);
    
                            }

                        }

                        else{

                            $Data[$Key] = $Value;

                        }
                        
                    }


                    if($Type == 'object'){

                        $Data = (Object) $Data;
                        
                    }


                }



                // var_dump($Data);
                // echo '</pre>';
                // exit;

                return new self($Data);

            }

        }

    }