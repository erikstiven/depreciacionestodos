<?php
/* ARCHIVO COMUN PARA LA EJECUCION DEL SERVIDOR AJAX DEL MODULO */
/***************************************************/
/* NO MODIFICAR */
if (!function_exists('ajax_fatal')) {
    function ajax_fatal($message)
    {
        if (!headers_sent()) {
            header('Content-Type: text/plain; charset=utf-8');
        }
        echo $message;
        exit;
    }
}

$config_path = __DIR__ . '/../../Include/config.inc.php';
if (!file_exists($config_path)) {
    ajax_fatal('Error: No se encontró Include/config.inc.php. Verifica la ruta ../../Include/ desde activos_revalorizacion/_Ajax.comun.php');
}
include_once($config_path);

$db_path = path(DIR_INCLUDE) . 'conexiones/db_conexion.php';
$comun_path = path(DIR_INCLUDE) . 'comun.lib.php';
$formulario_path = path(DIR_INCLUDE) . 'Clases/Formulario/Formulario.class.php';
$xajax_path = path(DIR_INCLUDE) . 'Clases/xajax/xajax_core/xajax.inc.php';

if (!file_exists($db_path)) {
    ajax_fatal('Error: No se encontró conexiones/db_conexion.php en DIR_INCLUDE.');
}
if (!file_exists($comun_path)) {
    ajax_fatal('Error: No se encontró comun.lib.php en DIR_INCLUDE.');
}
if (!file_exists($formulario_path)) {
    ajax_fatal('Error: No se encontró Formulario.class.php en DIR_INCLUDE.');
}
if (!file_exists($xajax_path)) {
    ajax_fatal('Error: No se encontró xajax.inc.php en DIR_INCLUDE.');
}

include_once($db_path);
include_once($comun_path);
include_once($formulario_path);
require_once($xajax_path);
/***************************************************/
/* INSTANCIA DEL SERVIDOR AJAX DEL MODULO*/
$xajax = new xajax('_Ajax.server.php');
$xajax->setCharEncoding(SISTEMA_CHARSET);
$xajax->configure('decodeUTF8Input',true);
/***************************************************/
//    FUNCIONES PUBLICAS DEL SERVIDOR AJAX DEL MODULO 
//    Aqui registrar todas las funciones publicas del servidor ajax
//    Ejemplo,
//    $xajax->registerFunction("Nombre de la Funcion");
/***************************************************/
$xajax->registerFunction("genera_cabecera_formulario");
$xajax->registerFunction("generar");
$xajax->registerFunction("f_filtro_subgrupo");
$xajax->registerFunction("f_filtro_activos");
$xajax->registerFunction("f_informacion_activo");
$xajax->registerFunction("f_calcula_vida_util");
?>
