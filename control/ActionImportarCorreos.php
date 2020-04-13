<?php
/***
 * Nombre: ActionImportarCorreos.php
 * Proposito:  Importar correos del imbox hacia HelpDesk
 * Autor:    valvarado
 * Fecha:    09/04/2020
 */
include_once(dirname(__FILE__) . "/../../lib/lib_control/CTSesion.php");

session_start();
$_SESSION["_SESION"] = new CTSesion();

include(dirname(__FILE__) . '/../../lib/DatosGenerales.php');
include_once(dirname(__FILE__) . '/../../lib/lib_general/Errores.php');
require dirname(__FILE__) . '/../../lib/PHPMailer/PHPMailerAutoload.php';
include_once(dirname(__FILE__) . '/../../lib/lib_general/cls_correo_externo.php');
include_once(dirname(__FILE__) . '/../../lib/rest/PxpRestClient.php');
include_once(dirname(__FILE__) . '/../../lib/FirePHPCore-0.3.2/lib/FirePHPCore/FirePHP.class.php');

ob_start();
$fb = FirePHP::getInstance(true);
$_SESSION["_CANTIDAD_ERRORES"] = 0;

include_once(dirname(__FILE__) . '/../../lib/lib_control/CTincludes.php');
include_once(dirname(__FILE__) . '/../../sis_soporte/environment.php');
include_once(dirname(__FILE__) . '/../../sis_soporte/modelo/MODHelpDesk.php');

$objPostData = new CTPostData();
$arr_unlink = array();
$aPostData = $objPostData->getData();
$aPostFiles = $objPostData->getFiles();
$_SESSION["_PETICION"] = serialize($aPostData);
$objParam = new CTParametro($aPostData['p'], null, $aPostFiles, $aPostData['x']);
$objParam->addParametro('id_usuario', 1);

$inbox = imap_open(HOSTNAME, USERNAME, PASSWORD) or die('Cannot connect to : correo.endetransmision.bo, ' . imap_last_error());
$emails = imap_search($inbox, 'UNSEEN');
if ($emails) {
    $importados = 0;
    $cantidad_correos = count($emails);
    rsort($emails);
    foreach ($emails as $email_number) {
        $header = imap_headerinfo($inbox, $email_number);
        $overview = imap_fetch_overview($inbox, $email_number, 0);
        $message = imap_fetchbody($inbox, $email_number, "1.1", FT_PEEK);

        if ($message == "") {
            $message = imap_fetchbody($inbox, $email_number, "1", FT_PEEK);
        }
        $message = quoted_printable_decode($message);

        $date = new DateTime($overview[0]->date);
        $id_funcionario = '665';

        if (isset($header->from[0]->personal)) {
            $personal = $header->from[0]->personal;
        } else {
            $personal = $header->from[0]->mailbox;
        }
        $objParam->defecto('ordenacion', 'descripcion_cargo');
        $objParam->defecto('dir_ordenacion', 'asc');
        $objParam->addParametroConsulta('cantidad', 1);
        $objParam->addParametroConsulta('puntero', 0);

        $objParam->addFiltro("lower(FUNCAR.desc_funcionario1) like lower(''%" . $personal . "%'')");
        $hpd1 = new MODHelpDesk($objParam);
        $rs = $hpd1->obtenerFuncionario();

        if ($rs->getTipo() == 'EXITO') {
            $datos = $rs->getDatos();
            $id_funcionario = $datos[0]['id_funcionario'];

            $objParam->addParametro('id_funcionario', $id_funcionario);
            $objParam->addParametro('id_estado_wf', 'NULL');
            $objParam->addParametro('fecha', $date->format("Y-m-d"));
            $objParam->addParametro('estado_reg', 'activo');
            $objParam->addParametro('descripcion', htmlentities($message));
            $objParam->addParametro('id_tipo', '2');
            $objParam->addParametro('numero_ref', 'NULL');
            $objParam->addParametro('numero_correo', $email_number);
            $hpd2 = new MODHelpDesk($objParam);
            $rs1 = $hpd2->insertarHelpDesk();
            if ($rs1->getTipo() == 'EXITO') {
                $datos = $rs1->getDatos();
                $objParam->addParametro('id_help_desk', $datos['id_help_desk']);
                $hpd3 = new MODHelpDesk($objParam);
                $rs2 = $hpd3->obtenerDatosWFHelpDesk();
                if ($rs2->getTipo() == 'EXITO') {
                    $datos = $rs2->getDatos();
                    $this->objParam->addParametro('id_proceso_wf_act', $datos[0]['id_proceso_wf']);
                    $this->objParam->addParametro('id_estado_wf_act', $datos[0]['id_estado_wf']);
                    $this->objParam->addParametro('id_depto_wf', $datos[0]['id_depto_wf']);
                    $this->objParam->addParametro('id_tipo_estado', $datos[0]['id_tipo_estado']);
                    $this->objParam->addParametro('id_funcionario_wf', 'NULL');
                    $this->objParam->addParametro('json_procesos', '[]');
                    $hpd4 = new MODHelpDesk($objParam);
                    $rs3 = $hpd4->siguienteEstado();
                }
                $importados++;
            }
        }
    }
}
if ($cantidad_correos == $importados) {
    $mensajeExito = new Mensaje();
    $mensajeExito->setMensaje('EXITO', 'ACTHelpDesk.php', 'Correos Importados Verifique su información', 'Se importaron ' . $cantidad_correos . ' correos!', '', '', '', '');
    $res = $mensajeExito;
    $res->imprimirRespuesta($res->generarJson());
} else {
    $mensajeExito = new Mensaje();
    $mensajeExito->setMensaje('ERROR', 'ACTHelpDesk.php', 'Verifique su información', 'Un fallo insperado ocurrió al importar correos!', '', '', '', '');
    $res = $mensajeExito;
    $res->imprimirRespuesta($res->generarJson());
}
exit;