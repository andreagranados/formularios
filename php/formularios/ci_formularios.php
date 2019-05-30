<?php
class ci_formularios extends toba_ci
{
    protected $s__datos_filtro;
    protected $s__where;
    
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
	//---- cuadro -----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cuadro(toba_ei_cuadro $cuadro)
	{
            if (isset($this->s__where)) {
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
            $salida->set_nombre_archivo("Formulario.pdf");
            //recuperamos el objteo ezPDF para agregar la cabecera y el pie de página 
            $salida->set_papel_orientacion('landscape');
            $salida->inicializar();
            $pdf = $salida->get_pdf();//top,bottom,left,righ
            $pdf->ezSetMargins(80, 50, 20, 20);
            //Configuramos el pie de página. El mismo, tendra el número de página centrado en la página y la fecha ubicada a la derecha. 
            //Primero definimos la plantilla para el número de página.
            $formato = utf8_decode('Página {PAGENUM} de {TOTALPAGENUM}   ');
            $titulo="   ";
            $opciones = array(
                'showLines'=>1,
                'splitRows'=>0,
               // 'rowGap' => 1,//, the space between the text and the row lines on each row
               // 'lineCol' => (r,g,b) array,// defining the colour of the lines, default, black.
                //'showLines'=>2,//coloca las lineas horizontales
                //'showHeadings' => true,//muestra el nombre de las columnas
                'titleFontSize' => 12,
                'fontSize' => 10,
                //'shadeCol' => array(1,1,1,1,1,1,1,1,1,1,1,1),
                //'shadeCol' => array(0.1,0.1,0.1),//darle color a las filas intercaladamente
                'outerLineThickness' => 0,
                'innerLineThickness' => 0,
                'xOrientation' => 'center',
                'width' => 800//,
               //'cols' =>array('col2'=>array('justification'=>'center') ,'col3'=>array('justification'=>'center'),'col4'=>array('justification'=>'center') ,'col5'=>array('justification'=>'center'),'col6'=>array('justification'=>'center') ,'col7'=>array('justification'=>'center') ,'col8'=>array('justification'=>'center'),'col9'=>array('justification'=>'center') ,'col10'=>array('justification'=>'center') ,'col11'=>array('justification'=>'center') ,'col12'=>array('justification'=>'center'),'col13'=>array('justification'=>'center') ,'col14'=>array('justification'=>'center') )
                );
               
               $form=$this->dep('datos')->tabla('formulario')->get();
               //llama a una funcion para asignar el numero de entrada         
               $sql="select asigna_numero_ingreso(".$form['id_form'].")";
               $resul=toba::db('formularios')->consultar($sql);
               $datos_form=$this->dep('datos')->tabla('item')->get_listado($form['id_form']);
              // print_r($datos_form);
               $datos=array();
              
               $i=0;
               //Configuración de Título.
               $tit=$this->dep('datos')->tabla('formulario')->get_titulo($form['id_form']);
               $texto=utf8_decode('(Su presentación es obligatoria ante la Dirección de Tesorería)');
               $salida->titulo(utf8_d_seguro($tit)); 
               $pdf->ezText($texto, 8, array('justification'=>'center'));
               $pdf->ezText("\n\n", 10);
               $pdf->ezText('DEPENDENCIA: '.$form['id_dependencia'], 10);
               if(isset($form['id_punto_venta'])){
                  $pdf->ezText('PUNTO DE VENTA: <b>'.$form['id_punto_venta'].'</b>', 10); 
               }
               $fec=date("d/m/Y",strtotime($form['fecha_creacion']));
               $pdf->ezText('FECHA CREACION: '.$fec, 10);
               $pdf->ezText('EXPEDIENTE: '.$form['nro_expediente'], 10);
               //print_r($datos_form );exit;
               foreach ($datos_form as $item) {
                    switch ($form['id_origen_recurso']){
                        case 1://f12
                            $datos[$i]=array( 'col2'=>$item['categ'],'col3' => $item['vinc'],'col4' => $item['nro_factura'],'col5' => $item['detalle'],'col6' => $item['condicion_venta'],'col7' => $item['condicion_venta2'],'col8' => number_format($item['monto'],2,'.',''));
                            break;
                        case 2://f13
                            $datos[$i]=array( 'col2'=>$item['nro_resol'],'col3' => $item['organismo'],'col4' => $item['nro_factura'],'col5' => $item['detalle'],'col6' => $item['condicion_venta'],'col7' => $item['condicion_venta2'],'col8' => number_format($item['monto'],2,'.',''));
                            break;
                        case 3://f14
                            $datos[$i]=array( 'col2'=>$item['proviene_de'],'col3' => $item['nro_factura'],'col4' => $item['detalle'],'col5' => $item['condicion_venta'],'col6' => $item['condicion_venta2'],'col7' => number_format($item['monto'],2,'.',''));
                            break;
                        case 4://f21
                            $datos[$i]=array( 'col2' => $item['nro_factura'],'col3' => $item['detalle'],'col4' => $item['condicion_venta'],'col5' => $item['condicion_venta2'],'col6' => number_format($item['monto'],2,'.',''));
                            break;
                        case 5://f22
                            $datos[$i]=array( 'col2' => $item['nro_factura'],'col3' => $item['detalle'],'col4' => $item['condicion_venta'],'col5' => $item['condicion_venta2'],'col6' => number_format($item['monto'],2,'.',''));
                            break;
                    }
                   $i++;
               }  
              //print_r($datos);exit;
                switch ($form['id_origen_recurso']) {
                    case 1://f12
                        $cat=utf8_decode('CATEGORÍA');
                        $vinc=utf8_decode('VINCULACIÓN');
                        $cols=array('col2'=>'<b>'.$cat.'</b>','col3' => '<b>'.$vinc.'</b>','col4' => '<b>NRO FACTURA</b>','col5' => '<b>DETALLE</b>','col6' => '<b>CONDICION DE VENTA</b>','col7' => '<b>DETALLE COND VENTA</b>','col8' => '<b>MONTO</b>');
                        $opc=array('showLines'=>2,'shaded'=>0,'width'=>800,'cols'=>array('col2'=>array('width'=>80),'col3'=>array('width'=>80),'col4'=>array('width'=>90),'col5'=>array('width'=>195),'col6'=>array('width'=>80),'col7'=>array('width'=>190),'col8'=>array('width'=>85,'justification'=>'right')));
                        break;
                    case 2://f13
                        $resol=utf8_decode('RESOLUCIÓN');
                        $cols=array('col2'=>'<b>'.$resol.'</b>','col3' => '<b>ORGANISMO</b>','col4' => '<b>NRO FACTURA</b>','col5' => '<b>DETALLE</b>','col6' => '<b>CONDICION DE VENTA</b>','col7' => '<b>DETALLE COND VENTA</b>','col8' => '<b>MONTO</b>'); 
                        $opc=array('showLines'=>2,'shaded'=>0,'width'=>800,'cols'=>array('col2'=>array('width'=>80),'col3'=>array('width'=>80),'col4'=>array('width'=>90),'col5'=>array('width'=>195),'col6'=>array('width'=>80),'col7'=>array('width'=>190),'col8'=>array('width'=>85)));
                        break;
                    case 3://f14
                        $cols=array('col2'=>'<b>PROVIENE DE </b>','col3' => '<b>NRO FACTURA</b>','col4' => '<b>DETALLE</b>','col5' => '<b>CONDICION DE VENTA</b>','col6' => '<b>DETALLE COND DE VENTA</b>','col7' => '<b>MONTO</b>');
                        $opc=array('showLines'=>2,'shaded'=>0,'cols'=>array('col2'=>array('width'=>160),'col3'=>array('width'=>90),'col4'=>array('width'=>195),'col5'=>array('width'=>80),'col6'=>array('width'=>190),'col7'=>array('width'=>85)));
                        break;
                    case 4://f21
                        $cols=array('col2' => '<b>NRO FACTURA</b>','col3' => '<b>DETALLE</b>','col4' => '<b>CONDICION DE VENTA</b>','col5' => '<b>DETALLE DE CONDICION DE VENTA</b>','col6' => '<b>MONTO</b>');
                        $opc=array('showLines'=>2,'shaded'=>0,'width'=>800,'cols'=>array('col2'=>array('width'=>160),'col3'=>array('width'=>195),'col4'=>array('width'=>80),'col5'=>array('width'=>190),'col6'=>array('width'=>85)));
                        break;
                    case 5://f22
                        $cols=array('col2' => '<b>NRO FACTURA</b>','col3' => '<b>DETALLE</b>','col4' => '<b>CONDICION DE VENTA</b>','col5' => '<b>DETALLE DE CONDICION DE VENTA</b>','col6' => '<b>MONTO</b>');
                        $opc=array('showLines'=>2,'shaded'=>0,'width'=>800,'cols' =>array('cols'=>array('col2'=>array('width'=>160),'col3'=>array('width'=>195),'col4'=>array('width'=>80),'col5'=>array('width'=>190),'col6'=>array('justification'=>'right','width'=>85))));
                        break;
                    default:
                        break;
                }

               //$pdf->ezTable($datos, $cols, $titulo, $opciones);
               //$pdf->ezTable($datos, $cols, $titulo, array('cols'=>array('col2'=>array('width'=>80),'col3'=>array('width'=>80),'col4'=>array('width'=>90),'col5'=>array('width'=>195),'col6'=>array('width'=>80),'col7'=>array('width'=>190),'col8'=>array('width'=>85))));
               $pdf->ezTable($datos, $cols, $titulo,$opc);
               if($form['id_origen_recurso']==1){
                    $datos1[0]=array('col1'=>'<b>TOTAL BRUTO</b>','col2'=>number_format($datos_form[0]['total'],2,'.',''));
                    $ded=utf8_decode('DEDUCCIÓN');
                    $datos1[1]=array('col1'=>'<b>'.$ded.'</b>','col2'=>number_format($datos_form[0]['retencion'],2,'.',''));
                    $dif=$datos_form[0]['total']-$datos_form[0]['retencion'];
                    $datos1[2]=array('col1'=>'<b>TOTAL NETO</b>','col2'=>number_format($dif,2,'.',''));
                    $pdf->ezTable($datos1,array('col1'=>'','col2'=>''),'',array('showHeadings'=>0,'shaded'=>0,'width'=>800,'cols'=>array('col1'=>array('justification'=>'left','width'=>715),'col2'=>array('justification'=>'right','width'=>85))));
               }else{
                   $datos1[0]=array('col1'=>'<b>TOTAL</b>','col2'=>number_format($datos_form[0]['total'],2,'.',''));
                   $pdf->ezTable($datos1,array('col1'=>'','col2'=>''),'',array('showHeadings'=>0,'shaded'=>0,'width'=>800,'cols'=>array('col1'=>array('justification'=>'left','width'=>715),'col2'=>array('justification'=>'right','width'=>85))));
               }

               $pdf->addText(500,80,8,'------------------------------------------------------------------------'); 
               $pdf->addText(500,70,8,'            Firma del Responsable Administrativo '); 
               $pdf->addText(500,60,8,utf8_decode('     El presente tiene carácter de Declaración Jurada ')); 
               $pdf->ezStartPageNumbers(100, 20, 8, 'left', utf8_d_seguro($formato), 1); 
                //Luego definimos la ubicación de la fecha en el pie de página.
               $pdf->addText(710,20,8,date('d/m/Y h:i:s a')); 
               //Recorremos cada una de las hojas del documento para agregar el encabezado
                foreach ($pdf->ezPages as $pageNum=>$id){ 
                    $pdf->reopenObject($id); //definimos el path a la imagen de logo de la organizacion 
                    //agregamos al documento la imagen y definimos su posición a través de las coordenadas (x,y) y el ancho y el alto.
                    $imagen = toba::proyecto()->get_path().'/www/img/sello.jpg';
                    $pdf->addJpegFromFile($imagen, 700, 500, 80, 75);
                    $pdf->closeObject(); 
                } 
                
                $pdf->addText(725,530,8,$resul[0]['asigna_numero_ingreso']); 
        }

}
?>