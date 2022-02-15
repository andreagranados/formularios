<?php
/**
 * Esta clase fue y ser� generada autom�ticamente. NO EDITAR A MANO.
 * @ignore
 */
class formularios_autoload 
{
	static function existe_clase($nombre)
	{
		return isset(self::$clases[$nombre]);
	}

	static function cargar($nombre)
	{
		if (self::existe_clase($nombre)) { 
			 require_once(dirname(__FILE__) .'/'. self::$clases[$nombre]); 
		}
	}

	static protected $clases = array(
            'abm_ci' => 'extension_toba/componentes/abm_ci.php',
            'formularios_abm_ci' => 'extension_toba/componentes/formularios_abm_ci.php',
		'formularios_ci' => 'extension_toba/componentes/formularios_ci.php',
		'formularios_cn' => 'extension_toba/componentes/formularios_cn.php',
		'formularios_datos_relacion' => 'extension_toba/componentes/formularios_datos_relacion.php',
		'formularios_datos_tabla' => 'extension_toba/componentes/formularios_datos_tabla.php',
		'formularios_ei_arbol' => 'extension_toba/componentes/formularios_ei_arbol.php',
		'formularios_ei_archivos' => 'extension_toba/componentes/formularios_ei_archivos.php',
		'formularios_ei_calendario' => 'extension_toba/componentes/formularios_ei_calendario.php',
		'formularios_ei_codigo' => 'extension_toba/componentes/formularios_ei_codigo.php',
		'formularios_ei_cuadro' => 'extension_toba/componentes/formularios_ei_cuadro.php',
		'formularios_ei_esquema' => 'extension_toba/componentes/formularios_ei_esquema.php',
		'formularios_ei_filtro' => 'extension_toba/componentes/formularios_ei_filtro.php',
		'formularios_ei_firma' => 'extension_toba/componentes/formularios_ei_firma.php',
		'formularios_ei_formulario' => 'extension_toba/componentes/formularios_ei_formulario.php',
		'formularios_ei_formulario_ml' => 'extension_toba/componentes/formularios_ei_formulario_ml.php',
		'formularios_ei_grafico' => 'extension_toba/componentes/formularios_ei_grafico.php',
		'formularios_ei_mapa' => 'extension_toba/componentes/formularios_ei_mapa.php',
		'formularios_servicio_web' => 'extension_toba/componentes/formularios_servicio_web.php',
		'formularios_comando' => 'extension_toba/formularios_comando.php',
		'formularios_modelo' => 'extension_toba/formularios_modelo.php',
	);
}
?>