<?php
class dt_condicion_venta extends toba_datos_tabla
{
	function get_descripciones()
	{
            $sql = "SELECT id_cond, descripcion FROM condicion_venta ORDER BY descripcion";
            return toba::db('formularios')->consultar($sql);
	}
        function get_condiciones($id_form){
            $sql="select case when d.tipo_dep=1 then 1 else 0 end as tipo from formulario f"
                    . " inner join dependencia d on (f.id_dependencia=d.sigla)"
                    . " where f.id_form=$id_form";
            $resul= toba::db('formularios')->consultar($sql);
            if($resul[0]['tipo']==0){//no es dependencia
                $sql="SELECT id_cond, descripcion FROM condicion_venta WHERE id_cond<>1 ORDER BY descripcion";
            }else{
                $sql="SELECT id_cond, descripcion FROM condicion_venta WHERE id_cond<>1 ORDER BY descripcion";
            }
            return toba::db('formularios')->consultar($sql);
        }

}

?>