<?php
class dt_categoria extends toba_datos_tabla
{
	function get_descripciones()
	{
		$sql = "SELECT id_categoria, id_categoria||'-'||descripcion as descripcion FROM categoria ORDER BY id_categoria";
		return toba::db('formularios')->consultar($sql);
	}

}

?>