<?php
class dt_programa_dependencia extends toba_datos_tabla
{
     function get_descripciones($id_dep=null)
	{
            $where='';
            if(isset($id_dep)){
                 $where=" and a.id_dependencia='".$id_dep."'";
             }
            $sql = "SELECT p.id_programa, p.nombre"
                    . " FROM programa_dependencia a, programa p , dependencia d"
                    . " where a.id_programa=p.id_programa"
                    . "  and a.id_dependencia=d.sigla ".$where
                    . " ORDER BY nombre";    
            $sql = toba::perfil_de_datos()->filtrar($sql);
            return toba::db('formularios')->consultar($sql);
	}
}
?>