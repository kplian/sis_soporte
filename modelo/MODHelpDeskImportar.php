<?php
/**
 * @package pXP
 * @file gen-MODHelpDeskImportar.php
 * @author  (valvarado)
 * @date 15/04/2020 20:44:00
 * @description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
 *    ISUE                FECHA                AUTHOR                    DESCRIPCION
 * */

class MODHelpDeskImportar extends MODbase
{

    function __construct(CTParametro $pParam)
    {
        parent::__construct($pParam);
    }

    function insertarHelpDesk()
    {
        $this->procedimiento = 'sopte.ft_help_desk_ime';
        $this->transaccion = 'SOPTE_HELP_INS';
        $this->tipo_procedimiento = 'IME';
        $this->tipo_conexion = 'seguridad';
        //Define los parametros para la funcion
        $this->setParametro('id_funcionario', 'id_funcionario', 'int4');
        $this->setParametro('id_proceso_wf', 'id_proceso_wf', 'int4');
        $this->setParametro('id_estado_wf', 'id_estado_wf', 'int4');
        $this->setParametro('fecha', 'fecha', 'date');
        $this->setParametro('estado_reg', 'estado_reg', 'varchar');
        $this->setParametro('nro_tramite', 'nro_tramite', 'varchar');
        $this->setParametro('descripcion', 'descripcion', 'codigo_html');
        $this->setParametro('id_tipo', 'id_tipo', 'int4');
        $this->setParametro('numero_ref', 'numero_ref', 'integer');//#14
        $this->setParametro('numero_correo', 'numero_correo', 'int4');

        $this->armarConsulta();
        $this->ejecutarConsulta();

        return $this->respuesta;
    }

    function siguienteEstado()
    {
        $this->procedimiento = 'sopte.ft_help_desk_ime';
        $this->transaccion = 'SOPTE_SIGEHELP_INS';
        $this->tipo_procedimiento = 'IME';
        $this->tipo_conexion = 'seguridad';

        $this->setParametro('id_help_desk', 'id_help_desk', 'int4');
        $this->setParametro('id_proceso_wf_act', 'id_proceso_wf_act', 'int4');
        $this->setParametro('id_estado_wf_act', 'id_estado_wf_act', 'int4');
        $this->setParametro('id_funcionario_usu', 'id_funcionario_usu', 'int4');
        $this->setParametro('id_tipo_estado', 'id_tipo_estado', 'int4');
        $this->setParametro('id_funcionario_wf', 'id_funcionario_wf', 'int4');
        $this->setParametro('id_depto_wf', 'id_depto_wf', 'int4');
        $this->setParametro('obs', 'obs', 'text');
        $this->setParametro('json_procesos', 'json_procesos', 'text');
        $this->setParametro('id_depto_lb', 'id_depto_lb', 'int4');
        $this->setParametro('id_cuenta_bancaria', 'id_cuenta_bancaria', 'int4');
        $this->setParametro('id_sub_tipo', 'id_sub_tipo', 'varchar');//#10
        $this->setParametro('prioridad', 'prioridad', 'varchar');//#10

        $this->armarConsulta();
        $this->ejecutarConsulta();

        return $this->respuesta;
    }

    function obtenerFuncionario()
    {
        $this->procedimiento = 'orga.ft_funcionario_sel';
        $this->transaccion = 'RH_FUNCIOCAR_SEL';
        $this->tipo_procedimiento = 'SEL';
        $this->tipo_conexion = 'seguridad';
        $this->setCount(false);
        $this->setParametro('estado_reg_fun', 'estado_reg_fun', 'varchar');
        $this->setParametro('estado_reg_asi', 'estado_reg_asi', 'varchar');
        $this->captura('id_uo_funcionario', 'integer');
        $this->captura('id_funcionario', 'integer');
        $this->captura('desc_funcionario1', 'text');
        $this->captura('desc_funcionario2', 'text');
        $this->captura('id_uo', 'integer');
        $this->captura('nombre_cargo', 'varchar');
        $this->captura('fecha_asignacion', 'date');
        $this->captura('fecha_finalizacion', 'date');
        $this->captura('num_doc', 'integer');
        $this->captura('ci', 'varchar');
        $this->captura('codigo', 'varchar');
        $this->captura('email_empresa', 'varchar');
        $this->captura('estado_reg_fun', 'varchar');
        $this->captura('estado_reg_asi', 'varchar');
        $this->captura('id_cargo', 'integer');
        $this->captura('descripcion_cargo', 'varchar');
        $this->captura('cargo_codigo', 'varchar');
        $this->captura('id_lugar', 'integer');
        $this->captura('id_oficina', 'integer');
        $this->captura('lugar_nombre', 'varchar');
        $this->captura('oficina_nombre', 'varchar');

        $this->setParametro('antiguedad_anterior', 'antiguedad_anterior', 'varchar');

        $this->armarConsulta();
        $this->ejecutarConsulta();
        return $this->respuesta;
    }

    function obtenerDatosWFHelpDesk()
    {
        $this->procedimiento = 'sopte.f_obtener_help_desk_wf';
        $this->transaccion = 'SOPTE_OBWF_SEL';
        $this->tipo_procedimiento = 'SEL';
        $this->tipo_conexion = 'seguridad';
        $this->setCount(false);
        $this->setParametro('id_help_desk', 'id_help_desk', 'int4');
        $this->captura('id_help_desk', 'int4');
        $this->captura('id_proceso_wf', 'int4');
        $this->captura('id_estado_wf', 'int4');
        $this->captura('id_depto_wf', 'integer');
        $this->captura('id_tipo_estado', 'integer');

        $this->armarConsulta();
        $this->ejecutarConsulta();
        return $this->respuesta;
    }
}

?>