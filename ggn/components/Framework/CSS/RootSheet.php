<?php

	namespace Framework\CSS;

		use GGN;

		use GGN\xDump;

		use Framework\Ability;


	if(!class_exists("\\" . __NAMESPACE__ . "\RootSheet")){

      	class RootSheet{


          	use Ability\StyleSheet;

          	use Ability\ColoriMetry;

          	use Ability\ScreenSize;


          	const SheetExt = '.sheet.php';

          	//var $DirSlug = 'Dir:CSS';


          	public function __construct(

              	String $Path

              	, Object $Palette

              	, Array $Pseudo = []

              	, Array $MediaScreen = []

            ){

              	global $GGN;

				$this->Dir = \dirname($Path) . '/';

              	$File = $Path . self::SheetExt;


              	if(is_file($File)){

                  	$this->Color = $Palette;

                  	$this->Pseudo = (Object) ['', 'hover', 'focus'];

                  	$this->MediaScreen = (Object) ((!empty($MediaScreen) && is_array($MediaScreen)) ? $MediaScreen : ['dMi', 'dS', 'dM', 'dL']);


                  	if(!empty($Pseudo) && !empty($Pseudo[0])){

                  		$this->Pseudo = (Object) array_merge(

                          	(Array) $this->Pseudo

                      		, $Pseudo

                        );

                    }


                  	if(!empty($MediaScreen) && !empty($MediaScreen[0])){

                  		$this->MediaScreen = (Object) $MediaScreen;

                    }

					// var_dump($this->MediaScreen);exit;

                  	include $File;


                }

              	else{

                  	$this->__FAILED__ = TRUE;

                }


			}
			

			public function Load(String $Name){

				include $this->Dir . $Name . self::SheetExt;

				return $this;
				
			}
			

        }

    }