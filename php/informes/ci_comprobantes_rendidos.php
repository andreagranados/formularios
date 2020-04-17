<?php
class ci_comprobantes_rendidos extends formularios_ci
{
        protected $s__where;
        protected $s__datos_filtro;
        //-----------------------------------------------------------------------------------
	//---- filtros ----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------
        function conf__filtros(toba_ei_filtro $filtro)
	{
            if (isset($this->s__datos_filtro)) {
                $filtro->set_datos($this->s__datos_filtro);               
            }
            $filtro->columna('fecha_emision')->set_condicion_fija('entre',true)  ;   
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
                $cuadro->set_datos($this->dep('datos')->tabla('comprobante')->get_comprobantes_rendidos($this->s__where));
            }
	}

}

?>