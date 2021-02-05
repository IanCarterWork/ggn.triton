<?php


namespace GGN\Patterns;


	/**
     * Tags
    */

	class Tags{

		static public function Single(String $Name) : string{

			return '/<' . $Name . '\b([^>]*)\/>/s';

		}


		static public function Logic(String $Name) : string{

			return '/<' . $Name . '\b([^>]*)>(.*)<\/' . $Name . '>/Us';

		}


    }


