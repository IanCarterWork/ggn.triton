<?php


namespace Framework\Ability;


	trait StyleSheet{


      	//var $Color = [];

      	var $Selector = [];



      	public function KeyFrames(String $Key, Array $Props = []){

          	$Code = "";

            if(!empty($Props)){

              	foreach($Props as $Level => $Pr){

                  	$Code .= '' . $Level . '{';

                    foreach($Pr as $Name => $Prop){

                        if(is_string($Prop)){

                            $Code .= $this->Property($Name, $Prop);

                        }

                        if(is_array($Prop)){

                            foreach($Prop as $Pro){

                                if(is_string($Pro)){

                                    $Code .= $this->Property($Name, $Pro);

                                }

                            }

                        }

                    }

                  	$Code .= '}';

                }

            }


          	$this->Selector[] = '@-webkit-keyframes ' . $Key . ' {' . $Code . '}';

          	$this->Selector[] = '@-moz-keyframes ' . $Key . ' {' . $Code . '}';

			$this->Selector[] = '@keyframes ' . $Key . ' {' . $Code . '}';


			return $this;

        }


       	public function Selector(String $Selector, Array $Props, Bool $Return = false, Bool $BrowserPrefix = true){

			$Code = "";

            if(!empty($Props)){

              	$Code .= "" . $Selector . "{";

              	foreach($Props as $Name => $Prop){

                  	if(is_string($Prop)){

                      	$Code .= $this->Property($Name, $Prop, $BrowserPrefix);

                    }

                  	if(is_array($Prop)){

                  		foreach($Prop as $Pro){

                          	if(is_string($Pro)){

                  				$Code .= $this->Property($Name, $Pro, $BrowserPrefix);

                            }

                		}

                    }

                }

              	$Code .= "}";

              	$this->Selector[] = $Code;

            }


          	if($Return === TRUE){

              	return $Code;

            }


          	if($Return !== TRUE){

              	return $this;

            }


       	}






      	public function Mount($Return = false){

          	$Out = "";

			foreach($this->Selector as $Selector){

              	$Out .= ($Selector);

            }

          	if($Return === true){ return $Out; }

          	if($Return === false){ echo $Out; }

        }






      	public function Color(String $Name, String $Alter = ""){

          	return (isset($this->Color->{$Name})) ? $this->Color->{$Name} : ($Alter);


        }















        /* Pour CSS3, update(160202.0750) */

        public function PropertiesPatterns(){

            return [

                // "/^(.*)-transform/"

                //, "/^transform-origin/"

                "/^appearance/"

                , "/^transition-(.*)/"

                , "/^transition/"

                //, "/^filter/"

                //, "/^backface-visibility/"

                //, "/^align-items/"

                //, "/^column-count/"

                //, "/^justify-content/"

                // , "/flex/"

                //, "/^flex-(.*)/"

                //, "/^columns/"

                //, "/^column-(.*)/"

                //, "/^flex-direction/"

                //, "/^opacity/"

                // , "/^user-select/"

                //, "/^line-clamp/"

                //, "/^box-orient/"

                //, "/(.*)-radius/"

                //, "/^border-image/"

                //, "/(.*)-shadow/"

                , "/^animation-(.*)/"

                , "/^animation/"

                //, "/^background-clip/"

                //, "/^background-origin/"

                //, "/^background-size/"

                , "/(.*)-gradient/"

            ];

        }





      	public function PropertyPrefix(String $Prop, Array $Prefix = ["-webkit", "-moz"]){

            $r = false;

            foreach ($this->PropertiesPatterns() as $key => $Pattern) {

                if(preg_match_all($Pattern, $Prop, $Out, PREG_PATTERN_ORDER)){

                	$r = $Out[0][0];

                	break;

                }

            }

          	if(is_string($r) && !empty($Prefix)){

              	$Out = [];

          		foreach($Prefix as $Pre){

					$Out[] = $Pre . "-" . $r;

            	}

              	return $Out;

            }

          	else{

              	return $Prop;

            }


        }





      	public function Property(String $Prop, String $Value = "", Bool $BrowserPrefix = true){

			$Prefix = ($BrowserPrefix === true) ? $this->PropertyPrefix($Prop) : '';

          	$Out = "";


			if(is_array($Prefix) && !empty($Prefix)){

              	foreach($Prefix as $Pre){

                  	foreach(explode("&&", $Pre) as $Property){

                      $Out .= ltrim(rtrim($Property)) . ":" . ltrim(rtrim($Value)) . ";";


                    }

                }

            }


          	if(is_string($Prefix)){

             	foreach(explode("&&", $Prefix) as $Property){

                	$Out .= ltrim(rtrim($Property)) . ":" . ltrim(rtrim($Value)) . ";";

                }

            }


			return $Out;


        }





    }







?>