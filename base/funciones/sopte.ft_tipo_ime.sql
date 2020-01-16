--------------- SQL ---------------

CREATE OR REPLACE FUNCTION sopte.ft_tipo_ime (
  p_administrador integer,
  p_id_usuario integer,
  p_tabla varchar,
  p_transaccion varchar
)
RETURNS varchar AS
$body$
/**************************************************************************
 SISTEMA:		Soporte
 FUNCION: 		sopte.ft_tipo_ime
 DESCRIPCION:   Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'sopte.ttipo'
 AUTOR: 		 (eddy.gutierrez)
 FECHA:	        28-02-2019 16:38:04
 COMENTARIOS:
***************************************************************************
 HISTORIAL DE MODIFICACIONES:
#ISSUE				FECHA				AUTOR				DESCRIPCION
 #0				28-02-2019 16:38:04						    Funcion que gestiona las operaciones basicas (inserciones, modificaciones, eliminaciones de la tabla 'sopte.ttipo'
 #17                15/01/2020          EGS                 Inacti o activa un tipo de soporte
 ***************************************************************************/

DECLARE

	v_nro_requerimiento    	integer;
	v_parametros           	record;
	v_id_requerimiento     	integer;
	v_resp		            varchar;
	v_nombre_funcion        text;
	v_mensaje_error         text;
	v_id_tipo	            integer;
	v_codigo                varchar;
BEGIN

    v_nombre_funcion = 'sopte.ft_tipo_ime';
    v_parametros = pxp.f_get_record(p_tabla);

	/*********************************
 	#TRANSACCION:  'SOPTE_TIPSOP_INS'
 	#DESCRIPCION:	Insercion de registros
 	#AUTOR:		eddy.gutierrez
 	#FECHA:		28-02-2019 16:38:04
	***********************************/

	if(p_transaccion='SOPTE_TIPSOP_INS')then

        begin
            --validacion que codigo no se repita
            --quitamos espacios y lo volvemos en mayusculas
            v_parametros.codigo = REPLACE(v_parametros.codigo,' ', '');
            v_parametros.codigo = UPPER(v_parametros.codigo) ;
            SELECT
                tip.codigo
            INTO
                v_codigo
            FROM sopte.ttipo tip
            WHERE tip.codigo = v_parametros.codigo;
            IF v_codigo is not null THEN
                RAISE EXCEPTION 'El Codigo % ya Existe',v_codigo;
            END IF;


        	--Sentencia de la insercion
        	insert into sopte.ttipo(
			codigo,
			estado_reg,
			id_tipo_fk,
			descripcion,
			fecha_reg,
			usuario_ai,
			id_usuario_reg,
			id_usuario_ai,
			fecha_mod,
			id_usuario_mod,
            nombre
          	) values(
			v_parametros.codigo,
			'activo',
			v_parametros.id_tipo_fk,
			v_parametros.descripcion,
			now(),
			v_parametros._nombre_usuario_ai,
			p_id_usuario,
			v_parametros._id_usuario_ai,
			null,
			null,
            v_parametros.nombre



			)RETURNING id_tipo into v_id_tipo;

			--Definicion de la respuesta
			v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Tipo Soporte almacenado(a) con exito (id_tipo'||v_id_tipo||')');
            v_resp = pxp.f_agrega_clave(v_resp,'id_tipo',v_id_tipo::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'SOPTE_TIPSOP_MOD'
 	#DESCRIPCION:	Modificacion de registros
 	#AUTOR:		eddy.gutierrez
 	#FECHA:		28-02-2019 16:38:04
	***********************************/

	elsif(p_transaccion='SOPTE_TIPSOP_MOD')then

		begin
			--Sentencia de la modificacion
			update sopte.ttipo set
			codigo = v_parametros.codigo,
			id_tipo_fk = v_parametros.id_tipo_fk,
			descripcion = v_parametros.descripcion,
			fecha_mod = now(),
			id_usuario_mod = p_id_usuario,
			id_usuario_ai = v_parametros._id_usuario_ai,
			usuario_ai = v_parametros._nombre_usuario_ai,
            nombre = v_parametros.nombre
			where id_tipo=v_parametros.id_tipo;

			--Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Tipo Soporte modificado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_tipo',v_parametros.id_tipo::varchar);

            --Devuelve la respuesta
            return v_resp;

		end;

	/*********************************
 	#TRANSACCION:  'SOPTE_TIPSOP_ELI'
 	#DESCRIPCION:	Eliminacion de registros
 	#AUTOR:		eddy.gutierrez
 	#FECHA:		28-02-2019 16:38:04
	***********************************/

	elsif(p_transaccion='SOPTE_TIPSOP_ELI')then

		begin
			--Sentencia de la eliminacion
			delete from sopte.ttipo
            where id_tipo=v_parametros.id_tipo;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Tipo Soporte eliminado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_tipo',v_parametros.id_tipo::varchar);

            --Devuelve la respuesta
            return v_resp;

        end;
    /*********************************
 	#TRANSACCION:  'SOPTE_ACTI_MOD'
 	#DESCRIPCION:	modifica es estado reg del registro
 	#AUTOR:		eddy.gutierrez
 	#FECHA:		28-02-2019 16:38:04
    #ISSUE:     #17
	***********************************/

	elsif(p_transaccion='SOPTE_ACTI_MOD')then

		begin
			--Sentencia de la eliminacion
			UPDATE sopte.ttipo SET
                estado_reg = v_parametros.estado_reg
            WHERE id_tipo = v_parametros.id_tipo;

            UPDATE sopte.ttipo SET
                estado_reg = v_parametros.estado_reg
            WHERE id_tipo_fk = v_parametros.id_tipo;

            --Definicion de la respuesta
            v_resp = pxp.f_agrega_clave(v_resp,'mensaje','Tipo Soporte eliminado(a)');
            v_resp = pxp.f_agrega_clave(v_resp,'id_tipo',v_parametros.id_tipo::varchar);

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