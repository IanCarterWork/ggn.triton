<?php

	namespace GGN;

		use GGN;

		use GGN\xDump;


    class Caches{


        protected $Dir;

        protected $Path;

        protected $Slot;

        public $Hash;


        public function __construct(?String $Path = null, ?String $Slot = null){

            global $GGN;


            $Path = $Path ?: ($_SERVER['REQUEST_URI']);

            $this->OriginalPath = $Path;

            $this->Slot = $Slot;

            $this->Dir = $GGN->{'Dir:Caches'};

            $this->Hash = sha1($Path);

            $this->Path = $this->Dir . $this->Slot . '@' . sha1($Path) . '.ggn-cache';


            if(\is_dir($this->Dir . '@' . $this->Slot)){

                \mkdir($this->Dir . '@' . $this->Slot, 0777, true);

            }

        }

        
        public function Entry() :String{ return $this->Path; }


        public function Slot() :String{ return $this->Slot; }

        public function Hash() :?String{

            if(\is_file($this->Path)){

                return \sha1_file($this->Path);
                
            }

            return null;
            
        }


        public function SetHeader(){

            if(\is_file($this->Path)){

                header('Content-Type: ' . mime_content_type($this->Path));
                
            }

            return $this;

        }


        public function Get() :?String{

            if(\is_file($this->Path)){

                return file_get_contents($this->Path);
                
            }

            return null;

        }


        public function Set(Bool $Overwrite = true) :?Bool{

            if($Overwrite == true){

                return file_put_contents($this->Path, \file_get_contents($this->OriginalPath)) ? true : false;
                
            }

            else{

                if(is_file($this->Path)){ return true; }

                else{ return false; }
                
            }

        }



        public function Update(){

            $Hash = $this->Hash();

            $Source = (\is_file($this->OriginalPath)) ? \sha1_file($this->OriginalPath) : null;


            if($Source != null && $Source != $Hash){

                if($this->Set(true)){ return $this->Get(); }

                else{ return null; }

            }

            return $this->Get();

        }



        public function HasChanged() :?Bool{

            $Hash = $this->Hash();

            $Source = (\is_file($this->OriginalPath)) ? \sha1_file($this->OriginalPath) : null;

            if($Source != null && $Source != $Hash){ return true; }

            return false;

        }




    }
