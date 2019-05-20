<?php                                                                       
class dt_recibo extends toba_datos_tabla                                     
{                                                                           
	 function get_listado_filtro($where=null){
            $condicion=' WHERE 1=1 ';
            if(!is_null($where)){
                    $condicion.=' and  '.$where;
                }
           $sql="select r.id_recibo,r.fecha,r.recibi_de,r.concepto,r.monto,r.estado,o.titulo as formul"
                   . " from (select * from recibo $condicion) r"
                   . " left outer join formulario f on (r.id_recibo=f.id_recibo)"
                    . " left outer join origen_ingreso o on (o.id_origen=f.id_origen_recurso)"
                    . " order by fecha";
            return toba::db('formularios')->consultar($sql);
         }
                                                                            
}                                                                           
                                                                            
?>                                                                          