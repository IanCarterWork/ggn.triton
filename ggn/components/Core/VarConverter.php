<?php

namespace Core;


Class VarConverter{

    static public function BytesUnits(){

        return ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
        
    }

    public function toBytes(String $Length): ?Int{

        $Units = self::BytesUnits();
        
        $Number = substr($Length, 0, -2);
        
        $Unit = strtoupper(substr($Length,-2));
    
        if(is_numeric(substr($Unit, 0, 1))) { return preg_replace('/[^\d]/', '', $Length); }
    
        $Expo = array_flip($Units)[$Unit] ?? null;
        
        if($Expo === null) { return null; }
    
        return $Number * (1024 ** $Expo);

    }

}