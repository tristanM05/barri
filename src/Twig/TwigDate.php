<?php

namespace App\Twig;

use Twig\TwigFilter;
use Twig\Extension\AbstractExtension;

class TwigDate extends AbstractExtension{

    public function getFilters(){
        return[new TwigFilter('frenchdate',[$this, 'datefrench'])];
    }

    public function datefrench($content) : string{
        // dump($content);
        $new_cont = date_format($content, 'j F Y');
        // dump($new_cont);
        $explode = explode(' ',$new_cont);
        

        if ($explode[1] == "January") {
            $explode[1] = "Janvier";
        } elseif ($explode[1] == "February") {
            $explode[1] = "Février";
        } elseif ($explode[1] == "March") {
            $explode[1] = "Mars";
        } elseif ($explode[1] == "April") {
            $explode[1] = "Avril";
        } elseif ($explode[1] == "May") {
            $explode[1] = "Mai";
        } elseif ($explode[1] == "June") {
            $explode[1] = "Juin";
        } elseif ($explode[1] == "July") {
            $explode[1] = "Juillet";
        } elseif ($explode[1] == "August") {
            $explode[1] = "Août";
        } elseif ($explode[1] == "September") {
            $explode[1] = "Septembre";
        } elseif ($explode[1] == "October") {
            $explode[1] = "Octobre";
        } elseif ($explode[1] == "November") {
            $explode[1] = "Novembre";
        } else {
            $explode[1] = "Décembre";
        }
        
        return($explode[0].' '.$explode[1].' '.$explode[2]);
    }
}