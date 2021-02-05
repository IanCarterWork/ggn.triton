<?php

namespace Terminal\Modules\Config;


class Command{


    use \Framework\Ability\Terminal\Console;
    

    protected $UserLevel = 3;


    var $Title = "GGN Configration File";
    
    var $Description = "Config Manager";

    var $ScrollFocus = false;

    var $Ui = false;

    var $Data = false;

    var $PanelTitle = false;


    public function __construct(){
        
        $this->Ui = $this->UiPanel;
        
    }

    static public function LoadSheet($Path){

        $GGN = (Object) [];

        include $Path . 'Config.php';

        return $GGN;
        
    }

    public function Exec(Array $Query){

        global $GGN;


        switch(strtolower($Query[0])){

            case 'get:vars':

                $this->PanelTitle = 'Gestionnaire des variables';

                $this->Ui = $this->UiEditPad;

                $this->ScrollFocus = $this->FocusPanelTop;

                $this->Data = self::LoadSheet($GGN->{'Dir:Main'});

            break;

            default:

                $this->Data = 'Executant introuvable';

            break;
            
        }

        return $this;

    }
    
}


