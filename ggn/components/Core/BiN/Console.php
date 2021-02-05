<?php

namespace Core\BiN;



Class Console{


    public $Input;

    private $Options;



    public function __construct(?Array $Options = null){

        $this->Options = $Options;

        $this->OS = strtolower(\PHP_OS);
        
    }
    



    public function Line(String $Text){

        echo '' . $Text . '';

        echo \PHP_EOL;

        return $this;
        
    }
    



    public function Write(String $Text){

        return readline($Text . ' ');
        
    }
    



    public function ShiftCommand(Array &$Args, String $DOM, ?String $Inject = null){

        \array_unshift($Args, ($Inject ?: 'ggn/console'));

        $Args[1] = $DOM;

        return $this;
    
    }
    
    



    public function Open(String $Path, Bool $Returned = false){

        $Cmd = null;

        switch(substr($this->OS, 0, 3)){

            case 'linux'; $Cmd = "xdg-open"; break;

            case 'macos';
            
            case 'darwin'; 
            
                $Cmd = "open"; 
            
            break;

            case 'win'; $Cmd = "start"; break;
            
        }

        
        // var_dump(\php_uname('s'));
        // var_dump($this->OS, $Cmd . " " . $Path, $_SERVER );


        if($Returned){ return $Cmd . " " . $Path; }

        if(!$Returned){ $this->Exec($Cmd . " " . $Path); }

        return $this;
    
    }
    



    public function Set(String $Name){

        $Name = "\Core\BiN\Console\\" . (ucfirst(str_replace('/', '\\', $Name))) . "";

        if(class_exists($Name)){

            $Class = (new $Name($this))->Set();

        }

        return $this;

    }
    



    public function Exec(String $cmd){

        echo exec($cmd);

        return $this;
        
    }
    



    public function Initialize(Array $Input){

        $this->Input = $Input;



        // $this->Line('////////////// :-) //////////////');



        if($Return = $this->Set($Input[1])){

        }

        else{

            $this->Line("" . ($this->Input[1] ?: "undefined") . " est introuvable!");
            
        }

        
        
        // $Name = $this->Write('What\'s Your Name : ');

        // $this->Line('Your Name is > ' . $Name . ' ');
        
        // echo "input something ... (5 sec)\n";

        // // get file descriptor for stdin 
        // $fd = fopen('php://stdin', 'r');

        // // prepare arguments for stream_select()
        // $read = array($fd);
        // $write = $except = array(); // we don't care about this
        // $timeout = NULL;

        // // wait for maximal 5 seconds for input
        // if(stream_select($read, $write, $except, $timeout)) {
        //     echo "you typed: " . fgets($fd) . PHP_EOL;
        // } else {
        //     echo "you typed nothing\n";
        // }

        
        return $this;
        
    }

    


}