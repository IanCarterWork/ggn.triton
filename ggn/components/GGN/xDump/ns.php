<?php

namespace GGN;

	use GGN\Dial;


	/**
     * Debogage
    */

	class xDump{


		const SyntaxColor = [

			"string" => "#1abc9c"

			,"integer" => "#2ecc71"

			,"float" => "#3498db"

			,"double" => "#3498db"

			,"boolean" => "#9b59b6"

			,"array" => "#e67e22"

			,"object" => "#e74c3c"

			,"null" => "#607D8B"

			//,"ressource" => "#555"

		];


		static public function Detail($Entry){

			$Type = strtolower(gettype($Entry));

			$Color = self::SyntaxColor[$Type]??'#9E9E9E';

			$Return = '<div style="color:' . $Color . ';border-left:1px solid ' . $Color . ';padding-left:16px;" class=""><i>' . strtoupper($Type) . '</i> : ';


			switch($Type){

				case 'string':

					$Return .= ('"' . $Entry . '"');

				break;

				case 'integer':

				case 'float':

				case 'double':

				case 'null':

					$Return .= ('' . $Entry . '');

				break;

				case 'boolean':

					$Return .= ('' . ($Entry === TRUE ? 'TRUE' : 'FALSE') . '');

				break;

				case 'object':

					$Ref = new \ReflectionObject($Entry);

					$Return .= '(<b>' . $Ref->getName() . '</b> : ' . count(get_object_vars($Entry)) . ') #' . spl_object_id($Entry) . ' ';

					foreach($Entry as $Key => $Obj){

						$Return .= '<div style="padding:4px 16px;" >';

						$Return .= '<div style="padding:8px 0px;">{ ' . $Key . ' }</div>';

							$Return .= self::Detail($Obj);

						$Return .= '</div>';

					}

					$Return .= '';

				break;

				case 'array':

					$Ref = new \ReflectionObject($Entry);

					$Return .= '(' . count($Entry) . ')';

					foreach($Entry as $Key => $Obj){

						$Return .= '<div style="padding:4px 16px;" >';

						$Return .= '<div style="padding:8px 0px;">[ ' . $Key . ' ]</div>';

							$Return .= self::Detail($Obj);

						$Return .= '</div>';

					}

					$Return .= '';

				break;


			}


			$Return .= '</div>';

			return $Return;

		}

		static public function Debug(...$Entries){

			$Content = '';

			if(!empty($Entries)){

				foreach($Entries as $Entry){

					$Content .= self::Detail($Entry);

				}

			}

			Dial\Info(

				'Débogage'

				, null

				, $Content

			);

		}

	}
