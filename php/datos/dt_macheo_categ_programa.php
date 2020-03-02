<?php
class dt_macheo_categ_programa extends toba_datos_tabla
{
    function get_categorias($id_prog=null)
	{
            $where='';
            if(isset($id_prog)){
              $where=" and  id_programa=".$id_prog;
            }
            $sql = "SELECT b.id_categoria, b.id_categoria||'-'||b.descripcion as descripcion"
                    . " FROM macheo_categ_programa a, categoria b"
                    . " where a.id_categoria=b.id_categoria "
                    . $where
                    . " ORDER BY descripcion";
            return toba::db('formularios')->consultar($sql);
	}
}
?>