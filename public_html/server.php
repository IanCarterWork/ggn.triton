<?php


    $AllowPaths = [

        '/assets\/(.*)$/'

        , '/mokian\/(.*)$/'

        , '/mokian.js$/'

        , '/viewer\/(.*)$/'
    ];


    
    foreach($AllowPaths as $Path){

        if( $MatchPaths = preg_match($Path, $_SERVER["REQUEST_URI"]) ){ return false; }

    }

    include __DIR__ . '/index.php';


?>