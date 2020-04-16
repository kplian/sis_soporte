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
require_once dirname(__DIR__).'/environment.php';
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
        $this->objParam->addParametro('numero_correo', 'NULL');
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

    function importar_correos()
    {
        $hostname = HOSTNAME;
        $username = USERNAME;
        $password = PASSWORD;

        $inbox = imap_open($hostname, $username, $password) or die('Cannot connect to : correo.endetransmision.bo, ' . imap_last_error());
        $emails = imap_search($inbox, 'UNSEEN');
        if ($emails) {
            $importados = 0;
            $cantidad_correos = count($emails);
            rsort($emails);
            foreach ($emails as $email_number) {
                $header = imap_headerinfo($inbox, $email_number);
                $overview = imap_fetch_overview($inbox, $email_number, 0);
                $structure = imap_fetchstructure($inbox, $email_number);
                $email_empresa = $header->from[0]->mailbox . "@" . $header->from[0]->host;
                if (isset($header->from[0]->personal)) {
                    $personal = iconv_mime_decode($header->from[0]->personal, 0, "UTF-8");
                } else {
                    $personal = iconv_mime_decode($header->from[0]->mailbox, 0, "UTF-8");
                }

                if (isset($structure->parts) && is_array($structure->parts) && isset($structure->parts[1])) {
                    $part = $structure->parts[1];
                    $message = imap_fetchbody($inbox, $email_number, "1.1", FT_PEEK);
                    if ($message == '') {
                        $message = imap_fetchbody($inbox, $email_number, "1", FT_PEEK);
                    }
                    if ($message == '') {
                        $message = imap_fetchbody($inbox, $email_number, "2.1", FT_PEEK);
                    }
                    if ($message == '') {
                        $message = imap_fetchbody($inbox, $email_number, "2.2", FT_PEEK);
                    }
                    if ($message == '') {
                        $message = imap_fetchbody($inbox, $email_number, "", FT_PEEK);
                    }
                    $message = quoted_printable_decode($message);
                }

                $post_forwarded = strpos($message, "-------- Forwarded Message --------");
                if ($post_forwarded > 0)
                    $message = substr($message, 0, $post_forwarded);
                $tmp = htmlspecialchars(strip_tags($message));
                if ($tmp == "") {
                    $message = utf8_encode($message);
                } else {
                    $message = htmlspecialchars(strip_tags($message));
                }
                $date = new DateTime($overview[0]->date);
                $id_funcionario = '665';
                $rs = $this->obtenerFuncionario($email_empresa);

                if ($rs->getTipo() == 'EXITO') {
                    $datos = $rs->getDatos();

                    if (sizeof($datos) > 0) {
                        $id_funcionario = $datos[0]['id_funcionario'];

                        $this->objParam->addParametro('id_funcionario', $id_funcionario);
                        $this->objParam->addParametro('id_estado_wf', 'NULL');
                        $this->objParam->addParametro('fecha', $date->format("Y-m-d"));
                        $this->objParam->addParametro('estado_reg', 'activo');
                        $this->objParam->addParametro('descripcion', $message);
                        $this->objParam->addParametro('id_tipo', '2');
                        $this->objParam->addParametro('numero_ref', 'NULL');
                        $this->objParam->addParametro('numero_correo', $email_number);
                        $this->objFunc = $this->create('MODHelpDeskImportar');
                        $rs1 = $this->objFunc->insertarHelpDesk($this->objParam);
                        if ($rs1->getTipo() == 'EXITO') {
                            $datos = $rs1->getDatos();
                            $this->objParam->addParametro('id_help_desk', $datos['id_help_desk']);
                            $rs2 = $this->obtenerDatosWfHelpDesk();
                            if ($rs2->getTipo() == 'EXITO') {
                                $datos = $rs2->getDatos();
                                $this->objParam->addParametro('id_proceso_wf_act', $datos[0]['id_proceso_wf']);
                                $this->objParam->addParametro('id_estado_wf_act', $datos[0]['id_estado_wf']);
                                $this->objParam->addParametro('id_depto_wf', $datos[0]['id_depto_wf']);
                                $this->objParam->addParametro('id_tipo_estado', $datos[0]['id_tipo_estado']);
                                $this->objParam->addParametro('id_funcionario_wf', 'NULL');
                                $this->objParam->addParametro('json_procesos', '[]');
                                $this->objFunc1 = $this->create('MODHelpDeskImportar');
                                $rs3 = $this->objFunc1->siguienteEstado($this->objParam);
                            }
                            imap_setflag_full($inbox, $email_number, "\\Seen \\Flagged");
                            $importados++;
                        }
                    }
                }
            }
        }
        if ($cantidad_correos == $importados) {
            $mensajeExito = new Mensaje();
            $mensajeExito->setMensaje('EXITO', 'ACTHelpDesk.php', 'Correos Importados Verifique su información', 'Se importaron ' . $cantidad_correos . ' correos!', '', '', '', '');
            $this->res = $mensajeExito;
            $this->res->imprimirRespuesta($this->res->generarJson());
        } else {
            $mensajeExito = new Mensaje();
            $mensajeExito->setMensaje('ERROR', 'ACTHelpDesk.php', 'Verifique su información', 'Un fallo insperado ocurrió al importar correos!', '', '', '', '');
            $this->res = $mensajeExito;
            $this->res->imprimirRespuesta($this->res->generarJson());
        }
    }

    function obtenerFuncionario($searchText)
    {
        $this->objParam->defecto('ordenacion', 'descripcion_cargo');
        $this->objParam->defecto('dir_ordenacion', 'asc');
        $this->objParam->addParametroConsulta('cantidad', 1);
        $this->objParam->addParametroConsulta('puntero', 0);
        $this->objParam->parametros_consulta['filtro'] = ' 0 = 0 ';
        $this->objParam->addFiltro("lower(FUNCAR.email_empresa) LIKE lower(''%" . $searchText . "%'')");
        $this->objFun1 = $this->create('MODHelpDeskImportar');
        return $this->objFun1->obtenerFuncionario($this->objParam);
    }

    function obtenerDatosWfHelpDesk()
    {
        $this->objFun1 = $this->create('MODHelpDeskImportar');
        return $this->objFun1->obtenerDatosWFHelpDesk($this->objParam);
    }
}

?>