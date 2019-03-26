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
 ***************************************************************************/

DECLARE

    v_consulta            varchar;
    v_parametros          record;
    v_nombre_funcion       text;
    v_resp                varchar;
    v_filtro              varchar; 
    sw_obs                varchar;           
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
                 IF p_administrador !=1  then
                 --si es la vista del help y estan en estado asignado y finalizado muestra solo os registristros del funcionario solicitante
                    IF v_parametros.nombreVista = 'HelpDesk'   THEN --#3
                      v_filtro = '(help.id_funcionario = '||v_parametros.id_funcionario_usu::varchar||' ) and ';
                    ELSIF v_parametros.nombreVista = 'HelpDeskAsis' and (v_parametros.estado = 'asignado' or v_parametros.estado ='proceso'or v_parametros.estado ='resuelto')  THEN --#3 si esde estado asignado solo muestra los registros que le pertenecen al asignarle
                    v_filtro = '(ew.id_funcionario = '||v_parametros.id_funcionario_usu::varchar||' ) and ';  
                    ELSE
                    v_filtro = ' ';
                    END IF;
                 ELSE
                 v_filtro = ' ';
                 END IF;
                 
                 IF v_parametros.nombreVista = 'HelpDeskAsis'  THEN
                        sw_obs = 'ew.obs,';
                 ELSE
                        sw_obs = ' ''--''::text as obs,';
                 END IF;
        
        
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
                        '||sw_obs||'                        
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
            --Sentencia de la consulta de conteo de registros
            v_consulta:='select count(id_help_desk)
                        from sopte.thelp_desk help
                        inner join segu.tusuario usu1 on usu1.id_usuario = help.id_usuario_reg
                        left join segu.tusuario usu2 on usu2.id_usuario = help.id_usuario_mod
                        inner join orga.vfuncionario fun on fun.id_funcionario = help.id_funcionario
                        inner join wf.testado_wf ew on ew.id_proceso_wf = help.id_proceso_wf and  ew.estado_reg = ''activo''
                        inner join wf.ttipo_estado te on te.id_tipo_estado = ew.id_tipo_estado
                        where ';
            
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