<?php

	namespace GGN;

		use GGN;

		use GGN\xDump;

		use GGN\Dial;

		use Framework\CSS;

		use GGN\App\Boot;



	/*
     * Class Theme
     *
    */

	class Theme{


		const TYPE_NATIVE = ':Native';

		const TYPE_BUFFER = ':Buffer';



		const LayerExt = '.layer';

		const LayoutExt = '.layout';

		const ViewExt = '.view';
			

		
		var $Manifest;
		
		var $Settings;
		
		var $Name;

		var $Palette;

		var $Tone;

		var $Type = false;

		var $IncludeDir;

		var $ViewDir;





		public function __construct(
			
			?String $Name = null
			
			, String $Palette = 'default'
			
			, String $Tone = 'dark'
			
			, ?Object $App = null

			, ?String $Type = null
			
		){

			$Name = $Name ?: null;

			// if(is_string($Name)){

				global $GGN;


				$Manifest = $GGN->{'Dir:Themes'} . $Name . '/manifest.json';


				$this->App = $App ?: (new \stdClass);

				$this->Settings = new \stdClass;


				$this->Name = $this->App->Manifest->Theme->Name ?: $Name;

				$this->Palette = $this->App->Manifest->Theme->Palette ?: $Palette;

				$this->Tone = $this->App->Manifest->Theme->Tone ?: $Tone;

				
				$this->Type = $Type;
				


				if(is_file($Manifest)){

					$this->Manifest = json_decode( file_get_contents($Manifest) );
					
					$this->Dir = dirname($Manifest) . '/';

				}

				if(!is_file($Manifest)){

					$this->Manifest = (Object) [
						
						"Infos" => [

							"Author" => "Custom"

							,"Email" => null

						]

						,"Type" => ":Native"

					];
					
					$this->Dir = null;

				}


					

				$this->Settings->{'Theme:Name'} = $this->Name ?: $Name;

				$this->Settings->{'Theme:Palette'} = $this->Palette ?: $Palette;

				$this->Settings->{'Theme:Tone'} = $this->Tone ?: $Tone;

				$this->Settings->{'Theme:LayerExt'} = self::LayerExt;

				$this->Settings->{'Theme:LayoutExt'} = self::LayoutExt;



				// $this->Settings->{'Http:Referer'} = $_SERVER['HTTP_REFERER'] ?: null;
		

				// $this->Settings = (Object) array_merge((Array) $this->Settings, (Array) $this->Manifest);

				// xDump::Debug($this->Settings->{'Theme:Tone'});

				$this
				
					->UpdateSettings()
					
					->UpdateAppSettings()

					->UpdateColorSettings()
					
				;
				



			// }

		}


		public function Settings(String $Name, $Value, Bool $Important = false) : self{

			if(isset($this->Settings->{$Name})){

				if($Important === TRUE){

					$this->Settings->{$Name} = is_string($Value) ? \GGN\ParsingVars($Value) : $Value;
					
				}
				
			}

			else{

				$this->Settings->{$Name} = is_string($Value) ? \GGN\ParsingVars($Value) : $Value;

			}

			return $this;

		}


		public function UpdateSettings() : self{

			global $GGN;

			foreach($GGN as $Key => $Value){

				if(strpos($Key, 'Dir:') !== false || strpos($Key, ':Dir') !== false){continue;}

				$this->Settings->{$Key} = is_string($Value) ? \GGN\ParsingVars($Value) : $Value;

		  	}

			return $this;

		}


		static public function SecureSettings(Object $Object) : Object{

			$New = new \stdClass;

			foreach($Object as $Key => $Value){

				if(strpos($Key, 'Dir:') !== false || strpos($Key, ':Dir') !== false){continue;}

				if(strpos($Key, 'Kernel:') !== false || strpos($Key, ':Kernel') !== false){continue;}

				if(strpos($Key, 'Autonomous:') !== false || strpos($Key, ':Autonomous') !== false){continue;}

				if(strpos($Key, 'System:') !== false || strpos($Key, ':System') !== false){continue;}

				$New->{$Key} = is_string($Value) ? \GGN\ParsingVars($Value) : $Value;

		  	}

			return $New;

	  	}

		public function UpdateAppSettings(){

			if(is_object($this->App)){

				if(isset($this->App->Manifest->Assembly) && is_object($this->App->Manifest->Assembly)){

					foreach($this->App->Manifest->Assembly as $Key => $Value){

						$this->Settings->{$Key} = is_string($Value) ? \GGN\ParsingVars($Value) : $Value;

					}

				}

				if(isset($this->App->Manifest->Theme) && is_object($this->App->Manifest->Theme)){

					foreach($this->App->Manifest->Theme as $Key => $Value){

						$this->Settings->{'Theme:' . $Key} = is_string($Value) ? \GGN\ParsingVars($Value) : $Value;

					}
					
				}

			}

			return $this;
			
		}


		public function UpdateColorSettings(){
			
			foreach($this->Coloring() as $Key => $Value){

				$this->Settings->{'Coloring:' . $Key} = is_string($Value) ? \GGN\ParsingVars($Value) : $Value;

			}

			return $this;
			
		}


		public function Coloring(){

			
			// echo'<pre>'; var_dump('Coloring', $this->Tone, $this->Palette);echo'</pre>'; exit;

			return (Object) array_merge(

				(Array) new CSS\Blend(

					GGN\Settings::Get('Framework/CSS/Tones/' . ucfirst($this->Tone) . '')

				)

				, (Array) new CSS\Blend(

					GGN\Settings::Get('Framework/CSS/Palettes/' . ucfirst($this->Palette) . '')

				)

			);

		}

		
		public function CSSColoringVars() : String{

			$Out = ':root{';

            foreach($this->Settings as $Key => $Color){

				$Ex = explode('Coloring:', $Key);

				if(isset($Ex[1]) && (!substr_count($Ex[1], ':RGB')) ){

					$Out .= ('--Palette-' . str_replace(':', '-', $Ex[1]) . ':' . $Color . '; ');

				}

			}
			
			$Out .= '}';

			return $Out;

		}

		public function BuildSettings() : Object{

			return self::SecureSettings($this->Settings);

		}


		public function Layer(String $Name):?String{

			$Path = $this->Dir . 'Layers/' . $Name . self::LayerExt;

			if(is_file($Path)){

				if($this->Type == self::TYPE_BUFFER){
					
					return file_get_contents($Path);

				}

				else{
					
					include $Path;

					return $Path;

				}

			}

			return null;

		}



		public function Layout(String $Name):?String{

			$Path = $this->Dir . 'Layouts/' . $Name . self::LayoutExt;

			if(is_file($Path)){

				if($this->Type == self::TYPE_BUFFER){
					
					return file_get_contents($Path);

				}

				else{
					
					include $Path;

					return $Path;

				}

			}

			return null;

		}


		static public function UsesAjax(){

			return (isset($_SERVER['HTTP_X_REQUESTED_WITH']))

				? TRUE // (strpos($_SERVER['HTTP_X_REQUESTED_WITH'], 'GGN:') === 0)

				: FALSE

			;

		}


		static public function ToAttributes(Array $Array){

			$Attr = [];

			if(!empty($Array)){

				foreach($Array as $Name => $Prop){

					$Attr[] = '' . $Name . '="' . \addslashes($Prop) . '"';
					
				}
				
			}

			return $Attr;

		}


		public function Preset(String $Name) : self{

			global $GGN;

			$File = dirname(__FILE__) . '/Presets/' . ($Name) . '.preset';

			if(is_file($File)){

				include $File;
				
			}

			return $this;

		}


		public function Head($Name) : self{

			if(!self::UsesAjax()){

				$Path = $this->Dir . 'Head/' . $Name . self::LayerExt;

				if(is_file($Path)){

					if($this->Type == self::TYPE_BUFFER){
						
						return file_get_contents($Path);
	
					}
	
					else{
						
						include $Path;
		
					}
	
				}
	
				return $this;

			}

			return $this;

		}



		public function Include(String $Name, bool $UsesAjax = false, ?Array $Arguments = []) : self{

			if(self::UsesAjax() === TRUE && $UsesAjax === FALSE){
				
			}

			else{

				global $GGN;


				$Dir =  $this->IncludeDir 
				
					?: $this->Settings->{'Dir:App:Includes'}
					
					?: $GGN->{'Dir:Viewer'}
					
				;

				$Path = $Dir . $Name . self::ViewExt;

				if(is_file($Path)){

					include $Path;

				}

			}

			return $this;

		}
		
		



		public function ParseVars(String $String) : String{

			global $GGN;

			$Vars = array_merge(

				[]

				, (Array) $GGN

				, (Array) $this->Settings
				
			);

			foreach($Vars as $Key => $Value){

				if(is_string($Value)){

					$String = str_replace('{{' . $Key . '}}', $Value, $String);

				}


			}

			return $String;

		}
		
		



		public function Security(Object $Config) : self{

			switch(strtolower($Config->Type ?: '')){

				case 'token':

					unset($Config->Type);
					
					$Config->Duration = $Config->Duration ?: 60;

					$this->Security->{'Token:' . ($Config->Name ?: 'Default')} = GGN\Security\Token::Create($Config);

				break;
				
			}

			return $this;
			
		}



		public function EventTrigger(String $Name) : ?Object{

			if(is_object($this->App)){

				if(isset($this->App->EventsDir) && is_string($this->App->EventsDir)){

					$File = $this->App->EventsDir . 'On' . $Name . '.php';

					if(is_file($File)){

						include $File;

						return $this;
						
					}

					return null;
				
				}
				
			}

			return $this;
			
		}




		public function Build(){

			if(self::UsesAjax() === TRUE){

				echo '<script>$Settings = GGN.Merge($Settings, ' . $this->GenerateSettings() . ');</script>';
				
			}

			return $this;
			
		}
		

		public function GenerateSettings(){

			// if(self::UsesAjax() === TRUE){

				$SettingsString = json_encode( $this->BuildSettings() );

				return '' . ($SettingsString) . '';
				
			// }


			// return $this;
			
		}
		



		public function Prop(String $Key, $Value = null) : self{

			$this->{$Key} = $Value;

			return $this;
			
		}
		


		public function CallPathProtocol(String $thisPath){

			$Root = $_SERVER['DOCUMENT_ROOT'];

			$Dir =  is_string($this->ViewDir) ? $this->ViewDir : ($this->Ui->App->{'Dir:App:Views'} ?? $GGN->{'Dir:Viewer'});

			$_Path = $Dir . \dirname($thisPath) . '';

			$Paths = \explode('/', substr(str_replace('\\', '/', $_Path), strlen($Root) ) );

			// $Paths = \explode('/', str_replace('\\', '/', $_Path) );

			$Current = '';

			// $Current = $Root;

			$Root = substr($Root, -1) == '/' ? substr($Root0, -1) : ($Root . '/');

			$_Path = '';

			// $Current = substr($Current, -1) == '/' ? $Current : $Current . '/';


			// echo '<pre>';

				// var_dump($Paths);

			foreach($Paths as $Path){

				$_Path .= $Path . '/';

				$File = str_replace('//', '/', $Root . $_Path . '/') . '.path.protocol.php'; 
				

				$Is = \is_file($File);

				if($Is){
					
					require $File;
					
				}
				
				// echo $File . '<br>';


				// if($Root == $Path || $Path == '/' || empty($Path)){

				// 	$Current .= $Path;
					
				// 	continue;

				// }

				// else{

				// 	$File = $Root . ($Current) . $Path . '/.path.protocol.php';

				// 	$Is = \is_file($File);
	
				// 	var_dump($File);
	
				// 	if($Is){
						
				// 		require $File;
						
				// 	}
					
				// 	if(!$Is){
						
				// 		$Current .= '' . $Path . '/';
						
				// 	}
					
				// }


				
			}


			// echo '</pre>';

			return $this;
			
		}



		public function View($Name, $UsesAjax = false) : self{

			if(self::UsesAjax() === TRUE && $UsesAjax === FALSE){

				return $this;
				
			}

			global $GGN;

			$Dir =  is_string($this->ViewDir) ? $this->ViewDir : ($this->Ui->App->{'Dir:App:Views'} ?? $GGN->{'Dir:Viewer'});

			$Path = $Dir . $Name . self::ViewExt;

			$Is = is_file($Path);

			

			if($Is){

				$this->EventTrigger('BeforeView');
				
				include $Path;

				$this->EventTrigger('AfterView');

			}

			if(!$Is){

				if($this->EventTrigger('View404')){}

				else{

					exit('Page not found');

				}

			}

			return $this;
			
		}
		
		


	}

