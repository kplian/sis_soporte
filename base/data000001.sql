/********************************************I-DAT-EGS-SOPTE-0-27/02/2019********************************************/
-------insertamos el sistema------------

INSERT INTO segu.tsubsistema ("codigo", "nombre", "fecha_reg", "prefijo", "estado_reg", "nombre_carpeta", "id_subsis_orig")
VALUES (E'SOPTE', E'Soporte', E'2019-02-27', E'SOPTE', E'activo', E'soporte', NULL);
----------Estructura WF--------------
select wf.f_import_tproceso_macro ('insert','SOPTE', 'SOPTE', 'Soporte tecnico','si');
select wf.f_import_tcategoria_documento ('insert','legales', 'Legales');
select wf.f_import_tcategoria_documento ('insert','proceso', 'Proceso');
select wf.f_import_ttipo_proceso ('insert','SOPTEC',NULL,NULL,'SOPTE','Solicitud de soporte','sopte.vhelp_desk','id_help_desk','si','','','','SOPTEC',NULL);
select wf.f_import_ttipo_estado ('insert','borrador','SOPTEC','Borrador ','si','no','no','todos','','ninguno','','','no','no',NULL,'<font color="99CC00" size="5"><font size="4">{TIPO_PROCESO}</font></font><br><br><b>&nbsp;</b>Tramite:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp; &nbsp;&nbsp; <b>{NUM_TRAMITE}</b><br><b>&nbsp;</b>Usuario :<b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; {USUARIO_PREVIO} </b>en estado<b>&nbsp; {ESTADO_ANTERIOR}<br></b>&nbsp;<b>Responsable:&nbsp;&nbsp; &nbsp;&nbsp; </b><b>{FUNCIONARIO_PREVIO}&nbsp; {DEPTO_PREVIO}<br>&nbsp;</b>Estado Actual<b>: &nbsp; &nbsp;&nbsp; {ESTADO_ACTUAL}</b><br><br><br>&nbsp;{OBS} <br>','Aviso WF ,  {PROCESO_MACRO}  ({NUM_TRAMITE})','','no','','','','','notificacion','','{}',NULL);
select wf.f_import_ttipo_estado ('insert','finalizado','SOPTEC','Finalizacion','no','no','si','ninguno','','ninguno','','','no','no',NULL,'<font color="99CC00" size="5"><font size="4">{TIPO_PROCESO}</font></font><br><br><b>&nbsp;</b>Tramite:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp; &nbsp;&nbsp; <b>{NUM_TRAMITE}</b><br><b>&nbsp;</b>Usuario :<b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; {USUARIO_PREVIO} </b>en estado<b>&nbsp; {ESTADO_ANTERIOR}<br></b>&nbsp;<b>Responsable:&nbsp;&nbsp; &nbsp;&nbsp; </b><b>{FUNCIONARIO_PREVIO}&nbsp; {DEPTO_PREVIO}<br>&nbsp;</b>Estado Actual<b>: &nbsp; &nbsp;&nbsp; {ESTADO_ACTUAL}</b><br><br><br>&nbsp;{OBS} <br>','Aviso WF ,  {PROCESO_MACRO}  ({NUM_TRAMITE})','responsable','no','','','','','','','',NULL);
select wf.f_import_ttipo_estado ('insert','pendiente','SOPTEC','Pendiente','no','no','no','ninguno','','depto_listado','','','si','no',NULL,'<font color="99CC00" size="5"><font size="4">{TIPO_PROCESO}</font></font><br><br><b>&nbsp;</b>Tramite:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp; &nbsp;&nbsp; <b>{NUM_TRAMITE}</b><br><b>&nbsp;</b>Usuario :<b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; {USUARIO_PREVIO} </b>en estado<b>&nbsp; {ESTADO_ANTERIOR}<br></b>&nbsp;<b>Responsable:&nbsp;&nbsp; &nbsp;&nbsp; </b><b>{FUNCIONARIO_PREVIO}&nbsp; {DEPTO_PREVIO}<br>&nbsp;</b>Estado Actual<b>: &nbsp; &nbsp;&nbsp; {ESTADO_ACTUAL}</b><br>&nbsp;Funcionario Solicitante:&nbsp;&nbsp; {$tabla.desc_funcionario}<br><br>&nbsp;{OBS} <br>','Aviso WF ,  {PROCESO_MACRO}  ({NUM_TRAMITE})','administrador','no','','','','','','','',NULL);
select wf.f_import_ttipo_estado ('insert','asignado','SOPTEC','Asignado','no','no','no','segun_depto','','anterior','','','si','no',NULL,'<font color="99CC00" size="5"><font size="4">{TIPO_PROCESO}</font></font><br><br><b>&nbsp;</b>Tramite:&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;<b>{NUM_TRAMITE}</b><br><b>&nbsp;Asignado Por</b>:<b>&nbsp;{USUARIO_PREVIO}&nbsp;</b>en estado<b>&nbsp; {ESTADO_ANTERIOR}<br></b>&nbsp;<b>Responsable:&nbsp;</b><b>{FUNCIONARIO_PREVIO}&nbsp; {DEPTO_PREVIO}<br>&nbsp;</b>Estado Actual<b>:&nbsp; &nbsp;{ESTADO_ACTUAL}</b><br>&nbsp;Funcionario Solicitante:&nbsp; {$tabla.desc_funcionario}<br><br>{OBS}&nbsp;','Aviso WF ,  {PROCESO_MACRO}  ({NUM_TRAMITE})','','no','','','','','','','',NULL);
select wf.f_import_ttipo_estado ('insert','rechazado','SOPTEC','Rechazado','no','no','si','funcion_listado','sopte.f_lista_funcionario_wf_res','ninguno','','','no','no',NULL,'<font color="99CC00" size="5"><font size="4">{TIPO_PROCESO}</font></font><br><br><b>&nbsp;</b>Tramite:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp; &nbsp;&nbsp; <b>{NUM_TRAMITE}</b><br><b>&nbsp;</b>Usuario :<b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; {USUARIO_PREVIO} </b>en estado<b>&nbsp; {ESTADO_ANTERIOR}<br></b>&nbsp;<b>Responsable:&nbsp;&nbsp; &nbsp;&nbsp; </b><b>{FUNCIONARIO_PREVIO}&nbsp; {DEPTO_PREVIO}<br>&nbsp;</b>Estado Actual<b>: &nbsp; &nbsp;&nbsp; {ESTADO_ACTUAL}</b><br><br><br>&nbsp;{OBS} <br>','Aviso WF ,  {PROCESO_MACRO}  ({NUM_TRAMITE})','','no','','','','','','','',NULL);
select wf.f_import_ttipo_estado ('insert','proceso','SOPTEC','Proceso','no','no','no','anterior','','anterior','','','no','no',NULL,'<font color="99CC00" size="5"><font size="4">{TIPO_PROCESO}</font></font><br><br><b>&nbsp;</b>Tramite:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp; &nbsp;&nbsp; <b>{NUM_TRAMITE}</b><br><b>&nbsp;</b>Usuario :<b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; {USUARIO_PREVIO} </b>en estado<b>&nbsp; {ESTADO_ANTERIOR}<br></b>&nbsp;<b>Responsable:&nbsp;&nbsp; &nbsp;&nbsp; </b><b>{FUNCIONARIO_PREVIO}&nbsp; {DEPTO_PREVIO}<br>&nbsp;</b>Estado Actual<b>: &nbsp; &nbsp;&nbsp; {ESTADO_ACTUAL}</b><br><br><br>&nbsp;{OBS} <br>','Aviso WF ,  {PROCESO_MACRO}  ({NUM_TRAMITE})','','no','','','','','','','',NULL);
select wf.f_import_ttipo_estado ('insert','resuelto','SOPTEC','Resuelto','no','no','si','funcion_listado','sopte.f_lista_funcionario_wf_res','ninguno','','','no','no',NULL,'<font color="99CC00" size="5"><font size="4">{TIPO_PROCESO}</font></font><br><br><b>&nbsp;</b>Tramite:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp; &nbsp;&nbsp; <b>{NUM_TRAMITE}</b><br><b>&nbsp;</b>Usuario :<b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; {USUARIO_PREVIO} </b>en estado<b>&nbsp; {ESTADO_ANTERIOR}<br></b>&nbsp;<b>Responsable:&nbsp;&nbsp; &nbsp;&nbsp; </b><b>{FUNCIONARIO_PREVIO}&nbsp; {DEPTO_PREVIO}<br>&nbsp;</b>Estado Actual<b>: &nbsp; &nbsp;&nbsp; {ESTADO_ACTUAL}</b><br><br><br>&nbsp;{OBS} <br>','Aviso WF ,  {PROCESO_MACRO}  ({NUM_TRAMITE})','','no','','','','','','','',NULL);

select wf.f_import_ttipo_documento ('insert','DOCHELP','SOPTEC','Documento Problema','Documento con la descripcion del problema','','escaneado',1.00,'{}');
select wf.f_import_ttipo_documento ('insert','IMGHELP','SOPTEC','Imagen Problema','Imagen indicando el problema','','escaneado',1.00,'{}');


select wf.f_import_testructura_estado ('insert','borrador','pendiente','SOPTEC',1,'');
select wf.f_import_testructura_estado ('insert','pendiente','asignado','SOPTEC',1,'');
select wf.f_import_testructura_estado ('insert','asignado','asignado','SOPTEC',1,'');
select wf.f_import_testructura_estado ('insert','pendiente','rechazado','SOPTEC',1,'');
select wf.f_import_testructura_estado ('insert','asignado','proceso','SOPTEC',1,'');
select wf.f_import_testructura_estado ('insert','proceso','resuelto','SOPTEC',1,'');
--select wf.f_import_testructura_estado ('insert','resuelto','finalizado','SOPTEC',1,'');

-----------Estructura del menu-------------
select pxp.f_insert_tgui ('<i class="fa fa-medkit fa-2x"></i> SOPORTE', '', 'SOPTE', 'si', 1, '', 1, '', '', 'SOPTE');
select pxp.f_insert_tgui ('Help Desk', 'Help Desk', 'HED', 'si', 1, 'sis_soporte/vista/help_desk/HelpDesk.php', 2, '', 'HelpDesk', 'SOPTE');
select pxp.f_insert_tgui ('Help Asistencia', 'Help Asistencia', 'HES', 'si', 1, 'sis_soporte/vista/help_desk/HelpDeskAsis.php', 2, '', 'HelpDeskAsis', 'SOPTE');
select pxp.f_insert_tgui ('Configuraciones', 'Configuraciones', 'CONFSOP', 'si', 1, '', 2, '', '', 'SOPTE');
select pxp.f_insert_tgui ('Tipo', 'Tipo Soporte', 'TIPSOP', 'si', 1, 'sis_soporte/vista/tipo/Tipo.php', 3, '', 'Tipo', 'SOPTE');
-----------Catalogo de prioridades-------------

select param.f_import_tcatalogo_tipo ('insert','tprioridad_help_desk','SOPTE','thelp_desk');
select param.f_import_tcatalogo ('insert','SOPTE','Baja','prio_baja','tprioridad_help_desk');
select param.f_import_tcatalogo ('insert','SOPTE','Normal','prio_normal','tprioridad_help_desk');
select param.f_import_tcatalogo ('insert','SOPTE','Alta','prio_alta','tprioridad_help_desk');
select param.f_import_tcatalogo ('insert','SOPTE','Urgente','prio_urgente','tprioridad_help_desk');

--------variable global  -------------
INSERT INTO pxp.variable_global ("variable", "valor", "descripcion")
VALUES 
  (E'sopte_prioridad', E'Baja,Normal,Alta,Urgente', E'del catalogo el nombre de las prioridades para un soporte');
/********************************************F-DAT-EGS-SOPTE-0-27/02/2019**********************************************/

