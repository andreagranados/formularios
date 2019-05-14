<?php
require_once toba::proyecto()->get_path().'/php/formularios/NumeroALetras.php';
class ci_impresion extends ci_recibos
{
   
	function conf__form_reciboi(toba_ei_formulario $form)
        {
            $datos=$this->controlador()->dep('datos')->tabla('recibo')->get();
            $form->set_datos($datos);
        }
        function transforma($iNumero){
            $sTexto = NumeroALetras::convertir($iNumero);
            return $sTexto;
        }
	function puntos_cm ($medida, $resolucion=72)
        {
           //// 2.54 cm / pulgada
           return ($medida/(2.54))*$resolucion;
        }
    //generacion recibo
        function vista_pdf(toba_vista_pdf $salida){ 
          
                $salida->set_nombre_archivo("Recibo.pdf");
                //recuperamos el objteo ezPDF para agregar la cabecera y el pie de página 
                $salida->set_papel_orientacion('portrait');
                $salida->inicializar();
                $pdf = $salida->get_pdf();
	        $pdf->ezSetMargins(80, 50, 30, 30);	//top, bottom, left, right
		$titulo="   ";
                $opciones = array(
                    'showHeadings'=>0,
                    'shaded'=>0,
                    'width'=>500,
                    'justification'=>'full',
                    'fontSize' => 12,
                    'rowGap' => 10,
                    'colGap' => 10,
                    
                    );		
		//Pie de p�gina
	       $formato = 'P�gina {PAGENUM} de {TOTALPAGENUM}';
		
               $recibo=$this->controlador()->dep('datos')->tabla('recibo')->get();                 
               
               $datos2=array();
               $num=utf8_decode('RECIBO N°: '.$recibo['id_recibo']);
               $datos2[0]=array('col1'=>'','col2'=>'');
               $datos2[1]=array('col1'=>'','col2'=>'');//aqui numero de recibo
               $datos2[2]=array('col1'=>'UNIVERSIDAD NACIONAL DEL COMAHUE','col2'=>$num);
               $datos2[3]=array('col1'=>'','col2'=>'');
               $datos2[4]=array('col1'=>'','col2'=>'');
                         
               $opc=array('showHeadings' => 0,'showLines'=>1,'shaded'=>0,'width'=>500,'colGap' => 10,'cols' =>array('col1'=>array('width'=>300,'justification'=>'right'),'col2'=>array('width'=>200,'justification'=>'right')));
               //colocamos el cursor a unos 27 cm del final de la pagina
               $pdf->ezSetY($this->puntos_cm(27));
               $pdf->ezTable($datos2,array('col1'=>'','col2'=>''),'',$opc);
              
               $datos=array();
               $m=date("m",strtotime($recibo['fecha']));
               switch ($m) {
                    case 1:$mes=' Enero ';                  break;
                    case 2:$mes=' Febrero ';                  break;  
                    case 3:$mes=' Marzo ';                  break;  
                    case 4:$mes=' Abril ';                  break;  
                    case 5:$mes=' Mayo ';                  break;  
                    case 6:$mes=' Junio ';                  break;  
                    case 7:$mes=' Julio ';                  break;  
                    case 8:$mes=' Agosto ';                  break;  
                    case 9:$mes=' Septiembre ';                  break;  
                    case 10:$mes=' Octubre ';                  break;  
                    case 11:$mes=' Noviembre ';                  break;  
                    case 12:$mes=' Diciembre ';                  break;  
                   default:
                       break;
               }
               
               $dia=date("d",strtotime($recibo['fecha']));
               $anio=date("Y",strtotime($recibo['fecha']));
               $monto_letras= $this->transforma($recibo['monto']);
               $texto=utf8_decode('Recibí de ').trim($recibo['recibi_de']).' la suma de pesos '. $monto_letras.' en concepto de pago '.$recibo[0]['concepto'];
               $texto2=utf8_decode('Neuquén, ').$dia.' de'.$mes.$anio;
               
               $datos[0]=array('col1'=>'');
               $datos[1]=array('col1'=>$texto);
               $datos[2]=array('col1'=>$texto2);
               //number_format($recibo['0']['monto'],2,'.','')
               //para que muestre el monto con 2 decimales
               $datos[3]=array('col1'=>'SON $ '.number_format($recibo['monto'],2,'.',''));
               
               $pdf->ezTable($datos,array('col1'=>''),'',$opciones);

               //colocamos el cursor en la mitad
               $pdf->ezSetY($this->puntos_cm(14));
               $pdf->ezTable($datos2,array('col1'=>'','col2'=>''),'',$opc);
               $pdf->ezTable($datos,array('col1'=>''),'',$opciones);
		//Encabezado
//		$pdf = $salida->get_pdf() ;
//               
//		foreach ($pdf->ezPages as $pageNum=>$id){
//			$pdf->reopenObject($id);
//			$imagen = toba::proyecto()->get_path().'/www/img/logo_toba_siu.jpg';
//			$pdf->addJpegFromFile($imagen, 50, 780, 141, 45);	//imagen, x, y, ancho, alto
//          	$pdf->closeObject();		
//		}	
        }
}?>