<?php
class dt_libro_ingreso extends toba_datos_tabla
{
    function get_descripciones()
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
}?>