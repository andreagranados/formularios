<?php                                                                       
class dt_recibo extends toba_datos_tabla                                     
{                                                                           
	 function get_listado_filtro($where=null){
            $condicion=' WHERE 1=1 ';
            if(!is_null($where)){
                    $condicion.=' and  '.$where;
                }
            $sql=" select * from recibo "
                    . " $condicion";
           
            return toba::db('formularios')->consultar($sql);
         }
                                                                            
}                                                                           
                                                                            
?>                                                                          