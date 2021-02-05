<?php

namespace GGN;

    use DateTime;

    use DateInterval;



class Dater{

    static public $_Lang = [

        's' => 'sec'

        , 'i' => 'min'

        , 'h' => 'h'

        , 'd' => 'jour'

        , 'm' => 'mois'

        , 'y' => 'an'

        , 'since' => 'Il y a '
        
    ];

    
    var $Instance;
    

    static public function Lang(String $Slug){

        $L = self::$_Lang;

        return $L[$Slug] ?: null;

    }

    static public function Since(String $To, ?String $From = null):String{

        $T = new DateTime($To);

        $F = new DateTime($From?:'now');

        $Since = $F->diff($T);

        return new self($Since);

    }



    public function __construct(?Object $Instance = null) {

        $this->Instance = $Instance;
        
    }


    public function __toString() {

        return $this->Format(null) ?: '';
        
    }
    

    public function Format(?Object $Instance = null):?String{

        $Instance = $Instance ?: $this->Instance ?: null;
        

        if($Instance instanceof DateInterval){

            $Out = self::Lang('since');

            if(is_object($Instance)){

                if($Instance->d < 1){  
    
                    if($Instance->h >= 1 && $Instance->h > 0){ $Out .= $Instance->h . self::Lang('h') . ' '; }
                    
                    if($Instance->i <= 60 && $Instance->i > 0){ $Out .= $Instance->i . self::Lang('i') . ' '; }
        
                    if($Instance->s <= 60 && $Instance->s > 0){ $Out .= $Instance->s . self::Lang('s') . ' '; }
                    
                }
        
                else{

                    // var_dump($Instance);
        
                    if($Instance->d >= 1){ $Out .= $Instance->d . self::Lang('d') . ' '; }
                    
                    if($Instance->m >= 1){ $Out .= $Instance->m . self::Lang('m') . ' '; }
        
                    if($Instance->y >= 1){ $Out .= $Instance->s . self::Lang('y') . ' '; }
                    
                }
        
                return $Out;
        
            }
    
        }


        return null; 
    
    }
    
    
}