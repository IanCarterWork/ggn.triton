<?php

	namespace GGN\Image;

		use GGN;

		use GGN\xDump;



	if(!class_exists("\\" . __NAMESPACE__ . "\ARC")){

      	class ARC{

          	static public function Graft(

              String $File

              , ?Int $Quality = null

              , ?Array $Set = null

              , Bool $Cache = false

            ) : ?GGN\Image{

                global $GGN;



                $CacheFile = null;



                if($Cache === true){

                  $CacheObject = new \GGN\Caches(null, 'com.images');

                  $CacheFile = $CacheObject->Entry();


                  if(\is_file($CacheFile)){

                      $CacheData = $CacheObject->Get();

                      if($CacheData){

                        $CacheObject->SetHeader();

                        echo($CacheData);

                        exit;

                      }

                  }


                }


              	if(is_file($File)){

                    $Image = (new GGN\Image($File,[

                       'Quality' => $Quality

                        , 'Cache' => $Cache

                        , 'Out' => $CacheFile

                    ]))

                        ->Assign($Set)

                        ->SetHeader()

                        ->Save()

                    ;

                    // var_dump('->', $Image);exit;

                  	echo $Image;

                }

              	return null;

            }



        }

    }