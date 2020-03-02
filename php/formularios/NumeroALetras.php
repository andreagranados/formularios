<?php
/**
 * Clase que implementa un coversor de n�meros
 * a letras.
 *
 * Soporte para PHP >= 5.4
 * Para soportar PHP 5.3, declare los arreglos
 * con la funci�n array.
 *
 * @author AxiaCore S.A.S
 *
 */
class NumeroALetras
{
//    protected $UNIDADES
//    = array(
//        '',
//        'UN ',
//        'DOS ',
//        'TRES ',
//        'CUATRO ',
//        'CINCO ',
//        'SEIS ',
//        'SIETE ',
//        'OCHO ',
//        'NUEVE ',
//        'DIEZ ',
//        'ONCE ',
//        'DOCE ',
//        'TRECE ',
//        'CATORCE ',
//        'QUINCE ',
//        'DIECISEIS ',
//        'DIECISIETE ',
//        'DIECIOCHO ',
//        'DIECINUEVE ',
//        'VEINTE '
//    );
//    protected $DECENAS = array(
//        'VENTI',
//        'TREINTA ',
//        'CUARENTA ',
//        'CINCUENTA ',
//        'SESENTA ',
//        'SETENTA ',
//        'OCHENTA ',
//        'NOVENTA ',
//        'CIEN '
//    );
//    protected $CENTENAS = array(
//        'CIENTO ',
//        'DOSCIENTOS ',
//        'TRESCIENTOS ',
//        'CUATROCIENTOS ',
//        'QUINIENTOS ',
//        'SEISCIENTOS ',
//        'SETECIENTOS ',
//        'OCHOCIENTOS ',
//        'NOVECIENTOS '
//    );
//    function convertGroup($n)
//    {
//       print_r($n[0]);exit;
//        $output = '';
//        if ($n == '100') {
//            $output = "CIEN ";
//        } else if ($n[0] !== '0') {
//            $output = $this->CENTENAS[$n[0] - 1];
//        }
//        $k = intval(substr($n,1));
//        if ($k <= 20) {
//            $output .= $this->UNIDADES[$k];
//        } else {
//            if(($k > 30) && ($n[2] !== '0')) {
//                $output .= sprintf('%sY %s', $this->DECENAS[intval($n[1]) - 2], $this->UNIDADES[intval($n[2])]);
//            } else {
//                $output .= sprintf('%s%s', $this->DECENAS[intval($n[1]) - 2], $this->UNIDADES[intval($n[2])]);
//            }
//        }
//        return $output;
//    }
//    public function convertir($number, $moneda = '', $centimos = '', $forzarCentimos = false)
//    {
//        $converted = '';
//        $decimales = '';
//        if (($number < 0) || ($number > 999999999)) {
//            return 'No es posible convertir el numero a letras';
//        }
//        $div_decimales = explode('.',$number);//devuelve el arreglo de string
//       
//        if(count($div_decimales) > 1){
//            $number = $div_decimales[0];
//            $decNumberStr = (string) $div_decimales[1];
//            if(strlen($decNumberStr) == 2){
//                $decNumberStrFill = str_pad($decNumberStr, 9, '0', STR_PAD_LEFT);
//                $decCientos = substr($decNumberStrFill, 6);
//                $decimales = $this->convertGroup($decCientos);
//            }
//        }
//        else if (count($div_decimales) == 1 && $forzarCentimos){//no tiene decimales
//            $decimales = 'CERO ';
//        }
//        $numberStr = (string) $number;
//        $numberStrFill = str_pad($numberStr, 9, '0', STR_PAD_LEFT);
//        $millones = substr($numberStrFill, 0, 3);
//        $miles = substr($numberStrFill, 3, 3);
//        $cientos = substr($numberStrFill, 6);
//        if (intval($millones) > 0) {
//            if ($millones == '001') {
//                $converted .= 'UN MILLON ';
//            } else if (intval($millones) > 0) {
//                $converted .= sprintf('%sMILLONES ', self::convertGroup($millones));
//            }
//        }
//        if (intval($miles) > 0) {
//            if ($miles == '001') {
//                $converted .= 'MIL ';
//            } else if (intval($miles) > 0) {
//                $converted .= sprintf('%sMIL ', self::convertGroup($miles));
//            }
//        }
//        if (intval($cientos) > 0) {
//            if ($cientos == '001') {
//                $converted .= 'UN ';
//            } else if (intval($cientos) > 0) {
//                $converted .= sprintf('%s ', self::convertGroup($cientos));
//            }
//        }
//        if(empty($decimales)){
//            $valor_convertido = $converted . strtoupper($moneda);
//        } else {
//            $valor_convertido = $converted . strtoupper($moneda) . ' CON ' . $decimales . ' ' . strtoupper($centimos);
//        }
//        print_r($valor_convertido);exit;
//        return $valor_convertido;
//    }
//    //private static function convertGroup($n)
 
    //El número a convertir  a letras debe ser un entero positivo menor a 999,999, 999,999.
//    El número a convertir  a letras debe ser un entero positivo menor a 999,999, 999,999.
//    La línea del codigo 3 declara un array con la representación alfabética de los números comprendidos entre 1 y 29. Esta característica se debe a que dichos números no siguen un diagrama para su escritura.
//La línea 11 nos declara un arreglo que contiene la conversión de los números 30, 40 … y 90.
//  La línea del codigo 14 obtiene el módulo 10 de n.
//Si el módulo del codigo 10 de n es cero se regresa la posición asociada a n en el vector decenas(línea 11).
//Si el módulo del codigo 10 de n es diferente de cero. Se regresa la posición asociada a n en el vector decenas y se hace un llamado a la función básico.
//La línea del codigo 21 declara un arreglo para la representación alfabética de los números 100,200, … y 900.
//La línea del codigo  30 hace uso de un operador ternario para poder manejar la escritura de los números comprendidos entre 101 y 199.
//En la línea del codigo  39 se obtiene la longitud de n. En el caso de los miles está longitud varía entre 4 y 6.
//substr($cadena, -3) obtiene los últimos 3 caracteres de la cadena, línea 41.
    function basico($numero) {
        $valor = array ('uno','dos','tres','cuatro','cinco','seis','siete','ocho',
        'nueve','diez', 'once','doce','trece','catorce','quince','dieciséis','diecisiete','dieciocho','diecinueve','veinte','veintiuno','veintidos','veintitres','veinticuatro','veinticinco',
        'veintiséis','veintisiete','veintiocho','veintinueve');
        return $valor[$numero - 1];
    }
 
    function decenas($n) {
        $decenas = array (30=>'treinta',40=>'cuarenta',50=>'cincuenta',60=>'sesenta',
        70=>'setenta',80=>'ochenta',90=>'noventa');
        if( $n <= 29) return self::basico($n);
        $x = $n % 10;
        if ( $x == 0 ) {
            return $decenas[$n];
        } else return $decenas[$n - $x].' y '. self::basico($x);
    }
 
    function centenas($n) {
        $cientos = array (100 =>'cien',200 =>'doscientos',300=>'trecientos',
        400=>'cuatrocientos', 500=>'quinientos',600=>'seiscientos',
        700=>'setecientos',800=>'ochocientos', 900 =>'novecientos');
        if( $n >= 100) {
            if ( $n % 100 == 0 ) {
                return $cientos[$n];
            } else {
                $u = (int) substr($n,0,1);
                $d = (int) substr($n,1,2);
                return (($u == 1)?'ciento':$cientos[$u*100]).' '.self::decenas($d);
            }
        } else //return $this->decenas($n);
            return self::decenas($n);
    }
 
    function miles($n) {
        if($n > 999) {
            if( $n == 1000) {return 'mil';}
            else {
                $l = strlen($n);
                $c = (int)substr($n,0,$l-3);
                $x = (int)substr($n,-3);
                if($c == 1) {$cadena = 'mil '.self::centenas($x);}
                else if($x != 0) {$cadena = self::centenas($c).' mil '.self::centenas($x);}
                else $cadena = self::centenas($c). ' mil';
                return $cadena;
            }
        } else 
            return self::centenas($n);
    }
 
    function millones($n) {
        if($n == 1000000) {return 'un millón';}
        else {
            $l = strlen($n);
            $c = (int)substr($n,0,$l-6);
            $x = (int)substr($n,-6);
        if($c == 1) {
            $cadena = ' millón ';
        } else {
            $cadena = ' millones ';
        }
        return self::miles($c).$cadena.(($x > 0)?self::miles($x):'');
        }
    }
    function convertir($n) {
        $div_decimales = explode('.',$n);//devuelve el arreglo de string //Array ( [0] => 900 [1] => 09 )
        if(count($div_decimales)>1){//tiene 
            switch ($div_decimales[1]) {
                case 1:$auxi=10; break;
                case 2:$auxi=20; break;
                case 3:$auxi=30; break;
                case 4:$auxi=40; break;
                case 5:$auxi=50; break;
                case 6:$auxi=60; break;
                case 7:$auxi=70; break;
                case 8:$auxi=80; break;
                case 9:$auxi=90; break;
                   

                default:
                    $auxi=$div_decimales[1];
                    break;
            }
            $final=' con '.$auxi.'/100'; 
        }else{
            $final=' con 00/100 ';
        }
        $nn=$div_decimales[0];
        
        //cambio $this->basico() por self::
        switch ($nn) {
            case ( $nn >= 1 && $nn <= 29) : return self::basico($nn).$final; break;
            case ( $nn >= 30 && $nn < 100) : return self::decenas($nn).$final; break;
            case ( $nn >= 100 && $nn < 1000) : return self::centenas($nn).$final; break;
            case ( $nn >= 1000 && $nn <= 999999): return self::miles($nn).$final; break;
            case ( $nn >= 1000000): return self::millones($nn);
        }
    }
 


}
?>