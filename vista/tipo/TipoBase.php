<?php
/**
*@package pXP
*@file TipoBase.php
*@author  (eddy.gutierrez)
*@date 28-02-2019 16:38:04
*@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
 ISSUE       FECHA       AUTHOR          DESCRIPCION
 #17         15/01/2020  EGS             se agrega colores a los campos para diferenciar entre activo e inactivo
*/

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.TipoBase=Ext.extend(Phx.gridInterfaz,{

	constructor:function(config){
		this.maestro=config.maestro;
    	//llama al constructor de la clase padre
		Phx.vista.TipoBase.superclass.constructor.call(this,config);
		this.init();
        this.grid.addListener('cellclick', this.oncellclick,this);//#17

        //this.load({params:{start:0, limit:this.tam_pag}})
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
                this.activaInactivarTipo('inactivo')
            },
            tooltip: '<b>Inactivar</b>'
        });
        this.getBoton('btnActivo').hide();//#17
        this.getBoton('btnInactivo').hide();//#17

	},
			
	Atributos:[
		{
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'id_tipo'
			},
			type:'Field',
			form:true 
		},
		{
			config:{
				name: 'codigo',
				fieldLabel: 'codigo',
				allowBlank: false,
				anchor: '80%',
				gwidth: 100,
				maxLength:200
			},
				type:'TextField',
				filters:{pfiltro:'tipsop.codigo',type:'string'},
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'nombre',
				fieldLabel: 'nombre',
				allowBlank: false,
				anchor: '80%',
				gwidth: 100,
				maxLength:200,
                renderer : function(value,metaData, record,rowIndex, colIndex, store) {//#17
                   if (record.data['estado_reg'] == 'activo') {
                        return String.format('<b><font size=3 style="color:#008000";>{0}</font><b>', record.data['nombre']);
                    } else {
                        return String.format('<b><font size=3 style="color:#FF0000";>{0}</font><b>', record.data['nombre']);
                    }
                }
			},
				type:'TextField',
				filters:{pfiltro:'tipsop.codigo',type:'string'},
				id_grupo:1,
				grid:true,
				form:true
		},

			{
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'id_tipo_fk'
			},
			type:'Field',
			form:true 
		},
		{
			config:{
				name: 'descripcion',
				fieldLabel: 'descripcion',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:500
			},
				type:'TextField',
				filters:{pfiltro:'tipsop.descripcion',type:'string'},
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'fecha_reg',
				fieldLabel: 'Fecha creación',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
							format: 'd/m/Y', 
							renderer:function (value,p,record){return value?value.dateFormat('d/m/Y H:i:s'):''}
			},
				type:'DateField',
				filters:{pfiltro:'tipsop.fecha_reg',type:'date'},
				id_grupo:1,
				grid:true,
				form:false
		},		
		{
			config:{
				name: 'estado_reg',
				fieldLabel: 'Estado Reg.',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:10,
                renderer : function(value,metaData, record,rowIndex, colIndex, store) {//#17
                    if (record.data['estado_reg'] == 'activo') {
                        return String.format('<b><font size=3 style="color:#008000";>{0}</font><b>', record.data['estado_reg']);
                    } else {
                        return String.format('<b><font size=3 style="color:#FF0000";>{0}</font><b>', record.data['estado_reg']);
                    }
                }
			},
				type:'TextField',
				filters:{pfiltro:'tipsop.estado_reg',type:'string'},
				id_grupo:1,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'usuario_ai',
				fieldLabel: 'Funcionaro AI',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:300
			},
				type:'TextField',
				filters:{pfiltro:'tipsop.usuario_ai',type:'string'},
				id_grupo:1,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'usr_reg',
				fieldLabel: 'Creado por',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:4
			},
				type:'Field',
				filters:{pfiltro:'usu1.cuenta',type:'string'},
				id_grupo:1,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'id_usuario_ai',
				fieldLabel: 'Creado por',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:4
			},
				type:'Field',
				filters:{pfiltro:'tipsop.id_usuario_ai',type:'numeric'},
				id_grupo:1,
				grid:false,
				form:false
		},
		{
			config:{
				name: 'fecha_mod',
				fieldLabel: 'Fecha Modif.',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
							format: 'd/m/Y', 
							renderer:function (value,p,record){return value?value.dateFormat('d/m/Y H:i:s'):''}
			},
				type:'DateField',
				filters:{pfiltro:'tipsop.fecha_mod',type:'date'},
				id_grupo:1,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'usr_mod',
				fieldLabel: 'Modificado por',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:4
			},
				type:'Field',
				filters:{pfiltro:'usu2.cuenta',type:'string'},
				id_grupo:1,
				grid:true,
				form:false
		}
	],
	tam_pag:50,	
	title:'Tipo Soporte',
	ActSave:'../../sis_soporte/control/Tipo/insertarTipo',
	ActDel:'../../sis_soporte/control/Tipo/eliminarTipo',
	ActList:'../../sis_soporte/control/Tipo/listarTipo',
	id_store:'id_tipo',
	fields: [
		{name:'id_tipo', type: 'numeric'},
		{name:'codigo', type: 'string'},
		{name:'estado_reg', type: 'string'},
		{name:'id_tipo_fk', type: 'numeric'},
		{name:'descripcion', type: 'string'},
		{name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'usuario_ai', type: 'string'},
		{name:'id_usuario_reg', type: 'numeric'},
		{name:'id_usuario_ai', type: 'numeric'},
		{name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'id_usuario_mod', type: 'numeric'},
		{name:'usr_reg', type: 'string'},
		{name:'usr_mod', type: 'string'},
		{name:'nombre', type: 'string'},
		
	],
	sortInfo:{
		field: 'id_tipo',
		direction: 'ASC'
	},
	bdel:true,
	bsave:true,
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
        Phx.CP.getPagina(this.idContenedorPadre).reload();
        Ext.MessageBox.alert('EXITO!!!', 'Se realizo con exito la operación.');
    },
    successSave: function () {//#17
        Phx.CP.loadingHide();
        Phx.CP.getPagina(this.idContenedorPadre).reload();
        this.window.hide();
    },
	}
)
</script>
		
		