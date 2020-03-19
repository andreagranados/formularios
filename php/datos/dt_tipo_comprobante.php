<?php
class dt_tipo_comprobante extends toba_datos_tabla
{
	function get_descripciones()
	{
		$sql = "SELECT id_tipo, descripcion FROM tipo_comprobante ORDER BY descripcion";
		return toba::db('formularios')->consultar($sql);
	}

}

?>