<?php


namespace Framework\Ability;


  	trait ColoriMetry{


    	var $ColorVarianteLite = 4;

    	var $ColorVariante = 12;

    	var $ColorVarianteHigh = 20;

    	var $ColorVarianteHighPlus = 127;





      	public function ColorByIntensity(String $hex, $Variante = null){

          	$color = $this->CorrectHexColor($hex);

          	$Intensity = $this->GetColorIntensity($color);


          	return (

              	($Intensity === true)

                 	? $this->ColorVariante($color, -1 * ($Variante ?? $this->ColorVarianteHigh))

                 	: $this->ColorVariante($color, ($Variante ?? $this->ColorVarianteHigh))

           	);

        }



        /* Obtenir l'intensité d'une couleur, update(160204.1439) */
        public function GetColorIntensity(String $hex){

            /*
                Limit en les 2 seuils majeurs
            */
            $median = 128;


            /*
                Correction du code couleur
            */
            $color = $this->CorrectHexColor($hex);


            /*
                Conversion RVB
            */
            $rgb = $this->toRGB($color);


            /*
                Si la conversion est vérifiée
            */
            if(is_array($rgb)){

                /*
                    Compteurs de hit
                */
                    $dark = 0;

                    $light = 0;

                    $middle = 0;


                /*
                    Comparaison : Verification des couleurs composantes
                */
                    foreach ($rgb as $k => $c) {

                        if($c < $median){$dark +=$c;}

                        if($c > $median){$light +=$c;}

                        if($c == $median){$middle +=$c;}

                    }


                $inst = new \GGN\EObject([

                    'dark'=>$dark

                    ,'light'=>$light

                    ,'middle'=>$middle

                    // ,'is'=> ($dark > $light ? true : false)

                    ,'is'=> (($dark > $light && $dark > $middle) ? true : (($light > $middle) ? false: null))

                ]);

                return $inst;

            }


            /*
                Sinon : echec
            */
            else{

                return false;

            }


        }






        /* Obtenir une variante (foncé ou claire) d'une couleur, update(160204.1439) */
        public function ColorVariante($hex, $coef = 10){

            /*
                Correction du code couleur
            */
            $color = $this->CorrectHexColor($hex);



            /*
                Conversion en RVB
            */
            $rgb = $this->toRGB($color);



            /*
                Si la conversion est vérifiée
            */
            if(is_array($rgb)){

                /*
                    Initialisation de la nouvelle couleur
                */
                $r = [0,0,0];

                /*
                    Détermination de la variante
                */
                foreach ($rgb as $k => $c) {

                    $v = $c + $coef;

                    $r[$k] = ($v<0) ? 0 : ( ($v>255) ? 255: $v); /* Regulation et assignation */

                }

                /*
                    Re-conversion en Hexa
                */

                return $this->toHEX($r);

            }

            /*
                Sinon : echec
            */
            else{

                return false;

            }


        }







        /* Correction du code Hexa de la couleur, update(160204.1439) */

        public function isColor($Color){

            $Color = \str_replace('#', '', $Color);

            return ctype_xdigit($Color) && (strlen($Color) == 6 || strlen($Color) == 3);
            
        }
        
        public function isColorName($Name){

            return !(
            
                preg_match("/^([a-zA-Z].*)Name$/s", $Name)

                || preg_match("/^([a-zA-Z].*)Version$/s", $Name)

                || preg_match("/^([a-zA-Z].*)Update$/s", $Name)

                || strpos($Name, ':RGB') != false

            );
            
        }
        
        public function isColorVar($Name, $Color){

            return 
            
                $this->isColor($Color) && $this->isColorName($Name)
        
            ;
            
        }
        
        public function CorrectHexColor($hex){

            $ihex = (substr($hex, 0,1)=='#') ? substr($hex, 1) : $hex;

            $lenhex = strlen($ihex);

            return (

              	($lenhex===3) ? '#' . $ihex . $ihex:

              		(($lenhex===2) ? '#' . $ihex . $ihex . $ihex :

                     	($lenhex===1 ? '#' . $ihex . $ihex . $ihex . $ihex . $ihex . $ihex : substr($hex, 0,7) )

                    )
            );


        }





      	public function toHEX($Red = 0, Int $Green = 0, Int $Blue =0){

          	$hex = "#";


          	switch(gettype($Red)){

              	case 'string':

                	$Ex = [];

                	$Exo = explode(",", $Red);

                	$Ex = array_merge($Ex, $Exo);

                		$Ex0 = $Exo[0];


                	$Exo = explode("|", $Red);

                	$Ex = array_merge($Ex, $Exo);

                		$Ex1 = $Exo[0];


                	$Exo = explode(" ", $Red);

                	$Ex = array_merge($Ex, $Exo);

                		$Ex2 = $Exo[0];




                	$Red = (is_numeric($Ex0))

                      	? $Ex0 : (is_numeric($Ex1)

                        	? $Ex1 : (is_numeric($Ex2) ? $Ex2 : $Ex[0]));


                	$Green = (isset($Ex[1]) && $Green != 0) ? $Ex[1] : 0;

                	$Blue = (isset($Ex[2]) && $Blue != 0) ? $Ex[2] : 0;

                break;

              	case 'array':

                	$Green = (isset($Red[1])) ? $Red[1] : 0;

                	$Blue = (isset($Red[2])) ? $Red[2] : 0;

                	$Red = $Red[0];

                break;

            }

          	$hex.= str_pad(dechex($Red), 2, "0", STR_PAD_LEFT);

          	$hex.= str_pad(dechex($Green), 2, "0", STR_PAD_LEFT);

          	$hex.= str_pad(dechex($Blue), 2, "0", STR_PAD_LEFT);


          	//var_dump("ToHex", $hex, $Red, $Green, $Blue);

          	return $hex;

        }




      	public function toRGB(String $HEX, Bool $toString = false){

            $hex = str_replace("#", "", $HEX);

          	$row = strlen($hex);

          	$x3 = ($row===3);

          	$x6 = ($row===6);

          	$r=false;$g=false;$b=false;


             if($x3===true){

               	$r = hexdec(substr($hex,0,1).substr($hex,0,1));

               	$g = hexdec(substr($hex,1,1).substr($hex,1,1));

               	$b = hexdec(substr($hex,2,1).substr($hex,2,1));

             }

              if($x6===true){

                $r = hexdec(substr($hex,0,2));

                $g = hexdec(substr($hex,2,2));

                $b = hexdec(substr($hex,4,2));

              }


             return ($toString === true) ? $r . ',' . $g . ',' . $b : [$r,$g,$b];


        }



    }

