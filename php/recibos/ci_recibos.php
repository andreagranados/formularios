<?php
require_once(toba_dir() . '/php/3ros/ezpdf/class.ezpdf.php');
require_once toba::proyecto()->get_path().'/php/formularios/NumeroALetras.php';


class ci_recibos extends formularios_abm_ci
{
        protected $s__where;
        protected $s__datos_filtro;
        protected $s__recibo;
        protected $nombre_tabla='recibo';
       
        //-----------------------------------------------------------------------------------
	//---- filtros ----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__filtros(toba_ei_filtro $filtro)
	{
            if (isset($this->s__datos_filtro)) {
                $filtro->set_datos($this->s__datos_filtro);
            }
	}

	function evt__filtros__filtrar($datos)
	{
            $this->s__datos_filtro = $datos;
            $this->s__where = $this->dep('filtros')->get_sql_where();
	}

	function evt__filtros__cancelar()
	{
            unset($this->s__datos_filtro);
            unset($this->s__where);
	}
        //-----------------------------------------------------------------------------------
	//---- botones -----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------
        function evt__agregar()
	{
             $this->set_pantalla('pant_edicion');
	}
        function evt__anular()
	{//anular un recibo
            if($this->dep('datos')->tabla('recibo')->esta_cargada()){
                $mensaje='';
                $recibo=$this->dep('datos')->tabla('recibo')->get();
                $form_asoc=$this->dep('datos')->tabla('recibo')->asociado_formulario($recibo['id_recibo']);
                //print_r($form_asoc);exit;
                if(count($form_asoc)>0){//tiene asociado un formulario
                   $this->dep('datos')->tabla('formulario')->desasociar_recibo($form_asoc[0]);
                   $mensaje=" Este recibo correspondia al formulario ".$form_asoc[1];
                }
                $datos['estado']='A';
                $this->dep('datos')->tabla('recibo')->set($datos);
                $this->dep('datos')->tabla('recibo')->sincronizar();
                $this->dep('datos')->tabla('recibo')->resetear();
                $this->set_pantalla('pant_inicial');
                toba::notificacion()->agregar('El recibo ha sido anulado correctamente'.$mensaje, 'info');  
            } 
	}
        function evt__volver()
	{
            $this->dep('datos')->tabla('recibo')->resetear();
            $this->set_pantalla('pant_inicial');
	}
        //-----------------------------------------------------------------------------------
	//---- form_recibo -----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------
//        function conf__form_recibo(toba_ei_formulario $form)
//        {
//            $datos=$this->controlador()->dep('datos')->tabla('recibo')->get();
//            $form->set_datos($datos);
//        }
//        function conf__form_reciboi(toba_ei_formulario $form)
//        {
//            $datos=$this->controlador()->dep('datos')->tabla('recibo')->get();
//            $form->set_datos($datos);
//        }
        //-----------------------------------------------------------------------------------
	//---- cuadro -----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cuadro(toba_ei_cuadro $cuadro)
	{
            if (isset($this->s__where)) {
                $cuadro->set_datos($this->dep('datos')->tabla('recibo')->get_listado_filtro($this->s__where));
            } 
//            else{
//                $cuadro->set_datos($this->dep('datos')->tabla('recibo')->get_listado_filtro());
//            }
	}
        //selecciona un recibo del cuadro
        function evt__cuadro__seleccion($datos)
        {
            $this->dep('datos')->tabla('recibo')->cargar($datos);
            $this->set_pantalla('pant_impresion');
        }
        
        function conf__pant_edicion(toba_ei_pantalla $pantalla)
        {
            if($this->dep('datos')->tabla('recibo')->esta_cargada()){
              $this->pantalla()->evento('imprimirr')->mostrar();  
            }else{
              $this->pantalla()->evento('imprimirr')->ocultar();  
            }
        }
        function conf__form_recibosl(toba_ei_formulario $form)
        {
            $datos=$this->dep('datos')->tabla('recibo')->get();
            if(isset($datos['archivo_recibo'])and $datos['archivo_recibo']<>''){
                $fechaHora = idate("Y").idate("m").idate("d").idate("H").idate("i").idate("s");
                $nomb_ft="/formularios/1.0/recibos/".$datos['archivo_recibo'];
                $nomb_ft.="?v=".$fechaHora;
                $datos['imagen_vista_previa_r'] = "<a target='_blank' href='{$nomb_ft}' >recibo_firmado</a>";
            }
            $datos['archivo_recibo']=' ';//para que no aparezca en pantalla el nombre con el que esta guardado el archivo
            $form->set_datos($datos);   
        }
        function evt__form_recibosl__modificacion($datos)
        {
             if($this->dep('datos')->tabla('recibo')->esta_cargada()){
                $recib=$this->dep('datos')->tabla('recibo')->get();
                if(isset($datos['aclaracion'])){
                    $datos2['aclaracion']=$datos['aclaracion'];
                }
                if ($datos['eliminar_recibo']==1) {
                        if (isset($recib['archivo_recibo'])){
                            $datos2['archivo_recibo']=null;
                            $nombre_ca=toba::proyecto()->get_path()."/www/recibos/".$recib['archivo_recibo'];
                            if (file_exists($nombre_ca)) {
                                unlink($nombre_ca);//borra el archivo
                             }
                             $mensaje='Recibo eliminado exitosamente';
                            
                        }else{
                            $mensaje='Este recibo no tiene adjunto';
                            throw new toba_error($mensaje);
                        }
                            
                }else{
                    if (isset($datos['archivo_recibo'])) {//esta modificando el archivo recibo
                        $nombre_ca=str_pad(date("Y", strtotime($recib['fecha'])), 4, "0", STR_PAD_LEFT)."_".$recib['id_recibo'].".pdf";
                        $destino_ca=toba::proyecto()->get_path()."/www/recibos/".$nombre_ca;
                        if(move_uploaded_file($datos['archivo_recibo']['tmp_name'], $destino_ca)){//mueve un archivo a una nueva direccion, retorna true cuando lo hace y falso en caso de que no
                            $datos2['archivo_recibo']=strval($nombre_ca);   
                            $mensaje='Recibo guardado exitosamente';
                        }
                    }
                }
                
                if(isset($datos2)){
                    $this->dep('datos')->tabla('recibo')->set($datos2);
                    $this->dep('datos')->tabla('recibo')->sincronizar();
                    toba::notificacion()->agregar($mensaje, 'info');
                }
               clearstatcache(); 
            }

        }
       
        //-----recibo
        function transforma($iNumero){
            $sTexto = NumeroALetras::convertir($iNumero);
            return $sTexto;
        }
        function puntos_cm ($medida, $resolucion=72)
        {
           //// 2.54 cm / pulgada
           return ($medida/(2.54))*$resolucion;
        }
        //visualizacion  recibo
//        function vista_pdf(toba_vista_pdf $salida){ 
//            if($this->dep('datos')->tabla('recibo')->esta_cargada()){
//                $recibo=$this->dep('datos')->tabla('recibo')->get();            
//                $salida->set_nombre_archivo("Recibo".'_'.$recibo['id_recibo'].".pdf");
//                //recuperamos el objteo ezPDF para agregar la cabecera y el pie de página 
//                $salida->set_papel_orientacion('portrait');
//                $salida->inicializar();
//                $pdf = $salida->get_pdf();
//	        $pdf->ezSetMargins(80, 50, 30, 30);	//top, bottom, left, right
//		$titulo="   ";
//                $opciones = array(
//                    'showHeadings'=>0,
//                    'shaded'=>0,
//                    'width'=>500,
//                    'justification'=>'full',
//                    'fontSize' => 12,
//                    'rowGap' => 10,
//                    'colGap' => 10,
//                    
//                    );		
//		//Pie de p�gina
//	       $formato = 'P�gina {PAGENUM} de {TOTALPAGENUM}';
//                              
//               $datos2=array();
//               $num=utf8_decode('RECIBO N°: '.$recibo['id_recibo']);
//               $datos2[0]=array('col1'=>'','col2'=>'');
//               $datos2[1]=array('col1'=>'','col2'=>'');//aqui numero de recibo
//               $datos2[2]=array('col1'=>'UNIVERSIDAD NACIONAL DEL COMAHUE','col2'=>$num);
//               $datos2[3]=array('col1'=>'','col2'=>'');
//               $datos2[4]=array('col1'=>'','col2'=>'');
//               $opc=array('showHeadings' => 0,'showLines'=>1,'shaded'=>0,'width'=>500,'colGap' => 10,'cols' =>array('col1'=>array('width'=>300,'justification'=>'right'),'col2'=>array('width'=>200,'justification'=>'right')));
//               
//               
//               //colocamos el cursor a unos 27 cm del final de la pagina
//               $pdf->ezSetY($this->puntos_cm(27));
//               $pdf->ezTable($datos2,array('col1'=>'','col2'=>''),'',$opc);
//              
//               $datos=array();
//               $m=date("m",strtotime($recibo['fecha']));
//               switch ($m) {
//                    case 1:$mes=' Enero ';                  break;
//                    case 2:$mes=' Febrero ';                  break;  
//                    case 3:$mes=' Marzo ';                  break;  
//                    case 4:$mes=' Abril ';                  break;  
//                    case 5:$mes=' Mayo ';                  break;  
//                    case 6:$mes=' Junio ';                  break;  
//                    case 7:$mes=' Julio ';                  break;  
//                    case 8:$mes=' Agosto ';                  break;  
//                    case 9:$mes=' Septiembre ';                  break;  
//                    case 10:$mes=' Octubre ';                  break;  
//                    case 11:$mes=' Noviembre ';                  break;  
//                    case 12:$mes=' Diciembre ';                  break;  
//                   default:
//                       break;
//               }
//               
//               $dia=date("d",strtotime($recibo['fecha']));
//               $anio=date("Y",strtotime($recibo['fecha']));
//               $monto_letras= $this->transforma($recibo['monto']);
//               $texto=utf8_decode('Recibí de ').trim($recibo['recibi_de']).' la suma de pesos '. $monto_letras.' en concepto de '.$recibo['concepto'];
//               $texto2=utf8_decode('Neuquén, ').$dia.' de'.$mes.$anio;
//               
//               $datos[0]=array('col1'=>'');
//               $datos[1]=array('col1'=>$texto);
//               $datos[2]=array('col1'=>$texto2);
//               //number_format($recibo['0']['monto'],2,'.','')
//               //para que muestre el monto con 2 decimales
//               $datos[3]=array('col1'=>'SON $ '.number_format($recibo['monto'],2,',','.'));
//               
//               $pdf->ezTable($datos,array('col1'=>''),'',$opciones);
//              
//               //colocamos el cursor en la mitad
//               $pdf->ezSetY($this->puntos_cm(14));
//               $pdf->ezTable($datos2,array('col1'=>'','col2'=>''),'',$opc);
//               $pdf->ezTable($datos,array('col1'=>''),'',$opciones);
//               $imagen = toba::proyecto()->get_path().'/www/img/logo-unc.jpg';
//               $pdf->addJpegFromFile($imagen, $this->puntos_cm(2), $this->puntos_cm(24.5), 70, 65);
//               $pdf->addJpegFromFile($imagen, $this->puntos_cm(2), $this->puntos_cm(11.5), 70, 65);
//               ob_end_clean();//Limpiar (eliminar) el búfer de salida y deshabilitar el almacenamiento en el mismo
//              
//        }
//    }

}
?>