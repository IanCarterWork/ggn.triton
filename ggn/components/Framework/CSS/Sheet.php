<?php

	namespace Framework\CSS;

		use GGN;

		use GGN\xDump;

		use Framework\Ability;


	if(!class_exists("\\" . __NAMESPACE__ . "\Sheet")){

      	class Sheet{


          	use Ability\StyleSheet;

          	use Ability\ColoriMetry;

          	use Ability\ScreenSize;



          	const SheetExt = '.sheet.php';

          	var $DirSlug = 'Dir:Framework:CSS';




          	public function __construct(

              	String $Path

              	, Object $Palette

              	, Array $Pseudo = []

              	, Array $MediaScreen = []

            ){

              	global $GGN;


              	$File = $GGN->{$this->DirSlug} . $Path . self::SheetExt;

              	if(is_file($File)){

                  	$this->Color = $Palette;

                  	$this->Pseudo = (Object) ['', 'hover', 'focus'];

                  	$this->MediaScreen = (Object) ['dMi', 'dS', 'dM', 'dL'];


                  	if(!empty($Pseudo) && !empty($Pseudo[0])){

                  		$this->Pseudo = (Object) array_merge(

                          	(Array) $this->Pseudo

                      		, $Pseudo

                        );

                    }


                  	if(!empty($MediaScreen) && !empty($MediaScreen[0])){

                  		$this->MediaScreen = (Object) $MediaScreen;

                    }



                  	//xDump::Debug($this->Pseudo, $Pseudo);

                  	include $File;

                }

              	else{

                  	$this->__FAILED__ = TRUE;

                }


            }

        }

    }