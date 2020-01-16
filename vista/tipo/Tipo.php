<?php
/**
*@package pXP
*@file gen-Tipo.php
*@author  (eddy.gutierrez)
*@date 28-02-2019 16:38:04
*@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
 ISSUE       FECHA           AUTHOR          DESCRIPCION
 #17          15/01/2020    EGS              Activa o inactiva un tipo de soporte
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
        this.grid.addListener('cellclick', this.oncellclick,this);//#17
        this.iniciarEventos();
		this.load({params:{start:0, limit:this.tam_pag, nombreVista: this.nombreVista, tipo:'si'}})

        this.addButton('btnActivo', {//#17
            text: 'Activar',
            iconCls: 'bchecked',
            disabled: false,
            handler: function () {
                this.activaInactivarTipo('activo')
            },
            tooltip: '<b>Activar</b>'
        });

        this.addButton('btnInactivo', {//#17
            text: 'Inactivar',
            iconCls: 'bunchecked',
            disabled: false,
            handler: function () {
                this.activaInactivarTipo('Inactivo')
            },
            tooltip: '<b>Inactivar</b>'
        });
        this.getBoton('btnActivo').hide();//#17
        this.getBoton('btnInactivo').hide();//#17

	},
    iniciarEventos:function(){

    },
    oncellclick : function(grid, rowIndex, columnIndex, e) {//#17
        var record = this.store.getAt(rowIndex),
            fieldName = grid.getColumnModel().getDataIndex(columnIndex); // Get field name

        if(record.data['estado_reg']=='activo'){
            this.getBoton('btnInactivo').show();
            this.getBoton('btnActivo').hide();
        }
        else{
            this.getBoton('btnInactivo').hide();
            this.getBoton('btnActivo').show();
        }
    },

    activaInactivarTipo: function (valor) {//#17
        var data = this.getSelectedData();
        console.log('data',data);
        if(valor =='activo'){
            this.getBoton('btnInactivo').show();
            this.getBoton('btnActivo').hide();
        }
        else{
            this.getBoton('btnInactivo').hide();
            this.getBoton('btnActivo').show();
            //this.getBoton('btnAprobado').disable();
        }
	    var me = this;

            Phx.CP.loadingShow();
            Ext.Ajax.request({
                url: '../../sis_soporte/control/Tipo/activarInactivarTipo',
                params: {
                    'id_tipo': data.id_tipo,
                    'estado_reg': valor,
                },
                success: me.successSaveOperacion,
                failure: me.conexionFailure,
                timeout: me.timeout,
                scope: me
            });
           this.reload();
    },
    //
    successSaveOperacion: function () { //#17
        Phx.CP.loadingHide();
        Ext.MessageBox.alert('EXITO!!!', 'Se realizo con exito la operación.');
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
		
		