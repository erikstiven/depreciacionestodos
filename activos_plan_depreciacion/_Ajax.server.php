<?php

require("_Ajax.comun.php"); // No modificar esta linea

ini_set('display_errors', 1);
error_reporting(E_ALL);
/* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
  // S E R V I D O R   A J A X //
  :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */

function f_filtro_sucursal($aForm, $data)
{
    global $DSN, $DSN_Ifx;
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
                $oReturn->script(('anadir_elemento_sucursal(' . $i++ . ',\'' . $oIfx->f('sucu_cod_sucu') . '\', \'' . $oIfx->f('sucu_nom_sucu') . '\' )'));
            } while ($oIfx->SiguienteRegistro());
        }
    }
    $oReturn->assign('sucursal', 'value', $data);
    return $oReturn;
}

function f_filtro_activos_desde($aForm)
{
    global $DSN, $DSN_Ifx;
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $oIfx = new Dbo();
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    $oReturn = new xajaxResponse();
    $idempresa = $_SESSION['U_EMPRESA'];
    $idsucursal = $_SESSION['U_SUCURSAL'];
    $empresa = $aForm['empresa'];
    $sucursal = $aForm['sucursal'];
    $subgrupo = $aForm['cod_subgrupo'];
    if (empty($empresa)) {
        $empresa = $idempresa;
    }
    if (empty($sucursal)) {
        $sucursal = $idsucursal;
    }
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
                $oReturn->script(('anadir_elemento_activo_desde(' . $i++ . ',\'' . $oIfx->f('act_cod_act') . '\', \'' . $oIfx->f('act_clave_act') . ' - ' . $oIfx->f('act_nom_act') . '\' )'));
            } while ($oIfx->SiguienteRegistro());
        }
    }
    return $oReturn;
}

function f_filtro_activos_hasta($aForm)
{
    global $DSN, $DSN_Ifx;
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $oIfx = new Dbo();
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    $oReturn = new xajaxResponse();

    $idempresa = $_SESSION['U_EMPRESA'];
    $idsucursal = $_SESSION['U_SUCURSAL'];
    $empresa = $aForm['empresa'];
    $sucursal = $aForm['sucursal'];
    $subgrupo = $aForm['cod_subgrupo'];
    if (empty($empresa)) {
        $empresa = $idempresa;
    }
    if (empty($sucursal)) {
        $sucursal = $idsucursal;
    }
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
                $oReturn->script(('anadir_elemento_activo_hasta(' . $i++ . ',\'' . $oIfx->f('act_cod_act') . '\', \'' . $oIfx->f('act_clave_act') . ' - ' . $oIfx->f('act_nom_act') . '\' )'));
            } while ($oIfx->SiguienteRegistro());
        }
    }
    return $oReturn;
}

function f_filtro_grupo($aForm, $data)
{
    global $DSN, $DSN_Ifx;

    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $oIfx = new Dbo();
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    $oReturn = new xajaxResponse();
    $idempresa = $_SESSION['U_EMPRESA'];
    $empresa = $aForm['empresa'];

    if (empty($empresa)) {
        $empresa = $idempresa;
    }

    $sql = "select gact_cod_gact, gact_des_gact
             from saegact
             where gact_cod_empr = '$empresa'
             order by gact_des_gact";
    $i = 1;
    if ($oIfx->Query($sql)) {
        $oReturn->script('eliminar_lista_grupo();');
        if ($oIfx->NumFilas() > 0) {
            do {
                $oReturn->script(('anadir_elemento_grupo(' . $i++ . ',\'' . $oIfx->f('gact_cod_gact') . '\', \'' . $oIfx->f('gact_des_gact') . '\' )'));
            } while ($oIfx->SiguienteRegistro());
        }
    }

    $oReturn->assign('cod_grupo', 'value', $data);
    $oReturn->assign('cod_activo_desde', 'value', null);
    $oReturn->assign('cod_activo_hasta', 'value', null);

    return $oReturn;
}

function f_filtro_subgrupo($aForm = '')
{
    global $DSN, $DSN_Ifx;

    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $oIfx = new Dbo();
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    $oReturn = new xajaxResponse();
    $idempresa = $_SESSION['U_EMPRESA'];
    $empresa = $aForm['empresa'];
    $codigoGrupo = $aForm['cod_grupo'];
    if (empty($empresa)) {
        $empresa = $idempresa;
    }

    $sql = "select sgac_cod_sgac, sgac_des_sgac
             from saesgac where sgac_cod_empr = $empresa
             and gact_cod_gact = '$codigoGrupo'
             order by sgac_des_sgac";
    $i = 1;
    if ($oIfx->Query($sql)) {
        $oReturn->script('eliminar_lista_subgrupo();');
        if ($oIfx->NumFilas() > 0) {
            do {
                $oReturn->script(('anadir_elemento_subgrupo(' . $i++ . ',\'' . $oIfx->f('sgac_cod_sgac') . '\', \'' . $oIfx->f('sgac_des_sgac') . '\' )'));
            } while ($oIfx->SiguienteRegistro());
        }
    }
    $oReturn->script('f_filtro_activos_desde()');
    $oReturn->script('f_filtro_activos_hasta()');
    return $oReturn;
}

function plan_obtener_contexto($aForm)
{
    $empresa = $aForm['empresa'];
    $sucursal = $aForm['sucursal'];
    $grupo = $aForm['cod_grupo'];
    $subgrupo = $aForm['cod_subgrupo'];
    $activo_desde = $aForm['cod_activo_desde'];
    $activo_hasta = $aForm['cod_activo_hasta'];

    $idempresa = $_SESSION['U_EMPRESA'];
    $idsucursal = $_SESSION['U_SUCURSAL'];

    if (empty($empresa)) {
        $empresa = $idempresa;
    }
    if (empty($sucursal)) {
        $sucursal = $idsucursal;
    }

    $filtro = '';
    if (!empty($grupo) && $grupo !== '0') {
        $filtro .= " and saeact.gact_cod_gact = '$grupo'";
    }
    if (!empty($subgrupo) && $subgrupo !== '0') {
        $filtro .= " and saeact.sgac_cod_sgac = '$subgrupo'";
    }
    if (!empty($activo_desde) && $activo_desde !== '0' && !empty($activo_hasta) && $activo_hasta !== '0') {
        $filtro .= " and saeact.act_cod_act between $activo_desde and $activo_hasta";
    }

    return [
        'empresa' => $empresa,
        'sucursal' => $sucursal,
        'grupo' => $grupo,
        'subgrupo' => $subgrupo,
        'activo_desde' => $activo_desde,
        'activo_hasta' => $activo_hasta,
        'filtro' => $filtro,
    ];
}

function plan_filtros_completos($aForm)
{
    return !empty($aForm['empresa']) && $aForm['empresa'] !== '0'
        && !empty($aForm['sucursal']) && $aForm['sucursal'] !== '0'
        && !empty($aForm['cod_grupo']) && $aForm['cod_grupo'] !== '0'
        && !empty($aForm['cod_subgrupo']) && $aForm['cod_subgrupo'] !== '0'
        && !empty($aForm['cod_activo_desde']) && $aForm['cod_activo_desde'] !== '0'
        && !empty($aForm['cod_activo_hasta']) && $aForm['cod_activo_hasta'] !== '0';
}

function plan_tiene_met_estado($oIfx)
{
    $sql = "select count(*) as total
            from information_schema.columns
            where table_name = 'saemet'
            and column_name = 'met_estado'";
    return intval(consulta_string($sql, 'total', $oIfx, 0)) > 0;
}

function generarPlan($aForm = '')
{
    global $DSN, $DSN_Ifx;

    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $oIfx = new Dbo();
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    $oReturn = new xajaxResponse();

    if (!plan_filtros_completos($aForm)) {
        return $oReturn;
    }

    $oReturn->assign('divPlanMensajes', 'innerHTML', plan_mensaje_alerta('El plan se genera desde la ficha del activo. Use \"Consultar Plan\" para revisar y validar.'));
    $oReturn->script("document.getElementById('divPlanMensajes').style.display = 'block';");
    return $oReturn;
}

function listarPlan($aForm = '')
{
    global $DSN_Ifx;

    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $oIfx = new Dbo();
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    $oReturn = new xajaxResponse();

    if (!plan_filtros_completos($aForm)) {
        $oReturn->assign('divPlanTabla', 'innerHTML', '');
        $oReturn->assign('divPlanMensajes', 'innerHTML', '');
        $oReturn->script("document.getElementById('divPlanMensajes').style.display = 'none';");
        return $oReturn;
    }

    $contexto = plan_obtener_contexto($aForm);
    $empresa = $contexto['empresa'];
    $sucursal = $contexto['sucursal'];
    $filtro = $contexto['filtro'];
    $tiene_estado = plan_tiene_met_estado($oIfx);

    $campo_estado = $tiene_estado ? "coalesce(saemet.met_estado, 'P')" : "'P'";
    $sql = "select saeact.act_cod_act,
                   saeact.act_clave_act,
                   saeact.act_nom_act,
                   saemet.metd_des_fech,
                   saemet.metd_has_fech,
                   saemet.metd_val_metd,
                   $campo_estado as met_estado
            from saemet
            join saeact on saeact.act_cod_act = saemet.metd_cod_acti
            where saemet.metd_cod_empr = $empresa
            and saemet.act_cod_sucu = $sucursal
            $filtro
            order by saeact.act_cod_act, saemet.metd_has_fech";

    $tabla = '';
    if ($oIfx->Query($sql)) {
        $acumulado = 0;
        $activo_actual = null;
        while ($oIfx->SiguienteRegistro()) {
            $codigo_activo = $oIfx->f('act_cod_act');
            if ($activo_actual !== $codigo_activo) {
                $activo_actual = $codigo_activo;
                $acumulado = 0;
            }
            $monto = floatval($oIfx->f('metd_val_metd'));
            $acumulado += $monto;
            $periodo = DateTime::createFromFormat('Y-m-d', $oIfx->f('metd_has_fech'));
            $texto_periodo = $periodo ? plan_periodo_texto($periodo) : $oIfx->f('metd_has_fech');
            $tabla .= '<tr>'
                . '<td>' . htmlspecialchars($oIfx->f('act_clave_act'), ENT_QUOTES, 'UTF-8') . '</td>'
                . '<td>' . htmlspecialchars($oIfx->f('act_nom_act'), ENT_QUOTES, 'UTF-8') . '</td>'
                . '<td>' . $texto_periodo . '</td>'
                . '<td class="text-right">' . number_format($monto, 2, '.', ',') . '</td>'
                . '<td class="text-right">' . number_format($acumulado, 2, '.', ',') . '</td>'
                . '<td>' . htmlspecialchars($oIfx->f('met_estado'), ENT_QUOTES, 'UTF-8') . '</td>'
                . '</tr>';
        }
    }

    if (empty($tabla)) {
        $tabla = '<tr><td colspan="6" class="text-center">No hay plan de depreciación para los filtros seleccionados.</td></tr>';
    }

    $html = '<table id="tablaPlanDepreciacion" class="table table-bordered table-condensed table-hover">'
        . '<thead><tr>'
        . '<th>Activo</th>'
        . '<th>Nombre</th>'
        . '<th>Periodo</th>'
        . '<th>Monto</th>'
        . '<th>Acumulado</th>'
        . '<th>Estado</th>'
        . '</tr></thead>'
        . '<tbody>' . $tabla . '</tbody>'
        . '</table>';

    $oReturn->assign('divPlanTabla', 'innerHTML', $html);
    $oReturn->script('initPlanDataTable();');
    return $oReturn;
}

function prorrogarPlan($aForm = '')
{
    global $DSN_Ifx;

    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $oIfx = new Dbo();
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    $oReturn = new xajaxResponse();

    if (!plan_filtros_completos($aForm)) {
        return $oReturn;
    }

    $contexto = plan_obtener_contexto($aForm);
    $empresa = $contexto['empresa'];
    $sucursal = $contexto['sucursal'];
    $activo_desde = $contexto['activo_desde'];
    $activo_hasta = $contexto['activo_hasta'];
    $meses_prorroga = intval($aForm['meses_prorroga']);
    $tiene_estado = plan_tiene_met_estado($oIfx);

    if (empty($activo_desde) || $activo_desde === '0' || $activo_desde !== $activo_hasta) {
        $oReturn->assign('divPlanMensajes', 'innerHTML', plan_mensaje_alerta('Debe seleccionar un activo específico para prorrogar.'));
        $oReturn->script("document.getElementById('divPlanMensajes').style.display = 'block';");
        return $oReturn;
    }

    if ($meses_prorroga <= 0) {
        $oReturn->assign('divPlanMensajes', 'innerHTML', plan_mensaje_alerta('Debe indicar los meses a prorrogar.'));
        $oReturn->script("document.getElementById('divPlanMensajes').style.display = 'block';");
        return $oReturn;
    }

    $sql_activo = "select act_val_comp, act_vres_act, act_clave_act, act_nom_act
                    from saeact
                    where act_cod_act = $activo_desde
                    and act_cod_empr = $empresa
                    and act_cod_sucu = $sucursal";
    $oIfx->Query($sql_activo);
    if ($oIfx->NumFilas() === 0) {
        $oReturn->assign('divPlanMensajes', 'innerHTML', plan_mensaje_alerta('Activo no encontrado para prórroga.'));
        $oReturn->script("document.getElementById('divPlanMensajes').style.display = 'block';");
        return $oReturn;
    }
    $oIfx->SiguienteRegistro();
    $valor_compra = floatval($oIfx->f('act_val_comp'));
    $valor_residual = floatval($oIfx->f('act_vres_act'));
    $clave_activo = $oIfx->f('act_clave_act');

    $sql_ultima = "select max(metd_has_fech) as ultima_fecha
                   from saemet
                   where metd_cod_empr = $empresa
                   and metd_cod_acti = $activo_desde";
    $ultima_fecha = consulta_string($sql_ultima, 'ultima_fecha', $oIfx, 0);
    if (empty($ultima_fecha)) {
        $oReturn->assign('divPlanMensajes', 'innerHTML', plan_mensaje_alerta('El activo no tiene plan para prorrogar.'));
        $oReturn->script("document.getElementById('divPlanMensajes').style.display = 'block';");
        return $oReturn;
    }

    $ultima_dt = DateTime::createFromFormat('Y-m-d', $ultima_fecha);
    $inicio_mes_actual = new DateTime(date('Y-m-01'));
    if ($ultima_dt >= $inicio_mes_actual) {
        $oReturn->assign('divPlanMensajes', 'innerHTML', plan_mensaje_alerta('El plan aún no termina. No se puede prorrogar.'));
        $oReturn->script("document.getElementById('divPlanMensajes').style.display = 'block';");
        return $oReturn;
    }

    if (!$tiene_estado) {
        $oReturn->assign('divPlanMensajes', 'innerHTML', plan_mensaje_alerta('La columna met_estado no existe en saemet. No se puede prorrogar sin estado del plan.'));
        $oReturn->script("document.getElementById('divPlanMensajes').style.display = 'block';");
        return $oReturn;
    }

    $sql_ejecutado = "select coalesce(sum(metd_val_metd), 0) as ejecutado
                      from saemet
                      where metd_cod_empr = $empresa
                      and metd_cod_acti = $activo_desde
                      and met_estado = 'E'";
    $ejecutado = floatval(consulta_string($sql_ejecutado, 'ejecutado', $oIfx, 0));

    $valor_neto = round($valor_compra - $valor_residual, 2);
    $pendiente = round($valor_neto - $ejecutado, 2);
    if ($pendiente <= 0) {
        $oReturn->assign('divPlanMensajes', 'innerHTML', plan_mensaje_alerta('No hay saldo pendiente para prorrogar.'));
        $oReturn->script("document.getElementById('divPlanMensajes').style.display = 'block';");
        return $oReturn;
    }

    $monto_mensual = round($pendiente / $meses_prorroga, 2);
    if ($monto_mensual <= 0) {
        $oReturn->assign('divPlanMensajes', 'innerHTML', plan_mensaje_alerta('La depreciación mensual es 0. Revise la prórroga.'));
        $oReturn->script("document.getElementById('divPlanMensajes').style.display = 'block';");
        return $oReturn;
    }

    $inicio_prorroga = (clone $ultima_dt)->modify('first day of next month');
    $porcentaje = round(1 / $meses_prorroga, 6);
    $acumulado = 0;
    for ($i = 0; $i < $meses_prorroga; $i++) {
        $periodo_dt = (clone $inicio_prorroga)->modify('+' . $i . ' months');
        $fecha_desde = $periodo_dt->format('Y-m-01');
        $fecha_hasta = $periodo_dt->format('Y-m-t');
        $dias = intval($periodo_dt->format('t'));

        if ($i === $meses_prorroga - 1) {
            $monto = round($pendiente - $acumulado, 2);
        } else {
            $monto = $monto_mensual;
        }
        $acumulado += $monto;

        $sql_insert = "insert into saemet
            (met_anio_met, metd_des_fech, metd_has_fech, metd_cod_empr, metd_cod_acti,
             act_cod_empr, act_cod_sucu, met_porc_met, metd_val_metd, met_num_dias,
             metd_cod_reva, met_estado)
            values
            (" . intval($periodo_dt->format('Y')) . ", '$fecha_desde', '$fecha_hasta', $empresa, $activo_desde,
             $empresa, $sucursal, $porcentaje, $monto, $dias, 0, 'P')";
        $oIfx->QueryT($sql_insert);
    }

    $mensaje = plan_mensaje_ok("Prórroga aplicada al activo {$clave_activo} por {$meses_prorroga} meses.");
    $oReturn->assign('divPlanMensajes', 'innerHTML', $mensaje);
    $oReturn->script("document.getElementById('divPlanMensajes').style.display = 'block';");
    $oReturn->script('listarPlan();');
    $oReturn->script('validarPlan();');
    return $oReturn;
}

function validarPlan($aForm = '')
{
    global $DSN_Ifx;

    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $oIfx = new Dbo();
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    $oReturn = new xajaxResponse();

    if (!plan_filtros_completos($aForm)) {
        $oReturn->assign('divPlanMensajes', 'innerHTML', '');
        $oReturn->script("document.getElementById('divPlanMensajes').style.display = 'none';");
        $oReturn->script("document.getElementById('btnProrrogarPlan').style.display = 'none';");
        return $oReturn;
    }

    $contexto = plan_obtener_contexto($aForm);
    $empresa = $contexto['empresa'];
    $sucursal = $contexto['sucursal'];
    $activo_desde = $contexto['activo_desde'];
    $activo_hasta = $contexto['activo_hasta'];
    $filtro = $contexto['filtro'];
    $tiene_estado = plan_tiene_met_estado($oIfx);

    $sql_activos = "select act_cod_act, act_val_comp, act_vres_act, act_clave_act, act_nom_act
                    from saeact
                    where act_cod_empr = $empresa
                    and act_cod_sucu = $sucursal
                    and act_ext_act = 1
                    $filtro
                    order by act_cod_act";

    $mensajes = [];
    $puede_prorrogar = false;

    if ($oIfx->Query($sql_activos)) {
        if ($oIfx->NumFilas() > 0) {
            while ($oIfx->SiguienteRegistro()) {
                $codigo_activo = $oIfx->f('act_cod_act');
                $clave_activo = $oIfx->f('act_clave_act');
                $nombre_activo = $oIfx->f('act_nom_act');
                $valor_neto = round(floatval($oIfx->f('act_val_comp')) - floatval($oIfx->f('act_vres_act')), 2);

                $sql_plan = "select count(*) as total
                            from saemet
                            where metd_cod_empr = $empresa
                            and metd_cod_acti = $codigo_activo";
                $plan_existe = intval(consulta_string($sql_plan, 'total', $oIfx, 0));
                if ($plan_existe === 0) {
                    $mensajes[] = "El activo {$clave_activo} - {$nombre_activo} no tiene plan generado.";
                    continue;
                }

                $sql_ceros = "select count(*) as total
                            from saemet
                            where metd_cod_empr = $empresa
                            and metd_cod_acti = $codigo_activo
                            and metd_val_metd = 0";
                $ceros = intval(consulta_string($sql_ceros, 'total', $oIfx, 0));
                if ($ceros > 0) {
                    $mensajes[] = "El activo {$clave_activo} tiene periodos con monto 0.";
                }

                if ($tiene_estado) {
                    $sql_ejecutado = "select count(*) as total
                                      from saemet
                                      where metd_cod_empr = $empresa
                                      and metd_cod_acti = $codigo_activo
                                      and met_estado = 'E'";
                    $ejecutados = intval(consulta_string($sql_ejecutado, 'total', $oIfx, 0));
                    if ($ejecutados > 0) {
                        $mensajes[] = "El activo {$clave_activo} tiene periodos ejecutados.";
                    }
                } else {
                    $mensajes[] = "La columna met_estado no existe en saemet; no se puede validar periodos ejecutados/anulados.";
                }

                $sql_total_plan = "select coalesce(sum(metd_val_metd), 0) as total_plan
                                   from saemet
                                   where metd_cod_empr = $empresa
                                   and metd_cod_acti = $codigo_activo";
                $total_plan = floatval(consulta_string($sql_total_plan, 'total_plan', $oIfx, 0));
                if (round($total_plan, 2) !== round($valor_neto, 2)) {
                    $mensajes[] = "El activo {$clave_activo} tiene inconsistencias: total plan {$total_plan} vs valor neto {$valor_neto}.";
                }

                if ($activo_desde === $activo_hasta && !empty($activo_desde)) {
                    $sql_ultima = "select max(metd_has_fech) as ultima_fecha
                                   from saemet
                                   where metd_cod_empr = $empresa
                                   and metd_cod_acti = $codigo_activo";
                    $ultima_fecha = consulta_string($sql_ultima, 'ultima_fecha', $oIfx, 0);
                    $ultima_dt = $ultima_fecha ? DateTime::createFromFormat('Y-m-d', $ultima_fecha) : null;
                    $inicio_mes_actual = new DateTime(date('Y-m-01'));
                    if ($ultima_dt && $ultima_dt < $inicio_mes_actual) {
                        $puede_prorrogar = true;
                    }
                }
            }
        }
    }

    if (empty($mensajes)) {
        $mensaje = plan_mensaje_ok('Validación del plan correcta.');
    } else {
        $mensaje = plan_mensaje_alerta('Validaciones encontradas:')
            . '<ul>' . implode('', array_map(function ($mensaje) {
                return '<li>' . htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8') . '</li>';
            }, $mensajes)) . '</ul>';
    }

    $oReturn->assign('divPlanMensajes', 'innerHTML', $mensaje);
    $oReturn->script("document.getElementById('divPlanMensajes').style.display = 'block';");
    if ($puede_prorrogar) {
        $oReturn->script("document.getElementById('btnProrrogarPlan').style.display = 'inline-block';");
    } else {
        $oReturn->script("document.getElementById('btnProrrogarPlan').style.display = 'none';");
    }
    return $oReturn;
}

/* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */
/* PROCESO DE REQUEST DE LAS FUNCIONES MEDIANTE AJAX NO MODIFICAR */
$xajax->processRequest();
/* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */
