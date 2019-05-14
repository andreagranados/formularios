<?php
class dt_estado_form extends toba_datos_tabla
{
	function get_descripciones()
	{
		$sql = "SELECT id_estado, descripcion FROM estado_form ORDER BY descripcion";
		return toba::db('formularios')->consultar($sql);
	}

}

?>