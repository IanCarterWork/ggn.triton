<?php

namespace Framework\Uploader;


\ini_set('memory_limit', '2G');


class Driver{

    var $Mode;

    var $File;

    var $Success = false;

    var $Types = null;

    var $Errors = null;

    var $Names = [];
    

    public function __construct(?String $Mode = null, $File = null, String $Types = null){

        $this->Mode = $Mode ?: ':generic';

        $this->File = $File;

        // var_dump($File, $Types);exit;

        $this->Types = explode(';',$Types);

        $this->LimitSize = $this->GetLimitSize();

    }


    static public function Input(String $Name, $Key = 0, ?Object $Data = null){

        $Data = $Data ?: $_REQUEST;


        if($Key === null){

            $Out = [];
           
            for($K = 0; $K < count((Array) $Data); $K++){

                if(isset($Data->{'FILE---' . $Name . '-data'}[$K])){

                    $Out[] = (Object) [

                        'Name' => $Data->{'FILE---' . $Name . '-name'}[$K] ?: null
            
                        , 'LastModified' => $Data->{'FILE---' . $Name . '-lastModified'}[$K] ?: null
            
                        , 'Size' => $Data->{'FILE---' . $Name . '-size'}[$K] ?: null
            
                        , 'Type' => $Data->{'FILE---' . $Name . '-type'}[$K] ?: null
            
                        , 'Data' => $Data->{'FILE---' . $Name . '-data'}[$K] ?: null
                        
                    ];

                }

            }

            return $Out;
    
        }

        else{

            return (Object) [

                'Name' => $Data->{'FILE---' . $Name . '-name'}[$Key] ?: null
    
                , 'LastModified' => $Data->{'FILE---' . $Name . '-lastModified'}[$Key] ?: null
    
                , 'Size' => $Data->{'FILE---' . $Name . '-size'}[$Key] ?: null
    
                , 'Type' => $Data->{'FILE---' . $Name . '-type'}[$Key] ?: null
    
                , 'Data' => $Data->{'FILE---' . $Name . '-data'}[$Key] ?: null
                
            ];

        }


        
    }
    

    public function GetLimitSize() : int{

        return 2000000000;

        // return min( 
            
        //     (int)(ini_get('upload_max_filesize'))
            
        //     , (int)(ini_get('post_max_size'))
            
        //     , (int)(ini_get('memory_limit'))
        
        // );
        
    }


    public function GenericMove(String $Dir, bool $Uniq = true, ?String $Name = null){
        
        $this->Name = $this->File['name'];

        if($this->File['size'] > $this->LimitSize){

            $this->Errors = (Object) [

                'Code' => 1

                ,'About' => 'File:Size.Too.Large'

                ,'Exception' => null
                
            ];

        }

        if($Uniq === true){

            $this->Name = $Name ?: 'Up-' . (\GGN\Encryption\Customize( \GGN\ALPHA_NUMERIC_LOWER, 8 )) . '-' . (\GGN\Encryption\Customize( \GGN\ALPHA_NUMERIC_LOWER, 8 ));

        }


        $this->Name = $this->Name . '.' . $this->File['type'];

        $Uploaded = move_uploaded_file($this->File['tmp_name'],  $this->Name);

        if(!$Uploaded){

            $this->Errors = (Object) [

                'Code' => 2

                ,'About' => 'File:Upload:Failed'

                ,'Exception' => null
                
            ];

        }
        
        if($Uploaded){

            $this->Success = true;
            
        }

        return $this;

    }
    


    public function ModernExplode($Data){
        
        $this->Explode = \explode(';base64,', $Data);

        return $this;

    }
    
    

    public function ModernFileExtension($Type){
        
        return \explode('/', $Type)[1] ?: 'cache';

    }
    


    public function ModernMove(String $Dir, bool $Uniq = true, ?String $Name = null, ?Object $File = null){

        $File = $File ?: $this->File;
        
        $this->Name = $Name ?: $File->Name;

        if($File->Size > $this->LimitSize){

            $this->Errors = (Object) [

                'Code' => 1

                ,'About' => 'File:Size.Too.Large'

                ,'Exception' => null
                
            ];

        }

        if($Uniq === true){

            $this->Name = $Name ?: 'ggn-' . (\GGN\Encryption\Customize( \GGN\ALPHA_NUMERIC_LOWER, 8 )) . '-' . (\GGN\Encryption\Customize( \GGN\ALPHA_NUMERIC_LOWER, 8 ));

        }



        if(in_array($this->Types, $File->Type)){

            $this->Errors = (Object) [

                'Code' => 4

                ,'About' => 'File:Type.Failed'

                ,'Exception' => null
                
            ];

        }


        // var_dump($File->Data);exit;

        $this->ModernExplode($File->Data);

        $Fex = $this->ModernFileExtension($File->Type);



        if(!isset($this->Explode[1])){

            $this->Errors = (Object) [

                'Code' => 3

                ,'About' => 'File:Broken'

                ,'Exception' => null
                
            ];

        }





        if(isset($this->Explode[1])){


            $this->Name = $this->Name . '.' . $Fex;

            \ini_set('memory_limit', '2G');

            // $Decoded = base64_decode($this->Explode[1]);


            // $pointer = 0; 
            
            // $size = strlen($this->Explode[1]);

            // $chunkSize = 1048576;

            // while ($pointer < $size) {

            //     $chunk = substr($data, $pointer, $chunkSize);

            //     // doSomethingWithChunk($chunk);

            //     file_put_contents($Dir . $this->Name, $chunk );

            //     $pointer += $chunkSize;

            // }


            for ($i=0; $i < ceil(strlen($this->Explode[1])/1048); $i++){

                // $decoded = $decoded . base64_decode(substr($this->Explode[1],$i*256,256)); 

                file_put_contents($Dir . $this->Name, base64_decode(substr($this->Explode[1],$i*1048,1048)), \FILE_APPEND );
                
                // $decoded = $decoded . base64_decode(substr($this->Explode[1],$i*256,256)); 

            }



            $Uploaded = is_file($Dir . $this->Name) ? true : false;



            if(!$Uploaded){

                $this->Errors = (Object) [

                    'Code' => 2

                    ,'About' => 'File:Upload.Failed'

                    ,'Exception' => null
                    
                ];

            }
            
            if($Uploaded){

                $this->Names[] = $this->Name;

                $this->Success = true;
                
            }

        }

        return $this;

    }
    


    public function Move(String $Dir, bool $Uniq = true, ?String $Name = null){

        try{

            if(!is_dir($Dir)){ \mkdir($Dir, 0777, true); }

            switch(strtolower($this->Mode)){

                case ':generic':
    
                    // $this->File = (Object) $this->File;
    
                    $this->GenericMove($Dir, $Uniq, $Name);
    
                break;
                

                case ':modern':

                    // $this->File = (Object) $this->File;

                    // var_dump($this->File);
    
                    if(is_array($this->File)){

                        foreach($this->File as $File){

                            // var_dump($File);

                            $this->ModernMove($Dir, true, null, $File);
    
                        }

                    }

                    else{

                        $this->ModernMove($Dir, $Uniq, $Name);
    
                    }
    
                break;
                
            }

        }

        catch(\Exception $E){
            
            $this->Errors = (Object) [

                'Code' => 0
                
                , 'Exception' => $E

            ];

        }

        return $this;

    }
    


}
