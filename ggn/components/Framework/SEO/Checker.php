<?php

namespace Framework\SEO;



Class Checker{


    public $URL;

    public $Parsing;

    public $Title;
    

    public function __construct(String $URL) {

        $this->URL = $URL;
        
    }


    public function Connect():Checker{

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_HEADER, 0);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($ch, CURLOPT_URL, $this->URL);

        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

        $this->Raw = curl_exec($ch);

        curl_close($ch);

        return $this;

    }


    public function EncodeField(?String $Value): ?String{

        return htmlentities( addslashes($Value) );

    }


    public function Parse(?String $String = null):Checker{

        $this->Parsing = (Object) [];

        $this->Document = new \DOMDocument();

        $this->Document->loadHTML($this->Raw);


        // Titre de la page

        $Title = $this->Document->getElementsByTagName('title');

        if($Title){

            $this->Parsing->title = $Title->item(0)->nodeValue;
            
        }


        // Balise Meta

        $this->Parsing->Meta = (Object) [];

        $Metas = $this->Document->getElementsByTagName('meta');

        if($Metas){

            for ($i = 0; $i < $Metas->length; $i++) {

                $Meta = $Metas->item($i);

                if($Name = $Meta->getAttribute('name')){

                    $this->Parsing->Meta->{$Name} = $this->Parsing->Meta->{$Name} ?: [];

                    $this->Parsing->Meta->{$Name}[] = (Object) [

                        'Type' => '@Name'
                        
                        , 'content' => $this->EncodeField($Meta->getAttribute('content'))
                    
                    ];
                    
                }
                
                else{

                    if($Property = $Meta->getAttribute('property')){

                        $this->Parsing->Meta->{$Property} = $this->Parsing->Meta->{$Property} ?: [];

                        $this->Parsing->Meta->{$Property}[] = (Object) [

                            'Type' => '@Property'
                            
                            , 'content' => $this->EncodeField($Meta->getAttribute('content'))
                        
                        ];

                    }
                    

                    else if($Itemprop = $Meta->getAttribute('itemprop')){

                        $this->Parsing->Meta->{$Itemprop} = $this->Parsing->Meta->{$Itemprop} ?: [];

                        $this->Parsing->Meta->{$Itemprop}[] = (Object) [

                            'Type' => '@Itemprop'
                            
                            , 'content' => $this->EncodeField($Meta->getAttribute('content'))
                        
                        ];

                    }
                    

                    else if($Charset = $Meta->getAttribute('charset')){

                        $this->Parsing->Meta->Charset = (Object) [

                            'Type' => '@Charset'
                            
                            , 'content' => $this->EncodeField($Meta->getAttribute('charset'))
                        
                        ];

                    }
                    

                }

                

            }
            
        }
        

        return $this;
        
    }
    


    static public function RenderMetaEntry(String $Name, Object $Entry): String{

        $Output = [];

        switch($Entry->Type ?: null){

            case '@Charset';

                $Output[] = '<meta charset="' . $Entry->content . '">';

            break;

            case '@Name';

                $Output[] = '<meta name="' . $Name . '" content="' . $Entry->content . '">';

            break;

            case '@Itemprop';

                $Output[] = '<meta itemprop="' . $Name . '" content="' . $Entry->content . '">';

            break;

            case '@Property';

                $Output[] = '<meta property="' . $Name . '" content="' . $Entry->content . '">';

            break;

        }


        return implode('', $Output);
        
    }
    


    static public function RenderMetasFromString(String $Data): String{

        $Output = [];

        $Metas = json_decode( 
            
            str_replace('&quot;', '"', $Data )
        
        );

        if(is_object($Metas)){

            foreach($Metas as $Name => $Meta){

                if(
                    
                    $Name != 'viewport'

                    && $Name != 'Charset'
                    
                ){

                    if(is_array($Meta)){

                        foreach($Meta as $Content){
    
                            $Output[] = self::RenderMetaEntry($Name, $Content);
                            
                        }
                        
                    }
    
                    else if(is_object($Meta)){
    
                        $Output[] = self::RenderMetaEntry($Name, $Meta);
                            
                    }
    
                    else{
    
                    }

                }

                
            }
            
        }


        return implode('', $Output);

    }
    
    

}

