<?php

namespace Database;




class EntityForms{



    private $_SAMPLE = [

        '@default' => [

            'Tag:Name' => 'input'

            , 'Tag:Attributes' => [

                'placeholder' => "Entrer une valeur"

                , 'value' => ""

                , 'type' => "text"

            ]
            
        ]
        
        , 'string' => [

            'Tag:Name' => 'mok-input'
            
            , 'Tag:Attributes' => [

                'visual:error' => ":"

                , 'placeholder' => "Entrer un valeur"

                , 'value' => ""

                , 'glyph:label' => "fa-lg fa-circle"

                , 'glyph:cleaner' => ":true"
                
                , 'type' => "text"

            ]
            
        ]
        
        , 'url' => [

            'Tag:Name' => 'mok-input'
            
            , 'Tag:Attributes' => [

                'visual:error' => ":"

                , 'placeholder' => "http://exemple.com"

                , 'value' => ""

                , 'glyph:label' => "fa-lg fa-globe"

                , 'glyph:cleaner' => ":true"

                , 'type' => "url"

            ]
            
        ]
        
        , 'text' => [

            'Tag:Name' => 'textarea'
            
            , 'Tag:Attributes' => [

                'visual:error' => ":"

                , 'placeholder' => "Entrer un valeur"

                , 'value' => ""

                , 'glyph:label' => "fa-lg fa-circle"

                , 'glyph:cleaner' => ":true"
                
                , 'Form:Texarea' => ":true"

            ]
            
        ]
        
        , 'password' => [

            'Tag:Name' => 'mok-input'
            
            , 'Tag:Attributes' => [

                'visual:error' => ":"

                , 'placeholder' => "Mot de passe"

                , 'value' => ""

                , 'glyph:label' => "fa-lg fa-circle"

                , 'glyph:cleaner' => ":true"
                
                , 'type' => "password"

            ]
            
        ]
        
        
        , 'path:image' => [

            'Tag:Name' => 'mok-uploader'
            
            , 'Tag:Attributes' => [

                'label' => "Selectionnez une image"

                , 'mode' => ":default"

                , 'glyph' => "fa-lg fa-upload"

                , 'mime' => "image/*"
                
                , 'type' => "file"

            ]
            
        ]
        
        
        , 'choices' => [

            'Tag:Name' => 'mok-input'
            
            , 'Tag:Attributes' => [

                'label' => "Faites un ou plusieurs choix"

                , 'placeholder' => "Choix Multiple"

                , 'model' => ":list"

                , 'multiple' => ":true"

                , 'glyph:label' => "fa-lg fa-circle"
                
                , 'type' => "text"

            ]
            
        ]
        
    ];
    
    
    


    public function __construct(EntityManager $Manager){

        $this->Manager = $Manager;

        $this->Config = (Object) [];

    }


    public function Sample(String $Type){

        return (Object) ($this->_SAMPLE[$Type] ?: $this->_SAMPLE['@default']);

    }


    public function Config(String $Field, $Value){

        $this->Config->{$Field} = $Value;

        return $this;

    }


    

    public function Rendering(Bool $Updating = false){

        global $GGN;

        

        $Render = null;


        if($this->Manager->Fields){

            $Render = [];


            if($Updating){

                $Render[]= '<input type="hidden" name="iD" value="' . $this->Manager->Entity->Get('iD') . '" >';
                
            }
            

            if(is_object($GGN->{'Connected:User'})){

                $Render[]= '<input type="hidden" name="UUiD" value="' . ($GGN->{'Connected:User'}->Value->UUiD ?: '') . '">';

            }
            

            if(is_object($GGN->{'App:Current:Settings'})){

                $Render[]= '<input type="hidden" name="APPiD" value="' . ($GGN->{'App:Current:Settings'}->{'App:iD'} ?: '') . '">';

            }


            

            foreach( $this->Manager->Fields as $Field){

                $Render[] = $this->RenderField($Field);
                
            }

        }

        else{

            $Render[]= '<div class="">Aucun Champs trouvé</div>';
            
        }

        return implode(' ', $Render);
        
    }






    public function RenderField(Object $Field):?String{

        $Render = [''];

        if($Field->input){

            $Sample = $this->Sample($Field->type[0][0]);

            if(isset($this->Config->{$Field->name}) && is_array($this->Config->{$Field->name}) ){

                foreach($this->Config->{$Field->name} as $Attr => $Value){

                    $Sample->{'Tag:Attributes'}[$Attr] = $Value;
                    
                }
                
            }


            if(isset($Field->type[0][1])){

                $Sample->{'Tag:Attributes'}['maxlength'] = $Field->type[0][1];
                
            }


            if( $fValue = $this->Manager->Entity->Get($Field->name) ){

                $Sample->{'Tag:Attributes'}['value'] = $fValue;
                
            }

            
            $Render[]= $this->Mixages($Field, $Sample);

        }

        return implode(' ', $Render);
        
    }






    public function RenderRow(String $FieldName):?String{

        $Render = [''];

        $Field = null;


        foreach($this->Manager->Fields as $f){

            if($FieldName == $f->name){

                $Field = $f;

                break;
                
            }
            
        }

        if(is_object($Field)){

            $Render[] = $this->RenderField($Field);
            
        }
        
        return implode(' ', $Render);
        
    }



    public function Mixages(Object $Field, Object $Sample){

        $Sample->{'Tag:Name'} = strtolower( $Sample->{'Tag:Name'} );
        
        $Current = null;


        $Return = '';

        $Return .= '<';

        $Return .= $Sample->{'Tag:Name'};

        $Return .= ' name="' . $Field->name . '" ';

        $Return .= ' ' . \GGN\Objects\toString($Sample->{'Tag:Attributes'}, '="', '" ') . '" ';

        $Return .= '>';



        if(is_object($this->Manager->Entity)){

            $Current = $this->Manager->Entity;
            
        }


        if(isset($Field->relationShip)){


            $Return .= '<option value="">Aucun Choix</option>';

            // var_dump($this->Manager->Entity);exit;


            if($Field->relationShip[0] && $Field->relationShip[1]){

                $tEntity = $Field->relationShip[0];

                $tField = trim($Field->relationShip[1]?:null);

                $tRelation = $Field->relationShip[2]?:'OneToMany';

                $tLabel = trim($Field->relationShip[3]?:'title');

                
                $relaTable = new $tEntity();


                if($tRelation){
                        
                    if(strstr($tRelation, 'OneTo')){

                        $tTable = $relaTable->FindAll(NULL, NULL, [

                            $tField => NULL
                            
                        ]);

                    }

                    else{

                        $tTable = $relaTable->FindAll(NULL, NULL);

                    }

                }
            

                if(is_object($tTable)){

                    $CurrentID = $Current->Get('iD');

                    $ParentIDs = explode(',', $Current->Get($Field->name) );

                    foreach($tTable as $Entry){

                        if($CurrentID != $Entry->iD){

                            // var_dump($Entry->iD, $ParentIDs, (in_array($Entry->iD, $ParentIDs)), '||||');

                            $Return .= '<option ' . (in_array($Entry->iD, $ParentIDs) ? 'selected' : '') . ' value="' . $Entry->iD . '">' . $Entry->{$tLabel} . '</option>';
    
                        }

                    }

                }
                    


            }

        }


        if($Sample->{'Tag:Name'} != 'input'){

            $Return .= '</';

            $Return .= $Sample->{'Tag:Name'};
    
            $Return .= '>';
    
        }



        return $Return;
        
    }
    
    
    
}