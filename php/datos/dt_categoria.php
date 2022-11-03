<?php
class dt_categoria extends toba_datos_tabla
{
	function get_descripciones()
	{
            $sql = "SELECT id_categoria, id_categoria||'-'||descripcion as descripcion FROM categoria ORDER BY id_categoria";
            return toba::db('formularios')->consultar($sql);
	}
        function get_categoria_otra(){
            $sql = "SELECT id_categoria, id_categoria||'-'||descripcion as descripcion "
                    . " FROM categoria WHERE id_categoria=8"
                    . " ORDER BY id_categoria";
            
            return toba::db('formularios')->consultar($sql);
            
            //return $resul;//Array ( [0] => Array ( [id_categoria] => 8 [descripcion] => 8-Otros ) )
        }
                
}
?>