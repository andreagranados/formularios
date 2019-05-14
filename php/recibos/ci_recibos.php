<?php

require_once toba::proyecto()->get_path().'/php/formularios/NumeroALetras.php';

//require_once(toba_dir() . '/php/3ros/ezpdf/class.ezpdf.php');
class ci_recibos extends toba_ci
{
        protected $s__where;
        protected $s__datos_filtro;
        protected $s__recibo;
       
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
	//---- botones -----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------
        function evt__agregar()
	{
             $this->set_pantalla('pant_edicion');
	}
        function evt__anular()
	{
            if($this->dep('datos')->tabla('recibo')->esta_cargada()){
                $datos['estado']='A';
                $this->dep('datos')->tabla('recibo')->set($datos);
                $this->dep('datos')->tabla('recibo')->sincronizar();
                $this->dep('datos')->tabla('recibo')->resetear();
                $this->set_pantalla('pant_inicial');
                toba::notificacion()->agregar('El recibo ha sido anulado correctamente', 'info');  
            } 
	}
        function evt__volver()
	{
            $this->dep('datos')->tabla('recibo')->resetear();
            $this->set_pantalla('pant_inicial');
	}
        //-----------------------------------------------------------------------------------
	//---- form_recibo -----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------
//        function conf__form_recibo(toba_ei_formulario $form)
//        {
//            $datos=$this->controlador()->dep('datos')->tabla('recibo')->get();
//            $form->set_datos($datos);
//        }
//        function conf__form_reciboi(toba_ei_formulario $form)
//        {
//            $datos=$this->controlador()->dep('datos')->tabla('recibo')->get();
//            $form->set_datos($datos);
//        }
        //-----------------------------------------------------------------------------------
	//---- cuadro -----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cuadro(toba_ei_cuadro $cuadro)
	{
            if (isset($this->s__where)) {
                $cuadro->set_datos($this->dep('datos')->tabla('recibo')->get_listado_filtro($this->s__where));
            } 
            else{
                $cuadro->set_datos($this->dep('datos')->tabla('recibo')->get_listado_filtro());
            }
	}
        //selecciona un recibo del cuadro
        function evt__cuadro__seleccion($datos)
        {
            $this->dep('datos')->tabla('recibo')->cargar($datos);
            $this->set_pantalla('pant_impresion');
        }
 
}
?>