<?php
class dt_cuenta_bancaria extends toba_datos_tabla
{
	function get_descripciones()
	{
		$sql = "SELECT id_cuenta, descripcion FROM cuenta_bancaria ORDER BY descripcion";
		return toba::db('formularios')->consultar($sql);
	}
        function get_cuentas()
	{
		$sql = "SELECT id_cuenta, t_c.nro_cuenta||'('||t_c.descripcion||')' as descripcion FROM cuenta_bancaria t_c"
                        . " INNER JOIN dependencia t_d ON t_d.sigla=t_c.id_dependencia "
                        ;
                $sql = toba::perfil_de_datos()->filtrar($sql);
                $sql=$sql
                          . " UNION "
                        . " SELECT id_cuenta, t_cc.nro_cuenta||'('||t_cc.descripcion||')' as descripcion "
                        . " FROM cuenta_bancaria t_cc"
                        . " WHERE t_cc.id_dependencia='RECT'";
                
		return toba::db('formularios')->consultar($sql);
	}
        function get_listado($where=null){
              
            $condicion='';
            if(!is_null($where)){
                $condicion=' WHERE '.$where;
            }
            $sql="select c.*,b.nombre as banco from cuenta_bancaria c"
                    . " inner join banco b on c.id_banco=b.id_banco $condicion";
            return toba::db('formularios')->consultar($sql);
        }

}

?>