<?php

namespace Framework\Mokian;

    use Framework\Mokian;

    use Framework\Application;


class Mesa{


    public const APP_LAUNCHER_ATTRIBUTES = [

        'Mokian:Mesa:Config' => 'config.json'

        , 'Mokian:Mesa:Index' => 'boot.js'

    ];
    

    static public function RootFeatures(Application $App) : ?string{

        $Out = null;

        if($App->Name){

            $Out = '';

            foreach(self::APP_LAUNCHER_ATTRIBUTES as $Attribute => $Type){

                $Out .= ' ' . $Attribute . '="' 
                
                    . $App->Settings->{'Http:Host'} 

                    . Mokian::PATH . 'apps/'
                    
                    . $App->Name 

                    . (is_string($Type) ? ('.' . $Type) : '')
                    
                    . '" '
                    
                ;
                
            }

        }
        
        return $Out;
        
    }



    static public function Handler(Application $App, ?Array $Arguments = null){

        
        $App->Settings->{"Navigation:UseHash"} = false;

        if(Application::UsesAjax() !== TRUE){
            
            $App->Include('Head', false);
            
        }
        
        if(Application::UsesAjax() === TRUE){

            $App->View($App->Settings->{'View:Current'} ?: null, true, $Arguments, '404' );
    
        }
    
        if(Application::UsesAjax() !== TRUE){
    
            $App->Include("Foot", false);
    
        }

        
    }



    
}