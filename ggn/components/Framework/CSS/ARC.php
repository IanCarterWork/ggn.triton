<?php

	namespace Framework\CSS;

		use GGN;

		use GGN\xDump;



	if(!class_exists("\\" . __NAMESPACE__ . "\ARC")){

      	class ARC{


          	var $Entity = '\Framework\CSS';

          	var $TonesSettingsDir = 'Framework/CSS/Tones/';

          	var $PalettesSettingsDir = 'Framework/CSS/Palettes/';

          	var $DirSlug = 'Dir:CSS';



          	public function Sheet(

              	String $Slug

              	, Array $Options = []

            ){

              	global $GGN;


              	$OutPut = false;

              	$PaletteName = (String) $Options['Palette'] ?? '';

              	$ToneName = (String) $Options['Tone'] ?? '';


              	$PseudoClass = (String) $Options['PseudoClass'] ?? '';

              	$MediaScreen = (String) $Options['MediaScreen'] ?? '';

               	$Pseudo = explode(';', $PseudoClass);

               	$MediaScreens = explode(';', $MediaScreen);


              	$Palette = (Object) array_merge(

                  	(Array) new Blend(

                      	GGN\Settings::Get($this->TonesSettingsDir . ucfirst($ToneName) . '')

                    )

                  	, (Array) new Blend(

                      	GGN\Settings::Get($this->PalettesSettingsDir . ucfirst($PaletteName) . '')

                    )

                );

              	//xDump::Debug($Palette);

				
				// var_dump('Palette', $Palette);exit;



				/**
                 * Chargement de la version
                 */


              	$Sheet = new RootSheet(

                  	$GGN->{$this->DirSlug} . $Slug

                  	, $Palette

                  	, $Pseudo

                  	, $MediaScreens

                );

              	if(!isset($Sheet->__FAILED__)){

                  	$OutPut = $Sheet->Mount(true);

                }


				return $OutPut;


            }





          	public function Graft(

              	String $Version = '0.0.1'

              	, String $Mods = ''

              	, String $PaletteName = ''

              	, String $ToneName = ''

              	, String $PseudoClass = ''

              	, String $MediaScreen = ''

            ){



              	$OutPut = '';

               	$Pseudo = explode(';', $PseudoClass);

               	$MediaScreens = explode(';', $MediaScreen);

              	//$BlendEntity = $this->Entity . '\Blend';

              	$SheetEntity = $this->Entity . '\Sheet';



              	$Palette = (Object) array_merge(

                  	(Array) new Blend(

                      	GGN\Settings::Get($this->TonesSettingsDir . ucfirst($ToneName) . '')

                    )

                  	, (Array) new Blend(

                      	GGN\Settings::Get($this->PalettesSettingsDir . ucfirst($PaletteName) . '')

                    )

                );


              	//xDump::Debug($this->TonesSettingsDir, $this->);



				/**
                 * Chargement de la version
                 */

              	$VersionSheet = new $SheetEntity(

                  	'Versions/' . $Version

                  	, $Palette

                  	, $Pseudo

                  	, $MediaScreens

                );

              	if(!isset($VersionSheet->__FAILED__)){

                  	$OutPut .= $VersionSheet->Mount(true);

                }

              	if(isset($VersionSheet->__FAILED__)){

                  	$OutPut .= '/* @Version < ' . $Version . ' > Not Load */';

                }




				/**
                 * Chargement des modules
                 */

              	foreach(explode(';', $Mods) as $Mod){

                    $Sheet = new $SheetEntity(

                        'Mods/' . $Mod

                        , $Palette

                      	, $Pseudo

                      	, $MediaScreens

                    );

                    if(!isset($Sheet->__FAILED__)){

                        $OutPut .= $Sheet->Mount(true);

                    }

                  	else{$OutPut .= '/* @Sheet Not Found < ' . $Mod . ' > */';}

                }




              	header("Content-Type:text/css");

              	return $OutPut;




            }




        }

    }