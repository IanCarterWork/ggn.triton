<?php

namespace Framework\SEO;


class Meta{

    static public function Build(

        String $Title

        , String $Description

        , ?String $Image = null

        , ?String $Type = 'website'
        
    ) : String{


        $Meta = [];
        
        $URL = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];


        /** Facebook */
        
        $Meta[] = '<meta property="og:url" content="' . $URL . '">';
        
        $Meta[] = '<meta property="og:title" content="' . ($Title) . '">';
        
        $Meta[] = '<meta property="og:description" content="' . ($Description) . '">';
        
        
        /** Google */

        $Meta[] = '<meta name="title" content="' . ($Title) . '">';
        
        $Meta[] = '<meta itemprop="name" content="' . ($Title) . '">';
        
        $Meta[] = '<meta itemprop="description" content="' . ($Description) . '">';
        
        
        /** Twitter */

        $Meta[] = '<meta property="twitter:url" content="' . ($URL) . '">';

        $Meta[] = '<meta property="twitter:title" content="' . ($Title) . '">';

        $Meta[] = '<meta property="twitter:description" content="' . ($Description) . '">';

        
        
        if($Image){ 
            
            $Meta[] = '<meta itemprop="image" content="' . ($Image) . '">';
            
            $Meta[] = '<meta property="og:image" content="' . ($Image) . '">'; 
            
            $Meta[] = '<meta property="twitter:image" content="' . ($Image) . '">';
        
        }

        if($Type){ 
            
            $Meta[] = '<meta property="og:type" content="' . $Type . '">'; 
        
        }
        


        return implode("\n", $Meta);

    }


}