--------------- SQL ---------------

CREATE OR REPLACE FUNCTION sopte.ft_help_desk_sel (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:       Soporte
 FUNCION:         sopte.ft_help_desk_sel
 DESCRIPCION:   Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'wf.thelp_desk'
 AUTOR:          (eddy.gutierrez)
 FECHA:            22-02-2019 19:07:11
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE                FECHA                AUTOR                DESCRIPCION
 #0                22-02-2019 19:07:11                          Funcion que devuelve conjuntos de registros de las consultas relacionadas con la tabla 'wf.thelp_desk'
 #3 EndeEtr           25/03/2019            EGS                 Mejora Filtro
 #4 EndeEtr           08/04/2019            EGS                 Se agrego que los administradores de work flow de los deptos vean todos los tramites
 #5 EndeEtr           09/04/2019            EGS                 Se visualiza obs de Wf en estado resuelto y rechazado
 #7 EndeEtr           22/04/2019            EGS                 Se arregla filtros

 ***************************************************************************/

DECLARE

    v_consulta            varchar;
    v_parametros          record;
    v_nombre_funcion       text;
    v_resp                varchar;
    v_filtro              varchar;
    sw_obs                varchar;
    v_cargo               varchar;
    v_depto                 VARCHAR;
    v_item              record;

BEGIN

    v_nombre_funcion = 'sopte.ft_help_desk_sel';
    v_parametros = pxp.f_get_record(p_tabla);

    /*********************************
     #TRANSACCION:  'SOPTE_HELP_SEL'
     #DESCRIPCION:    Consulta de datos
     #AUTOR:        eddy.gutierrez
     #FECHA:        22-02-2019 19:07:11
    ***********************************/

    if(p_transaccion='SOPTE_HELP_SEL')then

        begin
                --#4 recuperando los deptos pertenecientes al usuario
                v_depto = '';
                v_cargo = 'no_adm';
                FOR v_item in (SELECT
                      deptous.id_depto,
                      deptous.cargo
                FROM param.tdepto_usuario deptous
                WHERE deptous.id_usuario = p_id_usuario )LOOP
                        v_depto = v_item.id_depto||','||v_depto;
                        IF v_item.cargo = 'administrador' THEN
                            v_cargo = v_item.cargo;
                        END IF;
                END LOOP;
                   v_depto = v_depto||']';
                   v_depto=REPLACE(v_depto,',]', '');

                   --#4 se agrego que los administrdores de work flow vean todos los soportes por sus deptos
                 IF p_administrador !=1 then
                 --si es la vista del help y estan en estado asignado y finalizado muestra solo os registristros del funcionario solicitante
                    IF v_parametros.nombreVista = 'HelpDesk'   THEN --#3

                        v_filtro = '(help.id_funcionario = '||v_parametros.id_funcionario_usu::varchar||' ) and ';

                    --Si no soy administrador y estoy en pendiente no veo nada
                    ElSIF v_parametros.nombreVista = 'HelpDeskAsis' and v_cargo <> 'administrador' and v_parametros.estado = 'pendiente' THEN
                        v_filtro = 'help.id_help_desk = 0 and';
                     -- si no soy administrador y estoy en estado asignado y proceso solo veo lo que que se me asigno en los deptos que pertenece
                    ELSIF v_parametros.nombreVista = 'HelpDeskAsis' and v_cargo <> 'administrador' and (v_parametros.estado = 'asignado' or v_parametros.estado ='proceso')  THEN --#3 si esde estado asignado solo muestra los registros que le pertenecen al asignarle

                        v_filtro = '(ew.id_funcionario = '||v_parametros.id_funcionario_usu::varchar||' ) and ew.id_depto in ('||v_depto||') and';

                    ELSE
                    v_filtro = ' ';
                    END IF;
                 ELSE
                 v_filtro = ' ';
                 END IF;

                 IF v_parametros.nombreVista = 'HelpDeskAsis'  THEN
                        sw_obs = 'ew.obs';
                 ELSE
                        sw_obs = ' ''--''::text';
                 END IF;

            --#5 cambio que al ser estado rechazdo o resuelto el solicitante vea sus descripcion de obs
            --Sentencia de la consulta
            v_consulta:='select
                        help.id_help_desk,
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
                        usu1.cuenta as usr_reg,
                        usu2.cuenta as usr_mod,
                        help.estado,
                        fun.desc_funcionario1::varchar as desc_funcionario,
                        te.etapa,
                        CASE
                        WHEN help.estado = ''rechazado'' or help.estado = ''resuelto'' THEN
                        ew.obs
                        ELSE
                        '||sw_obs||'
                        END as obs,
                        tip.nombre as nombre_tipo,
                        help.descripcion,
                        funi.desc_funcionario1::varchar as desc_funcionario_asignado,
                        help.prioridad,
                        help.id_tipo,
                        help.id_tipo_sub,
                        subti.nombre as nombre_subtipo,
                        cat.descripcion as desc_prioridad
                        from sopte.thelp_desk help
                        inner join segu.tusuario usu1 on usu1.id_usuario = help.id_usuario_reg
                        left join segu.tusuario usu2 on usu2.id_usuario = help.id_usuario_mod
                        inner join orga.vfuncionario fun on fun.id_funcionario = help.id_funcionario
                        inner join wf.testado_wf ew on ew.id_proceso_wf = help.id_proceso_wf and  ew.estado_reg = ''activo''
                        left join orga.vfuncionario funi on funi.id_funcionario = ew.id_funcionario
                        inner join wf.ttipo_estado te on te.id_tipo_estado = ew.id_tipo_estado
                        left join sopte.ttipo tip on tip.id_tipo = help.id_tipo
                        left join sopte.ttipo subti on subti.id_tipo =help.id_tipo_sub
                        left join param.tcatalogo cat on cat.codigo = help.prioridad
                        where  '||v_filtro||'';

            --Definicion de la respuesta
            v_consulta:=v_consulta||v_parametros.filtro;
            v_consulta:=v_consulta||' order by ' ||v_parametros.ordenacion|| ' ' || v_parametros.dir_ordenacion || ' limit ' || v_parametros.cantidad || ' offset ' || v_parametros.puntero;
            --raise exception 'v_consulta %',v_consulta;
            --Devuelve la respuesta
            return v_consulta;

        end;

    /*********************************
     #TRANSACCION:  'SOPTE_HELP_CONT'
     #DESCRIPCION:    Conteo de registros
     #AUTOR:        eddy.gutierrez
     #FECHA:        22-02-2019 19:07:11
    ***********************************/

    elsif(p_transaccion='SOPTE_HELP_CONT')then

        begin
             v_depto = '';
                v_cargo = 'no_adm';
                FOR v_item in (SELECT
                      deptous.id_depto,
                      deptous.cargo
                FROM param.tdepto_usuario deptous
                WHERE deptous.id_usuario = p_id_usuario )LOOP
                        v_depto = v_item.id_depto||','||v_depto;
                        IF v_item.cargo = 'administrador' THEN
                            v_cargo = v_item.cargo;
                        END IF;
                END LOOP;
                   v_depto = v_depto||']';
                   v_depto=REPLACE(v_depto,',]', '');

                   --#4 se agrego que los administrdores de work flow vean todos los soportes por sus deptos
                 IF p_administrador !=1 then
                 --si es la vista del help y estan en estado asignado y finalizado muestra solo os registristros del funcionario solicitante
                    IF v_parametros.nombreVista = 'HelpDesk'   THEN --#3

                        v_filtro = '(help.id_funcionario = '||v_parametros.id_funcionario_usu::varchar||' ) and ';

                    --Si no soy administrador y estoy en pendiente no veo nada
                    ElSIF v_parametros.nombreVista = 'HelpDeskAsis' and v_cargo <> 'administrador' and v_parametros.estado = 'pendiente' THEN
                        v_filtro = 'help.id_help_desk = 0 and';
                     -- si no soy administrador y estoy en estado asignado y proceso solo veo lo que que se me asigno en los deptos que pertenece
                    ELSIF v_parametros.nombreVista = 'HelpDeskAsis' and v_cargo <> 'administrador' and (v_parametros.estado = 'asignado' or v_parametros.estado ='proceso')  THEN --#3 si esde estado asignado solo muestra los registros que le pertenecen al asignarle

                        v_filtro = '(ew.id_funcionario = '||v_parametros.id_funcionario_usu::varchar||' ) and ew.id_depto in ('||v_depto||') and';

                    ELSE
                    v_filtro = ' ';
                    END IF;
                 ELSE
                 v_filtro = ' ';
                 END IF;

                 IF v_parametros.nombreVista = 'HelpDeskAsis'  THEN
                        sw_obs = 'ew.obs';
                 ELSE
                        sw_obs = ' ''--''::text';
                 END IF;
            --Sentencia de la consulta de conteo de registros --#7
            v_consulta:='select count(id_help_desk)
                        from sopte.thelp_desk help
                        inner join segu.tusuario usu1 on usu1.id_usuario = help.id_usuario_reg
                        left join segu.tusuario usu2 on usu2.id_usuario = help.id_usuario_mod
                        inner join orga.vfuncionario fun on fun.id_funcionario = help.id_funcionario
                        inner join wf.testado_wf ew on ew.id_proceso_wf = help.id_proceso_wf and  ew.estado_reg = ''activo''
                        left join orga.vfuncionario funi on funi.id_funcionario = ew.id_funcionario
                        inner join wf.ttipo_estado te on te.id_tipo_estado = ew.id_tipo_estado
                        left join sopte.ttipo tip on tip.id_tipo = help.id_tipo
                        left join sopte.ttipo subti on subti.id_tipo =help.id_tipo_sub
                        left join param.tcatalogo cat on cat.codigo = help.prioridad
                        where '||v_filtro||'';

            --Definicion de la respuesta
            v_consulta:=v_consulta||v_parametros.filtro;

            --Devuelve la respuesta
            return v_consulta;

        end;

    else

        raise exception 'Transaccion inexistente';

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