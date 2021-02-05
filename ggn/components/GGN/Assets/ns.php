<?php

namespace GGN;


    use GGN\Settings;

    // use GGN\Dial;

    // use GGN\Patterns\Annotations;




/**
 * Assets : Application Request Canonical
 */

 Class Assets{


    
    private $Settings;
    





    public function __construct(?Settings $Settings = null){

        $this->Settings = $Settings;

    }




    static public function Path(String $Dir, ?String $Path = null, ?String $Domain = null){

        global $GGN;

        return ($Domain ?: $GGN->{'Http:Host'}) . 'assets/' . $Dir . '/' . (($Path) ? ($Path) : '');

    }


    static public function CSS(String $Path, ?String $Domain = null){ return self::Path('css', $Path, $Domain); }

    static public function JS(String $Path, ?String $Domain = null){ return self::Path('js', $Path, $Domain); }

    static public function Sound(String $Path, ?String $Domain = null){ return self::Path('sounds', $Path, $Domain); }

    static public function Video(String $Path, ?String $Domain = null){ return self::Path('videos', $Path, $Domain); }

    static public function Font(String $Path, ?String $Domain = null){ return self::Path('fonts', $Path, $Domain); }

    static public function Document(String $Path, ?String $Domain = null){ return self::Path('documents', $Path, $Domain); }

    static public function Theme(String $Path, ?String $Domain = null){ return self::Path('themes', $Path, $Domain); }

    static public function Image(String $Path, ?String $Domain = null){ return self::Path('images', $Path, $Domain); }

    static public function xImage($Path, ?Array $Setter = null, ?String $Domain = null){

        global $GGN; 


        if($Path){

            if(is_string($Path)){

                return ($Domain ?: $GGN->{'Http:Host'}) 
            
                    . 'images/' 
                    
                    . $Path

                    . (($Setter) ? ('?set[]=' . \implode('&set[]=', $Setter) ): '')
                    
                ; 
                
            }


            if(\is_array($Path) || is_object($Path)){

                $Paths = [];

                foreach($Path as $File){

                    $Paths[] = ($Domain ?: $GGN->{'Http:Host'}) 
            
                        . 'images/' 
                        
                        . $Path

                        . (($Setter) ? ('?set[]=' . \implode('&set[]=', $Setter) ): '')
                        
                    ; 
                    
                }

                return implode('|', $Paths)?:null;
                
            }

        }

        return null;
    
    }
    
    
    
     
 }
