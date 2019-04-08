<?php
/**
*@package pXP
*@file gen-HelpDesk.php
*@author  (eddy.gutierrez)
*@date 22-02-2019 19:07:11
*@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
HISTORIAL DE MODIFICACIONES:
 #ISSUE                FECHA                AUTOR                DESCRIPCION
 #4 EndeEtr           08/04/2019            EGS                 Se modifico el grid de la vizualizacion para que muestre lo oculto ante como usr reg y fecha reg
 * */

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.HelpDeskBase=Ext.extend(Phx.gridInterfaz,{
	
	constructor:function(config){
		this.maestro=config.maestro;
		var v_prioridad;
    	//llama al constructor de la clase padre
		Phx.vista.HelpDeskBase.superclass.constructor.call(this,config);
		this.init();
		this.load({params:{start:0, limit:this.tam_pag}})
		this.addBotonesGantt();
		this.obtenerVariableGlobal();
        this.addButton('btnChequeoDocumentosWf',
	            {
	                text: 'Archivos del Problema',
	                grupo:[0,1,2,3,4],
	                iconCls: 'bchecklist',
	                disabled: true,
	                handler: this.loadCheckDocumentosWf,
	                tooltip: '<b>Documentos del Problema</b><br/>Permite ver los documentos asociados al NRO de trámite.'
	            });		
	},
	/* //#4
	arrayDefaultColumHidden:['fecha_mod','usr_mod','id_proceso_wf','id_estado_wf','estado_reg','id_usuario_ai','usuario_ai'],
	rowExpander: new Ext.ux.grid.RowExpander({
	            tpl : new Ext.Template(
	                '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Usuario Registro:&nbsp;&nbsp;</b> {usr_reg}</p>',
	                '<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Fecha Registro:&nbsp;&nbsp;</b> {fecha_reg}</p>',           
	                //'<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Usuario Modificación:&nbsp;&nbsp;</b> {usr_mod}</p>',
	                //'<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Fecha Modificación:&nbsp;&nbsp;</b> {fecha_mod}</p>'
	            )
	    }) ,*/

    	loadCheckDocumentosWf:function() {
            var rec=this.sm.getSelected();
            rec.data.nombreVista = this.nombreVista;
            Phx.CP.loadWindows('../../../sis_workflow/vista/documento_wf/DocumentoWf.php',
                    'Documentos del Proceso',
                    {
                        width:'90%',
                        height:500
                    },
                    rec.data,
                    this.idContenedor,
                    'DocumentoWf'
        	)},	
	addBotonesGantt: function() {
	        this.menuAdqGantt = new Ext.Toolbar.SplitButton({
	            id: 'b-diagrama_gantt-' + this.idContenedor,
	            text: 'Gantt',
	            disabled: true,
	            grupo:[0,1,2,3,4],
	            iconCls : 'bgantt',
	            handler:this.diagramGanttDinamico,
	            scope: this,
	            menu:{
		            items: [{
		                id:'b-gantti-' + this.idContenedor,
		                text: 'Gantt Imagen',
		                tooltip: '<b>Muestra un reporte gantt en formato de imagen</b>',
		                handler:this.diagramGantt,
		                scope: this
		            }, {
		                id:'b-ganttd-' + this.idContenedor,
		                text: 'Gantt Dinámico',
		                tooltip: '<b>Muestra el reporte gantt facil de entender</b>',
		                handler:this.diagramGanttDinamico,
		                scope: this
		            }]
	            }
	        });
			this.tbar.add(this.menuAdqGantt);
   		},
	diagramGantt: function (){	
		var data=this.sm.getSelected().data.id_proceso_wf;

		Phx.CP.loadingShow();
		Ext.Ajax.request({
			url:'../../sis_workflow/control/ProcesoWf/diagramaGanttTramite',
			params:{'id_proceso_wf':data},
			success: this.successExport,
			failure: this.conexionFailure,
			timeout: this.timeout,
			scope: this
		});			
	},
	
	diagramGanttDinamico: function (){	
		var data=this.sm.getSelected().data.id_proceso_wf;

		window.open('../../../sis_workflow/reportes/gantt/gantt_dinamico.html?id_proceso_wf='+data)		
	},
		
	Atributos:[
		{
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'id_help_desk'
			},
			type:'Field',
			form:true 
		},
		{
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'id_proceso_wf'
			},
			type:'Field',
			form:true 
		},
		{
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'id_estado_wf'
			},
			type:'Field',
			form:true 
		},
		{
           config: {
                     labelSeparator: '',
                     inputType: 'hidden',
                     name: 'id_usuario'
                    },
                    type: 'Field',
                    form: true
         },
 
		{
			config:{
				name: 'nro_tramite',
				fieldLabel: 'Nro Tramite',
				allowBlank: true,
				anchor: '80%',
				gwidth: 350,
				maxLength:-5,
				renderer:function(value,metaData, record,rowIndex, colIndex, store){
				console.log('record....', value, metaData, record,'-->',rowIndex,'<---',colIndex,'<xxxxx>',store);
				
				 var prioridad = store.data.prioridad; 
				 	 prioridad = prioridad.split(",");  
                        console.log('record',record); 
                 var fecha = '"'+record.data['fecha_reg']+'"';
                     fecha = Ext.util.Format.date(fecha,'d/m/Y');        
                        
                         				
				if(record.json.nombreVista == 'HelpDesk' ){
						return '<tpl for="."><div class="x-combo-list-item"><p><font><b>Nro Tramite: </b>'+record.data['nro_tramite']+'</font></p><p><b>Estado: <font  size=3 ></b> '+record.data['estado'] +'</font></p></div></tpl>';
				}
				if(record.json.nombreVista == 'HelpDeskAsis' ){
						//#4
						if (record.data['desc_prioridad']==prioridad[0]){
						return '<tpl for="."><div class="x-combo-list-item">\
						<p><b>Prioridad: <font  size=3 color="green"> </b> '+record.data['desc_prioridad'] +'</font></p>\
						<p><b>Estado: <font  size=3 > </b> '+record.data['estado'] +'</font>\
						</p><p><font><b>Nro Tramite: </b>'+record.data['nro_tramite']+'</font></p>\
						<p><font><b>Funcionario Solicitante: </b>'+record.data['desc_funcionario']+'</font></p>\
						</p><p><font><b>Usuario Registro: </b>'+record.data['usr_reg']+'</font></p>\
						</p><p><font><b>Fecha Registro: </b>'+fecha+'</font></p>\
						</div></tpl>';
							}
							 if (record.data['desc_prioridad']==prioridad[1]){
						return '<tpl for="."><div class="x-combo-list-item">\
						<p><b>Prioridad: <font  size=3 color="blue"> </b> '+record.data['desc_prioridad'] +'</font></p>\
						<p><b>Estado: <font  size=3 > </b> '+record.data['estado'] +'</font></p>\
						<p><font><b>Nro Tramite: </b>'+record.data['nro_tramite']+'</font></p>\
						<p><font><b>Funcionario Solicitante: </b>'+record.data['desc_funcionario']+'</font></p>\
						</p><p><font><b>Usuario Registro: </b>'+record.data['usr_reg']+'</font></p>\
						</p><p><font><b>Fecha Registro: </b>'+fecha+'</font></p>\
						</div></tpl>';
							}
							 if (record.data['desc_prioridad']==prioridad[2]){
						return '<tpl for="."><div class="x-combo-list-item">\
						<p><b>Prioridad: <font  size=3 color="orange"> </b> '+record.data['desc_prioridad'] +'</font></p>\
						<p><b>Estado: <font  size=3>  </b> '+record.data['estado'] +'</font></p>\
						<p><font><b>Nro Tramite: </b>'+record.data['nro_tramite']+'</font></p>\
						<p><font><b>Funcionario Solicitante: </b>'+record.data['desc_funcionario']+'</font></p>\
						</p><p><font><b>Usuario Registro: </b>'+record.data['usr_reg']+'</font></p>\
						</p><p><font><b>Fecha Registro: </b>'+fecha+'</font></p>\
						</div></tpl>';
							}
							if (record.data['desc_prioridad']==prioridad[3]){
						return '<tpl for="."><div class="x-combo-list-item">\
						<p><b>Prioridad: <font  size=3 color="red"> </b> '+record.data['desc_prioridad'] +'</font></p>\
						<p><b>Estado: <font  size=3 ></b> '+record.data['estado'] +'</font></p>\
						<p><font><b>Nro Tramite: </b>'+record.data['nro_tramite']+'</font></p>\
						<p><font><b>Funcionario Solicitante: </b>'+record.data['desc_funcionario']+'</font></p>\
						</p><p><font><b>Usuario Registro: </b>'+record.data['usr_reg']+'</font></p>\
						</p><p><font><b>Fecha Registro: </b>'+fecha+'</font></p>\
						</div></tpl>';
							}
						if (record.data['desc_prioridad']== '' ){
						return '<tpl for="."><div class="x-combo-list-item">\
						<p><font><b>Nro Tramite: </b>'+record.data['nro_tramite']+'</font></p>\
						<p><b>Estado: <font  size=3 ></b> '+record.data['estado'] +'</font></p>\
						<p><font><b>Funcionario Solicitante: </b>'+record.data['desc_funcionario']+'</font></p>\
						</p><p><font><b>Usuario Registro: </b>'+record.data['usr_reg']+'</font></p>\
						</p><p><font><b>Fecha Registro: </b>'+fecha+'</font></p>\
						</div></tpl>';
						}
				}
				if(record.json.nombreVista == '' ){
						return record.data['nro_tramite'];
				}
				}
			},
				type:'TextField',
				filters:{pfiltro:'help.nro_tramite',type:'string'},
				id_grupo:1,
				grid:true,
				form:false
		},
		 {
			config:{
				name: 'desc_prioridad',
				fieldLabel: 'Prioridad',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:-5,
				renderer : function(value,metaData, record,rowIndex, colIndex, store) {
				   	 var prioridad = store.data.prioridad; 
				 	 prioridad = prioridad.split(",") ; 
                        
                   if (record.data['desc_prioridad']==prioridad[0]){
                   	return String.format('<b><font size=3 style="color:#008000";>{0}</font><b>', record.data['desc_prioridad']);												
					}
					 if (record.data['desc_prioridad']==prioridad[1]){
                   	return String.format('<b><font size=3 style="color:#0000FF";>{0}</font><b>', record.data['desc_prioridad']);												
					}
					 if (record.data['desc_prioridad']==prioridad[2]){
                   	return String.format('<b><font size=3 style="color:#FFA500";>{0}</font><b>', record.data['desc_prioridad']);												
					}
					if (record.data['desc_prioridad']==prioridad[3]){
                   	return String.format('<b><font size=3 style="color:#FF0000";>{0}</font><b>', record.data['desc_prioridad']);												
					}

				}
                
			},
				type:'TextField',
				filters:{pfiltro:'cat.desc_prioridad',type:'string'},
				id_grupo:1,
				grid:true,
				form:false
		},
		{
            config:{
                name:'id_tipo',
                fieldLabel:'Tipo',
                emptyText:'Tipo..',
                typeAhead: true,
                lazyRender:true,
                allowBlank: false,
                mode: 'remote',
                gwidth: 130,
                anchor: '100%',
                store: new Ext.data.JsonStore({
                    url: '../../sis_soporte/control/Tipo/listarTipo',
                    id: 'id_tipo',
                    root: 'datos',
                    sortInfo:{
                        field: 'id_tipo',
                        direction: 'ASC'
                    },
                    totalProperty: 'total',
                    fields: ['id_tipo','codigo','nombre','descripcion'],
                    // turn on remote sorting
                    remoteSort: true,
                    baseParams:{par_filtro:'tipsop.id_tipo#tipsop.codigo#tipsop.nombre',tipo:'si' }
                }),
               // tpl:'<tpl for=".">\
                 //              <div class="x-combo-list-item"><p><b>ID Comprobante:</b>{nombre},<b>Nro Tramite: </b>{nro_tramite}</p>\
				//</tpl>',
                               
                valueField: 'id_tipo',
                displayField: 'nombre',
                gdisplayField: 'nombre',
                hiddenName: 'id_tipo',
                forceSelection: true,
                typeAhead: false,
                triggerAction: 'all',
                lazyRender: true,
                mode:'remote',
                pageSize: 10,
                queryDelay: 1000,
                resizable: true,
                minChars:1,
                renderer : function(value, p, record) {
                    return String.format('{0}', record.data['nombre_tipo']);
                }
            },
            type:'ComboBox',
            id_grupo:1,
            filters:{pfiltro:'tip.nombre',type:'string'},
            grid:true,
            form:true,
            bottom_filter:true
        },
		{
			config:{
				name: 'nombre_subtipo',
				fieldLabel: 'Subtipo',
				allowBlank: true,
				anchor: '80%',
				gwidth: 150,
				maxLength:-5
			},
				type:'TextField',
				filters:{pfiltro:'subti.nombre_subtipo',type:'string'},
				id_grupo:1,
				grid:true,
				form:false
		},	
		{
	   			config:{
	       		    name:'id_funcionario',
	       		     hiddenName: 'id_funcionario',
	   				origen:'FUNCIONARIOCAR',
	   				fieldLabel:'Funcionario Solicitante',
	   				allowBlank:true,
	                gwidth:200,
	   				valueField: 'id_funcionario',
	   			    gdisplayField: 'desc_funcionario',
	   			    baseParams: {par_filtro: 'id_funcionario#desc_funcionario1'},
	      			renderer:function(value, p, record){return String.format('{0}', record.data['desc_funcionario']);}
	       	     },
	   			type:'ComboRec',//ComboRec
	   			id_grupo:0,
	   			filters:{pfiltro:'fun.desc_funcionario1',type:'string'},
	   			bottom_filter:false,
	   		    grid:true,
	   			form:true
		 },

		{
			config:{
				name: 'fecha',
				fieldLabel: 'fecha',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
							format: 'd/m/Y', 
							renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
			},
				type:'DateField',
				filters:{pfiltro:'help.fecha',type:'date'},
				id_grupo:1,
				grid:true,
				form:true
		},
		{
			config:{
				name: 'descripcion',
				fieldLabel: 'Descripcion',
				allowBlank: false,
				anchor: '80%',
				gwidth: 300,
				maxLength:1000,
				renderer: function(value, metaData, record, rowIndex, colIndex, store) {
                          metaData.css = 'multilineColumn'; 
                          return String.format('<div class="gridmultiline">{0}</div>', value);//#4
                     }
				
			},
				type:'TextArea',
				filters:{pfiltro:'help.descripcion',type:'string'},
				id_grupo:1,
				grid:true,
				form:true
		},

		{
			config:{
				name: 'estado',
				fieldLabel: 'estado',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:-5
			},
				type:'TextField',
				filters:{pfiltro:'help.nro_tramite',type:'string'},
				id_grupo:1,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'desc_funcionario_asignado',
				fieldLabel: 'Funcionario Asignado',
				allowBlank: true,
				anchor: '80%',
				gwidth: 200,
				maxLength:-5
			},
				type:'TextField',
				id_grupo:1,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'obs',
				fieldLabel: 'Observaciones',
				allowBlank: true,
				anchor: '80%',
				gwidth: 250,
				maxLength:-5
			},
				type:'TextField',
				filters:{pfiltro:'ew.obs',type:'string'},
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
				maxLength:10
			},
				type:'TextField',
				filters:{pfiltro:'help.estado_reg',type:'string'},
				id_grupo:1,
				grid:true,
				form:false
		},

		{
			config:{
				name: 'id_usuario_ai',
				fieldLabel: '',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:4
			},
				type:'Field',
				filters:{pfiltro:'help.id_usuario_ai',type:'numeric'},
				id_grupo:1,
				grid:false,
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
				filters:{pfiltro:'help.usuario_ai',type:'string'},
				id_grupo:1,
				grid:true,
				form:false
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
				filters:{pfiltro:'help.fecha_reg',type:'date'},
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
				filters:{pfiltro:'help.fecha_mod',type:'date'},
				id_grupo:1,
				grid:true,
				form:false
		}
	],
	tam_pag:50,	
	title:'Help Desk',
	ActSave:'../../sis_soporte/control/HelpDesk/insertarHelpDesk',
	ActDel:'../../sis_soporte/control/HelpDesk/eliminarHelpDesk',
	ActList:'../../sis_soporte/control/HelpDesk/listarHelpDesk',
	id_store:'id_help_desk',
	fields: [
		{name:'id_help_desk', type: 'numeric'},
		{name:'id_funcionario', type: 'numeric'},
		{name:'id_proceso_wf', type: 'numeric'},
		{name:'id_estado_wf', type: 'numeric'},
		{name:'fecha', type: 'date',dateFormat:'Y-m-d'},
		{name:'estado_reg', type: 'string'},
		{name:'nro_tramite', type: 'string'},
		{name:'id_usuario_ai', type: 'numeric'},
		{name:'usuario_ai', type: 'string'},
		{name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'id_usuario_reg', type: 'numeric'},
		{name:'id_usuario_mod', type: 'numeric'},
		{name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'usr_reg', type: 'string'},
		{name:'usr_mod', type: 'string'},
		{name:'estado', type: 'string'},
		{name:'desc_funcionario', type: 'string'},
		{name:'etapa', type: 'string'},
		{name:'obs', type: 'string'},
		{name:'descripcion', type: 'string'},
		{name:'nombre_tipo', type: 'string'},
		{name:'desc_funcionario_asignado', type: 'string'},
		{name:'desc_prioridad', type: 'string'},
		{name:'id_tipo', type: 'numeric'},
		{name:'nombre_subtipo', type: 'string'},
		{name:'id_tipo_sub', type: 'numeric'},
		{name:'prioridad', type: 'string'},

	],
	sortInfo:{
		field: 'id_help_desk',
		direction: 'ASC'
	},
	bdel:true,
	bsave:true,
    sigEstado:function(){                   
      var data = this.getSelectedData();
      this.objWizard = Phx.CP.loadWindows('../../../sis_workflow/vista/estado_wf/FormEstadoWf.php',
                                'Estado de Wf',
                                {
                                    modal:true,
                                    width:700,
                                    height:450
                                }, {data:{
                                	   id_help_desk:data.id_help_desk,
                                       id_estado_wf:data.id_estado_wf,
                                       id_proceso_wf:data.id_proceso_wf,
                                    	
                                    }}, this.idContenedor,'FormEstadoWf',
                                {
                                    config:[{
                                              event:'beforesave',
                                              delegate: this.onSaveWizard,
                                              
                                            }],
                                    
                                    scope:this
                                 });        
               
     },
      onSaveWizard:function(wizard,resp){

        Ext.Ajax.request({
            url:'../../sis_soporte/control/HelpDesk/siguienteEstado',
            params:{
                id_help_desk:      wizard.data.id_help_desk,
                id_proceso_wf_act:  resp.id_proceso_wf_act,
                id_estado_wf_act:   resp.id_estado_wf_act,
                id_tipo_estado:     resp.id_tipo_estado,
                id_funcionario_wf:  resp.id_funcionario_wf,
                id_depto_wf:        resp.id_depto_wf,
                obs:                resp.obs,
                json_procesos:      Ext.util.JSON.encode(resp.procesos)
                },
            success:this.successWizard,
            failure: this.conexionFailure,
            argument:{wizard:wizard},
            timeout:this.timeout,
            scope:this
        });
         
    },
       successWizard:function(resp){
        Phx.CP.loadingHide();
        resp.argument.wizard.panel.destroy()
        this.reload();
    },
    antEstado: function(res){
		var data = this.getSelectedData();
		Phx.CP.loadingHide();
		Phx.CP.loadWindows('../../../sis_workflow/vista/estado_wf/AntFormEstadoWf.php',
			'Estado de Wf',
			{   modal: true,
			    width: 450,
			    height: 250
			}, 
			{    data: data, 
				 estado_destino: res.argument.estado
			}, 
			this.idContenedor,'AntFormEstadoWf',
			{
			    config:[{
			              event:'beforesave',
			              delegate: this.onAntEstado,
			            }],
			   scope:this
			});
		
	}, 
	onAntEstado: function(wizard,resp){
		console.log('resp',wizard.data.id_help_desk);
        Phx.CP.loadingShow();
        var operacion = 'cambiar';

        Ext.Ajax.request({
                url:'../../sis_soporte/control/HelpDesk/anteriorEstado',
            params:{
            	id_help_desk: wizard.data.id_help_desk,
                id_proceso_wf: resp.id_proceso_wf,
                id_estado_wf:  resp.id_estado_wf,  
                obs: resp.obs,
                operacion: operacion
             },
            argument:{wizard:wizard},  
            success: this.successAntEstado,
            failure: this.conexionFailure,
            timeout: this.timeout,
            scope: this
        });
    }, 
    
       successAntEstado:function(resp){
        Phx.CP.loadingHide();
        resp.argument.wizard.panel.destroy()
        this.reload();
 
    }, 
          obtenerVariableGlobal: function(config){
            Phx.CP.loadingShow();
            Ext.Ajax.request({
                url:'../../sis_seguridad/control/Subsistema/obtenerVariableGlobal',
                params:{
                    codigo: 'sopte_prioridad'
                },
                success: function(resp){
                     Phx.CP.loadingHide();
                     var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
                    if (reg.ROOT.error) {
                        Ext.Msg.alert('Error','Error a recuperar la variable global')
                    } else {
                        this.v_prioridad = reg.ROOT.datos.valor;
                        ///añado la variable global al data del store cuidado con sobreescribir
                        this.store.data.prioridad = this.v_prioridad;

                   }
                },
                failure: this.conexionFailure,
                timeout: this.timeout,
                scope:this
            });
 
        }, 
	
	
	}
)
</script>
		
		