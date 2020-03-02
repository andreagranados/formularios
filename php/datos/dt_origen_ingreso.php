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
}

?>