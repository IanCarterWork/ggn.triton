<?php

	namespace GGN\Security;



	class Session{



      	protected $File;

      	protected $Dir;





      	public function __construct(?String $Name = null){

          	global $GGN;


			$Name = $Name ?? 'Default';

			$this->Duration = $GGN->{'Client:Session:Duration'};

            $this->iP = $GGN->{'Client:iP'};

            $this->iD = $GGN->{'Client:iD'};

            $this->Platform = $GGN->{'Client:Platform'} ?: 'Generic';

            $this->Browser = $GGN->{'Client:Browser:Name'};

            $this->BrowserVersion = $GGN->{'Client:Browser:Version'};

            $this->File = $GGN->{'Dir:Sessions'}

                . $this->iP . '/'

                . $this->iD . '/'

                . $this->Platform . '.'

                . str_replace(' ', '', $this->Browser) . '/'

                . $Name . ''

                . '.session'

            ;

			// var_dump($this->File);exit;

          	$this->Dir = dirname($this->File);


          	if(!is_dir($this->Dir)){

              	mkdir($this->Dir,0777,true);

            }


        }






      	static public function Set(String $Name, $Value = null, ?Int $Duration = null) : bool{


          	$Cl = new self($Name);


          	$Cl->Value = $Value;

          	$Cl->Duration = $Duration ?? $Cl->Duration;

          	$Cl->Timeout = time() + $Cl->Duration;

			$SetDir = (!is_dir(\dirname($Cl->File))) ? \chmod(\dirname($Cl->File), 0777) : true;

			$Is = file_put_contents($Cl->File, json_encode($Cl));

			// var_dump($Name, $SetDir, $Is, $Cl);

			if($Is){

              	return true;

            }

          	else{

              	return false;

            }


        }






      	static public function Get(String $Name) : ?Object{

          	global $GGN;


          	$Cl = new self($Name);

			// var_dump($Cl->File);echo '<br>';
			  
			if(is_file($Cl->File)){

              	$Get = json_decode(file_get_contents($Cl->File));

              	if(is_object($Get)){

                  	if(

                      	isset($Get->Value)

                      	&& isset($Get->Timeout)

                      	&& isset($Get->iP)

                      	&& isset($Get->iD)

                      	&& isset($Get->Platform)

                      	&& isset($Get->Browser)

                    ){

                      	if(

                          	($Get->Timeout > time() || $Get->Timeout == 0)

                          	&& $Get->iP == $GGN->{'Client:iP'}

                          	&& $Get->iD == $GGN->{'Client:iD'}

                          	&& $Get->Platform == $GGN->{'Client:Platform'}

                          	&& $Get->Browser == $GGN->{'Client:Browser:Name'}

                        ){

                          	return $Get;

                        }

                    }

                }

            }

          	return null;

        }






      	static public function Refresh(String $Name) : ?Object{

			if(is_object($Get = self::Get($Name))){

              	if(self::Set($Name, $Get->Value, $Get->Duration)===true){

                  	return $Get;

                }

            }

          	return null;

        }







      	static public function Destroy(String $Name) : ?Bool{

          	global $GGN;

          	$Cl = new self($Name);

			if(is_file($Cl->File)){

              	unlink($Cl->File);

              	return true;

            }

          	return false;

        }









    }