<?php


namespace GGN{

	global $GGN;

  	use GGN\Client;


	\ini_set("track_errors", "Off");


	/* Constante / DEBUT */

		const UN_QUOTE = ':strip.quote';

		const UN_NUM = ':strip.numeric';

		const UN_PAR = ':strip.par';

		const ALPHA = 'a b c d e f g h i j k l m n o p q r s t u v w x y z A B C D E F G H I J K L M N O P Q R S T U V W X Y Z';

		const ALPHA_LOWER = 'a b c d e f g h i j k l m n o p q r s t u v w x y z';

		const ALPHA_UPPER = 'A B C D E F G H I J K L M N O P Q R S T U V W X Y Z';

		const NUMERIC = '0 1 2 3 4 5 6 7 8 9';

		const ALPHA_NUMERIC = 'a b c d e f g h i j k l m n o p q r s t u v w x y z A B C D E F G H I J K L M N O P Q R S T U V W X Y Z 0 1 2 3 4 5 6 7 8 9';

		const ALPHA_NUMERIC_LOWER = 'a b c d e f g h i j k l m n o p q r s t u v w x y z 0 1 2 3 4 5 6 7 8 9';

		const ALPHA_NUMERIC_UPPER = 'A B C D E F G H I J K L M N O P Q R S T U V W X Y Z 0 1 2 3 4 5 6 7 8 9';

		const DAYS = [

			'lundi'

			, 'Mardi'

			, 'Mercredi'

			, 'Jeudi'

			, 'Vendredi'

			, 'Samedi'

			, 'Dimanche'

		];

	/* Constante / FIN */







    /**
     * Objet Vide / DEBUT

     */

            class EObject{


             	public function __construct(Array $Args = []){

                	foreach($Args as $Name => $Value){

                  		$this->{$Name} = $Value;

                	}

              	}


              	public function toArray(Array $Add = []){

					$Out = [];

                  	foreach($this as $Name => $Value){

                      	$Out[$Name] = $Value;

                    }

                  	return array_merge($Out, $Add);

                }


              	public function toObject(){

                  	return json_decode(json_encode($this));

                }


            }

    /* Objet Vide / FIN */





	/**
     * Components Autoload
     *
	*/
	

	spl_autoload_register(function(String $NS){

      	global $GGN;


      	$Path = $GGN->{'Dir:Components'} . implode("/", explode("\\", $NS));

      	if(

          	is_dir($Path)

          	&& is_file($Path . '/ns.php')

        ){

			require $Path . '/ns.php';

        }

      	else if(is_file($Path . '.php')){

          	require $Path . '.php';

        }

      	else{

			// echo '<pre>' . var_dump(get_included_files()) . '</pre>';

			Dial\Error(

				'Composante introuvable'

				,'Gestionnaire des Composantes : <br><b>' . $NS . '</b>'

				,'<h1>Details : </h1>'

              		.'<b>' . $NS . '</b> introuvable '

              		.'<ul>'

              			.'<li>Les composantes sont localisées dans <b>' . $GGN->{'Dir:Components'} . '</b></li> '

              			.'<li>Si le fichier correspondant à ce composant existe, vérifiez que la fonctionnalité demandé existe.</b></li> '

					.'</ul>'

					.'<h1>Trace : Inclusions</h1>'

					. \implode("<br>\n", get_included_files())

			);

			

        }


	});



	/**
     * Gestionnaire d'erreurs
     *
    */

	if($GGN->{'Error:Reporting'} === TRUE){

		error_reporting(0);

		$GGN->{'Error:ShutDown'} = register_shutdown_function("GGN\Error\ShutDown");

		$GGN->{'Error:OldHandler'} = set_error_handler("GGN\Error\Handler");

		//error_reporting(1);

	}





	/**
     * URL de GGN
     *
    */

	$GGN->{'Server:Protocol'} = (
		
		((stripos( $_SERVER['SERVER_PROTOCOL'], 'https') === true) 
		
		|| ($_SERVER['HTTPS'] == 'on')

		|| ($_SERVER['SERVER_PORT'] == '443')

		) ? 'https://' : 'http://' )
		
	;

	
	function HttpHost(){

		global $GGN;

      	$URL = (

          $GGN->{'Server:Protocol'}

          . $_SERVER["SERVER_NAME"]

        //   . ($_SERVER["SERVER_PORT"] == 80 ? '' : ':' . $_SERVER['SERVER_PORT'])

          . dirname($_SERVER["PHP_SELF"])

          . "/"

        );

      	return( (substr($URL, -2) == '//') ? substr($URL, 0, -1) : $URL );

	};
	

	$GGN->{'Http:Host'} = $GGN->{'Http:Host'} ?: HttpHost();





	/**
     * Infos du Client
     *
    */

  	$GGN->{'Client:Infos'} = Client\Detect();

  	$GGN->{'Client:iD'} = Client\iD();

  	$GGN->{'Client:iP'} = Client\iP();

  	$GGN->{'Client:Platform'} = $GGN->{'Client:Infos'}['platform'] ?? null;

  	$GGN->{'Client:Browser:Name'} = $GGN->{'Client:Infos'}['browser'] ?? null;

  	$GGN->{'Client:Browser:Version'} = $GGN->{'Client:Infos'}['version'] ?? null;






	/**
     * Variables
     *
    */

	function ParsingVars(String $Value, Array $Put = []){

		global $GGN;

		preg_match_all(
			
			\GGN\Patterns\Expressions::DoubleSingle('(.*)')

			, $Value

			, $Matches

			, \PREG_SET_ORDER
		
		);

		if(!empty($Matches)){

			$Put = (Object) array_merge([], $Put);

			$Put = (Object) array_merge([], (Array) $GGN);

			foreach($Matches as $Match){

				if(isset($Put->{$Match[1]})){

					$Value = str_replace($Match[0], $Put->{$Match[1]}, $Value);

				}
				
			}

		}

		return $Value;

	}



}








namespace GGN\Client{


  	use GGN;

  	use GGN\Encryption;


	function iD(){

		global $GGN;

		$Name = $GGN->{"ClientDevice:Cookie:iD"};

      	//unset($_COOKIE[$Name]);

		if(isset($_COOKIE[$Name])){

			return $_COOKIE[$Name];

		}

		else{

			setcookie(

				$Name

				, Encryption\Customize(GGN\ALPHA, 4)

              		. Encryption\Customize(GGN\ALPHA_NUMERIC, 60)

              		. time()

				, ( time() + ($GGN->{"ClientDevice:Cookie:Duration"} * 1) )

				, "/"

			);

		}

    }






	function iP(){

		global $GGN;

		$Out = false;

		$Headers = [

			'HTTP_VIA'

			, 'HTTP_X_FORWARDED_FOR'

			, 'HTTP_FORWARDED_FOR'

			, 'HTTP_X_FORWARDED'

			, 'HTTP_FORWARDED'

			, 'HTTP_CLIENT_IP'

			, 'HTTP_FORWARDED_FOR_IP'

			, 'VIA'

			, 'X_FORWARDED_FOR'

			, 'FORWARDED_FOR'

			, 'X_FORWARDED'

			, 'FORWARDED'

			, 'CLIENT_IP'

			, 'FORWARDED_FOR_IP'

			, 'HTTP_PROXY_CONNECTION'

			, 'REMOTE_ADDR'

		];


		foreach($Headers as $Header){

			if(isset($_SERVER[$Header])){

				$Out = $_SERVER[$Header];

				break;

			}

		}


		return str_replace("::1", "127.0.0.1",$Out);


    }






	function Infos(){

		$UA = isset($_SERVER["HTTP_USER_AGENT"]) ? $_SERVER["HTTP_USER_AGENT"] : false;

		// var_dump($UA);exit;

		$Out = [

			"browser" => null

			, "platform" => null

			, "version" => null

			, "model" => null

		];

		if(is_string($UA)){

			$Browsers = [

				"Edg" => "Microsft Edge"

				,"Firefox"

				, "Chrome"

				, "MSIE" => "msie trident"

				, "Edge"

				, "Safari"

				, "Symbian"

				, "Ovi"

				, "Opera"

				, "OperaMini"

				, "BlackBerry"

			];

			$OS = [

				"Android" => " android "

				, "iOS"

				, "SymbianOS"

				, "BlackBerry"

				, "TV"

				, "Win" => "windows"

				, "MacOSX" => "Mac OS X"

				, "MacOS" => "Macintosh; "

				, "Linux"

			];


			foreach($Browsers as $Name => $Brow){

				$Brs = explode(" ", is_numeric($Name) ? $Brow : $Name);

				$Found = false;

				// echo "<pre>"; var_dump('Browser Type', $Brs, $Brow); echo "</pre>";

				foreach($Brs as $Browser){

					$P0 = '/' . strtolower($Browser) . '\/((?:[0-9]+\.?)+)/i';

					$P1 = '/' . strtolower($Browser) . '((?:[0-9]+\.?)+)\/((?:[0-9]+\.?)+)$/i';


					if(preg_match($P0, $UA, $About)){

						$Out['browser'] = $Brow;

						$Out['browser.Agent'] = $Browser;

						$Out['version'] = $About[1];

						$Found = true;

						break;

					}


					if(preg_match($P1, $UA, $About)){

						$Out['browser'] = $Brow;

						$Out['browser.Agent'] = $Browser;

						$Out['model'] = $About[1];

						$Out['version'] = $About[2];

						$Found = true;

						break;

					}

				}


				if($Found === true){break;}

			}


			foreach($OS as $Name => $Get){

				$GOs = explode(" ", is_numeric($Name) ? $Get : $Name);

				$Found = false;

				// echo '<pre>';var_dump( "----------",$GOs, $Get);echo '</pre>';

				foreach($GOs as $O){

					$P0 = '/' . strtolower($Get) . '/i';

					if($Fo = preg_match($P0, strtolower($UA))){

						$Out['platform'] = $O;

						$Found = true;

					}

					// echo '<pre>';var_dump($O, '=', $Fo, $P0);echo '</pre>';

				}

				if($Found === true){break;}

			}


		}

		return $Out;

	}





	function Name(){

		return (is_array($Get = Infos()) && isset($Get["browser"]))

			? $Get["browser"]

			: null

		;

	}




	function Version(){

		return (is_array($Get = Infos()) && isset($Get["version"]))

			? $Get["version"]

			: null

		;

	}




	function OS(){

		return (is_array($Get = Infos()) && isset($Get["platform"]))

			? $Get["platform"]

			: null

		;

	}




	function Detect(String $Key = ''){
		
		$Infos = Infos();

		// var_dump($Infos);exit;

		if(is_array($Infos) ){

			return ($Infos[$Key] ?? $Infos);

        }

		else{

			if(!is_array($Get = get_browser()) ){

				return  ((!empty($Key) && isset($Get[$Key])) ? $Get[$Key] : $Get);

			}

        }

	}






}








namespace GGN\Structuring{


	/**
     * Interface UiController
    */

	interface UiAssigner{

      	static public function Set() : Object;

      	static public function Mount() : Object;

    }



	/**
     * Interface UiDOMEngineRule
    */

	interface UiEngine{

      	public function __construct(String $Version, String $Buffer, \GGN\Theme $Theme, Object $Controller);

      	public function Assigner(String $Data, Array $Inject):String;

      	public function Compile(Bool $Return);

    }



	/**
     * Interface UiDOMEngineRule
    */

	interface UiDOMEngineRule{

      	public function __construct(Object $Engine);

      	public function Swap(\DOMDocument &$DOM, String $Slug = '');

      	static public function Main();

    }


}






namespace GGN\Encryption{


      	function Customize($Sampled, $Len = 8){

          	$Output = false;

			if(gettype($Sampled) == "string"){

             	$Sampled = explode(" ", $Sampled);

            }

          	if(is_array($Sampled)){

              	$Output = "";

          		for($x = $Len; $x > 0; $x--){

              		$Output .= $Sampled[ mt_rand(0, count($Sampled) - 1) ];

            	}

            }

          return $Output;

        }




}







namespace GGN\Assigner{

  	class Ui{

		static public function Mount(Array $Base = [], Array $Put = []):Object{

          	$Assign = (Object) $Base;

			foreach($Put as $Key => $Value){

				$Assign->{$Key} = $Value;

			}

			return $Assign;

		}


    }

}








namespace GGN\Strings{


	function MatchScript(String $String) : bool{

      	return (

          (preg_match('/<?php(.*)?>/Us', $String) == 1)

          || (preg_match('/<?=(.*);?>/Us', $String) == 1)

        ) ? true : false;

    }


  	function IsBinary(String $String) : bool{

      	return preg_match('/[^\x20-\x7E\t\r\n]/', $String) > 0;

    }



}








namespace GGN\Objects{


  	function toURLQuery(...$Objects) : String{

		return toString($Objects, '=', '&');

    }


  	function toString($Entries, ?String $Intersection = '', ?String $Separator = '', Bool $UseGroup = false, ?String $GroupName = null) : String{

		$Return = null;


		foreach($Entries as $Name => $Entry){
			
			$Return = $Return ?: [];

			if(is_object($Entry) || \is_array($Entry)){


				foreach($Entry as $Key => $Value){

					if(is_array($Value) || is_object($Value)){

						$Return[] = toString($Value, $Intersection, $Separator, true, $Key . '[]');
						
					}

					else{

						$Return[] = toString([$Value], $Intersection, $Separator, true, $Key);
						
					}

				}

			}

			else{
				
				$Return[] =  ($UseGroup ? ($GroupName ?: $Name) : $Name) . ($Intersection ?: '') . ($Entry);

			}

		}
			

		return is_array($Return) ? \implode($Separator ?: '', $Return) : '';

    }



}








namespace GGN\Error{

	use GGN\Dial;



	/**
     * A l'arret du script
     *
    */

	function ShutDown(){

      	global $GGN;

		$Error = error_get_last();

      	if($Error !== NULL){

            $Error['type'] = $Error['type'] ?? null;

            $Error['type'] = ($Error['type'] == E_ERROR) ? 'E_ERROR' : $Error['type'];

            $Error['type'] = ($Error['type'] == E_USER_ERROR) ? 'E_USER_ERROR' : $Error['type'];

            $Error['type'] = ($Error['type'] == E_USER_WARNING) ? 'E_USER_WARNING' : $Error['type'];

            $Error['type'] = ($Error['type'] == E_USER_NOTICE) ? 'E_USER_NOTICE' : $Error['type'];


            $SysInfo = "" . $GGN->{'Infos:Name'} . " (" . $GGN->{'Infos:Version'} . ")"

                . ", PHP : " . PHP_VERSION . ", Serveur : " . PHP_OS . "";


				$Messages = $Error['message'] ?: 'ShutDown';
				
				$_Messages = str_replace('Stack trace:', '', implode('<br>', array_reverse(\explode('#', $Error['message'] ?: null))));


			// if(is_array($_Messages)){

			// 	$Messages = $_Messages;
				
			// }
			

			// echo '<pre>';
			// var_dump( $Messages );
			// echo '</pre>';
			// exit;


            Dial\Error(

                'Erreur Fatale'

                ,'Gestionnaire des Erreurs<br><b> { ' . ($Error['type']??'UNKNWON_ERROR') . ' } </b> '

                ,'<h1>Details</h1> '

                    .'<ul>'

                        .'<li>Fichier : <b>' . ($Error['file']??'UnKnown File') . '</b> </li>'

                        .'<li>Ligne : <b>' . ($Error['line']??'0') . '</b> </li>'

                    .'</ul>'

                    .'' . ($_Messages) . ''

                    .'<br><br>' . ($SysInfo) . ''

            );

        }

	}




	/**
     * Manivelle des erreurs
     *
    */

	function Handler($Type, $Message, $File, $Line){

		global $GGN;


        if(!(error_reporting() & $Type)) {return;}

        $SysInfo = "" . $GGN->{'Infos:Name'} . " (" . $GGN->{'Infos:Version'} . ")"

        . ", PHP : " . PHP_VERSION . ", Serveur : " . PHP_OS . "";

        switch ($Type) {

            case E_USER_ERROR:

                Dial\Error(

                    'Erreur Fatale'

                    ,'Gestionnaire des Erreurs <br><b> { E_USER_ERROR }</b>'

                    ,'<h1>Details</h1>'

                        .'<ul>'

                            .'<li>Fichier : <b>' . ($File) . '</b></li>'

                            .'<li>Ligne : <b>' . ($Line) . '</b></li>'

                        .'</ul>'

                        .'' . ($Message) . ''

                        .'<br><br>' . ($SysInfo) . ''

                );

            break;

            case E_USER_WARNING:

                Dial\Warning(

                    'Alerte'

                    ,'Gestionnaire des Erreurs <br><b>{ E_USER_WARNING }</b>'

                    ,'<h1>Details</h1>'

                        .'<ul>'

                            .'<li>Fichier : <b>' . ($File) . '</b></li>'

                            .'<li>Ligne : <b>' . ($Line) . '</b></li>'

                        .'</ul>'

                        .'' . ($Message) . ''

                        .'<br><br>' . ($SysInfo) . ''

                );

            break;

            case E_USER_NOTICE:

                Dial\Warning(

                    'Notice'

                    ,'Gestionnaire des Erreurs <br><b>{ E_USER_NOTICE }</b>'

                    ,'<h1>Details</h1>'

                        .'<ul>'

                            .'<li>Fichier : <b>' . ($File) . '</b></li>'

                            .'<li>Ligne : <b>' . ($Line) . '</b></li>'

                        .'</ul>'

                        .'' . ($Message) . ''

                        .'<br><br>' . ($SysInfo) . ''

                );

            break;

            default:

                Dial\Info(

                    'Information'

                    ,'Gestionnaire des Erreurs'

                    ,'<h1>Details</h1>'

                        .'<ul>'

                            .'<li>Fichier : <b>' . ($File) . '</b></li>'

                            .'<li>Ligne : <b>' . ($Line) . '</b></li>'

                        .'</ul>'

                        .'' . ($Message) . ''

                        .'<br><br>' . ($SysInfo) . ''

                );

            break;

         }

        return true;

    }


}









namespace GGN\Dial{



	/**
     * Show
    */

	function Show(String $Type = 'Info', String $Title = '', $About = null, String $Content = '', Bool $Die = true){

      	global $GGN;


		$Title = $Title??'';

		$Content = $Content??'';

		$ContentType = $_SERVER['CONTENT_TYPE'] ?: $GGN->{'HEADER:CONTENT:TYPE'} ?: NULL;


		switch(strtolower($ContentType)){

			case 'application/json':

				$Res = [

					'Response' => null

					,'Type' => $Type

					,'Title' => strip_tags($Title)

					,'About' => strip_tags($About)

					,'Content' => strip_tags($Content)
					
				];

				echo json_encode($Res);

			break;

			default:

				$UI = new UI([

					'Colorize' => $Type??'Info'
	
				]);
	
	
				$UI->Preset('HTML:Begin', [
	
					'Title' => ($Title . " - " . $GGN->{'Infos:Name'} . ", " . $GGN->{'Infos:Version'} )
	
				]);
		
					$UI->Preset('Box:Center',[
		
						'Title' => $Title
		
							, 'About' => $About
		
							, 'Content' => $Content
		
					]);
	
					$UI->Preset('Section:End');
	
				$UI->Preset('HTML:End');
  
			break;

		}
		

		if($Die == true){exit(1);}

	}




	/**
     * Error
    */

	function Error(String $Title = null, $About = null, String $Content, Bool $Die = true){

		Show('Error', $Title, $About, $Content, $Die);

	}




	/**
     * Warning
    */

	function Warning(String $Title = null, $About = null, String $Content, Bool $Die = true){

		Show('Warning', $Title, $About, $Content, $Die);

	}



	/**
     * Info
    */

	function Info(String $Title = null, $About = null, String $Content, Bool $Die = true){

		Show('Info', $Title, $About, $Content, $Die);

	}


	/**
     * Success
    */

	function Success(String $Title = null, $About = null, String $Content, Bool $Die = true){

		Show('Success', $Title, $About, $Content, $Die);

	}





	/**
     * Class : UI
    */

  	class UI{


      	var $Colorize = [

          	'Info' => [

              	'Text' => '#cacaca'

              	,'Background' => '#444'

            ]

          	,'Error' => [

              	'Text' => '#ff9696'

              	,'Background' => '#b13333'

            ]

          	, 'Warning' => [

              	'Text' => '#3d2805'

              	,'Background' => '#e5b665'

            ]

          	, 'Success' => [

              	'Text' => '#99ff97'

              	,'Background' => '#36b133'

            ]

        ];


      	public function __construct(Array $Option = []){

          	$this->Color = $this->Colorize[$Option['Colorize'] ?? ':Info'] ?? $this->Colorize[':Info'];

        }


		public function Preset(String $Slug, Array $Option = []){

			switch($Slug){

          		case 'HTML:Begin':

?>
<!doctype html>

<html lang="ltr">

    <head>

        <meta charset="utf-8">

        <meta name="viewport" content= "width=device-width, initial-scale=1.0">

        <title><?=$Option['Title']??'GGN Terminal';?></title>

        <meta http-equiv="cache-control" content="max-age=0" />

        <meta http-equiv="cache-control" content="no-store" />

        <meta http-equiv="expires" content="-1" />

        <meta http-equiv="expires" content="Tue, 01 Jan 1980 1:00:00 GMT" />

        <meta http-equiv="pragma" content="no-cache" />

        <style type="text/css">

          body{

            margin:0;

            padding:0;

            background-color:#282828;

            color:#888;

          }

          *,:root{

            font-family: arial;

            font-size:13px;

          }

          h1{font-size:32px;}

          .width-full{width:100vw;}
          .height-full{height:100vh;}

          .flex-box{display:flex;}
          .flex-row{flex-direction:row;}
          .flex-column{flex-direction:column;}
          .flex-items-start{align-items:start;}
          .flex-items-center{align-items:center;}
          .flex-items-end{align-items:end;}
          .flex-justify-start{justify-content:start;}
          .flex-justify-center{justify-content:center;}
          .flex-justify-end{justify-content:end;}

          .align-self-start{align-self:flex-start;}
          .align-self-center{align-self:center;}
          .align-self-end{align-self:flex-end;}

          .align-self-top{margin-bottom:0px;}
          .align-self-bottom{margin-top:0;}
          .align-self-left{margin-right:0px;}
          .align-self-right{margin-left:0px;}

          .dial-box{

            /* border-radius:16px; */

            color:#777;

            background-color:#282828;

            width:100vw;

            /* min-width:320px; */

            /* max-width:960px; */

            /* height:90vh; */

            min-height:100vh;

            /* max-height:960px; */

            /* overflow:hidden; */

          }

          .dial-box > .header > .sticker{

            width:16px;

            height:32px;

            margin:12px 0px 12px 12px;

            background-color:<?=$this->Color['Background'];?>;

            color:<?=$this->Color['Text'];?>;

            border-radius:32px;

          }

          .dial-box > .header > .title{

            padding: 12px 8px;

            font-size:18px;

          }

          .dial-box > .about{

            padding: 12px 16px;

            font-size:28px;

            color:<?=$this->Color['Background'];?>;

            border-bottom: 1px solid <?=$this->Color['Background'];?>;

            /* color:<?=$this->Color['Text'];?>; */

            min-height:128px;

            /* overflow-x:auto; */

          }

          .dial-box > .about .content{

            color:<?=$this->Color['Background'];?>;

          }

          .dial-box > .about * {

            font-size:inherit;

          }

          .dial-box > .container{

            flex:1 auto;

            /* width:inherit; */

            /* overflow:auto; */

          }

          .dial-box > .container > .content{

            padding: 8px 16px 16px;

            font-size:14px;

          }

          .dial-bubble-box{

            color:inherit;

            background-color:rgba(0,0,0,.08);

            margin:8px;

            padding:12px;

          }

          .dial-bubble-box.x1{

            border-radius:8px;

          }

          .dial-bubble-box.x2{

            border-radius:16px;

          }

          .dial-bubble-box.x3{

            border-radius:32px;

          }

        </style>

        <script>

          	const Ge = function(i){return document.querySelector(i);}

      	</script>

    </head>

    <body>

        <div ggn-sheet="default">

<?php
            	break;



          		case 'Box:Center':
?>

<?php

?>

          <section style="min-height:100vh;" class="flex-box flex-items-center flex-justify-center width-full">

              <div class="dial-box flex-box flex-column">

                  <div class="header flex-box flex-items-center">

                      <div class="sticker flex-box flex-row flex-items-center"><?=$Option['Sticker']??'';?></div>

                      <div class="title">

                          <?=$Option['Title']??'';?>

                      </div>

                  </div>

<?php

	if(isset($Option['About'])){

?>
                  <div class="about flex-box flex-items-end ">

                      <div class="content align-self-end">

                          <?=$Option['About'];?>

                      </div>


                 </div>

<?php

    }

?>

                  <div class="container">

                      <div class="content">

                          <?=$Option['Content']??'';?>

                      </div>

                  </div>

<?php

	if(isset($Option['Buttons'])){

?>
                  <div class="buttons flex-row flex-row">

                      <?=$Option['Buttons'];?>

                 </div>

<?php

    }

?>

              </div>

          </section>

<?php

?>

<?php
            	break;



          		case 'HTML.End':
?>

        </div>

    </body>

</html>

<?php

                break;

            }

    	}

	}

}