<?php
class dt_programa_dependencia extends toba_datos_tabla
{
     function get_descripciones($id_dep=null)
	{
            $where='';
            if(isset($id_dep)){
                 $where=" and id_dependencia='".$id_dep."'";
             }
            $sql = "SELECT p.id_programa, p.nombre"
                    . " FROM programa_dependencia a, programa p "
                    . " where a.id_programa=p.id_programa".$where
                    . " ORDER BY nombre";            
            return toba::db('formularios')->consultar($sql);
	}
}
?>