<?php

	namespace Framework\JsCore;

		use GGN\xDump;

		use Framework\CSS;

		use GGN\Patterns;




	class SenseUiEngine{


      	//var $NAME = 'JsCore';

      	var $BaseUrl = '';


		public function Script(String $Path, ?String $Version = null): String{

          	$Version = is_string($Version) ? ('-' . $Version) : ('');

			return '<sense-asset type="file/js" src="' . $this->BaseURL . $Path . $Version . '.js"></sense-asset>'

			;

        }

          	public function Graft(

              	Object $Engine

              	, Object $Attributes

              	, String $Content

            ){

              	global $GGN;


				$Version = $Attributes->Version ?? '';

              	$Output = [];

              	$this->BaseURL = $GGN->{'Http:Host'} . 'assets/js/ggn/';


              	$Output[] = (!empty($Version)) ? $this->Script('core/' . $Version) : '';


				if(preg_match_all(Patterns\Tags::Single('mod'), $Content, $GetMods, \PREG_SET_ORDER)){

					foreach($GetMods as $GetMod){

						if($Mod = Patterns\Attributes::Get($GetMod[1])){

                          	if(isset($Mod->Name)){

                          		$Output[] = $this->Script('mods/' . $Mod->Name, $Mod->Version??null);

                            }

                        }

					}

				}




				return implode('',$Output);


            }


	}