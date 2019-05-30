<?php
require_once '3ros/phpExcel/PHPExcel/Reader/IReader.php';
//require_once '3ros/phpExcel';
class ci_importacion_comprobantes extends toba_ci
{
        protected $s__nombre_archivo;
        protected $s__datos;
	//-----------------------------------------------------------------------------------
	//---- form -------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__form(formularios_ei_formulario $form)
	{
	}

	function evt__form__validar($datos)
	{
            print_r($datos);
            if (isset($datos['archivo'])) {
		$this->s__nombre_archivo = $datos['archivo']['name'];
		$img = toba::proyecto()->get_www_temp($this->s__nombre_archivo);
                $path=toba::proyecto()->get_www_temp($this->s__nombre_archivo);
		// Mover los archivos subidos al servidor del directorio temporal PHP a uno propio.
		move_uploaded_file($datos['archivo']['tmp_name'], $img['path']);
            }
            $sql=" CREATE LOCAL TEMP TABLE auxi(
                    fila            integer,
                    id_punto_venta    integer,
                    nro_comprobante    integer,
                    fecha_emision   date,
                    total   numeric
                    );";
            toba::db('formularios')->consultar($sql);
            $fp = fopen ( $path['path'] , "r" ); 
            $f=1;
            //Similar a fgets() excepto que fgetcsv() analiza la línea que lee para buscar campos en formato CSV, devolviendo un array que contiene los campos leídos
            while (($data = fgetcsv($fp, 2048, ",")) !== FALSE) {//mientras hay lineas que leer
         
                $i = 0; 
//                foreach($data as $row) {
//                    echo "Campo $i: $row<br>\n"; // Muestra todos los campos de la fila actual 
//                    $i++ ;
//                    
//                 }
//                 echo "<br /><br />\n\n";
                $numero = count($data);
                $sql="insert into auxi(fila,id_punto_venta,nro_comprobante,fecha_emision,total)values("
                        .$f.",".$data[2].",".$data[3].",'".$data[0]."',".$data[15]. ")";
                toba::db('formularios')->consultar($sql);
                $f++;
               
            }//fin recorrido
            //$sql="select * from auxi";$resul=toba::db('formularios')->consultar($sql);print_r($resul);exit;
                  
            //verifico que no haya repetidos. Cuento la cantidad de registro que se repiten
            $sql="select * from (select id_punto_venta,nro_comprobante,count(*) as cant from auxi"
                    . " group by id_punto_venta,nro_comprobante)sub where cant>1";
            $resul=toba::db('formularios')->consultar($sql);
            
            if(count($resul)>0){
                toba::notificacion()->agregar('En el archivo existen comprobantes repetidos', 'info');
            }else{
                //verifico que no haya comprobantes que ya estan en la base de datos
                $sql="select * from auxi a where exists (select * from comprobante c"
                        . "                              where a.id_punto_venta=c.id_punto_venta"
                        . "                                 and a.nro_comprobante=c.nro_comprobante )";
                $resul=toba::db('formularios')->consultar($sql);  
              // print_r($resul);
               
                if(count($resul)==0){//no hay repetidos pasa a la siguiente pantalla
                    $sql="select * from auxi";
                    $this->s__datos=toba::db('formularios')->consultar($sql);   
                    $this->set_pantalla('pant_importar');
                }else{
                    $mensaje='';
                    foreach ($resul as $key => $value) {
                        $mensaje.=$value['fila'].', ';    
                    }
                    //print_r($mensaje);
                    toba::notificacion()->agregar('Hay comprobantes que ya estan en la base, filas: '.$mensaje, 'info');
                }
            }
           
            fclose ( $fp ); 
         
	}

	//-----------------------------------------------------------------------------------
	//---- cuadro -----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cuadro(formularios_ei_cuadro $cuadro)
	{
            if(isset($this->s__datos)){
                 //print_r($this->s__datos);
                return $this->s__datos;
            }
	}
        
       

	

	//-----------------------------------------------------------------------------------
	//---- JAVASCRIPT -------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	

	//-----------------------------------------------------------------------------------
	//---- Eventos ----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function evt__importar()
	{//encierro la linea de codigo que puede producir un error en el try
            try{
                $this->dep('datos')->tabla('comprobante')->importar($this->s__datos);
                toba::notificacion()->agregar('Importacion exitosa!', 'info');
                unset($this->s__datos);
                $this->set_pantalla('pant_inicial');
            }
            catch(Exception $e)//recibe un objeto de tipo exception
            {
                echo $e->getMessage();
            };
                
            
	}

}
?>