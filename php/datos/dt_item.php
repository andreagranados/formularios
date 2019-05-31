<?php
class dt_item extends toba_datos_tabla
{
    function get_listado($id_form){
        //si es F12 y la categoria tiene retencion entonces calculo porcentaje
        $sql="select sub.*,t_t.total,case when id_origen_recurso=1 and tiene_retencion then trunc(t_t.total*porc_retencion/100,2)  else 0 end  as retencion from "
                . "(select lpad(cast(t_f.id_punto_venta as text),5,'0')||'-'||lpad(cast(t_co.nro_comprobante as text),8,'0') as nro_factura,trim(t_pd.descripcion) as proviene_descrip,t_i.id_form,t_i.nro_resol,t_i.organismo,t_c.tiene_retencion,t_p.porc_retencion,t_i.id_item,t_f.id_origen_recurso,t_i.id_condicion_venta,t_i.detalle,trim(t_c.descripcion) as categ,trim(t_o.descripcion) as condicion_venta, case when t_i.id_condicion_venta=2 then 'Nro cheque: '||cast(nro_cheque as text)||' '||t_b.nombre else case when t_i.id_condicion_venta=3 then 'Nro transferencia: '||cast(nro_transferencia as text)||' CBU Cuenta: '||t_cu.cbu||' '||t_ba.nombre  else '' end end as condicion_venta2, trim(t_v.descripcion) as vinc, t_i.monto"
                . " from item t_i "
                . " left outer join comprobante t_co on (t_co.id_comprob=t_i.id_comprobante)"
                 . " left outer join tipo_proviene_de t_pd on (t_pd.id_proviene=t_i.proviene_de)"
                . " left outer join formulario t_f on (t_f.id_form=t_i.id_form)"
                . " left outer join punto_venta t_p on (t_f.id_punto_venta=t_p.id_punto)"
                . " left outer join categoria t_c on (t_i.id_categ =t_c.id_categoria)"
                . " left outer join vinculacion t_v on (t_i.id_vinc =t_v.id_vinc)"
                . " left outer join condicion_venta t_o on (t_i.id_condicion_venta =t_o.id_cond)"
                . " left outer join banco t_b on (t_i.id_banco =t_b.id_banco)"
                . " left outer join cuenta_bancaria t_cu on (t_i.cuenta_a_acreditar=t_cu.id_cuenta) "
                . " left outer join banco t_ba on (t_ba.id_banco=t_cu.id_banco) "
                . " where t_i.id_form=$id_form "
                . ")sub"
                . " left outer join (select id_form,sum(monto) as total from item "
                . "                  where id_form=$id_form"
                . "                  group by id_form) t_t on (t_t.id_form=sub.id_form)";
        return toba::db('formularios')->consultar($sql);
    }
    function get_totales($filtro=array()){
        $where ="";
        if (isset($filtro['anio'])) {
                $where = "  where extract(year from fecha_creacion)= ".$filtro['anio']['valor'];
        }
        //print_r($where);
        $sql="select dependencia,total as total_bruto,retencion, total-retencion as total_neto from 
            (select dependencia,sum(total) as total,sum(retencion)as retencion from
            (select dependencia,id_form,total, case when id_origen_recurso=1 and tiene_retencion then trunc(total*porc_retencion/100,2)  else 0 end  as retencion from 
            (select distinct t_d.descripcion as dependencia,t_f.id_form,t_p.porc_retencion,t_f.id_origen_recurso,CASE WHEN t_i.id_categ is null THEN false ELSE t_c.tiene_retencion END as tiene_retencion,total
            from item t_i
            inner join formulario t_f on (t_i.id_form=t_f.id_form)
            inner join punto_venta t_p on (t_f.id_punto_venta=t_p.id_punto)
            inner join dependencia t_d on (t_d.sigla=t_f.id_dependencia)
            left outer join categoria t_c on (t_i.id_categ =t_c.id_categoria)
            left outer join (select t_it.id_form,sum(monto) as total from item t_it
                                        group by t_it.id_form) t_t on (t_t.id_form=t_f.id_form)
           $where
            )sub    
                           )sub2 
                           group by dependencia )sub3";
        return toba::db('formularios')->consultar($sql);
    }
    function no_repite_cheque($nro_cheque){//retorna true sino se repite
        $sql="select * from item where nro_cheque=".$nro_cheque;
        $resul = toba::db('formularios')->consultar($sql);
        if(count($resul)>0){
            return false;
        }else{
            return true;
        }
    }
    
    function no_repite_transferencia($nro_transf){//retorna true sino se repite
        $sql="select * from item where nro_transferencia=".$nro_transf;
        $resul = toba::db('formularios')->consultar($sql);
        if(count($resul)>0){
            return false;
        }else{
            return true;
        }
    }
}?>