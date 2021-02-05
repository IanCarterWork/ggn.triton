<?php

	namespace Framework\CSS;

		use GGN\xDump;

		use GGN\Patterns;



      	class SenseUiEngine{


          	var $NAME = 'CSS.Core';

          	var $ARC = 'csscore';


			public function Link(String $Path, ?String $Version = null): String{
				
				$Version = is_string($Version) ? ('-' . $Version) : ('');

				return '<link rel="StyleSheet" type="text/css" sense-framework="'

                  	. $this->NAME . '" href="'

                  	. $this->BaseURL . $Path . $Version

                  	. '?palette={{Theme:Palette}}'

                  	. '&tone={{Theme:Tone}}'

                  	. '&pseudo=' . $this->Pseudo

                  	. '&media-screen=' . $this->MediaScreen

					. '" >'
					  
				 ;
				
			}

          	public function Graft(

              	Object $Engine

              	, Object $Attributes

              	, String $Content

            ){

              	global $GGN;


				$Version = $Attributes->Version ?? '';

				$this->Palette = $Attributes->Palette ?? $Engine->Settings->PaletteName ?? 'default';

				$this->Tone = $Attributes->Tone ?? $Engine->Settings->ToneName ?? 'dark';

              	$this->Pseudo = $Engine->Settings->{'Pseudo:Class'} ?? '';

              	$this->MediaScreen = $Engine->Settings->{'Media:Screen'} ?? '';

				$this->BaseURL = $GGN->{'Http:Host'} . 'css/' . $this->ARC . '/';

				$Output = '';



				if(preg_match(Patterns\Tags::Single('pseudo-class'), $Content, $GetPseudo, \PREG_SET_ORDER)){

					$Psdo = Patterns\Attributes($GetPseudo[1]);

                  	$this->Pseudo = $Psdo->value ?? '';

                }


				if(preg_match(Patterns\Tags::Single('media-screen'), $Content, $GetMS, \PREG_SET_ORDER)){

					$MS = Patterns\Attributes($GetMS[1]);

                  	$this->MediaScreen = $Psdo->value ?? '';

				}


				
				if(is_string($Version) && !empty($Version)){

					$Output .= $this->Link('core/' . $Version, null);
					
				}


				if(preg_match_all(Patterns\Tags::Single('mod'), $Content, $GetMods, \PREG_SET_ORDER)){

					foreach($GetMods as $GetMod){

						if($Mod = Patterns\Attributes::Get($GetMod[1])){

                          	if(isset($Mod->Name)){

								$Output .= $this->Link('mods/' . $Mod->Name, $Mod->Version);

                            }

                        }

					}

				}

              	return $Output;

            }


        }