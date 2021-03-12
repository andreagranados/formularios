<?php
class dt_formulario extends toba_datos_tabla
{      
    function igual_categ_vinc($id_form){//retorna true se todos los items tienen la misma categ y vinculacion
        $sql="select count(distinct id_item) as cant_i,count(distinct(id_categ)) as cant_c,count(distinct(id_vinc))as cant_v from item "
                . " where id_form=$id_form";
        $resul=toba::db('formularios')->consultar($sql);
        if($resul[0]['cant_i']>1 and $resul[0]['cant_c']==1 and $resul[0]['cant_v']==1 ){//solo cuando tiene mas de 1 item
            return true;
        }else{
            return false;
        }
    }
    function get_programa($id_form){
        $sql="select id_programa from formulario where id_form=$id_form";
        $resul=toba::db('formularios')->consultar($sql);
        if(count($resul)>0){
            return $resul[0]['id_programa'];
        }
    }
    function su_punto_venta($id_form){
        $sql="select id_punto_venta from formulario where id_form=$id_form";
        $resul=toba::db('formularios')->consultar($sql);
        if(count($resul)>0){
            return $resul[0]['id_punto_venta'];
        }
    }
    function get_formularios(){//utilizada desde Importar rango de facturacion
        $condicion=" WHERE (estado='I' or estado='R') and id_punto_venta>0";
        $pd = toba::manejador_sesiones()->get_perfil_datos(); 
        $con="select sigla from dependencia ";
        $con = toba::perfil_de_datos()->filtrar($con);
        $resul=toba::db('formularios')->consultar($con);
       
        if(isset($pd)){//pd solo tiene valor cuando el usuario esta asociado a un perfil de datos
                    $condicion.=" and id_dependencia = ".quote($resul[0]['sigla']);
                }//sino es usuario de la central no filtro a menos que haya elegido
          
        $sql="select distinct id_form,'('||id_form||')'||nro_expediente||' PV: '||id_punto_venta||'('||p.nombre||')' as descripcion"
                . " from formulario f"
                . " left outer join programa p on (f.id_programa=p.id_programa)"
                . "$condicion";
        return toba::db('formularios')->consultar($sql);
    }
    
    
    function puede_enviar($id_form){
            $sql=" select ".
                    "case when ingresa_fondo_central then case when tipo_dep=1 then 
       case when totalm=totali then true else false 
       end 
   else case when id_origen_recurso=1 then 
           case when retencion>0 then case when retencion=totalm then true else false end
           else case when totalm=totali then true else false end
           end
        else case when totalm=totali then true else false end
        end 
   end 
else true 
end as puede"
                    ." from 
                 (select subcon.*,case when id_origen_recurso=1 and tiene_retencion then round(totali*porc_retencion/100,2)  else 0 end  as retencion  from
                 (select distinct t_f.id_form,t_f.nro_expediente,t_f.id_dependencia,t_f.id_origen_recurso,t_f.ingresa_fondo_central,t_d.tipo_dep,t_c.tiene_retencion,t_p.porc_retencion,subm.totalm,subi.totali
                     from formulario t_f
                     inner join dependencia t_d on (t_f.id_dependencia=t_d.sigla)
                     inner join punto_venta t_p on (t_p.id_punto=t_f.id_punto_venta)
                     inner join item t_i on (t_i.id_form=t_f.id_form)
                     left outer join categoria t_c on (t_i.id_categ =t_c.id_categoria)
                     left outer join 
                     (select id_form,sum(monto)as totalm from modalidad_pago m
                     group by id_form) subm on (subm.id_form=t_f.id_form)
                     left outer join 
                     (select id_form,sum(monto)as totali from item m
                     group by id_form) subi on (subi.id_form=t_f.id_form)
                     )subcon where id_form=$id_form
                 )sub";
            $resul=toba::db('formularios')->consultar($sql);
            return $resul[0]['puede'];
        }
        function esta_en_libro_cerrado($id_form){//retorna 1 si el libro esta cerrado y 0 en caso contrario
            $sql="select case when l.cerrado then 1 else 0 end as cerrado "
                    . " from formulario f"
                    . " left outer join libro_ingreso l on (f.ano_cobro=l.anio)"
                    . "where id_form= ".$id_form;
            $resul=toba::db('formularios')->consultar($sql);
            return $resul[0]['cerrado'];
        }
        function anular_recibo($id_form){
            $sql="update recibo set estado='A' where id_recibo in(select id_recibo from formulario where id_form=".
                    $id_form. ")";
            
            $resul=toba::db('formularios')->consultar($sql);
        }
        function pasado_pilaga($id_form){
            $mensaje='';
            $sql="select pasado_pilaga from formulario where id_form= ".$id_form;
            $resul=toba::db('formularios')->consultar($sql);
            if(isset($resul[0]['pasado_pilaga'])){
                $concatenar=" not (pasado_pilaga)";
                if($resul[0]['pasado_pilaga']){
                    $mensaje=" Destildado ";
                }else{
                    $mensaje=" Tildado ";
                }
                
            }else{
                $concatenar="'true'";
                $mensaje="Tildado";
            }
            
            $sql=" update formulario set pasado_pilaga=".$concatenar." where id_form= ".$id_form;  
            toba::db('formularios')->consultar($sql);
            return $mensaje;
        }
        function get_recibo($id_form){
            $sql="select * "
                    . " from formulario f"
                    . " left outer join recibo r on (f.id_recibo=r.id_recibo)"
                    . " where f.id_form=$id_form";
            return toba::db('formularios')->consultar($sql);
        }
        function puede_agregar($id_form,$id_categ){
            //recupero la retencion de la categoria que ingresa
            $sql="select tiene_retencion from categoria where id_categoria=$id_categ";
            $resul=toba::db('formularios')->consultar($sql);
            $sql="select tiene_retencion from item t
                left outer join categoria c on (t.id_categ=c.id_categoria)
                    where id_form=$id_form ";
            $resul2=toba::db('formularios')->consultar($sql);
            if(count($resul2)>0){
                if($resul[0]['tiene_retencion']==$resul2[0]['tiene_retencion']){
                    return true;
                }else{
                    return false;
                }   
            }else{
                return true;
            }
             
        }
     
        function tiene_items($id_f){//retorna true si tiene items y false en caso contrario
            $sql="select * from item where id_form=$id_f";
            $resul=toba::db('formularios')->consultar($sql);
            if(count($resul)>0){
                return true;
            }else{
                return false;
            }
        }

        function get_listado_filtro($where=null){
            $condicion=' WHERE 1=1 ';
            if(!is_null($where)){
                    $condicion.=' and  '.$where;
                }
            $pd = toba::manejador_sesiones()->get_perfil_datos(); 
            $con="select sigla from dependencia ";
            $con = toba::perfil_de_datos()->filtrar($con);
            $resul=toba::db('formularios')->consultar($con);
            $con2="select id_punto from punto_venta ";
            $con2 = toba::perfil_de_datos()->filtrar($con2);
            $pos=strripos($con2,'WHERE');//devuelve falso sino encuentra

            $resul2=toba::db('formularios')->consultar($con2);
          
            if(isset($pd)){//pd solo tiene valor cuando el usuario esta asociado a un perfil de datos
                    $condicion.=" and id_dependencia = ".quote($resul[0]['sigla']);
                    if(!$pos===false){//aplica el perfil del punto de venta cuando encuentra el where
                        $condicion.=" and id_punto_venta IN ( ";
                        foreach ($resul2 as $key => $value) {
                            $condicion.=$value['id_punto'].',';
                        }
                        $condicion=substr ( $condicion ,0 , strlen($condicion)-1 ) ;//le saco el ultimo ,
                        $condicion.=')';
                       //print_r($condicion);
                    }
                }//sino es usuario de la central no filtro a menos que haya elegido
         
             $sql="select distinct * from (select sub.*, case when check_presupuesto then 'SI' else 'NO' end as check_pres,md.modalidad,us.usuario from 
                (select distinct t_f.id_form,t_b.nombre||' Nro Cuenta: '||t_cu.nro_cuenta as disponibilidad,t_f.fecha_envio,t_f.id_origen_recurso,t_f.id_programa,lpad(cast(t_f.id_programa as text),2,'0') as prog,t_f.ano_cobro,t_f.anio_ingreso,extract(year from t_f.fecha_creacion) as anio_creacion,t_f.nro_ingreso,t_f.nro_expediente,t_f.fecha_creacion,t_f.id_dependencia,t_f.id_recibo,t_f.check_presupuesto,t_f.observacionpresupuesto,observacionfinanzas,case when t_f.id_dependencia='FAIN' then case when t_f.nro_ingreso is not null then 'SI' else 'NO' end else case when t_f.pasado_pilaga then 'SI' else 'NO' end end  as pasado_pilaga,case when t_f.id_dependencia='FAIN' then case when t_f.nro_ingreso is not null then 1 else 0 end else case when t_f.pasado_pilaga then 1 else 0 end end  as pas_pilaga,case when t_f.id_punto_venta<=0 then true else false end as sin_facturacion, lpad(cast(t_f.nro_ingreso as text),4,'0')||'/'||t_f.anio_ingreso as numero_ingreso,t_f.id_punto_venta, case when t_f.id_punto_venta<=0 then 0 else t_f.id_punto_venta end as pv,t_p.descripcion as desc_pv,t_f.estado,t_c.titulo as origen ,t_t.total as monto"//sum(t_i.monto) as monto
                         ." from formulario t_f 
                         INNER JOIN origen_ingreso t_c ON (t_f.id_origen_recurso=t_c.id_origen)
                         INNER JOIN punto_venta t_p ON (t_p.id_punto=t_f.id_punto_venta)
                         LEFT OUTER JOIN cuenta_bancaria t_cu on (t_f.disponibilidad=t_cu.id_cuenta) 
                         LEFT OUTER JOIN banco t_b on (t_cu.id_banco=t_b.id_banco) 
                         LEFT OUTER JOIN item t_i on (t_i.id_form=t_f.id_form) "
                      ." LEFT OUTER JOIN (select t_it.id_form,sum(monto) as total from item t_it
                                            group by t_it.id_form) t_t on (t_t.id_form=t_f.id_form) "
                        // " GROUP BY t_f.id_form,fecha_envio,t_f.id_origen_recurso,t_f.id_programa,t_f.mes_cobro,t_f.ano_cobro,anio_ingreso,nro_ingreso,nro_expediente,fecha_creacion, t_f.id_dependencia,id_recibo,t_f.check_presupuesto,observacionpresupuesto,observacionfinanzas,pasado_pilaga,id_punto_venta,pv,estado,titulo
                         ." )sub "
                    . " LEFT OUTER JOIN (select i.id_form,string_agg('$'||i.monto||' '||c.descripcion||case when i.id_condicion_venta=2 then ' Nro:'||nro_cheque||' Banco '||n.nombre else case when i.id_condicion_venta=3 then ' Nro transf: '||nro_transferencia||' CBU: '||b.cbu||' Banco '||a.nombre||coalesce(' CUIL/T:'||cuil1||'-'||lpad(cast(cuil as text),8,'0')||'-'||cuil2,'') else '' end end ,',') as modalidad
                                        from modalidad_pago i                 
                                        left outer join condicion_venta c on (i.id_condicion_venta=c.id_cond)
                                        left outer join cuenta_bancaria b on (b.id_cuenta=i.cuenta_a_acreditar) 
                                        left outer join banco a on (a.id_banco=b.id_banco) 
                                        left outer join banco n on (n.id_banco=i.id_banco)
                                        group by i.id_form )md 
                                    ON (md.id_form=sub.id_form)"
                    ."   LEFT OUTER JOIN (select id_form,auditoria_usuario as usuario from public_auditoria.logs_formulario"
                    . "                   where auditoria_operacion='I' and auditoria_usuario<>'postgres')us ON (us.id_form=sub.id_form)"                
                    . ")sub2 $condicion"
                    . " order by fecha_creacion desc";
           // $sql = toba::perfil_de_datos()->filtrar($sql);
             
            return toba::db('formularios')->consultar($sql);
        }
        
        function get_origen_recurso($id_form){
            $sql="select id_origen_recurso from formulario where id_form=$id_form";
            $resul= toba::db('formularios')->consultar($sql);
            return $resul[0]['id_origen_recurso'];
        }
        function get_titulo($id_form){
            $sql="select 'FORMULARIO ' || t_o.descripcion as titulo "
                    . " from formulario t_f "
                    . " inner join origen_ingreso t_o on (t_f.id_origen_recurso=t_o.id_origen)"
                    . " where id_form=$id_form";
            $resul= toba::db('formularios')->consultar($sql);
            return $resul[0]['titulo'];
        }
        function get_dependencia($id_form){
            $sql="select 'DEPENDENCIA: ' ||t_d.descripcion as dependencia "
                    . " from formulario t_f "
                    . " inner join dependencia t_d on (t_f.id_dependencia=t_d.sigla)"
                    . " where id_form=$id_form";
            $resul= toba::db('formularios')->consultar($sql);
            return $resul[0]['dependencia'];  
        }
        function get_disponibilidad($id_form){
            $sql="select t_o.tipo_cuenta ||' Nro '|| t_o.nro_cuenta||' Banco '||t_b.nombre as disponib "
                    . " from formulario t_f "
                    . " left outer join cuenta_bancaria t_o on (t_f.disponibilidad=t_o.id_cuenta)"
                    . " left outer join banco t_b on (t_o.id_banco=t_b.id_banco)"
                    . " where id_form=$id_form";
            $resul= toba::db('formularios')->consultar($sql);
            if(count($resul)>0){
               return $resul[0]['disponib']; 
            }else{
               return ' '; 
            }
        }
        function get_desc_programa($id_form){
            $sql="select t_p.nombre as programa "
                    . " from formulario t_f "
                    . " inner join programa t_p on (t_f.id_programa=t_p.id_programa)"
                    . " where id_form=$id_form";
            $resul= toba::db('formularios')->consultar($sql);
            if(count($resul)>0){
               return $resul[0]['programa']; 
            }else{
               return ' '; 
            }
        }
        function get_punto_venta($id_form){
            $sql="select 'PUNTO DE VENTA: ' ||case when t_p.id_punto<=0 then 0 else t_p.id_punto end||' ('||t_p.descripcion||')'  as punto_venta"
                    . " from formulario t_f "
                    . " inner join punto_venta t_p on (t_f.id_punto_venta=t_p.id_punto)"
                    . " where id_form=$id_form";
            $resul= toba::db('formularios')->consultar($sql);
            return $resul[0]['punto_venta'];    
        }
        function get_anios(){
            $sql="select distinct extract(year from fecha_creacion) as anio"
                    . " from formulario";
            return toba::db('formularios')->consultar($sql);
        }
        function desasociar_recibo($id_form){
            $sql=" update formulario set estado='T',id_recibo=null where id_form=".$id_form;
            toba::db('formularios')->consultar($sql);
        }
        
}

?>