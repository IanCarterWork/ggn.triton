<?php

namespace GGN\Serializer;




class ObjectToXML{


  	var $Output;


  	public function __construct(Object $Object){

        $this->Output = (new \SimpleXMLElement('<root>' . $this->Parse($Object) . '</root>'))->asXML();

    }



  	public function Parse(Object $Object) : ?String{

      	$Out = '';

		foreach($Object as $Key => $Value){

          	$Out .= '<' . $Key . '>';

          	$Out .= (is_string($Value))

              	? ($Value)

              	: (

                  	(is_object($Value))

                  	? $this->Parse($Value)

                  	: ((String) $Value)

                );

          	$Out .= '</' . $Key . '>';

        }


      	return $Out;

    }



}