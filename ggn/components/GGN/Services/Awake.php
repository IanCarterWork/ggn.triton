<?php

	namespace GGN\Services;

		use GGN;

		use GGN\Dial;

		use GGN\xDump;

		use GGN\Security;

		use Database;



		// var_dump($_FILES['file']['name']);exit;




	// var_dump( \ini_get('memory_limit') );

	// \ini_set('memory_limit', '2G');

	// var_dump( \ini_get('memory_limit') );

	// exit;



	class Awake{




      	var $Name;



      	// var $Options;



		public function AllowAccess(){

			header('Access-Control-Allow-Origin: *');
			
			header('Access-Control-Allow-Credentials: true');
			
			header('Access-Control-Allow-Headers: Content-Type, API-KEY, API-CLIENT-LANG');
			
			header('Access-Control-Allow-Methods: POST, GET, OPTIONS, PATCH, DELETE, HEAD, CONNECT, TRACE');

			return $this;
			
		}



      	public function Init($Request) : self{

			global $GGN;


			$Referer = parse_url($_SERVER['HTTP_REFERER'] ?: '');

			// var_dump(parse_url($Referer));exit;
			

			$this->Settings = (Object) array_merge( (Array) \GGN\Settings::Get('Apps/Service.Awake') );
        
			$this->Domain = $_SERVER['HTTP_ORIGIN'] ?: (is_array($Referer) ? ($Referer['scheme'] . '://' . $Referer['host'] . ':' . $Referer['port']) : null);

			$this->PublicKey = $_SERVER['HTTP_API_KEY'] ?: null;

			$this->Method = $_SERVER['REQUEST_METHOD'] ?: null;

			$this->Request = $Request;

			$this->Name = $Request->Queries->Matches[1] ?: null;

			// $this->Input = ((Object) $_REQUEST);


			// var_dump($this->Domain);exit;


			$GGN->{'HEADER:CONTENT:TYPE'} = 'application/json;charset=utf-8';
			  

			$this->Path = $GGN->{'Dir:Services'} . $this->Name . '/';

			$this->NS = '\Service\\' . \str_replace('/', '\\', $this->Name) . '\State';
			

			$this->AllowAccess();

			$this->Vendor = $this->AuthCurrentVendor();

			if(!is_object($this->Vendor)){

				Dial\Error(
					
					'Service Awake'

					, 'Echec Authentification'

					, 'Accès réfusé'
				
				);

				return null;
				
			}

          	return $this;

		}
		

		public function AuthCurrentVendor(){

			global $GGN;

			$Get = Database\Driver::Query("SELECT * FROM", $this->Settings->{'Service:Awake:DB:API'}, [

				"Query" => " WHERE `publickey` = :publickey ORDER BY created DESC LIMIT 0,1"

				, "Prepare" => [

					"publickey" => $this->PublicKey
					
				]
				
			]);

			if(\is_object($Get)){

				$Data = $Get->State->fetchAll();

				if(\is_array($Data)){

					// var_dump($Data);

					if(!empty($Data)){

						$Data = (Object) $Data[0];

						if($Data->domain?:false){

							if(\strtolower($this->Domain) != \strtolower($Data->domain)){ return null; }
							
						}

						if($Data->browser?:false){

							if(\strtolower($GGN->{'Client:Browser:Name'}) != \strtolower($Data->browser)){ return null; }
							
						}
						
						if($Data->platform?:false){

							if(\strtolower($GGN->{'Client:Platform'}) != \strtolower($Data->platform)){ return null; }
							
						}

						return $Data;
						
					}

					else{return false;}

				}

				
			}

			return null;
			
		}




      	public function Build() : ?string {

			global $GGN;


			$Class = new $this->NS($this->Input ?: null);
			  

			$UseCustomizeInputData = isset($Class->CustomizeInputData) && $Class->CustomizeInputData === TRUE;



			if($UseCustomizeInputData){

				$Class->Input = ((Object) $_REQUEST);

			}

			if(!$UseCustomizeInputData){

				$Class->Input = $this->Request->GetBody() ?: ((Object) $_REQUEST);

			}
			  

			// var_dump($Class);exit;s

          	$Output = null;
			
			// $GGN->{'HEADER:CONTENT:TYPE'} = $this->Options->{'Format:Output'} ?: $GGN->{'HEADER:CONTENT:TYPE'};




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





			/* Verification se la methode */

			if(!method_exists($Class, $this->Method)){

				$Output = '{"Response":"Method:Failed"}';

			}
			

			if(method_exists($Class, $this->Method)){

				$Output = json_encode( 
					
					$Class->{$this->Method}($_REQUEST)

					, JSON_FORCE_OBJECT | JSON_NUMERIC_CHECK

					// , 4096
				
				);

			}
			

			// var_dump('tnb ///', $Output, $this->Method);exit;

			// var_dump($this);exit;




          	return $Output;

        }


    }