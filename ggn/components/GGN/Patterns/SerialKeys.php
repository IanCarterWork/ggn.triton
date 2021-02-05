<?php


namespace GGN\Patterns;

	use GGN\Encryption;

	/**
     * Tags
    */

	class SerialKeys{

		static public function Block(Int $Row = 3, Int $Length = 4, Bool $AsArray = false, ?String $Separator = null){

			$Push = [];

			for($x = 1; $x <= $Row; $x++){ 
				
				$Push[] = Encryption\Customize(\GGN\ALPHA_NUMERIC, $Length); 
			
			}

			if($AsArray){ return $Push; }

			else{ return \implode($Separator ?: '-', $Push); }


		}


    }


