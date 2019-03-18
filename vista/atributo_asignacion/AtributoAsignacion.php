<?php
/**
*@package pXP
*@file gen-HelpDesk.php
*@author  (eddy.gutierrez)
*@date 22-02-2019 19:07:11
*@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
	ISUE				FECHA				AUTHOR					DESCRIPCION	
 	#8					18/03/2019			EGS						Se agrega el poder de editar el tipo y recarga los subtipos correspondientes		
*/

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.AtributoAsignacion=Ext.extend(Phx.frmInterfaz,{

	constructor:function(config){
		this.maestro=config;
		console.log('maestro',this.maestro);
    	//llama al constructor de la clase padre
		Phx.vista.AtributoAsignacion.superclass.constructor.call(this,config);
		this.init();
		this.inciarEventos();
	},
	inciarEventos: function(){
		console.log('id_help_desk',this.maestro);
		this.Cmp.id_help_desk.setValue(this.maestro.id_help_desk);	
		
		///#8	
		this.Cmp.id_sub_tipo.store.baseParams.id_tipo_fk = this.maestro.id_tipo;

		
		this.Cmp.id_tipo.store.baseParams.query = this.maestro.id_tipo;
							    this.Cmp.id_tipo.store.load({params:{start:0,limit:this.tam_pag}, 
					               callback : function (r) {                        
					                    if (r.length > 0 ) {                        
					                    	
					                       this.Cmp.id_tipo.setValue(r[0].data.id_tipo);
					                    }     
					                                    
					                }, scope : this
					            });
					            
		 
	  this.Cmp.id_tipo.on('select',function(combo,record,index){
	  		 console.log('record',record.data.id_tipo);
			 this.Cmp.id_sub_tipo.store.baseParams.id_tipo_fk= record.data.id_tipo;
							    this.Cmp.id_sub_tipo.store.load({params:{start:0,limit:this.tam_pag}, 
					               callback : function (r) {
					               		console.log('r',r);                        
					                    if (r.length > 0 ) {                        
					                    	
					                       this.Cmp.id_sub_tipo.setValue(r[0].data.id_tipo);
					                    }     
					                                    
					                }, scope : this
					            });		            
		 },this);	
		
		
		if (this.maestro.desc_prioridad != '') {
		
		this.Cmp.prioridad.store.baseParams.query = this.maestro.prioridad;
							    this.Cmp.prioridad.store.load({params:{start:0,limit:this.tam_pag}, 
					               callback : function (r) {                        
					                    if (r.length > 0 ) {                        
					                    	
					                       this.Cmp.prioridad.setValue(r[0].data.codigo);
					                    }     
					                                    
					                }, scope : this
					            });
		 
		 this.Cmp.id_sub_tipo.store.baseParams.query = this.maestro.id_tipo_sub;
							    this.Cmp.id_sub_tipo.store.load({params:{start:0,limit:this.tam_pag}, 
					               callback : function (r) {                        
					                    if (r.length > 0 ) {                        
					                    	
					                       this.Cmp.id_sub_tipo.setValue(r[0].data.id_tipo);
					                    }     
					                                    
					                }, scope : this
					            });
	
		};///#8
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
			config: {
				name: 'prioridad',
				fieldLabel: 'Prioridad',
				anchor: '100%',
				tinit: false,
				allowBlank: false,
				origen: 'CATALOGO',
				gdisplayField: 'prioridad',
				hiddenName: 'tipo',
				gwidth: 150,
				baseParams:{
					cod_subsistema:'SOPTE',
					catalogo_tipo:'tprioridad_help_desk',
					par_filtro:'cat.codigo'
				},
				valueField: 'codigo',
				hidden: false,
			},
			type: 'ComboRec',
			id_grupo: 0,
			grid: true,
			form: true
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
                gwidth: 180,
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
                name:'id_sub_tipo',
                fieldLabel:'Sub Tipo',
                emptyText:'Sub Tipo..',
                typeAhead: true,
                lazyRender:true,
                allowBlank: false,
                mode: 'remote',
                gwidth: 180,
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
                    fields: ['id_tipo','codigo','nombre','descripcion','id_tipo_fk'],
                    // turn on remote sorting
                    remoteSort: true,
                    baseParams:{par_filtro:'tipsop.id_tipo#tipsop.codigo#tipsop.nombre#tipsop.id_tipo_fk',tipo:'no' }
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

	],
	tam_pag:50,	
	title:'Help Desk',
	ActSave:'../../sis_soporte/control/HelpDesk/insertarAtributoAsignacion',
	ActList:'../../sis_soporte/control/HelpDesk/listarAtributoAsignacion',
	id_store:'id_help_desk',
	fields: [
		{name:'id_help_desk', type: 'numeric'},
		{name:'prioridad', type: 'string'},
		{name:'id_tipo', type: 'numeric'},
	   {name:'id_sub_tipo', type: 'numeric'},//#8	
	   	{name:'id_tipo_fk', type: 'numeric'},//#8	


	],
	sortInfo:{
		field: 'id_help_desk',
		direction: 'ASC'
	},
	bsave:true,
	successSave:function(resp)
        {
            Phx.CP.loadingHide();
            Phx.CP.getPagina(this.idContenedorPadre).reload();
            this.panel.close();
       },
	}
)
</script>
		
		