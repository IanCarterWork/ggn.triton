<?php

namespace Framework\Sense;


	use GGN\xDump;

	use GGN\Patterns;

	use GGN\Encryption;

	use GGN\Strings;

	use GGN\Security;

	use GGN\Ui;



$Build['Tag/Head'] = [

	'Pattern' => Patterns\Tags::Logic('sense-head')

  	, 'Worker' => function(Object &$Engine, String $Cache = '', Array $Args = []){

		$Attr = Patterns\Attributes::Get($Args[1]);

		if(is_object($Attr) && isset($Attr->Name)){

			$Engine->Head .= $Engine->Swap($Engine->Theme->Layer('Head/' . $Attr->Name));

		}

		$Engine->Head .= $Args[2];

		return str_replace($Args[0], '', $Cache);

    }

];


$Build['Tag/Security'] = [

	'Pattern' => Patterns\Tags::Single('sense-security')

  	, 'Worker' => function(Object &$Engine, String $Cache = '', Array $Args = []){

		$Attr = Patterns\Attributes::Get($Args[1]);

		if(is_object($Attr) && isset($Attr->Type)){

          	switch(strtolower($Attr->Type)){

              	case 'token':

					unset($Attr->Type);
					
					$Attr->Duration = $Attr->Duration ?: 60;

          			//$Engine->Assigners->{'Security:Token:' . ($Attr->Name ?? 'Default')} = (new Security\Token((Array) $Attr))->Create();

                	$Engine->Assigners->{'Security:Token:' . ($Attr->Name ?? 'Default')} = Security\Token::Create($Attr);

                break;

            }

		}

     	return str_replace($Args[0], '', $Cache);

    }

];


$Build['Tag/Settings/Single'] = [

	'Pattern' => Patterns\Tags::Single('sense-settings')

  	, 'Worker' => function(Object &$Engine, String $Cache = '', Array $Args = []){

		$Attr = Patterns\Attributes::Get($Args[1]);

		if(is_object($Attr) && isset($Attr->Name) ){


			$Important = (isset($Attr->Important) && ($Attr->Important === true));


          	$Engine->Settings->{$Attr->Name} = $Attr->Value;

          	$Engine->Assigners->{$Attr->Name} = $Attr->Value;


          	if($Important === true){

              	$Engine->UserAssigner->{$Attr->Name} = $Attr->Value;

            }


          	if($Attr->Name == 'Theme:Palette'){

              	$Engine->Theme->Palette = $Attr->Value;

              	$Engine->SetColoring();

            }


          	if($Attr->Name == 'Theme:Tone'){

              	$Engine->Theme->Tone = $Attr->Value;

              	$Engine->SetColoring();

            }


		}

     	return str_replace($Args[0], '', $Cache);

    }

];



$Build['Tag/Settings/Logic'] = [

	'Pattern' => Patterns\Tags::Logic('sense-settings')

  	, 'Worker' => function(Object &$Engine, String $Cache = '', Array $Args = []){

		$Attr = Patterns\Attributes::Get($Args[1]);

		if(is_object($Attr) && isset($Attr->Name) ){


			$Important = (isset($Attr->Important) && ($Attr->Important === true));


          	$Engine->Settings->{$Attr->Name} = $Args[2];

          	$Engine->Assigners->{$Attr->Name} = $Args[2];


          	if($Important === true){

              	$Engine->UserAssigner->{$Attr->Name} = $Args[2];

            }


          	if($Attr->Name == 'Theme:Palette'){

              	$Engine->Theme->Palette = $Args[2];

              	$Engine->SetColoring();

            }


          	if($Attr->Name == 'Theme:Tone'){

              	$Engine->Theme->Tone = $Args[2];

              	$Engine->SetColoring();

            }


		}

     	return str_replace($Args[0], '', $Cache);

    }

];



$Build['Tag/Responsive'] = [

	'Pattern' => Patterns\Tags::Single('sense-responsive')

  	, 'Worker' => function(Object &$Engine, String $Cache = '', Array $Args = []){

		$Attr = Patterns\Attributes::Get($Args[1]);

		if(is_object($Attr) && isset($Attr->Active) && $Attr->Active == true){

			return str_replace($Args[0], '<meta name="viewport" content="width=device-width, initial-scale=1.0">', $Cache);

		}

      	else{

          	return str_replace($Args[0], '', $Cache);

        }

    }

];



$Build['Tag/View'] = [

	'Pattern' => Patterns\Tags::Single('sense-view')

  	, 'Worker' => function(Object &$Engine, String $Cache = '', Array $Args = []){

		$Attr = Patterns\Attributes::Get($Args[1]);

		if(is_object($Attr) && isset($Attr->Path)){

          	$Attr->Path = $Engine->Assigner($Attr->Path . Ui::Ext);

          	if(is_file($Attr->Path)){

              	if($Engine::UsesAjax()){

                  	if(isset($Attr->Ajax) && $Attr->Ajax === true){

                        return str_replace(

                            $Args[0]

                            , $Engine->Swap(file_get_contents($Attr->Path))

                            , $Cache

                        );

                    }

                  	else{

                      	return str_replace($Args[0], '', $Cache);

                    }

                }

              	else{

                    return str_replace(

                        $Args[0]

                        , $Engine->Swap(file_get_contents($Attr->Path))

                        , $Cache

                    );

                }


            }

		}


      	return str_replace($Args[0], 'Error : ' . ($Attr->Path), $Cache);

    }

];



$Build['Tag/Framework'] = [

	'Pattern' => Patterns\Tags::Logic('sense-framework')

  	, 'Worker' => function(Object &$Engine, String $Cache = '', Array $Args = []){

		$Attr = Patterns\Attributes::Get($Args[1]);

		if(is_object($Attr) && isset($Attr->Name) ){

          	$NS = '\\Framework\\' . $Attr->Name . '\SenseUiEngine';

          	$Attr->Palette = '{{Theme:Palette}}';

          	$Attr->Tone = '{{Theme:Tone}}';

			return str_replace($Args[0], ((new $NS)->Graft($Engine, $Attr, $Args[2])), $Cache);

		}

      	else{

          	return str_replace($Args[0], '', $Cache);

        }

    }

];

/* 
$Build['Tag/SplashScreen'] = [

	'Pattern' => Patterns\Tags::Logic('sense-splash')

  	, 'Worker' => function(Object &$Engine, String $Cache = '', Array $Args = []){

		$Attr = Patterns\Attributes::Get($Args[1]);

      	$iD = 'sense-splash-' . time();

      	$Delay = (isset($Attr->Delay)) ? $Attr->Delay : '360';

      	$Payload = (isset($Attr->Payload)) ? 'true' : 'false';


      	$H = '';

      	$H .= '<div style="display:flex;flex-direction:column;align-items:center;justify-content:center;position:fixed;top:0px;left:0px;z-index:999;width:100vw;height:100vh;opacity:1;transform:scale(1);transition:all ' . $Delay . 'ms ease-in-out;" Sense:Splash:Screen="' . $iD . '" class="sense-splashscreen ' . (isset($Attr->Class) ? $Attr->Class : 'ui-bg-layer') . '" id="' . $iD . '">';

      	$H .= $Args[2]?:'...';

      	$H .= '</div>';


      	$H .= '<script>(function(iD){';

      	$H .= '$Settings = $Settings||{};';

      	$H .= '$Settings.UsePayloadData = ' . $Payload . ';';

      	$H .= 'var e=document.querySelector(iD);';

      	$H .= '$Settings.SplashScreen = e;';

      	$H .= '$Settings.SplashScreenDelay = ' . $Delay . ';';


      	if($Payload=='false'){

            $H .= 'window.addEventListener("load",()=>{';

            $H .= 'e.style.opacity="0.001";';

            $H .= 'e.style.webkitTransform="scale(1.5)";';

            $H .= 'e.style.transform="scale(1.5)";';

            $H .= 'setTimeout(()=>{';

            $H .= 'e.style.display="none";';

            $H .= '},' . $Delay . ');';

            $H .= '});';

        }

      	$H .= '})("#' . $iD . '");</script>';


		return str_replace($Args[0], $H, $Cache);

    }

];
 */