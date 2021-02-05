<?php

	namespace GGN\Services;

		use GGN;

		use GGN\xDump;

		use GGN\Security;





	class Wave{




      	var $Name;



      	var $Options;




      	public function Initialize(?String $Name = NULL, ?Object $Options = null) : self{

          	global $GGN;


          	$this->Name = $Name;

          	$this->Options = $Options;

          	$this->Options->Method = ucfirst(strtolower($this->Options->Method ?: 'get'));

          	$this->Options->Input = $this->Options->Input ?: null;

          	$this->Path = $GGN->{'Dir:Services'} . $this->Name . '/';

          	$this->NS = '\Service\\' . str_replace('/', '\\', $this->Name) . '\Trigger';


			// echo '<pre>';
			// var_dump($this->NS);exit;

          	return $this;

        }




      	public function Build() : ?string {

			global $GGN;


          	$Class = new $this->NS($this->Options->Input);

          	$Output = null;

			
			$GGN->{'HEADER:CONTENT:TYPE'} = $this->Options->{'Format:Output'} ?: $GGN->{'HEADER:CONTENT:TYPE'};




          	/* Verification du Login */

          	if(isset($Class->LoginRequired)){

				$UserSession = Security\Session::Get($GGN->{'Client:Session:Login'});

				$Connect = new \App\Connect\Master();

				if(is_object($Connect->GetUserName($UserSession->Value->name))){

					Security\Session::Refresh($GGN->{'Client:Session:Login'});
		
				}
		
				else{

					return '{"Response":"Connect:Failed"}';
					
				}

			}



          	/* Verification du Token */

          	if(isset($Class->TokenRequired)){

				if($Class->TokenRequired === TRUE){

                  	if(

                      	isset($this->Options->Token)

                      	&& Security\Token::Check($this->Options->Token) === TRUE

                    ){

						if(isset($Class->TokenRefresh) && $Class->TokenRefresh === TRUE){

							Security\Token::Refresh($this->Options->Token);
							
						}

						else{

							Security\Token::Destroy($this->Options->Token);

						}

                    }

                  	else{

                      	return '{"Response":"Token:Failed"}';

                    }

                }

            }



			/* Verification se la methode */

			if(method_exists($Class, $this->Options->Method)){


              	$Output = $Class->{$this->Options->Method}();

                switch($this->Options->{'Format:Output'}){

                    case 'text/javascript':

                        $Output = 'window["' . ($this->Options->{'JS:CallBack'} ?? 'ServiceCallBack') . '"] = ' . json_encode($Output);

                    break;


                    case 'text/xml':

                        $Output = (new GGN\Serializer\ObjectToXML($Output))->Output;

                    break;


                    default:

                        $Output = json_encode($Output);

                    break;

                }


            }







          	return $Output;



        }


    }