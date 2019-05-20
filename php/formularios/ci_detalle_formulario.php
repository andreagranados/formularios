<?php
require_once 'NumeroALetras.php';
class ci_detalle_formulario extends toba_ci
{
    protected $s__mostrar_i;
    protected $datos;
   
    function get_monto($id_comprobante){
        return $this->controlador()->dep('datos')->tabla('comprobante')->get_monto($id_comprobante);
    }
    
    function get_comprobantes($corresponde){
        $form=$this->controlador()->dep('datos')->tabla('formulario')->get();
        return $this->controlador()->dep('datos')->tabla('comprobante')->get_comprobantes($form['id_punto_venta']);
    }
    
    function transforma($iNumero){
        $sTexto = NumeroALetras::convertir($iNumero);
        return $sTexto;
    }
        
    function get_origen_recurso(){
        if ($this->controlador()->dep('datos')->tabla('formulario')->esta_cargada()) {
           $datos = $this->controlador()->dep('datos')->tabla('formulario')->get();
           $resul=$this->controlador()->dep('datos')->tabla('formulario')->get_origen_recurso($datos['id_form']);
           return $resul;
        }  
    }
    //-----------------------------------------------------------------------------------
    //---- form_inicial -----------------------------------------------------------------
    //-----------------------------------------------------------------------------------

    function conf__form_inicial(toba_ei_formulario $form)
    {
        if ($this->controlador()->dep('datos')->tabla('formulario')->esta_cargada()) {
           $datos = $this->controlador()->dep('datos')->tabla('formulario')->get();
           $form->set_datos($datos);
        }else{
            $this->pantalla()->tab("pant_detalle")->desactivar();
        }     
        $this->s__mostrar_i=0;
    }

    function evt__form_inicial__alta($datos)
    {
        $datos['estado']='I';
        $datos['fecha_creacion']=date('d/m/Y');
        $this->controlador()->dep('datos')->tabla('formulario')->set($datos);
        $this->controlador()->dep('datos')->tabla('formulario')->sincronizar();
        $form=$this->controlador()->dep('datos')->tabla('formulario')->get();
        $elem['id_form']=$form['id_form'];
        $this->controlador()->dep('datos')->tabla('formulario')->cargar($elem);
    }

    function evt__form_inicial__baja($datos)
    {
        $form=$this->controlador()->dep('datos')->tabla('formulario')->get();
        if($form['estado']=='I' or $form['estado']=='R'){
            $bandera=$this->controlador()->dep('datos')->tabla('formulario')->tiene_items($form['id_form']);
            $mensaje='El formulario tiene items. Debe eliminar primero los items';
        }else{
            $mensaje='No puede eliminar este formulario';
            $bandera=true;
        }
        
        if(!$bandera){
            $this->controlador()->dep('datos')->tabla('formulario')->eliminar_todo();
            $this->controlador()->dep('datos')->tabla('formulario')->resetear();
            $this->controlador()->set_pantalla('pant_seleccion');
            toba::notificacion()->agregar('El formulario se ha eliminado correctamente', 'info');  
        }else{
            toba::notificacion()->agregar($mensaje, 'info');  
        }
    }

    function evt__form_inicial__modificacion($datos)
    {
       //si modifica el origen del recurso si ya tiene items. Porque los items son distintos dependiendo del tipo de recurso
        $form=$this->controlador()->dep('datos')->tabla('formulario')->get();
        
        if($form['estado']=='I' or $form['estado']=='R'){ //solo si esta en estado I o en estado R
            if($form['id_origen_recurso']<>$datos['id_origen_recurso'] or $form['id_punto_venta']<>$datos['id_punto_venta']){
                $bandera=$this->controlador()->dep('datos')->tabla('formulario')->tiene_items($form['id_form']);
                if(!$bandera){
                    $this->controlador()->dep('datos')->tabla('formulario')->set($datos);
                    $this->controlador()->dep('datos')->tabla('formulario')->sincronizar();
                }else{
                    toba::notificacion()->agregar('No puede cambiar datos principales del formulario si el mismo tiene items. Borre los items y luego modifique.', 'info');  
                }
            }else{
                $this->controlador()->dep('datos')->tabla('formulario')->set($datos);
                $this->controlador()->dep('datos')->tabla('formulario')->sincronizar();
            }
        }else{
          toba::notificacion()->agregar('El formulario no puede ser modificado', 'info');   
        }
        //$bandera=false;
        
//        if(!$bandera){
//           
//           if($form['estado']=='I' or $form['estado']=='R'){
//                $this->controlador()->dep('datos')->tabla('formulario')->set($datos);
//                $this->controlador()->dep('datos')->tabla('formulario')->sincronizar();
//            }else{
//            toba::notificacion()->agregar('El formulario no puede ser modificado', 'error');  
//            } 
//        }else{
//           toba::notificacion()->agregar('No puede cambiar datos principales del formulario si el mismo tiene items. Borre los items y luego modifique.', 'error');  
//        }
    }
    function evt__form_inicial__modif($datos)//boton para finanzas
    {
         $this->controlador()->dep('datos')->tabla('formulario')->set($datos);
         $this->controlador()->dep('datos')->tabla('formulario')->sincronizar();
    }

    function evt__form_inicial__cancelar($datos)
    {
        $this->controlador()->set_pantalla('pant_seleccion');
        $this->controlador()->dep('datos')->tabla('formulario')->resetear();
        
    }

	//-----------------------------------------------------------------------------------
	//---- JAVASCRIPT -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function extender_objeto_js()
	{
		echo "
		//---- Eventos ---------------------------------------------
		
		{$this->objeto_js}.evt__alta = function()
		{
		}
		//---- Eventos ---------------------------------------------
		
		{$this->objeto_js}.evt__enviar = function()
		{
		}
		";
	}

        //-----------------------------------------------------------------------------------
	//---- form_encabezado -----------------------------------------------------------------
	//-----------------------------------------------------------------------------------
        function conf__form_encabezado(toba_ei_formulario $form)
        {
            if ($this->controlador()->dep('datos')->tabla('formulario')->esta_cargada()) {
                $formul=$this->controlador()->dep('datos')->tabla('formulario')->get();
                $tit=$this->controlador()->dep('datos')->tabla('formulario')->get_titulo($formul['id_form']);
                $dep=$this->controlador()->dep('datos')->tabla('formulario')->get_dependencia($formul['id_form']);
                $pv=$this->controlador()->dep('datos')->tabla('formulario')->get_punto_venta($formul['id_form']);
                $texto=$dep.'<br>'.$tit.'<br>'.$pv.'<br>'.' EXPEDIENTE: '.$formul['nro_expediente']." FECHA: ".date("d/m/Y",strtotime($formul['fecha_creacion']));
                $form->set_titulo($texto);   
            }
        }
	//-----------------------------------------------------------------------------------
	//---- form_detalle -----------------------------------------------------------------
	//-----------------------------------------------------------------------------------
        function conf__form_detalle(toba_ei_formulario $form)
	{
            if($this->s__mostrar_i==1){
               $this->dep('form_detalle')->descolapsar();
               if($this->controlador()->dep('datos')->tabla('item')->esta_cargada()){
                   $datos=$this->controlador()->dep('datos')->tabla('item')->get();
              // print_r($datos);
                   if(!isset($datos['id_comprobante'])){//sino tiene valor
                       $datos['corresponde_factura']='NO';
                       $datos['id_comprobante']=0;   
                       $datos2['monto']=$datos['monto'];
                   }else{
                       $datos['corresponde_factura']='SI';
                       $datos2['monto']=$datos['monto'];
                   }
                   $datos['nro_cuil']=$datos['cuil1'].str_pad($datos['cuil'], 8, '0', STR_PAD_LEFT).$datos['cuil2'];
                   //print_r($datos);
                   $form->set_datos($datos);
                   $form->set_datos($datos2);
               }           
            }else{
                $this->dep('form_detalle')->colapsar();
            }
	}
	function evt__form_detalle__alta($datos)
	{
            $bandera=true;
            //si es f12 debe controlar que no mezcle categoria con deduccion de las sin deduccion
            $form=$this->controlador()->dep('datos')->tabla('formulario')->get();
            $datos['id_form']=$form['id_form'];
            if($form['id_origen_recurso']==1){//f12
                $bandera=$this->controlador()->dep('datos')->tabla('formulario')->puede_agregar($form['id_form'],$datos['id_categ']);
            }
            if($bandera){
                //debe controlar que el numero de comprobante no este en otro formulario
                //$datos['id_comprobate'] solo tiene valor si eleigio que corresponde factura
                if(isset($datos['id_comprobante'])){//si datos['id_comprobante'] tiene valor entonces 
                    $repetido=$this->controlador()->dep('datos')->tabla('comprobante')->esta_repetido($datos['id_comprobante']);
                }else{//no hay id comprobante por tanto corresponde factura=no entonces no esta repetido
                    $repetido=false;
                }
                
                if(!$repetido){
                    $datos['cuil1']=substr($datos['nro_cuil'], 0, 2);
                    $datos['cuil']=substr($datos['nro_cuil'], 2, 8);
                    $datos['cuil2']=substr($datos['nro_cuil'], 10, 1);
                    $this->controlador()->dep('datos')->tabla('item')->set($datos);
                    $this->controlador()->dep('datos')->tabla('item')->sincronizar();
                    $this->controlador()->dep('datos')->tabla('item')->resetear();
                    $this->s__mostrar_i=0;
                }else{
                    toba::notificacion()->agregar('El numero de comprobante se encuentra en otro formulario', 'error');   
                } 
            }else{
                toba::notificacion()->agregar('No puede mezclar categorias', 'error');   
            }
	}
       	
        /**
	 * Metodo invocado desde JS para 'calcular' el nuevo importe
	 */
	function ajax__calcular($parametros, toba_ajax_respuesta $respuesta)
	{
           $total=$this->controlador()->dep('datos')->tabla('comprobante')->get_monto($parametros['id_comprobante']);
           $respuesta->set($total);
	}//esta funcion es llamada desde javascript
         
	function evt__form_detalle__baja()
	{
            $this->controlador()->dep('datos')->tabla('item')->eliminar_todo();
            $this->controlador()->dep('datos')->tabla('item')->resetear();
            toba::notificacion()->agregar('El item se ha eliminado correctamente', 'info');   
            $this->s__mostrar_i=0;
	}

	function evt__form_detalle__modificacion($datos)
	{ 
            $bandera=true;
            
             //si es f12 debe controlar que no mezcle categoria con deduccion de las sin deduccion
            $form=$this->controlador()->dep('datos')->tabla('formulario')->get();
            $datos['id_form']=$form['id_form'];
            if($form['id_origen_recurso']==1){//f12
                $bandera=$this->controlador()->dep('datos')->tabla('formulario')->puede_agregar($form['id_form'],$datos['id_categ']);
            }
            if($bandera){
              //que no se repita el numero de comprobante
               // print_r($item);
                $item=$this->controlador()->dep('datos')->tabla('item')->get();
                if($item['id_comprobante']<>$datos['id_comprobante']){//modifica el comprobante
                   $repetido=$this->controlador()->dep('datos')->tabla('comprobante')->esta_repetido($datos['id_comprobante']);
                }else{
                   $repetido=false; 
                }
                if(!$repetido){
                    $datos['cuil1']=substr($datos['nro_cuil'], 0, 2);
                    $datos['cuil']=substr($datos['nro_cuil'], 2, 8);
                    $datos['cuil2']=substr($datos['nro_cuil'], 10, 1);
                    $this->controlador()->dep('datos')->tabla('item')->set($datos);
                    $this->controlador()->dep('datos')->tabla('item')->sincronizar();
                    toba::notificacion()->agregar('El item se ha modificado correctamente', 'info'); 
                    $this->s__mostrar_i=0;   
                }else{
                    toba::notificacion()->agregar('No es posible modificar porque el comprobante esta en otro formulario', 'error');   
                }
                
            }
            
	}

	function evt__form_detalle__cancelar()
	{
            $this->controlador()->dep('datos')->tabla('item')->resetear();
            $this->s__mostrar_i=0;
	}

        /**
	 * Permite configurar el evento por fila.
	 * �til para decidir si el evento debe estar disponible o no de acuerdo a los datos de la fila
	 * [wiki:Referencia/Objetos/ei_cuadro#Filtradodeeventosporfila Ver m�s]
	 */
	function conf_evt__cuadro_detalle__seleccion(toba_evento_usuario $evento, $fila)
	{
		if (($this->datos[$fila]['id_item'] ) <0) {
			$evento->anular();
		}
	}
	//-----------------------------------------------------------------------------------
	//---- cuadro_detalle ---------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cuadro_detalle(toba_ei_cuadro $cuadro)
	{
           $form=$this->controlador()->dep('datos')->tabla('formulario')->get();
           $this->datos=$this->controlador()->dep('datos')->tabla('item')->get_listado($form['id_form']);
          // print_r($this->datos);exit;
           if($form['estado']<>'A'){//el boton imprimir solo aparece si el formualrio esta aprobado
                $cuadro->eliminar_evento('imprimir');
            }
           
           if(count($this->datos)>0){
               switch ($this->datos[0]['id_origen_recurso']) {
                   case 1://si es F12
                       $columnas=array('organismo','nro_resol','proviene_descrip');
                       $cuadro->eliminar_columnas($columnas);
                       $elem['categ']="<b>TOTAL BRUTO:</b>";
                       $elem['id_item']=-1;
                       $elem['monto']=$this->datos[0]['total'];
                       array_push($this->datos,$elem);
                       $elem['categ']='<b>DEDUCCION '.$this->datos[0]['porc_retencion'].' %:</b>';
                       $elem['id_item']=-2;
                       $elem['monto']=$this->datos[0]['retencion'];
                       array_push($this->datos,$elem);
                       $elem['categ']='<b>TOTAL NETO :</b>';
                       $elem['id_item']=-3;
                       $elem['monto']=$this->datos[0]['total']-$this->datos[0]['retencion'];
                       array_push($this->datos,$elem);
                      
                       break;
                   case 2://si es F13
                       $columnas=array('categ','vinc','proviene_descrip');
                       $cuadro->eliminar_columnas($columnas);
                       $elem['nro_resol']="<b>TOTAL:</b>";
                       $elem['id_item']=-1;
                       $elem['monto']=$this->datos[0]['total'];
                       array_push($this->datos,$elem);
                       break;
                    case 3://si es F14
                       $columnas=array('categ','vinc','nro_resol','organismo');
                       $cuadro->eliminar_columnas($columnas);
                       $elem['proviene_descrip']="<b>TOTAL:</b>";
                       $elem['id_item']=-1;
                       $elem['monto']=$this->datos[0]['total'];
                       array_push($this->datos,$elem);
                       break;
                   case 4://f21
                       $columnas=array('categ','vinc','nro_resol','organismo','proviene_descrip');
                       $cuadro->eliminar_columnas($columnas);
                       $elem['nro_factura']="<b>TOTAL:</b>";
                       $elem['id_item']=-1;
                       $elem['monto']=$this->datos[0]['total'];
                       array_push($this->datos,$elem);
                       break;
                   
                   case 5: //f22
                       $columnas=array('categ','vinc','nro_resol','organismo','proviene_descrip');
                       $cuadro->eliminar_columnas($columnas);
                       $elem['nro_factura']="<b>TOTAL:</b>";
                       $elem['id_item']=-1;
                       $elem['monto']=$this->datos[0]['total'];
                       array_push($this->datos,$elem);break;
                   
                   default:
                       break;
                   //si es F12 debo colocar  total bruto, deduccion y total neto
               }
          }
               
//            $cuadro->limpiar_columnas();
//            $cuadro->agregar_columnas($columnas);
           
//           $elem['categ']="<b>TOTAL BRUTO</b>";
//           $elem['id_item']=-1;
//           $elem['monto']=$datos[0]['total']; 
//           array_push($datos,$elem);
          

          
           return $this->datos;
	}

	function evt__cuadro_detalle__seleccion($datos)
	{
            if ($this->controlador()->dep('datos')->tabla('formulario')->esta_cargada()) {
               $form = $this->controlador()->dep('datos')->tabla('formulario')->get();
               if($form['estado']=='I' or $form['estado']=='R'){
                   if($datos['id_item']>0){
                    $this->controlador()->dep('datos')->tabla('item')->cargar($datos);
                    $this->s__mostrar_i=1;
                  }
               }else{
                    toba::notificacion()->agregar('Los datos no pueden ser modificados porque el formulario no esta en estado Inicial(I) o Rechazado(R)', 'info');   
               }
             }
            
	}
       

	//-----------------------------------------------------------------------------------
	//---- Eventos ----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------
//alta de un nuevo item
	function evt__alta()
	{
            $form=$this->controlador()->dep('datos')->tabla('formulario')->get();
            if($form['estado']=='I' or $form['estado']=='R'){
                $this->controlador()->dep('datos')->tabla('item')->resetear();
                $this->s__mostrar_i=1;
            }else{
                toba::notificacion()->agregar('Ya no puede agregar items al formulario. Verifique el estado del formulario', 'info'); 
            }
	}
        function evt__enviar()
	{
//            $x=13550.40;
//             $monto_letras= $this->transforma($x);
//             print_r($monto_letras);
//            exit;
            $form=$this->controlador()->dep('datos')->tabla('formulario')->get();
            if($form['estado']=='I' or $form['estado']=='R' ){
                $band=$this->controlador()->dep('datos')->tabla('formulario')->tiene_items($form['id_form']);
                if($band){
                    $datos['estado']='E';
                    $this->controlador()->dep('datos')->tabla('formulario')->set($datos);
                    $this->controlador()->dep('datos')->tabla('formulario')->sincronizar();
                    $this->controlador()->dep('datos')->tabla('formulario')->resetear();
                    $this->controlador()->set_pantalla('pant_seleccion');
                    toba::notificacion()->agregar('El formulario ha sido enviado correctamente', 'info');   
                }else{
                    toba::notificacion()->agregar('El formulario no tiene items cargados', 'info'); 
                }
                
            }else{
                toba::notificacion()->agregar('Ya no puede enviar. Verifique el estado del formulario.', 'info'); 
            }
	}
//        function conf__pant_detalle(toba_ei_pantalla $pantalla){
//            //El evento "imprimir" no posee un VINCULO ASOCIADO.
//            if ($this->controlador()->dep('datos')->tabla('formulario')->esta_cargada()) {
//                $form=$this->controlador()->dep('datos')->tabla('formulario')->get();
//                if($insc['estado']=='T'){
//                    $this->evento('imprimir')->mostrar();
//                    $this->evento('imprimir_ficha')->mostrar();
//                    $this->evento('imprimir')->vinculo()->agregar_parametro('evento_trigger', 'imprimir1');
//                    //$this->evento('imprimir_ficha')->vinculo()->agregar_parametro('evento_trigger', 'imprimir2'); 
//                }else{
//                     if($insc['estado']=='I'){
//                        $this->evento('imprimir')->ocultar();
//                        $this->evento('imprimir_ficha')->ocultar();
//                     }else{//oculta todo para el resto de los estados
//                        $this->evento('enviar')->ocultar();
//                        $this->evento('previu')->ocultar();
//                        $this->evento('imprimir')->ocultar();
//                        $this->evento('imprimir_ficha')->ocultar();
//                         
//                     }
//                }
//             }
//            
//        }
        function puntos_cm ($medida, $resolucion=72)
        {
           //// 2.54 cm / pulgada
           return ($medida/(2.54))*$resolucion;
        }
        //generacion recibo
        function vista_pdf(toba_vista_pdf $salida){  
         
         $form=$this->controlador()->dep('datos')->tabla('formulario')->get();
        // if($form['estado']=='T'){
            //llama a una funcion para generar el recibo. Si ya lo tiene retorna los datos, sino lo tiene lo genere y sino corresponde
            $sql="select genera_recibo(".$form['id_form'].")";
            $resul=toba::db('formularios')->consultar($sql);
            if($resul[0]['genera_recibo']==1){//corresponde generar recibo
                //recupero los datos del recibo para mostrarlos
                $recibo=$this->controlador()->dep('datos')->tabla('formulario')->get_recibo($form['id_form']);    
                $salida->set_nombre_archivo("Recibo.pdf");
                //recuperamos el objteo ezPDF para agregar la cabecera y el pie de página 
                $salida->set_papel_orientacion('portrait');
                $salida->inicializar();
                $pdf = $salida->get_pdf();
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
               $num=utf8_decode('RECIBO N°: '.$recibo[0]['id_recibo']);
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
               $m=date("m",strtotime($recibo[0]['fecha']));
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
               
               $dia=date("d",strtotime($recibo[0]['fecha']));
               $anio=date("Y",strtotime($recibo[0]['fecha']));
               $monto_letras= $this->transforma($recibo[0]['monto']);
               $texto=utf8_decode('Recibí de ').trim($recibo[0]['recibi_de']).' la suma de pesos '. $monto_letras.' en concepto de pago '.$recibo[0]['concepto'];
               $texto2=utf8_decode('Neuquén, ').$dia.' de'.$mes.$anio;
               
               $datos[0]=array('col1'=>'');
               $datos[1]=array('col1'=>$texto);
               $datos[2]=array('col1'=>$texto2);
               //number_format($recibo['0']['monto'],2,'.','')
               //para que muestre el monto con 2 decimales
               $datos[3]=array('col1'=>'SON $ '.number_format($recibo[0]['monto'],2,'.',''));
               
               $pdf->ezTable($datos,array('col1'=>''),'',$opciones);

               //colocamos el cursor en la mitad
               $pdf->ezSetY($this->puntos_cm(14));
               $pdf->ezTable($datos2,array('col1'=>'','col2'=>''),'',$opc);
               $pdf->ezTable($datos,array('col1'=>''),'',$opciones);
               $imagen = toba::proyecto()->get_path().'/www/img/logo-unc.jpg';
               //$pdf->addJpegFromFile($imagen, 55, 690, 70, 65);//funciona
               //donde 'x' e 'y' son las coordenadas de la esquina inferior izquierda de la imagen.
               $pdf->addJpegFromFile($imagen, $this->puntos_cm(2), $this->puntos_cm(24.5), 70, 65);
               $pdf->addJpegFromFile($imagen, $this->puntos_cm(2), $this->puntos_cm(11.5), 70, 65);

              }
        //}
        }
            
}
?>