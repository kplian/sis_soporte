<?php
/**
*@package pXP
*@file gen-ACTHelpDesk.php
*@author  (eddy.gutierrez)
*@date 22-02-2019 19:07:11
*@description Clase que recibe los parametros enviados por la vista para mandar a la capa de Modelo
 HISTORIAL DE MODIFICACIONES:
 #ISSUE                FECHA                AUTOR                DESCRIPCION
 #4 EndeEtr           08/04/2019            EGS                 Se modifico los filtros para cada una de las ventas de help desk y asis    
 #5 EndeEtr           09/04/2019            EGS                 Ordenacion DESC  
 #15 EndeEtr          16/12/2019            EGS                 recarga de numero referencial automatico del funcionario
 */

class ACTHelpDesk extends ACTbase{    
			
	function listarHelpDesk(){
		$this->objParam->defecto('ordenacion','id_help_desk');
		$this->objParam->addParametro('id_funcionario_usu',$_SESSION["ss_id_funcionario"]); 

		$this->objParam->defecto('dir_ordenacion','DESC');//ordenacion toma de la vista
		if ($this->objParam->getParametro('estado') != '') {
			
			if ($this->objParam->getParametro('estado') == 'resuelto') {//#4
				$this->objParam->addFiltro("help.estado in (''resuelto'',''rechazado'')");	//#4				
			}
			else if ($this->objParam->getParametro('estado') == 'asignado' && $this->objParam->getParametro('nombreVista') == 'HelpDeskAsis') {
				$this->objParam->addFiltro("help.estado in (''asignado'',''proceso'')");	//#4				
			} else {
				$this->objParam->addFiltro("help.estado = ''" . $this->objParam->getParametro('estado') . "''");		
			}
			
		}
		
		if($this->objParam->getParametro('tipoReporte')=='excel_grid' || $this->objParam->getParametro('tipoReporte')=='pdf_grid'){
			$this->objReporte = new Reporte($this->objParam,$this);
			$this->res = $this->objReporte->generarReporteListado('MODHelpDesk','listarHelpDesk');
		} else{
			$this->objFunc=$this->create('MODHelpDesk');
			
			$this->res=$this->objFunc->listarHelpDesk($this->objParam);
		}
		//añadimos el nombre de la vista a los datos de retorno
		for ($i=0; $i < count($this->res->datos); $i++) { 
            $arrayTotal = array('nombreVista' => $this->objParam->getParametro('nombreVista'));
            $this->res->datos[$i] = array_merge($this->res->datos[$i],$arrayTotal);            
        }
		
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
				
	function insertarHelpDesk(){
		$this->objFunc=$this->create('MODHelpDesk');	
		if($this->objParam->insertar('id_help_desk')){
			$this->res=$this->objFunc->insertarHelpDesk($this->objParam);			
		} else{			
			$this->res=$this->objFunc->modificarHelpDesk($this->objParam);
		}
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
						
	function eliminarHelpDesk(){
			$this->objFunc=$this->create('MODHelpDesk');	
		$this->res=$this->objFunc->eliminarHelpDesk($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
	
	function siguienteEstado(){
        $this->objFunc=$this->create('MODHelpDesk');  
        $this->res=$this->objFunc->siguienteEstado($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }

    function anteriorEstado(){
        $this->objFunc=$this->create('MODHelpDesk');  
        $this->res=$this->objFunc->anteriorEstado($this->objParam);
        $this->res->imprimirRespuesta($this->res->generarJson());
    }
	
	function insertarAtributoAsignacion(){		
		$this->objParam->defecto('ordenacion','id_help_desk');
		$this->objFunc=$this->create('MODHelpDesk');
		$this->res=$this->objFunc->insertarAtributoAsignacion($this->objParam);
		$this->res->imprimirRespuesta($this->res->generarJson());
	}
    function obtenerNumeroReferencial(){//#15
        $this->objParam->defecto('ordenacion','id_help_desk');

        $this->objFunc=$this->create('MODHelpDesk');
        $this->res=$this->objFunc->obtenerNumeroReferencial($this->objParam);

        $this->res->imprimirRespuesta($this->res->generarJson());
    }
		
			
}

?>