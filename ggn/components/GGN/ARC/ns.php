<?php

namespace GGN;


    use GGN\Settings;

    use GGN\Dial;

    use GGN\Patterns\Annotations;

/**
 * ARC : Application Request Canonical
 */


 Class ARC{


    
    private $Settings;
    





    public function __construct(?String $Current = null, Settings $Settings){

        $this->Current = $Current ?: '/';
        
        $this->Settings = $Settings;

    }





    public function Matches(String $Slug){

        global $GGN;

        $Check = NULL;
        
        $Pattern = '/^' . (str_replace('*', '(.*)', str_replace('/', '\/', $Slug) )) . '$/i';

        $Found = preg_match_all($Pattern, $this->Current, $Matches);


        if($this->Current == $Slug){

            
            $Check = (Object) [];

            $Check->Type = ':static';

            $Check->Path = implode('/', $Matches[1] ?: []) ?: '/';
            
            $Check->Name = explode('/', $this->Current)[1] . '/';

            $Check->Matches = $Matches;

            $Check->Slug = $Slug;

            $Check = new ARC\Hit($Check);
            

            
        }
        

        else{
                
            if($Found){

                $Check = (Object) [];

                $Check->Type = \substr_count($Slug, '*') ? ':dynamic' : ':static';

                $Check->Path = implode('/', $Matches[1] ?: []) ?: NULL;
                
                $Check->Name = explode('/', $this->Current)[1] . '/';

                $Check->Matches = $Matches;

                $Check->Slug = $Slug;

                // $Check->Page = $GGN->{'Http:Host'} . substr($this->Current, 1);

                $Check = new ARC\Hit($Check);
                
            }

        }

        return $Check;

    }





    public function GetArguments($Method){

        $_Method = (new \ReflectionMethod($Method->class, $Method->name));
        
        // $Arguments = ;


        // foreach ($_Method->getParameters() as $Arg) {


        //     $Type = $Arg->getType() ?: null;


        //     // if($Type){

        //     //     $TypeClass = $Type->getName();

        //     //     $Instance = new $TypeClass(...$Params);

        //     //     $Arguments[] = (Object) [];
                
        //     // }

        //     // else{

        //     //     $Instance = $Arg->name;
                
        //     // }

        //     $Arguments[] = $Instance;
            
        // }

        return $_Method->getParameters() ?: null;

    }





    public function BindInstanceToArguments($_Arguments, Array $Params = []){

        $Arguments = [];

        foreach ($_Arguments as $Arg) {


            $Type = $Arg->getType() ?: null;


            if($Type){

                $TypeClass = $Type->getName();

                $Instance = new $TypeClass(...$Params);
                
            }

            else{

                $Instance = $Arg->name;
                
            }

            $Arguments[] = $Instance;
            
        }

        return $Arguments;

    }





    public function Main(){

        global $GGN;


        /**
         * Boot
         */

         
        if($this->Current == '/' && ($this->Settings->Boot?:null)){

            header("location:" . $GGN->{'Http:Host'} . $this->Settings->Boot);

            exit(1);
            
        }

        
        
        

        /** 
         * @Fn :matches, :entities
        */


        if(is_object($this->Settings->Entities ?: false)){

            
            $GGN->{'ARC:Instance'} = $this;


            foreach($this->Settings->Entities as $Name => $Config){

                $ClassName = "ARC\\" . $Name . "";

                $Class = new $ClassName();

                $Methods = (new \ReflectionClass($Class))->getMethods();


                if(\is_array($Methods)){

                    foreach($Methods as $Method){
                        
                        

                        $_Method = (new \ReflectionMethod($Method->class, $Method->name));

                        $Instances = Annotations::Find(
                        
                            $_Method->getDocComment()

                            , '@Instance'
                    
                        );


                        if(\is_array($Instances)){

                            
                            foreach($Instances as $Instance){

                                $Params = \json_decode("{" . $Instance . "}");

                                if(\is_object($Params)){

                                    if($this->Match = $this->Matches($Params->ARC)){

                                        $this->Arguments = $this->GetArguments($Method, [$this->Match]);
                                        


                                        $Directive = $this->GetDirective($Params->ARC);

                                        if($Directive){

                                            if(!$this->SetDirective($Directive)){

                                                $this->Arguments = $this->BindInstanceToArguments($this->Arguments, [$this->Match]);
                                                
                                            }

                                        }

                                        if(!$Directive){

                                            $this->Arguments = $this->BindInstanceToArguments($this->Arguments, [$this->Match]);
                                                
                                        }
                                        

                                        $Execute = call_user_func_array( [$Class, $Method->name], $this->Arguments );

                                        if($Execute){

                                            $GGN->{'System:Return'} = true;

                                            if(\is_string($Execute)){ echo $Execute; }

                                            exit;
                                            
                                        }

                                        else{ continue; }
    
                                        
                                    }

                                }

                                
                            }
                            

                        }
                            

                    }
                    
                }

        
            
            }

            
            Dial\Info(
                
                'Bienvenue'
                
                , 'GGN Frameworks'
                
                , 'Vous utilisez ' 
                    
                    . $GGN->{'Infos:Name'} . '<br>'

                    . 'Version : '.$GGN->{'Infos:Version'}.' ' . '<br>'

                    . 'Kernel : '.$GGN->{'Kernel:Default'}.' ' . '<br>'
            
            );
            

            return false;

        }

        else{

            Dial\Error('GGN ARC', 'Echec', 'Aucune Entité définie');
            
        }

        
        return $this;

    }





    public function GetDirective($iD){

        $Found = null;

        foreach($this->Settings->Directives->Matches as $_Name => $Directive){

            foreach( explode(' | ', $_Name) as $Name){

                if(strtolower($Name) == strtolower($iD)){

                    $Found = $Directive;

                    break;

                }
                
                
            }
                
        }

        return $Found;

    }





    public function SetDirective(Object $Directive){

        $Set = null;


        if(is_string($Directive->{'@Bind'}) ){

            foreach($this->Arguments as $Key => $Argument){

                $Set = true;


                $Type = $Argument->getType() ?: null;


                if($Type){

                    $TypeClass = $Type->getName();

                    if($TypeClass == $Directive->{'@Bind'} ){

                        $this->Arguments[$Key] = new $TypeClass(...$Directive->{'@Arguments'});
                        
                    }
                    
                    else{

                        $this->Arguments[$Key] = new $TypeClass($this->Match);
                        
                    }

                    // $this->Arguments[$Key]->{'ARC:Current'} = $this;

                    continue;
                    
                }


                $this->Arguments[$Key] = $this->Arguments->name;
                
            }

            
        }

        else{

            return false;
            
        }
        

        return $Set;

    }
    
    
    
     
 }
