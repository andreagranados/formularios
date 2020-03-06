<?php                                                                       
class dt_recibo extends toba_datos_tabla                                     
{                                                                           
	 function get_listado_filtro($where=null){
            $condicion=' WHERE 1=1 ';
            if(!is_null($where)){
                    $condicion.=' and  '.$where;
                }
           $sql="select * from (select r.id_recibo,r.fecha,r.recibi_de,r.concepto,r.monto,r.estado, extract(year from r.fecha)as anio,o.titulo||'('||f.nro_ingreso||'/'||f.anio_ingreso||')' as formul, case when o.titulo is null then false else true end as de_formulario"
                   . " from recibo r"
                   . " left outer join formulario f on (r.id_recibo=f.id_recibo)"
                    . " left outer join origen_ingreso o on (o.id_origen=f.id_origen_recurso))sub"
                   . " $condicion"
                    . " order by id_recibo desc";
            return toba::db('formularios')->consultar($sql);
         }
         function get_anios(){
             $sql="select distinct extract(year from fecha) as anio from recibo ";
             return toba::db('formularios')->consultar($sql);
         }
         function asociado_formulario($id_recibo){
             $sql="select * from formulario "
                     . " where id_recibo=".$id_recibo;
             $resul= toba::db('formularios')->consultar($sql);
             $salida=array();
             if(count($resul)>0){
                 $salida[0]=$resul[0]['id_form'];
                 $salida[1]=$resul[0]['nro_ingreso'].'/'.$resul[0]['anio_ingreso'];
             }
             return $salida;
         }
                                                                            
}                                                                           
                                                                            
?>                                                                          