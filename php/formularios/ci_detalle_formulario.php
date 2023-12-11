<?php
require_once 'NumeroALetras.php';
require_once toba::proyecto()->get_path().'/php/datos/dt_dependencia.php';

class ci_detalle_formulario extends formularios_abm_ci
{
    protected $s__mostrar_i;
    protected $datos;
    //protected $s__monto;
    protected $s__mostrar_m;
    protected $nombre_tabla='formulario'; 
    
   
    function get_condiciones(){//dependiendo si es una secretaria o no trae las condiciones
        $form=$this->controlador()->dep('datos')->tabla('formulario')->get();
        return $this->controlador()->dep('datos')->tabla('condicion_venta')->get_condiciones($form['id_form']);
    }

    function get_grupos_fuente(){
        $form=$this->controlador()->dep('datos')->tabla('formulario')->get();
        return $this->controlador()->dep('datos')->tabla('origen_ingreso')->get_grupos_fuente($form['id_origen_recurso']);
    }
    function get_categorias(){
        $form=$this->controlador()->dep('datos')->tabla('formulario')->get();
        return $this->controlador()->dep('datos')->tabla('macheo_categ_programa')->get_categorias($form['id_programa']);
    }
    function get_actividades($id_categ=null){
        $where=" WHERE 1=1 ";
        if(isset($id_categ)){
            $where.=" and id_categ=".$id_categ;
        }
        $form=$this->controlador()->dep('datos')->tabla('formulario')->get();
        
        $sql="select * from actividad"
                . $where." and id_dependencia='".$form['id_dependencia']."' and id_programa=".$form['id_programa'];
        return toba::db('formularios')->consultar($sql);
    }

    function get_opciones(){//si el punto de venta es ficticio entonces la unica opcion es no facturar
       $bandera=true;
       $form=$this->controlador()->dep('datos')->tabla('formulario')->get();
        
       if($form['id_punto_venta']<=0){//punto de venta ficticio
           $bandera=false;
       }
       if($bandera){//si o si tiene que ingresar numero de comprobante
            $ar=array();
            $ar[0]=array('descripcion'=>'SI');
       } else{
           $ar=array();
           $ar[0]=array('descripcion'=>'NO');
       }
        return $ar;
    }
    
    function get_monto($id_comprobante){
        return $this->controlador()->dep('datos')->tabla('comprobante')->get_monto($id_comprobante);
    }
    //trae el listado de comprobantes en funcion al punto de venta y año de cobro
    //si lo cobro en 2019 es porque lo facturo en 2019 o en 2018
    function get_comprobantes($corresponde){
        $form=$this->controlador()->dep('datos')->tabla('formulario')->get();
        if ($this->controlador()->dep('datos')->tabla('item')->esta_cargada()) {//si el item esta cargado y tiene comprobante entonces lo agrego al desplegable
            $item=$this->controlador()->dep('datos')->tabla('item')->get();
            if(isset($item['id_comprobante'])){
                $id_comprob=$item['id_comprobante'];
            }else{
                $id_comprob=0;
            }
            
        }else{$id_comprob=0;}
        return $this->controlador()->dep('datos')->tabla('comprobante')->get_comprobantes($form['id_punto_venta'],$form['ano_cobro'],$id_comprob);
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
           $datos['anio']=substr($datos['nro_expediente'],9,4);
           if($datos['ingresa_fondo_central']){
               $datos['ingresa_fondo']='SI';
           }else{
               $datos['ingresa_fondo']='NO';
              // $this->pantalla()->tab("pant_modalidad")->desactivar();
           }
           $form->set_datos($datos);
        }//else{
          //  $this->pantalla()->tab("pant_detalle")->desactivar();
          //  $this->pantalla()->tab("pant_modalidad")->desactivar();
        //}     
        $this->s__mostrar_i=0;
    }
    function get_anio_libro_abierto(){
         $lib=$this->controlador()->dep('datos')->tabla('libro_ingreso')->get_libros_abiertos();
         if(count($lib)>0){
             return $lib[0]['anio'];
         }
    }
    function evt__form_inicial__alta($datos)
    {     
        $datos['estado']='I';
        $datos['fecha_creacion']=date('d/m/Y');
        $datos['pasado_pilaga']=false;
        $datos['check_presupuesto']=false;
        $band=dt_dependencia::es_secretaria($datos['id_dependencia']);
        if($band){//si es secretaria siempre es true
            $datos['ingresa_fondo_central']=true;
        }else{
            if($datos['id_origen_recurso']==1 and $datos['id_programa']==40 ){//f12 y programa 40
                $datos['ingresa_fondo_central']=false;
            }else{//f12 y programa de grado
                if($datos['id_origen_recurso']==1 and (($datos['id_programa']==16 or $datos['id_programa']==17 or $datos['id_programa']==18 or $datos['id_programa']==19 or $datos['id_programa']==20 or $datos['id_programa']==21 or $datos['id_programa']==22 or $datos['id_programa']==23 or $datos['id_programa']==24 or $datos['id_programa']==25 or $datos['id_programa']==26 or $datos['id_programa']==27 or $datos['id_programa']==28 or $datos['id_programa']==33 or $datos['id_programa']==34 or $datos['id_programa']==39)) ){//f12
                    $datos['ingresa_fondo_central']=false;
                  }else{//f12
                      if($datos['id_origen_recurso']==1 and $datos['id_programa']==35){
                          $datos['ingresa_fondo_central']=true;
                      }else{
                         if($datos['ingresa_fondo']=='NO'){
                            $datos['ingresa_fondo_central']=false;
                         }else{
                            $datos['ingresa_fondo_central']=true;
                          } 
                      }
                  }   
            }
        }
        $this->controlador()->dep('datos')->tabla('formulario')->set($datos);
        $this->controlador()->dep('datos')->tabla('formulario')->sincronizar();
        $form=$this->controlador()->dep('datos')->tabla('formulario')->get();
        $elem['id_form']=$form['id_form'];
        $this->controlador()->dep('datos')->tabla('formulario')->cargar($elem);
        //envio de mail cuando tilda "Destino Sueldos"
        
        if($datos['destino_sueldo']==1){
            $asunto =utf8_decode('Formulario de Ingresos - Destino Sueldo ');
            $cuerpo_mail = utf8_decode('El formulario id '.$form['id_form'].' ha sido ingresado en el sistema "Formulario de Ingresos" con tilde Destino Sueldo');
            toba::instancia()->get_db()->abrir_transaccion();
                try {
                    $mail = new toba_mail('cynthia.huenuhueque@central.uncoma.edu.ar', $asunto, $cuerpo_mail);
                    $mail->set_html(true);
                    $mail->enviar();
                    $mail = new toba_mail('rodrigo.pesce@fain.uncoma.edu.ar', $asunto, $cuerpo_mail);
                    $mail->set_html(true);
                    $mail->enviar(); 
                    toba::notificacion()->agregar(utf8_decode('Se ha enviado mail a Presupuesto notificando el alta de un formulario con destino sueldos'), 'info');
                    toba::instancia()->get_db()->cerrar_transaccion();                    
                } catch (toba_error $e) {
                        toba::instancia()->get_db()->abortar_transaccion();
                        toba::logger()->debug('Proceso de envio de random a cuenta: '. $e->getMessage());
                        throw new toba_error($e->getMessage());
                        throw new toba_error('Se produjo un error en el proceso de envio del correo, por favor contactese con un administrador del sistema.');
                }
        }
        
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
            $band=dt_dependencia::es_secretaria($datos['id_dependencia']);
            if($band){//si es dependencia siempre es true
                $datos['ingresa_fondo_central']=true;
            }else{
              if($datos['id_origen_recurso']==1 and $datos['id_programa']==40 ){//f12 y programa 40
                    $datos['ingresa_fondo_central']=false;
                }else{//f12 y programa de grado
                    if($datos['id_origen_recurso']==1 and (($datos['id_programa']==16 or $datos['id_programa']==17 or $datos['id_programa']==18 or $datos['id_programa']==19 or $datos['id_programa']==20 or $datos['id_programa']==21 or $datos['id_programa']==22 or $datos['id_programa']==23 or $datos['id_programa']==24 or $datos['id_programa']==25 or $datos['id_programa']==26 or $datos['id_programa']==27 or $datos['id_programa']==28 or $datos['id_programa']==33 or $datos['id_programa']==34 or $datos['id_programa']==39)) ){//f12
                        $datos['ingresa_fondo_central']=false;
                      }else{//f12
                          if($datos['id_origen_recurso']==1 and $datos['id_programa']==35){
                              $datos['ingresa_fondo_central']=true;
                          }else{
                             if($datos['ingresa_fondo']=='NO'){
                                $datos['ingresa_fondo_central']=false;
                             }else{
                                $datos['ingresa_fondo_central']=true;
                              } 
                          }
                      }   
                }
            }
            if($form['id_origen_recurso']<>$datos['id_origen_recurso'] or $form['id_punto_venta']<>$datos['id_punto_venta'] or $form['id_programa']<>$datos['id_programa'] or $form['ano_cobro']<>$datos['ano_cobro']){
                $bandera=$this->controlador()->dep('datos')->tabla('formulario')->tiene_items($form['id_form']);
                if(!$bandera){
                    $this->controlador()->dep('datos')->tabla('formulario')->set($datos);
                    $this->controlador()->dep('datos')->tabla('formulario')->sincronizar();
                }else{
                    toba::notificacion()->agregar('No puede cambiar Origen del Recurso, Punto de Venta, Programa o mes/año de cobro porque el formulario tiene items. Elimine los items y luego modifique.', 'info');  
                }
            }else{          
                $this->controlador()->dep('datos')->tabla('formulario')->set($datos);
                $this->controlador()->dep('datos')->tabla('formulario')->sincronizar();
                toba::notificacion()->agregar('Los datos se han guardado correctamente', 'info');  
            }
        }else{
          toba::notificacion()->agregar('El formulario no puede ser modificado', 'info');   
        }
    }
    function evt__form_inicial__modif($datos)//boton para finanzas
    {
        $datos2=array();
        $form=$this->controlador()->dep('datos')->tabla('formulario')->get();
        if($form['estado']=='E' or $form['estado']=='A' or $form['estado']=='R' or $form['estado']=='N') {//puede cambiar el estado y la observacion
            if($datos['estado']<>$form['estado']){//si cambia el estado
               
                if($datos['estado']=='A' or $datos['estado']=='R' or $datos['estado']=='N'){
                    if($datos['estado']=='A'){//si lo aprueba                        
                        if(trim($form['id_dependencia'])=='FAIN' and ($form['id_origen_recurso']==1 or $form['id_origen_recurso']==3)){//si es FAIN F12 o F14 no necesita el check_presupuesto 
                            $datos2['estado']=$datos['estado'];
                            $datos2['observacionfinanzas']=$datos['observacionfinanzas'];
                            $mensaje=' Datos guardados correctamente';
                             
                       } else{
                            if($form['check_presupuesto']==1){
                                $datos2['estado']=$datos['estado'];
                                $datos2['observacionfinanzas']=$datos['observacionfinanzas'];
                                $mensaje=' Datos guardados correctamente';
                            }else{
                                $mensaje=' Debe tener el check de presupuesto para aprobar';
                            }  
                       }
                        
                    }else{//para estados R o N no hace falta chequear el check presupuesto
                        $datos2['estado']=$datos['estado'];
                        $datos2['observacionfinanzas']=$datos['observacionfinanzas'];
                        $mensaje=' Datos guardados correctamente';
                    }
                    
                    if($datos['estado']=='N'){//si anula tambien anula el recibo si lo tuviese
                        $this->controlador()->dep('datos')->tabla('formulario')->anular_recibo($form['id_form']);
                    }
                   
                }else{
                    $mensaje=' No es posible cambiar el estado.';
                }
            }else{
                $datos2['observacionfinanzas']=$datos['observacionfinanzas'];
            }
            //$mensaje='Recuerde que solo puede modificar el nro de expediente. Datos guardados correctamente';
        }else{
            $mensaje=' Recuerde que solo puede modificar estado y observacion si el formulario se encuentra en estado E';
        }
        //print_r($datos2);
        $datos2['nro_expediente']=$datos['nro_expediente'];//que el expediente lo pueda cambiar siempre
        $this->controlador()->dep('datos')->tabla('formulario')->set($datos2);
        $this->controlador()->dep('datos')->tabla('formulario')->sincronizar();
        toba::notificacion()->agregar($mensaje, 'info');  
        
    }
    
    function evt__form_inicial__modifp($datos)//boton para presupuesto
    {
        $datos2=array();
        $form=$this->controlador()->dep('datos')->tabla('formulario')->get();
        if($form['estado']=='E'){//si esta en E
            $datos2['check_presupuesto']=$datos['check_presupuesto'];     
        }
        //la observacion la puede cambiar en cualquier momento
        $datos2['observacionpresupuesto']=$datos['observacionpresupuesto'];
        $this->controlador()->dep('datos')->tabla('formulario')->set($datos2);
        $this->controlador()->dep('datos')->tabla('formulario')->sincronizar();
        toba::notificacion()->agregar('Guardado correctamente. Recuerde que solo en estado E puede tocar el check', 'info');  
    }
    
    function evt__form_inicial__cancelar($datos)
    {
        $this->controlador()->set_pantalla('pant_seleccion');
        $this->controlador()->dep('datos')->tabla('formulario')->resetear();
        
    }
    function evt__form_inicial__anular($datos)
    {
        if ($this->controlador()->dep('datos')->tabla('formulario')->esta_cargada()) {
            $datos2['estado']='N';
            $form=$this->controlador()->dep('datos')->tabla('formulario')->get();
            $this->controlador()->dep('datos')->tabla('formulario')->anular_recibo($form['id_form']);
            $this->controlador()->dep('datos')->tabla('formulario')->set($datos2);
            $this->controlador()->dep('datos')->tabla('formulario')->sincronizar();
            $this->controlador()->set_pantalla('pant_seleccion');
            toba::notificacion()->agregar('El formulario ha sido anulado', 'info');  
            $this->controlador()->dep('datos')->tabla('formulario')->resetear();
          }
        
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
                   //$this->s__monto=$datos['monto'];
                   if(!isset($datos['id_comprobante'])){//sino tiene valor
                       $datos['corresponde_factura']='NO';
                       //$datos['id_comprobante']=0;   //no corresponde!!
                       //print_r($this->s__monto);//esto lo coloco para que no se autocomplete el monto en 0 con javascript
                   }else{
                       $datos['corresponde_factura']='SI';
                   }
                   $datos['nro_cuil']=$datos['cuil1'].str_pad($datos['cuil'], 8, '0', STR_PAD_LEFT).$datos['cuil2'];
                   //print_r($datos);
                   $form->set_datos($datos);
               }   
               $f=$this->controlador()->dep('datos')->tabla('formulario')->get();
               if($f['id_punto_venta']>0){//esto para que no me modifique el monto que trae del comprobante
                    $form->set_solo_lectura('monto', true);        
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
                 if(isset($datos['nro_cuil'])){
                    $datos['cuil1']=substr($datos['nro_cuil'], 0, 2);
                    $datos['cuil']=substr($datos['nro_cuil'], 2, 8);
                    $datos['cuil2']=substr($datos['nro_cuil'], 10, 1);
                }
                 if($datos['corresponde_factura']=='SI'){
                    $total=$this->controlador()->dep('datos')->tabla('comprobante')->get_monto($datos['id_comprobante']);
                    if($total<>$datos['monto']){
                        $datos['monto']=$total;
                    }
                }
                $datos['detalle']=str_replace('#', ' ', $datos['detalle']);
                $this->controlador()->dep('datos')->tabla('item')->set($datos);
                $this->controlador()->dep('datos')->tabla('item')->sincronizar();
                $this->controlador()->dep('datos')->tabla('item')->resetear();
                $this->s__mostrar_i=0;
            }else{
                throw new toba_error('No puede mezclar categorias');
                //toba::notificacion()->agregar('No puede mezclar categorias', 'error');   
            }
	}
       	
        /**
	 * Metodo invocado desde JS para 'calcular' el nuevo importe
	 */
	function ajax__calcular($parametros, toba_ajax_respuesta $respuesta)
	{
           if(is_numeric($parametros['id_comprobante'])){
                 $total=$this->controlador()->dep('datos')->tabla('comprobante')->get_monto($parametros['id_comprobante']);
                 $respuesta->set($total); 
           }else{//no hay comprobante
                 if($this->controlador()->dep('datos')->tabla('item')->esta_cargada()){
                    $datos=$this->controlador()->dep('datos')->tabla('item')->get();
                    $respuesta->set($datos['monto']); 
                 }
             }
          
	}//esta funcion es llamada desde javascript
         
         /**
	 * Metodo invocado desde JS para 'calcular_detalle' 
	 */
	function ajax__calcular_detalle($parametros, toba_ajax_respuesta $respuesta)
	{
            if(is_numeric($parametros['id_comprobante'])){
                $det=$this->controlador()->dep('datos')->tabla('comprobante')->get_detalle($parametros['id_comprobante']);
                $datos=$this->controlador()->dep('datos')->tabla('item')->get();
                if(isset($datos)){//si ya tenia algo en detalle entonces muestra lo que tenia
                    $respuesta->set($datos['detalle']); 
                }else{
                    $respuesta->set($det); 
                }
           }else{//no hay comprobante
                $datos=$this->controlador()->dep('datos')->tabla('item')->get();
                $respuesta->set($datos['detalle']); 
             }
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
                if(isset($datos['nro_cuil'])){
                    $datos['cuil1']=substr($datos['nro_cuil'], 0, 2);
                    $datos['cuil']=substr($datos['nro_cuil'], 2, 8);
                    $datos['cuil2']=substr($datos['nro_cuil'], 10, 1);    
                }
                
                if($datos['corresponde_factura']=='SI'){
                    $total=$this->controlador()->dep('datos')->tabla('comprobante')->get_monto($datos['id_comprobante']);
                    if($total<>$datos['monto']){
                        $datos['monto']=$total;
                    }
                }
                $datos['detalle']=str_replace('#', ' ', $datos['detalle']);
                $this->controlador()->dep('datos')->tabla('item')->set($datos);
                $this->controlador()->dep('datos')->tabla('item')->sincronizar();
                toba::notificacion()->agregar('El item se ha modificado correctamente', 'info'); 
                $this->s__mostrar_i=0;  
            }else{
                 throw new toba_error('No puede mezclar categorias');
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
           if($form['estado']<>'A' and $form['estado']<>'T' and $form['estado']<>'P'){//el boton imprimir solo aparece si el formualrio esta aprobado (T o P para que pueda reimprimir)
                $cuadro->eliminar_evento('imprimir');
            }
           if($form['disponibilidad']!=53){//solo si la disponibilidad esta en la cuenta de Mercado Pago muestra las columnas de comision mp y monto/comision mp
               $columnas=array('comision_mp','resta');
               $cuadro->eliminar_columnas($columnas);
           }
           //print_r($this->datos[0]['id_origen_recurso']);
           if(count($this->datos)>0){
               switch ($this->datos[0]['id_origen_recurso']) {
                   case 1://si es F12
                       $columnas=array('organismo','nro_resol','proviene_descrip');
                       $cuadro->eliminar_columnas($columnas);
                       break;
                   case 2://si es F13
                       $columnas=array('proviene_descrip');
                       $cuadro->eliminar_columnas($columnas);
                       break;
                    case 3://si es F14
                        $columnas=array('nro_resol','organismo');
                        $cuadro->eliminar_columnas($columnas);
                       break;
                   case 4://f21
                       $columnas=array('nro_resol','proviene_descrip');
                       $cuadro->eliminar_columnas($columnas);
                       break;
                   case 5: //f22
                       $columnas=array('nro_resol','proviene_descrip');
                       $cuadro->eliminar_columnas($columnas);
                       break;
                    case 6://si es F11
                       $columnas=array('proviene_descrip');
                       $cuadro->eliminar_columnas($columnas);
                       break;
                   default:
                       break;
                   //si es F12 debo colocar  total bruto, deduccion y total neto
               }
          }
         if($form['id_punto_venta']<0){
            $columnas=array('nro_factura','receptor','denom_receptor');
            $cuadro->eliminar_columnas($columnas);
         }
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
	//---- modalidad ---------------------------------------------------------------
	//-----------------------------------------------------------------------------------

        function conf__form_modalidad(toba_ei_formulario $form)
	{
            if($this->s__mostrar_m==1){
               $this->dep('form_modalidad')->descolapsar();
               if($this->controlador()->dep('datos')->tabla('modalidad_pago')->esta_cargada()){
                   $datos=$this->controlador()->dep('datos')->tabla('modalidad_pago')->get();
                   if(!empty($datos['archivo_trans'])){//no esta vacia
                        $fechaHora = idate("Y").idate("m").idate("d").idate("H").idate("i").idate("s");
                        $nomb_ft="/formularios/1.0/adjuntos/".$datos['archivo_trans'];
                        $nomb_ft.="?v=".$fechaHora;
                        $datos['imagen_vista_previa_t'] = "<a target='_blank' href='{$nomb_ft}' >comprob transf</a>";
                    }
                   $datos['nro_cuil']=$datos['cuil1'].str_pad($datos['cuil'], 8, '0', STR_PAD_LEFT).$datos['cuil2'];
                   $form->set_datos($datos);
               }           
            }else{
                $this->dep('form_modalidad')->colapsar();
            }
	}
        function evt__form_modalidad__alta($datos)
	{
            $bandera=true;
            $adj=false;
            $form=$this->controlador()->dep('datos')->tabla('formulario')->get();
            if(isset($datos['nro_cheque'])){//si tiene cheque
                $bandera=$this->controlador()->dep('datos')->tabla('modalidad_pago')->no_repite_cheque($datos['nro_cheque']);
            }
            if($bandera){
                $repetido=array();
                if(isset($datos['nro_transferencia'])){
                    $repetido=$this->controlador()->dep('datos')->tabla('modalidad_pago')->no_repite_transferencia($datos['nro_transferencia']);
                }
                if(count($repetido)==0){//no se repite
                    $datos['id_form']=$form['id_form'];
                    if(isset($datos['nro_cuil'])){
                        $datos['cuil1']=substr($datos['nro_cuil'], 0, 2);
                        $datos['cuil']=substr($datos['nro_cuil'], 2, 8);
                        $datos['cuil2']=substr($datos['nro_cuil'], 10, 1);
                    }
                    if (isset($datos['archivo_trans'])) {
                        $archivo=$datos['archivo_trans'];
                        $datos['archivo_trans']='';
                        $adj=true;
                    }
                    $this->controlador()->dep('datos')->tabla('modalidad_pago')->set($datos);
                    $this->controlador()->dep('datos')->tabla('modalidad_pago')->sincronizar();
                    
                    if($adj){
                        $modalidad=$this->controlador()->dep('datos')->tabla('modalidad_pago')->get();
                        $nombre_ca=$modalidad['id_form']."_comprob_transf_".$modalidad['id_mod'].".pdf";
                        $destino_ca=toba::proyecto()->get_path()."/www/adjuntos/".$nombre_ca;
                        if(move_uploaded_file($archivo['tmp_name'], $destino_ca)){//mueve un archivo a una nueva direccion, retorna true cuando lo hace y falso en caso de que no
                               $valor=strval($nombre_ca);      
                        }
                        $this->controlador()->dep('datos')->tabla('modalidad_pago')->cambiar_adj($modalidad['id_mod'],$valor);
                        
                    }
                    
                    $this->controlador()->dep('datos')->tabla('modalidad_pago')->resetear();
                    $this->s__mostrar_m=0;
                    
                }else{
                    throw new toba_error('El numero de transferencia se repite en: id_form '.$repetido[0]['id_form'].' id_mod '.$repetido[0]['id_mod']);
                }
            }else{
                throw new toba_error('El numero de cheque se repite');
            }
            
	}
        function evt__form_modalidad__baja()
	{
            $datos=$this->controlador()->dep('datos')->tabla('modalidad_pago')->get();
            if(isset($datos['archivo_trans'])){//si tiene archivo lo borra
                $nomb_ft=toba::proyecto()->get_path()."/www/adjuntos/".$datos['archivo_trans'];
                unlink($nomb_ft);
            }
            $this->controlador()->dep('datos')->tabla('modalidad_pago')->eliminar_todo();
            $this->controlador()->dep('datos')->tabla('modalidad_pago')->resetear();
            toba::notificacion()->agregar('Se ha eliminado correctamente', 'info');   
            $this->s__mostrar_m=0;
	}
        
        function evt__form_modalidad__cancelar()
        {
            $this->controlador()->dep('datos')->tabla('modalidad_pago')->resetear();
            $this->s__mostrar_m=0;
        }

	function evt__form_modalidad__modificacion($datos)
        {
            $bandera=true;
            $modalidad=$this->controlador()->dep('datos')->tabla('modalidad_pago')->get();
            if(isset($datos['nro_cheque']) and $modalidad['nro_cheque']<>$datos['nro_cheque']){
                $bandera=$this->controlador()->dep('datos')->tabla('modalidad_pago')->no_repite_cheque($datos['nro_cheque']);
            }
            if($bandera){
                $repetido=array();
                if(isset($datos['nro_transferencia']) and $modalidad['nro_transferencia']<>$datos['nro_transferencia']){
                    $repetido=$this->controlador()->dep('datos')->tabla('modalidad_pago')->no_repite_transferencia_modif($datos['nro_transferencia'],$modalidad['id_mod']);
                }
                
                if(count($repetido)==0){//no se repite
                    switch ($datos['id_condicion_venta']) {
                        case 1://efectivo
                            $datos['nro_cheque']=null;    
                            $datos['id_banco']=null;    
                            $datos['fecha_emision_cheque']=null;    
                            $datos['cuenta_a_acreditar']=null;    
                            $datos['nro_transferencia']=null;    
                            $datos['nro_cuil']=null;    
                            $datos['archivo_trans']=null; 
                            $nombre_ca=null;
                            break;
                        case 2://cheque
                            $datos['nro_cheque']=null;    
                            $datos['id_banco']=null;    
                            $datos['fecha_emision_cheque']=null;    
                            $nombre_ca=null;
                            break;
                        case 3://transferencia
                            $datos['nro_cheque']=null;    
                            $datos['id_banco']=null;    
                            $datos['fecha_emision_cheque']=null;
                            $nombre_ca=$modalidad['archivo_trans'];
                            break;
                        default:
                            break;
                    }
                    //borra el archivo de transferencia cuando cambia la condicion
                    if(($datos['id_condicion_venta']==1 or $datos['id_condicion_venta']==2) and isset($modalidad['archivo_trans'])){
                        if(isset($modalidad['archivo_trans'])){//si tiene archivo lo borra
                            $nomb_ft=toba::proyecto()->get_path()."/www/adjuntos/".$modalidad['archivo_trans'];
                            unlink($nomb_ft);
                        }
                    }
                   
                    if (isset($datos['archivo_trans'])) {//esta modificando el comprobante
                            $nombre_ca=$modalidad['id_form']."_comprob_transf_".$modalidad['id_mod'].".pdf";
                            $destino_ca=toba::proyecto()->get_path()."/www/adjuntos/".$nombre_ca;
                            move_uploaded_file($datos['archivo_trans']['tmp_name'], $destino_ca);//mueve un archivo a una nueva direccion, retorna true cuando lo hace y falso en caso de que no                       
                    }
                    if(isset($datos['nro_cuil'])){
                        $datos['cuil1']=substr($datos['nro_cuil'], 0, 2);
                        $datos['cuil']=substr($datos['nro_cuil'], 2, 8);
                        $datos['cuil2']=substr($datos['nro_cuil'], 10, 1);
                    }
                    $datos['archivo_trans']=strval($nombre_ca);//esto xq sino deja en nulo el campo archivo transferencia
                    $this->controlador()->dep('datos')->tabla('modalidad_pago')->set($datos);
                    $this->controlador()->dep('datos')->tabla('modalidad_pago')->sincronizar();
                    $this->controlador()->dep('datos')->tabla('modalidad_pago')->resetear();
                    toba::notificacion()->agregar('Se ha modificado correctamente', 'info');   
                    $this->s__mostrar_m=0;
                }else{
                    throw new toba_error('El numero de transferencia se repite en:'.$repetido[0]['id_form'].' id_mod '.$repetido[0]['id_mod']);   
                }
                
            }else{
              throw new toba_error('El numero de cheque se repite');   
            }
            
        }
        //-----------------------------------------------------------------------------------
	//---- cuadro modalidad ----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------
        function conf__cuadrom(toba_ei_cuadro $cuadro)
        {
           $form=$this->controlador()->dep('datos')->tabla('formulario')->get();
           return $this->controlador()->dep('datos')->tabla('modalidad_pago')->get_listado($form['id_form']);
        }
        function evt__cuadrom__seleccion($datos)
	{
            if ($this->controlador()->dep('datos')->tabla('formulario')->esta_cargada()) {
               $form = $this->controlador()->dep('datos')->tabla('formulario')->get();
               if($form['estado']=='I' or $form['estado']=='R'){
                    $this->controlador()->dep('datos')->tabla('modalidad_pago')->cargar($datos);
                    $this->s__mostrar_m=1;  
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
        //alta de un nueva modalidad
	function evt__altam()
	{
            $form=$this->controlador()->dep('datos')->tabla('formulario')->get();
            if($form['estado']=='I' or $form['estado']=='R'){
                $this->controlador()->dep('datos')->tabla('modalidad_pago')->resetear();
                $this->s__mostrar_m=1;
            }else{
                toba::notificacion()->agregar('Ya no puede agregar al formulario. Verifique el estado del formulario', 'info'); 
            }
	}
        function evt__rango(){
            $form=$this->controlador()->dep('datos')->tabla('formulario')->get();
            if($form['estado']=='I' or $form['estado']=='R' ){
                if($form['id_punto_venta']<=0){//punto ficticio
                    toba::notificacion()->agregar('No puede levantar un rango de comprobantes para un punto de venta ficticio', 'info');     
                }else{
                    $parametros['id_form']=$form['id_form'];
                    //print_r($parametros);
                    toba::vinculador()->navegar_a('formularios',3838,$parametros);    
                }
            }else{
                toba::notificacion()->agregar('El formulario debe estar en estado Inicial o Rechazado para poder agregar un rango de comprobantes', 'info'); 
            }
        }
        
        function evt__enviar()
	{
            $form=$this->controlador()->dep('datos')->tabla('formulario')->get();
            if($form['estado']=='I' or $form['estado']=='R' ){
                $band=$this->controlador()->dep('datos')->tabla('formulario')->tiene_items($form['id_form']);
                if($band){
                    $negativo=$this->controlador()->dep('datos')->tabla('formulario')->total_negativo($form['id_form']);
                    if(!$negativo){
                        $cerrado=$this->controlador()->dep('datos')->tabla('formulario')->esta_en_libro_cerrado($form['id_form']);
                        if($cerrado==1){//si el libro al que corresponde el formulario esta cerrado
                            toba::notificacion()->agregar('No es posible enviar, el libro esta cerrado', 'info');   
                        }else{
                            $band=$this->controlador()->dep('datos')->tabla('comprobante')->tiene_comprob_repetidos($form['id_form']);
                            if(!$band){
                                $band=$this->controlador()->dep('datos')->tabla('modalidad_pago')->puede_enviar($form['id_form']);
                                if($band){
                                    $band=$this->controlador()->dep('datos')->tabla('formulario')->puede_enviar($form['id_form']);
                                    if($band){
                                        $datos['estado']='E';
                                        $datos['fecha_envio']=date('d/m/Y');
                                        $this->controlador()->dep('datos')->tabla('formulario')->set($datos);
                                        $this->controlador()->dep('datos')->tabla('formulario')->sincronizar();
                                        $this->controlador()->dep('datos')->tabla('formulario')->resetear();
                                        $this->controlador()->dep('datos')->tabla('modalidad_pago')->resetear();
                                        $this->s__mostrar_m=0;
                                        $this->controlador()->set_pantalla('pant_seleccion');
                                        toba::notificacion()->agregar('El formulario ha sido enviado correctamente', 'info');   
                                    }else{
                                        toba::notificacion()->agregar('No es posible enviar, verifique la Modalidad de Ingreso', 'info');   
                                    } 
                                }else{
                                      toba::notificacion()->agregar('No es posible enviar, falta comprobante en Modalidad de Ingreso', 'info');   
                                } 
                            }else{
                                toba::notificacion()->agregar('No es posible enviar, tiene comprobantes repetidos', 'info');   
                            }
                          }
                    }else{
                        toba::notificacion()->agregar('No es posible enviar. No se puede declarar formularios en negativo. Los mismos son declarativos de ingresos.', 'info');   
                    }
                    
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
        function evt__volver()
        {
            $this->controlador()->set_pantalla('pant_seleccion');
            $this->controlador()->dep('datos')->tabla('formulario')->resetear();
            $this->controlador()->dep('datos')->tabla('item')->resetear();
            $this->controlador()->dep('datos')->tabla('modalidad_pago')->resetear();
        }
        function puntos_cm ($medida, $resolucion=72)
        {
           //// 2.54 cm / pulgada
           return ($medida/(2.54))*$resolucion;
        }
        //generacion recibo
//        function vista_pdf(toba_vista_pdf $salida){  
//         
//         $form=$this->controlador()->dep('datos')->tabla('formulario')->get();
//         if($form['estado']=='T'){
//            //llama a una funcion para generar el recibo. Si ya lo tiene retorna los datos, sino lo tiene lo genere y sino corresponde
//            $sql="select genera_recibo(".$form['id_form'].")";
//            $resul=toba::db('formularios')->consultar($sql);
//            if($resul[0]['genera_recibo']==1){//corresponde generar recibo
//                //recupero los datos del recibo para mostrarlos
//                $recibo=$this->controlador()->dep('datos')->tabla('formulario')->get_recibo($form['id_form']);   
//                $salida->set_nombre_archivo("Recibo_".$recibo[0]['id_recibo'].".pdf");
//                //recuperamos el objteo ezPDF para agregar la cabecera y el pie de página 
//                $salida->set_papel_orientacion('portrait');
//                $salida->inicializar();
//                $pdf = $salida->get_pdf();
//                $pdf->ezSetMargins(80, 50, 30, 30);//top,bottom,left,righ
//                //Configuramos el pie de página. El mismo, tendra el número de página centrado en la página y la fecha ubicada a la derecha. 
//                
//                $titulo="   ";
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
//               $datos2=array();
//               $num=utf8_decode('RECIBO N°: '.$recibo[0]['id_recibo']);
//               $datos2[0]=array('col1'=>'','col2'=>'');
//               $datos2[1]=array('col1'=>'','col2'=>'');//aqui numero de recibo
//               $datos2[2]=array('col1'=>'UNIVERSIDAD NACIONAL DEL COMAHUE','col2'=>$num);
//               $datos2[3]=array('col1'=>'','col2'=>'');
//               $datos2[4]=array('col1'=>'','col2'=>'');
//                         
//               $opc=array('showHeadings' => 0,'showLines'=>1,'shaded'=>0,'width'=>500,'colGap' => 10,'cols' =>array('col1'=>array('width'=>300,'justification'=>'right'),'col2'=>array('width'=>200,'justification'=>'right')));
//               //colocamos el cursor a unos 27 cm del final de la pagina
//               $pdf->ezSetY($this->puntos_cm(27));
//               $pdf->ezTable($datos2,array('col1'=>'','col2'=>''),'',$opc);
//              
//               $datos=array();
//               $m=date("m",strtotime($recibo[0]['fecha']));
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
//               $dia=date("d",strtotime($recibo[0]['fecha']));
//               $anio=date("Y",strtotime($recibo[0]['fecha']));
//               $monto_letras= $this->transforma($recibo[0]['monto']);
//               $texto=utf8_decode('Recibí de ').trim($recibo[0]['recibi_de']).' la suma de pesos '. utf8_decode($monto_letras).' en concepto de '.$recibo[0]['concepto'];
//               $texto2=utf8_decode('Neuquén, ').$dia.' de'.$mes.$anio;
//               
//               $datos[0]=array('col1'=>'');
//               $datos[1]=array('col1'=>$texto);
//               $datos[2]=array('col1'=>$texto2);
//               //number_format($recibo['0']['monto'],2,'.','')
//               //para que muestre el monto con 2 decimales
//               $datos[3]=array('col1'=>'SON $ '.number_format($recibo[0]['monto'],2,',','.'));
//               
//               $pdf->ezTable($datos,array('col1'=>''),'',$opciones);
//
//               //colocamos el cursor en la mitad
//               $pdf->ezSetY($this->puntos_cm(14));
//               $pdf->ezTable($datos2,array('col1'=>'','col2'=>''),'',$opc);
//               $pdf->ezTable($datos,array('col1'=>''),'',$opciones);
//               $imagen = toba::proyecto()->get_path().'/www/img/logo-unc.jpg';
//               //$pdf->addJpegFromFile($imagen, 55, 690, 70, 65);//funciona
//               //donde 'x' e 'y' son las coordenadas de la esquina inferior izquierda de la imagen.
//               $pdf->addJpegFromFile($imagen, $this->puntos_cm(2), $this->puntos_cm(24.5), 70, 65);
//               $pdf->addJpegFromFile($imagen, $this->puntos_cm(2), $this->puntos_cm(11.5), 70, 65);
//
//              }
//        }
//        }
            
	function conf()
	{
            if ($this->controlador()->dep('datos')->tabla('formulario')->esta_cargada()) {
               $datos = $this->controlador()->dep('datos')->tabla('formulario')->get();
               if(!$datos['ingresa_fondo_central']){
                   $this->pantalla()->tab("pant_modalidad")->desactivar();
               } 
            }else{
                $this->pantalla()->tab("pant_detalle")->desactivar();
                $this->pantalla()->tab("pant_modalidad")->desactivar();
            }
	}

}
?>