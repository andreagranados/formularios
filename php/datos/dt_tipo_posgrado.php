<?php
class dt_tipo_posgrado extends toba_datos_tabla
{
	function get_descripciones()
	{
		$sql = "SELECT id_tipo,descripcion FROM tipo_posgrado ORDER BY descripcion";
		return toba::db('formularios')->consultar($sql);
	}

}

?>