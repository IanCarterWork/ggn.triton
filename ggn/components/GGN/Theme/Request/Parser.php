<?php

	namespace GGN\Theme\Request;

use stdClass;

/*
     * Class Ui\Kit
     *
    */

	class Parser{

		
		static public function Get(String $Data = '{}') : Object{

			return json_decode($Data);

		}

		
		static public function Attributes(Array $Replacements = []) : Object{

			return isset($_REQUEST['theme-attrs']) ? self::Exists(self::Request($_REQUEST['theme-attrs']), $Replacements) : new stdClass;

		}

		
		static public function Args(Array $Replacements = []) : Object{

			return isset($_REQUEST['theme-args']) ? self::Exists(self::Get($_REQUEST['theme-args']), $Replacements) : new stdClass;

		}
		
		
		static public function Items(Array $Replacements = []) : Object{

			return isset($_REQUEST['theme-items']) ? self::Exists(self::Get($_REQUEST['theme-items']), $Replacements) : new stdClass;

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

