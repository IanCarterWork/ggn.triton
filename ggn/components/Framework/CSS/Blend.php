<?php

	namespace Framework\CSS;

		use GGN;

		use GGN\xDump;

		use Framework\Ability;



	if(!class_exists("\\" . __NAMESPACE__ . "\Blend")){

      	class Blend{


          	use Ability\ColoriMetry;


          	public function __construct(GGN\Settings $Settings){

              	if(!isset($Settings->__FAILED__)){

                  	foreach($Settings as $Key => $Value){

                      	$Ex = explode(':', $Key);

                      	$Prefix = strtolower($Ex[0]);

                      	$Name = $Ex[1];


                      	if($Prefix == 'color'){

                          	$this->{$Name . ':Lite:Plus'} = $this->ColorVariante($Value, $this->ColorVarianteHighPlus);

                          	$this->{$Name . ':Lite'} = $this->ColorVariante($Value, $this->ColorVarianteHigh);

                          	$this->{$Name} = $Value;

                          	$this->{$Name . ':High'} = $this->ColorVariante($Value, -1 * $this->ColorVarianteHigh);

                          	$this->{$Name . ':High:Plus'} = $this->ColorVariante($Value, -1 * $this->ColorVarianteHighPlus);


                          	$this->{$Name . ':Lite:Plus:RGB'} = implode(',', $this->toRGB($this->{$Name . ':Lite:Plus'}));

                          	$this->{$Name . ':Lite:RGB'} = implode(',', $this->toRGB($this->{$Name . ':Lite'}));

                          	$this->{$Name . ':RGB'} = implode(',', $this->toRGB($Value));

                          	$this->{$Name . ':High:RGB'} = implode(',', $this->toRGB($this->{$Name . ':High'}));

                          	$this->{$Name . ':High:Plus:RGB'} = implode(',', $this->toRGB($this->{$Name . ':High:Plus'}));


                        }

                    }

                }


            }


        }



    }