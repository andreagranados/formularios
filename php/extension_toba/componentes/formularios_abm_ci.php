<?php
class formularios_abm_ci extends toba_ci
{
    
    
    function vista_pdf(toba_vista_pdf $salida){ 
        if($this->nombre_tabla=='recibo'){//viene de ci_recibos
            if($this->dep('datos')->tabla('recibo')->esta_cargada()){
                $tiene=true;
                $recibo=$this->dep('datos')->tabla('recibo')->get();     
             }
        }else{
             if($this->nombre_tabla=='formulario'){
                if($this->controlador()->dep('datos')->tabla('formulario')->esta_cargada()){
                    $form=$this->controlador()->dep('datos')->tabla('formulario')->get();
                    if($form['estado']=='T'){
                        //llama a una funcion para generar el recibo. Si ya lo tiene retorna los datos, sino lo tiene lo genere y sino corresponde
                        $sql="select genera_recibo(".$form['id_form'].")";
                        $resul=toba::db('formularios')->consultar($sql);

                    }
                  $tiene=$this->controlador()->dep('datos')->tabla('formulario')->tiene_recibo($form['id_form']);
                  if($tiene){
                      $datos_recibo=$this->controlador()->dep('datos')->tabla('formulario')->get_recibo($form['id_form']); 
                      $recibo=$datos_recibo[0];
                  }
              } 
            }
        }
    
        if($tiene){               //muestro $recibo
                $salida->set_nombre_archivo("Recibo".'_'.$recibo['id_recibo'].".pdf");
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
               
               $datos2=array();
               $num=utf8_decode('RECIBO N°: '.$recibo['id_recibo']);
               $datos2[0]=array('col1'=>'','col2'=>'');
               $datos2[1]=array('col1'=>'','col2'=>'');//aqui numero de recibo
               $datos2[2]=array('col1'=>'UNIVERSIDAD NACIONAL DEL COMAHUE','col2'=>$num);
               $datos2[3]=array('col1'=>'','col2'=>'');
               $datos2[4]=array('col1'=>'','col2'=>'');
               $opc=array('showHeadings' => 0,'showLines'=>1,'shaded'=>0,'width'=>500,'colGap' => 10,'cols' =>array('col1'=>array('width'=>300,'justification'=>'right'),'col2'=>array('width'=>200,'justification'=>'right')));
                    
               $usuario = toba::usuario()->get_id();
               if($usuario=='prodriguez'){//'agranados'
                   $firma = toba::proyecto()->get_path().'/www/img/firma.jpg';
                    //y,x,largo, ancho
                   $pdf->addJpegFromFile($firma, $this->puntos_cm(15), $this->puntos_cm(6.5), 95, 50);     
                   $pdf->addJpegFromFile($firma, $this->puntos_cm(15), $this->puntos_cm(19.5), 95, 50);     
               }
               
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
               $texto=utf8_decode('Recibí de ').trim($recibo['recibi_de']).' la suma de pesos '. $monto_letras.' en concepto de '.$recibo['concepto'];
               $texto2=utf8_decode('Neuquén, ').$dia.' de'.$mes.$anio;

               $datos[0]=array('col1'=>'');
               $datos[1]=array('col1'=>$texto);
               $datos[2]=array('col1'=>$texto2);
               //number_format($recibo['0']['monto'],2,'.','')
               //para que muestre el monto con 2 decimales
               $datos[3]=array('col1'=>'SON $ '.number_format($recibo['monto'],2,',','.'));

               $pdf->ezTable($datos,array('col1'=>''),'',$opciones);
               
               //colocamos el cursor en la mitad
               $pdf->ezSetY($this->puntos_cm(14));
               $pdf->ezTable($datos2,array('col1'=>'','col2'=>''),'',$opc);
               $pdf->ezTable($datos,array('col1'=>''),'',$opciones);
               $imagen = toba::proyecto()->get_path().'/www/img/logo-unc.jpg';
               $pdf->addJpegFromFile($imagen, $this->puntos_cm(2), $this->puntos_cm(24.5), 70, 65);
               $pdf->addJpegFromFile($imagen, $this->puntos_cm(2), $this->puntos_cm(11.5), 70, 65);
               ob_end_clean();//Limpiar (eliminar) el búfer de salida y deshabilitar el almacenamiento en el mismo   
              
              }

    }
}
?>