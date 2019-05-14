<?php
class dt_vinculacion extends toba_datos_tabla
{
	function get_descripciones()
	{
		$sql = "SELECT id_vinc, descripcion FROM vinculacion ORDER BY descripcion";
		return toba::db('formularios')->consultar($sql);
	}

}

?>