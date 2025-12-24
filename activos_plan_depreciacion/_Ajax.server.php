<?php

include_once('../../Include/config.inc.php');
include_once(path(DIR_INCLUDE) . 'conexiones/db_conexion.php');
include_once(path(DIR_INCLUDE) . 'comun.lib.php');
include_once(path(DIR_INCLUDE) . 'Clases/Formulario/Formulario.class.php');
require_once(path(DIR_INCLUDE) . 'Clases/xajax/xajax_core/xajax.inc.php');

require_once(__DIR__ . '/_Functions.php');

$xajax = new xajax('_Ajax.server.php');
$xajax->setCharEncoding(SISTEMA_CHARSET);
$xajax->configure('decodeUTF8Input', true);

$xajax->registerFunction("genera_cabecera_formulario");
$xajax->registerFunction("generar_plan");
$xajax->registerFunction("f_filtro_sucursal");
$xajax->registerFunction("f_filtro_grupo");
$xajax->registerFunction("f_filtro_subgrupo");
$xajax->registerFunction("f_filtro_activos_desde");
$xajax->registerFunction("f_filtro_activos_hasta");

function genera_cabecera_formulario($sAccion = 'nuevo', $aForm = '')
{
    global $DSN, $DSN_Ifx;

    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $oIfx = new Dbo();
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    $oReturn = new xajaxResponse();

    $idempresa = $_SESSION['U_EMPRESA'] ?? '';
    $empresa = $aForm['empresa'] ?? '';
    if (empty($empresa)) {
        $empresa = $idempresa;
    }

    $sql = "select empr_cod_empr, empr_nom_empr from saeempr order by empr_nom_empr";
    $i = 1;
    if ($oIfx->Query($sql)) {
        $oReturn->script('eliminar_lista_empresa();');
        if ($oIfx->NumFilas() > 0) {
            do {
                $oReturn->script(('anadir_elemento_empresa(' . $i++ . ',\'' . $oIfx->f('empr_cod_empr') . '\', \'' . $oIfx->f('empr_nom_empr') . '\')'));
            } while ($oIfx->SiguienteRegistro());
        }
    }

    if (!empty($empresa)) {
        $oReturn->assign('empresa', 'value', $empresa);
    }

    $oReturn->script('f_filtro_sucursal();');
    $oReturn->script('f_filtro_grupo();');

    return $oReturn;
}

function f_filtro_sucursal($aForm)
{
    global $DSN_Ifx;
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $oIfx = new Dbo();
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    $oReturn = new xajaxResponse();

    $empresa = $aForm['empresa'];

    $sql = "select sucu_cod_sucu, sucu_nom_sucu
            from saesucu
            where sucu_cod_empr = '$empresa'
            order by sucu_nom_sucu";
    $i = 1;
    if ($oIfx->Query($sql)) {
        $oReturn->script('eliminar_lista_sucursal();');
        if ($oIfx->NumFilas() > 0) {
            do {
                $oReturn->script(('anadir_elemento_sucursal(' . $i++ . ',\'' . $oIfx->f('sucu_cod_sucu') . '\', \'' . $oIfx->f('sucu_nom_sucu') . '\')'));
            } while ($oIfx->SiguienteRegistro());
        }
    }

    $oReturn->script('f_filtro_subgrupo();');
    return $oReturn;
}

function f_filtro_grupo($aForm)
{
    global $DSN_Ifx;
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $oIfx = new Dbo();
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    $oReturn = new xajaxResponse();

    $empresa = $aForm['empresa'];

    $sql = "select gact_cod_gact, gact_des_gact
            from saegact
            where gact_cod_empr = '$empresa'
            order by gact_des_gact";
    $i = 1;
    if ($oIfx->Query($sql)) {
        $oReturn->script('eliminar_lista_grupo();');
        if ($oIfx->NumFilas() > 0) {
            do {
                $oReturn->script(('anadir_elemento_grupo(' . $i++ . ',\'' . $oIfx->f('gact_cod_gact') . '\', \'' . $oIfx->f('gact_des_gact') . '\')'));
            } while ($oIfx->SiguienteRegistro());
        }
    }

    $oReturn->script('f_filtro_subgrupo();');
    return $oReturn;
}

function f_filtro_subgrupo($aForm = '')
{
    global $DSN_Ifx;
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $oIfx = new Dbo();
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    $oReturn = new xajaxResponse();

    $codigoGrupo = $aForm['cod_grupo'];
    $empresa = $aForm['empresa'];

    $sql = "select sgac_cod_sgac, sgac_des_sgac
            from saesgac
            where sgac_cod_empr = '$empresa'
            and gact_cod_gact = '$codigoGrupo'
            order by sgac_des_sgac";
    $i = 1;
    if ($oIfx->Query($sql)) {
        $oReturn->script('eliminar_lista_subgrupo();');
        if ($oIfx->NumFilas() > 0) {
            do {
                $oReturn->script(('anadir_elemento_subgrupo(' . $i++ . ',\'' . $oIfx->f('sgac_cod_sgac') . '\', \'' . $oIfx->f('sgac_des_sgac') . '\')'));
            } while ($oIfx->SiguienteRegistro());
        }
    }

    $oReturn->script('f_filtro_activos_desde();');
    $oReturn->script('f_filtro_activos_hasta();');
    return $oReturn;
}

function f_filtro_activos_desde($aForm)
{
    global $DSN_Ifx;
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $oIfx = new Dbo();
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    $oReturn = new xajaxResponse();

    $empresa = $aForm['empresa'];
    $sucursal = $aForm['sucursal'];
    $subgrupo = $aForm['cod_subgrupo'];

    $sql = "select act_cod_act, act_nom_act, act_clave_act
            from saeact
            where act_cod_empr = '$empresa'
            and act_cod_sucu = '$sucursal'
            and sgac_cod_sgac  = '$subgrupo'
            order by act_cod_act";
    $i = 1;
    if ($oIfx->Query($sql)) {
        $oReturn->script('eliminar_lista_activo_desde();');
        if ($oIfx->NumFilas() > 0) {
            do {
                $oReturn->script(('anadir_elemento_activo_desde(' . $i++ . ',\'' . $oIfx->f('act_cod_act') . '\', \'' . $oIfx->f('act_clave_act') . ' - ' . $oIfx->f('act_nom_act') . '\')'));
            } while ($oIfx->SiguienteRegistro());
        }
    }
    return $oReturn;
}

function f_filtro_activos_hasta($aForm)
{
    global $DSN_Ifx;
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $oIfx = new Dbo();
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    $oReturn = new xajaxResponse();

    $empresa = $aForm['empresa'];
    $sucursal = $aForm['sucursal'];
    $subgrupo = $aForm['cod_subgrupo'];

    $sql = "select act_cod_act, act_nom_act, act_clave_act
            from saeact
            where act_cod_empr = '$empresa'
            and act_cod_sucu = '$sucursal'
            and sgac_cod_sgac  = '$subgrupo'
            order by act_cod_act";
    $i = 1;
    if ($oIfx->Query($sql)) {
        $oReturn->script('eliminar_lista_activo_hasta();');
        if ($oIfx->NumFilas() > 0) {
            do {
                $oReturn->script(('anadir_elemento_activo_hasta(' . $i++ . ',\'' . $oIfx->f('act_cod_act') . '\', \'' . $oIfx->f('act_clave_act') . ' - ' . $oIfx->f('act_nom_act') . '\')'));
            } while ($oIfx->SiguienteRegistro());
        }
    }
    return $oReturn;
}

function generar_plan($aForm)
{
    global $DSN_Ifx;

    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $oIfx = new Dbo();
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    $oIfxA = new Dbo();
    $oIfxA->DSN = $DSN_Ifx;
    $oIfxA->Conectar();

    $oReturn = new xajaxResponse();

    $empresa = $aForm['empresa'];
    $sucursal = $aForm['sucursal'];
    $grupo = $aForm['cod_grupo'];
    $subgrupo = $aForm['cod_subgrupo'];
    $activo_desde = $aForm['cod_activo_desde'];
    $activo_hasta = $aForm['cod_activo_hasta'];

    if (empty($empresa) || empty($sucursal)) {
        $oReturn->alert('Debe seleccionar Empresa y Sucursal.');
        return $oReturn;
    }

    $filtro = '';
    if (!empty($grupo)) {
        $filtro .= " and saeact.gact_cod_gact = '" . $grupo . "'";
    }
    if (!empty($subgrupo)) {
        $filtro .= " and saeact.sgac_cod_sgac = '" . $subgrupo . "'";
    }
    if (!empty($activo_desde) && !empty($activo_hasta)) {
        $filtro .= " and saeact.act_cod_act between " . $activo_desde . " and " . $activo_hasta;
    }

    $sql = "SELECT saeact.act_cod_act,
                   saeact.act_vutil_act,
                   saeact.act_val_comp,
                   saeact.act_fcmp_act,
                   saeact.act_fdep_act,
                   saeact.act_fiman_act,
                   saeact.act_vres_act,
                   saeact.act_cod_sucu,
                   saeact.act_clave_act,
                   saeact.act_nom_act
            FROM saeact
            WHERE saeact.act_cod_empr = $empresa
              AND saeact.act_cod_sucu = $sucursal
              AND saeact.act_ext_act = 1
              $filtro
            ORDER BY saeact.act_cod_act";

    $procesados = 0;
    $omitidos = 0;
    $detalle = [];

    if ($oIfxA->Query($sql) && $oIfxA->NumFilas() > 0) {
        do {
            $codigo_activo = $oIfxA->f('act_cod_act');
            $vida_util = floatval($oIfxA->f('act_vutil_act'));
            $valor_compra = floatval($oIfxA->f('act_val_comp'));
            $valor_residual = floatval($oIfxA->f('act_vres_act'));
            $fecha_compra = $oIfxA->f('act_fcmp_act');
            $fecha_depreciacion = $oIfxA->f('act_fdep_act');
            $fecha_fin_activo = $oIfxA->f('act_fiman_act');
            $clave_activo = $oIfxA->f('act_clave_act');
            $nombre_activo = $oIfxA->f('act_nom_act');
            $motivo = '';

            if ($vida_util <= 0) {
                $motivo = 'VIDA ÚTIL INVÁLIDA';
            } elseif ($valor_compra <= $valor_residual) {
                $motivo = 'VALOR COMPRA NO SUPERA RESIDUAL';
            } elseif (!empty($fecha_fin_activo)) {
                $motivo = 'ACTIVO DADO DE BAJA';
            }

            $inicio_activo_dt = obtener_fecha_inicio_activo($fecha_depreciacion, $fecha_compra);
            if (!$inicio_activo_dt) {
                $motivo = 'FECHA INICIAL INVÁLIDA';
            }

            $sql_plan = "select count(*) as total_plan
                        from saemet
                        where metd_cod_acti = $codigo_activo
                        and act_cod_empr = $empresa
                        and act_cod_sucu = $sucursal";
            $total_plan = intval(consulta_string($sql_plan, 'total_plan', $oIfx, 0));
            if ($total_plan > 0) {
                $motivo = 'PLAN YA EXISTE';
            }

            if (!empty($motivo)) {
                $omitidos++;
                $detalle[] = [
                    'activo' => $clave_activo,
                    'nombre' => $nombre_activo,
                    'estado' => 'OMITIDO',
                    'motivo' => $motivo,
                ];
                continue;
            }

            $vida_util_meses = intval(round($vida_util * 12, 0));
            $valor_neto = $valor_compra - $valor_residual;
            $plan = calcular_plan_prorrateo_real($inicio_activo_dt, $vida_util_meses, $valor_neto);

            if (empty($plan)) {
                $omitidos++;
                $detalle[] = [
                    'activo' => $clave_activo,
                    'nombre' => $nombre_activo,
                    'estado' => 'OMITIDO',
                    'motivo' => 'PLAN NO GENERADO',
                ];
                continue;
            }

            foreach ($plan as $fila) {
                $fecha_hasta = $fila['fecha_hasta'];
                $sql_existe = "select count(*) as existe
                    from saemet
                    where metd_cod_acti = $codigo_activo
                    and act_cod_empr = $empresa
                    and act_cod_sucu = $sucursal
                    and metd_has_fech = '$fecha_hasta'";
                $existe = intval(consulta_string($sql_existe, 'existe', $oIfx, 0));
                if ($existe > 0) {
                    continue;
                }

                $porcentaje = $valor_neto > 0 ? round($fila['valor_mes'] / $valor_neto, 6) : 0;
                $sql_insert = "insert into saemet
                    (met_anio_met, metd_des_fech, metd_has_fech, metd_cod_empr, metd_cod_acti,
                     act_cod_empr, act_cod_sucu, met_porc_met, metd_val_metd, met_num_dias)
                    values ({$fila['anio']}, '{$fila['fecha_desde']}', '{$fila['fecha_hasta']}',
                        $empresa, $codigo_activo, $empresa, $sucursal, $porcentaje, {$fila['valor_mes']}, {$fila['dias_prorrateo']})";
                $oIfx->QueryT($sql_insert);
            }

            $procesados++;
            $detalle[] = [
                'activo' => $clave_activo,
                'nombre' => $nombre_activo,
                'estado' => 'PROCESADO',
                'motivo' => 'PLAN GENERADO',
            ];
        } while ($oIfxA->SiguienteRegistro());
    }

    $tabla = '<div class="row">'
        . '<div class="col-md-12">'
        . '<p><strong>Activos procesados:</strong> ' . $procesados . '</p>'
        . '<p><strong>Activos omitidos:</strong> ' . $omitidos . '</p>'
        . '</div>'
        . '</div>'
        . '<div class="table-responsive" style="max-height: 320px; overflow: auto;">'
        . '<table class="table table-striped table-hover" style="width: 100%; margin-bottom: 0px;">'
        . '<tr class="msgFrm">'
        . '<td class="bg-primary text-center"><h5>Activo</h5></td>'
        . '<td class="bg-primary text-center"><h5>Nombre</h5></td>'
        . '<td class="bg-primary text-center"><h5>Estado</h5></td>'
        . '<td class="bg-primary text-center"><h5>Motivo</h5></td>'
        . '</tr>';

    foreach ($detalle as $fila) {
        $tabla .= '<tr>'
            . '<td>' . htmlspecialchars($fila['activo'], ENT_QUOTES, 'UTF-8') . '</td>'
            . '<td>' . htmlspecialchars($fila['nombre'], ENT_QUOTES, 'UTF-8') . '</td>'
            . '<td>' . htmlspecialchars($fila['estado'], ENT_QUOTES, 'UTF-8') . '</td>'
            . '<td>' . htmlspecialchars($fila['motivo'], ENT_QUOTES, 'UTF-8') . '</td>'
            . '</tr>';
    }

    $tabla .= '</table></div>';

    $oReturn->assign('reporte', 'innerHTML', $tabla);
    $oReturn->alert('Proceso Terminado con Exito');

    return $oReturn;
}

$xajax->processRequest();
