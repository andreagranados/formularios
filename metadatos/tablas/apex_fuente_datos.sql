
------------------------------------------------------------
-- apex_fuente_datos
------------------------------------------------------------
INSERT INTO apex_fuente_datos (proyecto, fuente_datos, descripcion, descripcion_corta, fuente_datos_motor, host, punto_montaje, subclase_archivo, subclase_nombre, orden, schema, instancia_id, administrador, link_instancia, tiene_auditoria, parsea_errores, permisos_por_tabla, usuario, clave, base) VALUES (
	'formularios', --proyecto
	'formularios', --fuente_datos
	'Fuente formularios', --descripcion
	'formularios', --descripcion_corta
	'postgres7', --fuente_datos_motor
	NULL, --host
	'26', --punto_montaje
	NULL, --subclase_archivo
	NULL, --subclase_nombre
	NULL, --orden
	'public', --schema
	'formularios', --instancia_id
	NULL, --administrador
	'1', --link_instancia
	'1', --tiene_auditoria
	'0', --parsea_errores
	'0', --permisos_por_tabla
	NULL, --usuario
	NULL, --clave
	NULL  --base
);
