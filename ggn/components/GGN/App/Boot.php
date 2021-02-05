<?php

	namespace GGN\App;

		use GGN;

		use GGN\xDump;

		use GGN\Ui;

		use GGN\Patterns;





	class Boot{




		// var $Manifest;



      	// var $Key;




      	public function __construct(?String $Key = NULL, ?GGN\Ui $Ui = null, ?Array $Options = []){

			$this->Key = $Key;

			$this->Ui = $Ui;

			$this->Options = $Options;

		}



      	public function Initialize(?String $Key = NULL, GGN\Ui $Ui, ?Array $Options = []){

          	global $GGN;


          	$this->Key = $this->Key ?: $Key;

          	$this->Options = $this->Options ?: (Object) $Options;

          	$this->Ui = $this->Ui ?: $Ui;


          	$this->Dir = $GGN->{'Dir:Apps'} . $this->Key . '/';

          	$this->ViewsDir = $this->Dir . 'views/';

			$this->IncludesDir = $this->Dir . 'includes/';
			  
			$this->ResourcesDir = $this->Dir . 'resources/';
			  
          	$this->EventsDir = $this->Dir . 'events/';


          	return $this->LoadManifest();

        }



      	public function LoadManifest(){

			global $GGN;

          	if(is_file($File = ($this->Dir . 'Manifest.json'))){

				$this->Manifest = json_decode(file_get_contents($File));
				  

            }

          	return $this;

        }




      	public function Resources(String $Path, ?String $Type = null) {

			global $GGN;
			
			$File = $this->ResourcesDir . $Path;

			$Out = $this;


			if(is_file($File)){

				switch($Type){

					case ':JSON':

						$Out = json_decode(file_get_contents($File));

					break;

					case ':Content':

						$Out = file_get_contents($File);

					break;

					default:

						include $File;

					break;
					
				}
				
				
				return $Out;

			}

          	return null;

        }




      	public function View(String $Path) : self {

			global $GGN;

          	foreach($this->Options as $Op => $Option){

              	$this->Manifest->Assembly->{$Op} = $Option;

            }

          	$this->Manifest->Assembly->{'Dir:App'} = $this->Dir;

          	$this->Manifest->Assembly->{'Dir:App:Views'} = $this->ViewsDir;

			$this->Manifest->Assembly->{'Dir:App:Includes'} = $this->IncludesDir;
			  
          	$this->Manifest->Assembly->{'App:URL'} = $GGN->{'Http:Host'} . ((strpos($GGN->{'ARC:Current'}->Type, ':DIR') > -1) ? str_replace('*', '', substr($GGN->{'ARC:Current'}->Slug, 1)) : '');

			$this->Manifest->Assembly = Ui\Assigner\App::Set((Array) $this->Manifest->Assembly);

			// xDump::Debug($this->ViewsDir, $Path);

			$this
			
				->Ui
				
				->Prop('App', $this)
				
				->Viewer(

					$Path

					, $this->Manifest->Assembly

					, $this->ViewsDir

				)
				
				
			;

          	return $this;

        }


    }