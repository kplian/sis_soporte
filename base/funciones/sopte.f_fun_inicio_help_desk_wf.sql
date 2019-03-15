CREATE OR REPLACE FUNCTION sopte.f_fun_inicio_help_desk_wf (
  p_id_help_desk integer,
  p_id_usuario integer,
  p_id_usuario_ai integer,
  p_usuario_ai varchar,
  p_id_estado_wf integer,
  p_id_proceso_wf integer,
  p_codigo_estado varchar,
  p_id_depto_lb integer = NULL::integer,
  p_id_cuenta_bancaria integer = NULL::integer,
  p_estado_anterior varchar = 'no'::character varying
)
RETURNS boolean AS
$body$
/**************************************************************************
 SISTEMA:       Sistema 
 FUNCION:       pro.f_fun_inicio_presolicitud_wf
                
 DESCRIPCION:   Actualiza los estados en proceso WF
 AUTOR:         EGS
 FECHA:         19/02/2019
 COMENTARIOS: 

 ***************************************************************************
 HISTORIAL DE MODIFICACIONES:

 DESCRIPCION:   
 AUTOR:         
 FECHA:         
***************************************************************************/
DECLARE

    v_nombre_funcion                text;
    v_resp                          varchar;     
    v_mensaje                       varchar;    
    v_registros                     record;
    v_rec                           record;

BEGIN
    
    --Identificación del nombre de la función
    v_nombre_funcion = 'sopte.f_fun_inicio_help_desk_wf';

    --raise exception 'entra';
    ----------------------------------------------
    --Obtención de datos de la cuenta documentada
    ----------------------------------------------
    select 
        c.id_help_desk,
        c.estado,
        c.id_estado_wf,
        ew.id_funcionario
        into v_rec
    from sopte.thelp_desk c
    inner join wf.testado_wf ew on ew.id_estado_wf =  c.id_estado_wf
    where c.id_help_desk = p_id_help_desk;

    --Actualización del estado de la solicitud
    update sopte.thelp_desk set 
    id_estado_wf    = p_id_estado_wf,
    estado          = p_codigo_estado,
    id_usuario_mod  = p_id_usuario,
    id_usuario_ai   = p_id_usuario_ai,
    usuario_ai      = p_usuario_ai,
    fecha_mod       = now()                     
    where id_help_desk = p_id_help_desk;
    --Respuesta
    return true;

EXCEPTION
                    
    WHEN OTHERS THEN

        v_resp = '';
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