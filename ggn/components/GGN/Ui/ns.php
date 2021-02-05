<?php

	namespace GGN;


	/*
     * Class Ui
     *
    */

	class Ui{

		const Ext = '.view';



		/* Moteur */
		var $Engine;



		/* Theme */
		var $Theme;




		/*
			* Construct
			* @Param Object $Config (e.g: stdClass)
		*/

		public function __construct($Config = null){

			$this->Props($Config);

		}


		public function Prop(String $Key, $Value){

			$this->{$Key} = $Value;

			return $this;

		}

		public function Props($Config = null){

			if(is_object($Config) || is_array($Config)){

				foreach($Config as $Key => $Value){

					$this->{$Key} = $Value;

				}

			}

			return $this;

		}


		public function Viewer(String $Slug, Object $Assigner = null, ?String $Dir = null){

			global $GGN;

			$this->Viewer = ($Dir ?: $GGN->{'Dir:Viewer'}) . $Slug . self::Ext;


			// xDump::Debug($Slug, $Dir, $this->Engine->CompilatorType, $this->Viewer);


			// if(is_file($this->Viewer)){

				if(isset($this->Engine) && is_object($this->Engine)){

					switch($this->Engine->CompilatorType ?? null){

						case 'Compile:Content':

							$State = (new $this->Engine->Name(

								$this->Engine->Version ?? false

								, file_get_contents($this->Viewer)

								, (new Theme(

									$this->Theme->Name 

									, $this->Theme->Palette ?? 'default'

									, $this->Theme->Tone ?? 'dark'

									, $this->App ?: null

									, Theme::TYPE_BUFFER

								))

								, $Assigner

							))

								->Compile()

							;

						break;

						// case 'Native:PHP': 
							
						default:

							$this->Theme = (new Theme(

								$this->Theme->Name 

								, $this->Theme->Palette ?? 'default'

								, $this->Theme->Tone ?? 'dark'

								, $this->App ?: null

								, Theme::TYPE_NATIVE

							))

								->Prop('Engine', $this->Engine)

								->Prop('ViewDir', $Dir ?: $GGN->{'Dir:Viewer'})

								// ->Prop('Ui', $this)

								->View($Slug, true)
								
								->Build()

							;

							// var_dump($Slug, $Dir, $this->Theme);

						break;

					}

				}

				else{

					include $this->Viewer;
					
				}

				//xDump::Debug($this);

			// }

			// else{

			// 	self::Error('Viewer:404');

			// }

			return $this;

		}


		static public function Error(String $Slug = 'Page:404'){

			exit("Error/" . $Slug);

		}

	}

