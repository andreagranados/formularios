<?php
class dt_comprobante extends toba_datos_tabla
{
    
        function importar($datos=array()){
            foreach ($datos as $key => $value) {
                $sql="insert into comprobante (id_punto_venta,nro_comprobante,fecha_emision,total,id_condicion_venta,estado)".
                     "values(".$value['id_punto_venta'].",".$value['nro_comprobante'].",'".$value['fecha_emision']."',".$value['total'].",1,'I'".")"; 
                toba::db('formularios')->consultar($sql);
            }
        }
	function get_descripciones()
	{
            $sql = "SELECT id_comprob, nro_comprobante FROM comprobante ORDER BY estado";
            return toba::db('formularios')->consultar($sql);
	}
        function get_comprobantes($id_punto){
            $sql = "SELECT id_comprob, nro_comprobante "
                    . " FROM comprobante "
                    . " WHERE id_punto_venta=$id_punto "
                    
                    . " order by id_comprob";
            return toba::db('formularios')->consultar($sql);
        }
        function esta_repetido($id_comp){//retorna true cuando esta repetido
            //verifico si existe algun item con el numero de comprobante que intenta dar de alta
            $sql = " select * from item i "
                   . " where i.id_comprobante=$id_comp ";
            $resul= toba::db('formularios')->consultar($sql); 
            if(count($resul)>0){
                return true ;  
            }else{
                return false;
            }
        }
        
        
//        function get_monto($id_comprobante){
//            $sql = "SELECT total "
//                    . " FROM comprobante "
//                    . " WHERE id_comprob=$id_comprobante ";
//            $resul= toba::db('formularios')->consultar($sql);
//            if(count($resul)>0){
//                return $resul[0]['total'];
//            }else{
//                return 0;
//            }
//        }
        function get_monto($id_comprobante){
           
            if(isset($id_comprobante)){
                $sql = "SELECT total "
                    . " FROM comprobante "
                    . " WHERE id_comprob=$id_comprobante ";
                $resul= toba::db('formularios')->consultar($sql);
                if(count($resul)>0){
                    return $resul[0]['total'];
                }else{
                    return 0;
                }
            }else{
                return 0;
            }
        }
        function get_comprobantes_rendidos($where=null){
            $condicion=' WHERE 1=1 ';
            if(!is_null($where)){
                    $condicion.=' and  '.$where;
                }
            $sql="select * from (select t_c.fecha_emision,t_p.id_dependencia,t_d.descripcion as dependencia,t_p.id_punto,extract(year from t_c.fecha_emision )as anio,extract(month from t_c.fecha_emision )as mes,extract(day from t_c.fecha_emision )as dia,lpad(cast(t_p.id_punto as text),6,'0')||'-'||lpad(cast(t_c.nro_comprobante as text),8,'0') as nro_comprobante,case when sub.id_comprob is null then 'R' else 'N' end as rendido
                    from comprobante t_c
                    inner join punto_venta t_p on  (t_p.id_punto=t_c.id_punto_venta)
                    inner join dependencia t_d on (t_d.sigla=t_p.id_dependencia)
                    left outer join (select c.id_comprob 
                                     from item t_i 
                                     inner join comprobante c on (c.id_comprob=t_i.id_comprobante)
                                       ) sub on (sub.id_comprob=t_c.id_comprob)
                                       )sub
                                       $condicion"
                    . " order by rendido,dependencia,anio,mes,dia ";
            
            return toba::db('formularios')->consultar($sql);
        }

}

?>