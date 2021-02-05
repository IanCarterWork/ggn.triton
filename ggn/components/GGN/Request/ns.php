<?php

	namespace GGN;

	/**
     * Class Request
     *
    */

    if(!class_exists("\\" . __NAMESPACE__ . "\Request")){

        class Request{


          	var $Method;


            public function __construct(){

                $this->Method = $_SERVER['REQUEST_METHOD'];

			}

			public function GetBody(){ 
				
				try{

					return \json_decode(\file_get_contents('php://input')); 

				} catch(\Exception $e){}

				return null;

			}
			
			
			public function Matches(String $Slug, String $Method = '*') : ?Object{

				global $GGN;

				$Type = '';

				$Path = '/' . $_REQUEST['ggn_arc_request'] ?: '';

				$Type = null;


				$Pattern = '/^' . (str_replace('*', '(.*)', str_replace('/', '\/', $Slug) )) . '$/i';

				
				if($Slug == '/'){
	
					$Found = ($Path == '/') ? 1 : 0;

					$Matches = [];
	
				}
				
				if($Slug != '/'){

					$Found = preg_match($Pattern, $Path, $Matches);

				}


				if(strpos($Slug, '/*') > -1){

					$Type .= ':DIR';

					$Path .= '/';

					$Found = ($Found) ? $Found : preg_match($Pattern, $Path, $Matches);
	
				}

				if(strpos($Slug, '*.') > -1){

					$Type .= ':EXT';

				}

				if(strpos($Slug, '.*') > -1){

					$Type .= ':DOTEX';

				}


				// if($Found){

					// echo "<pre>"; var_dump('ARC Match', $Path, $Slug, $Found, $Pattern, $Matches ); echo "</pre>";
				
				// }



				$GGN->{'Http:Current:URL'} = ($GGN->{'Server:Protocol'}) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];


				return (Object) [

					// 'ARC' => str_replace('*', '', $Slug)

					'Slug' => $Slug

					,'File' => $Path

					,'Is' => $Found

					,'Type' => $Type

					,'Pattern' => $Pattern

					,'Matches' => $Matches

					,'URL' => $GGN->{'Http:Current:URL'}
					
				];
				
			}

          	public function GetSlug(String $Slug){

              	global $GGN;


              	//$Slug = '/' . $Slug;

              	$Current = $_REQUEST['ggn_arc_request'] ?: '';

              	$Out = [];

				// $Out['URLTpl'] = $GGN->{"Http:Host"};
				  
              	$Out['ARC'] = '/' . $Current;

              	$Out['Slug'] = '/' . $Current;

              	$Out['File'] = NULL;

				$Out['URL'] = ($GGN->{'Server:Protocol'}) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
				  
				$GGN->{'Http:Current:URL'} = $Out['URL'];



              	$ARCm = substr($Slug,0,-1);

              	$ARCn =  strtolower(substr($Out['ARC'], 0, (strlen($Slug) - 2)) . '/*');

              	$ARCw = strtolower($Out['ARC'] . '/');



              	if(substr($Slug, -2) == '/*' && ($ARCm == $Out['ARC'] || $ARCm == $ARCw || $ARCn == strtolower($Slug)) ){

                  	$Out['Slug'] = '/' . substr($Current, 0, strlen( substr($Slug, 1, -2) ) ) . '/*';

                  	$Out['File'] = substr($Current, strlen($Out['Slug']) - 2);

                  	$Out['File'] = $Out['File'] ?: NULL;

                }

              	//echo '<pre>';var_dump($Slug); var_dump($Out); echo '</pre>';

              	return (Object) $Out;

            }

          	static public function Input(String $Key, $Surrogate = null){

              	return isset($_REQUEST[$Key]) ? $_REQUEST[$Key] : $Surrogate;

            }

          	static public function Post(String $Key, $Surrogate = null){

              	return isset($_POST[$Key]) ? $_POST[$Key] : $Surrogate;

            }

          	static public function Get(String $Key, $Surrogate = null){

              	return isset($_GET[$Key]) ? $_GET[$Key] : $Surrogate;

            }

          	static public function Put(String $Flag = 'r'){

              	return fopen("php://input", $Flag);

            }

        }

    }