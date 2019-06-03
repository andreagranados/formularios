<?php
class dt_comprobante extends toba_datos_tabla
{
    
        function importar($datos=array()){
            foreach ($datos as $key => $value) {
                $sql="insert into comprobante (id_punto_venta,nro_comprobante,fecha_emision,total,id_condicion_venta,estado)".
                     "values(".$value['id_punto_venta'].",".$value['nro_comprobante'].",'".$value['fecha_emision']."',".$value['total'].",1,'I'".")"; 
                toba::db('formularios')->consultar($sql);
            }
        }
	function get_descripciones()
	{
            $sql = "SELECT id_comprob, nro_comprobante FROM comprobante ORDER BY estado";
            return toba::db('formularios')->consultar($sql);
	}
        function get_comprobantes($id_punto,$fecha_creac,$id_comprob){
            //toma el año de la fecha de creacion del formulario
            $d=strtotime($fecha_creac);
            $a=date("Y", $d);
            $fec=date("d/m/Y",strtotime($fecha_creac));
            if($id_comprob<>0){
                $concatenar=' UNION select id_comprob, nro_comprobante from comprobante where id_comprob='.$id_comprob;
            }else{
                $concatenar='';
            }
        
            $sql = "SELECT id_comprob, nro_comprobante "
                    . " FROM comprobante c"
                    . " WHERE c.id_punto_venta=$id_punto
                     and extract(year from c.fecha_emision)=$a"
                    . " and not exists (select * from comprobante t_c" //todos menos los comprobantes de ese punto venta asociados a items (ya rendidos) de formularios no anulados
                    . "                  inner join item t_i on (t_c.id_comprob=t_i.id_comprobante)"
                    . "                  inner join formulario t_f on (t_f.id_form =t_i.id_form)"
                    . "                 where "
                    . "                      t_f.id_punto_venta=$id_punto
                                           and t_c.id_comprob=c.id_comprob
                                           and t_f.estado<>'N'
                                           )"
                    .$concatenar
                    . " order by nro_comprobante";
            
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
            $sql=" select * from (select t_c.fecha_emision,t_p.id_dependencia,t_d.descripcion as dependencia,t_p.id_punto,extract(year from t_c.fecha_emision )as anio,extract(month from t_c.fecha_emision )as mes,extract(day from t_c.fecha_emision )as dia,lpad(cast(t_p.id_punto as text),5,'0')||'-'||lpad(cast(t_c.nro_comprobante as text),8,'0') as nro_comprobante,case when sub.id_comprob is null then 'N' else case when sub.estado='N' then 'N' else 'R' end end as rendido,sub.nro_formulario,sub.nro_expediente,case when sub.nro_formulario is null then false else true end as tiene_numero
                    from comprobante t_c
                    inner join punto_venta t_p on  (t_p.id_punto=t_c.id_punto_venta)
                    inner join dependencia t_d on (t_d.sigla=t_p.id_dependencia)
                    
                    left outer join (select c.id_comprob ,nro_ingreso||'/'||anio_ingreso as nro_formulario, f.estado,nro_expediente
                                     from item t_i 
                                     inner join formulario f on (f.id_form=t_i.id_form)
                                     inner join comprobante c on (c.id_comprob=t_i.id_comprobante)
                                     ) sub on (sub.id_comprob=t_c.id_comprob)
                                   
                                       )sub2
                                       $condicion"
                    . " order by rendido,dependencia,id_punto,anio,mes,dia ";
            
            return toba::db('formularios')->consultar($sql);
        }

}

?>