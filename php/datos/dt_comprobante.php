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

}

?>