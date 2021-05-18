<?php
class extension_adjuntos_evt extends toba_ei_cuadro
{
	

	function conf_evt__adjuntos($evento, $fila)
	{
		if ($this->datos[$fila]['archivo']==1) {
                     $evento->set_msg_ayuda('Tiene el archivo adjunto');
                }else{
                    $evento->set_msg_ayuda('<b>NO TIENE archivo adjunto</b>');
                }
	}

}
?>                      
