<?php

	namespace Framework\CSS;

		use GGN\xDump;



	if(!class_exists("\\" . __NAMESPACE__ . "\SenseDOMEngine")){

      	class SenseDOMEngine{


          	var $NAME = 'CSS.Core';

          	var $ARC = 'csscore';


          	public function Graft(

              	Object $Engine

              	, \DOMDocument $DOM

              	, \DOMElement $Tag

            ){

              	global $GGN;


				$Version = $Tag->getAttribute('version');

				$Palette = $Tag->getAttribute('palette');

				$Tone = $Tag->getAttribute('tone');

				$Mods = [];


				$GetMods = $Tag->getElementsByTagName('mod');

				if($GetMods->length > 0){

					foreach($GetMods as $Mod){

						$Mods[] = $Mod->getAttribute('name');

					}

				}


				$GetPseudo = $Tag->getElementsByTagName('pseudo-class');


              	$Url = ''

                  	. $GGN->{'Http:Host'}

              		. '' . ($this->ARC) . '?version=' . ((is_string($Version) && !empty($Version)) ? $Version : '0.0.1')

                  	. '&mods=' . (implode(';', $Mods))

                  	. '&palette=' . ((is_string($Palette) && !empty($Palette)) ? $Palette : ($Engine->Settings->PaletteName ?? 'default') )

                  	. '&tone=' . ((is_string($Tone) && !empty($Tone)) ? $Tone : ($Engine->Settings->ToneName ?? 'light') )

                  	. '&pseudo=' . (($GetPseudo->length > 0) ? $GetPseudo->item(0)->getAttribute('value') : '')

             	;

				$New = $DOM->createElement('link');

              	$New->setAttribute('rel', 'StyleSheet');

              	$New->setAttribute('type', 'text/css');

              	$New->setAttribute('sense-framework', ($this->NAME));

              	$New->setAttribute('href', $Url);

				return $New;

            }


        }

    }