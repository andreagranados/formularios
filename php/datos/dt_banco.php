<?php
class dt_banco extends toba_datos_tabla
{
	function get_descripciones()
	{
		$sql = "SELECT id_banco, nombre FROM banco ORDER BY nombre";
		return toba::db('formularios')->consultar($sql);
	}

}

?>