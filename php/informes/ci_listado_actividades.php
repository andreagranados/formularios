<?php
class ci_listado_actividades extends formularios_ci
{
    protected $s__where;
    protected $s__datos_filtro;
    protected $s__columnas;
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
        if(isset ($this->s__where)){
            if($this->s__columnas['nro_resol']==0){
                $c=array('nro_resol');
                $this->dep('cuadro')->eliminar_columnas($c); 
                }
            if($this->s__columnas['organismo']==0){
                $c=array('organismo');
                $this->dep('cuadro')->eliminar_columnas($c); 
                }
            if($this->s__columnas['proviene_de']==0){
                $c=array('proviene_de');
                $this->dep('cuadro')->eliminar_columnas($c); 
                }
             if($this->s__columnas['tipo_post_desc']==0){
                $c=array('tipo_post_desc');
                $this->dep('cuadro')->eliminar_columnas($c); 
                }    
            $cuadro->set_datos($this->dep('datos')->tabla('actividad')->get_listado_actividades($this->s__where));
        }
    }
}
?>