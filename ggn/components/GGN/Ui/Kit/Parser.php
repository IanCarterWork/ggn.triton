<?php

	namespace GGN\Ui\Kit;

use stdClass;

/*
     * Class Ui\Kit
     *
    */

	class Parser{

		
		static public function Request(String $Data = '{}') : Object{

			return json_decode($Data);

		}

		
		static public function Attributes(Array $Replacements = []) : Object{

			return isset($_REQUEST['ui-attrs']) ? self::Exists(self::Request($_REQUEST['ui-attrs']), $Replacements) : new stdClass;

		}
		
		
		static public function Args(Array $Replacements = []) : Object{

			return isset($_REQUEST['ui-args']) ? self::Exists(self::Request($_REQUEST['ui-args']), $Replacements) : new stdClass;

		}
		
		
		static public function Items(Array $Replacements = []) : Object{

			return isset($_REQUEST['ui-items']) ? self::Exists(self::Request($_REQUEST['ui-items']), $Replacements) : new stdClass;

		}

		
		static public function Exists(Object $Handler, Array $Keys) : Object{

			foreach($Keys as $Key => $Value){

				if(!isset($Handler->{$Key})){

					$Handler->{$Key} = $Value;

				}
				
			}

			return $Handler;

		}
		

	}

