<?php

namespace Core\BiN\Console;


    use Core\BiN\ConsoleSheet;


Class Server extends ConsoleSheet{


    public function Set(){

        global $GGN;




        // var_dump($this->Console->Input, $_SERVER['argv'] );


        switch(\strtolower( $this->Console->Input[2] ?: null )){

            case 'start':

                if(is_object($GGN->{'System:Config'})){

                    if($GGN->{'System:Config'}->Mode == ':dev'){


                        $Host = ""

                            . ($GGN->{'System:Config'}->Development->Host ?: "127.0.0.1")
                            
                            . ":"

                            . ($this->Console->Input[3] ?: $GGN->{'System:Config'}->Development->Port ?: "80")
                            
                        ;

                        $Root = ($GGN->{'System:Config'}->{'Path:Public'} ?: "public_html");

                        $this->Console

                            ->Line("Démarrage du server de développement")
                        
                            ->Exec(""

                                . "cd " 
                            
                                . $Root
                                
                                . " && " 

                                . "php -S " 

                                . $Host
                            
                                . " server.php"

                                // . " -t " . $Root . "/"

                                // . " & " . $this->Console->Open("http://" . $Host, true)

                                // . " & stunnel -d 443 -r " . ($GGN->{'System:Config'}->Development->Port ?: "80")

                                . " "
                            )
                            
                        ;
                        
                        
                    }
                    
                    else{ $this->Console->Line("Variable d'environnement < System:Config > introuvable!"); }

                }

                else{ $this->Console->Line("Variable d'environnement < System:Config > introuvable!"); }

            break;





            default:

                $this->Console->Line('Aucune entrée détectée');
            
                // var_dump($this->Input);

            break;
            
            

        }


        return $this;

    }
    
    
}