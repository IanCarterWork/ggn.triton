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

    if(!class_exists("\\" . __NAMESPACE__ . "\HomePage")){

        class HomePage extends GGN\Assigner\Ui implements GGN\Structuring\UiAssigner{

          	static public function Set(Array $Put = []) : Object{

              	$Assign = [];

              	$Assign['App:DefaultTitle'] = 'Default Title';

              	$Assign['App:Title'] = 'Default Title';


              	$Assign['Settings:Lang'] = 'ltr';

              	//$Assign['Settings:PaletteName'] = 'default';

              	//$Assign['Settings:ToneName'] = 'light';


              	return self::Mount($Assign, $Put);

            }

        }

    }