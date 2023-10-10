<?php
class ci_alta_actividad extends toba_ci
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
                $cuadro->set_datos($this->dep('datos')->tabla('actividad')->get_listado($this->s__where));
            }
            else{
                $cuadro->set_datos($this->dep('datos')->tabla('actividad')->get_listado());
            }
	}
        function evt__cuadro__seleccion($datos)
	{
            $this->dep('datos')->tabla('actividad')->cargar($datos);
            $this->set_pantalla('pant_edicion');
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
        function conf__form_activ(formularios_ei_formulario $form)
	{
            if ($this->dep('datos')->tabla('actividad')->esta_cargada()) {
                $datos = $this->dep('datos')->tabla('actividad')->get();
                $form->set_datos($datos);
            }
	}
	function evt__form_activ__alta($datos)
	{
            $bandera=$this->dep('datos')->tabla('actividad')->existe($datos['descripcion'],$datos['id_dependencia'],$datos['id_programa'],$datos['id_categ']);
            
            if($bandera){
                toba::notificacion()->agregar('Ya existe una actividad con el mismo nombre', 'error');  
                $this->set_pantalla('pant_inicial');
            }else{
                
                //pasa a mayuscula y saca acentos
                $datos['descripcion']=mb_strtoupper($datos['descripcion'],'LATIN1');
                $search  = array('Á', 'É', 'Í', 'Ó', 'Ú');
                $replace = array('A', 'E', 'I', 'O', 'U');
                $datos['descripcion']= str_replace($search, $replace, utf8_encode($datos['descripcion']));
                $this->dep('datos')->tabla('actividad')->set($datos);
                $this->dep('datos')->tabla('actividad')->sincronizar();
                $this->dep('datos')->tabla('actividad')->resetear();
                toba::notificacion()->agregar('La actividad ha sido ingresada correctamente', 'info'); 
                $this->set_pantalla('pant_inicial');
            }
	}

	function evt__form_activ__baja()
	{
            $act=$this->dep('datos')->tabla('actividad')->get();
            //no puede borrar cuando la actividad tiene items asociados
            $bandera=$this->dep('datos')->tabla('actividad')->tiene_item($act['id_actividad']);
            if(!$bandera){//sino tiene items asociados entonces puedo eliminar
                $this->dep('datos')->tabla('actividad')->eliminar_todo();
                $this->dep('datos')->tabla('actividad')->resetear();
                toba::notificacion()->agregar('La actividad ha sido eliminada correctamente', 'info'); 
            }else{
                toba::notificacion()->agregar('La actividad tiene items asociados, no puede borrarlo', 'error');  
            }
            $this->set_pantalla('pant_inicial');
	}

	function evt__form_activ__modificacion($datos)
	{
               $act=$this->dep('datos')->tabla('actividad')->get();
               $band=$this->dep('datos')->tabla('actividad')->tiene_item($act['id_actividad']);
               if($band){
                  toba::notificacion()->agregar('La actividad tiene items asociados. No puede modificar porque modifica la actividad de los items asociados', 'info');
               }else{//no tiene items asociados
                   $band2=$this->dep('datos')->tabla('actividad')->modificar($datos,$act['id_actividad']);
                   if($band2){//es true cuando puede modificar porque no existe otro expediente con el mismo nro
                        $datos['descripcion']=mb_strtoupper($datos['descripcion'],'LATIN1');
                        $datos['descripcion']=mb_strtoupper($datos['descripcion'],'LATIN1');
                        $search  = array('Á', 'É', 'Í', 'Ó', 'Ú');
                        $replace = array('A', 'E', 'I', 'O', 'U');
                        $datos['descripcion']= str_replace($search, $replace, utf8_encode($datos['descripcion']));
                        $this->dep('datos')->tabla('actividad')->set($datos);
                        $this->dep('datos')->tabla('actividad')->sincronizar();
                        toba::notificacion()->agregar('La actividad se ha modificado correctamente '.$mensaje, 'info');   
                   }
            
               }
        }
        function evt__form_activ__cancelar()
	{
            $this->dep('datos')->tabla('actividad')->resetear();
            $this->set_pantalla('pant_inicial');
	}

}
?>