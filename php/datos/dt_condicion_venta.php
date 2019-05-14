<?php
class dt_condicion_venta extends toba_datos_tabla
{
	function get_descripciones()
	{
		$sql = "SELECT id_cond, descripcion FROM condicion_venta ORDER BY descripcion";
		return toba::db('formularios')->consultar($sql);
	}

}

?>