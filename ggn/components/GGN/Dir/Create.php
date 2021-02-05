<?php

namespace GGN\Dir;


Class Create {

    public function Path(String $Path, Int $Flag) : Bool{

        return (Bool) mkdir($Path, $Flag);

    }

}