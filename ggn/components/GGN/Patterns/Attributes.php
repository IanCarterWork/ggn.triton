<?php


namespace GGN\Patterns;


	/**
     * Attributes
    */

	class Attributes{

		static public function Get(String $String, Bool $Array = false){

			if(empty($String)){return false;}

			$Out = false;

			if(preg_match_all(

				'/(^w+|[a-zA-Z0-9-]*)=["]([^"]*)/s'

				, $String

				, $Matches

				, PREG_SET_ORDER

			)){

				//var_dump($Matches);

				$Out = ($Array === true) ? [] : new \GGN\EObject();

				foreach($Matches as $Match){

					$Re = new \GGN\EObject();

					$k = ltrim(rtrim(ucfirst(strtolower($Match[1]))));

					$Re->{$k} = $Match[2];

					switch(strtolower($Re->{$k})){

						case 'true': $Re->{$k} = true;break;

						case 'false': $Re->{$k} = false;break;

						case 'null': $Re->{$k} = null;break;

						default:

							$Re->{$k} = is_numeric($Re->{$k}) ? $Re->{$k} * 1 : $Re->{$k};

						break;

					}

					if($Array === true){

						$Out[] = $Re;

					}

					else{

						$Out->{$k} = $Re->{$k};

					}

				}

			}

			return $Out;

		}

	}


