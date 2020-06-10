<?php
class form_modalidad_extension extends toba_ei_formulario
{
    function extender_objeto_js()
    {
        echo "
			
                       
			 {$this->objeto_js}.evt__id_condicion_venta__procesar = function(es_inicial) 
			{
                                var myvar=this.ef('id_condicion_venta').get_estado();
                                /*console.log(myvar);*/
				switch (this.ef('id_condicion_venta').get_estado()) {
                                
					case '1':
                                          this.ef('cuenta_a_acreditar').ocultar();
                                          this.ef('nro_transferencia').ocultar();
                                           this.ef('nro_cuil').ocultar();
                                            this.ef('nro_cheque').ocultar();
                                            this.ef('id_banco').ocultar();
                                            this.ef('fecha_emision_cheque').ocultar();
                                            this.ef('archivo_trans').ocultar();
                                            this.ef('imagen_vista_previa_t').ocultar();
                                            /*this.ef('cuil1').ocultar();
                                            this.ef('cuil').ocultar();
                                            this.ef('cuil2').ocultar();*/
                                            break;
                                        case '2': 
                                            this.ef('cuenta_a_acreditar').ocultar();
                                            this.ef('nro_transferencia').ocultar();
                                            this.ef('nro_cuil').ocultar();
                                            this.ef('nro_cheque').mostrar();
                                            this.ef('id_banco').mostrar();
                                            this.ef('fecha_emision_cheque').mostrar();
                                            this.ef('archivo_trans').ocultar();
                                            this.ef('imagen_vista_previa_t').ocultar();
                                            /*this.ef('cuil1').ocultar();
                                            this.ef('cuil').ocultar();
                                            this.ef('cuil2').ocultar();*/
                                            break;
                                        case '3': 
                                            this.ef('nro_cheque').ocultar();
                                            this.ef('id_banco').ocultar();
                                            this.ef('fecha_emision_cheque').ocultar();
                                            this.ef('cuenta_a_acreditar').mostrar();
                                            this.ef('nro_transferencia').mostrar();
                                            this.ef('nro_cuil').mostrar();
                                            this.ef('archivo_trans').mostrar();
                                            this.ef('imagen_vista_previa_t').mostrar();
                                            break;
                                        default:/* por defecto no aparece nada*/
                                            this.ef('cuenta_a_acreditar').ocultar();
                                            this.ef('nro_transferencia').ocultar();
                                            this.ef('nro_cuil').ocultar();
                                            this.ef('nro_cheque').ocultar();
                                            this.ef('id_banco').ocultar();
                                            this.ef('fecha_emision_cheque').ocultar();
                                            this.ef('archivo_trans').ocultar();
                                            this.ef('imagen_vista_previa_t').ocultar();
                                            break;   
									
				}
			}
                      
                        ";
    }
}

?>