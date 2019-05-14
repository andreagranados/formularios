<?php
class dt_tipo_proviene_de extends toba_datos_tabla
{
	function get_descripciones()
	{
		$sql = "SELECT id_proviene, descripcion FROM tipo_proviene_de ORDER BY descripcion";
		return toba::db('formularios')->consultar($sql);
	}

}

?>