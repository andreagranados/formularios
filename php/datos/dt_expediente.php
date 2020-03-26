<?php
class dt_expediente extends toba_datos_tabla
{
    function puedo_borrar($exp){
        $sql = "select * from formulario where nro_expediente='".$exp."'";   
        $res = toba::db('formularios')->consultar($sql);
        if(count($res)>0){//el expediente tiene formularios asociados
            return false;
        }else{
            return true;
        }
    }
    function existe($exp){
        $sql = "select * from expediente where nro_expediente='".$exp."'";   
        $res = toba::db('formularios')->consultar($sql);
        if(count($res)>0){//ya existe el expediente
            return true;
        }else{
            return false;
        }
    }
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
        $sql="select * from (select *,cast((SUBSTRING ( nro_expediente ,10 , 4 )) as integer) as anio "
                . " from expediente)sub "
                . $condicion;
        return toba::db('formularios')->consultar($sql);
    }
    //trae todos los expedientes de un determinado año
    function get_descripciones($anio)
    {
        $where=' WHERE 1=1 ';
         //le aplico el perfil de datos para que cada uno vea sus expedientes
        $con="select sigla,descripcion from dependencia ";
        $con = toba::perfil_de_datos()->filtrar($con);
        $resul=toba::db('formularios')->consultar($con);
        if(count($resul)==1){//esta asociada a un perfil de datos
            $where.= " and id_dependencia = ".quote($resul[0]['sigla']);     
         }
        
        if(isset($anio)){
            $where.=' and cast((SUBSTRING ( nro_expediente ,10 , 4 )) as integer)= '.$anio;
        }
        $sql = "SELECT * "
                . " FROM expediente "
                .  $where 
                . " ORDER BY nro_expediente";
        
        return toba::db('formularios')->consultar($sql);
    }
    function get_anios(){
        $sql = "SELECT SUBSTRING (nro_expediente ,10 , 4 ) as anio"
                . " FROM expediente "
                . " ORDER BY nro_expediente";
        return toba::db('formularios')->consultar($sql);
    }
    //retorna true cuando pudo hacer la modificacion
    function modificar($datos=array(),$exped){//cuando quiere modificar el numero de expediente
        $sql="select * from expediente where nro_expediente='".$datos['nro_expediente']."'";
        $resul=toba::db('formularios')->consultar($sql);
        if(count($resul)>0){
            $band=false;
            
        }else{
            $sql="update expediente set nro_expediente='".$datos['nro_expediente']."' where nro_expediente='".$exped."'";
            toba::db('formularios')->consultar($sql);
            $band=true;
        }
        return $band;
    }
    function tiene_formularios($exp){
          $sql="select * from formulario where nro_expediente='".$exp."'";
          $resul=toba::db('formularios')->consultar($sql);  
          if(count($resul)>0){
              return true;
          }else{
              return false;
          }
    }

}
?>