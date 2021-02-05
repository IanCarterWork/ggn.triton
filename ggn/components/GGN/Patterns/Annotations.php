<?php


namespace GGN\Patterns;


	/**
     * Tags
    */

	class Annotations{

		static public function Type(String $Text){

			$Reading = [];

			foreach(preg_split("/((\r?\n)|(\r\n?))/", $Text) as $Line){

				if(($Split = explode('@Type ', $Line)) && strstr($Line, '@Type ')){

					$Insert = [];

					foreach(explode(',', $Split[1]) as $Var){

						$Insert[] = rtrim(ltrim($Var));

					}

					$Reading[] = $Insert;
					
				}

			}

			return empty($Reading) ? null : $Reading;

		}

		static public function Find(String $Text, String $Directive = '@Var'){

			$Reading = [];

			foreach(preg_split("/((\r?\n)|(\r\n?))/", $Text) as $Line){

				if(($Split = explode($Directive . ' ', $Line)) && strstr($Line, $Directive . ' ')){

					$Split[1] = rtrim(ltrim($Split[1]));

					if($Split[1] ?: null){ $Reading[] = $Split[1]; }
					
				}

			}

			return empty($Reading) ? null : $Reading;

		}


    }


