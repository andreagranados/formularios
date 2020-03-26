<?php
class dt_punto_venta extends toba_datos_tabla
{
        function get_descripciones($id_dependencia=null)
	{
            $where ="";
            if(isset($id_dependencia)){
              $where=" WHERE id_dependencia='".$id_dependencia."'";        
             }
            $sql = "SELECT id_punto, '('||case when id_punto<=0 then 0 else id_punto end ||')'||descripcion as descripcion "
                    . " FROM punto_venta $where"
                    
                    . " ORDER BY descripcion";
           
            return toba::db('formularios')->consultar($sql);
	}
        function get_listado($where=null){
              
            $condicion='';
            if(!is_null($where)){
                $condicion=' WHERE '.$where;
            }
            $sql="select *,case when id_punto<=0 then 0 else id_punto end as pv from punto_venta $condicion"
                    . " order by id_dependencia";
            return toba::db('formularios')->consultar($sql);
        }

}

?>