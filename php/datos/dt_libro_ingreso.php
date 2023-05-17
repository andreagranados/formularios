<?php
class dt_libro_ingreso extends toba_datos_tabla
{
    function get_descripciones()
	{
            $sql = "SELECT distinct anio,numero,case when cerrado then 'SI' else 'NO' end as cerrado "
                    . " FROM libro_ingreso "
                    . " ORDER BY ANIO DESC";
                    
            return toba::db('formularios')->consultar($sql);
	}
    function get_libros_abiertos()
	{
            $sql = "SELECT distinct anio  FROM libro_ingreso where not cerrado ";
            return toba::db('formularios')->consultar($sql);
	}    
    function esta_cerrado($anio)
    {
        $sql = "SELECT cerrado FROM libro_ingreso where anio= ".$anio;
        $resp = toba::db('formularios')->consultar($sql);
        if($resp[0]['cerrado']){
            return true;
        }else{
            return false;
        }
    }
    function get_listado()//trae solo los años
	{
            $sql = "SELECT distinct anio FROM libro_ingreso order by anio desc";
            return toba::db('formularios')->consultar($sql);
	}
}?>