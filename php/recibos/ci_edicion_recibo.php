<?php                           
class ci_edicion_recibo extends toba_ci
{
    protected $s__datos;
    protected $s__pantalla;
    protected $s__confirmar;
    protected $s__generado;
    
    function evt__form_recibo__guardar($datos)//boton implicito se ejecuta cuando se presiona el boton siguiente
    {
        $this->s__generado=false;
        $datos['estado']='I';
        $datos['fecha']=date("d-m-Y");
        $this->controlador()->dep('datos')->tabla('recibo')->set($datos);
        if($this->s__pantalla=='pant_edicion'){////la variable es true cuando esta en la pantalla pant_edicion
            $this->controlador()->dep('datos')->tabla('recibo')->sincronizar();
            toba::notificacion()->agregar(utf8_decode('El recibo se ha generado exitosamente'),'info');
            $this->s__generado=true;
           // print_r('guarda');
        }
//            
    }
    function conf__form_recibo(toba_ei_formulario $form)
    {
        if($this->s__pantalla=='pant_edicion'){
            $form->ef('recibi_de')->set_solo_lectura('true');
            $form->ef('concepto')->set_solo_lectura('true');
            $form->ef('monto')->set_solo_lectura('true');
        }
        //si estoy en pantalla edicion los pongo de solo lectura
        $datos=$this->controlador()->dep('datos')->tabla('recibo')->get();
        $form->set_datos($datos);
        
    }
    function evt__confirmar(){
        //guarda el recibo y genera el numero
        
    }
     
        
    function conf__pant_edicion(toba_ei_pantalla $pantalla)
    {
        $this->s__pantalla='pant_edicion';
        $this->s__confirmar=true;//la variable es true cuando esta en la pantalla pant_edicion
        if($this->s__generado){
            $this->pantalla()->eliminar_evento('confirmar');
            $this->pantalla()->eliminar_evento('cambiar_tab__anterior');
            $this->pantalla()->evento('imprimir')->mostrar();
           // print_r('elimina');
        }else{
            $this->pantalla()->evento('imprimir')->ocultar();
            $this->pantalla()->evento('finalizar')->ocultar();
        }
       // $this->pantalla()->eliminar_evento('confirmar');
    }
    function conf__pant_inicial(toba_ei_pantalla $pantalla)
    {
        $this->s__pantalla='pant_inicial';
        $this->s__confirmar=false;
        
    }
    function evt__finalizar(){
        $this->controlador()->set_pantalla('pant_inicial');
    }
}
?>                   