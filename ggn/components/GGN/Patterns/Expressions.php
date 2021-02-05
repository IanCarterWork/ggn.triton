<?php


namespace GGN\Patterns;


	/**
     * Expressions
    */

	class Expressions{

		static function Vars(String $Name) : string{

			return '/{' . $Name . '{(.*)}(.*)}/Us';

		}


		static function Single(String $Name) : string{

			return '/{' . $Name . '}/s';

		}

		static function DoubleSingle(String $Name) : string{

			return '/{{' . $Name . '}}/s';

		}


    }
