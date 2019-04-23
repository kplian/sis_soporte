CREATE OR REPLACE FUNCTION sopte.ft_help_desk_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Soprote
 FUNCION: 		sopte.ft_help_desk_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'wf.thelp_desk'
 AUTOR: 		 (eddy.gutierrez)
 FECHA:	        22-02-2019 19:07:11
 COMENTARIOS:	
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				22-02-2019 19:07:11		EGS EndeETR			Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'wf.thelp_desk'	
 #0				22-02-2019 19:07:11		EGS EndeETR			Se agrego el campo sub_tipo
 #5             09/04/2019              EGS EndeEtr         Se agrego el envio de correos al solicitante en los estados rechazado y resuelto
 #7             22/04/2019              EGS EndeEtr         Se envia correos para los administradores y el superior del solicitante en estado pendiente
                                                            y correos al superior y solicitante en estado resuelto
 #8             23/04/2019              EGS EndeEtr         actualizacion en correos cuando funcionario superior es null inmediato superior y nivel uo 5 se toma como funcionario base
 ***************************************************************************/

DECLARE

	v_nro_requerimiento    	integer;
	v_parametros           	record;
	v_id_requerimiento      integer;
    v_resp                  varchar;
    v_nombre_funcion        text;
    v_mensaje_error         text;
    v_id_help_desk          integer;
    v_prioridad             varchar;
    v_id_tipo_sub            varchar;
    
    --variables wf
    v_id_proceso_macro      integer;
    v_num_tramite           varchar;
    v_codigo_tipo_proceso   varchar;
    v_fecha                 date;
    v_codigo_estado         varchar;
    v_id_proceso_wf         integer;
    v_id_estado_wf          integer;
    v_id_gestion            integer;
    
    --#4 variables de sig y ant estado de Wf
    v_id_tipo_estado        integer;    
    v_codigo_estado_siguiente    varchar;
    v_id_depto              integer;
    v_obs                   varchar;
    v_acceso_directo        varchar;
    v_clase                 varchar;
    v_codigo_estados        varchar;
    v_id_cuenta_bancaria    integer;
    v_id_depto_lb           integer;
    v_parametros_ad         varchar;
    v_tipo_noti             varchar;
    v_titulo                varchar;
    v_id_estado_actual      integer;
    v_registros_proc        record;
    v_codigo_tipo_pro       varchar;
    v_id_usuario_reg        integer;
    v_id_estado_wf_ant       integer;
    v_id_funcionario        integer;
   
    --#4 variables para cambio de estado automatico
    va_id_tipo_estado       integer[];
    va_codigo_estado        varchar[];
    va_disparador           varchar[];
    va_regla                varchar[]; 
    va_prioridad            integer[];
    p_id_usuario_ai         integer;
    p_usuario_ai            varchar;
    v_nro_tramite           varchar;--#5
    v_id_alarma             varchar;--#5
    v_correo                varchar;--#5
    v_descripcion_correo    varchar;--#5
    vuo_id_uo               INTEGER[];--#7 
    vuo_id_funcionario      INTEGER[];--#7 
    vuo_gerencia            VARCHAR[];--#7 
    vuo_numero_nivel        VARCHAR[];--#7 
    v_correo_encar          VARCHAR;--#7 
    v_descripcion_sol       VARCHAR;--#7 
    v_desc_funcionario      VARCHAR;--#7 
    v_admi_correos          record;--#7 
    v_cor                   record;--#7 
    v_desc_funcionario_encar    VARCHAR;--#7 
    v_id_funcionario_encar  integer;--#7 
    
    v_bandera_GG                    varchar; --#7 
    v_bandera_GAF                   varchar;--#7 
    v_record_gg                     varchar[];--#7  
    v_record_gaf                    varchar[];--#7  
    v_record_id_funcionario_gg      integer; --#7 
    v_record_id_funcionario_gaf     integer;--#7  
    v_fecha_now                     date;--#7 
    v_id_funcionario_array          integer;

    
BEGIN

    v_nombre_funcion = 'sopte.ft_help_desk_ime';
    v_parametros = pxp.f_get_record(p_tabla);

    /*********************************    
     #TRANSACCION:  'SOPTE_HELP_INS'
     #DESCRIPCION:    Insercion de registros
     #AUTOR:        eddy.gutierrez    
     #FECHA:        22-02-2019 19:07:11
    ***********************************/

    if(p_transaccion='SOPTE_HELP_INS')then
                    
        begin
            --raise exception 'v_parametros %',v_parametros;
          --codigo de proceso WF de presolicitudes de compra
            v_codigo_tipo_proceso = 'SOPTEC';           
            --Recoleccion de datos para el proceso WF #4
             --obtener id del proceso macro

             select
             pm.id_proceso_macro
             into
             v_id_proceso_macro
             from wf.tproceso_macro pm
             left join wf.ttipo_proceso tp on tp.id_proceso_macro  = pm.id_proceso_macro
             where tp.codigo = v_codigo_tipo_proceso;
                          
             If v_id_proceso_macro is NULL THEN
               raise exception 'El proceso macro  de codigo % no esta configurado en el sistema WF',v_codigo_tipo_proceso;
             END IF; 
             
             If v_parametros.id_tipo is NULL THEN
               raise exception 'Ingrese un Tipo de Soporte';
             END IF;                
            --Obtencion de la gestion #4
             v_fecha= now()::date;
              select
                per.id_gestion
                into
                v_id_gestion
                from param.tperiodo per
                where per.fecha_ini <= v_parametros.fecha and per.fecha_fin >= v_parametros.fecha
                limit 1 offset 0;            
    
             -- inciar el tramite en el sistema de WF   #4        
            SELECT
                   ps_num_tramite ,
                   ps_id_proceso_wf ,
                   ps_id_estado_wf ,
                   ps_codigo_estado
                into
                   v_num_tramite,
                   v_id_proceso_wf,
                   v_id_estado_wf,
                   v_codigo_estado

            FROM wf.f_inicia_tramite(
                   p_id_usuario,
                   v_parametros._id_usuario_ai,
                   v_parametros._nombre_usuario_ai,
                   v_id_gestion,
                   v_codigo_tipo_proceso,
                   v_parametros.id_funcionario,
                   null,
                   'Inicio de Solicitud de Soporte',
                   '' );
            --Sentencia de la insercion
            insert into sopte.thelp_desk(
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
            estado,
            descripcion,
            id_tipo
              ) values(
            v_parametros.id_funcionario,
            v_id_proceso_wf,
            v_id_estado_wf,
            v_parametros.fecha,
            'activo',
            v_num_tramite,
            v_parametros._id_usuario_ai,
            v_parametros._nombre_usuario_ai,
            now(),
            p_id_usuario,
            null,
            null,
            v_codigo_estado,
            v_parametros.descripcion,
            v_parametros.id_tipo

            )RETURNING id_help_desk into v_id_help_desk;
            
            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Help Desk almacenado(a) con exito (id_help_desk'||v_id_help_desk||')'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_help_desk',v_id_help_desk::varchar);

            --Devuelve la respuesta
            return v_resp;

        end;

    /*********************************    
     #TRANSACCION:  'SOPTE_HELP_MOD'
     #DESCRIPCION:    Modificacion de registros
     #AUTOR:        eddy.gutierrez    
     #FECHA:        22-02-2019 19:07:11
    ***********************************/

    elsif(p_transaccion='SOPTE_HELP_MOD')then

        begin
            --Sentencia de la modificacion
            update sopte.thelp_desk set
            --id_funcionario = v_parametros.id_funcionario,
            --id_proceso_wf = v_parametros.id_proceso_wf,
            --id_estado_wf = v_parametros.id_estado_wf,
            --fecha = v_parametros.fecha,
            --nro_tramite = v_parametros.nro_tramite,
            id_usuario_mod = p_id_usuario,
            fecha_mod = now(),
            id_usuario_ai = v_parametros._id_usuario_ai,
            usuario_ai = v_parametros._nombre_usuario_ai,
            descripcion = v_parametros.descripcion,
            id_tipo = v_parametros.id_tipo    
            where id_help_desk=v_parametros.id_help_desk;
               
            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Help Desk modificado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_help_desk',v_parametros.id_help_desk::varchar);
               
            --Devuelve la respuesta
            return v_resp;
            
        end;

    /*********************************    
     #TRANSACCION:  'SOPTE_HELP_ELI'
     #DESCRIPCION:    Eliminacion de registros
     #AUTOR:        eddy.gutierrez    
     #FECHA:        22-02-2019 19:07:11
    ***********************************/

    elsif(p_transaccion='SOPTE_HELP_ELI')then

        begin
            --Sentencia de la eliminacion
            delete from sopte.thelp_desk
            where id_help_desk=v_parametros.id_help_desk;
               
            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Help Desk eliminado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_help_desk',v_parametros.id_help_desk::varchar);
              
            --Devuelve la respuesta
            return v_resp;

        end;
                            
          /*********************************
          #TRANSACCION:      'SOPTE_SIGEHELP_INS'
          #DESCRIPCION:      Controla el cambio al siguiente estado
          #AUTOR:           EGS
          #FECHA:           19/02/2019
          #ISSUE:           #4
          ***********************************/

            
          elseif(p_transaccion='SOPTE_SIGEHELP_INS')then
              
              begin
               -- raise exception 'v_parametros.id_help_desk %',v_parametros.id_help_desk;
                  --Obtenemos datos basico                                
                  select
                  ew.id_proceso_wf,    
                  c.id_estado_wf,
                  c.estado,
                  c.prioridad,
                  c.id_tipo_sub,
                  c.nro_tramite,
                  c.fecha,
                  c.id_funcionario,
                  c.descripcion
                  into
                  v_id_proceso_wf,
                  v_id_estado_wf,
                  v_codigo_estado,
                  v_prioridad,
                  v_id_tipo_sub, ---id del subtipo
                  v_nro_tramite, --#5
                  v_fecha,
                  v_id_funcionario,
                  v_descripcion_sol
                  from sopte.thelp_desk c
                  inner join wf.testado_wf ew on ew.id_estado_wf = c.id_estado_wf  
                  where c.id_help_desk = v_parametros.id_help_desk;

                  --Recupera datos del estado
                  select
                  ew.id_tipo_estado,
                  te.codigo
                  into
                  v_id_tipo_estado,
                  v_codigo_estados
                  from wf.testado_wf ew
                  inner join wf.ttipo_estado te on te.id_tipo_estado = ew.id_tipo_estado
                  where ew.id_estado_wf = v_parametros.id_estado_wf_act;
                  
                  -- obtener datos tipo estado
                  select
                  te.codigo
                  into
                  v_codigo_estado_siguiente
                  from wf.ttipo_estado te
                  where te.id_tipo_estado = v_parametros.id_tipo_estado;
                  
                  if pxp.f_existe_parametro(p_tabla,'id_depto_wf') then
                      v_id_depto = v_parametros.id_depto_wf;
                  end if;
                  --Raise exception 'v_id_depto %',v_id_depto;                                    

                  if pxp.f_existe_parametro(p_tabla,'obs') then
                      v_obs=v_parametros.obs;
                  else
                      v_obs='---';
                  end if;



                  IF v_codigo_estado = 'pendiente' and v_codigo_estado_siguiente = 'asignado'  THEN
                       IF v_prioridad is null or v_prioridad = '' THEN
                        RAISE EXCEPTION 'Ingrese una prioridad antes de Asignar';
                       ELSIF v_id_tipo_sub is null or v_id_tipo_sub = '' THEN
                        RAISE EXCEPTION 'Ingrese un subtipo antes de Asignar';                       
                       END IF;
                  END IF;
                  
                  ---------------------------------------
                  -- REGISTRA EL SIGUIENTE ESTADO DEL WF
                  ---------------------------------------
                  --Configurar acceso directo para la alarma
                  v_acceso_directo = '';
                  v_clase = '';
                  v_parametros_ad = '';
                  v_tipo_noti = 'notificacion';
                  v_titulo  = 'Asignacion';
                  --raise exception 'v_codigo_estado_siguiente %',v_codigo_estado_siguiente;
                  if v_codigo_estado_siguiente not in('borrador','finalizado','anulado') then
                      v_acceso_directo = '../../../sis_soporte/vista/help_desk/HelpDeskAsis.php';
                      v_clase = 'HelpDeskAsis';
                      v_parametros_ad = '{filtro_directo:{campo:"help.id_proceso_wf",valor:"'||v_id_proceso_wf::varchar||'"}}';
                      v_tipo_noti = 'notificacion';
                      v_titulo  = 'Asignacion';
                  end if;


                  v_id_estado_actual = wf.f_registra_estado_wf(
                                                         v_parametros.id_tipo_estado,
                                                         v_parametros.id_funcionario_wf,
                                                         v_parametros.id_estado_wf_act,
                                                         v_id_proceso_wf,
                                                         p_id_usuario,
                                                         v_parametros._id_usuario_ai,
                                                         v_parametros._nombre_usuario_ai,
                                                         v_id_depto,                       --depto del estado anterior
                                                         v_obs,
                                                         v_acceso_directo,
                                                         v_clase,
                                                         v_parametros_ad,
                                                         v_tipo_noti,
                                                         v_titulo );
                ----Creamos una tabla temporal para el envio de correos --#7 
                CREATE TEMPORARY TABLE temporal(
                                      id serial,
                                      id_funcionario integer,
                                      email_empresa varchar,
                                      cargo         varchar,
                                      enviar        boolean
                                     ) ON COMMIT DROP;                                                               
                 --recuperamos los funcionarios administradores del depto e insertamos  a la temp --#7 
                 FOR v_admi_correos IN (
                 SELECT
                    fun.id_funcionario,
                    fun.email_empresa 
                 FROM param.tdepto depto
                 LEFT JOIN param.tdepto_usuario deptous on deptous.id_depto = depto.id_depto
                 LEFT JOIN segu.tusuario usu on usu.id_usuario = deptous.id_usuario
                 LEFT JOIN orga.vfuncionario_persona  fun on fun.id_persona = usu.id_persona 
                 WHERE deptous.cargo = 'administrador' and depto.id_depto = v_id_depto)LOOP
                            INSERT INTO 
                            temporal
                            (
                              id_funcionario, 
                              email_empresa, 
                              cargo,       
                              enviar       
                             )VALUES(
                             v_admi_correos.id_funcionario,
                             v_admi_correos.email_empresa,
                             'administrador',
                             TRUE                       
                             );
                    
                 END LOOP;
                                                       
                 --recuperamos los funcionarios encargado de los funcionarios solicitantes --#7 
                   WITH RECURSIVE path( id_funcionario, id_uo, gerencia, numero_nivel) AS (
                       
                      SELECT uofun.id_funcionario,uo.id_uo,uo.gerencia, no.numero_nivel
                      FROM orga.tuo_funcionario uofun
                        inner join orga.tuo uo on uo.id_uo = uofun.id_uo
                        inner join orga.tnivel_organizacional no on no.id_nivel_organizacional = uo.id_nivel_organizacional
                      where uofun.fecha_asignacion <= v_fecha::date and (uofun.fecha_finalizacion is null or uofun.fecha_finalizacion >= v_fecha::date)
                        and uofun.estado_reg = 'activo' and uofun.id_funcionario = v_id_funcionario
                    UNION
                       
                     SELECT uofun.id_funcionario,euo.id_uo_padre,uo.gerencia,no.numero_nivel
                     FROM orga.testructura_uo euo
                        inner join orga.tuo uo on uo.id_uo = euo.id_uo_padre
                        inner join orga.tnivel_organizacional no on no.id_nivel_organizacional = uo.id_nivel_organizacional
                        inner join path hijo on hijo.id_uo = euo.id_uo_hijo
                        left join orga.tuo_funcionario uofun on uo.id_uo = uofun.id_uo and uofun.estado_reg = 'activo' and uofun.fecha_asignacion <= v_fecha::date 
                        and (uofun.fecha_finalizacion is null or uofun.fecha_finalizacion >= v_fecha::date)
                                                    
                    )
                     SELECT 
                        pxp.aggarray(id_uo),
                        pxp.aggarray(id_funcionario),
                        pxp.aggarray(gerencia),  
                        pxp.aggarray(numero_nivel)
                     Into
                     vuo_id_uo, 
                     vuo_id_funcionario,
                     vuo_gerencia,
                     vuo_numero_nivel
                     FROM path
                    WHERE numero_nivel not in (5,7,8,9)and id_funcionario is not null;--#8 
                
                ---recuperamos los id_funcionarios de gg --#7  
                v_bandera_GG	= pxp.f_get_variable_global('orga_codigo_gerencia_general');
                
                v_fecha_now = now();
                v_record_gg = orga.f_obtener_gerente_x_codigo_uo(v_bandera_GG,v_fecha_now);
                v_record_id_funcionario_gg	= v_record_gg[1]::integer;
                 
                   
                 --Raise exception 'funcionario %',v_record_id_funcionario_gaf;    
                 --Raise exception 'funcionario %',vuo_id_funcionario[1];
                 IF vuo_id_funcionario[1] <> v_record_id_funcionario_gg THEN
                       IF v_id_funcionario = vuo_id_funcionario[1] THEN 
                          v_id_funcionario_encar = vuo_id_funcionario[2]::INTEGER;
                       ELSE
                          v_id_funcionario_encar = vuo_id_funcionario[1]::INTEGER;
                       END IF;
                  ELSE 
                          v_id_funcionario_encar  = v_record_id_funcionario_gg::integer;
                  END IF ;
                 --recuperamos el correo del encargado --#7 
                 SELECT
                    fu.desc_funcionario1,
                    fun.email_empresa
                 INTO
                    v_desc_funcionario_encar,
                    v_correo_encar
                 FROM orga.tfuncionario fun
                 LEFT JOIN orga.vfuncionario fu on fu.id_funcionario = fun.id_funcionario
                 WHERE fun.id_funcionario = v_id_funcionario_encar;
                 --insertamos en la tabla temporal --#7 
                 INSERT INTO 
                            temporal
                            (
                              id_funcionario, 
                              email_empresa, 
                              cargo,       
                              enviar       
                             )VALUES(
                             vuo_id_funcionario[1]::INTEGER,
                             v_correo_encar,
                             'encargado',
                             TRUE                       
                             );                                    
                 --#5 buscamos el correo del funcionario solicitante
                /*
                 if pxp.f_existe_parametro(p_tabla,'id_funcionario_wf') and v_parametros.id_funcionario_wf is not null then
                      v_id_funcionario = v_parametros.id_funcionario_wf;
                 end if;*/
                 SELECT
                    fu.desc_funcionario1,
                    fun.email_empresa
                 INTO
                    v_desc_funcionario,
                    v_correo
                 FROM orga.tfuncionario fun
                 LEFT JOIN orga.vfuncionario fu on fu.id_funcionario = fun.id_funcionario                 
                 WHERE fun.id_funcionario = v_id_funcionario ;
                 
                 --insertamos en la tabla temporal--#7 
                  INSERT INTO 
                            temporal
                            (
                              id_funcionario, 
                              email_empresa, 
                              cargo,       
                              enviar       
                             )VALUES(
                             v_parametros.id_funcionario_wf,
                             v_correo,
                             'solicitante',
                             TRUE                       
                             );
    
                 --Raise exception 'v_correo_encar %',v_correo_encar;                                                                            
                  --Acciones por estado siguiente que podrian realizarse
                  --#5 Insertamos una alarma para el funcionario solicitante
                  --#5 se aumento obs de wf en correo y alarma
                  if v_codigo_estado_siguiente in ('rechazado') then --#5  
                            IF v_codigo_estado_siguiente = 'rechazado' THEN
                                 v_descripcion_correo = '<font color="FF0000" size="5"><font size="4">NOTIFICACIÓN SOPORTE</font> </font><br><br><b></b>El motivo de la presente es notificarle sobre El Rechazo de la solicitud de soporte con número de trámite : <b>'||v_nro_tramite||'</b>.<br>'||v_obs||'.<br>Para mas información comuníquese con los administradores. Saludos<br>';
                                 v_titulo = 'Servicio de Soporte Rechazado: '||v_nro_tramite;             
                            END IF;
      
                             v_id_alarma = param.f_inserta_alarma(
                                    v_parametros.id_funcionario_wf,
                                    v_descripcion_correo,--par_descripcion
                                    v_acceso_directo,--acceso directo
                                    now()::date,--par_fecha: Indica la fecha de vencimiento de la alarma
                                    v_tipo_noti, --notificacion
                                    v_titulo,  --asunto
                                    p_id_usuario,
                                    v_clase, --clase
                                    v_titulo,--titulo
                                    v_parametros_ad,--par_parametros varchar,   parametros a mandar a la interface de acceso directo
                                    p_id_usuario, --usuario a quien va dirigida la alarma
                                    v_titulo,--titulo correo
                                    v_correo --correo funcionario
                                   );
                  ELSIF v_codigo_estado_siguiente in ('pendiente','resuelto') then--#7 
                                IF v_codigo_estado_siguiente = 'resuelto' THEN--#7 
                                      v_descripcion_correo='<font color="99CC00" size="5"><font size="4">NOTIFICACIÓN SOPORTE</font> </font><br><br><b></b>El motivo de la presente es notificarle sobre la resolución del soporte con número de trámite : <b>'||v_nro_tramite||'</b>.<br>'||v_obs||'.<br> Agradecerle que lo revise para su conformidad.<br>  Saludos<br>Nota: Se Adjunta copia a '||v_desc_funcionario_encar;
                                      v_titulo = 'Servicio de Soporte Resuelto: '||v_nro_tramite;
                                    IF vuo_id_funcionario[1] <> v_record_id_funcionario_gg THEN
                                        UPDATE temporal SET
                                            enviar = false
                                        WHERE cargo in ('administrador');
                                    ELSE
                                          UPDATE temporal SET
                                              enviar = false
                                          WHERE cargo in ('encargado','administrador');
                                    END IF;                        
                                ELSIF v_codigo_estado_siguiente = 'pendiente' THEN--#7 
                                       --raise exception 'ds %', v_desc_funcionario;
                                      v_descripcion_correo='<font color="99CC00" size="5"><font size="4">Solicitud Soporte</font> </font><br>Estado Actual :<b>'||v_codigo_estado_siguiente||'</b><br><b></b>El motivo de la presente es Solicitar el soporte con el número de trámite : <b>'||v_nro_tramite||'</b>.<br><br><b>Detalle :</b><br>'||lower(v_descripcion_sol)||'.<br><br> Agradecerles que lo revisen por Favor.<br> Atte. '||v_desc_funcionario||'<br>Saludos<br>Nota: Se Adjunta copia a '||v_desc_funcionario_encar;
                                      v_titulo = 'Solicitud de Soporte: '||v_nro_tramite;
                                     IF vuo_id_funcionario[1] <> v_record_id_funcionario_gg THEN
                                          UPDATE temporal SET
                                              enviar = false
                                          WHERE cargo in ('solicitante');
                                     ELSE
                                          UPDATE temporal SET
                                              enviar = false
                                          WHERE cargo in ('encargado','solicitante');
                                     END IF; 
                                END IF;
                                
                                FOR v_cor IN(
                                    SELECT 
                                        id_funcionario, 
                                        email_empresa, 
                                        cargo,       
                                        enviar 
                                    FROM temporal
                                    WHERE enviar = true                                                         
                                )LOOP    
                                v_id_alarma = param.f_inserta_alarma(
                                    v_cor.id_funcionario,
                                    v_descripcion_correo,--par_descripcion
                                    v_acceso_directo,--acceso directo
                                    now()::date,--par_fecha: Indica la fecha de vencimiento de la alarma
                                    v_tipo_noti, --notificacion
                                    v_titulo,  --asunto
                                    p_id_usuario,
                                    v_clase, --clase
                                    v_titulo,--titulo
                                    v_parametros_ad,--par_parametros varchar,   parametros a mandar a la interface de acceso directo
                                    p_id_usuario, --usuario a quien va dirigida la alarma
                                    v_titulo,--titulo correo
                                    v_cor.email_empresa --correo funcionario
                                   );
                                 END LOOP;  
                  END IF;
                  --------------------------------------
                  -- Registra los procesos disparados
                  --------------------------------------
                  for v_registros_proc in ( select * from json_populate_recordset(null::wf.proceso_disparado_wf, v_parametros.json_procesos::json)) loop

                      --Obtencion del codigo tipo proceso
                      select
                      tp.codigo
                      into
                      v_codigo_tipo_pro
                      from wf.ttipo_proceso tp
                      where tp.id_tipo_proceso =  v_registros_proc.id_tipo_proceso_pro;

                      --Disparar creacion de procesos seleccionados
                      select
                      ps_id_proceso_wf,
                      ps_id_estado_wf,
                      ps_codigo_estado
                      into
                      v_id_proceso_wf,
                      v_id_estado_wf,
                      v_codigo_estado
                      from wf.f_registra_proceso_disparado_wf(
                      p_id_usuario,
                      v_parametros._id_usuario_ai,
                      v_parametros._nombre_usuario_ai,
                      v_id_estado_actual,
                      v_registros_proc.id_funcionario_wf_pro,
                      v_registros_proc.id_depto_wf_pro,
                      v_registros_proc.obs_pro,
                      v_codigo_tipo_pro,
                      v_codigo_tipo_pro);

                  end loop;

                  --------------------------------------------------
                  --  ACTUALIZA EL NUEVO ESTADO DE LA CUENTA DOCUMENTADA
                  ----------------------------------------------------
                  IF pxp.f_existe_parametro(p_tabla,'id_cuenta_bancaria') THEN
                      v_id_cuenta_bancaria =  v_parametros.id_cuenta_bancaria;
                  END IF;

                  IF pxp.f_existe_parametro(p_tabla,'id_depto_lb') THEN
                      v_id_depto_lb =  v_parametros.id_depto_lb;
                  END IF;
                      if sopte.f_fun_inicio_help_desk_wf(
                              v_parametros.id_help_desk,
                              p_id_usuario,
                              v_parametros._id_usuario_ai,
                              v_parametros._nombre_usuario_ai,
                              v_id_estado_actual,
                              v_id_proceso_wf,
                              v_codigo_estado_siguiente,
                              v_id_depto_lb,
                              v_id_cuenta_bancaria,
                              v_codigo_estado
                          ) then

                      end if;
                  -- si hay mas de un estado disponible  preguntamos al usuario
                  v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Se realizo el cambio de estado del pago simple id='||v_parametros.id_help_desk);
                  v_resp = pxp.f_agrega_clave(v_resp,'operacion','cambio_exitoso');
                  -- Devuelve la respuesta
                  return v_resp;
              end;
              
          /*********************************
          #TRANSACCION:      'SOPTE_ANTEHELP_IME'
          #DESCRIPCION:     Retrocede el estado proyectos
          #AUTOR:           EGS
          #FECHA:           19/02/2019
          #ISSUE:           #4
          ***********************************/

          elseif(p_transaccion='SOPTE_ANTEHELP_IME')then

              begin
                 --raise exception'entra';
                  --Obtenemos datos basicos
                  select
                  c.id_help_desk,
                  ew.id_proceso_wf,    
                  c.id_estado_wf,
                  c.estado    
                  into
                  v_registros_proc
                  from sopte.thelp_desk c
                  inner join wf.testado_wf ew on ew.id_estado_wf = c.id_estado_wf  
                  where c.id_help_desk = v_parametros.id_help_desk;
              
               v_id_proceso_wf = v_registros_proc.id_proceso_wf;                  
               select
                    ps_id_tipo_estado,
                    ps_id_funcionario,
                    ps_id_usuario_reg,
                    ps_id_depto,
                    ps_codigo_estado,
                    ps_id_estado_wf_ant
                  into
                    v_id_tipo_estado,
                    v_id_funcionario,
                    v_id_usuario_reg,
                    v_id_depto,
                    v_codigo_estado,
                    v_id_estado_wf_ant
                  from wf.f_obtener_estado_ant_log_wf(v_parametros.id_estado_wf);
                  
                  -- si vuelve a estado borrador actualizamos
                  IF v_codigo_estado = 'borrador' THEN
                       UPDATE sopte.thelp_desk set
                            prioridad = NULL,
                            id_tipo_sub = null
                       WHERE id_help_desk =  v_registros_proc.id_help_desk ;
                  END IF;    
                  --Configurar acceso directo para la alarma
                        v_acceso_directo = '';
                        v_clase = '';
                        v_parametros_ad = '';
                        v_tipo_noti = 'notificacion';
                        v_titulo  = 'Visto Bueno';

                        if v_codigo_estado_siguiente not in('borrador','finalizado','anulado') then
                
                            v_acceso_directo = '../../../sis_soporte/vista/help_desk/HelpDesk.php';
                            v_clase = 'HelpDesk';
                            v_parametros_ad = '{filtro_directo:{campo:"help.id_proceso_wf",valor:"'||v_id_proceso_wf::varchar||'"}}';
                            v_tipo_noti = 'notificacion';
                            v_titulo  = 'Visto Bueno';
                        end if;


                        --Registra nuevo estado
                        v_id_estado_actual = wf.f_registra_estado_wf(
                            v_id_tipo_estado,                --  id_tipo_estado al que retrocede
                            v_id_funcionario,                --  funcionario del estado anterior
                            v_parametros.id_estado_wf,       --  estado actual ...
                            v_id_proceso_wf,                 --  id del proceso actual
                            p_id_usuario,                    -- usuario que registra
                            v_parametros._id_usuario_ai,
                            v_parametros._nombre_usuario_ai,
                            v_id_depto,                       --depto del estado anterior
                            '[RETROCESO] '|| v_parametros.obs,
                            v_acceso_directo,
                            v_clase,
                            v_parametros_ad,
                            v_tipo_noti,
                            v_titulo);
                        --raise exception 'v_id_estado_actual %', v_id_estado_actual;
                        if not sopte.f_fun_regreso_help_desk_wf(
                                                            v_parametros.id_help_desk,
                                                            p_id_usuario,
                                                            v_parametros._id_usuario_ai,
                                                            v_parametros._nombre_usuario_ai,
                                                            v_id_estado_actual,
                                                            v_parametros.id_proceso_wf,
                                                            v_codigo_estado) then

                            raise exception 'Error al retroceder estado';

                        end if; 
              
                  v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Se realizo el cambio de estado del pago simple)');
                  v_resp = pxp.f_agrega_clave(v_resp,'operacion','cambio_exitoso');

                  --Devuelve la respuesta
                  return v_resp;

              end;
    /*********************************    
     #TRANSACCION:  'SOPTE_HELPATRAS_MOD'
     #DESCRIPCION:    Modificacion de registros al asignar
     #AUTOR:        eddy.gutierrez    
     #FECHA:        22-02-2019 19:07:11
    ***********************************/

    elsif(p_transaccion='SOPTE_HELPATRAS_MOD')then

        begin
            --Sentencia de la modificacion
            update sopte.thelp_desk set
            id_usuario_mod = p_id_usuario,
            fecha_mod = now(),
            id_usuario_ai = v_parametros._id_usuario_ai,
            usuario_ai = v_parametros._nombre_usuario_ai,
            prioridad = v_parametros.prioridad,
            id_tipo = v_parametros.id_tipo,
            id_tipo_sub = v_parametros.id_sub_tipo  --#8  
            where id_help_desk=v_parametros.id_help_desk;
               
            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Help Desk modificado(a)'); 
            v_resp = pxp.f_agrega_clave(v_resp,'id_help_desk',v_parametros.id_help_desk::varchar);
               
            --Devuelve la respuesta
            return v_resp;
            
        end;   
         
    else
     
        raise exception 'Transaccion inexistente: %',p_transaccion;

    end if;

EXCEPTION
                
    WHEN OTHERS THEN
        v_resp='';
        v_resp = pxp.f_agrega_clave(v_resp,'mensaje',SQLERRM);
        v_resp = pxp.f_agrega_clave(v_resp,'codigo_error',SQLSTATE);
        v_resp = pxp.f_agrega_clave(v_resp,'procedimientos',v_nombre_funcion);
        raise exception '%',v_resp;
                        
END;
$body$
LANGUAGE 'plpgsql'
VOLATILE
CALLED ON NULL INPUT
SECURITY INVOKER
COST 100;