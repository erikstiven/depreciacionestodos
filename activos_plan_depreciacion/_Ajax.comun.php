<?php
/* ARCHIVO COMUN PARA LA EJECUCION DEL SERVIDOR AJAX DEL MODULO */
/***************************************************/
/* NO MODIFICAR */
include_once('../../Include/config.inc.php');
include_once(path(DIR_INCLUDE).'conexiones/db_conexion.php');
include_once(path(DIR_INCLUDE).'comun.lib.php');
include_once(path(DIR_INCLUDE).'Clases/Formulario/Formulario.class.php');
require_once (path(DIR_INCLUDE).'Clases/xajax/xajax_core/xajax.inc.php');
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
$xajax->registerFunction("generarPlan");
$xajax->registerFunction("listarPlan");
$xajax->registerFunction("prorrogarPlan");
$xajax->registerFunction("validarPlan");
$xajax->registerFunction("f_filtro_grupo");
$xajax->registerFunction("f_filtro_subgrupo");
$xajax->registerFunction("f_filtro_activos_desde");
$xajax->registerFunction("f_filtro_activos_hasta");
$xajax->registerFunction("f_filtro_sucursal");

function plan_inicio_mes(DateTime $fecha)
{
    $inicio = new DateTime($fecha->format('Y-m-01'));
    if (intval($fecha->format('d')) !== 1) {
        $inicio->modify('+1 month');
    }
    return $inicio;
}

function plan_periodo_texto(DateTime $fecha)
{
    return $fecha->format('m/Y');
}

function plan_fecha_periodo($anio, $mes, $ultimoDia = false)
{
    $fecha = DateTime::createFromFormat('Y-n-j', $anio . '-' . intval($mes) . '-1');
    if (!$fecha) {
        return null;
    }
    if ($ultimoDia) {
        $fecha->modify('last day of this month');
    }
    return $fecha;
}

function plan_mensaje_alerta($mensaje)
{
    return '<div class="alert alert-warning">' . htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8') . '</div>';
}

function plan_mensaje_ok($mensaje)
{
    return '<div class="alert alert-success">' . htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8') . '</div>';
}

?>
