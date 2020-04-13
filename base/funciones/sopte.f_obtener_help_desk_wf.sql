CREATE OR REPLACE FUNCTION sopte.f_obtener_help_desk_wf(p_administrador integer,
                                                        p_id_usuario integer,
                                                        p_tabla varchar,
                                                        p_transaccion varchar)
    RETURNS varchar
AS
$body$
/**************************************************************************
 SISTEMA:       Sistema
 FUNCION:       pro.f_obtener_help_desk_wf

 DESCRIPCION:   Obtiene datos de help desk
 AUTOR:         valvarado
 FECHA:         08/04/2020
 COMENTARIOS:

***************************************************************************/
DECLARE

    v_nombre_funcion text;
    v_resp           varchar;
    v_id_tipo_estado integer;
    v_id_dpto        integer;
    v_parametros     record;
    v_registros      record;
    v_consulta       varchar;
BEGIN

    v_nombre_funcion = 'sopte.f_obtener_help_desk_wf';
    v_parametros = pxp.f_get_record(p_tabla);
    select c.id_help_desk,
           c.estado,
           c.id_estado_wf,
           ew.id_funcionario,
           c.id_proceso_wf
    into v_registros
    from sopte.thelp_desk c
             inner join wf.testado_wf ew on ew.id_estado_wf = c.id_estado_wf
    where c.id_help_desk = v_parametros.id_help_desk;

    select te.id_tipo_estado
    into v_id_tipo_estado
    FROM wf.ttipo_estado te
             join wf.ttipo_proceso tp on tp.id_tipo_proceso = te.id_tipo_proceso
             join wf.tproceso_macro pm on pm.id_proceso_macro = tp.id_proceso_macro
    WHERE pm.codigo = 'SOPTE'
      AND te.codigo = 'pendiente';

    select dpto.id_depto into v_id_dpto from param.tdepto dpto where dpto.codigo = 'SOPTE-CBBA-TI';
    v_consulta := 'select c.id_help_desk,
               c.id_proceso_wf,
               c.id_estado_wf,
               ' || coalesce(v_id_dpto,'0') || ' as id_depto_wf,
               ' || coalesce(v_id_tipo_estado,'0') || ' as id_tipo_estado
        from sopte.thelp_desk c
                 inner join wf.testado_wf ew on ew.id_estado_wf = c.id_estado_wf
        where c.id_help_desk ='  || v_parametros.id_help_desk;

    RETURN v_consulta;
EXCEPTION

    WHEN OTHERS THEN
        v_resp = '';
        v_resp = pxp.f_agrega_clave(v_resp, 'mensaje', SQLERRM);
        v_resp = pxp.f_agrega_clave(v_resp, 'codigo_error', SQLSTATE);
        v_resp = pxp.f_agrega_clave(v_resp, 'procedimientos', v_nombre_funcion);
        raise exception '%',v_resp;

END;
$body$
    LANGUAGE 'plpgsql'
    VOLATILE
    CALLED ON NULL INPUT
    SECURITY INVOKER
    COST 100;