<?php

namespace Framework\Embedded;

class EmbedPlayer{


    static public function Check(?String $URL = null):Object{

        $Out = (Object) [];

        if(strpos($URL, 'youtube') !== false){

            if(preg_match('/(.+)youtube\.com\/watch\?v=([\w-]+)/', $URL, $Match)){
        
                $Out->iD = $Match[2];
        
                $Out->Type = 'Youtube';
        
            }
    
        }
        
        elseif(strpos($URL, 'youtu.be') !== false){

            $d = strpos($URL, 'youtu.be/');
        
            if(preg_match('/(.+)youtu.be\/([\w-]+)/', $URL, $Match)){
        
                $Out->iD = $Match[2];
        
                $Out->Type = 'Youtube';
        
            }
    
        }
        
        elseif(strpos($URL, 'vimeo') !== false){

            if(preg_match('/https:\/\/vimeo.com\/([\w-]+)/', $URL, $Match)){

                $Out->iD = $Match[1];

                $Out->Type = 'Vimeo';

            }
    
        }
        
        elseif(strpos($URL, 'dailymotion') !== false){

            if(preg_match('/(.+)dailymotion.com\/video\/([\w-]+)/', $URL, $Match)){
        
                $Out->iD = $Match[2];
        
                $Out->Type = 'Dailymotion';
        
            }
    
        }
        
        elseif(strpos($URL, 'dai.ly') !== false){

            if(preg_match('/(.+)dai.ly\/([\w-]+)/', $URL, $Match)){

                $Out->iD = $Match[2];

                $Out->Type = 'Dailymotion';

            }

        }

        return $Out;

    }




    static public function Show(?String $URL = null, ?Object $Config = null):Object{

        $Check = self::Check($URL);


            $Config = $Config ?: (Object) [];

            $Config->Width = $Config->Width ?: '720px';

            $Config->Height = $Config->Height ?: '405px';

            $Config->AutoPlay = (isset($Config->AutoPlay)) ? $Config->AutoPlay : true;
        

        $Check->Config = $Config;


        switch($Check->Type){

            case'Youtube' :

                $Check->iFrame = '<iframe type="text/html" width="' . $Config->Width . '" height="' . $Config->Height . '" src="https://www.youtube.com/embed/' . $Check->iD . '?color=red&disablekb=1&modestbranding=1' . ($Config->AutoPlay ? '&autoplay=1' : '') . '" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" frameborder="0" allowfullscreen ></iframe>';

            break;

            case 'Vimeo' :

                $Check->iFrame = '<iframe type="text/html" src="https://player.vimeo.com/video/' . $Check->iD . ($Config->AutoPlay ? '?autoplay=1' : '') . '" width="' . $Config->Width . '" height="' . $Config->Height . '" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" frameborder="0"  webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';

            break;

            case 'Dailymotion' :

                $Check->iFrame = '<iframe type="text/html" frameborder="0" width="' . $Config->Width . '" height="' . $Config->Height . '" src="//www.dailymotion.com/embed/video/' . $Check->iD . ($Config->AutoPlay ? '?autoplay=1' : '') . '" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';

            break;
            
        }



        return $Check;
        
    }
    



    
    static public function Thumbnail(?String $URL = null):Object{

        $Check = self::Check($URL);

        if(empty($Check->iD)) return null;


        switch($Check->Type){
                
            case'Youtube' :

                $Check->Thumb = 'https://img.youtube.com/vi/' . $Check->iD . '/maxresdefault.jpg';

            break;

            case 'Vimeo' :

                $Check->JSON = json_decode(@file_get_contents('https://vimeo.com/api/v2/video/' . $Check->iD . '.json'));

                if(!empty($Check->JSON)){
                    
                    $Check->Thumb = $Check->JSON[0]->thumbnail_large;
                
                }

            break;

            case 'Dailymotion' :

                $Check->JSON = json_decode(@file_get_contents('https://api.dailymotion.com/video/' . $Check->iD . '?fields=urlthumbnail_1080_url'));

                if(!empty($Check->JSON)){
                    
                    $Check->Thumb = $Check->JSON->urlthumbnail_1080_url;
                
                }

            break;

        }


        return $Check;
        
    }
    
    



    
    static public function Title(?String $URL = null):Object{

        $Check = self::Check($URL);

        // if(empty($Check->iD)){ return null;}


        switch($Check->Type){
                
            case'Youtube' :

                $Check->JSON = json_decode(@file_get_contents('https://www.youtube.com/oembed?url=' . $URL . '&format=json'), true);

                $Check->Title = $Check->JSON['title'];

            break;

            case 'Vimeo' :

                $Check->JSON = json_decode(@file_get_contents('https://vimeo.com/api/v2/video/' . $Check->iD . '.json'));

                if(!empty($Check->JSON)){ 

                    $Check->Title = $Check->JSON[0]->title;
                
                }
                
            break;

            case 'Dailymotion' :

                $Check->JSON = json_decode(@file_get_contents('https://api.dailymotion.com/video/' . $Check->iD . '?fields=title'));

                if(!empty($Check->JSON)){ 

                    $Check->Title = $Check->JSON[0]->title;
                
                }

            break;

        }


        return $Check;
        
    }
    





}