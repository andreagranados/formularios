<?php
class ci_impresion extends toba_ci
{
    	
//	function vista_pdf(toba_vista_pdf $salida)
//	{
//		//Cambio lo m�rgenes accediendo directamente a la librer�a PDF
//		$pdf = $salida->get_pdf();
//		$pdf->ezSetMargins(80, 50, 30, 30);	//top, bottom, left, right
//				
//		//Pie de p�gina
//		$formato = 'P�gina {PAGENUM} de {TOTALPAGENUM}';
//		$pdf->ezStartPageNumbers(300, 20, 8, 'left', $formato, 1);	//x, y, size, pos, texto, pagina inicio
//
//		//Inserto los componentes usando la API de toba_vista_pdf
//		$salida->titulo($this->get_nombre());
//		$salida->mensaje('Nota: Este es el Principal');
//		
//		//Encabezado
//		$pdf = $salida->get_pdf();
//		foreach ($pdf->ezPages as $pageNum=>$id){
//			$pdf->reopenObject($id);
//			$imagen = toba::proyecto()->get_path().'/www/img/logo_toba_siu.jpg';
//			$pdf->addJpegFromFile($imagen, 50, 780, 141, 45);	//imagen, x, y, ancho, alto
//          	$pdf->closeObject();		
//		}		
//		
//	}
    //generacion recibo
        function vista_pdf(toba_vista_pdf $salida){ 
            
            if($this->dep('datos')->tabla('recibo')->esta_cargada()){
                //recupero los datos del recibo para mostrarlos
                $recibo=$this->dep('datos')->tabla('recibo')->get();
                $nombre='Recibo_'.$recibo['id_recibo'].'.pdf';
                $salida->set_nombre_archivo($nombre);
                //recuperamos el objteo ezPDF para agregar la cabecera y el pie de página 
                $salida->set_papel_orientacion('portrait');
                $salida->inicializar();
                $pdf = $salida->get_pdf();
                //$pdf = new Cezpdf('a4');
                $pdf->ezSetMargins(80, 50, 30, 30);//top,bottom,left,righ
                //Configuramos el pie de página. El mismo, tendra el número de página centrado en la página y la fecha ubicada a la derecha. 
              
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
               $texto=utf8_decode('Recibí de ').trim($recibo['recibi_de']).' la suma de pesos '. $monto_letras.' en concepto de pago '.$recibo['concepto'];
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
               
////                $imagen = toba::proyecto()->get_path().'/www/img/logo-unc.jpg';
////              $pdf->addJpegFromFile($imagen,2, 34, 70, 65);
             $imagen = toba::proyecto()->get_path().'/www/img/logo_uc.jpg';             
             $pdf->addJpegFromFile($imagen, 40, 715, 70, 66); 
               // $pdf->addImage($imagen, 50, 67, 47, 50);
                //$pdf->Image('http://chart.googleapis.com/chart?cht=p3&chd=t:60,40&chs=250x100&chl=Hello|World',60,30,90,0,'PNG');
               //$pdf->addJpegFromFile($imagen, $this->puntos_cm(2), $this->puntos_cm(24.5), 70, 65);
//               $pdf->ezTable($datos,array('col1'=>''),'',$opciones);
//            //colocamos el cursor en la mitad
//               $pdf->ezSetY($this->puntos_cm(14));
//               $pdf->ezTable($datos2,array('col1'=>'','col2'=>''),'',$opc);
//               $pdf->ezTable($datos,array('col1'=>''),'',$opciones);
//               //agrego el logo
//               //$imagen = 'C:\proyectos\toba_2.6.3\proyectos\formularios\www\img\sello.jpg';
//               $imagen = toba::proyecto()->get_path().'/www/img/sello.jpg';
//              // $pdf->addJpegFromFile('logo.jpg', $this->puntos_cm(2), $this->puntos_cm(24.5), 70, 65);
//                // $this->addJpegFromFile($this->ezBackground['image'], 5, 45, 70, 65);
//               	$pdf->addImage($imagen, 5, 45, 70, 65, $quality=75);
//               //$pdf->ezImage($imagen, 0, 40, 'none', 'left');
//               //$pdf->addJpegFromFile($imagen, $this->puntos_cm(2), $this->puntos_cm(24.5), 70, 65);

        }
        }
}?>