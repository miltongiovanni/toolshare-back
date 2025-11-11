<?php

namespace App\Service;

class Utilidades
{
    public function getNit($numero): string{
        return number_format($numero, 0, '.', '.') . "-" . $this->verificaNit($numero);
    }

    public function verificaNit($numero){
        $primos = array(3, 7, 13, 17, 19, 23, 29, 37, 41, 43, 47, 53, 59, 67, 71);
        $suma = 0;
        for ($i=0; $i<=(strlen($numero)-1); $i++ ){
            $suma += substr($numero, -($i+1), 1) * $primos[$i];
        }
        $resto = $suma % 11;
        if ( $resto == 0 || $resto == 1 )
            $digitov = $resto;
        else
            $digitov = 11 - $resto;
        return $digitov;
    }

}