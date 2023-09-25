<?php
class dt_actividad extends toba_datos_tabla
{
    function get_listado($where=null)
    {    
        $condicion=' WHERE 1=1 ';
        //le aplico el perfil de datos para que cada uno vea sus expedientes
        $con="select sigla,descripcion from dependencia ";
        $con = toba::perfil_de_datos()->filtrar($con);
       
        $resul=toba::db('formularios')->consultar($con);
        if(count($resul)==1){//esta asociada a un perfil de datos
            $condicion.= " and id_dependencia = ".quote($resul[0]['sigla']);     
         }
        if(!is_null($where)){    
                $condicion.='and '.$where;
        }
        $sql="select a.id_actividad,"
                . "a.descripcion,"
                . "d.descripcion as dependencia,"
                . "p.id_programa,"
                . "p.nombre as programa, "
                . "c.id_categoria,"
                . "c.descripcion as categoria "
                . " from actividad a"
                . " inner join programa p on (a.id_programa=p.id_programa)"
                . " inner join categoria c on (a.id_categ=c.id_categoria)"
                . " inner join dependencia d on (a.id_dependencia=d.sigla)"
                . $condicion;
        return toba::db('formularios')->consultar($sql);
    }
    function tiene_item($id_act){
          $sql="select * from item where id_actividad='".$id_act."'";
          $resul=toba::db('formularios')->consultar($sql);  
          if(count($resul)>0){//el expediente tiene items asociados
              return true;
          }else{
              return false;
          }
    }
    //retorna true si existe otra actividad con el mismo nombre dentro de la misma ua, programa,categ
    function existe($desc,$id_dep,$id_prog,$id_categ){
          $sql="select * from actividad "
                  . " where id_dependencia='".$id_dep."'"
                  . " and id_programa=$id_prog"
                  . " and id_categ=$id_categ"
                  . " and trim(descripcion) = '".upper(trim(translate($desc,'áéíóúÁÉÍÓÚ','aeiouAEIOU')))."'";
          $resul=toba::db('formularios')->consultar($sql);  
          if(count($resul)>0){//la actividad tiene items asociados
              return true;
          }else{
              return false;
          }
    }
     //retorna true cuando puede hacer la modificacion porque no existe otra activ con el mismo nombre
    function modificar($datos=array(),$idact){//cuando quiere modif una activ
        $sql="select * "
                . " from actividad "
                . " where id_dependencia='".$datos['id_dependencia']."'"
                . " and id_programa=".$datos['id_programa']
                . " and id_categ=".$datos['id_categ']
                . " and descripcion = '".upper(trim(translate($datos['descripcion'],'áéíóúÁÉÍÓÚ','aeiouAEIOU')))."'"
                . " and id_actividad<>".$idact;
        $resul=toba::db('formularios')->consultar($sql);
        
        if(count($resul)>0){//si existe otro con el mismo nombre entonces no puede modificar
            $band=false;
        }else{
            $band=true;
        }
        return $band;
    }
    function get_descripciones()
    {
            $sql = "SELECT id_actividad, descripcion FROM actividad ORDER BY descripcion";
            return toba::db('formularios')->consultar($sql);
    }
    
    function get_listado_actividades($where=null){
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

        $sql="select * from (select t_a.id_actividad,t_a.descripcion as activ,t_g.id_grupo,"
                //. " t_g.descripcion as grupo,"
                . " lpad(t_i.id_grupo::text,4,'0') as grupo,"
                . " t_c.id_categoria as id_categoria,"
                . " t_c.descripcion as categ,"
                . " t_v.id_vinc,"
                . " t_v.descripcion as vinc,"
                . " t_f.id_dependencia,"
                . " t_f.nro_expediente,"
                . " t_o.titulo as origen,"
                . " t_o.id_origen,"
                . "t_p.id_programa,"
                ." lpad(cast(t_f.id_programa as text),2,'0') as prog,"
                . "lpad(cast(t_f.id_punto_venta as text),5,'0')||'-'||lpad(cast(t_co.nro_comprobante as text),8,'0') as nro_factura,"
                . "t_co.fecha_emision,"
                . "t_f.id_punto_venta as id_punto,"
                . "case when t_f.id_punto_venta<=0 then 0 else t_f.id_punto_venta end as pv,"
                . "t_f.ano_cobro,"
                . "t_f.id_form,"
                . "t_i.nro_resol,"
                . "t_i.organismo,"
                . "t_pd.descripcion as prov_de,"
                . "t_i.proviene_de,"
                . "t_i.detalle,"
                . "tipo_receptor||' '||nro_receptor as receptor,"
                . "t_co.denominacion_receptor ,"
                . "t_i.monto,"
                . "lpad(cast(t_f.nro_ingreso as text),4,'0')||'/'||t_f.anio_ingreso as numero_ingreso,"
                . "t_f.estado,"
                . "t_pd.descripcion as proviene_de,"
                . "t_i.tipo_posg,"
                . "t_tp.descripcion as tipo_post_desc,"
                . "t_cv.descripcion as condicion_venta"
                
                
                . " from item t_i"
                . " inner join formulario t_f on (t_i.id_form=t_f.id_form)"
                . " inner join origen_ingreso t_o on (t_o.id_origen=t_f.id_origen_recurso)"
                . " inner join programa t_p on (t_p.id_programa=t_f.id_programa)"
                . " inner join punto_venta t_pv on (t_pv.id_punto=t_f.id_punto_venta)"
                . " left outer join tipo_proviene_de t_pd on (t_pd.id_proviene=t_i.proviene_de)"
                . " left outer join comprobante t_co on (t_co.id_comprob=t_i.id_comprobante)"
                . " inner join actividad t_a on (t_a.id_actividad=t_i.id_actividad)"
                . " inner join grupo_presupuestario t_g on (t_g.id_grupo=t_i.id_grupo)"
                . " inner join categoria t_c on (t_c.id_categoria=t_i.id_categ)"
                . " left outer join vinculacion t_v on (t_v.id_vinc=t_i.id_vinc)"
                . " left outer join tipo_posgrado t_tp on (t_tp.id_tipo=t_i.tipo_posg)"
                . " left outer join condicion_venta t_cv on (t_cv.id_cond=t_i.id_condicion_venta))"
                . "sub"
                . "$condicion";
        //agregar condicion de que el formulario no este anulado
//        $salida=toba::db('formularios')->consultar($sql);
//        print_r($salida);
        return toba::db('formularios')->consultar($sql);
    }

}
?>