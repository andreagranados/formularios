<?php
class dt_origen_ingreso extends toba_datos_tabla
{
	function get_descripciones()
	{
		$sql = "SELECT id_origen, descripcion FROM origen_ingreso ORDER BY descripcion";
		return toba::db('formularios')->consultar($sql);
	}
	function get_titulos()
	{
		$sql = "SELECT id_origen, titulo FROM origen_ingreso ORDER BY titulo";
		return toba::db('formularios')->consultar($sql);
	}
        function get_grupos_fuente($id_origen){
            $where ="";
            if(isset($id_origen)){
              $where=" WHERE id_origen=$id_origen";        
             }
            $sql = "SELECT sub.id_origen,sub.id_grupo,'('||lpad(sub.id_grupo::text,4,'0')||')'||g.descripcion as descripcion
                    FROM(SELECT id_origen,unnest(id_grupos) as id_grupo
                    FROM origen_ingreso b
                    $where)sub, grupo_presupuestario g
                    WHERE sub.id_grupo=g.id_grupo 
                    ORDER BY sub.id_origen,sub.id_grupo"
                    ;
            return toba::db('formularios')->consultar($sql);
        }
}

?>