<?php

namespace Framework;

    use Framework\Application\Assets;
    use GGN\Dir;
    use GGN\Security;
    use GGN\Settings;



Class Application{


    private $Dir;


    public $Name;

    public $Version;

    public $PiD;

    public $Manifest;

    public $ARC;

    public $Settings;




    
    public function __construct(?String $Name = null, ?String $Version = null){

        global $GGN;


        $this->Name = $Name;

        $this->Version = $Version;

        $this->Dir = $GGN->{'Dir:Apps'} . $this->Name . ($this->Version ? ('-' . $this->Version) : '') . '/';

        $this->ARC = $GGN->{'ARC:Instance'}->Match ?: null;
        
        $this->App = (Object) [];

            $this->App->Menus = [];

        $this->Connected = (Object) [];

        // $this->Settings = (Object) [];

            $this->Settings = new Settings(array_merge([], (Array) Settings::Get('Applications.Register/' . $this->Name) ));

            $this->Settings->{'Http:Host'} = $GGN->{'Http:Host'};

            $this->Settings->{'App:URL'} = $GGN->{'Http:Host'} . $this->ARC->Name;

            $this->Settings->{'View:Current'} = $this->ARC->Matches[1][0] ?: null;

            $this->Settings->{'View:Path'} = $this->ARC->Name ?: null;


    }




    public function Boot(?String $View = null, ?Array $Arguments = null) : Application{

        $this
        
            ->LoadManifest()

            ->Required()

            ->Hook('Boot')

            ->View($View ?: $this->Manifest->{'View:Default'}, true, $Arguments, 'UnBootable')
            
        ;

        return $this;

    }




    public function Menus() : Application{

        if(is_file( $File = $this->Dir . 'Menus.json' )){

            $this->App->Menus = json_decode(\file_get_contents($File));

        }

        return $this;

    }




    public function Required() : Application{

        global $GGN;



        /**
         * @Make :app:require
         */

         
        /**
         * @Make :app:require:login
         */

        $this->Connected->USER = $GGN->{'Connected:User'};

        
        
        if($this->Settings->{'Require:Login'} ?: null){
            

            if(!$this->Connected->USER){

                if(!$this->Settings->{'View:Login'}){

                    $this->Hook('HttpError', false, ['HttpErrorCode'=>407])->Include('Error/407', true, [], null );

                    exit;

                }

                else{

                    if(
                        
                        ($this->Settings->{'View:Login'} != $this->Settings->{'View:Current'})

                        && !(

                            !$this->Settings->{'View:Current'}
                            
                            && !self::UsesAjax()

                            && !$this->Settings->{'Navigation:UsePathMode'}
                            
                        )
                        
                    ){

                        header("location: " . $this->Settings->{'App:URL'} . $this->Settings->{'View:Login'} . "?error=access:denied");

                        exit;

                    }


                }


            }


            if($this->Settings->{'User:Level'} > $this->Connected->USER->Value->level){

                // var_dump($this->Settings->{'View:Login'}, $this->Connected->USER );exit;

                if(strtolower($this->Settings->{'View:Login'}) != strtolower($this->Settings->{'View:Current'})){

                    header("location: " . $this->Settings->{'App:URL'} . $this->Settings->{'View:Login'} . "?error=access:restrict");

                    exit;

                }


            }

            
            // var_dump('Here');
            
        }

        
        

        return $this;

    }





    /**
     * Chemin et Dossier
     */
    
    public function Link(String $Path) : String{

        return $this->Settings->{'App:URL'} . (($Path) ? (($this->Settings->{'Navigation:UsePathMode'} ?'': '#') . $Path) :'');

    }

    public function Assets(String $Path, ?String $File = '', ?String $Domain = null) : String{

        global $GGN;

        $this->Assets = $this->Assets ?: new Assets($this->Settings->{'Asset:iD'}, $Domain);

        return $this->Assets->Get($Path, $File);
        
    }


    public function CSS(?String $File = null, ?String $Domain = null) : String{ return $this->Assets('css', $File ?: null, $Domain); }

    public function JS(?String $File = null, ?String $Domain = null) : String{ return $this->Assets('js', $File ?: null, $Domain); }

    public function Sound(?String $File = null, ?String $Domain = null) : String{ return $this->Assets('sounds', $File ?: null, $Domain); }

    public function Video(?String $File = null, ?String $Domain = null) : String{ return $this->Assets('videos', $File ?: null, $Domain); }

    public function Font(?String $File = null, ?String $Domain = null) : String{ return $this->Assets('fonts', $File ?: null, $Domain); }

    public function Document(?String $File = null, ?String $Domain = null) : String{ return $this->Assets('document', $File ?: null, $Domain); }

    public function Image(?String $File = null, ?String $Domain = null) : String{ return $this->Assets('images', $File ?: null, $Domain); }

    public function xImage(String $File, ?Array $Setter = null, ?String $Domain = null) : String{

        global $GGN; 

        return 
        
            ($Domain ?: $GGN->{'Http:Host'}) 
            
            . 'images/' 
            
            . ($this->Settings->{'Asset:iD'}?:'') . '/'
            
            . ($File)

            . (($Setter) ? ('?set[]=' . \implode('&set[]=', $Setter) ): '')
            
        ; 
    
    }
    



    public function LoadManifest() : Application{

        global $GGN;


        $this->Manifest = null;

        // echo '<pre>'; var_dump($this);exit;

        if(is_file($File = ($this->Dir . 'Manifest.json'))){


            
            /**
             * @Make :app:get:manifest
             */
            
            if(\is_object( $_Manifest = json_decode(\file_get_contents($File)) )){


                /**
                 * @Make :app:register:update
                 */
                
                Application\Manager::AddToRegister($_Manifest, $this->ARC->Name, dirname($this->Dir));
                

                /**
                 * @Make :app:settings:merge
                 */
                foreach($_Manifest as $Property => $Value){

                    $this->Settings->{$Property} = ($Value);
                    
                }



                /**
                 * Extension du Manifest de l'application
                 */

                $GGN->{'App:Current:Settings'} = $this->Settings;
                

            }

            

        }

        return $this;

    }





    public function CallPathProtocol(?String $thisPath = null) : Application{

        global $GGN;

        if($thisPath){

            $Root = $_SERVER['DOCUMENT_ROOT'];

            $Dir =  is_string($this->ViewDir) ? $this->ViewDir : ($this->Ui->App->{'Dir:App:Views'} ?? $GGN->{'Dir:Viewer'});

            $_Path = $Dir . \dirname($thisPath) . '';

            $Paths = \explode('/', substr(str_replace('\\', '/', $_Path), strlen($Root) ) );

            $Current = '';

            $Root = substr($Root, -1) == '/' ? substr($Root, -1) : ($Root . '/');

            $_Path = '';


            foreach($Paths as $Path){

                $_Path .= $Path . '/';

                $File = str_replace('//', '/', $Root . $_Path . '/') . '.path.protocol.php'; 
                

                $Is = \is_file($File);

                if($Is){
                    
                    require $File;
                    
                }
                
            }

        }

        return $this;
        
    }


    

    static public function UsesAjax(): Bool{

        return (isset($_SERVER['HTTP_X_REQUESTED_WITH']))

            ? TRUE

            : FALSE

        ;

    }



    static public function UsesAjaxOrNot(): Bool{

        return (self::UsesAjax());

    }


    public function Settings(String $Name, $Value = null) : Application{

        $this->{$Name} = $Value;

        return $this;

    }


    public function GenerateSettings() : String{

        $SettingsString = json_encode( $this->BuildSettings() );

        return '' . ($SettingsString) . '';

    }

    public function BuildSettings() : Object{

        return self::SecureSettings($this->Settings);

    }

    static public function SecureSettings(Object $Object) : Object{

        $New = new \stdClass;

        foreach($Object as $Key => $Value){

            if(strpos($Key, 'Dir:') !== false || strpos($Key, ':Dir') !== false){continue;}

            if(strpos($Key, 'Kernel:') !== false || strpos($Key, ':Kernel') !== false){continue;}

            if(strpos($Key, 'Autonomous:') !== false || strpos($Key, ':Autonomous') !== false){continue;}

            if(strpos($Key, 'System:') !== false || strpos($Key, ':System') !== false){continue;}

            $New->{$Key} = is_string($Value) ? \GGN\ParsingVars($Value) : $Value;

          }

        return $New;

      }






    private function InternalFile(?String $Path, ?String $File, bool $UsesAjax = false, ?Array $Arguments = [], ?String $ErrorView = null) : Application{

        if(self::UsesAjax() === TRUE && $UsesAjax === FALSE){ return $this; }

        \extract($Arguments);
        

        if(is_file($FilePath = $this->Dir . $Path . '/' . ($File?:'Index') ) ){

            include $FilePath;
            
        }

        else{
            

            if(\is_string($ErrorView)){
            
                $this
                
                    ->Hook('HttpError', true, ['HttpErrorCode'=>(Int)($ErrorView)])
                    
                    ->Include('Error/' . $ErrorView, (true), $Arguments, null)
                    
                ;

            }
            
        }

        return $this;

    }


    public function View(?String $File, bool $UsesAjax = false, ?Array $Arguments = [], ?String $ErrorView = null) : Application{ 
        
        return $this->Hook('View')->InternalFile('views', $File . '.view', $UsesAjax, $Arguments, $ErrorView); 
    
    }


    public function Include(?String $File, bool $UsesAjax = false, ?Array $Arguments = [], ?String $ErrorView = null) : Application{ 

        return $this->Hook('Include')->InternalFile('includes', $File . '.view', $UsesAjax, $Arguments, $ErrorView); 
    
    }

    public function Framework(?String $File, bool $UsesAjax = true, ?Array $Arguments = [], ?String $ErrorView = null) : Application{ 
        
        return $this->Hook('Framework')->InternalFile('frameworks', $File . '.php', $UsesAjax, $Arguments, $ErrorView); 
    
    }

    public function Hook(?String $HookDir, bool $UsesAjax = true, ?Array $Arguments = [], ?String $ErrorView = null) : Application{ 

        $Dir = $this->Dir . 'hooks/' . $HookDir . '/';

        if(is_dir($Dir)){

            $Scan = Dir\Scan::Path($Dir);

            if(is_object($Scan)){

                foreach($Scan as $HookFile){ include $HookFile; }
                
            }
    
        }

        return $this;
    
    }

    
    
    
}
