<?php
class dt_modalidad_pago extends toba_datos_tabla
{
    
    function cambiar_adj($id,$valor){
        $sql="update modalidad_pago set archivo_trans='".$valor."' where id_mod=".$id;
        toba::db('formularios')->consultar($sql);
    }
    function get_listado($id_form){
        $sql="select t_m.id_mod,t_m.id_form,trim(t_o.descripcion) as condicion,case when t_m.id_condicion_venta=2 then 'Nro Cheque: '||cast(nro_cheque as text)||' '||t_b.nombre||' (Fecha: '||cast(to_char(t_m.fecha_emision_cheque, 'DD/MM/YYYY') as text)||')' else case when t_m.id_condicion_venta=3 then 'Nro transf.: '||cast(nro_transferencia as text)||' CBU Cuenta: '||t_cu.cbu||' '||t_ba.nombre||coalesce(' CUIL/T:'||cuil1||'-'||lpad(cast(cuil as text),8,'0')||'-'||cuil2,'')  else '' end end as detalle, monto,"
                ." case when  (archivo_trans is null or archivo_trans='') then '' else "."'<a href='||chr(39)||'/adjuntos/'||archivo_trans||chr(39)|| ' target='||chr(39)||'_blank'||chr(39)||'>'||archivo_trans||'</a>' "." end as comprob_trans"
                
                . " from modalidad_pago t_m"
                . " inner join condicion_venta t_o on (t_m.id_condicion_venta =t_o.id_cond)"
                . " left outer join banco t_b on (t_m.id_banco =t_b.id_banco)"
                . " left outer join cuenta_bancaria t_cu on (t_m.cuenta_a_acreditar=t_cu.id_cuenta) "
                . " left outer join banco t_ba on (t_ba.id_banco=t_cu.id_banco) "
                . " where t_m.id_form=$id_form";
        return toba::db('formularios')->consultar($sql);
    }
    function no_repite_cheque($nro_cheque){//retorna true sino se repite
        $sql="select * from modalidad_pago where nro_cheque=".$nro_cheque;
        $resul = toba::db('formularios')->consultar($sql);
        if(count($resul)>0){
            return false;
        }else{
            return true;
        }
    }
   
    function no_repite_transferencia($nro_transf){//retorna true sino se repite
        $sql="select * from modalidad_pago where nro_transferencia=".$nro_transf;
        $resul = toba::db('formularios')->consultar($sql);
        if(count($resul)>0){
            return false;
        }else{
            return true;
        }
    }
}?>