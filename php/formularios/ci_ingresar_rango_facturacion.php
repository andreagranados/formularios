<?php
class ci_ingresar_rango_facturacion extends toba_ci
{
    protected $s__datos;
    protected $s__datos_comprob;
    protected $s__rango;
   
    
    function get_categorias($id_form){
        $programa=$this->dep('datos')->tabla('formulario')->get_programa($id_form);
        return $this->dep('datos')->tabla('macheo_categ_programa')->get_categorias($programa);
    }
    function get_comprobantes($id_form,$anio,$tipo){
        $pv=$this->dep('datos')->tabla('formulario')->su_punto_venta($id_form);
        return $this->dep('datos')->tabla('comprobante')->get_comprobantes_rango($pv,$anio,$tipo);
    }
    function get_grupos_fuente(){
        $ff=$this->dep('datos')->tabla('formulario')->get_origen_recurso($this->s__rango['id_form']);
        return $this->controlador()->dep('datos')->tabla('origen_ingreso')->get_grupos_fuente($ff);
    }
    //-----------------------------------------------------------------------------------
    //---- form_inicial -----------------------------------------------------------------
    //-----------------------------------------------------------------------------------

    function conf__form_inicial(formularios_ei_formulario $form)
    {
       
        if(isset($this->s__rango)){//viene de la solapa detalle entonces cargo el formulario correspondiente
            $form->set_datos($this->s__rango);
            $prog=$this->dep('datos')->tabla('formulario')->get_programa($this->s__rango['id_form']);
            $ff=$this->dep('datos')->tabla('formulario')->get_origen_recurso($this->s__rango['id_form']);
            if($prog<>40){
                $form->desactivar_efs('tipo_posg')  ;
            }
            $f12=array('nro_resol','proviene_de','organismo');
            $f13=array('proviene_de');
            $f14=array('nro_resol','organismo');
            $f21=array('nro_resol','proviene_de');
            $f22=array('nro_resol','proviene_de');
            $ftodos=array('nro_resol','proviene_de','organismo');
         
            switch ($ff) {
                case 1: $form->desactivar_efs($f12)  ;             break;
                case 2: $form->desactivar_efs($f13)  ;              break;//f13
                case 3: $form->desactivar_efs($f14)  ;             break;//f14
                case 4: $form->desactivar_efs($f21)  ;             break;//f21
                case 5: $form->desactivar_efs($f22)  ;             break;//f22
                default: $form->desactivar_efs($ftodos)  ;             break;
            }
            
            
        }
//        else{
//            if(isset($this->s__datos)){
//                $form->set_datos($this->s__datos);//la variable se llena cuando se presiona el boton validar
//            }        
//        }
    }
    
    function evt__form_inicial__validar($datos)
    {//print_r($datos);//Array ( [id_form] => 64 [id_categ] => 8 [id_vinc] => 2 [detalle] => sas [anio] => 2019 [tipo_comprob] => 11 [desde] => 437 [hasta] => 442 )
          $this->s__datos=$datos;
          $this->set_pantalla('pant_importar');
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
        if(count($this->s__datos_comprob)>0){
            $this->dep('datos')->tabla('item')->importar($this->s__datos_comprob,$this->s__datos);
            $parametros['id_form']=$this->s__rango['id_form'];
        }
        toba::vinculador()->navegar_a('formularios',3814,$parametros);
    }

    function conf()
    {
        $id_form= toba::memoria()->get_parametro('id_form');
        if(isset($id_form)){//si tiene valor entonces viene desde el detalle del formulario
            $this->s__rango['id_form']=$id_form;
        }
    }
}
?>