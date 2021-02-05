<?php


namespace Database;

    
    use GGN\Patterns\Annotations;

    use Database\EntityManager;

    use Framework\JSON\Responses;

    use Framework\Uploader;

    use Core\VarConverter;


trait EntityService{



    /**
     * Table
     */
    private $Entity;



    /**
     * Variable du Repondeur
     */
    public $_Responses;




    /**
     * Définitions des Instances
     */
    private function Initialize(){

        $Ref = new \ReflectionClass($this);

        $Entity = Annotations::Find($Ref->getDocComment(), '@Entity')[0] ?: null;

        $this->Answering = json_decode('{' . (Annotations::Find($Ref->getDocComment(), '@Answering')[0] ?: '"Title": "Gestionnaire de Service", "About": "Aucune donnée"') . '}');

        if($Entity){

            $this->Entity = new $Entity;

            $this->Manager = new EntityManager($this->Entity);
            
        }
        
    }




    /**
     * Repondeur JSON
     */
    private function Responses(?Bool $Value, ?String $About = null){

        $this->_Responses = ($this->_Responses ?: (new Responses($this->Answering->Title)))->Set($Value, $About);

        return $this->_Responses;
        
    }



    /**
     * Vérification de l'Entrées
     */

    private function InputValidation(Object $Field, &$Value, Bool $Update = false){

        global $GGN;


        // var_dump('InputValidation :', $Field, $Value);

        /** Si la donnée peut etre null */

        if($Value === null ){

            if( $Field->type[0][2] != 'NULL'){

                $this->Responses(null, $Field->name . " ne doit pas etre vide...");
                
                return false;

            }
            
        }
            
            
        /** Nombre de caratères */

        if($Field->type[0][1]){

            if(strlen($Value) > $Field->type[0][1]){

                $this->Responses(null, $Field->name . " est trop long...");
                
                return false;
                
            }

        }


        /** 
         * Champs de téléchargement de fichier 
         * */

        $Path = \explode(':', $Field->type[0][0] ?: []);

        
        if($Path[0] == 'path'){
            

            // var_dump($Update, $this->Input->{$Field->name}, $Value);
            
            if(
                
                $Update===true
                
                && (empty($this->Input->{$Field->name}))
                
                && empty($Value)
                
            ){

                return ':loop';
                
            }

            else{

                $Dir = $GGN->{'Dir:Images'} . 'library/';


                /** 
                 * Upload : Image 
                 * */

                if( $Path[1] == 'image' || $Path[1] == 'images' ){

                    $Multiple = ($Path[1] == 'images') ? null : 0;

                    $Dir = $GGN->{'Dir:Images'} . 'library/' . ($Field->uploading->dir?:'');

                }


                /** 
                 * Upload : Déplacement 
                 * */

                $File = (new Uploader\Driver(
                    
                    ':modern'
                    
                    , Uploader\Driver::Input($Field->name, $Multiple, $this->Input)
                    
                    , (\implode(';', $Field->uploading->mimes?:null) ?: 'image/*')
                
                ))->Move($Dir , true);

                

                /**
                 * Mise à jour de la valeur
                 */

                if($File->Names){ $Value = \implode('|', $File->Names?:'') ?: null; }

            }



        }




        return true;

    }



    /**
     * @Method POST
     */
    public function Post(){

        /**
         * Définitions
         */

        $this->Initialize();



        /**
         * Concordances des données
         */

        if(\is_array($this->Manager->Fields)){

            $Allow = false;


            /**
             * Création de table s'il elle n'existe pas
             */

            $this->Manager->CreateTable();


            /**
             * Création des lignes de requetes
             */

            foreach( $this->Manager->Fields as $Field){

                if(isset($this->Input->{$Field->name})){


                    /**
                     * Initialisation
                     */

                    $Value = $this->Input->{$Field->name} ?: null;




                    /**
                     * Vérifications des données
                     */

                    if($Validation = ($this->InputValidation($Field, $Value, false) === true)){

                        $Allow = true;

                        if(isset($Field->relationShip)){

                            $Values = null;

                            $tEntity = $Field->relationShip[0]?:null;

                            $tField = $Field->relationShip[1]?:null;
                
                            $tRelation = $Field->relationShip[2]?:'OneToMany';
                

                            if(strstr($tRelation, 'OneTo')){

                                $Values = (is_array($Value)) ? ($Value[0] ?: null) : $Value;

                            }

                            if(strstr($tRelation, 'ManyTo')){

                                $Values = (is_array($Value)) ? (implode(',', $Value) ?: null) : $Value;

                            }

                            $Value = $Values;

                        }

                        $this->Entity->Set($Field->name, $Value);

                    }



                    /**
                     * Arret du tratement
                     */

                    if($Validation === false){

                        $Allow = false;
                        
                        $this->Entity->Set($Field->name, $Value);

                        break;

                    }


                    
                    
                }
                
            }

            
            /**
             * Reponse de l'insertion
             */

            if($Allow === true){

                $Return = $this->Manager->Insert($this->Entity)->Apply(true, $this->Input);


            
                if(!empty($Return->Traces->Insert)){
    
                    $this->Responses(true, 'Ajouté avec succès');
    
                    $this->_Responses->LastID = end($Return->Traces->Insert)->Connexion->lastInsertId();
                    
                }
    
                if(empty($Return->Traces->Insert)){
    
                    $this->Responses(false, 'Impossible d\'inserer les données');
                    
                }

            }

            return $this->_Responses->Get();

        }
        
        
    }





    /**
     * @Method Put
     */
    public function Put(){

        /**
         * Définitions
         */

        $this->Initialize();

        $Updater = [];


        /**
         * Concordances des données
         */

        if(\is_array($this->Manager->Fields) && ($this->Input->iD ?: false)){

            $EntityInstant = $this->Entity->FindOneBy('iD', $this->Input->iD);


            /**
             * Création des lignes de requetes
             */

            $FnRemoveRelation = (isset($this->Input->{'service-relationship-delete'})) 

                ? explode(',', $this->Input->{'service-relationship-delete'})

                : null;

            $FnAddRelation = (isset($this->Input->{'service-relationship-add'})) 

                ? explode(',', $this->Input->{'service-relationship-add'})

                : null;

            $FnForceValue = (isset($this->Input->{'service-force-value'})) 

                ? explode(',', $this->Input->{'service-force-value'})

                : null;


            foreach( $this->Manager->Fields as $Field){

                if($Field->name == 'iD'){continue;}

                if(isset($this->Input->{$Field->name})){


                    /**
                     * Initialisation
                     */

                    $Value = $this->Input->{$Field->name} ?: null;



                    /**
                     * Vérifications des données
                     */


                    if($FnForceValue){

                        $FnFVFound = false;

                        foreach($FnForceValue as $FnFV){

                            if($FnFV == $Field->name){

                                // var_dump('Force Value', $Field->name,  $FnFV, $Value);exit;

                                $Updater[$Field->name] = $Value;

                                $FnFVFound = true;

                                continue;

                            }
                            
                        }

                        if($FnFVFound === true){ continue; }
                        
                    }



                    if(($Validation = $this->InputValidation($Field, $Value, true)) === true){
                        
                        if($Validation === ':loop'){ continue; }


                        $Allow = true;


                        if(isset($Field->relationShip)){

                            $Values = null;

                            $tEntity = $Field->relationShip[0]?:null;

                            $tField = $Field->relationShip[1]?:null;
                
                            $tRelation = $Field->relationShip[2]?:'OneToMany';
                

                            if($tRelation){
                                
                                if(strstr($tRelation, 'OneTo')){

                                    $Values = (is_array($Value)) ? ($Value[0] ?: $Value) : $Value;

                                }

                                else{

                                    $Values = (is_array($Value)) ? (implode(',', $Value) ?: $Value) : $Value;

                                } 

                            }


                            if($FnRemoveRelation){

                                foreach($FnRemoveRelation as $FnRR){

                                    if($FnRR == $Field->name){

                                        if(is_object($EntityInstant)){
    
                                            $Bind = explode(',', $EntityInstant->{$FnRR}?:'');
    
                                            if (($key = array_search($Value, $Bind)) !== false) {
                                                
                                                unset($Bind[$key]);
    
                                            }
    
                                            $Values = implode(',', $Bind);
                                        
                                        }
    
                                    }

                                }

                            }




                            if($FnAddRelation){

                                foreach($FnRemoveRelation as $FnAR){

                                    if($FnAR == $Field->name){

                                        if(is_object($EntityInstant)){

                                            $Bind = explode(',', $EntityInstant->{$FnAR}?:'');

                                            $Bind[] = $Value;

                                            $Values = implode(',', $Bind);

                                            // var_dump('Delete Relationship ', $Field->name, $Bind );exit;
                                        
                                        }

                                    }

                                }

                            }




                            $Value = $Values;

                        }



                        $Updater[$Field->name] = $Value;


                    }



                    /**
                     * Arret du tratement
                     */

                    if($Validation === false){

                        $Allow = false;
                        
                        // $Updater[$Field->name] = $Value;

                        break;

                        // continue;

                    }

                     
                    
                }
                
            }

            
            /**
             * Reponse de la mise à jour
             */


            //  var_dump('$Updater :', $Updater);exit;

            $Return = $this->Manager->Update($this->Input->iD, $Updater)->Apply(true, $this->Input);

            
            if(!empty($Return->Traces->Update)){

                $this->Responses(true, 'Mise à jour éffectué avec succès');

                $this->_Responses->LastID = $this->Input->iD;
                
            }

            if(empty($Return->Traces->Update)){

                $this->Responses(false, 'Impossible de proceder à la mise à jour les données');
                
            }


        }
        
        else{

            $this->Responses(false, 'Aucun identifiant indiqué');
                
        }
        
        return ($this->_Responses)->Get();


    }





    /**
     * @Method Delete
     */
    public function Delete(){

        /**
         * Définitions
         */

        $this->Initialize();


        /**
         * Concordances des données
         */

        if(\is_array($this->Manager->Fields) && ($this->Input->iD ?: false)){

            
            /**
             * Reponse de la suppression
             */

            $Return = $this->Manager->Delete($this->Input->iD)->Apply();

            
            if(!empty($Return->Traces->Delete)){

                $this->Responses(true, 'Suppression éffectué avec succès');

                $this->_Responses->LastID = $this->Input->iD;
                
            }

            if(empty($Return->Traces->Delete)){

                $this->Responses(false, 'Impossible de proceder à la suppression des données');
                
            }

        }

        else{

            $this->Responses(false, 'Aucun identifiant indiqué');
                
        }
        
        return $this->_Responses->Get();


    }
    

}