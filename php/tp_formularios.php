<?php
 class tp_formularios extends toba_tp_basico
 
  {
  protected $clase_encabezado = 'encabezado';	

	
	function barra_superior()
	{ 
		echo "<div align=center>";
		echo toba_recurso::imagen_proyecto('logof.png', true,null,'100px');
                echo "<br>";
		echo "<div style='font-size:15px;font-family:Verdana,Helvetica;color:#660033;font-weight:bold;'>";
		echo "Formularios";
		echo "</div>";
		echo "<div>versi&oacute;n ".toba::proyecto()->get_version();
               // echo " <a href='ManualModuloDesignaciones.pdf'>Descargar Manual Ayuda</a>" ."</div>";
		//echo " <a href='Disposicion005-15SH.pdf'>Disposici�n</a>" ."</div>";
                
		echo "</div>";
		echo "</div>\n\n";    
		
	}
	
	protected function estilos_css()
	{
		parent::estilos_css();
		echo "
		<style type='text/css'>
			#barra_superior {
				display:block;
			}
		</style>			
		";
	}	
	
	protected function generar_ayuda()
	{
		$mensaje = toba::mensajes()->get_operacion_actual();
		if (isset($mensaje)) {
			if (strpos($mensaje, ' ') !== false) {	//Detecta si es una url o un mensaje completo
				$desc = toba_parser_ayuda::parsear($mensaje);
				$ayuda = toba_recurso::ayuda(null, $desc, 'item-barra-ayuda', 0);
				echo "<div $ayuda>";
				echo toba_recurso::imagen_toba("ayuda_grande.gif", true);
				echo "</div>";
			} else {
				if (! toba_parser_ayuda::es_texto_plano($mensaje)) {
					$mensaje = toba_parser_ayuda::parsear($mensaje, true); //Version resumida
				}
				$js = "abrir_popup('ayuda', '$mensaje', {width: 800, height: 600, scrollbars: 1})";
				echo "<a class='barra-superior-ayuda' href='#' onclick=\"$js\" title='Abrir ayuda'>".toba_recurso::imagen_toba("ayuda_grande.gif", true)."</a>";
			}
		}	
	}
	
	/**
	 * Retorna el t�tulo de la opreaci�n actual, utilizado en la barra superior
	 */
	protected function titulo_item()
	{
		return toba::solicitud()->get_datos_item('item_nombre');
	}

	protected function info_version()
	{
		$version = toba::proyecto()->get_parametro('version');
		if( $version && ! (toba::proyecto()->get_id() == 'toba_editor') ) {
			$info = '';
			$version_fecha = toba::proyecto()->get_parametro('version_fecha');
			if($version_fecha) {
				$info .= "Lanzamiento: <strong>$version_fecha</strong> <br />";	
			}			
			$version_detalle = toba::proyecto()->get_parametro('version_detalle');
			if($version_detalle) {
				$info .= "<hr />$version_detalle<br>";	
			}
			$version_link = toba::proyecto()->get_parametro('version_link');
			if($version_link) {
				$info .= "<hr /><a href=\'http://$version_link\' target=\"_bank\">M�s informaci�n</a><br>";	
			}
			if($info) {
				$info = "Versi�n: <strong>$version</strong><br>" . $info;
				$info = toba_recurso::ayuda(null, $info, 'enc-version');
			}else{
				$info = "class='enc-version'";
			}
			echo "<div $info >";		
			echo 'Versi�n <strong>' . $version .'</strong>';
			echo '</div>';	
                       
		}
	}	
		
	function pre_contenido()
	{
		echo "\n<div align='center' class='cuerpo'>\n";		
	}
	
	function post_contenido()
	{
                echo "</div>";		
		echo "<div class='login-pie'>";
                echo "<br>";
                echo "<div>Desarrollado por <strong> <a href='http://sti.uncoma.edu.ar'>" . toba_recurso::imagen_proyecto("isosubti.png",true,null,'20px')."<br></a>Secretar&iacute;a de Hacienda <a href='http://www.uncoma.edu.ar' style='text-decoration: none' target='_blank'> - Uncoma</a></strong></div>
			<div>2019 - ".date('Y')."</div>";

	}
			
  }
  
?>