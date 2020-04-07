<?php
class ci_extracontable extends formularios_ci
{
	protected $s__where;
        protected $s__datos_filtro;
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
	//---- cuadro -----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cuadro(toba_ei_cuadro $cuadro)
	{
          if(isset ($this->s__where)){
             if($this->s__columnas['otros_datos']==0){
                        $c=array('otros_datos');
                        $this->dep('cuadro')->eliminar_columnas($c); 
                }
             if($this->s__columnas['posgrado']==0){
                        $c=array('posgrado');
                        $this->dep('cuadro')->eliminar_columnas($c); 
                }   
              if($this->s__columnas['detalle']==0){
                        $c=array('detalle');
                        $this->dep('cuadro')->eliminar_columnas($c); 
                }   
               if($this->s__columnas['cond_venta']==0){
                        $c=array('cond_venta');
                        $this->dep('cuadro')->eliminar_columnas($c); 
                } 
               if($this->s__columnas['cond_venta2']==0){
                        $c=array('cond_venta2');
                        $this->dep('cuadro')->eliminar_columnas($c); 
                }   
               if($this->s__columnas['desc_exp']==0){
                        $c=array('desc_exp');
                        $this->dep('cuadro')->eliminar_columnas($c); 
                }   
             $cuadro->set_datos($this->dep('datos')->tabla('item')->get_extracontable($this->s__where));
                
            }
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

}

?>