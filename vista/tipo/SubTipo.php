<?php
/**
*@package pXP
*@file SubTipo.php
*@author  (eddy.gutierrez)
*@date 28-02-2019 16:38:04
*@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
*/

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.SubTipo ={
	
	require:'../../../sis_soporte/vista/tipo/TipoBase.php',
	requireclase:'Phx.vista.TipoBase',
	title:'SubTipo',
	nombreVista: 'SubTipo',
	
	constructor:function(config){
		this.maestro=config.maestro;
    	//llama al constructor de la clase padre
		Phx.vista.Tipo.superclass.constructor.call(this,config);
		this.init();
		this.bloquearMenus();
		//this.load({params:{start:0, limit:this.tam_pag}})
	},
	onReloadPage: function (m) {
				//alert ('asda');				  
		            this.maestro = m;
		            this.store.baseParams = {id_tipo: this.maestro.id_tipo, nombreVista:this.nombreVista ,tipo:'no'}; 
		            this.load({params: {start: 0, limit: 50}});
		            //this.Atributos[1].valorInicial = this.maestro.id_tipo;
		            this.Atributos[this.getIndAtributo('id_tipo_fk')].valorInicial = this.maestro.id_tipo;

		            
		            
	},
			
	
	}
</script>
		
		