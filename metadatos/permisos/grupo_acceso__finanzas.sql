
------------------------------------------------------------
-- apex_usuario_grupo_acc
------------------------------------------------------------
INSERT INTO apex_usuario_grupo_acc (proyecto, usuario_grupo_acc, nombre, nivel_acceso, descripcion, vencimiento, dias, hora_entrada, hora_salida, listar, permite_edicion, menu_usuario) VALUES (
	'formularios', --proyecto
	'finanzas', --usuario_grupo_acc
	'Finanzas', --nombre
	NULL, --nivel_acceso
	'Para Finanzas', --descripcion
	NULL, --vencimiento
	NULL, --dias
	NULL, --hora_entrada
	NULL, --hora_salida
	NULL, --listar
	'0', --permite_edicion
	NULL  --menu_usuario
);

------------------------------------------------------------
-- apex_usuario_grupo_acc_item
------------------------------------------------------------

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'formularios', --proyecto
	'finanzas', --usuario_grupo_acc
	NULL, --item_id
	'1'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'formularios', --proyecto
	'finanzas', --usuario_grupo_acc
	NULL, --item_id
	'2'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'formularios', --proyecto
	'finanzas', --usuario_grupo_acc
	NULL, --item_id
	'3813'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'formularios', --proyecto
	'finanzas', --usuario_grupo_acc
	NULL, --item_id
	'3814'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'formularios', --proyecto
	'finanzas', --usuario_grupo_acc
	NULL, --item_id
	'3823'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'formularios', --proyecto
	'finanzas', --usuario_grupo_acc
	NULL, --item_id
	'3825'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'formularios', --proyecto
	'finanzas', --usuario_grupo_acc
	NULL, --item_id
	'3826'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'formularios', --proyecto
	'finanzas', --usuario_grupo_acc
	NULL, --item_id
	'3827'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'formularios', --proyecto
	'finanzas', --usuario_grupo_acc
	NULL, --item_id
	'3828'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'formularios', --proyecto
	'finanzas', --usuario_grupo_acc
	NULL, --item_id
	'3829'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'formularios', --proyecto
	'finanzas', --usuario_grupo_acc
	NULL, --item_id
	'3830'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'formularios', --proyecto
	'finanzas', --usuario_grupo_acc
	NULL, --item_id
	'3840'  --item
);
INSERT INTO apex_usuario_grupo_acc_item (proyecto, usuario_grupo_acc, item_id, item) VALUES (
	'formularios', --proyecto
	'finanzas', --usuario_grupo_acc
	NULL, --item_id
	'3865'  --item
);
--- FIN Grupo de desarrollo 0

------------------------------------------------------------
-- apex_grupo_acc_restriccion_funcional
------------------------------------------------------------
INSERT INTO apex_grupo_acc_restriccion_funcional (proyecto, usuario_grupo_acc, restriccion_funcional) VALUES (
	'formularios', --proyecto
	'finanzas', --usuario_grupo_acc
	'50'  --restriccion_funcional
);
