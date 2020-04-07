<?php
class dt_item extends toba_datos_tabla
{
    function importar($datos_item=array(),$datos=array()){//$datos tiene los datos comunes a todos los items
       // print_r($datos);exit;//Array ( [id_form] => 64 [id_categ] => 8 [id_vinc] => 2 [detalle] => sas [anio] => 2019 [tipo_comprob] => 11 [desde] => 437 [hasta] => 441 ) 
       // print_r($datos_item);exit;
            foreach ($datos_item as $key => $value) {
                 $sql="INSERT INTO item(
               id_form, id_categ, id_vinc, proviene_de, nro_resol, 
                organismo, id_condicion_venta, nro_cheque, id_banco, fecha_emision_cheque, 
                nro_transferencia, cuil1, cuil, cuil2, alias, detalle, monto, 
                cuenta_a_acreditar, trans_proviene_de, id_comprobante, tipo_posg)
            VALUES ( ".$datos['id_form'].",". $datos['id_categ'].",". $datos['id_vinc'].", null, null, 
                    null, null, null, null, null, 
                    null, null, null, null, null, '".$datos['detalle']."',".$value['total'].", 
                    null, null,". $value['id_comprob'].", null);
            ";
            toba::db('formularios')->consultar($sql);
        } 
    }
    function get_listado($id_form){
        //si es F12 y la categoria tiene retencion entonces calculo porcentaje
        $sql="select sub.*,t_t.total,case when id_origen_recurso=1 and tiene_retencion then trunc(t_t.total*porc_retencion/100,2)  else 0 end  as retencion from "
                . "(select lpad(cast(t_f.id_punto_venta as text),5,'0')||'-'||lpad(cast(t_co.nro_comprobante as text),8,'0') as nro_factura,trim(t_pd.descripcion) as proviene_descrip,t_i.id_form,trim(t_i.nro_resol) as nro_resol,trim(t_i.organismo)as organismo,t_c.tiene_retencion,t_p.porc_retencion,t_i.id_item,t_f.id_origen_recurso,t_i.id_condicion_venta,trim(t_i.detalle) as detalle,trim(t_c.descripcion)||coalesce(case when t_i.tipo_posg is not null then '('||t_po.descripcion||')' else '' end) as categ,trim(t_o.descripcion) as condicion_venta, case when t_i.id_condicion_venta=2 then 'Nro cheque: '||cast(nro_cheque as text)||' '||t_b.nombre else case when t_i.id_condicion_venta=3 then 'Nro transf.: '||cast(nro_transferencia as text)||' CBU Cuenta: '||t_cu.cbu||' '||t_ba.nombre||coalesce(' CUIL/T:'||cuil1||'-'||lpad(cast(cuil as text),8,'0')||'-'||cuil2,'')  else '' end end as condicion_venta2, trim(t_v.descripcion) as vinc, t_i.monto"
                . " from item t_i "
                . " left outer join comprobante t_co on (t_co.id_comprob=t_i.id_comprobante)"
                 . " left outer join tipo_proviene_de t_pd on (t_pd.id_proviene=t_i.proviene_de)"
                . " left outer join formulario t_f on (t_f.id_form=t_i.id_form)"
                . " left outer join punto_venta t_p on (t_f.id_punto_venta=t_p.id_punto)"
                . " left outer join categoria t_c on (t_i.id_categ =t_c.id_categoria)"
                . " left outer join tipo_posgrado t_po on (t_i.tipo_posg  =t_po.id_tipo)"
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
    function get_totales($where=null){
        $condicion=' WHERE 1=1 ';
        if(!is_null($where)){
                    $condicion.=' and  '.$where;
                }
        $pd = toba::manejador_sesiones()->get_perfil_datos(); 
        $con="select sigla from dependencia ";
        $con = toba::perfil_de_datos()->filtrar($con);
        $resul=toba::db('formularios')->consultar($con);
       
        if(isset($pd)){//pd solo tiene valor cuando el usuario esta asociado a un perfil de datos
                $condicion.=" and id_dependencia = ".quote($resul[0]['sigla']);
                }//sino es usuario de la central no filtro a menos que haya elegido
       //f12 categorias con retencion --> tiene retencion                 
//        $sql="select dependencia,case when id_punto<=0 then 0 else id_punto end as id_punto,total as total_bruto,retencion, total-retencion as total_neto from 
//            (select dependencia,id_punto,sum(total) as total,sum(retencion)as retencion from
//            (select dependencia,id_form,id_punto,total, case when id_origen_recurso=1 and tiene_retencion then trunc(total*porc_retencion/100,2)  else 0 end  as retencion from 
//                (select distinct t_f.id_dependencia,t_d.descripcion as dependencia,ano_cobro as anio,t_f.id_form,t_p.id_punto,t_p.porc_retencion,t_f.id_origen_recurso,t_c.tiene_retencion as tiene_retencion,total
//                from item t_i
//                inner join formulario t_f on (t_i.id_form=t_f.id_form)
//                inner join punto_venta t_p on (t_f.id_punto_venta=t_p.id_punto)
//                inner join dependencia t_d on (t_d.sigla=t_f.id_dependencia)
//                inner join categoria t_c on (t_i.id_categ =t_c.id_categoria)
//                left outer join (select t_it.id_form,sum(monto) as total from item t_it
//                                            group by t_it.id_form) t_t on (t_t.id_form=t_f.id_form)
//               where t_f.estado<>'N'
//                )sub    $condicion
//                           )sub2 
//                           group by dependencia,id_punto )sub3";
        $sql="select dependencia,id_punto,case when id_punto<=0 then 0 else id_punto end as pv,desc_punto,total as total_bruto,retencion, total-retencion as total_neto from 
            (select dependencia,id_punto,desc_punto, sum(total) as total,sum(retencion)as retencion from
            (select dependencia, id_form, id_punto, desc_punto, total, case when id_origen_recurso=1 and tiene_retencion then trunc(total*porc_retencion/100,2)  else 0 end  as retencion from 
                (select distinct t_f.id_dependencia,t_d.descripcion as dependencia,ano_cobro as anio,t_f.id_form,t_p.id_punto,t_p.descripcion as desc_punto,t_p.porc_retencion,t_f.id_origen_recurso,t_c.tiene_retencion as tiene_retencion,total
                from item t_i
                inner join formulario t_f on (t_i.id_form=t_f.id_form)
                inner join punto_venta t_p on (t_f.id_punto_venta=t_p.id_punto)
                inner join dependencia t_d on (t_d.sigla=t_f.id_dependencia)
                inner join categoria t_c on (t_i.id_categ =t_c.id_categoria)
                left outer join (select t_it.id_form,sum(monto) as total from item t_it
                                            group by t_it.id_form) t_t on (t_t.id_form=t_f.id_form)
               where t_f.estado<>'N'
                )sub    $condicion
                           )sub2 
                           group by dependencia,id_punto ,desc_punto
                           )sub3";        
        return toba::db('formularios')->consultar($sql);
    }
    function get_extracontable($where=null){
        $condicion=" WHERE estado<>'N' ";
        if(!is_null($where)){
              $condicion.=' and  '.$where;
         }
        // print_r($condicion);
        $sql="select *,monto-retencion as neto from (select distinct t_f.id_dependencia,t_f.nro_expediente,t_pr.id_programa,lpad(cast(t_pr.id_programa as text),2,'0') as prog,t_f.id_origen_recurso,t_o.titulo as fuente,ano_cobro as anio,t_f.id_form,t_p.id_punto,case when t_p.id_punto<=0 then 0 else id_punto end as pv, t_p.descripcion as desc_punto,
        CASE WHEN t_f.id_origen_recurso=1 and t_c.tiene_retencion THEN 'SI' ELSE 'NO' END as tiene_reten,CASE WHEN t_f.id_origen_recurso=1 and t_c.tiene_retencion THEN trunc(t_i.monto*t_p.porc_retencion/100,2) ELSE 0 END as retencion,t_f.ano_cobro,case when t_f.id_dependencia='FAIN' then case when t_f.nro_ingreso is not null then 'SI' else 'NO' end else case when t_f.pasado_pilaga then 'SI' else 'NO' end end  as pasado_pila,
    case when t_p.id_punto > 0 then lpad(cast(t_p.id_punto as text),5,'0')||'-'||lpad(cast(t_co.nro_comprobante as text),8,'0') else '' end as nro_comprobante,t_i.monto,lpad(cast(nro_ingreso as text),4,'0')||'/'||anio_ingreso as nro_ingreso,
    t_tc.descripcion as tipo_comprob, t_f.estado,
    case when t_f.id_origen_recurso=2 then 'Norma: '||t_i.nro_resol||' Organismo: '||t_i.organismo else case when t_f.id_origen_recurso=3 then t_t.descripcion else case when t_f.id_origen_recurso=4 or t_f.id_origen_recurso=5 then ' Organismo: '||t_i.organismo else '' end end end as otros_datos,
    case when t_f.id_programa=40 then t_pos.descripcion else '' end as posgrado, t_i.detalle,t_cv.descripcion as cond_venta,
    case when t_i.id_condicion_venta=2 then 'Nro cheque: '||cast(nro_cheque as text)||' '||t_b.nombre else case when t_i.id_condicion_venta=3 then 'Nro transf: '||cast(nro_transferencia as text)||' CBU Cuenta: '||t_cu.cbu||' '||t_ba.nombre||coalesce(' CUIL/T:'||cuil1||'-'||lpad(cast(cuil as text),8,'0')||'-'||cuil2,'')  else '' end end as cond_venta2,t_e.descripcion as desc_exp
            from item t_i
            inner join formulario t_f on (t_i.id_form=t_f.id_form)
            inner join expediente t_e on (t_f.nro_expediente=t_e.nro_expediente)
            inner join origen_ingreso t_o on (t_f.id_origen_recurso=t_o.id_origen)
            inner join programa t_pr on (t_f.id_programa=t_pr.id_programa)
            inner join punto_venta t_p on (t_f.id_punto_venta=t_p.id_punto)
            inner join dependencia t_d on (t_d.sigla=t_f.id_dependencia)
            inner join categoria t_c on (t_i.id_categ =t_c.id_categoria)
            left outer join comprobante t_co on (t_i.id_comprobante =t_co.id_comprob)
            left outer join tipo_comprobante t_tc on (t_tc.id_tipo =t_co.tipo)
            left outer join tipo_proviene_de t_t on (t_i.proviene_de=t_t.id_proviene)
            left outer join tipo_posgrado t_pos on (t_i.tipo_posg=t_pos.id_tipo)
            left outer join condicion_venta t_cv on (t_cv.id_cond=t_i.id_condicion_venta)
            left outer join banco t_b on (t_i.id_banco =t_b.id_banco)
            left outer join cuenta_bancaria t_cu on (t_i.cuenta_a_acreditar=t_cu.id_cuenta) 
            left outer join banco t_ba on (t_ba.id_banco=t_cu.id_banco)
            )sub
          $condicion "
                . " order by ano_cobro,id_dependencia,id_form"
                    ;
        return toba::db('formularios')->consultar($sql);
        
    }
    
    
}?>