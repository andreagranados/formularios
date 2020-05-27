
------------------------------------------------------------
-- apex_dimension
------------------------------------------------------------

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_dimension (proyecto, dimension, nombre, descripcion, schema, tabla, col_id, col_desc, col_desc_separador, multitabla_col_tabla, multitabla_id_tabla, fuente_datos_proyecto, fuente_datos) VALUES (
	'formularios', --proyecto
	'21', --dimension
	'sigla', --nombre
	'Dependencia', --descripcion
	NULL, --schema
	'dependencia', --tabla
	'sigla', --col_id
	'sigla', --col_desc
	NULL, --col_desc_separador
	NULL, --multitabla_col_tabla
	NULL, --multitabla_id_tabla
	'formularios', --fuente_datos_proyecto
	'formularios'  --fuente_datos
);
INSERT INTO apex_dimension (proyecto, dimension, nombre, descripcion, schema, tabla, col_id, col_desc, col_desc_separador, multitabla_col_tabla, multitabla_id_tabla, fuente_datos_proyecto, fuente_datos) VALUES (
	'formularios', --proyecto
	'22', --dimension
	'id_punto', --nombre
	'Punto de Venta', --descripcion
	NULL, --schema
	'punto_venta', --tabla
	'id_punto', --col_id
	'id_punto', --col_desc
	NULL, --col_desc_separador
	NULL, --multitabla_col_tabla
	NULL, --multitabla_id_tabla
	'formularios', --fuente_datos_proyecto
	'formularios'  --fuente_datos
);
--- FIN Grupo de desarrollo 0
