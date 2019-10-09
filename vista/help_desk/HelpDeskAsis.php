<?php
/**
*@package pXP
*@file gen-HelpDesk.php
*@author  (eddy.gutierrez)
*@date 22-02-2019 19:07:11
*@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
 *  #ISSUE                FECHA                AUTOR                DESCRIPCION
 #4 EndeEtr           08/04/2019            EGS                 Se modifico las ventanas atab a solo una ventana 
 #9 EndeEtr			  07/06/2019			EGS					Se agrego que el boton de tipo y prioridades sea visible en el estado de asignacion
 #11 EndeEtr		  08/07/2019			EGS					Se agregan la obs del wf
 #12 EndeEtr            09/10/2019          EGS                 e arrregla bug para paginacion
 *  * */

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.HelpDeskAsis = {
	
	require:'../../../sis_soporte/vista/help_desk/HelpDeskBase.php',
	requireclase:'Phx.vista.HelpDeskBase',
	title:'HelpDeskAsis',
	nombreVista: 'HelpDeskAsis',

	constructor:function(config){
		this.maestro=config.maestro;
		this.Atributos[this.getIndAtributo('id_funcionario')].grid=false;
    	//llama al constructor de la clase padre
		Phx.vista.HelpDeskAsis.superclass.constructor.call(this,config);
		this.init();
		this.iniciarEventos();
		this.finCons = true;
        this.store.baseParams={estado:'pendiente',nombreVista: this.nombreVista};//#12
		this.load({params:{start:0, limit:this.tam_pag }});
		
		this.addButton('ant_estado',
						{argument: {estado: 'anterior'},
						text:'Anterior',
		                grupo:[0,1,2],
						iconCls: 'batras',
						disabled:true,
						handler:this.antEstado,
						tooltip: '<b>Pasar al Anterior Estado</b>'
						});
		this.addButton('sig_estado',
						{ text:'Siguiente',
						grupo:[0,1,2], 
						iconCls: 'badelante', 
						disabled: true, 
						handler: this.sigEstado, 
						tooltip: '<b>Pasar al Siguiente Estado</b>'
						});
		
		this.addButton('btnatrasignacion',
	            {
	                text: 'Subtipo/Priorida',
	                grupo:[0,1],//#9 
	                iconCls: 'bchecklist',
	                disabled: true,
	                handler: this.atributoAsignacion,
	                tooltip: '<b>Elige subtipo y Priaridad al Asignar</b>'
	            });	
		
	},
	 atributoAsignacion:function() {
            var rec=this.sm.getSelected();
            Phx.CP.loadWindows('../../../sis_soporte/vista/atributo_asignacion/AtributoAsignacion.php',
                    'Prioridad/Subtipo',
                    {
                        width:'50%',
                        height:400
                    },
                    rec.data,
                    this.idContenedor,
                    'AtributoAsignacion'
        	)},	
	
	bnewGroups: [],    
    beditGroups: [],
    bdelGroups:  [],
    bsaveGroups: [],
    bactGroups:  [0,1,2,3,4],
    btestGroups: [0],
    bexcelGroups: [0,1,2,3,4],
    /*//#4
    gruposBarraTareas:[	{name:'pendiente',title:'<H1 align="center"><i class="fa fa-eye"></i> Pendiente</h1>',grupo:0,height:0},
    					{name:'asignado',title:'<H1 align="center"><i class="fa fa-eye"></i> Asignado</h1>',grupo:1,height:0},
				    	{name:'proceso',title:'<H1 align="center"><i class="fa fa-eye"></i> Proceso</h1>',grupo:2,height:0},
                       	{name:'resuelto',title:'<H1 align="center"><i class="fa fa-eye"></i> Resuelto</h1>',grupo:3,height:0},
                       	{name:'finalizado',title:'<H1 align="center"><i class="fa fa-eye"></i>Finali./Rech.</h1>',grupo:4,height:0}
                       
                       ],*/
                      
    gruposBarraTareas:[	{name:'pendiente',title:'<H1 align="center"><i class="fa fa-eye"></i> Pendiente</h1>',grupo:0,height:0},
    					{name:'asignado',title:'<H1 align="center"><i class="fa fa-eye"></i> Asig/Proc</h1>',grupo:1,height:0},//#4
                       	{name:'resuelto',title:'<H1 align="center"><i class="fa fa-eye"></i> Resuel/Recha</h1>',grupo:3,height:0}],
                       	                  
    actualizarSegunTab: function(name, indice){
        if(this.finCons) {        	 
             //this.store.baseParams.nombre_estado= name; 
             this.store.baseParams.estado= name;
             this.store.baseParams.nombreVista = this.nombreVista ;                           
             this.load({params:{start:0, limit:this.tam_pag}});
        }
    },	
	
	iniciarEventos: function() {		
	
	},
	onButtonNew: function() {
             Phx.vista.HelpDeskAsis.superclass.onButtonNew.call(this);
             this.Cmp.fecha.setValue(new Date());
             this.Cmp.fecha.disable();
                   
       
    },
    preparaMenu:function(n){
      var data = this.getSelectedData();
      var tb =this.tbar;
        Phx.vista.HelpDeskAsis.superclass.preparaMenu.call(this,n);
        this.getBoton('diagrama_gantt').enable();
		this.getBoton('btnChequeoDocumentosWf').enable();
        this.getBoton('btnatrasignacion').enable();
        this.getBoton('btnObs').enable();//#11

         if (data.estado == 'resuelto') {//#4
         	this.getBoton('sig_estado').disable();
         } else{
         	this.getBoton('sig_estado').enable();
         	
         };
		 	this.getBoton('ant_estado').enable();	
 	
         return tb 
     }, 
     liberaMenu:function(){
        var tb = Phx.vista.HelpDeskAsis.superclass.liberaMenu.call(this);
        if(tb){
			this.getBoton('btnChequeoDocumentosWf').disable();           
            this.getBoton('diagrama_gantt').disable();
            this.getBoton('btnatrasignacion').disable();
        	this.getBoton('ant_estado').disable();
    		this.getBoton('sig_estado').disable(); 
            this.getBoton('btnObs').disable();//#11
 
             
        }
       return tb
    },
	
}
</script>
		
		