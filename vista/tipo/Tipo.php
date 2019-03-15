<?php
/**
*@package pXP
*@file gen-Tipo.php
*@author  (eddy.gutierrez)
*@date 28-02-2019 16:38:04
*@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
*/

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.Tipo ={
	
	require:'../../../sis_soporte/vista/tipo/TipoBase.php',
	requireclase:'Phx.vista.TipoBase',
	title:'Tipo',
	nombreVista: 'Tipo',
	
	constructor:function(config){
		this.maestro=config.maestro;
    	//llama al constructor de la clase padre
		Phx.vista.Tipo.superclass.constructor.call(this,config);
		this.init();
		this.load({params:{start:0, limit:this.tam_pag, nombreVista: this.nombreVista, tipo:'si'}})
	},
		
	tabsouth: [{
		 url:'../../../sis_soporte/vista/tipo/SubTipo.php',
          title:'SubTipo', 
          width:'50%',
          height:'50%',
          cls:'SubTipo'
	}],		
	
	}
</script>
		
		