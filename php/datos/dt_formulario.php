<?php
class dt_formulario extends toba_datos_tabla
{
        function pasado_pilaga($id_form){
            $mensaje='';
            $sql="select pasado_pilaga from formulario where id_form= ".$id_form;
            $resul=toba::db('formularios')->consultar($sql);
            if(isset($resul[0]['pasado_pilaga'])){
                $concatenar=" not (pasado_pilaga)";
                if($resul[0]['pasado_pilaga']){
                    $mensaje=" Destildado";
                }else{
                    $mensaje="Tildado";
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
          // print_r($resul);
            if(isset($pd)){//pd solo tiene valor cuando el usuario esta asociado a un perfil de datos
                    $condicion.=" and id_dependencia = ".quote($resul[0]['sigla']);
                }//sino es usuario de la central no filtro a menos que haya elegido
          
            $sql="select * from 
                (select t_f.id_form,t_f.anio_ingreso,extract(year from t_f.fecha_creacion) as anio_creacion,t_f.nro_ingreso,t_f.nro_expediente,t_f.fecha_creacion,t_f.id_dependencia,t_f.id_recibo,observacionfinanzas,pasado_pilaga,case when t_f.id_punto_venta<=0 then true else false end as sin_facturacion, t_f.nro_ingreso||'/'||t_f.anio_ingreso as numero_ingreso, case when t_f.id_punto_venta<=0 then 0 else t_f.id_punto_venta end as id_punto_venta,t_f.estado,t_c.titulo as origen ,sum(t_i.monto) as monto
                         from formulario t_f 
                         INNER JOIN origen_ingreso t_c ON (t_f.id_origen_recurso=t_c.id_origen)
                         LEFT OUTER JOIN item t_i on (t_i.id_form=t_f.id_form) 
                         GROUP BY t_f.id_form,anio_ingreso,nro_ingreso,nro_expediente,fecha_creacion, id_dependencia,id_recibo,observacionfinanzas,pasado_pilaga,id_punto_venta,estado,titulo
                         )sub $condicion";
           
           // $sql = toba::perfil_de_datos()->filtrar($sql);
            return toba::db('formularios')->consultar($sql);
        }
        
        function get_origen_recurso($id_form){
            $sql="select id_origen_recurso from formulario where id_form=$id_form";
            $resul= toba::db('formularios')->consultar($sql);
            return $resul[0]['id_origen_recurso'];
        }
        function get_titulo($id_form){
            $sql="select 'FORMULARIO ' ||t_o.titulo||'-'|| t_o.descripcion as titulo "
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
        function get_punto_venta($id_form){
            $sql="select 'PUNTO DE VENTA: ' ||t_p.id_punto||' ('||t_p.descripcion||')'  as punto_venta"
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
        
}

?>