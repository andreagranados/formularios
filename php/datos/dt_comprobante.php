<?php
class dt_comprobante extends toba_datos_tabla
{
        function get_anios(){
            $sql="select distinct extract(year from fecha_emision) as anio"
                    . " from comprobante";
            return toba::db('formularios')->consultar($sql);
        }
        function get_comprobantes_desde_hasta($id_form,$tipo_comp,$anio,$desde,$hasta){
           
            $sql=" select t_c.*,t_t.descripcion as tipo_c,
                 lpad(cast(t_c.id_punto_venta as text),5,'0')||'-'||lpad(cast(t_c.nro_comprobante as text),8,'0') as nro_factura 
                from comprobante t_c
                left outer join tipo_comprobante t_t on (t_c.tipo=t_t.id_tipo)
                    where extract(year from fecha_emision)=".$anio
                    ." and tipo=$tipo_comp
                    and id_punto_venta in (select id_punto_venta from formulario where id_form=$id_form)
                    and nro_comprobante>=(select nro_comprobante from comprobante where id_comprob=$desde)
                    and nro_comprobante<=(select nro_comprobante from comprobante where id_comprob=$hasta)
                    and t_c.id_comprob not in  (select id_comprobante from item b, formulario f
                                                where f.estado<>'N'
                                                and b.id_form=f.id_form
                                                and b.id_comprobante is not null)";
           
            return toba::db('formularios')->consultar($sql);

        }
        function importar($datos=array()){
            foreach ($datos as $key => $value) {
                $sql="insert into comprobante (id_punto_venta,nro_comprobante,fecha_emision,total,id_condicion_venta,estado,tipo)".
                     "values(".$value['id_punto_venta'].",".$value['nro_comprobante'].",'".$value['fecha_emision']."',".$value['total'].",1,'I',".$value['tipo'].")"; 
                toba::db('formularios')->consultar($sql);
            }
        }
	function get_descripciones()
	{
            $sql = "SELECT id_comprob, nro_comprobante FROM comprobante ORDER BY estado";
            return toba::db('formularios')->consultar($sql);
	}
        function get_comprobantes_rango($id_punto,$ano,$tipo){
           
             $sql = "SELECT * from "
                    . " (SELECT id_comprob,c.tipo,c.fecha_emision,t.desc_corta||'('||c.tipo||')'||'-'||lpad(cast(nro_comprobante as text),8,'0')||'('||to_char(fecha_emision,'DD/MM/YYYY')||')' as nro_comprobante"
                    . " FROM comprobante c"
                    . " LEFT OUTER JOIN tipo_comprobante t on (c.tipo=t.id_tipo) "
                    . " WHERE c.id_punto_venta=$id_punto
                     and ( (extract(year from c.fecha_emision)<=$ano))"// or (extract(year from c.fecha_emision)=$ano and extract(month from c.fecha_emision)<=$mes))"
                    . " and not exists (select * from comprobante t_c" //todos menos los comprobantes de ese punto venta asociados a items (ya rendidos) de formularios no anulados
                    . "                  inner join item t_i on (t_c.id_comprob=t_i.id_comprobante)"
                    . "                  inner join formulario t_f on (t_f.id_form =t_i.id_form)"
                    . "                 where "
                    . "                      t_f.id_punto_venta=$id_punto
                                           and t_c.id_comprob=c.id_comprob
                                           and t_f.estado<>'N'
                                           )"
                    ." and tipo=".$tipo
                    .") sub"
                    . " order by tipo,fecha_emision,nro_comprobante";
            return toba::db('formularios')->consultar($sql);
        }
        function get_comprobantes($id_punto,$ano,$id_comprob){
            //las facturas se cobran en forma posterior a la emision, por lo tanto busco los comprobantes cuya
            
            //toma el mes y año de cobro de las facturas
//            $d=strtotime($fecha_creac);
//            $a=date("Y", $d);
//            $b=$a-1;
//            $fec=date("d/m/Y",strtotime($fecha_creac));
            if($id_comprob<>0){
                $concatenar=" UNION select id_comprob,c.tipo,c.fecha_emision, t.desc_corta||'('||c.tipo||')'||'-'||lpad(cast(nro_comprobante as text),8,'0')||'('||to_char(fecha_emision,'DD/MM/YYYY')||')' as nro_comprobante "
                        . "    from comprobante c "
                        . "    LEFT OUTER JOIN tipo_comprobante t ON (c.tipo=t.id_tipo) "
                        . "    where id_comprob=".$id_comprob;
            }else{
                $concatenar='';
            }
        
            $sql = "SELECT * from "
                    . " (SELECT id_comprob,c.tipo,c.fecha_emision,t.desc_corta||'('||c.tipo||')'||'-'||lpad(cast(nro_comprobante as text),8,'0')||'('||to_char(fecha_emision,'DD/MM/YYYY')||')' as nro_comprobante"
                    . " FROM comprobante c"
                    . " LEFT OUTER JOIN tipo_comprobante t on (c.tipo=t.id_tipo) "
                    . " WHERE c.id_punto_venta=$id_punto
                     and ( (extract(year from c.fecha_emision)<=$ano))"// or (extract(year from c.fecha_emision)=$ano and extract(month from c.fecha_emision)<=$mes))"
                    . " and not exists (select * from comprobante t_c" //todos menos los comprobantes de ese punto venta asociados a items (ya rendidos) de formularios no anulados
                    . "                  inner join item t_i on (t_c.id_comprob=t_i.id_comprobante)"
                    . "                  inner join formulario t_f on (t_f.id_form =t_i.id_form)"
                    . "                 where "
                    . "                      t_f.id_punto_venta=$id_punto
                                           and t_c.id_comprob=c.id_comprob
                                           and t_f.estado<>'N'
                                           )"
                    .$concatenar
                    .") sub"
                    . " order by tipo,fecha_emision,nro_comprobante";
            return toba::db('formularios')->consultar($sql);
        }
        function esta_repetido($id_comp){//retorna true cuando esta repetido
            //verifico si existe algun item con el numero de comprobante que intenta dar de alta
            $sql = " select * from item i "
                   . " where i.id_comprobante=$id_comp ";
            $resul= toba::db('formularios')->consultar($sql); 
            if(count($resul)>0){
                return true ;  
            }else{
                return false;
            }
        }
        
        
//        function get_monto($id_comprobante){
//            $sql = "SELECT total "
//                    . " FROM comprobante "
//                    . " WHERE id_comprob=$id_comprobante ";
//            $resul= toba::db('formularios')->consultar($sql);
//            if(count($resul)>0){
//                return $resul[0]['total'];
//            }else{
//                return 0;
//            }
//        }
        function get_monto($id_comprobante){
           
            if(isset($id_comprobante)){
                $sql = "SELECT total "
                    . " FROM comprobante "
                    . " WHERE id_comprob=$id_comprobante ";
                $resul= toba::db('formularios')->consultar($sql);
                if(count($resul)>0){
                    return $resul[0]['total'];
                }else{
                    return 0;
                }
            }else{
                return 0;
            }
        }
        function get_comprobantes_rendidos($where=null){
            $pd = toba::manejador_sesiones()->get_perfil_datos(); 
            $con="select sigla from dependencia ";
            $con = toba::perfil_de_datos()->filtrar($con);
            $resul=toba::db('formularios')->consultar($con);
            $condicion=' WHERE 1=1 ';
            if(isset($pd)){//pd solo tiene valor cuando el usuario esta asociado a un perfil de datos
                    $condicion.=" and id_dependencia = ".quote($resul[0]['sigla']);
                }//sino es usuario de la central no filtro a menos que haya elegido
            if(!is_null($where)){
                    $condicion.=' and  '.$where;
                }
            $sql=" select * from (select t_c.nro_comprobante as numero,t_c.total,t_c.fecha_emision,t_t.descripcion as tipo_comprob,t_p.id_dependencia,t_d.descripcion as dependencia,t_p.id_punto,extract(year from t_c.fecha_emision )as anio,extract(month from t_c.fecha_emision )as mes,extract(day from t_c.fecha_emision )as dia,lpad(cast(t_p.id_punto as text),5,'0')||'-'||lpad(cast(t_c.nro_comprobante as text),8,'0') as nro_comprobante,case when sub.id_comprob is null then 'N' else case when sub.estado='N' then 'N' else 'R' end end as rendido,sub.id_form,sub.nro_formulario,sub.nro_expediente,case when sub.nro_formulario is null then false else true end as tiene_numero
                    from comprobante t_c
                    inner join punto_venta t_p on  (t_p.id_punto=t_c.id_punto_venta)
                    inner join dependencia t_d on (t_d.sigla=t_p.id_dependencia)
                    left outer join tipo_comprobante t_t on (t_t.id_tipo=t_c.tipo)
                    left outer join (select c.id_comprob ,f.id_form,lpad(cast(nro_ingreso as text),4,'0')||'/'||anio_ingreso as nro_formulario, f.estado,nro_expediente
                                     from item t_i 
                                     inner join formulario f on (f.id_form=t_i.id_form)
                                     inner join comprobante c on (c.id_comprob=t_i.id_comprobante)
                                     ) sub on (sub.id_comprob=t_c.id_comprob)
                                   
                                       )sub2
                                       $condicion"
                    . " order by rendido,dependencia,id_punto,tipo_comprob,nro_comprobante,anio,mes,dia ";
            
            return toba::db('formularios')->consultar($sql);
        }
        function tiene_comprob_repetidos($id_form){
            //verifico que todos los items del formulario no se repitan en el mismo o en otro formulario
            $sql="select * from item a
                where id_comprobante is not null
                and id_form=$id_form"
                //no si existe el mismo comprobante en otro formulario (no anulado)
                ." and not exists (select * from item b, formulario f
                                where b.id_form=f.id_form
                                and a.id_comprobante=b.id_comprobante
                                and a.id_form<>b.id_form
                                and f.estado<>'N'
                                 )"
                  //no existe el mismo comprobante en el mismo formulario           
                ."   and not exists(select *
                                from item c
                                where c.id_form=a.id_form
                                and c.id_comprobante=a.id_comprobante
                                and c.id_item<>a.id_item
                                )";
            $resul= toba::db('formularios')->consultar($sql);
            if(count($resul)>0){
                    return true;
                }else{
                    return false;
                }
        }

}

?>