<?php
class ci_formularios extends toba_ci
{
    protected $s__datos_filtro;
    protected $s__where;
    protected $s__columnas;
    
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
	//---- formulario -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------
	function conf__columnas(toba_ei_formulario $form)
	{
            $form->colapsar();
            $form->set_datos($this->s__columnas);    

	}
        function evt__columnas__modificacion($datos)
        {
            $this->s__columnas = $datos;
        }
	//-----------------------------------------------------------------------------------
	//---- cuadro -----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cuadro(toba_ei_cuadro $cuadro)
	{
            if (isset($this->s__where)) {
                if($this->s__columnas['disponibilidad']==0){
                        $c=array('disponibilidad');
                        $this->dep('cuadro')->eliminar_columnas($c); 
                }
                if($this->s__columnas['modalidad']==0){
                        $c=array('modalidad');
                        $this->dep('cuadro')->eliminar_columnas($c); 
                }
                if($this->s__columnas['desc_pv']==0){
                        $c=array('desc_pv');
                        $this->dep('cuadro')->eliminar_columnas($c); 
                }
                $cuadro->set_datos($this->dep('datos')->tabla('formulario')->get_listado_filtro($this->s__where));
            } 
//            else{
//                $cuadro->set_datos($this->dep('datos')->tabla('formulario')->get_listado_filtro());
//            }
	}

	function evt__cuadro__seleccion($datos)
	{
            $this->dep('datos')->tabla('formulario')->cargar($datos);
            $this->set_pantalla('pant_edicion');
	}
        function evt__cuadro__check($datos)
	{
            $mensaje=$this->dep('datos')->tabla('formulario')->pasado_pilaga($datos['id_form']);
            toba::notificacion()->agregar($mensaje, 'info');  
	}
	function evt__agregar()
	{
             $this->set_pantalla('pant_edicion');
	}
	//-----------------------------------------------------------------------------------
	//---- JAVASCRIPT -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function extender_objeto_js()
	{
		echo "
		//---- Eventos ---------------------------------------------
		
		{$this->objeto_js}.evt__agregar = function()
		{
		}
		";
	}
          
        function vista_pdf(toba_vista_pdf $salida){
            $form=$this->dep('datos')->tabla('formulario')->get();
            $resp=$this->dep('datos')->tabla('libro_ingreso')->esta_cerrado($form['ano_cobro']);
            if(!$resp){
                //recuperamos el objteo ezPDF para agregar la cabecera y el pie de página 
                $salida->set_papel_orientacion('landscape');
                $salida->inicializar();
                $pdf = $salida->get_pdf();//top,bottom,left,righ
                $pdf->ezSetMargins(75, 50, 20, 20);
                //Configuramos el pie de página. El mismo, tendra el número de página centrado en la página y la fecha ubicada a la derecha. 
                //Primero definimos la plantilla para el número de página.
                $formato = utf8_decode('Página {PAGENUM} de {TOTALPAGENUM}   ');
                $pdf->ezStartPageNumbers(100, 20, 8, 'left', utf8_d_seguro($formato), 1); 
                $usuario = toba::usuario()->get_nombre();
                    //Luego definimos la ubicación de la fecha en el pie de página.
                //$pdf->addText(500,20,8,'Generado por usuario: '.$usuario.' '.date('d/m/Y h:i:s a')); 
                $titulo="   ";
                $opciones = array(
                    'showLines'=>1,
                    'splitRows'=>0,
                    'rowGap' => 0,//, the space between the text and the row lines on each row
                   // 'lineCol' => (r,g,b) array,// defining the colour of the lines, default, black.
                    //'showLines'=>2,//coloca las lineas horizontales
                    //'showHeadings' => true,//muestra el nombre de las columnas
                    'titleFontSize' => 9,
                    'fontSize' => 10,
                    //'shadeCol' => array(1,1,1,1,1,1,1,1,1,1,1,1),
                    //'shadeCol' => array(0.1,0.1,0.1),//darle color a las filas intercaladamente
                    'outerLineThickness' => 0.7,
                    'innerLineThickness' => 0.7,
                    'xOrientation' => 'center',
                    'width' => 800//,
                   //'cols' =>array('col2'=>array('justification'=>'center') ,'col3'=>array('justification'=>'center'),'col4'=>array('justification'=>'center') ,'col5'=>array('justification'=>'center'),'col6'=>array('justification'=>'center') ,'col7'=>array('justification'=>'center') ,'col8'=>array('justification'=>'center'),'col9'=>array('justification'=>'center') ,'col10'=>array('justification'=>'center') ,'col11'=>array('justification'=>'center') ,'col12'=>array('justification'=>'center'),'col13'=>array('justification'=>'center') ,'col14'=>array('justification'=>'center') )
                    );

                   if($form['ingresa_fondo_central']==1){
                       $texto=utf8_decode('(Su presentación es obligatoria ante la Dirección de Tesorería)');
                       $datos_mod=$this->dep('datos')->tabla('modalidad_pago')->get_listado($form['id_form']);
                       $modalidad=array();
                       $j=0;
                       foreach ($datos_mod as $modal) {
                            $modalidad[$j]=array( 'col1'=>$modal['condicion'],'col2' => $modal['detalle'],'col3' =>number_format($modal['monto'],2,',','.'));
                            $j++;
                        }
                        $cols_mod=array('col1' => '<b>CONDICION</b>','col2' => '<b>DETALLE</b>','col3' => '<b>MONTO</b>');
                        $opc_mod=array('showLines'=>2,'shaded'=>0,'rowGap' => 3,'width'=>700,'cols'=>array('col1'=>array('width'=>90),'col2'=>array('width'=>520),'col3'=>array('width'=>90,'justification'=>'right')));
                   }else{
                       $texto=utf8_decode('(Su presentación es obligatoria ante la Dirección de Finanzas)');
                   }
                   //llama a una funcion para asignar el numero de entrada         
                   $sql="select asigna_numero_ingreso(".$form['id_form'].")";
                   $resul=toba::db('formularios')->consultar($sql);
                   $salida->set_nombre_archivo("Formulario".$resul[0]['asigna_numero_ingreso'].".pdf");
                   $datos_form=$this->dep('datos')->tabla('item')->get_listado($form['id_form']);
                   //print_r($datos_form);EXIT();
                   $datos=array();

                   $i=0;
                   //Configuración de Título.
                   $tit=$this->dep('datos')->tabla('formulario')->get_titulo($form['id_form']);
                   $dep=$this->dep('datos')->tabla('formulario')->get_dependencia($form['id_form']);
                   $disp=$this->dep('datos')->tabla('formulario')->get_disponibilidad($form['id_form']);
                   $prog=$this->dep('datos')->tabla('formulario')->get_desc_programa($form['id_form']);
                   $salida->titulo(utf8_d_seguro($tit)); 
                   $pdf->ezText($texto, 8, array('justification'=>'center'));
                   $pdf->ezText("\n\n", 10);
                   $pdf->ezText($dep, 10);//dependencia
                   if(isset($form['id_punto_venta'])){
                      if($form['id_punto_venta']<0){
                          $punto=0;
                      }else{
                          $punto=$form['id_punto_venta'];
                      }
                      $pdf->ezText('PUNTO DE VENTA: <b>'.$punto.'</b>', 10); 
                   }
                   $fec=date("d/m/Y",strtotime($form['fecha_creacion']));
                   $pdf->ezText('FECHA CREACION: '.$fec, 10);
                   $pdf->ezText('EXPEDIENTE: '.$form['nro_expediente'], 10);
                   $pdf->ezText($prog, 10);
                   //print_r($datos_form );exit;
                   foreach ($datos_form as $item) {
                        switch ($form['id_origen_recurso']){
                            case 1://f12
                                $datos[$i]=array( 'col2'=>$item['categ'],'col3' => $item['vinc'],'col4' => $item['nro_factura'],'col5' => $item['detalle'],'col6' => number_format($item['monto'],2,',','.'));
                                break;
                            case 2://f13
                               // $datos[$i]=array( 'col2'=>$item['nro_resol'],'col3' => $item['organismo'],'col4' => $item['nro_factura'],'col5' => $item['detalle'],'col6' => $item['condicion_venta'],'col7' => $item['condicion_venta2'],'col8' => number_format($item['monto'],2,',','.'));
                                 $datos[$i]=array( 'col2'=>$item['categ'],'col3' => $item['vinc'],'col4'=>$item['nro_resol'],'col5' => $item['organismo'],'col6' => $item['nro_factura'],'col7' => $item['detalle'],'col8' => number_format($item['monto'],2,',','.'));
                                break;
                            case 3://f14
                                $datos[$i]=array( 'col2'=>$item['categ'],'col3' => $item['vinc'],'col4'=>$item['proviene_descrip'],'col5' => $item['nro_factura'],'col6' => $item['detalle'],'col7' => number_format($item['monto'],2,',','.'));
                                break;
                            case 4://f21
                                $datos[$i]=array( 'col2'=>$item['categ'],'col3' => $item['vinc'],'col4' => $item['organismo'],'col5' => $item['nro_factura'],'col6' => $item['detalle'],'col7' => number_format($item['monto'],2,',','.'));
                                break;
                            case 5://f22
                                $datos[$i]=array( 'col2'=>$item['categ'],'col3' => $item['vinc'],'col4' => $item['organismo'],'col5' => $item['nro_factura'],'col6' => $item['detalle'],'col7' => number_format($item['monto'],2,',','.'));
                                break;
                        }
                       $i++;
                   }  
                  //print_r($datos);exit;
                    switch ($form['id_origen_recurso']) {
                        case 1://f12
                            $cat=utf8_decode('CATEGORÍA');
                            $vinc=utf8_decode('VINCULACIÓN');
                            $cols=array('col2'=>'<b>'.$cat.'</b>','col3' => '<b>'.$vinc.'</b>','col4' => '<b>NRO FACTURA</b>','col5' => '<b>DETALLE</b>','col6' => '<b>MONTO</b>');
                            $opc=array('showLines'=>2,'shaded'=>0,'rowGap' => 3,'width'=>800,'cols'=>array('col2'=>array('width'=>90),'col3'=>array('width'=>130),'col4'=>array('width'=>90),'col5'=>array('width'=>400),'col6'=>array('width'=>90,'justification'=>'right')));
                            break;
                        case 2://f13
                            $resol=utf8_decode('NORMA');
                            $cat=utf8_decode('CATEGORÍA');
                            $vinc=utf8_decode('VINCULACIÓN');
                            //$cols=array('col2'=>'<b>'.$resol.'</b>','col3' => '<b>ORGANISMO</b>','col4' => '<b>NRO FACTURA</b>','col5' => '<b>DETALLE</b>','col6' => '<b>CONDICION DE VENTA</b>','col7' => '<b>DETALLE COND VENTA</b>','col8' => '<b>MONTO</b>'); 
                            $cols=array('col2'=>'<b>'.$cat.'</b>','col3' => '<b>'.$vinc.'</b>','col4'=>'<b>'.$resol.'</b>','col5' => '<b>ORGANISMO</b>','col6' => '<b>NRO FACTURA</b>','col7' => '<b>DETALLE</b>','col8' => '<b>MONTO</b>'); 
                            //$opc=array('showLines'=>2,'shaded'=>0,'width'=>700,'cols'=>array('col2'=>array('width'=>80),'col3'=>array('width'=>80),'col4'=>array('width'=>90),'col5'=>array('width'=>195),'col6'=>array('width'=>80),'col7'=>array('width'=>190),'col8'=>array('width'=>85,'justification'=>'right')));
                            $opc=array('showLines'=>2,'shaded'=>0,'width'=>800,'cols'=>array('col2'=>array('width'=>90),'col3'=>array('width'=>90),'col5'=>array('width'=>90),'col6'=>array('width'=>90),'col7'=>array('width'=>260),'col8'=>array('width'=>90,'justification'=>'right')));
                            break;
                        case 3://f14
                            $cat=utf8_decode('CATEGORÍA');
                            $vinc=utf8_decode('VINCULACIÓN');
                            $cols=array('col2'=>'<b>'.$cat.'</b>','col3' => '<b>'.$vinc.'</b>','col4'=>'<b>PROVIENE DE </b>','col5' => '<b>NRO FACTURA</b>','col6' => '<b>DETALLE</b>','col7' => '<b>MONTO</b>');
                            $opc=array('showLines'=>2,'shaded'=>0,'width'=>800,'cols'=>array('col2'=>array('width'=>90),'col3'=>array('width'=>90),'col4'=>array('width'=>90),'col5'=>array('width'=>90),'col6'=>array('width'=>350),'col7'=>array('width'=>90,'justification'=>'right')));
                            break;
                        case 4://f21
                            $cat=utf8_decode('CATEGORÍA');
                            $vinc=utf8_decode('VINCULACIÓN');
                            $cols=array('col2'=>'<b>'.$cat.'</b>','col3' => '<b>'.$vinc.'</b>','col4' => '<b>ORGANISMO</b>','col5' => '<b>NRO FACTURA</b>','col6' => '<b>DETALLE</b>','col7' => '<b>MONTO</b>');
                            $opc=array('showLines'=>2,'shaded'=>0,'width'=>800,'cols' =>array('col2'=>array('width'=>90),'col3'=>array('width'=>90),'col4'=>array('width'=>90),'col5'=>array('width'=>90),'col6'=>array('width'=>350),'col7'=>array('width'=>90,'justification'=>'right')));
                            break;
                        case 5://f22
                            $cat=utf8_decode('CATEGORÍA');
                            $vinc=utf8_decode('VINCULACIÓN');
                            $cols=array('col2'=>'<b>'.$cat.'</b>','col3' => '<b>'.$vinc.'</b>','col4' => '<b>ORGANISMO</b>','col5' => '<b>NRO FACTURA</b>','col6' => '<b>DETALLE</b>','col7' => '<b>MONTO</b>');
                            $opc=array('showLines'=>2,'shaded'=>0,'width'=>800,'cols' =>array('col2'=>array('width'=>90),'col3'=>array('width'=>90),'col4'=>array('width'=>90),'col5'=>array('width'=>90),'col6'=>array('width'=>350),'col7'=>array('width'=>90,'justification'=>'right')));
                            break;
                        default:
                            break;
                    }

                   //$pdf->ezTable($datos, $cols, $titulo, $opciones);
                   //$pdf->ezTable($datos, $cols, $titulo, array('cols'=>array('col2'=>array('width'=>80),'col3'=>array('width'=>80),'col4'=>array('width'=>90),'col5'=>array('width'=>195),'col6'=>array('width'=>80),'col7'=>array('width'=>190),'col8'=>array('width'=>85))));
                   $pdf->ezTable($datos, $cols, $titulo,$opc);
                   if($form['id_origen_recurso']==1){//f12
                        $datos1[0]=array('col1'=>'<b>TOTAL BRUTO</b>','col2'=>number_format($datos_form[0]['total'],2,',','.'));
                        $ded=utf8_decode('DEDUCCIÓN');
                        $datos1[1]=array('col1'=>'<b>'.$ded.'</b>','col2'=>number_format($datos_form[0]['retencion'],2,',','.'));
                        $dif=$datos_form[0]['total']-$datos_form[0]['retencion'];
                        $datos1[2]=array('col1'=>'<b>TOTAL NETO</b>','col2'=>number_format($dif,2,',','.'));
                        $pdf->ezTable($datos1,array('col1'=>'','col2'=>''),'',array('showHeadings'=>0,'shaded'=>0,'width'=>800,'cols'=>array('col1'=>array('justification'=>'left','width'=>710),'col2'=>array('justification'=>'right','width'=>90))));
                   }else{
                       $datos1[0]=array('col1'=>'<b>TOTAL</b>','col2'=>number_format($datos_form[0]['total'],2,',','.'));
                       $pdf->ezTable($datos1,array('col1'=>'','col2'=>''),'',array('showHeadings'=>0,'shaded'=>0,'width'=>800,'cols'=>array('col1'=>array('justification'=>'left','width'=>710),'col2'=>array('justification'=>'right','width'=>90))));

                   }
                   $pdf->ezText('<b>Recursos en disponibilidad en: </b>'.$disp,10); 
                   if($form['ingresa_fondo_central']==1){
                       $pdf->ezText("\n\n", 10);
                       $pdf->ezTable($modalidad, $cols_mod, 'MODALIDAD DE INGRESOS DE FONDOS A TESORERIA UNCO',$opc_mod);

                   }
                   $pdf->ezText("\n\n", 10);
                   $pdf->addText(500,80,8,'--------------------------------------------------------------------------------'); 
                   $firma=utf8_decode('Firma, sello y aclaración del Responsable Administrativo');
                   $pdf->addText(500,70,8,$firma); 
                   $pdf->addText(500,60,8,utf8_decode('     El presente tiene carácter de Declaración Jurada ')); 
                   if($form['id_programa']==40){
                      $pdf->addText(100,80,8,'--------------------------------------------------------------------------'); 
                      $firma=utf8_decode('Firma, sello y aclaración del Responsable de Posgrado');
                      $pdf->addText(100,70,8,$firma); 
                      $pdf->addText(100,60,8,utf8_decode('     El presente tiene carácter de Declaración Jurada '));   
                   }
                  
                   //Recorremos cada una de las hojas del documento para agregar el encabezado
                    foreach ($pdf->ezPages as $pageNum=>$id){ 
                        $pdf->reopenObject($id); //definimos el path a la imagen de logo de la organizacion 
                        //agregamos al documento la imagen y definimos su posición a través de las coordenadas (x,y) y el ancho y el alto.
                        $imagen = toba::proyecto()->get_path().'/www/img/sello.jpg';
                        $pdf->addJpegFromFile($imagen, 700, 515, 80, 75);
                        $pdf->addText(730,548,8,$resul[0]['asigna_numero_ingreso']); 
                        $pdf->addText(500,20,8,'Generado por usuario: '.$usuario.' '.date('d/m/Y h:i:s a')); 
                        $pdf->closeObject(); 
                    } 
            }
        }
        
    function conf()
    {
        $id = toba::memoria()->get_parametro('id_form');
        if(isset($id)){//si vuelve desde la operacion para importar rango
             $datos['id_form']=$id;
             $this->dep('datos')->tabla('formulario')->cargar($datos);
             $this->set_pantalla('pant_edicion');
             toba::notificacion()->agregar(utf8_decode('Importación exitosa!'), 'info');
           // $this->dep('ci_detalle_formulario')->set_pantalla('pant_inicial');//este no funciona
        }
    }

}
?>