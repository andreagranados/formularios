<?php
class dt_punto_venta extends toba_datos_tabla
{
	function get_descripciones($id_dependencia=null)
	{
            $where ="";
            if(isset($id_dependencia)){
              $where=" WHERE id_dependencia='".$id_dependencia."'";        
             }
            $sql = "SELECT id_punto, '('||id_punto||')'||descripcion as descripcion "
                    . " FROM punto_venta $where"
                    
                    . " ORDER BY descripcion";
           
            return toba::db('formularios')->consultar($sql);
	}

}

?>