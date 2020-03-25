<?php
class ci_ingresar_rango_facturacion extends toba_ci
{
    protected $s__datos;
    protected $s__datos_comprob;
    protected $s__rango;
    protected $s__volver;
    
    function get_categorias($id_form){
        $programa=$this->dep('datos')->tabla('formulario')->get_programa($id_form);
        return $this->dep('datos')->tabla('macheo_categ_programa')->get_categorias($programa);
    }
    function get_comprobantes($id_form,$anio,$tipo){
        $pv=$this->dep('datos')->tabla('formulario')->su_punto_venta($id_form);
        return $this->dep('datos')->tabla('comprobante')->get_comprobantes_rango($pv,$anio,$tipo);
    }

    //-----------------------------------------------------------------------------------
    //---- form_inicial -----------------------------------------------------------------
    //-----------------------------------------------------------------------------------

    function conf__form_inicial(formularios_ei_formulario $form)
    {
        if(isset($this->s__rango)){//viene de la solapa detalle entonces cargo el formulario correspondiente
            $form->set_datos($this->s__rango);
            }else{
                if(isset($this->s__datos)){
                    $form->set_datos($this->s__datos);//la variable se llena cuando se presiona el boton validar
                }        
            }
    }
    
    function evt__form_inicial__validar($datos)
    {//print_r($datos);//Array ( [id_form] => 64 [id_categ] => 8 [id_vinc] => 2 [detalle] => sas [anio] => 2019 [tipo_comprob] => 11 [desde] => 437 [hasta] => 442 )
          $this->s__datos=$datos;
          $this->set_pantalla('pant_importar');
          // print_r($this->s__volver);
           if($this->s__volver==1){//viene desde detalle
               print_r('andrea');
           }
    }

    //-----------------------------------------------------------------------------------
    //---- cuadro -----------------------------------------------------------------------
    //-----------------------------------------------------------------------------------

    function conf__cuadro(formularios_ei_cuadro $cuadro)
    {
       $this->s__datos_comprob=$this->dep('datos')->tabla('comprobante')->get_comprobantes_desde_hasta($this->s__datos['id_form'],$this->s__datos['tipo_comprob'],$this->s__datos['anio'],$this->s__datos['desde'],$this->s__datos['hasta']);
       $cuadro->set_datos($this->s__datos_comprob);
    }

    //-----------------------------------------------------------------------------------
    //---- cuadro -----------------------------------------------------------------------
    //-----------------------------------------------------------------------------------

    function evt__cuadro__importar($datos)
    {
        $this->dep('datos')->tabla('item')->importar($this->s__datos_comprob,$this->s__datos);
        toba::notificacion()->agregar(utf8_decode('Importación exitosa!'), 'info');
    }

    function evt__volver()
    {
        if($this->s__volver==1){//viene desde detalle
            //print_r('xx');
            $parametros['id_form']=$this->s__rango['id_form'];
            toba::vinculador()->navegar_a('formularios',3814,$parametros);
        }
//        else{
//            $this->set_pantalla('pant_inicial');
//            unset($this->s__datos_comprob); 
//        }
    }
     function evt__volver_detalle()
    {
         if(isset($this->s__rango)){//viene desde detalle. la variable tiene valor solo si vuelve de detalle
            $parametros['id_form']=$this->s__rango['id_form'];
            toba::vinculador()->navegar_a('formularios',3814,$parametros);
        }

    }
    function conf()
    {
        $id_form= toba::memoria()->get_parametro('id_form');
        if(isset($id_form)){//si tiene valor entonces viene desde el detalle del formulario
            $this->s__rango['id_form']=$id_form;
            $this->s__volver=1;//para volver a detalle
            print_r('detalle');
        }else{
            $this->s__volver=0;
        }
    }
}
?>