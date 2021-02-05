<?php

namespace GGN\Security;


	use GGN;

	use GGN\xDump;

	use GGN\Encryption;



class Token{


	const Prefix = 'Token@';



  	static public function Create(Object $Payload) : ?String{

		global $GGN;

      	$Key = Encryption\Customize(GGN\ALPHA_NUMERIC, 64) . time();

		$Payload->Key = $Key;

		$Payload->Serve = $Payload->Serve ?: $GGN->{'ARC:Current:Slug'};

		$Payload->Method = $Payload->Method ?: $GGN->{'ARC:Current:Method'} ?: 'GET';

		$Duration = $Payload->Duration ?: 60*5;

		unset($Payload->Duration);



		  
		if(Session::Set(self::Prefix . $Key, $Payload, $Duration) === TRUE){

			return $Key;

		}


		// var_dump($Payload);exit;

      	return null;

    }




  	static public function Check(String $Key) : bool{

		if(is_object($Get = Session::Get(self::Prefix . $Key))){

			global $GGN;

			$Serve = $GGN->{'ARC:Current:Slug'};

			$Method = $GGN->{'ARC:Current:Method'};

          	return (
				  
				$Get->Value->Key == $Key

				|| (isset($Get->Value->Serve) && ($Get->Value->Serve == $Serve || $Get->Value->Serve == '*') )

				|| (isset($Get->Value->Method) && ($Get->Value->Method == $Method || $Get->Value->Method == '*') )
			
			);

        }

      	return false;

    }





	static public function Refresh(String $Key) : bool{

		return (is_object(Session::Refresh(self::Prefix . $Key))) ? TRUE: FALSE;

    }



  	static public function Destroy(String $Key){

		Session::Destroy(self::Prefix . $Key);

    }


}