<?php

namespace GGN\Dir;


Class Scan {


    
    static public function Path(String $Path, Bool $Persist = false, Int $Order = 0) : ?Object{


        if(is_dir($Path)){ 
        

            $Scan = scandir($Path, $Order);

            $Out = [];


            if($Persist === true){

                // $Out = [];

                foreach($Scan as $Entry){

                    if(is_dir($Entry)){

                        $Out = array_merge($Out, ((Array) self::Path($Entry . '/')) );

                    }
                    
                    else{

                        $Out[] = $Path . '/' . $Entry;
                        
                    }
                    
                }
                
            }

            if($Persist === false){

                foreach($Scan as $Entry){

                    $Out[] = $Path . $Entry;

                }
                
            }

            if($Order !== SCANDIR_SORT_DESCENDING){

                unset($Out[0]);

                unset($Out[1]);
                
            }
            
            if($Order === SCANDIR_SORT_DESCENDING){

                $Length = count($Out);
                
                unset($Out[$Length - 1]);

                unset($Out[$Length - 2]);
                
            }
            
            return (Object) $Out;

        }

        else{

            return null;
            
        }


    }




}

