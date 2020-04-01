<?php
class form_campos_solicitados extends toba_ei_formulario
{
    function extender_objeto_js()
    {
        echo "
			/**
			 * Acci�n del bot�n CALCULAR
			 
			{$this->objeto_js}.evt__id_comprobante__procesar = function(es_inicial) {
                           
                            switch (this.ef('id_comprobante').get_estado()) {
                            case '1':  alert ('ggg');this.ef('monto').set_estado(111);break;
                            default:  this.ef('monto').set_estado(0);break;
                            }
                         
			
			}*/
                        {$this->objeto_js}.evt__id_comprobante__procesar = function(es_inicial) {
                          /*alert ('hola');*/
				//--- Construyo los parametros para el calculo, en este caso son los valores del form
				var parametros = this.get_datos();
				
				//--- Hago la peticion de datos al server, la respuesta vendra en el m�todo this.actualizar_datos
				this.controlador.ajax('calcular', parametros, this, this.actualizar_datos);
				
				//--- Evito que el mecanismo 'normal' de comunicacion cliente-servidor se ejecute
				return false;
			}
                        /**
			 * Acci�n cuando vuelve la respuesta desde PHP
			 */
			{$this->objeto_js}.actualizar_datos = function(datos)
			{
				this.ef('monto').set_estado(datos);
			}

                        {$this->objeto_js}.evt__efecto__procesar = function(es_inicial) 
			{
                            
                                this.evt__id_origen_recurso__procesar(es_inicial);
                                this.evt__id_condicion_venta__procesar(es_inicial);
   
			}
                      
                        {$this->objeto_js}.evt__corresponde_factura__procesar = function(es_inicial) 
			{
                            switch (this.ef('corresponde_factura').get_estado()) {
                                case 'NO': this.ef('id_comprobante').ocultar();
                                            break;
                                case 'SI': this.ef('id_comprobante').mostrar();
                                           break;
                                            
                            }
                        }
                        {$this->objeto_js}.evt__id_categ__procesar = function(es_inicial)
                        {
                                var myvar=this.ef('id_categ').get_estado();
                                console.log(myvar);
                                switch (this.ef('id_categ').get_estado()) {
                                case '7': this.ef('tipo_posg').mostrar();break;
                                        default: this.ef('tipo_posg').ocultar(); break;
                                }

                        }

                        {$this->objeto_js}.evt__id_origen_recurso__procesar = function(es_inicial) 
			{
                                this.ef('id_origen_recurso').ocultar();
				switch (this.ef('id_origen_recurso').get_estado()) {
                              
					case '1':
                                            this.ef('proviene_de').ocultar();
                                            this.ef('organismo').ocultar();
                                            this.ef('nro_resol').ocultar();
                                            
						break;
                                        case '2': 
                                           /* this.ef('id_categ').ocultar();
                                            this.ef('id_vinc').ocultar();*/
                                            this.ef('proviene_de').ocultar();/*solo para f14*/
                                                break;
                                        case '3': /*f14*/
                                             /* this.ef('id_categ').ocultar();
                                            this.ef('id_vinc').ocultar(); */
                                            this.ef('organismo').ocultar();
                                            this.ef('nro_resol').ocultar();
                                            break;
                                        case '4': /*f21*/
                                                 /*this.ef('id_categ').ocultar();
                                            this.ef('id_vinc').ocultar();
                                           this.ef('organismo').ocultar();*/
                                            this.ef('nro_resol').ocultar();
                                            this.ef('proviene_de').ocultar();
                                            break;
                                        case '5': /*f22*/
                                                /* this.ef('id_categ').ocultar();
                                            this.ef('id_vinc').ocultar();
                                            this.ef('organismo').ocultar();*/
                                            this.ef('nro_resol').ocultar();
                                            this.ef('proviene_de').ocultar();
                                            break;
					default:
                                            this.ef('id_categ').ocultar();
                                            this.ef('id_vinc').ocultar();
                                            this.ef('organismo').ocultar();
                                            this.ef('nro_resol').ocultar();
                                            this.ef('proviene_de').ocultar();
					    this.ef('nro_cheque').ocultar();
                                            this.ef('id_banco').ocultar();
                                            this.ef('fecha_emision_cheque').ocultar();
                                            this.ef('alias').ocultar();	
                                            this.ef('cuenta_a_acreditar').ocultar();
                                            this.ef('nro_transferencia').ocultar();
                                            this.ef('cuil').ocultar();
                                            
						break;					
				}
			}
                       
			 {$this->objeto_js}.evt__id_condicion_venta__procesar = function(es_inicial) 
			{
                                var myvar=this.ef('id_condicion_venta').get_estado();
                                console.log(myvar);
				switch (this.ef('id_condicion_venta').get_estado()) {
                                
					case '1':
                                          this.ef('cuenta_a_acreditar').ocultar();
                                          this.ef('nro_transferencia').ocultar();
                                           this.ef('nro_cuil').ocultar();
                                            this.ef('nro_cheque').ocultar();
                                            this.ef('id_banco').ocultar();
                                            this.ef('fecha_emision_cheque').ocultar();
                                            /*this.ef('cuil1').ocultar();
                                            this.ef('cuil').ocultar();
                                            this.ef('cuil2').ocultar();*/
                                            this.ef('alias').ocultar();
                                            break;
                                        case '2': 
                                            this.ef('cuenta_a_acreditar').ocultar();
                                            this.ef('nro_transferencia').ocultar();
                                            this.ef('nro_cuil').ocultar();
                                            this.ef('nro_cheque').mostrar();
                                            this.ef('id_banco').mostrar();
                                            this.ef('fecha_emision_cheque').mostrar();
                                            /*this.ef('cuil1').ocultar();
                                            this.ef('cuil').ocultar();
                                            this.ef('cuil2').ocultar();*/
                                            this.ef('alias').ocultar();
                                            break;
                                        case '3': 
                                            this.ef('nro_cheque').ocultar();
                                            this.ef('id_banco').ocultar();
                                            this.ef('fecha_emision_cheque').ocultar();
                                            this.ef('cuenta_a_acreditar').mostrar();
                                            this.ef('nro_transferencia').mostrar();
                                             this.ef('nro_cuil').mostrar();
                                            break;
                                        default:/* por defecto no aparece nada*/
                                            this.ef('cuenta_a_acreditar').ocultar();
                                            this.ef('nro_transferencia').ocultar();
                                            this.ef('nro_cuil').ocultar();
                                            this.ef('nro_cheque').ocultar();
                                            this.ef('id_banco').ocultar();
                                            this.ef('fecha_emision_cheque').ocultar();
                                           
                                            this.ef('alias').ocultar();
                                             
                                            break;   
									
				}
			}
                      
                        ";
    }
}

?>