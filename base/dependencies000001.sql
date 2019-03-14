/********************************************I-DEP-EGS-SOPTE-0-27/02/2019*************************************/
CREATE OR REPLACE VIEW sopte.vhelp_desk(
    id_help_desk,
    id_funcionario,
    id_proceso_wf,
    id_estado_wf,
    fecha,
    estado_reg,
    nro_tramite,
    id_usuario_ai,
    usuario_ai,
    fecha_reg,
    id_usuario_reg,
    id_usuario_mod,
    fecha_mod,
    usr_reg,
    usr_mod,
    estado,
    desc_funcionario,
    etapa,
    obs,
    nombre_tipo,
    descripcion,
    desc_funcionario_asignado,
    prioridad,
    id_tipo,
    id_tipo_sub,
    nombre_subtipo,
    desc_prioridad)
AS
  SELECT help.id_help_desk,
         help.id_funcionario,
         help.id_proceso_wf,
         help.id_estado_wf,
         help.fecha,
         help.estado_reg,
         help.nro_tramite,
         help.id_usuario_ai,
         help.usuario_ai,
         help.fecha_reg,
         help.id_usuario_reg,
         help.id_usuario_mod,
         help.fecha_mod,
         usu1.cuenta AS usr_reg,
         usu2.cuenta AS usr_mod,
         help.estado,
         fun.desc_funcionario1 AS desc_funcionario,
         te.etapa,
         ew.obs,
         tip.nombre AS nombre_tipo,
         help.descripcion,
         funi.desc_funcionario1 AS desc_funcionario_asignado,
         help.prioridad,
         help.id_tipo,
         help.id_tipo_sub,
         subti.nombre AS nombre_subtipo,
         cat.descripcion AS desc_prioridad
  FROM sopte.thelp_desk help
       JOIN segu.tusuario usu1 ON usu1.id_usuario = help.id_usuario_reg
       LEFT JOIN segu.tusuario usu2 ON usu2.id_usuario = help.id_usuario_mod
       JOIN orga.vfuncionario fun ON fun.id_funcionario = help.id_funcionario
       JOIN wf.testado_wf ew ON ew.id_proceso_wf = help.id_proceso_wf AND
         ew.estado_reg::text = 'activo'::text
       LEFT JOIN orga.vfuncionario funi ON funi.id_funcionario =
         ew.id_funcionario
       JOIN wf.ttipo_estado te ON te.id_tipo_estado = ew.id_tipo_estado
       LEFT JOIN sopte.ttipo tip ON tip.id_tipo = help.id_tipo
       LEFT JOIN sopte.ttipo subti ON subti.id_tipo = help.id_tipo_sub
       LEFT JOIN param.tcatalogo cat ON cat.codigo::text = help.prioridad::text;
       
 -------------------SQL-----------------------
select wf.f_import_ttipo_documento_estado ('insert','DOCHELP','SOPTEC','borrador','SOPTEC','crear','superior','');
select wf.f_import_ttipo_documento_estado ('insert','IMGHELP','SOPTEC','borrador','SOPTEC','crear','superior','');

----------estructura del menu gui------------------

select pxp.f_insert_testructura_gui ('SOPTE', 'SISTEMA');
select pxp.f_insert_testructura_gui ('HED', 'SOPTE');
select pxp.f_insert_testructura_gui ('HES', 'SOPTE');
select pxp.f_insert_testructura_gui ('CONFSOP', 'SOPTE');
select pxp.f_insert_testructura_gui ('TIPSOP', 'CONFSOP');



/********************************************F-DEP-EGS-SOPTE-0-27/02/2019*************************************/


