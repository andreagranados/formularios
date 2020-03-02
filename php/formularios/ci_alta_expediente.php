<?php
class ci_alta_expediente extends toba_ci
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

	function conf__cuadro(formularios_ei_cuadro $cuadro)
	{
            if(isset($this->s__where)){
                $cuadro->set_datos($this->dep('datos')->tabla('expediente')->get_listado($this->s__where));
            }
            else{
                $cuadro->set_datos($this->dep('datos')->tabla('expediente')->get_listado());
            }
	}
        function evt__cuadro__seleccion($datos)
	{
            $this->dep('datos')->tabla('expediente')->cargar($datos);
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

	//-----------------------------------------------------------------------------------
	//---- Eventos ----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function evt__agregar()
	{
            $this->set_pantalla('pant_edicion');
	}

	

	//-----------------------------------------------------------------------------------
	//---- form_exped -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------
        function conf__form_exped(formularios_ei_formulario $form)
	{
            if ($this->dep('datos')->tabla('expediente')->esta_cargada()) {
                $datos = $this->dep('datos')->tabla('expediente')->get();
                $form->set_datos($datos);
            }
	}
	function evt__form_exped__alta($datos)
	{
            $bandera=$this->dep('datos')->tabla('expediente')->existe($datos['nro_expediente']);
            if($bandera){
                toba::notificacion()->agregar('Ese expediente ya existe', 'error');  
                $this->set_pantalla('pant_inicial');
            }else{
                $this->dep('datos')->tabla('expediente')->set($datos);
                $this->dep('datos')->tabla('expediente')->sincronizar();
                $exp=$this->dep('datos')->tabla('expediente')->get();
                $datose['nro_expediente']=$exp['nro_expediente'];      
                $this->dep('datos')->tabla('expediente')->cargar($datose);    
            }
	}

	function evt__form_exped__baja()
	{
            $exp=$this->dep('datos')->tabla('expediente')->get();
            $bandera=$this->dep('datos')->tabla('expediente')->puedo_borrar($exp['nro_expediente']);
            if($bandera){
                $this->dep('datos')->tabla('expediente')->eliminar_todo();
                $this->dep('datos')->tabla('expediente')->resetear();
            }else{
                toba::notificacion()->agregar('Este expediente tiene formularios asociados, no puede borrarlo', 'error');  
            }
            
            $this->set_pantalla('pant_inicial');
	}

	function evt__form_exped__modificacion($datos)
	{
//            $this->dep('datos')->tabla('expediente')->set($datos);
//            $this->dep('datos')->tabla('expediente')->sincronizar();
//            toba::notificacion()->agregar('El expediente se ha modificado correctamente', 'info');  
	}

	function evt__form_exped__cancelar()
	{
            $this->dep('datos')->tabla('expediente')->resetear();
            $this->set_pantalla('pant_inicial');
	}

	

}
?>