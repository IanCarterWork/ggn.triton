<?php

namespace GGN;


	use GGN;

	use GGN\Kernel\Vars;

	use GGN\Strings;


/*
	ini_set('max_input_time', 300);

	ini_set('max_execution_time', 300);
*/

	//ini_set("memory_limit", Vars::Get('System.Memory.Limit') );



	if(!class_exists("\\" . __NAMESPACE__ . "\Image")){

      	class Image{

          	var $File;

          	// var $Co;

          	public $Option;

          	var $Binary;

          	var $Infos;

          	var $Type;

          	var $Saved;


          	public $Main;

          	protected $Stabilized;

          	protected $Convolves = [

                'lighten' => [[[0,0,0], [0,12,0], [0,0,0]], 9, 0]

                , 'darken' => [[[0,0,0], [0,6,0], [0,0,0]], 9, 0]

                , 'sharpen' => [[[-1,-1,-1], [-1,16,-1], [-1,-1,-1]], 8, 0]

                , 'sharpen-alt' => [[[0,-1,0], [-1,5,-1], [0,-1,0]], 1, 0]

                , 'emboss' => [[[1,1,-1], [1,3,-1], [1,-1,-1]], 3, 0]

                , 'emboss-alt' => [[[-2,-1,0], [-1,1,1], [0,1,2]], 1, 0]

                , 'blur' => [[[1,1,1], [1,15,1], [1,1,1]], 23, 0]

                , 'gblur' => [[[1,2,1], [2,4,2], [1,2,1]], 16, 0]

                , 'edge' => [[[-1,-1,-1], [-1,8,-1], [-1,-1,-1]], 9, 0]

                , 'edge-alt' => [[[0,1,0], [1,-4,1], [0,1,0]], 1, 0]

                , 'draw' => [[[0,-1,0], [-1,5,-1], [0,-1,0]], 0, 0]

                , 'mean' => [[[1,1,1], [1,1,1], [1,1,1]], 9, 0]

                , 'motion' => [[[1,0,0], [0,1,0], [0,0,1]], 3, 0]

            ];



          	public function __construct(String $Source = '', Array $Option = []){

				$this->Initialize($Source, $Option);

            }



          	public function Initialize(String $Source = '', Array $Option = []){

				$this->Option = (new GGN\EObject($Option))->toObject();

					(

						Strings\IsBinary($Source)

						? $this->Binary($Source)

						: $this->File($Source)

					)

						->Infos()

						->Type()

						->Main()

						->SetOptions()

				;

				return $this;

            }



          	public function SetOptions(){

              	$this->Option->Out = $this->Option->Out ?? null;

              	$this->Option->Quality = $this->Option->Quality ?? 7;

              	return $this;

            }


          	public function File(String $File){

              	$this->File = (is_file($File)) ? $File : null;

               	$this->Main = ($File);

               	// $this->SourceType = '-file';

              	return $this;

            }


          	public function Binary(String $Binary){

				$this->Binary = $Binary;

				// $this->SourceType = '-binary';

              	return $this;

            }




          	public function Main(){

              	if($this->File){

                    switch($this->Type){

                        case 'jpg':

                        case 'jpeg':

                        	$this->Main = imagecreatefromjpeg($this->File);

                        break;


                        case 'png':

                        	$this->Main = imagecreatefrompng($this->File);

                        break;


                        case 'gif':

                        	$this->Main = imagecreatefromgif($this->File);

                        break;


                    }

                }

              	if($this->Binary){

					$this->Main = imagecreatefromstring($this->Binary);

                }

              	return $this;


            }



          	public function Type(){

              	$this->Type = (is_array($this->Infos)) ? str_replace('image/', '', $this->Infos['mime']) : null;

              	return $this;

            }


          	public function Infos(){

              	if($this->File){

					$this->Infos = getimagesize($this->File);

                }

              	if($this->Binary){

					$this->Infos = getimagesizefromstring($this->Binary);

                }


              	return $this;

            }





          	public function Stabilize(&$Thumb){


                if($this->Type == 'png'){

                    imagealphablending($Thumb, false);

                    imagesavealpha($Thumb, true);

                }


                if($this->Type=='gif'){

                    $GIFBGColor = imagecolorallocate($Thumb, 210, 255, 229);

                    imagefilledrectangle($Thumb, 0, 0, 99, 99, $GIFBGColor);

                    imagecolortransparent($Thumb, $GIFBGColor);

                }


              	return $Thumb;

            }





          	public function Save(?String $OutPut = null){


               	$this->Stabilize($this->Main);


              	if($this->Type == "png"){

                  	imagealphablending($this->Main, true);

				}
				

				if(is_string($this->Option->Out)){

					$Dir = \dirname($this->Option->Out);

					if(!is_dir($Dir)){

						$DirCreated = \mkdir($Dir, 0777, true);

					}
					
				}


              	switch($this->Type){

                	case 'jpg':

                  	case 'jpeg':


                      	$this->Saved = imagejpeg(

                          	$this->Main

                          	, $this->Option->Out

                          	, $this->Option->Quality * 10

                        );

                  	break;


					case 'png':

                      	$this->Saved = imagepng(

                          	$this->Main

                          	, $this->Option->Out

                          	, $this->Option->Quality

                        );

                  	break;


                   	case 'gif':

                      	$this->Saved = imagegif(

                          	$this->Main

                          	, $this->Option->Out

                          	, $this->Option->Quality

                        );

                  	break;


              	}


				imagedestroy($this->Main);
				  
				if(is_string($this->Option->Out)){
					
					return \file_get_contents($this->Option->Out);
					
				}
				

              	return $this;


            }





          	public function SetHeader(){

               	header("Content-Type:image/" . $this->Type);

              	return $this;


            }





          	public function Filter(String $Name, $Value){

              	$Val = explode(",", trim($Value));


              	switch(strtolower($Name)){


                  	case 'convolate':

                    	if(

                          isset($this->Convolves[$Val[0]])

                          && is_array($Mx = $this->Convolves[$Val[0]])

                        ){

                            imageconvolution(

                                $this->Main

                                , $Mx[0]

                                , $Mx[1]

                                , $Mx[2]

                            );

                        }

                    break;




                  	case 'colorize':

                    	imagefilter(

                          	$this->Main

                          	, IMG_FILTER_COLORIZE

                          	, isset($Val[0]) ? $Val[0] : 0

                          	, isset($Val[1]) ? $Val[1] : 0

                          	, isset($Val[2]) ? $Val[2] : 0

                        );

                    break;




                  	case 'smooth':

                  	case 'contrast':

                  	case 'brightness':

                    	imagefilter(

                          	$this->Main

                          	, constant('IMG_FILTER_' . strtoupper($Name))

                          	, isset($Val[0]) ? $Val[0] : 1

                        );

                    break;




                  	default:

                    	if(defined($Get = 'IMG_FILTER_' . strtoupper(str_replace(['-', '.'], '_', $Name)))){

                            imagefilter(

                                $this->Main

                                , constant($Get)

                            );

                        }

					break;

                }

              	return $this;

            }




            public function Assign(Array $Applies = []) :self{

                foreach($Applies as $Apply){

                    $Dot = explode(':', $Apply);

                    switch($Dot[0]){

                        case 'filter':

                        case 'fx':

                            $Cmd =	isset($Dot[1]) ? $Dot[1] : '';

                            $Ex = explode('=', $Cmd);

                            $this->Filter(

                                $Ex[0]

                                , isset($Ex[1]) ? $Ex[1] : false

                            );

                        break;


                      default:

                            $Ex = explode('=', $Apply);

                            if(method_exists($this, $Ex[0])){

                                $this->{$Ex[0]}($Ex[1]);

                            }

                      break;

                    }

                }

                return $this;

            }





          	public function Scale($Scale = 100){

              	$Sc = ($Scale > 100 ? 100 : $Scale) / 100;

              	$Width = intval(floor($Sc * $this->Infos[0]));

              	$Height = intval(floor($Sc * $this->Infos[1]));

               	$Thumb = imagecreatetruecolor($Width, $Height);


              	$this->Stabilize($Thumb);

              	imagecopyresampled(

					$Thumb

                  	, $this->Main

                  	, 0, 0, 0, 0

                  	, $Width

                  	, $Height

                  	, $this->Infos[0]

                  	, $this->Infos[1]

                );


              	$this->Main = $Thumb;

              	return $this;

            }





          	public function Resize($Size){

              	$Ex = explode('x', $Size);

              	$Width = $Ex[0] * 1;

              	$Height = ($Ex[1] ?? $Ex[0]) * 1;


				if($this->Infos[0] >= $Width && $this->Infos[1] >= $Height){

                    $Thumb = imagecreatetruecolor($Width, $Height);

                    $this->Stabilize($Thumb);

                    imagecopyresampled(

                        $Thumb

                        , $this->Main

                        , 0, 0, 0, 0

                        , $Width

                        , $Height

                        , $this->Infos[0]

                        , $this->Infos[1]

                    );

                    $this->Main = $Thumb;

                }

				return $this;

            }





          	public function AdaptHeight($Width){

               	$Height = intval(floor(($this->Infos[1] * $Width) / $this->Infos[0]));

               	$this->Resize($Width . 'x' . $Height);

				return $this;

            }




          	public function AdaptWidth($Height){

               	$Width = intval(floor(($this->Infos[0] * $Height) / $this->Infos[1]));

               	$this->Resize($Width . 'x' . $Height);

				return $this;

            }





          	public function AdaptLess($Size){

              	$Ex = explode('x', $Size);

              	$Width = $Ex[0] * 1;

              	$Height = ($Ex[1] ?? $Ex[0]) * 1;

				return ($this->Infos[0] >= $this->Infos[1])

                  	? $this->AdaptHeight($Width)

                  	: $this->AdaptWidth($Height)

              	;

            }





          	public function AdaptPlus($Size){

              	$Ex = explode('x', $Size);

              	$Width = $Ex[0] * 1;

              	$Height = ($Ex[1] ?? $Ex[0]) * 1;

				return ($this->Infos[0] <= $this->Infos[1])

                  	? $this->AdaptHeight($Width)

                  	: $this->AdaptWidth($Height)

              	;

            }






          	public function Crop($Size){

              	$Ex = explode(',', $Size);

              	$Width = $Ex[0] * 1;

              	$Height = ($Ex[1] ?? $Ex[0]) * 1;

              	$X = ($Ex[2] ?? 0);

              	$Y = ($Ex[3] ?? ($X === 'center' || $X === 'c' ? $X : 0));


				if($this->Infos[0] >= $Width && $this->Infos[1] >= $Height){



                  	$X = is_numeric($X) ? $X * 1 : strtolower($X);

                  	$X = ($X === 'left' || $X === 'l') ? 0 : $X;

                  	$X = ($X === 'center' || $X === 'c') ? floor(($this->Infos[0] - $Width) / 2) : $X;

                  	$X = ($X === 'right' || $X === 'r') ? ($this->Infos[0] - $Width) : $X;






                  	$Y = is_numeric($Y) ? $Y * 1 : strtolower($Y);

                  	$Y = ($Y === 'top' || $Y === 't') ? 0 : $Y;

                  	$Y = ($Y === 'center' || $Y === 'c') ? floor(($this->Infos[1] - $Height) / 2) : $Y;

                  	$Y = ($Y === 'bottom'|| $Y === 'b') ? ($this->Infos[1] - $Height) : $Y;






                  	if(

                      	($Thumb = imagecrop(

                          	$this->Main

                          	, [

                              	'x' => $X

                              	, 'y' => $Y

                              	, 'width' => $Width

                              	, 'height' => $Height

                            ]

                        )) !== FALSE

                    ){

                      	$this->Stabilize($Thumb);

                      	$this->Main = $Thumb;

                    }

                }

				return $this;

            }








        }


    }