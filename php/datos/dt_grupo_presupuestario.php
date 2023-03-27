<?php
class dt_grupo_presupuestario extends toba_datos_tabla
{
        function get_descripciones()
	{
		$sql = "SELECT id_grupo,'('||lpad(id_grupo::text,4,'0')||')'||descripcion as descripcion"
                        . " FROM grupo_presupuestario ORDER BY id_grupo";
		return toba::db('formularios')->consultar($sql);
	}
}
?>