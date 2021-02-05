<?php

	namespace Framework\Sense;

		use GGN\xDump;

		use GGN\Dial;

		use GGN\Structuring;

		use GGN\Patterns;


	/**
     * Class Engine
     *
    */

    if(!class_exists("\\" . __NAMESPACE__ . "\Engine")){

        class Engine implements Structuring\UiEngine{

            public function __construct(

              	String $Version = '0.0.1'

              	, String $Buffer = ''

              	, \GGN\Theme $Theme

              	, Object $Assigner

            ){


               	$this->Output = '';

               	$this->Head = '';

              	$this->Settings = new \stdClass;

                $this->Version = $Version;

                $this->Theme = $Theme;

                $this->Buffer = $Buffer;

              	$this->Path = dirname(__FILE__) . '/';

              	$this->UserAssigner = $Assigner;

              	$this->Assigners = (Object) [];


            }





			static public function UsesAjax(){

				return (isset($_SERVER['HTTP_X_REQUESTED_WITH']))

                  	? (strpos($_SERVER['HTTP_X_REQUESTED_WITH'], 'GGN:') === 0)

                  	: FALSE

              	;

			}




          	public function Rules(){

              	$this->RulesFile = $this->Path . '/Rules/' . $this->Version . '.php';

              	if(is_file($this->RulesFile)){

                  	$Build = [];

                  	include $this->RulesFile;

                  	$this->Rules = $Build;

                }

              	else{

                  	Dial\Warning(

                      	'Règles'

                      	, 'Sense Core'

                      	, 'Règle < ' . $this->Version . ' > introuvable'

                    );

                }

            }




          	public function ReAssigns(){

				foreach((Array) $this->UserAssigner as $Key => $Value){

                  	if($Key == 'Theme:Palette'){

                      	$this->Settings->{'Theme:Palette'} = $Value;

                      	$this->Theme->Palette = $Value;

                    }

                  	if($Key == 'Theme:Tone'){

                      	$this->Settings->{'Theme:Tone'} = $Value;

                      	$this->Theme->Tone = $Value;

                    }

					$this->Assigners->{$Key} = $Value;

					$this->Settings->{$Key} = $Value;

                }

              	return $this;

            }



          	public function Assigner(String $Data, Array $Inject = []):String{

				foreach(array_merge((Array) $this->Assigners, $Inject) as $Key => $Value){

                  	$Ex = explode(':', $Key);

                  	if($Ex[0] == 'Settings'){

                      	$this->Settings->{substr($Key, strlen('Settings:'))} = $Value;

                      	//continue;

                    }

                  	if($Ex[0] == 'Theme'){

                      	$this->Settings->{$Key} = $Value;

                      	$this->Theme->{substr($Key, strlen('Theme:'))} = $Value;

                    }

                  	$Data = str_replace('{{' . $Key . '}}', $Value, $Data);

                }

              	return $Data;

            }


          	public function Template(){


				$this->Template = null;

              	$this->TemplateFile = $this->Path . 'Templates/' . $this->Version . '.tpl';

              	$this->DefaultTemplateFile = $this->Path . 'Templates/0.0.1.tpl';

              	$TPLExists = is_file($this->Template);


              	if($TPLExists){

                  	$this->Template = file_get_contents($this->TemplateFile);

                }

              	else{

                  	$this->Template = file_get_contents($this->DefaultTemplateFile);

                }


              	return $this;

            }


          	public function SetColoring(){

              	/**
                 * Assignassion : Palette et Ton du Theme
                */


              	$this->Coloring = $this->Theme->Coloring();

              	foreach($this->Coloring as $Key => $Value){

                  	$this->Assigners->{'Coloring:' . $Key} = $Value;

                  	$this->Settings->{'Coloring:' . $Key} = $Value;

                }


              	return $this;

            }


          	public function Swap(String $Cache){

              	//$this->ReAssigns();

              	foreach($this->Rules as $Rule){

                  	if(preg_match_all($Rule['Pattern'], $Cache, $Process, \PREG_SET_ORDER)){

                      	foreach($Process as $Get){

                      		$Cache = (call_user_func_array($Rule['Worker'], [$this, $Cache, $Get] ));

                        }

                    }

                }

              	return $Cache;

            }


          	public function SecureSettings(Object $Object) : Object{

              	$New = new \stdClass;

              	foreach($Object as $Key => $Value){

                  	if(

                      	strpos($Key, 'Dir:') !== false

                      	|| strpos($Key, ':Dir') !== false

                    ){

                      	continue;

                    }

                  	$New->{$Key} = $this->Assigner($Value);

                }

              	return $New;

            }


          	public function Compile(Bool $Return = false){

              	global $GGN;




				/**
                 * Assignassion : Variables GGN
                */

              	foreach($GGN as $Key => $Value){

                  	$this->Assigners->{$Key} = $Value;

                }


              	/**
                 * Paramètres
                */

              	$this->Settings->{'Http:Host'} = $GGN->{'Http:Host'};

              	$this->Settings->{'Theme:Name'} = $this->Theme->Name;

              	//$this->Settings->{'Theme:Palette'} = $this->Theme->Palette;

              	//$this->Settings->{'Theme:Tone'} = $this->Theme->Tone;

              	$this->Settings->{'Theme:LayerExt'} = $this->Theme::LayerExt;

              	$this->Settings->{'Theme:LayoutExt'} = $this->Theme::LayoutExt;


              	foreach($this->Settings as $Key => $Value){

                  	$this->Assigners->{$Key} = $Value;

                }


				/**
                 * Règles
                */

              	$this->Rules();


				/**
                 * Template
                */

              	$this->Template();


				/**
                 * Buffer
                */

              	$this->ReAssigns();

              	$this->Buffer = $this->Swap($this->Buffer);



				/**
                 * Traitement de la sortie
                */

              	$this->ReAssigns();

              	$this->SetColoring();

              	//xDump::Debug($this->Assigners);



				$UsesAjax = self::UsesAjax();


				if(!$UsesAjax){

                    $this->Output = $this->Assigner(

                        $this->Template

                        ,[

                            'Sense:Engine:Head' => $this->Assigner($this->Head)

                            ,'Sense:Engine:Body' => $this->Assigner($this->Buffer)

                            //,'Sense:Engine:CSS' => $GGN->{'Http:Host'} . 'Assets/CSS/Sense/Core/' . $this->Version . '.css'

                            //,'Sense:Engine:JS' => $GGN->{'Http:Host'} . 'Assets/JS/Sense/Core/' . $this->Version . '.js'

                            ,'Sense:Engine:Settings' => 'window.$Settings = ' . json_encode($this->SecureSettings($this->Settings)) . ';'

                        ]

                    );

                }



				if($UsesAjax){

                  	$this->Output = $this->Assigner($this->Buffer);

					$this->Output .= '<script type="text/javascript" GGN:Sense:Script=":Settings">window.$Settings = ' . json_encode($this->SecureSettings($this->Settings)) . ';</script>';
					  
                }





				/**
                 * Affichage
                */

				if($Return === false){

                  	echo $this->Output;

              		return $this;

                }

              	if($Return !== false){

                  	return $this->Output;

                }





            }



        }

    }