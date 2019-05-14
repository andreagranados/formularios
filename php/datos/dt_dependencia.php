<?php
class dt_dependencia extends toba_datos_tabla
{
	function get_descripciones()
	{
		$sql = "SELECT sigla, descripcion FROM dependencia ORDER BY descripcion";
		return toba::db('formularios')->consultar($sql);
	}
        function get_dependencias()
	{
		$sql="select sigla,descripcion from dependencia order by descripcion";
                $sql = toba::perfil_de_datos()->filtrar($sql);
            	return toba::db('formularios')->consultar($sql);
	}
}

?>