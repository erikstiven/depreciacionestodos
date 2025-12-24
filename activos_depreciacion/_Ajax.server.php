<?php

require("_Ajax.comun.php"); // No modificar esta linea
/* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
  // S E R V I D O R   A J A X //
  :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */

function obtener_inicio_mes_depreciacion(DateTime $fecha)
{
    $inicio = new DateTime($fecha->format('Y-m-01'));
    if (intval($fecha->format('d')) !== 1) {
        $inicio->modify('+1 month');
    }
    return $inicio;
}

/* * ******************************************* */
/* FCA01 :: GENERA INGRESO TABLA PRESUPUESTO  */
/* * ******************************************* */

function f_filtro_sucursal($aForm, $data)
{
    //Definiciones
    global $DSN, $DSN_Ifx;
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $oCon = new Dbo();
    $oCon->DSN = $DSN;
    $oCon->Conectar();

    $oIfx = new Dbo();
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    $oReturn = new xajaxResponse();

    //variables formulario
    $empresa = $aForm['empresa'];

    // DATOS EMPRESA
    $sql = "select sucu_cod_sucu, sucu_nom_sucu
			from saesucu
			where sucu_cod_empr = '$empresa'			
			order by sucu_nom_sucu";
    //echo $sql; exit;
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

function f_filtro_anio($aForm, $data)
{
    //Definiciones
    global $DSN, $DSN_Ifx;
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $oCon = new Dbo();
    $oCon->DSN = $DSN;
    $oCon->Conectar();

    $oIfx = new Dbo();
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    return f_filtro_anio_rango($oIfx, $aForm, $data, 'anio', 'eliminar_lista_anio', 'anadir_elemento_anio');
}

function f_filtro_anio_desde($aForm, $data)
{
    global $DSN, $DSN_Ifx;
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $oIfx = new Dbo();
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    return f_filtro_anio_rango($oIfx, $aForm, $data, 'anio_desde', 'eliminar_lista_anio_desde', 'anadir_elemento_anio_desde');
}

function f_filtro_anio_hasta($aForm, $data)
{
    global $DSN, $DSN_Ifx;
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $oIfx = new Dbo();
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    return f_filtro_anio_rango($oIfx, $aForm, $data, 'anio_hasta', 'eliminar_lista_anio_hasta', 'anadir_elemento_anio_hasta');
}

function f_filtro_anio_rango($oIfx, $aForm, $data, $campo, $script_limpiar, $script_agregar)
{
    $oReturn = new xajaxResponse();
    $idempresa = $_SESSION['U_EMPRESA'];
    //variables formulario
    $empresa = $aForm['empresa'];
    if (empty($empresa)) {
        $empresa = $idempresa;
    }
    // DATOS EMPRESA
    $sql = "select ejer_fec_inil, date_part('year',ejer_fec_inil) as anio_i 
			from saeejer 
			where ejer_cod_empr = $empresa
			order by anio_i desc";
    $i = 1;
    if ($oIfx->Query($sql)) {
        $oReturn->script($script_limpiar . '();');
        if ($oIfx->NumFilas() > 0) {
            do {
                $oReturn->script(($script_agregar . '(' . $i++ . ',\'' . $oIfx->f('anio_i') . '\',\'' . $oIfx->f('anio_i') . '\')'));
            } while ($oIfx->SiguienteRegistro());
        }
    }
    $oReturn->assign($campo, 'value', $data);
    return $oReturn;
}

function f_filtro_activos_desde($aForm)
{
    //Definiciones
    global $DSN, $DSN_Ifx;
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $oCon = new Dbo();
    $oCon->DSN = $DSN;
    $oCon->Conectar();

    $oIfx = new Dbo();
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    $oReturn = new xajaxResponse();
    // variables de sesion
    $idempresa = $_SESSION['U_EMPRESA'];
    $idsucursal = $_SESSION['U_SUCURSAL'];
    //variables formulario
    $empresa = $aForm['empresa'];
    $sucursal = $aForm['sucursal'];
    $subgrupo = $aForm['cod_subgrupo'];
    if (empty($empresa)) {
        $empresa = $idempresa;
    }
    if (empty($sucursal)) {
        $sucursal = $idsucursal;
    }
    // DATOS DEL ACTIVO
    $sql = "select act_cod_act, act_nom_act, act_clave_act
			from saeact
			where act_cod_empr = '$empresa'
			and act_cod_sucu = '$sucursal'
			and sgac_cod_sgac  = '$subgrupo'
			order by act_cod_act";
    //echo $sql; exit;
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
    //Definiciones
    global $DSN, $DSN_Ifx;
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $oCon = new Dbo();
    $oCon->DSN = $DSN;
    $oCon->Conectar();

    $oIfx = new Dbo();
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    $oReturn = new xajaxResponse();

    // variables de sesion
    $idempresa = $_SESSION['U_EMPRESA'];
    $idsucursal = $_SESSION['U_SUCURSAL'];
    //variables formulario
    $empresa = $aForm['empresa'];
    $sucursal = $aForm['sucursal'];
    $subgrupo = $aForm['cod_subgrupo'];
    if (empty($empresa)) {
        $empresa = $idempresa;
    }
    if (empty($sucursal)) {
        $sucursal = $idsucursal;
    }
    // DATOS DEL ACTIVO
    $sql = "select act_cod_act, act_nom_act, act_clave_act
			from saeact
			where act_cod_empr = '$empresa'
			and act_cod_sucu = '$sucursal'
			and sgac_cod_sgac  = '$subgrupo'
			order by act_cod_act";
    //echo $sql; exit;
    $i = 1;
    if ($oIfx->Query($sql)) {
        $oReturn->script('eliminar_lista_activo_hasta();');
        if ($oIfx->NumFilas() > 0) {
            do {
                $oReturn->script(('anadir_elemento_activo_hasta(' . $i++ . ',\'' . $oIfx->f('act_cod_act') . '\', \'' . $oIfx->f('act_clave_act') . ' - ' . $oIfx->f('act_nom_act') . '\' )'));
            } while ($oIfx->SiguienteRegistro());
        }
    }
    //$oReturn->assign('cod_activo_hasta', 'value', $data);
    return $oReturn;
}

function f_filtro_mes($aForm, $data)
{
    //Definiciones
    global $DSN, $DSN_Ifx;
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $oCon = new Dbo();
    $oCon->DSN = $DSN;
    $oCon->Conectar();

    $oIfx = new Dbo();
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    return f_filtro_mes_rango($oIfx, $aForm, $data, 'anio', 'mes', 'eliminar_lista_mes', 'anadir_elemento_mes');
}

function f_filtro_mes_desde($aForm, $data)
{
    global $DSN, $DSN_Ifx;
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $oIfx = new Dbo();
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    return f_filtro_mes_rango($oIfx, $aForm, $data, 'anio_desde', 'mes_desde', 'eliminar_lista_mes_desde', 'anadir_elemento_mes_desde');
}

function f_filtro_mes_hasta($aForm, $data)
{
    global $DSN, $DSN_Ifx;
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $oIfx = new Dbo();
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    return f_filtro_mes_rango($oIfx, $aForm, $data, 'anio_hasta', 'mes_hasta', 'eliminar_lista_mes_hasta', 'anadir_elemento_mes_hasta');
}

function f_filtro_mes_rango($oIfx, $aForm, $data, $campo_anio, $campo_mes, $script_limpiar, $script_agregar)
{
    $oReturn = new xajaxResponse();

    //variables formulario
    $anio = $aForm[$campo_anio];
    $empresa = $_SESSION['U_EMPRESA'];
    if (empty($anio)) {
        $oReturn->script($script_limpiar . '();');
        $oReturn->assign($campo_mes, 'value', '');
        return $oReturn;
    }
    // DATOS DEL ACTIVO
    $sql = "select prdo_num_prdo, prdo_nom_prdo
			from saeprdo
			where prdo_cod_empr = '$empresa'
			and prdo_cod_ejer  = (select ejer_cod_ejer 
									from saeejer 
									where ejer_cod_empr = '$empresa' 
									and date_part('year',ejer_fec_inil) = $anio)
			order by prdo_num_prdo";
    $i = 1;
    if ($oIfx->Query($sql)) {
        $oReturn->script($script_limpiar . '();');
        if ($oIfx->NumFilas() > 0) {
            do {
                $oReturn->script(($script_agregar . '(' . $i++ . ',\'' . $oIfx->f('prdo_num_prdo') . '\', \'' . $oIfx->f('prdo_nom_prdo') . '\' )'));
            } while ($oIfx->SiguienteRegistro());
        }
    }

    $oReturn->assign($campo_mes, 'value', $data);

    return $oReturn;
}

function f_filtro_grupo($aForm, $data)
{
    //Definiciones
    global $DSN, $DSN_Ifx;

    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $oCon = new Dbo();
    $oCon->DSN = $DSN;
    $oCon->Conectar();

    $oIfx = new Dbo();
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    $oReturn = new xajaxResponse();
    $idempresa = $_SESSION['U_EMPRESA'];
    //variables formulario
    $empresa = $aForm['empresa'];
    $sucursal = $aForm['sucursal'];

    if (empty($empresa)) {
        $empresa = $idempresa;
    }
    if (empty($sucursal)) {
        $sucursal = $idsucursal;
    }

    // DATOS DEL GRUPO POR EMPRESA
    $sql = "select gact_cod_gact, gact_des_gact 
			 from saegact 
			 where gact_cod_empr = '$empresa'                                                                  
			 order by gact_des_gact";
    //echo $sql; exit;
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
    //Definiciones
    global $DSN, $DSN_Ifx;

    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $oCon = new Dbo();
    $oCon->DSN = $DSN;
    $oCon->Conectar();

    $oIfx = new Dbo();
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    $oReturn = new xajaxResponse();
    $idempresa = $_SESSION['U_EMPRESA'];
    //variables formulario	
    $empresa = $aForm['empresa'];
    $codigoGrupo = $aForm['cod_grupo'];
    if (empty($empresa)) {
        $empresa = $idempresa;
    }


    // DATOS DEL ACTIVO
    $sql = "select sgac_cod_sgac, sgac_des_sgac 
			 from saesgac where sgac_cod_empr = $empresa                                                                  
			 and gact_cod_gact = '$codigoGrupo'
			 order by sgac_des_sgac";
    //echo $sql; exit;
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

// PROCESAR DEPRECICION
function generar($aForm = '')
{

    //Definiciones
    global $DSN, $DSN_Ifx;

    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $oCon = new Dbo();
    $oCon->DSN = $DSN;
    $oCon->Conectar();

    $oIfx = new Dbo();
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    $oIfxA = new Dbo();
    $oIfxA->DSN = $DSN_Ifx;
    $oIfxA->Conectar();

    $oReturn = new xajaxResponse();

    //variables de sesion
    $array = ($_SESSION['ARRAY_PINTA']);
    $usuario_web = $_SESSION['U_ID'];
    $idempresa = $_SESSION['U_EMPRESA'];
    $idsucursal = $_SESSION['U_SUCURSAL'];

    //variables del formulario
    $empresa = $aForm['empresa'];
    $sucursal = $aForm['sucursal'];

    if (empty($empresa)) {
        $empresa = $idempresa;
    }
    if (empty($sucursal)) {
        $sucursal = $idsucursal;
    }

    //variables formulario
    $grupo           = $aForm['cod_grupo'];
    $subgrupo      = $aForm['cod_subgrupo'];
    $activo_desde = $aForm['cod_activo_desde'];
    $activo_hasta = $aForm['cod_activo_hasta'];
    $anio_desde = $aForm['anio_desde'];
    $mes_desde = $aForm['mes_desde'];
    $anio_hasta = $aForm['anio_hasta'];
    $mes_hasta = $aForm['mes_hasta'];
    $fechaServer = date("Y-m-d");

    if (empty($anio_desde) || empty($mes_desde) || empty($anio_hasta) || empty($mes_hasta)) {
        $oReturn->alert('Debe seleccionar Año/Mes Desde y Año/Mes Hasta.');
        return $oReturn;
    }
    $periodo_inicio = intval($anio_desde) * 100 + intval($mes_desde);
    $periodo_fin = intval($anio_hasta) * 100 + intval($mes_hasta);
    if ($periodo_inicio > $periodo_fin) {
        $oReturn->alert('El rango de fechas es inválido: Año/Mes Desde debe ser menor o igual a Año/Mes Hasta.');
        return $oReturn;
    }
    $fecha_inicio_rango = DateTime::createFromFormat('Y-n-j', $anio_desde . '-' . intval($mes_desde) . '-1');
    $fecha_fin_rango = DateTime::createFromFormat('Y-n-j', $anio_hasta . '-' . intval($mes_hasta) . '-1');
    if (!$fecha_inicio_rango || !$fecha_fin_rango) {
        $oReturn->alert('El rango de fechas es inválido. Verifique Año/Mes Desde y Hasta.');
        return $oReturn;
    }
    $fecha_fin_rango->modify('last day of this month');

    // ARMAR FILTROS
    $filtro = '';
    if (empty($grupo)) {
    } else {
        $filtro = " and saeact.gact_cod_gact = '" . $grupo . "'";
    }
    if (empty($subgrupo)) {
    } else {
        $filtro .= " and saeact.sgac_cod_sgac = '" . $subgrupo . "'";
    }
    if (empty($activo_desde) || empty($activo_hasta)) {
    } else {
        $filtro .= " and act_cod_act between " . $activo_desde . " and " . $activo_hasta;
    }
    //echo $filtro; exit;
    try {
        $arrayTipoDepre = [];
        // TIPO DE DEPRECIACION
        $sql_tipo = "select tdep_cod_tdep, tdep_tip_val 
						from saetdep";

        if ($oIfx->Query($sql_tipo)) {
            if ($oIfx->NumFilas() > 0) {
                unset($arrayTipoDepre);
                do {
                    $arrayTipoDepre[$oIfx->f('tdep_cod_tdep')] = $oIfx->f('tdep_tip_val');
                } while ($oIfx->SiguienteRegistro());
            }
        }

        $oIfx->Free();
        $sql = "  SELECT saeact.act_cod_act,   
						 saeact.act_vutil_act,   
						 saeact.act_val_comp,   
						 saeact.act_fcmp_act,   
						 saeact.tdep_cod_tdep,   
						 saeact.act_fdep_act,   
						 saeact.act_fiman_act,   
						 saeact.act_fcorr_act,   
                         saeact.act_clave_act,
                         saeact.act_nom_act,
						 saesgac.gact_cod_gact,   
						 saeact.sgac_cod_sgac,   
						 saeact.act_cod_sucu,   
						 saeact.act_vres_act  
					FROM saeact,   
						 saesgac  
				   WHERE ( saesgac.sgac_cod_sgac = saeact.sgac_cod_sgac ) and  
						 ( saesgac.sgac_cod_empr = saeact.act_cod_empr ) and  
						 ( saeact.act_clave_padr is null or saeact.act_clave_padr = '') and
						 ( saeact.act_cod_empr = $empresa ) AND  
						 --(saeact.act_ext_act <> 0 OR saeact.act_ext_act IS NULL  )    
						 saeact.act_ext_act = 1
						 $filtro";
        //echo $sql; exit;	
        if ($oIfxA->Query($sql)) {
            if ($oIfxA->NumFilas() > 0) {
                $audit_log = [];
                $total_evaluados = 0;
                $total_procesados = 0;
                $total_omitidos = 0;
                $alertas_pendientes = [];
                do {
                    // LEER DATOS AVTIVO
                    $codigo_activo        =    $oIfxA->f('act_cod_act');
                    $vida_util          =    $oIfxA->f('act_vutil_act');
                    $valor_compra        =    $oIfxA->f('act_val_comp');
                    $fecha_compra        =    $oIfxA->f('act_fcmp_act');
                    $tipo_depreciacion     =    $oIfxA->f('tdep_cod_tdep');
                    $fecha_depreciacion =   $oIfxA->f('act_fdep_act');
                    $fecha_fin_activo =   $oIfxA->f('act_fiman_act');
                    $cod_grupo          =    $oIfxA->f('gact_cod_gact');
                    $cod_subgrupo          =    $oIfxA->f('sgac_cod_sgac');
                    $valor_recidual        =     $oIfxA->f('act_vres_act');
                    $sucursal           =     $oIfxA->f('act_cod_sucu');
                    $clave_activo       =     $oIfxA->f('act_clave_act');
                    $nombre_activo      =     $oIfxA->f('act_nom_act');

                    $intervalo = $arrayTipoDepre[$tipo_depreciacion] ?? '';
                    if (empty($intervalo)) {
                        $intervalo = 'M';
                    }

                    $fecha_inicio_activo = $fecha_depreciacion;
                    if (empty($fecha_inicio_activo)) {
                        $fecha_inicio_activo = $fecha_compra;
                    }
                    $inicio_activo_dt = DateTime::createFromFormat('Y-m-d', $fecha_inicio_activo);
                    if (!$inicio_activo_dt) {
                        $inicio_activo_dt = clone $fecha_inicio_rango;
                    } else {
                        $inicio_activo_dt = obtener_inicio_mes_depreciacion($inicio_activo_dt);
                    }

                    $fin_activo_dt = null;
                    if (!empty($fecha_fin_activo)) {
                        $fin_activo_dt = DateTime::createFromFormat('Y-m-d', $fecha_fin_activo);
                    }

                    $vida_util_meses = intval($vida_util);
                    if ($intervalo === 'M') {
                        $vida_util_meses = intval($vida_util) * 12;
                    }
                    $estado_contable = 'OK';
                    $periodo_esperado = $periodo_fin;
                    $valor_neto = floatval($valor_compra) - floatval($valor_recidual);
                    $depreciacion_mensual = 0;
                    $depreciacion_valida = false;
                    // Calcular depreciación mensual en línea recta con validaciones obligatorias.
                    try {
                        if ($vida_util_meses <= 0) {
                            throw new Exception('Vida útil inválida para depreciación.');
                        }
                        $depreciacion_mensual = round($valor_neto / $vida_util_meses, 2);
                        if ($depreciacion_mensual <= 0) {
                            throw new Exception('Depreciación mensual inválida.');
                        }
                        $depreciacion_valida = true;
                    } catch (Exception $e) {
                        error_log("Depreciación masiva: activo {$codigo_activo} sin cálculo válido. " . $e->getMessage());
                    }
                    $fin_vida_util_dt = clone $inicio_activo_dt;
                    $fin_vida_util_dt->modify('+' . max($vida_util_meses - 1, 0) . ' months')->modify('last day of this month');
                    if ($fin_activo_dt && $fin_activo_dt < $fin_vida_util_dt) {
                        $fin_vida_util_dt = $fin_activo_dt;
                    }
                    $periodo_esperado = min($periodo_fin, intval($fin_vida_util_dt->format('Y')) * 100 + intval($fin_vida_util_dt->format('m')));
                    $sql_ultimo_periodo = "select max(cdep_ani_depr * 100 + cdep_mes_depr) as ultimo_periodo
										from saecdep
										where cdep_cod_acti = $codigo_activo
										and act_cod_empr = $empresa
										and act_cod_sucu = $sucursal";
                    $ultimo_periodo_real = intval(consulta_string($sql_ultimo_periodo, 'ultimo_periodo', $oIfx, 0));
                    if ($periodo_esperado > $ultimo_periodo_real) {
                        $estado_contable = 'PENDIENTE';
                        $ultimo_mostrado = $ultimo_periodo_real > 0
                            ? substr($ultimo_periodo_real, 4, 2) . '/' . substr($ultimo_periodo_real, 0, 4)
                            : '--/----';
                        $alertas_pendientes[] = 'El activo ' . $clave_activo . ' tiene meses pendientes de depreciar. '
                            . 'Último mes depreciado: ' . $ultimo_mostrado
                            . ' Mes esperado: ' . substr($periodo_esperado, 4, 2) . '/' . substr($periodo_esperado, 0, 4);
                    }

                    $periodo_actual = clone $fecha_inicio_rango;
                    while ($periodo_actual <= $fecha_fin_rango) {
                        $anio_iter = intval($periodo_actual->format('Y'));
                        $mes_iter = intval($periodo_actual->format('m'));
                        $mes_inicio = new DateTime($periodo_actual->format('Y-m-01'));
                        $estado = '';
                        $motivo = '';

                        $fin_activo_mes = $fin_activo_dt ? new DateTime($fin_activo_dt->format('Y-m-01')) : null;
                        $fin_vida_mes = new DateTime($fin_vida_util_dt->format('Y-m-01'));
                        $inicio_activo_mes = new DateTime($inicio_activo_dt->format('Y-m-01'));

                        if ($fin_activo_mes && $mes_inicio > $fin_activo_mes) {
                            $estado = 'OMITIDO';
                            $motivo = 'ACTIVO DE BAJA';
                        } elseif ($mes_inicio < $inicio_activo_mes || $mes_inicio > $fin_vida_mes) {
                            $estado = 'OMITIDO';
                            $motivo = 'FUERA DE VIDA UTIL';
                        } else {
                            $sql_existe = "select count(cdep_gas_depn) as existe
										from saecdep
										where cdep_cod_acti = $codigo_activo 
										and cdep_ani_depr = $anio_iter
										and cdep_mes_depr = $mes_iter";
                            $existe = consulta_string($sql_existe, 'existe', $oIfx, 0);
                            if ($existe > 0) {
                                $estado = 'OMITIDO';
                                $motivo = 'YA EXISTE';
                            } else {
                                $mes_anterior_dt = (clone $periodo_actual)->modify('-1 month');
                                $anio_prev = intval($mes_anterior_dt->format('Y'));
                                $mes_prev = intval($mes_anterior_dt->format('m'));
                                $fecha_hasta = $periodo_actual->format('Y-m-t');

                                $sql = "select metd_cod_acti, metd_val_metd 
					from saemet 
					where metd_has_fech = '$fecha_hasta'
					and metd_cod_empr   =  $empresa					
					";
                                $arrayValorDepre = [];
                                if ($oIfx->Query($sql)) {
                                    if ($oIfx->NumFilas() > 0) {
                                        do {
                                            $arrayValorDepre[$oIfx->f('metd_cod_acti')] = $oIfx->f('metd_val_metd');
                                        } while ($oIfx->SiguienteRegistro());
                                    }
                                }
                                $oIfx->Free();

                                $valor_mesual = $arrayValorDepre[$codigo_activo] ?? 0;

                                $sql_dep_acumulada = "SELECT (coalesce(cdep_dep_acum, 0) +  coalesce(cdep_gas_depn, 0)) as depr_acumulada
										from saecdep
										where cdep_cod_acti = $codigo_activo 
										and cdep_ani_depr = $anio_prev
										and cdep_mes_depr = $mes_prev";
                                $valor_acumulado = consulta_string($sql_dep_acumulada, 'depr_acumulada', $oIfx, 0);

                                if ($valor_acumulado == 0) {
                                    $valor_anterior = 0;
                                    $valor_acumulado = $valor_mesual;
                                } else {
                                    $valor_anterior = $valor_acumulado - $valor_mesual;
                                }

                                if (!$depreciacion_valida) {
                                    $estado = 'OMITIDO';
                                    $motivo = 'DEPRECIACION NO CALCULADA';
                                } elseif ($valor_acumulado >= $valor_neto || ($valor_acumulado + $depreciacion_mensual) > $valor_neto) {
                                    $estado = 'OMITIDO';
                                    $motivo = 'VALOR RESIDUAL ALCANZADO';
                                } else {
                                    $sql_cdep = "INSERT into saecdep (cdep_cod_acti, cdep_cod_tdep,     cdep_mes_depr, cdep_ani_depr, 
                                                     cdep_fec_depr, act_cod_empr,       act_cod_sucu,  cdep_dep_acum, 
                                                     cdep_gas_depn, cdep_est_cdep,      cdep_fec_cdep, cdep_val_rep1,
                                                     cdep_val_repr )
					                        values ($codigo_activo, '$tipo_depreciacion', $mes_iter,           $anio_iter, 
                                                    '$fecha_hasta',  $empresa,            $sucursal,      $valor_acumulado , 
                                                    $valor_mesual,      'PE',           '$fechaServer',    $valor_anterior,
                                                    $depreciacion_mensual)";
                                    $oIfx->QueryT($sql_cdep);
                                    $estado = 'PROCESADO';
                                    $motivo = 'DEPRECIADO';
                                }
                            }
                        }

                        $audit_log[] = [
                            'activo' => $clave_activo,
                            'nombre' => $nombre_activo,
                            'anio' => $anio_iter,
                            'mes' => $mes_iter,
                            'estado' => $estado,
                            'motivo' => $motivo,
                            'estado_contable' => $estado_contable,
                        ];
                        $total_evaluados++;
                        if ($estado === 'PROCESADO') {
                            $total_procesados++;
                        } else {
                            $total_omitidos++;
                        }

                        $periodo_actual->modify('+1 month');
                    }
                } while ($oIfxA->SiguienteRegistro());
                $tabla_detalle = '';
                foreach ($audit_log as $fila) {
                    $clase_estado = $fila['estado'] === 'PROCESADO' ? 'label label-success' : 'label label-warning';
                    $clase_contable = $fila['estado_contable'] === 'OK' ? 'label label-success' : 'label label-danger';
                    $tabla_detalle .= '<tr>'
                        . '<td>' . htmlspecialchars($fila['activo'], ENT_QUOTES, 'UTF-8') . '</td>'
                        . '<td>' . htmlspecialchars($fila['nombre'], ENT_QUOTES, 'UTF-8') . '</td>'
                        . '<td>' . $fila['anio'] . '</td>'
                        . '<td>' . str_pad($fila['mes'], 2, '0', STR_PAD_LEFT) . '</td>'
                        . '<td><span class="' . $clase_estado . '">' . $fila['estado'] . '</span></td>'
                        . '<td>' . htmlspecialchars($fila['motivo'], ENT_QUOTES, 'UTF-8') . '</td>'
                        . '<td><span class="' . $clase_contable . '">' . $fila['estado_contable'] . '</span></td>'
                        . '</tr>';
                }

                $alerta_html = '';
                if (!empty($alertas_pendientes)) {
                    $alerta_html = '<div class="alert alert-info" style="margin-top: 10px;">'
                        . '<strong>Alertas de control:</strong><ul><li>'
                        . implode('</li><li>', array_map('htmlspecialchars', $alertas_pendientes))
                        . '</li></ul></div>';
                }

                $resumen_html = '<div class="row">'
                    . '<div class="col-md-12">'
                    . '<p><strong>Meses evaluados:</strong> ' . $total_evaluados . '</p>'
                    . '<p><strong>Procesados:</strong> ' . $total_procesados . '</p>'
                    . '<p><strong>Omitidos:</strong> ' . $total_omitidos . '</p>'
                    . '</div>'
                    . '</div>'
                    . $alerta_html
                    . '<div class="table-responsive" style="max-height: 300px; overflow: auto;">'
                    . '<table class="table table-bordered table-condensed">'
                    . '<thead><tr>'
                    . '<th>Activo</th>'
                    . '<th>Nombre</th>'
                    . '<th>Año</th>'
                    . '<th>Mes</th>'
                    . '<th>Estado</th>'
                    . '<th>Motivo</th>'
                    . '<th>Estado contable</th>'
                    . '</tr></thead>'
                    . '<tbody>' . $tabla_detalle . '</tbody>'
                    . '</table>'
                    . '</div>';

                $oReturn->assign('divResumenDepreciacion', 'innerHTML', $resumen_html);
                $oReturn->script('mostrarResumenDepreciacion();');
            } else {
                $resumen_html = '<div class="row">'
                    . '<div class="col-md-12">'
                    . '<p><strong>Meses evaluados:</strong> 0</p>'
                    . '<p><strong>Procesados:</strong> 0</p>'
                    . '<p><strong>Omitidos:</strong> 0</p>'
                    . '</div>'
                    . '</div>'
                    . '<div class="table-responsive" style="max-height: 300px; overflow: auto;">'
                    . '<table class="table table-bordered table-condensed">'
                    . '<thead><tr>'
                    . '<th>Activo</th>'
                    . '<th>Nombre</th>'
                    . '<th>Año</th>'
                    . '<th>Mes</th>'
                    . '<th>Estado</th>'
                    . '<th>Motivo</th>'
                    . '<th>Estado contable</th>'
                    . '</tr></thead>'
                    . '<tbody></tbody>'
                    . '</table>'
                    . '</div>';

                $oReturn->assign('divResumenDepreciacion', 'innerHTML', $resumen_html);
                $oReturn->script('mostrarResumenDepreciacion();');
            }
        }
    } catch (Exception $e) {
        $oReturn->alert($e->getMessage());
    }
    return $oReturn;
}

/* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */
/* PROCESO DE REQUEST DE LAS FUNCIONES MEDIANTE AJAX NO MODIFICAR */
$xajax->processRequest();
/* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */
