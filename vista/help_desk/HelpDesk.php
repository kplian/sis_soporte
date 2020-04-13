<?php
/**
*@package pXP
*@file gen-HelpDesk.php
*@author  (eddy.gutierrez)
*@date 22-02-2019 19:07:11
*@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
 HISTORIAL DE MODIFICACIONES:
 #ISSUE                FECHA                AUTOR                DESCRIPCION
 #4 EndeEtr           08/04/2019            EGS                 Se modifico las ventanas atab a solo una ventana   
 #6 EndeEtr           18/04/2019            EGS                 Funcionarios solo vigentes
 #11 EndeEtr		  08/07/2019			EGS					Se agregan la obs del wf
 #12 EndeEtr            09/10/2019          EGS                 e arrregla bug para paginacion
 #15 EndeEtr          16/12/2019            EGS                 recarga de numero referencial automatico del funcionario
 #15 EndeEtr          17/12/2019            EGS                 Arreglo de bugs y mejora en la recarga automatica de Funcionario
*/

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.HelpDesk = {
	
	require:'../../../sis_soporte/vista/help_desk/HelpDeskBase.php',
	requireclase:'Phx.vista.HelpDeskBase',
	title:'HelpDesk',
	nombreVista: 'HelpDesk',

	constructor:function(config){
		this.maestro=config.maestro;
		//ejecutar antes del constructor oculta campo en la vista
		this.Atributos[this.getIndAtributo('desc_prioridad')].grid=false;
		this.Atributos[this.getIndAtributo('nombre_subtipo')].grid=false;
    	//llama al constructor de la clase padre
		Phx.vista.HelpDesk.superclass.constructor.call(this,config);
		this.init();
		this.iniciarEventos();
		this.finCons = true;
		
		this.store.baseParams={id_usuario:Phx.CP.config_ini.id_usuario,nombreVista: this.nombreVista};//#12
		console.log('id_funcionario',Phx.CP.config_ini.id_funcionario) ;             
		//this.load({params:{start:0, limit:this.tam_pag ,estado:'borrador',nombreVista: this.nombreVista}});//#4
		this.load({params:{start:0, limit:this.tam_pag }});

		
		this.addButton('ant_estado',
						{argument: {estado: 'anterior'},
						text:'Anterior',
		                grupo:[0,2],
						iconCls: 'batras',
						disabled:true,
						handler:this.antEstado,
						tooltip: '<b>Pasar al Anterior Estado</b>'
						});
		this.addButton('sig_estado',
						{ text:'Siguiente',
						grupo:[0,2], 
						iconCls: 'badelante', 
						disabled: true, 
						handler: this.sigEstado, 
						tooltip: '<b>Pasar al Siguiente Estado</b>'
						});
        this.addButton('importar_correos',
            {
                text: 'Importar Correos',
                grupo: [0, 2],
                iconCls: 'bdocuments',
                disabled: false,
                handler: this.importar,
                tooltip: '<b>Importar correos</b>'
            });
		
		
	},
	bnewGroups: [0],    
    beditGroups: [0],
    bdelGroups:  [0],
    bactGroups:  [0,1,2,3],
    btestGroups: [0],
    bexcelGroups: [0,1,2,3],
    /*//#4
    gruposBarraTareas:[	{name:'borrador',title:'<H1 align="center"><i class="fa fa-eye"></i> Solicitud</h1>',grupo:0,height:0},
					   	{name:'asignado',title:'<H1 align="center"><i class="fa fa-eye"></i> Asignacion</h1>',grupo:1,height:0},
						{name:'resuelto',title:'<H1 align="center"><i class="fa fa-eye"></i> Resuelto</h1>',grupo:2,height:0},					  
                       	{name:'finalizado',title:'<H1 align="center"><i class="fa fa-eye"></i> Finali./Rech.</h1>',grupo:3,height:0}
                       
                       ],
    actualizarSegunTab: function(name, indice){
        if(this.finCons) {        	 
             //this.store.baseParams.nombre_estado= name; 
             this.store.baseParams.estado = name;
             this.store.baseParams.nombreVista = this.nombreVista ;
             this.load({params:{start:0, limit:this.tam_pag}});
        }
    },	
	*/
	iniciarEventos: function() {		


	},
	onButtonNew: function() {
             Phx.vista.HelpDesk.superclass.onButtonNew.call(this);
             this.Cmp.fecha.setValue(new Date());
             this.Cmp.fecha.disable();
             this.Cmp.id_funcionario.store.baseParams.fecha = this.Cmp.fecha.getValue().dateFormat(this.Cmp.fecha.format);// #6
    		 this.Cmp.id_funcionario.store.baseParams.query = Phx.CP.config_ini.id_funcionario;
							    this.Cmp.id_funcionario.store.load({params:{start:0,limit:this.tam_pag}, 
					               callback : function (r) {                        
					                    if (r.length > 0 ) {                        
					                    	
					                       this.Cmp.id_funcionario.setValue(r[0].data.id_funcionario);
					                       this.obtenerNumeroReferencial(r[0].data.id_funcionario); //#15
					                    }     
					                                    
					                }, scope : this
					            });
             this.Cmp.id_funcionario.enable();//#15

             this.Cmp.id_funcionario.on('select',function(combo,record,index){//#15

                  this.obtenerNumeroReferencial( record.data.id_funcionario);

             },this)

    },
   	onButtonEdit: function() {
   			 var data = this.getSelectedData();
             Phx.vista.HelpDesk.superclass.onButtonEdit.call(this);

    		 this.Cmp.id_tipo.store.baseParams.query = data.id_tipo;
							    this.Cmp.id_tipo.store.load({params:{start:0,limit:this.tam_pag}, 
					               callback : function (r) {                        
					                    if (r.length > 0 ) {                        
					                    	
					                       this.Cmp.id_tipo.setValue(r[0].data.id_tipo);
					                    }     
					                                    
					                }, scope : this
					            });                  
       		  this.Cmp.id_funcionario.disable();

    },
    preparaMenu:function(n){
      var data = this.getSelectedData();
      var tb =this.tbar;
        Phx.vista.HelpDesk.superclass.preparaMenu.call(this,n);
        this.getBoton('diagrama_gantt').enable();
		this.getBoton('btnChequeoDocumentosWf').enable();
        this.getBoton('btnObs').enable();//#11

         if (data.estado == 'borrador') {
         	this.getBoton('ant_estado').disable();
    		this.getBoton('sig_estado').enable();	

         }else{
         	this.getBoton('sig_estado').disable();	
         }; 
         if(data.estado == 'resuelto'){ //#4
         	this.getBoton('ant_estado').enable();
    		this.getBoton('sig_estado').disable();		
         };
         

       	
         return tb 
     }, 
     liberaMenu:function(){
        var tb = Phx.vista.HelpDesk.superclass.liberaMenu.call(this);
        if(tb){
			this.getBoton('btnChequeoDocumentosWf').disable();          
            this.getBoton('diagrama_gantt').disable();
        	this.getBoton('ant_estado').disable();
    		this.getBoton('sig_estado').disable();
            this.getBoton('btnObs').disable();//#11
     
        }
       return tb
    },
    obtenerNumeroReferencial: function (id_funcionario) {
            Phx.CP.loadingShow();
            Ext.Ajax.request({
                url:'../../sis_soporte/control/HelpDesk/obtenerNumeroReferencial',
                params:{
                    id_funcionario: id_funcionario,
                },
                success: function(resp){
                    Phx.CP.loadingHide();
                    var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
                    if(reg.datos.length != 0 ){
                        var numero_ref = reg.datos[0]['numero_ref'];
                        this.Cmp.numero_ref.setValue(numero_ref);
                    }
                    else {
                        this.Cmp.numero_ref.reset();
                    }
                },
                failure: this.conexionFailure,
                timeout: this.timeout,
                scope:this
            });
    },
    importar: function () {
        var self = this;
        Phx.CP.loadingShow();
        Ext.Ajax.request({
            url: '../../sis_soporte/control/HelpDesk/importar_correos',
            params: {fecha: ''},
            success: function (resp) {
                Phx.CP.loadingHide();
                self.reload();
            },
            failure: this.conexionFailure,
            timeout: this.timeout,
            scope: this
        });
    }
	
}
</script>
		
		