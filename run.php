<?php

namespace GGN;



/** @GGN :start */

session_start();





/** @GGN :state */

$GGN = (Object) [];





/** @GGN :require */

require dirname(__FILE__) . '/ggn/config.php';

require dirname(__FILE__) . '/ggn/' . $GGN->{'Kernel:Default'} . '.php';






/** @GGN :init */

use GGN\ARC;

use GGN\Settings;

$GGN->{'System:Config'} = (Object) json_decode(file_get_contents($GGN->{'Dir:Root'} . 'ggn.json'));

$GGN->{'System:Return'} = false;





/** 
 * @GGN :trigger 
 * */

if(defined("GGN_ARC_USING") && GGN_ARC_USING === true){


    /** 
     * @ARC :state 
     * */


    $GGN->{'ARC:State'} = new ARC( '/' . ( $_REQUEST['ggn_arc_request'] ?: substr($_SERVER['PATH_INFO'] ?: (''), 1) ) ?: '', new Settings($GGN->{'System:Config'}->ARC) );
    

    /** 
     * @ARC :main 
     * */

    $GGN->{'ARC:State'}->Main();


    // return false;

    
}
