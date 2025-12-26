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
$xajax->registerFunction("guardar");
$xajax->registerFunction("eliminar");
$xajax->registerFunction("f_arma_codigo");
$xajax->registerFunction("f_cargar_datos");
$xajax->registerFunction("f_filtro_subgrupo");
$xajax->registerFunction("grabarDetalle");
$xajax->registerFunction("cargarDatosCuentas");
$xajax->registerFunction("eliminar_cta_gast");
$xajax->registerFunction("guardarResponsables");
$xajax->registerFunction("cargarDatosResponsables");
$xajax->registerFunction("eliminar_responsables");
$xajax->registerFunction("cargarDatosMantenimiento");
$xajax->registerFunction("guardarMantenimiento");
$xajax->registerFunction("eliminar_mantenimineto");
$xajax->registerFunction("guardarOtros");
$xajax->registerFunction("cargarDatosOtros");
$xajax->registerFunction("editar_otros_detalles");
$xajax->registerFunction("eliminar_otros_detalles");
$xajax->registerFunction("lista_reporte_index");
$xajax->registerFunction("nuevoDetalle");
$xajax->registerFunction("nuevoResponsable");
$xajax->registerFunction("nuevoAseguradoras");
$xajax->registerFunction("nuevoMantenimiento");
$xajax->registerFunction("validar_cuentas");
$xajax->registerFunction("validar_ccostos");
$xajax->registerFunction("cargarDatosPartes");
$xajax->registerFunction("grabarDetallePartes");
$xajax->registerFunction("eliminarDetallesPartes");
$xajax->registerFunction("f_tipoDepreciacion");
$xajax->registerFunction("imprime_ficha");
$xajax->registerFunction("form_empleados");

?>