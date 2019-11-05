<?php
class dt_programa extends toba_datos_tabla
{
    function get_descripciones()
	{
            $sql = "SELECT * FROM programa ORDER BY nombre";
            return toba::db('formularios')->consultar($sql);
	}
}
?>