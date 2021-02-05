<?php

namespace ARC;


    use GGN\ARC\Entity;

    use GGN\ARC\Parameters as ARCParameters;
    
    use GGN\Image;
    
    // use Framework\Application\Boot;




    
/**
 * @Class ImagesEntity
 */

Class ImagesEntity extends Entity{


    /**
     * 
     * @Instance "ARC": "/images/*"
     * 
     */
    public function Show(ARCParameters $File, Image $Image){

        global $GGN;

        $Path = $GGN->{'Dir:Images'} . $File;

        $Cache = true;

        $CacheFile = null;

        $Set = \is_array($_GET['set']) ? $_GET['set'] : [$_GET['set']];
        

        // var_dump($Path);exit;

        if(\is_file($Path)){


            if($Cache === true){

              $CacheObject = new \GGN\Caches(null, 'passive/images');

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



            $Data = $Image
            
                ->Initialize($Path, [

                    'Quality' => $_GET['quality'] ?: null

                    , 'Cache' => $Cache

                    , 'Out' => $CacheFile

                ])
                
                ->Assign($Set)

                ->SetHeader()

                ->Save(null)

            ;

            echo($Data);

            return true;

        }
        

        
        return null;

    }
    
    
}