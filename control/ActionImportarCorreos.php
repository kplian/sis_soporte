<?php
/***
 * Nombre: ActionImportarCorreos.php
 * Proposito:  Importar correos del imbox hacia HelpDesk
 * Autor:    valvarado
 * Fecha:    09/04/2020
 */
include_once(dirname(__FILE__) . "/../../lib/lib_control/CTSesion.php");
session_start();
$session = new CTSesion();
$session->setIdUsuario(1);
$_SESSION["_SESION"] = $session;
$_SESSION["ss_id_usuario"] = 1;
include(dirname(__FILE__) . '/../../lib/DatosGenerales.php');
include_once(dirname(__FILE__) . '/../../lib/lib_general/Errores.php');
ob_start();
$_SESSION["_CANTIDAD_ERRORES"] = 0;

include_once(dirname(__FILE__) . '/../../lib/lib_control/CTincludes.php');
include_once(dirname(__FILE__) . '/../../sis_soporte/control/ACTHelpDesk.php');

$objPostData = new CTPostData();
$arr_unlink = array();
$aPostData = $objPostData->getData();

$_SESSION["_PETICION"] = serialize($aPostData);
$objParam = new CTParametro($aPostData['p'], null, $aPostFiles);
$objParam->addParametro('id_usuario', 1);
$helpDesk = new ACTHelpDesk($objParam);
$rs = $helpDesk->importar_correos();
print_r($rs);
?>