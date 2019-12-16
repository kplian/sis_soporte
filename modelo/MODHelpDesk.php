<?php
/**
*@package pXP
*@file gen-MODHelpDesk.php
*@author  (eddy.gutierrez)
*@date 22-02-2019 19:07:11
*@description Clase que envia los parametros requeridos a la Base de datos para la ejecucion de las funciones, y que recibe la respuesta del resultado de la ejecucion de las mismas
 * 	ISUE				FECHA				AUTHOR					DESCRIPCION	
 	#8					18/03/2019			EGS						Se  agrega y diferencia tipo y sub_tipo
 	#10 EndeEtr		  1/07/2019			    EGS					    se agrego campos extras a wf
 * 
 * */

class MODHelpDesk extends MODbase{
	
	function __construct(CTParametro $pParam){
		parent::__construct($pParam);
	}
			
	function listarHelpDesk(){
		//Definicion de variables para ejecucion del procedimientp
		$this->procedimiento='sopte.ft_help_desk_sel';
		$this->transaccion='SOPTE_HELP_SEL';
		$this->tipo_procedimiento='SEL';//tipo de transaccion
				
		//Definicion de la lista del resultado del query
		
		$this->setParametro('id_funcionario_usu','id_funcionario_usu','int4');
		$this->setParametro('estado','estado','varchar');
		$this->setParametro('nombreVista','nombreVista','varchar');
				
		$this->captura('id_help_desk','int4');
		$this->captura('id_funcionario','int4');
		$this->captura('id_proceso_wf','int4');
		$this->captura('id_estado_wf','int4');
		$this->captura('fecha','date');
		$this->captura('estado_reg','varchar');
		$this->captura('nro_tramite','varchar');
		$this->captura('id_usuario_ai','int4');
		$this->captura('usuario_ai','varchar');
		$this->captura('fecha_reg','timestamp');
		$this->captura('id_usuario_reg','int4');
		$this->captura('id_usuario_mod','int4');
		$this->captura('fecha_mod','timestamp');
		$this->captura('usr_reg','varchar');
		$this->captura('usr_mod','varchar');
		$this->captura('estado','varchar');
		$this->captura('desc_funcionario','varchar');
		$this->captura('etapa','varchar');
		$this->captura('obs','text');
		$this->captura('nombre_tipo','varchar');
		$this->captura('descripcion','varchar');
		$this->captura('desc_funcionario_asignado','varchar');
		$this->captura('prioridad','varchar');
		$this->captura('id_tipo','int4');
		$this->captura('id_tipo_sub','int4');
		$this->captura('nombre_subtipo','varchar');
		$this->captura('desc_prioridad','varchar');
        $this->captura('numero_ref','integer');//#14
		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();
		
		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function insertarHelpDesk(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='sopte.ft_help_desk_ime';
		$this->transaccion='SOPTE_HELP_INS';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_funcionario','id_funcionario','int4');
		$this->setParametro('id_proceso_wf','id_proceso_wf','int4');
		$this->setParametro('id_estado_wf','id_estado_wf','int4');
		$this->setParametro('fecha','fecha','date');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('nro_tramite','nro_tramite','varchar');
		$this->setParametro('descripcion','descripcion','varchar');
		$this->setParametro('id_tipo','id_tipo','int4');
        $this->setParametro('numero_ref','numero_ref','integer');//#14

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function modificarHelpDesk(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='sopte.ft_help_desk_ime';
		$this->transaccion='SOPTE_HELP_MOD';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_help_desk','id_help_desk','int4');
		$this->setParametro('id_funcionario','id_funcionario','int4');
		$this->setParametro('id_proceso_wf','id_proceso_wf','int4');
		$this->setParametro('id_estado_wf','id_estado_wf','int4');
		$this->setParametro('fecha','fecha','date');
		$this->setParametro('estado_reg','estado_reg','varchar');
		$this->setParametro('nro_tramite','nro_tramite','varchar');
		$this->setParametro('descripcion','descripcion','varchar');
		$this->setParametro('id_tipo','id_tipo','int4');
        $this->setParametro('numero_ref','numero_ref','integer');//#14
		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
			
	function eliminarHelpDesk(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='sopte.ft_help_desk_ime';
		$this->transaccion='SOPTE_HELP_ELI';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_help_desk','id_help_desk','int4');

		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
		 function siguienteEstado(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento = 'sopte.ft_help_desk_ime';
        $this->transaccion = 'SOPTE_SIGEHELP_INS';
        $this->tipo_procedimiento = 'IME';
   
        //Define los parametros para la funcion
        $this->setParametro('id_help_desk','id_help_desk','int4');
        $this->setParametro('id_proceso_wf_act','id_proceso_wf_act','int4');
        $this->setParametro('id_estado_wf_act','id_estado_wf_act','int4');
        $this->setParametro('id_funcionario_usu','id_funcionario_usu','int4');
        $this->setParametro('id_tipo_estado','id_tipo_estado','int4');
        $this->setParametro('id_funcionario_wf','id_funcionario_wf','int4');
        $this->setParametro('id_depto_wf','id_depto_wf','int4');		
        $this->setParametro('obs','obs','text');
        $this->setParametro('json_procesos','json_procesos','text');

        $this->setParametro('id_depto_lb','id_depto_lb','int4');
		$this->setParametro('id_cuenta_bancaria','id_cuenta_bancaria','int4');
		$this->setParametro('id_sub_tipo','id_sub_tipo','varchar');//#10
		$this->setParametro('prioridad','prioridad','varchar');//#10
        //Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();
        //Devuelve la respuesta
        return $this->respuesta;
    }
    function anteriorEstado(){
        //Definicion de variables para ejecucion del procedimiento
        $this->procedimiento='sopte.ft_help_desk_ime';
        $this->transaccion='SOPTE_ANTEHELP_IME';
        $this->tipo_procedimiento='IME';                
        //Define los parametros para la funcion
         $this->setParametro('id_help_desk','id_help_desk','int4');
        $this->setParametro('id_proceso_wf','id_proceso_wf','int4');
        $this->setParametro('id_estado_wf','id_estado_wf','int4');
		$this->setParametro('obs','obs','varchar');
		$this->setParametro('estado_destino','estado_destino','varchar');		
		//Ejecuta la instruccion
        $this->armarConsulta();
        $this->ejecutarConsulta();

        //Devuelve la respuesta
        return $this->respuesta;
    }
	function insertarAtributoAsignacion(){
		//Definicion de variables para ejecucion del procedimiento
		$this->procedimiento='sopte.ft_help_desk_ime';
		$this->transaccion='SOPTE_HELPATRAS_MOD';
		$this->tipo_procedimiento='IME';
				
		//Define los parametros para la funcion
		$this->setParametro('id_help_desk','id_help_desk','int4');
		$this->setParametro('prioridad','prioridad','varchar');
		$this->setParametro('id_tipo','id_tipo','int4');
		$this->setParametro('id_sub_tipo','id_sub_tipo','int4');//#8


		//Ejecuta la instruccion
		$this->armarConsulta();
		$this->ejecutarConsulta();

		//Devuelve la respuesta
		return $this->respuesta;
	}
	
			
}
?>