<?php



namespace Framework\Ability;


	use GGN\EObject;


	trait ScreenSize{



      	var $ScreenDivide = 16;




      /* Media Queries : Largeur d'Ecran, update(1908.20) */

		var $ScreenSizes = [

			'dB' => 320

			,'dP' => 480

			,'dS' => 768

			,'dM' => 960

			,'dL' => 1200

			,'dU' => 1440

			,'dF' => 1920

			,'d2k' => 2048

			,'d4k' => 4096

			,'d8k' => 7680

			,'d16k' => 15360

		];







      	public function MediaScreen(String $Media, Array $Selectors = []):self{


			if(isset($this->Selector) && is_array($this->Selector)){

          		$this->Selector[] = '@media(' . $Media . '){';

              		foreach($Selectors as $Selector => $Value){

                      	$this->Selector($Selector, $Value);

                    }

          		$this->Selector[] = '}';

          	}

          	return $this;

        }





      	public function SetMediaScreen(Object $Sizes, $Fn, $Values = false):self{

            if($Fn){

				$Value = null;

				  $ScreenSizes = $this->ScreenSizes;

				  $Sizes = array_reverse((Array) $Sizes);
				  
				//   var_dump($ScreenSizes);

              	$AutoValuesTrigger = is_array($Values);

              	$this->ScreenSizeFn = $Fn;

              	($this->ScreenSizeFn)('', false, ($AutoValuesTrigger ? $Values[0] : null) );


          		foreach($Sizes as $Key){

					$Key = substr($Key, 0, 1) . strtoupper(substr($Key, 1,2)) . substr($Key, 2);

                  	$Size = $ScreenSizes[$Key]??null;

					//   var_dump($Key,$Size);

                  	if(!is_numeric($Size)){continue;}

					  
					if(isset($this->Selector) && is_array($this->Selector)){

                  		$this->Selector[] = '@media(max-width:' . $Size . 'px){';

                	}

                  	if($AutoValuesTrigger === true){

						$Value = (($Value === null) ? $Values[0] : $Value) + $Values[1];

                    }

					($this->ScreenSizeFn)(strtolower($Key), $Size, $Value);

					if(isset($this->Selector) && is_array($this->Selector)){

                  		$this->Selector[] = '}';

                	}



                }

            }

          	return $this;

        }






    }






?>