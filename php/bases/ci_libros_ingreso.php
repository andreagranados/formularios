<?php
class ci_libros_ingreso extends formularios_ci
{
	//-----------------------------------------------------------------------------------
	//---- cuadro -----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cuadro(formularios_ei_cuadro $cuadro)
	{
           $cuadro->set_datos($this->dep('datos')->tabla('libro_ingreso')->get_descripciones());
	}

	function evt__cuadro__seleccion($datos)
	{
            $this->dep('datos')->tabla('libro_ingreso')->cargar($datos);
            $this->set_pantalla('pant_edicion');
	}

	//-----------------------------------------------------------------------------------
	//---- form -------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__form(formularios_ei_formulario $form)
	{
            if ($this->dep('datos')->tabla('libro_ingreso')->esta_cargada()) {
                $form->set_datos($this->dep('datos')->tabla('libro_ingreso')->get());
            }
	}

	function evt__form__alta($datos)
	{
            $datos['numero']=1;
            $this->dep('datos')->tabla('libro_ingreso')->set($datos);
            $this->dep('datos')->tabla('libro_ingreso')->sincronizar();
            $this->set_pantalla('pant_inicial');   
            $this->dep('datos')->tabla('libro_ingreso')->resetear();
            toba::notificacion()->agregar('Se ha ingresado un nuevo libro', 'info'); 
	}

	function evt__form__baja()
	{
            $this->dep('datos')->tabla('libro_ingreso')->eliminar_todo();
            $this->resetear();
            toba::notificacion()->agregar('El libro se ha eliminado exitosamente', 'info'); 
	}

	function evt__form__modificacion($datos)
	{
            $this->dep('datos')->tabla('libro_ingreso')->set($datos);
            $this->dep('datos')->tabla('libro_ingreso')->sincronizar();
            $this->set_pantalla('pant_inicial');   
            $this->dep('datos')->tabla('libro_ingreso')->resetear();
            toba::notificacion()->agregar('Los datos se han guardado correctamente', 'info'); 
	}

	function evt__form__cancelar()
	{
          $this->set_pantalla('pant_inicial');   
          $this->dep('datos')->tabla('libro_ingreso')->resetear();
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

}
?>