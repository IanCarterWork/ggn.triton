<?php


namespace GGN\Patterns;


	/**
     * Tags
    */

	class CamelCase{

		static public function From(String $String){

			$Base = basename(str_replace('\\', '/', $String));
			
			return strtolower(preg_replace('/([A-Z])/', "_$1", strtolower($Base[0]) . substr($Base, 1) ));
	
		}


    }


