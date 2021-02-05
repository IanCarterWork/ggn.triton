<?php

	namespace GGN\Ui\Assigner;

		use GGN;

		use GGN\Ui;

		use GGN\Dial;

		use GGN\xDump;

	/**
     * Class \GGN\Ui\Controller
     *
    */


        class App extends GGN\Assigner\Ui implements GGN\Structuring\UiAssigner{

          	static public function Set(Array $Put = []) : Object{

              	$Assign = [];

              	$Assign['App:DefaultTitle'] = $Assign['App:DefaultTitle'] ?? 'GGN Application';

              	$Assign['App:Title'] = $Assign['App:Title'] ?? 'GGN Application';


              	$Assign['Settings:Lang'] = $Assign['Settings:Lang'] ?? 'ltr';

              	$Assign['Theme:Palette'] = $Assign['Theme:Palette'] ?? 'default';

              	//$Assign['Theme:Tone'] = $Assign['Theme:Tone'] ?? 'dark';


              	return self::Mount($Assign, $Put);

            }

        }