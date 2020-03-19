<?php
class ci_ingresar_rango_facturacion extends toba_ci
{
    function get_categorias($id_form){
        $programa=$this->dep('datos')->tabla('formulario')->get_programa($id_form);
        return $this->dep('datos')->tabla('macheo_categ_programa')->get_categorias($programa);
    }
    function get_comprobantes($id_form,$anio,$tipo){
        $pv=$this->dep('datos')->tabla('formulario')->su_punto_venta($id_form);
        $resul=$this->dep('datos')->tabla('comprobante')->get_comprobantes($pv,$anio,0);
    }

}?>