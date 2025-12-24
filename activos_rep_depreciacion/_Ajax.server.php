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


function genera_cabecera_formulario($sAccion = 'nuevo', $aForm = '')
{
	//Definiciones
	global $DSN_Ifx, $DSN;

	if (session_status() !== PHP_SESSION_ACTIVE) {
		session_start();
	}

	$oIfx = new Dbo();
	$oIfx->DSN = $DSN_Ifx;
	$oIfx->Conectar();

	$oIfx1 = new Dbo();
	$oIfx1->DSN = $DSN_Ifx;
	$oIfx1->Conectar();

	$oCon = new Dbo();
	$oCon->DSN = $DSN;
	$oCon->Conectar();

	$fu = new Formulario;
	$fu->DSN = $DSN;

	$ifu = new Formulario;
	$ifu->DSN = $DSN_Ifx;

	$oReturn = new xajaxResponse();

	//variables de sesion
	$idempresa = $_SESSION['U_EMPRESA'];

	//variables del formulario
	$empresa = $aForm['empresa'];

	if (empty($empresa)) {
		$empresa = $idempresa;
	}


	switch ($sAccion) {
		case 'nuevo':
			$ifu->AgregarCampoListaSQL('empresa', 'Empresa|left', "select empr_cod_empr, empr_nom_empr
															from saeempr
															order by empr_nom_empr", true, 150, 150);
			$ifu->AgregarComandoAlCambiarValor('empresa', 'f_filtro_sucursal()', true, 150, 150);
			$ifu->AgregarCampoListaSQL('sucursal', 'Sucursal|left', '', false, 150, 150);
			$ifu->AgregarComandoAlCambiarValor('sucursal', 'f_filtro_anio_i(); f_filtro_anio_f(); f_filtro_grupo()', true, 150, 150);
			$ifu->AgregarCampoListaSQL('anio', 'A&ntildeo Desde|left', "", true, 150, 150);
			$ifu->AgregarComandoAlCambiarValor('anio', 'f_filtro_mes()', true, 150, 150);
			$ifu->AgregarCampoListaSQL('anio_fin', 'A&ntildeo Hasta|left', "", true, 150, 150);
			$ifu->AgregarComandoAlCambiarValor('anio_fin', 'f_filtro_mes_fin()', true, 150, 150);
			$ifu->AgregarCampoListaSQL('mes', 'Mes Desde|left', "", true, 150, 150);
			$ifu->AgregarCampoListaSQL('mes_fin', 'Mes Hasta|left', "", true, 150, 150);
			$ifu->AgregarCampoListaSQL('cod_grupo', 'Grupo|left', "", false, 150, 150);
			$ifu->AgregarComandoAlCambiarValor('cod_grupo', 'f_filtro_subgrupo()');
			$ifu->AgregarCampoListaSQL('cod_subgrupo', "Subgrupo|left", "", false, 150, 150);
			$ifu->AgregarComandoAlCambiarValor('cod_subgrupo', 'f_filtro_activos();f_filtro_activos1()');
			$ifu->AgregarCampoListaSQL('cod_activo_desde', 'Activo Desde|left', "", false, 150, 150);
			$ifu->AgregarCampoListaSQL('cod_activo_hasta', 'Activo Hasta|left', "", false, 150, 150);
	}

	$table_op .= '<div class="panel panel-primary" style="margin-bottom: 10px;">
			<div class="panel-heading text-center">REPORTE DEPRECIACION DE ACTIVOS</div>
			<div class="panel-body">
				<div class="row" style="margin-bottom: 10px;">
					<div class="col-md-12 text-center">
						<div class="btn-group">
							<div class="btn btn-primary btn-sm" onclick="genera_cabecera_formulario();" >
									<span class="glyphicon glyphicon-file"></span>
									Nuevo
							</div>
							<div class="btn btn-primary btn-sm" onclick="generar();" id = "generar">
									<span class="glyphicon glyphicon-search"></span>
									Consultar
							</div>
							<div class="btn btn-primary btn-sm" onclick="f_exportar();" id = "exportar">
									<span class="glyphicon glyphicon-cog"></span>
									Excel
							</div>
						</div>
					</div>
				</div>
				<div class="row msgFrm" style="margin-bottom: 15px;">
					<div class="col-md-12 text-center">Los campos con * son de ingreso obligatorio</div>
				</div>
				<div class="row">
					<div class="col-md-3 form-group">
						' . $ifu->ObjetoHtmlLBL('empresa') . '
						' . $ifu->ObjetoHtml('empresa') . '
					</div>
					<div class="col-md-3 form-group">
						' . $ifu->ObjetoHtmlLBL('sucursal') . '
						' . $ifu->ObjetoHtml('sucursal') . '
					</div>
					<div class="col-md-3 form-group">
						' . $ifu->ObjetoHtmlLBL('cod_grupo') . '
						' . $ifu->ObjetoHtml('cod_grupo') . '
					</div>
					<div class="col-md-3 form-group">
						' . $ifu->ObjetoHtmlLBL('cod_subgrupo') . '
						' . $ifu->ObjetoHtml('cod_subgrupo') . '
					</div>
				</div>
				<div class="row">
					<div class="col-md-3 form-group">
						' . $ifu->ObjetoHtmlLBL('anio') . '
						' . $ifu->ObjetoHtml('anio') . '
					</div>
					<div class="col-md-3 form-group">
						' . $ifu->ObjetoHtmlLBL('mes') . '
						' . $ifu->ObjetoHtml('mes') . '
					</div>
					<div class="col-md-3 form-group">
						' . $ifu->ObjetoHtmlLBL('anio_fin') . '
						' . $ifu->ObjetoHtml('anio_fin') . '
					</div>
					<div class="col-md-3 form-group">
						' . $ifu->ObjetoHtmlLBL('mes_fin') . '
						' . $ifu->ObjetoHtml('mes_fin') . '
					</div>
				</div>
				<div class="row">
					<div class="col-md-3 form-group">
						' . $ifu->ObjetoHtmlLBL('cod_activo_desde') . '
						' . $ifu->ObjetoHtml('cod_activo_desde') . '
					</div>
					<div class="col-md-3 form-group">
						' . $ifu->ObjetoHtmlLBL('cod_activo_hasta') . '
						' . $ifu->ObjetoHtml('cod_activo_hasta') . '
					</div>
				</div>
				<div class="row" style="margin-top: 10px;">
					<div class="col-md-12">
						<div class="checkbox" style="display: inline-block; margin-right: 15px;">
							<label for="tipo">
								<input type="checkbox" name="tipo" id="tipo" value="S">
								Activos Revalorizados
							</label>
						</div>
						<div class="checkbox" style="display: inline-block; margin-right: 15px;">
							<label for="detallado">
								<input type="checkbox" name="detallado" id="detallado" value="S">
								Detallado
							</label>
						</div>
						<div class="checkbox" style="display: inline-block; margin-right: 15px;">
							<label for="control_depreciacion">
								<input type="checkbox" name="control_depreciacion" id="control_depreciacion" value="S">
								Control de Depreciaci&oacute;n
							</label>
							<span class="glyphicon glyphicon-question-sign"
								title="Control de depreciación
Compara meses esperados vs histórico (saecdep).
No recalcula valores contables."></span>
						</div>
						<div class="checkbox" style="display: inline-block;">
							<label for="foto_mes_contable">
								<input type="checkbox" name="foto_mes_contable" id="foto_mes_contable" value="S">
								Foto del mes contable
							</label>
							<span class="glyphicon glyphicon-question-sign"
								title="Foto del mes contable
Muestra el valor contable del activo al cierre del mes seleccionado.
El cálculo:
• no depende del historial de depreciaciones
• no usa prorrateos
• incluye el mes inicial solo si la fecha de inicio es día 1"></span>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div id = "reporte"> </div>';
	$table_op .= '</fieldset>';
	$oReturn->assign("divFormularioReportesDepr", "innerHTML", $table_op);

	return $oReturn;
}

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

function f_filtro_anio_i($aForm, $data)
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
	if (empty($empresa)) {
		$empresa = $idempresa;
	}
	// DATOS EMPRESA
	$sql = "select ejer_fec_inil, DATE_PART('year', ejer_fec_inil) as anio_i 
			from saeejer 
			where ejer_cod_empr = $empresa
			order by anio_i desc";
	//echo $sql; exit;
	$i = 1;
	if ($oIfx->Query($sql)) {
		$oReturn->script('eliminar_lista_anio_i();');
		if ($oIfx->NumFilas() > 0) {
			do {
				$oReturn->script(('anadir_elemento_anio_i(' . $i++ . ',\'' . $oIfx->f('anio_i') . '\',\'' . $oIfx->f('anio_i') . '\')'));
			} while ($oIfx->SiguienteRegistro());
		}
	}
	$oReturn->assign('anio', 'value', $data);
	return $oReturn;
}

function f_filtro_anio_f($aForm, $data)
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
	if (empty($empresa)) {
		$empresa = $idempresa;
	}
	// DATOS EMPRESA
	$sql = "select ejer_fec_inil, DATE_PART('year', ejer_fec_inil) as anio 
			from saeejer 
			where ejer_cod_empr = $empresa
			order by anio desc";
	//echo $sql; exit;
	$i = 1;
	if ($oIfx->Query($sql)) {
		$oReturn->script('eliminar_lista_anio_f();');
		if ($oIfx->NumFilas() > 0) {
			do {
				$oReturn->script(('anadir_elemento_anio_f(' . $i++ . ',\'' . $oIfx->f('anio') . '\',\'' . $oIfx->f('anio') . '\')'));
			} while ($oIfx->SiguienteRegistro());
		}
	}
	$oReturn->assign('anio_fin', 'value', $data);
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

function f_filtro_subgrupo($aForm, $data)
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
			 from saesgac where sgac_cod_empr = '$empresa'                                                                  
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

	$oReturn->assign('cod_subgrupo', 'value', $data);

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

	$oReturn = new xajaxResponse();

	//variables formulario
	$idempresa =  $_SESSION['U_EMPRESA'];
	$anio 	   =  $aForm['anio'];
	$empresa   =  $aForm['empresa'];
	if (empty($empresa)) {
		$empresa = $idempresa;
	}
	//echo $anio; exit;
	// DATOS DEL ACTIVO
	$sql = "select prdo_num_prdo, prdo_nom_prdo
			from saeprdo
			where prdo_cod_empr = '$empresa'
			and prdo_cod_ejer  = (select ejer_cod_ejer 
									from saeejer 
									where ejer_cod_empr = '$empresa' 
									and DATE_PART('year', ejer_fec_inil) = $anio)
			order by prdo_num_prdo";
	$i = 1;
	if ($oIfx->Query($sql)) {
		$oReturn->script('eliminar_lista_mes();');
		if ($oIfx->NumFilas() > 0) {
			do {
				$oReturn->script(('anadir_elemento_mes(' . $i++ . ',\'' . $oIfx->f('prdo_num_prdo') . '\', \'' . $oIfx->f('prdo_nom_prdo') . '\' )'));
			} while ($oIfx->SiguienteRegistro());
		}
	}

	$oReturn->assign('mes', 'value', $data);

	return $oReturn;
}

function f_filtro_mes_fin($aForm, $data)
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
	$anio = $aForm['anio_fin'];
	$empresa = $_SESSION['U_EMPRESA'];
	// DATOS DEL ACTIVO
	$sql = "select prdo_num_prdo, prdo_nom_prdo
			from saeprdo
			where prdo_cod_empr = '$empresa'
			and prdo_cod_ejer  = (select ejer_cod_ejer 
									from saeejer 
									where ejer_cod_empr = '$empresa' 
									and DATE_PART('year', ejer_fec_inil) = $anio)
			order by prdo_num_prdo";
	//echo $sql; exit;
	$i = 1;
	if ($oIfx->Query($sql)) {
		$oReturn->script('eliminar_lista_mes_fin();');
		if ($oIfx->NumFilas() > 0) {
			do {
				$oReturn->script(('anadir_elemento_mes_fin(' . $i++ . ',\'' . $oIfx->f('prdo_num_prdo') . '\', \'' . $oIfx->f('prdo_nom_prdo') . '\' )'));
			} while ($oIfx->SiguienteRegistro());
		}
	}

	$oReturn->assign('mes_fin', 'value', $data);

	return $oReturn;
}

function f_filtro_activos($aForm, $data)
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
	$idempresa = $_SESSION['U_EMPRESA'];
	$idsucursal = $_SESSION['U_SUCURSAL'];
	$subgrupo = $aForm['cod_subgrupo'];
	$empresa = $aForm['empresa'];
	$sucursal = $aForm['sucursal'];

	if (empty($empresa)) {
		$empresa = $idempresa;
	}
	if (empty($sucursal)) {
		$sucursal = $idsucursal;
	}

	// DATOS DEL ACTIVO
	$sql = "select act_cod_act, act_nom_act
			from saeact
			where act_cod_empr = '$empresa'
			and act_cod_sucu = '$sucursal'
			and sgac_cod_sgac  = '$subgrupo'
			order by act_cod_act";
	//echo $sql; exit;
	$i = 1;
	if ($oIfx->Query($sql)) {
		$oReturn->script('eliminar_lista_activo();');
		if ($oIfx->NumFilas() > 0) {
			do {
				$oReturn->script(('anadir_elemento_activo(' . $i++ . ',\'' . $oIfx->f('act_cod_act') . '\', \'' . $oIfx->f('act_nom_act') . '\' )'));
			} while ($oIfx->SiguienteRegistro());
		}
	}
	$oReturn->assign('cod_activo_desde', 'value', $data);
	return $oReturn;
}

function f_filtro_activos1($aForm, $data)
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
	$subgrupo = $aForm['cod_subgrupo'];
	$empresa = $_SESSION['U_EMPRESA'];
	$sucursal = $_SESSION['U_SUCURSAL'];
	$empresa = $aForm['empresa'];
	$sucursal = $aForm['sucursal'];

	if (empty($empresa)) {
		$empresa = $idempresa;
	}
	if (empty($sucursal)) {
		$sucursal = $idsucursal;
	}

	// DATOS DEL ACTIVO
	$sql = "select act_cod_act, act_nom_act
			from saeact
			where act_cod_empr = '$empresa'
			and act_cod_sucu = '$sucursal'
			and sgac_cod_sgac  = '$subgrupo'
			order by act_cod_act";
	//echo $sql; exit;
	$i = 1;
	if ($oIfx->Query($sql)) {
		$oReturn->script('eliminar_lista_activo1();');
		if ($oIfx->NumFilas() > 0) {
			do {
				$oReturn->script(('anadir_elemento_activo1(' . $i++ . ',\'' . $oIfx->f('act_cod_act') . '\', \'' . $oIfx->f('act_nom_act') . '\' )'));
			} while ($oIfx->SiguienteRegistro());
		}
	}
	$oReturn->assign('cod_activo_hasta', 'value', $data);
	return $oReturn;
}

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
	$array 		 = ($_SESSION['ARRAY_PINTA']);
	$usuario_web = $_SESSION['U_ID'];
	$idempresa   = $_SESSION['U_EMPRESA'];
	$idsucursal  = $_SESSION['U_SUCURSAL'];
	$empresa     = $aForm['empresa'];
	$sucursal    = $aForm['sucursal'];

	if (empty($empresa)) {
		$empresa = $idempresa;
	}
	if (empty($sucursal)) {
		$sucursal = $idsucursal;
	}


	//variables formulario
	$anio 		  =	$aForm['anio'];
	$anio_fin 	  =	$aForm['anio_fin'];
	$mes 		  =	$aForm['mes'];
	$mes_fin 	  =	$aForm['mes_fin'];
	$grupo 		  = $aForm['cod_grupo'];
	$subgrupo	  = $aForm['cod_subgrupo'];
	$activo_desde = $aForm['cod_activo_desde'];
	$activo_hasta = $aForm['cod_activo_hasta'];
	$estado_reva  = $aForm['tipo'];

	$detallado	  = $aForm['detallado'];
	$control_depreciacion = $aForm['control_depreciacion'];
	$foto_mes_contable = $aForm['foto_mes_contable'];
	// $fecha_corte  = $aForm['fecha_corte'];
	// $fecha_corte  = fecha_informix_func($fecha_corte);
	// $fechaServer  = date("Y-m-d");

	// ARMAR FILTROS
	$filtro = '';
	if (empty($grupo)) {
	} else {
		$filtro = $filtro . " and saegact.gact_cod_gact = '" . $grupo . "'";
	}
	if (empty($subgrupo)) {
	} else {
		$filtro = $filtro . " and saesgac.sgac_cod_sgac = '" . $subgrupo . "'";
	}
	if (empty($activo_desde) && empty($activo_hasta)) {
	} else {
		$filtro = $filtro . " and saeact.act_cod_act between " . $activo_desde . " and " . $activo_hasta;
	}
	if (!empty($estado_reva)) {
		$filtro = $filtro . " and saeact.act_est_reva <> 'N'";
	} else {
		$filtro = $filtro . " and saeact.act_est_reva = 'N'";
	}


	//$oReturn->alert($filtro);
	try {
		$oIfx->QueryT('BEGIN');

		$ctrl_reg = 0;



	unset($_SESSION['ACT_REP_DEPR']);
	$mostrar_aviso_mes = false;
	$aviso_mes_html = '';
	$mes_header = $foto_mes_contable == 'S'
		? '<span>Mes contable</span>'
		: '<span title="Este valor corresponde al último mes con depreciación registrada, no al filtro Mes Hasta.">Último mes depreciado</span>';
	$mes_badge = $foto_mes_contable == 'S'
		? '<span class="badge badge-info">Mes contable</span>'
		: '<span class="badge badge-info">Mes real depreciado</span>';
	$fuente_html = $foto_mes_contable == 'S'
		? '<div class="alert alert-info"><strong>Fuente:</strong> cálculo contable desde <em>saeact</em> a la fecha de corte seleccionada (no usa histórico).</div>'
		: '<div class="alert alert-warning"><strong>Fuente:</strong> histórico mensual registrado en <em>saecdep</em>.</div>';
	$html = '';
	$html .= $fuente_html;
	$html .= '<table class="table table-striped table-hover " id="tablaReporteDepreciacion" style="width: 100%; margin-bottom: 0px;">
							<tr class="msgFrm">
								<td class="bg-primary text-center"><h5> Clave </h5></td>
								<td class="bg-primary text-center"><h5> Nombre </h5></td>
								<td class="bg-primary text-center"><h5> F. Calculo </h5></td>
								<td class="bg-primary text-center"><h5> Vida Util </h5></td>
								<td class="bg-primary text-center"><h5> Anio </h5></td>
								<td class="bg-primary text-center"><h5>' . $mes_header . '</h5></td>
								<td class="bg-primary text-center"><h5> Valor Compra </h5> </td>
								<td class="bg-primary text-center"><h5> Valor Residual </h5></td>
								<td class="bg-primary text-center"><h5> Valor Neto </h5></td>
								<td class="bg-primary text-center"><h5> Dep. Anterior </h5></td>
								<td class="bg-primary text-center"><h5> Gasto Depr. </h5></td>
								<td class="bg-primary text-center"><h5> Dep. Acum. </h5></td>
								<td class="bg-primary text-center"><h5> Valor por Depr. </h5></td>
							</tr> ';
		// CAVECERA TABLA

		if ($control_depreciacion == 'S') {
			$periodo_fin_form = intval($anio_fin) * 100 + intval($mes_fin);
			$periodo_fin_dt = DateTime::createFromFormat('Y-n-j', $anio_fin . '-' . intval($mes_fin) . '-1');
			if (!$periodo_fin_dt) {
				$oReturn->alert('Rango de fechas inválido. Verifique Año/Mes Hasta.');
				return $oReturn;
			}

			$html = '<table class="table table-striped table-hover " style="width: 100%; margin-bottom: 0px;">
							<tr class="msgFrm">
								<td class="bg-primary text-center"><h5> Activo </h5></td>
								<td class="bg-primary text-center"><h5> A&ntilde;o </h5></td>
								<td class="bg-primary text-center"><h5> Mes </h5></td>
								<td class="bg-primary text-center"><h5> Estado </h5></td>
								<td class="bg-primary text-center"><h5> Motivo </h5></td>
								<td class="bg-primary text-center"><h5> Estado contable </h5></td>
							</tr> ';

			$sql_control = " SELECT saeact.act_cod_act,
					 saeact.act_clave_act,
					 saeact.act_nom_act,
					 saeact.act_fcmp_act,
					 saeact.act_fdep_act,
					 saeact.act_vutil_act,
					 saeact.act_fiman_act,
					 saeact.act_cod_sucu,
					 (select max(c.cdep_ani_depr * 100 + c.cdep_mes_depr)
					  from saecdep c
					  where c.cdep_cod_acti = saeact.act_cod_act
					  and c.act_cod_empr = saeact.act_cod_empr
					  and c.act_cod_sucu = saeact.act_cod_sucu) as periodo_ultimo
					FROM saeact,
						 saesgac,
						 saegact
				   WHERE ( saesgac.sgac_cod_sgac = saeact.sgac_cod_sgac ) and  
						 ( saesgac.sgac_cod_empr = saeact.act_cod_empr ) and  
						 ( saegact.gact_cod_gact = saesgac.gact_cod_gact ) and
						 ( saegact.gact_cod_empr = saesgac.sgac_cod_empr ) and
						 ( saeact.act_clave_padr is null or saeact.act_clave_padr = '') and
						 ( saeact.act_cod_empr = $empresa ) AND  
						 saeact.act_ext_act = 1
						 $filtro
					ORDER BY saeact.act_nom_act ";

			if (!$oIfx->Query($sql_control)) {
				throw new Exception('Error al generar el reporte de control de depreciación.');
			}
			if ($oIfx->NumFilas() > 0) {
				do {
					$clave = $oIfx->f('act_clave_act');
					$nombre = $oIfx->f('act_nom_act');
					$fecha_compra = $oIfx->f('act_fcmp_act');
					$fecha_depreciacion = $oIfx->f('act_fdep_act');
					$vida_util = intval($oIfx->f('act_vutil_act'));
					$periodo_ultimo = intval($oIfx->f('periodo_ultimo'));

					$fecha_inicio_activo = $fecha_depreciacion;
					if (empty($fecha_inicio_activo)) {
						$fecha_inicio_activo = $fecha_compra;
					}
					$inicio_activo_dt = DateTime::createFromFormat('Y-m-d', $fecha_inicio_activo);
					if (!$inicio_activo_dt) {
						$inicio_activo_dt = clone $periodo_fin_dt;
					} else {
						$inicio_activo_dt = obtener_inicio_mes_depreciacion($inicio_activo_dt);
					}

					$vida_util_meses = $vida_util * 12;
					$fin_vida_util_dt = clone $inicio_activo_dt;
					$fin_vida_util_dt->modify('+' . max($vida_util_meses - 1, 0) . ' months')->modify('last day of this month');
					$periodo_fin_vida = intval($fin_vida_util_dt->format('Y')) * 100 + intval($fin_vida_util_dt->format('m'));
					$periodo_esperado = min($periodo_fin_form, $periodo_fin_vida);

					if ($periodo_fin_form > $periodo_fin_vida) {
						$estado_contable = 'FUERA DE VIDA ÚTIL';
						$clase_contable = 'label label-warning';
					} else {
						$estado_contable = ($periodo_ultimo >= $periodo_esperado && $periodo_esperado > 0) ? 'OK' : 'PENDIENTE';
						$clase_contable = $estado_contable === 'OK' ? 'label label-success' : 'label label-danger';
					}

					$periodo_inicio_activo = intval($inicio_activo_dt->format('Y')) * 100 + intval($inicio_activo_dt->format('m'));
					$sql_periodos = "select cdep_ani_depr, cdep_mes_depr
						from saecdep
						where cdep_cod_acti = " . intval($oIfx->f('act_cod_act')) . "
						and act_cod_empr = $empresa
						and act_cod_sucu = " . intval($oIfx->f('act_cod_sucu')) . "
						and ((cdep_ani_depr * 100) + cdep_mes_depr) between $periodo_inicio_activo and $periodo_fin_form";
					$meses_depreciados = [];
					if ($oIfxA->Query($sql_periodos)) {
						if ($oIfxA->NumFilas() > 0) {
							do {
								$periodo_reg = intval($oIfxA->f('cdep_ani_depr')) * 100 + intval($oIfxA->f('cdep_mes_depr'));
								$meses_depreciados[$periodo_reg] = true;
							} while ($oIfxA->SiguienteRegistro());
						}
					}
					$oIfxA->Free();

					$periodo_iter = clone $inicio_activo_dt;
					while ($periodo_iter <= $periodo_fin_dt) {
						$anio_iter = intval($periodo_iter->format('Y'));
						$mes_iter = intval($periodo_iter->format('m'));
						$periodo_iter_num = ($anio_iter * 100) + $mes_iter;
						if ($periodo_iter_num > $periodo_fin_form) {
							break;
						}
						$motivo = '';
						$estado = 'PENDIENTE';
						if ($periodo_iter_num > $periodo_fin_vida) {
							$motivo = 'FUERA DE VIDA ÚTIL';
							$estado = 'FUERA DE VIDA ÚTIL';
						} elseif (!isset($meses_depreciados[$periodo_iter_num])) {
							$motivo = 'MES NO DEPRECIADO';
						}

						if (!empty($motivo)) {
							$ctrl_reg++;
							$html .= '<tr>'
								. '<td>' . htmlspecialchars($clave, ENT_QUOTES, 'UTF-8') . ' - ' . htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') . '</td>'
								. '<td align="right">' . $anio_iter . '</td>'
								. '<td align="right">' . str_pad($mes_iter, 2, '0', STR_PAD_LEFT) . '</td>'
								. '<td>' . $estado . '</td>'
								. '<td>' . $motivo . '</td>'
								. '<td><span class="' . $clase_contable . '">' . $estado_contable . '</span></td>'
								. '</tr>';
						}

						$periodo_iter->modify('+1 month');
					}
				} while ($oIfx->SiguienteRegistro());
			}
			$oIfx->Free();
			$html .= '</table>';

			if ($ctrl_reg != 0) {
				$_SESSION['ACT_REP_DEPR'] = $html;
			} else {
				$html = '<div style="font-size:14px;" ><b>..Sin Datos..<b/></div>';
			}

			$oReturn->assign("reporte", "innerHTML", $html);
			$oReturn->alert('Proceso Terminado con Exito');
			$oIfx->QueryT('COMMIT WORK;');
			return $oReturn;
		}

		if ($foto_mes_contable == 'S') {
			$fecha_corte_dt = DateTime::createFromFormat('Y-n-j', $anio_fin . '-' . intval($mes_fin) . '-1');
			if (!$fecha_corte_dt) {
				$oReturn->alert('Rango de fechas inválido. Verifique Año/Mes Hasta.');
				return $oReturn;
			}
			$fecha_corte_dt->modify('last day of this month');
			$fecha_corte = $fecha_corte_dt->format('Y-m-d');

			$sql = " WITH params AS (
						SELECT DATE '$fecha_corte' AS fecha_corte
					),
					base AS (
						SELECT
							saeact.act_cod_act,
							saeact.act_clave_act,
							saeact.act_nom_act,
							saeact.act_fdep_act,
							saeact.act_vutil_act,
							saeact.act_val_comp,
							saeact.act_vres_act,
							CASE
								WHEN EXTRACT(DAY FROM saeact.act_fdep_act) = 1
									THEN date_trunc('month', saeact.act_fdep_act)
								ELSE date_trunc('month', saeact.act_fdep_act) + interval '1 month'
							END AS inicio_mes,
							saegact.gact_des_gact,
							saesgac.sgac_des_sgac
						FROM saeact
						JOIN saesgac
						  ON saesgac.sgac_cod_sgac = saeact.sgac_cod_sgac
						 AND saesgac.sgac_cod_empr = saeact.act_cod_empr
						JOIN saegact
						  ON saegact.gact_cod_gact = saesgac.gact_cod_gact
						 AND saegact.gact_cod_empr = saesgac.sgac_cod_empr
						WHERE saeact.act_cod_empr = $empresa
						  AND ( ( (COALESCE(DATE_PART('year', act_fiman_act ),3000))*100+COALESCE(DATE_PART('month',act_fiman_act),13)   )  > ($anio_fin *100 + $mes_fin)  )
						  AND ( DATE_PART('year', act_fcmp_act) < $anio_fin OR ( DATE_PART('year', act_fcmp_act) = $anio_fin AND DATE_PART('month',act_fcmp_act) <= $mes_fin))
						  $filtro
					),
					calculo AS (
						SELECT
							base.*,
							(base.act_val_comp - base.act_vres_act) AS valor_neto,
							(base.act_vutil_act * 12) AS vida_util_meses,
							(base.act_val_comp - base.act_vres_act)
								/ (base.act_vutil_act * 12) AS depreciacion_mensual,
							(
								(EXTRACT(YEAR FROM p.fecha_corte) - EXTRACT(YEAR FROM base.inicio_mes)) * 12
								+ (EXTRACT(MONTH FROM p.fecha_corte) - EXTRACT(MONTH FROM base.inicio_mes))
								+ 1
							) AS meses_base
						FROM base
						CROSS JOIN params p
					)
					SELECT
						act_clave_act,
						act_nom_act,
						act_fdep_act,
						act_vutil_act,
						ROUND(act_val_comp, 2) AS valor_compra,
						ROUND(act_vres_act, 2) AS valor_residual,
						ROUND(valor_neto, 2) AS valor_neto,
						ROUND(depreciacion_mensual, 2) AS gasto_depreciacion,
						ROUND(depreciacion_mensual * LEAST(GREATEST(meses_base, 0), vida_util_meses), 2) AS dep_acumulada,
						ROUND(
							CASE
								WHEN LEAST(GREATEST(meses_base, 0), vida_util_meses) = 0 THEN 0
								ELSE (depreciacion_mensual * LEAST(GREATEST(meses_base, 0), vida_util_meses)) - depreciacion_mensual
							END,
							2
						) AS dep_anterior,
						ROUND(act_val_comp - (depreciacion_mensual * LEAST(GREATEST(meses_base, 0), vida_util_meses)), 2) AS valor_por_depreciar,
						gact_des_gact,
						sgac_des_sgac
					FROM calculo
					ORDER BY gact_des_gact, sgac_des_sgac, act_nom_act ";

			if ($oIfx->Query($sql)) {
				if ($oIfx->NumFilas() > 0) {
					$i = 1;
					$ctrl_reg++;

					do {
						$clave  	   = $oIfx->f('act_clave_act');
						$nombre 	   = $oIfx->f('act_nom_act');
						$fechaDepre    = $oIfx->f('act_fdep_act');
						$vidaUtil      = $oIfx->f('act_vutil_act');
						$anio 		   = $anio_fin;
						$mes 		   = $mes_fin;
						$valorCompra   = $oIfx->f('valor_compra');
						$valorResidu   = $oIfx->f('valor_residual');
						$grupo  	   = $oIfx->f('gact_des_gact');
						$subgrupo 	   = $oIfx->f('sgac_des_sgac');
						$gastoDepr     = $oIfx->f('gasto_depreciacion');
						$deprAcumulada = $oIfx->f('dep_acumulada');
						$deprAnterior  = $oIfx->f('dep_anterior');

						$valorNeto     = $oIfx->f('valor_neto');
						$valorPorDepr  = $oIfx->f('valor_por_depreciar');

						if ($i < 2) {
							$html .= '<tr>
										<td class="bg-info" colspan="13" style = "color:blue">' . $grupo . ' </td> 										
									</tr>
									<tr>										
										<td class="bg-info" colspan="13">&nbsp;&nbsp;&nbsp;&nbsp;' . $subgrupo . ' </td> 
									</tr>';
							$html .= '<tr>
										<td>' . $clave . ' </td> 
										<td>' . $nombre . ' </td> 
										<td>' . $fechaDepre . ' </td>
										<td align = right>' . $vidaUtil . ' </td>
										<td align = right>' . $anio . ' </td>
										<td align = right>' . $mes . ' ' . $mes_badge . '</td>
										<td align = right>' . number_format($valorCompra, 2, '.', ',') . ' </td>
										<td align = right>' . number_format($valorResidu, 2, '.', ',') . ' </td>
										<td align = right>' . number_format($valorNeto, 2, '.', ',') . ' </td>
										<td align = right>' . number_format($deprAnterior, 2, '.', ',') . ' </td>
										<td align = right>' . number_format($gastoDepr, 2, '.', ',') . ' </td>
										<td align = right>' . number_format($deprAcumulada, 2, '.', ',') . ' </td>
										<td align = right>' . number_format($valorPorDepr, 2, '.', ',') . ' </td>
									</tr>';
							$totalValorCompra     =  $valorCompra;
							$totalValorResidu     =  $valorResidu;
							$totalValorNeto       =  $valorNeto;
							$totalDeprAnterior    =  $deprAnterior;
							$totalGastoDepr       =  $gastoDepr;
							$totalDeprAcumulada   =  $deprAcumulada;
							$totalValorPorDepr    =  $valorPorDepr;
						} else {
							if ($grupo == $grupoAnt) {
								$totalValorCompra   = $totalValorCompra   + $valorCompra;
								$totalValorResidu   = $totalValorResidu   + $valorResidu;
								$totalValorNeto     = $totalValorNeto     + $valorNeto;
								$totalDeprAnterior  = $totalDeprAnterior  + $deprAnterior;
								$totalGastoDepr     = $totalGastoDepr     + $gastoDepr;
								$totalDeprAcumulada = $totalDeprAcumulada + $deprAcumulada;
								$totalValorPorDepr  = $totalValorPorDepr  + $valorPorDepr;
								if ($subgrupo == $subgrupoAnt) {
									$html .= '<tr>
												<td>' . $clave . ' </td> 
												<td>' . $nombre . ' </td> 
												<td>' . $fechaDepre . ' </td>
												<td align = right>' . $vidaUtil . ' </td>
												<td align = right>' . $anio . ' </td>
												<td align = right>' . $mes . ' ' . $mes_badge . '</td>
												<td align = right>' . number_format($valorCompra, 2, '.', ',') . ' </td>
												<td align = right>' . number_format($valorResidu, 2, '.', ',') . ' </td>
												<td align = right>' . number_format($valorNeto, 2, '.', ',') . ' </td>
												<td align = right>' . number_format($deprAnterior, 2, '.', ',') . ' </td>
												<td align = right>' . number_format($gastoDepr, 2, '.', ',') . ' </td>
												<td align = right>' . number_format($deprAcumulada, 2, '.', ',') . ' </td>
												<td align = right>' . number_format($valorPorDepr, 2, '.', ',') . ' </td>
											</tr>';
								} else {
									$html .= '<tr>																						
												<td class="bg-info" colspan="13">&nbsp;&nbsp;&nbsp;&nbsp;' . $subgrupo . ' </td>
											</tr>
											<tr>
												<td>' . $clave . ' </td> 
												<td>' . $nombre . ' </td> 
												<td>' . $fechaDepre . ' </td>
												<td align = right>' . $vidaUtil . ' </td>
												<td align = right>' . $anio . ' </td>
												<td align = right>' . $mes . ' ' . $mes_badge . '</td>
												<td align = right>' . number_format($valorCompra, 2, '.', ',') . ' </td>
												<td align = right>' . number_format($valorResidu, 2, '.', ',') . ' </td>
												<td align = right>' . number_format($valorNeto, 2, '.', ',') . ' </td>
												<td align = right>' . number_format($deprAnterior, 2, '.', ',') . ' </td>
												<td align = right>' . number_format($gastoDepr, 2, '.', ',') . ' </td>
												<td align = right>' . number_format($deprAcumulada, 2, '.', ',') . ' </td>
												<td align = right>' . number_format($valorPorDepr, 2, '.', ',') . ' </td>
											</tr>';
								}
							} else {
								$html .= '<tr> <td colspan="6" style = "color:red">' . "TOTAL GRUPO" . '</td>   									
											<td align = right style = "color:red">' . number_format($totalValorCompra, 2, '.', ',') . ' </td> 
											<td align = right style = "color:red">' . number_format($totalValorResidu, 2, '.', ',') . ' </td> 
											<td align = right style = "color:red">' . number_format($totalValorNeto, 2, '.', ',') . ' </td> 
											<td align = right style = "color:red">' . number_format($totalDeprAnterior, 2, '.', ',') . ' </td> 
											<td align = right style = "color:red">' . number_format($totalGastoDepr, 2, '.', ',') . ' </td> 
											<td align = right style = "color:red">' . number_format($totalDeprAcumulada, 2, '.', ',') . ' </td> 
											<td align = right style = "color:red">' . number_format($totalValorPorDepr, 2, '.', ',') . ' </td> 
										</tr>
										<tr>										
											<td class="bg-info" colspan="13" style = "color:blue">' . $grupo . ' </td>
										</tr>
										<tr>										
											<td class="bg-info" colspan="13">&nbsp;&nbsp;&nbsp;&nbsp;' . $subgrupo . ' </td>
										</tr>
										<tr>
											<td>' . $clave . ' </td> 
											<td>' . $nombre . ' </td> 
											<td>' . $fechaDepre . ' </td>
											<td align = right>' . $vidaUtil . ' </td>
											<td align = right>' . $anio . ' </td>
											<td align = right>' . $mes . ' ' . $mes_badge . '</td>
											<td align = right>' . number_format($valorCompra, 2, '.', ',') . ' </td>
											<td align = right>' . number_format($valorResidu, 2, '.', ',') . ' </td>
											<td align = right>' . number_format($valorNeto, 2, '.', ',') . ' </td>
											<td align = right>' . number_format($deprAnterior, 2, '.', ',') . ' </td>
											<td align = right>' . number_format($gastoDepr, 2, '.', ',') . ' </td>
											<td align = right>' . number_format($deprAcumulada, 2, '.', ',') . ' </td>
											<td align = right>' . number_format($valorPorDepr, 2, '.', ',') . ' </td>
										</tr>';
								// GUARDAR TOTALES GENERALES
								$sumaValorCompra	  = $sumaValorCompra   + $totalValorCompra;
								$sumaValorResidu	  = $sumaValorResidu   + $totalValorResidu;
								$sumaValorNeto  	  = $sumaValorNeto     + $totalValorNeto;
								$sumaDeprAnterior	  = $sumaDeprAnterior  + $totalDeprAnterior;
								$sumaGastoDepr	      = $sumaGastoDepr     + $totalGastoDepr;
								$sumaDeprAcumulada	  = $sumaDeprAcumulada + $totalDeprAcumulada;
								$sumaValorPorDepr	  = $sumaValorPorDepr  + $totalValorPorDepr;
								// INICIAR TOTALES POR GRUPO
								$totalValorCompra     =  $valorCompra;
								$totalValorResidu     =  $valorResidu;
								$totalValorNeto       =  $valorNeto;
								$totalDeprAnterior    =  $deprAnterior;
								$totalGastoDepr       =  $gastoDepr;
								$totalDeprAcumulada   =  $deprAcumulada;
								$totalValorPorDepr    =  $valorPorDepr;
							}
						}
						$grupoAnt	 = $grupo;
						$subgrupoAnt = $subgrupo;
						$i++;
					} while ($oIfx->SiguienteRegistro());
					// ULTIMA FILA POR GRUPOS
					$html .= '<tr> <td colspan="6" style = "color:red">' . "TOTAL GRUPO" . '</td>   									
								<td align = right style = "color:red">' . number_format($totalValorCompra, 2, '.', ',') . ' </td> 
								<td align = right style = "color:red">' . number_format($totalValorResidu, 2, '.', ',') . ' </td> 
								<td align = right style = "color:red">' . number_format($totalValorNeto, 2, '.', ',') . ' </td> 
								<td align = right style = "color:red">' . number_format($totalDeprAnterior, 2, '.', ',') . ' </td> 
								<td align = right style = "color:red">' . number_format($totalGastoDepr, 2, '.', ',') . ' </td> 
								<td align = right style = "color:red">' . number_format($totalDeprAcumulada, 2, '.', ',') . ' </td> 
								<td align = right style = "color:red">' . number_format($totalValorPorDepr, 2, '.', ',') . ' </td> 
							</tr>';
					// ULTIMA FILA TOTALES	
					$sumaValorCompra	  = $sumaValorCompra   + $totalValorCompra;
					$sumaValorResidu	  = $sumaValorResidu   + $totalValorResidu;
					$sumaValorNeto  	  = $sumaValorNeto     + $totalValorNeto;
					$sumaDeprAnterior	  = $sumaDeprAnterior  + $totalDeprAnterior;
					$sumaGastoDepr	      = $sumaGastoDepr     + $totalGastoDepr;
					$sumaDeprAcumulada	  = $sumaDeprAcumulada + $totalDeprAcumulada;
					$sumaValorPorDepr	  = $sumaValorPorDepr  + $totalValorPorDepr;
					$html .= '<tr> <td colspan="6" style = "color:red">' . "TOTALES" . '</td>   									
								<td align = right style = "color:red">' . number_format($sumaValorCompra, 2, '.', ',') . ' </td> 
								<td align = right style = "color:red">' . number_format($sumaValorResidu, 2, '.', ',') . ' </td> 
								<td align = right style = "color:red">' . number_format($sumaValorNeto, 2, '.', ',') . ' </td> 
								<td align = right style = "color:red">' . number_format($sumaDeprAnterior, 2, '.', ',') . ' </td> 
								<td align = right style = "color:red">' . number_format($sumaGastoDepr, 2, '.', ',') . ' </td> 
								<td align = right style = "color:red">' . number_format($sumaDeprAcumulada, 2, '.', ',') . ' </td> 
								<td align = right style = "color:red">' . number_format($sumaValorPorDepr, 2, '.', ',') . ' </td> 
							</tr>';
				}
			}
			$oIfx->Free();
		} elseif ($detallado == 'S') {
			$max_mes_encontrado = 0;
			$periodo_filtro = ($anio_fin * 100) + $mes_fin;
			$periodo_inicio = DateTime::createFromFormat('Y-n-j', $anio . '-' . intval($mes) . '-1');
			$periodo_fin = DateTime::createFromFormat('Y-n-j', $anio_fin . '-' . intval($mes_fin) . '-1');
			if (!$periodo_inicio || !$periodo_fin) {
				$oReturn->alert('Rango de fechas inválido.');
				return $oReturn;
			}
			$periodo_fin->modify('last day of this month');

			$periodo_actual = clone $periodo_inicio;
			while ($periodo_actual <= $periodo_fin) {
				$anio_iter = intval($periodo_actual->format('Y'));
				$mes_iter = intval($periodo_actual->format('m'));


				// ULTIMA FILA TOTALES	
				$sumaValorCompra	  = 0;
				$sumaValorResidu	  = 0;
				$sumaValorNeto  	  = 0;
				$sumaDeprAnterior	  = 0;
				$sumaGastoDepr	      = 0;
				$sumaDeprAcumulada	  = 0;
				$sumaValorPorDepr	  = 0;
				// LISTA DEPRECIACION DE ACTIVOS
				// Dep. Acum. usa SUM(cdep_val_repr) hasta el periodo seleccionado (sin recalcular).
				$sql = " SELECT saeact.act_cod_act,   
					 saeact.act_clave_act,   
					 saeact.act_nom_act,   
					 saeact.act_val_comp,   
					 saeact.act_vutil_act,   
					 saeact.act_fcmp_act, 
					 saeact.act_fiman_act,  
					 saeact.act_fdep_act,   
					 max(saecdep.cdep_ani_depr) as cdep_ani_depr,   
					 saegact.gact_cod_gact,   
					 saegact.gact_des_gact,   
					 saesgac.sgac_cod_sgac,   
					 saesgac.sgac_des_sgac,   					 
					(select COALESCE(SUM(c.cdep_val_repr), 0)
					 from saecdep c 
					 where c.cdep_cod_acti = saecdep.cdep_cod_acti
					 and c.act_cod_empr = saecdep.act_cod_empr
					 and c.act_cod_sucu = saecdep.act_cod_sucu
					 and ((c.cdep_ani_depr * 100) + c.cdep_mes_depr) <= ($anio_iter * 100 + $mes_iter)) as cdep_dep_acum,
					 sum(saecdep.cdep_gas_depn) as cdep_gas_depn, 					 
					 max(saecdep.cdep_mes_depr) as cdep_mes_depr,
					 DATE_PART('year', act_fiman_act ) anio,
					 DATE_PART('month',act_fiman_act) mes ,
					 saeact.act_vres_act
					FROM saegact,   
						 saesgac,
						 saecdep,   
						 saeact  
			   WHERE ( saegact.gact_cod_gact = saesgac.gact_cod_gact ) and  
					 ( saegact.gact_cod_empr = saesgac.sgac_cod_empr ) and  
					 ( saesgac.sgac_cod_sgac = saeact.sgac_cod_sgac ) and  
					 ( saesgac.sgac_cod_empr = saeact.act_cod_empr ) and  			
					 ( saeact.act_cod_act = saecdep.cdep_cod_acti ) and  
					 ( saeact.act_cod_empr = saecdep.act_cod_empr ) and  
					 ( ( saecdep.act_cod_empr = $empresa ) and
					 ( saecdep.cdep_ani_depr = $anio_iter ) and  
					 ( saecdep.cdep_mes_depr = $mes_iter  ) ) and
					 ( ( (COALESCE(DATE_PART('year', act_fiman_act ),3000))*100+COALESCE(DATE_PART('month',act_fiman_act),13)   )  > ($anio_fin *100 + $mes_iter)  )  and
					 ( DATE_PART('year', act_fcmp_act) < $anio_fin or ( DATE_PART('year', act_fcmp_act) = $anio_fin and DATE_PART('month',act_fcmp_act)<= $mes_iter))
						$filtro
						GROUP BY 1,2,3,4,5,6,7,8,10,11,12,13,14,17,18,19
						ORDER BY saegact.gact_des_gact, saesgac.sgac_des_sgac, saeact.act_nom_act, cdep_ani_depr, cdep_mes_depr ";
				//echo $sql; exit;
				//$oReturn->alert($sql);
				if ($oIfx->Query($sql)) {
					if ($oIfx->NumFilas() > 0) {
						$i = 1;
						$ctrl_reg++;

						do {
							$clave  	   = $oIfx->f('act_clave_act');
							$nombre 	   = $oIfx->f('act_nom_act');
							$fechaDepre    = $oIfx->f('act_fdep_act');
							$vidaUtil      = $oIfx->f('act_vutil_act');
							$anio 		   = $oIfx->f('cdep_ani_depr');
							$mes 		   = $oIfx->f('cdep_mes_depr');
							$periodo_encontrado = ($anio * 100) + $mes;
							if ($periodo_encontrado > $max_mes_encontrado) {
								$max_mes_encontrado = $periodo_encontrado;
							}
							$valorCompra   = $oIfx->f('act_val_comp');
							$valorResidu   = $oIfx->f('act_vres_act');
							$serie         = $oIfx->f('act_seri_act');
							$grupo  	   = $oIfx->f('gact_des_gact');
							$subgrupo 	   = $oIfx->f('sgac_des_sgac');
							$deprAcumulada = $oIfx->f('cdep_dep_acum');
							$gastoDepr     = $oIfx->f('cdep_gas_depn');
							$deprAnterior  = max($deprAcumulada - $gastoDepr, 0);

							$valorNeto     = $valorCompra - $valorResidu;
							$valorPorDepr  = $valorCompra -  $deprAcumulada;

							if ($i < 2) {
								$html .= '<tr>
										<td class="bg-info" colspan="13" style = "color:blue">' . $grupo . ' </td> 										
									</tr>
									<tr>										
										<td class="bg-info" colspan="13">&nbsp;&nbsp;&nbsp;&nbsp;' . $subgrupo . ' </td> 
									</tr>';
								$html .= '<tr>
										<td>' . $clave . ' </td> 
										<td>' . $nombre . ' </td> 
										<td>' . $fechaDepre . ' </td>
										<td align = right>' . $vidaUtil . ' </td>
										<td align = right>' . $anio . ' </td>
										<td align = right>' . $mes . ' <span class="badge badge-info">Mes real depreciado</span></td>
										<td align = right>' . number_format($valorCompra, 2, '.', ',') . ' </td>
										<td align = right>' . number_format($valorResidu, 2, '.', ',') . ' </td>
										<td align = right>' . number_format($valorNeto, 2, '.', ',') . ' </td>
										<td align = right>' . number_format($deprAnterior, 2, '.', ',') . ' </td>
										<td align = right>' . number_format($gastoDepr, 2, '.', ',') . ' </td>
										<td align = right>' . number_format($deprAcumulada, 2, '.', ',') . ' </td>
										<td align = right>' . number_format($valorPorDepr, 2, '.', ',') . ' </td>
									</tr>';
								$totalValorCompra     =  $valorCompra;
								$totalValorResidu     =  $valorResidu;
								$totalValorNeto       =  $valorNeto;
								$totalDeprAnterior    =  $deprAnterior;
								$totalGastoDepr       =  $gastoDepr;
								$totalDeprAcumulada   =  $deprAcumulada;
								$totalValorPorDepr    =  $valorPorDepr;
							} else {
								if ($grupo == $grupoAnt) {
									$totalValorCompra   = $totalValorCompra   + $valorCompra;
									$totalValorResidu   = $totalValorResidu   + $valorResidu;
									$totalValorNeto     = $totalValorNeto     + $valorNeto;
									$totalDeprAnterior  = $totalDeprAnterior  + $deprAnterior;
									$totalGastoDepr     = $totalGastoDepr     + $gastoDepr;
									$totalDeprAcumulada = $totalDeprAcumulada + $deprAcumulada;
									$totalValorPorDepr  = $totalValorPorDepr  + $valorPorDepr;
									if ($subgrupo == $subgrupoAnt) {
										$html .= '<tr>
												<td>' . $clave . ' </td> 
												<td>' . $nombre . ' </td> 
												<td>' . $fechaDepre . ' </td>
												<td align = right>' . $vidaUtil . ' </td>
												<td align = right>' . $anio . ' </td>
												<td align = right>' . $mes . ' <span class="badge badge-info">Mes real depreciado</span></td>
												<td align = right>' . number_format($valorCompra, 2, '.', ',') . ' </td>
												<td align = right>' . number_format($valorResidu, 2, '.', ',') . ' </td>
												<td align = right>' . number_format($valorNeto, 2, '.', ',') . ' </td>
												<td align = right>' . number_format($deprAnterior, 2, '.', ',') . ' </td>
												<td align = right>' . number_format($gastoDepr, 2, '.', ',') . ' </td>
												<td align = right>' . number_format($deprAcumulada, 2, '.', ',') . ' </td>
												<td align = right>' . number_format($valorPorDepr, 2, '.', ',') . ' </td>
											</tr>';
									} else {
										$html .= '<tr>																						
												<td class="bg-info" colspan="13">&nbsp;&nbsp;&nbsp;&nbsp;' . $subgrupo . ' </td>
											</tr>
											<tr>
												<td>' . $clave . ' </td> 
												<td>' . $nombre . ' </td> 
												<td>' . $fechaDepre . ' </td>
												<td align = right>' . $vidaUtil . ' </td>
												<td align = right>' . $anio . ' </td>
												<td align = right>' . $mes . ' <span class="badge badge-info">Mes real depreciado</span></td>
												<td align = right>' . number_format($valorCompra, 2, '.', ',') . ' </td>
												<td align = right>' . number_format($valorResidu, 2, '.', ',') . ' </td>
												<td align = right>' . number_format($valorNeto, 2, '.', ',') . ' </td>
												<td align = right>' . number_format($deprAnterior, 2, '.', ',') . ' </td>
												<td align = right>' . number_format($gastoDepr, 2, '.', ',') . ' </td>
												<td align = right>' . number_format($deprAcumulada, 2, '.', ',') . ' </td>
												<td align = right>' . number_format($valorPorDepr, 2, '.', ',') . ' </td>
											</tr>';
									}
								} else {
									$html .= '<tr> <td colspan="6" style = "color:red">' . "TOTAL GRUPO" . '</td>   									
											<td align = right style = "color:red">' . number_format($totalValorCompra, 2, '.', ',') . ' </td> 
											<td align = right style = "color:red">' . number_format($totalValorResidu, 2, '.', ',') . ' </td> 
											<td align = right style = "color:red">' . number_format($totalValorNeto, 2, '.', ',') . ' </td> 
											<td align = right style = "color:red">' . number_format($totalDeprAnterior, 2, '.', ',') . ' </td> 
											<td align = right style = "color:red">' . number_format($totalGastoDepr, 2, '.', ',') . ' </td> 
											<td align = right style = "color:red">' . number_format($totalDeprAcumulada, 2, '.', ',') . ' </td> 
											<td align = right style = "color:red">' . number_format($totalValorPorDepr, 2, '.', ',') . ' </td> 
										</tr>
										<tr>										
											<td class="bg-info" colspan="13" style = "color:blue">' . $grupo . ' </td>
										</tr>
										<tr>										
											<td class="bg-info" colspan="13">&nbsp;&nbsp;&nbsp;&nbsp;' . $subgrupo . ' </td>
										</tr>
										<tr>
											<td>' . $clave . ' </td> 
											<td>' . $nombre . ' </td> 
											<td>' . $fechaDepre . ' </td>
											<td align = right>' . $vidaUtil . ' </td>
											<td align = right>' . $anio . ' </td>
											<td align = right>' . $mes . ' <span class="badge badge-info">Mes real depreciado</span></td>
											<td align = right>' . number_format($valorCompra, 2, '.', ',') . ' </td>
											<td align = right>' . number_format($valorResidu, 2, '.', ',') . ' </td>
											<td align = right>' . number_format($valorNeto, 2, '.', ',') . ' </td>
											<td align = right>' . number_format($deprAnterior, 2, '.', ',') . ' </td>
											<td align = right>' . number_format($gastoDepr, 2, '.', ',') . ' </td>
											<td align = right>' . number_format($deprAcumulada, 2, '.', ',') . ' </td>
											<td align = right>' . number_format($valorPorDepr, 2, '.', ',') . ' </td>
										</tr>';
									// GUARDAR TOTALES GENERALES
									$sumaValorCompra	  = $sumaValorCompra   + $totalValorCompra;
									$sumaValorResidu	  = $sumaValorResidu   + $totalValorResidu;
									$sumaValorNeto  	  = $sumaValorNeto     + $totalValorNeto;
									$sumaDeprAnterior	  = $sumaDeprAnterior  + $totalDeprAnterior;
									$sumaGastoDepr	      = $sumaGastoDepr     + $totalGastoDepr;
									$sumaDeprAcumulada	  = $sumaDeprAcumulada + $totalDeprAcumulada;
									$sumaValorPorDepr	  = $sumaValorPorDepr  + $totalValorPorDepr;
									// INICIAR TOTALES POR GRUPO
									$totalValorCompra     =  $valorCompra;
									$totalValorResidu     =  $valorResidu;
									$totalValorNeto       =  $valorNeto;
									$totalDeprAnterior    =  $deprAnterior;
									$totalGastoDepr       =  $gastoDepr;
									$totalDeprAcumulada   =  $deprAcumulada;
									$totalValorPorDepr    =  $valorPorDepr;
								}
								// TOTAL POR GRUPOS

								//$totalValorCompra = $totalValorCompra + $valorCompra;
							}
							$grupoAnt	 = $grupo;
							$subgrupoAnt = $subgrupo;
							$i++;
						} while ($oIfx->SiguienteRegistro());
						// ULTIMA FILA POR GRUPOS
						$html .= '<tr> <td colspan="6" style = "color:red">' . "TOTAL GRUPO" . '</td>   									
								<td align = right style = "color:red">' . number_format($totalValorCompra, 2, '.', ',') . ' </td> 
								<td align = right style = "color:red">' . number_format($totalValorResidu, 2, '.', ',') . ' </td> 
								<td align = right style = "color:red">' . number_format($totalValorNeto, 2, '.', ',') . ' </td> 
								<td align = right style = "color:red">' . number_format($totalDeprAnterior, 2, '.', ',') . ' </td> 
								<td align = right style = "color:red">' . number_format($totalGastoDepr, 2, '.', ',') . ' </td> 
								<td align = right style = "color:red">' . number_format($totalDeprAcumulada, 2, '.', ',') . ' </td> 
								<td align = right style = "color:red">' . number_format($totalValorPorDepr, 2, '.', ',') . ' </td> 
							</tr>';
						// ULTIMA FILA TOTALES	
						$sumaValorCompra	  = $sumaValorCompra   + $totalValorCompra;
						$sumaValorResidu	  = $sumaValorResidu   + $totalValorResidu;
						$sumaValorNeto  	  = $sumaValorNeto     + $totalValorNeto;
						$sumaDeprAnterior	  = $sumaDeprAnterior  + $totalDeprAnterior;
						$sumaGastoDepr	      = $sumaGastoDepr     + $totalGastoDepr;
						$sumaDeprAcumulada	  = $sumaDeprAcumulada + $totalDeprAcumulada;
						$sumaValorPorDepr	  = $sumaValorPorDepr  + $totalValorPorDepr;
						$html .= '<tr> <td colspan="6" style = "color:red">' . "TOTALES" . '</td>   									
								<td align = right style = "color:red">' . number_format($sumaValorCompra, 2, '.', ',') . ' </td> 
								<td align = right style = "color:red">' . number_format($sumaValorResidu, 2, '.', ',') . ' </td> 
								<td align = right style = "color:red">' . number_format($sumaValorNeto, 2, '.', ',') . ' </td> 
								<td align = right style = "color:red">' . number_format($sumaDeprAnterior, 2, '.', ',') . ' </td> 
								<td align = right style = "color:red">' . number_format($sumaGastoDepr, 2, '.', ',') . ' </td> 
								<td align = right style = "color:red">' . number_format($sumaDeprAcumulada, 2, '.', ',') . ' </td> 
								<td align = right style = "color:red">' . number_format($sumaValorPorDepr, 2, '.', ',') . ' </td> 
							</tr>';
					}
				}
				$oIfx->Free();
				$periodo_actual->modify('+1 month');
			} //CIERRE WHILE MES
			if ($max_mes_encontrado > 0 && $periodo_filtro > $max_mes_encontrado) {
				$mostrar_aviso_mes = true;
			}
			if ($mostrar_aviso_mes) {
				$aviso_mes_html = '<div class="text-info"><em>Si el mes seleccionado aún no ha sido depreciado, el sistema mostrará el último mes disponible del activo.</em></div>';
			}

		} //CIERRE IF DETALLADO

		else {
			// LISTA DEPRECIACION DE ACTIVOS
			// Dep. Acum. usa SUM(cdep_val_repr) hasta el periodo seleccionado (sin recalcular).
			$sql = " SELECT saeact.act_cod_act,   
					 saeact.act_clave_act,   
					 saeact.act_nom_act,   
					 saeact.act_val_comp,   
					 saeact.act_vutil_act,   
					 saeact.act_fcmp_act, 
					 saeact.act_fiman_act,  
					 saeact.act_fdep_act,   
					 max(saecdep.cdep_ani_depr) as cdep_ani_depr,   
					 saegact.gact_cod_gact,   
					 saegact.gact_des_gact,   
					 saesgac.sgac_cod_sgac,   
					 saesgac.sgac_des_sgac,   					 
					(select COALESCE(SUM(c.cdep_val_repr), 0)
					 from saecdep c 
					 where c.cdep_cod_acti = saecdep.cdep_cod_acti
					 and c.act_cod_empr = saecdep.act_cod_empr
					 and c.act_cod_sucu = saecdep.act_cod_sucu
					 and ((c.cdep_ani_depr * 100) + c.cdep_mes_depr) <= ($anio_fin * 100 + $mes_fin)) as cdep_dep_acum,
					 MIN(saecdep.cdep_gas_depn) as cdep_gas_depn, 					 
					 max(saecdep.cdep_mes_depr) as cdep_mes_depr,
					 DATE_PART('year', act_fiman_act ) anio,
					 DATE_PART('month',act_fiman_act) mes,
					 saeact.act_vres_act,
					 MAX(saecdep.cdep_val_rep1) as cdep_val_rep1

					FROM saegact,   
						 saesgac,
						 saecdep,   
						 saeact  
					WHERE ( saegact.gact_cod_gact = saesgac.gact_cod_gact ) and  
					 ( saegact.gact_cod_empr = saesgac.sgac_cod_empr ) and  
					 ( saesgac.sgac_cod_sgac = saeact.sgac_cod_sgac ) and  
					 ( saesgac.sgac_cod_empr = saeact.act_cod_empr ) and  			
					 ( saeact.act_cod_act = saecdep.cdep_cod_acti ) and  
					 ( saeact.act_cod_empr = saecdep.act_cod_empr ) and  
					 ( ( saecdep.act_cod_empr = $empresa ) and
					 ( (saecdep.cdep_ani_depr * 100) + saecdep.cdep_mes_depr between ($anio * 100 + $mes) and ($anio_fin * 100 + $mes_fin) ) ) and
					 ( ( (COALESCE(DATE_PART('year', act_fiman_act ),3000))*100+COALESCE(DATE_PART('month',act_fiman_act),13)   )  > ($anio_fin *100 + $mes_fin)  )  and
					 ( DATE_PART('year', act_fcmp_act) < $anio_fin or ( DATE_PART('year', act_fcmp_act) = $anio_fin and DATE_PART('month',act_fcmp_act)<= $mes_fin))
						$filtro
						GROUP BY 1,2,3,4,5,6,7,8,10,11,12,13,14,17,18,19
						ORDER BY saegact.gact_des_gact, saesgac.sgac_des_sgac, saeact.act_nom_act, cdep_ani_depr, cdep_mes_depr ";
			//echo $sql; exit;
			//$oReturn->alert($sql);
			if ($oIfx->Query($sql)) {
				if ($oIfx->NumFilas() > 0) {
					$i = 1;
					$ctrl_reg++;

					do {
						$clave  	   = $oIfx->f('act_clave_act');
						$nombre 	   = $oIfx->f('act_nom_act');
						$fechaDepre    = $oIfx->f('act_fdep_act');
						$vidaUtil      = $oIfx->f('act_vutil_act');
						$anio 		   = $oIfx->f('cdep_ani_depr');
						$mes 		   = $oIfx->f('cdep_mes_depr');
						if ((($anio_fin * 100) + $mes_fin) > (($anio * 100) + $mes)) {
							$mostrar_aviso_mes = true;
						}
						$valorCompra   = $oIfx->f('act_val_comp');
						$valorResidu   = $oIfx->f('act_vres_act');
						$serie         = $oIfx->f('act_seri_act');
						$grupo  	   = $oIfx->f('gact_des_gact');
						$subgrupo 	   = $oIfx->f('sgac_des_sgac');
						$gastoDepr     = $oIfx->f('cdep_gas_depn');
						$deprAcumulada = $oIfx->f('cdep_dep_acum');
						$deprAnterior  = max($deprAcumulada - $gastoDepr, 0);



						$valorNeto     = $valorCompra - $valorResidu;
						$valorPorDepr  = $valorCompra -  $deprAcumulada;

						if ($i < 2) {
							$html .= '<tr>
										<td class="bg-info" colspan="13" style = "color:blue">' . $grupo . ' </td> 										
									</tr>
									<tr>										
										<td class="bg-info" colspan="13">&nbsp;&nbsp;&nbsp;&nbsp;' . $subgrupo . ' </td> 
									</tr>';
							$html .= '<tr>
										<td>' . $clave . ' </td> 
										<td>' . $nombre . ' </td> 
										<td>' . $fechaDepre . ' </td>
										<td align = right>' . $vidaUtil . ' </td>
										<td align = right>' . $anio . ' </td>
										<td align = right>' . $mes . ' <span class="badge badge-info">Mes real depreciado</span></td>
										<td align = right>' . number_format($valorCompra, 2, '.', ',') . ' </td>
										<td align = right>' . number_format($valorResidu, 2, '.', ',') . ' </td>
										<td align = right>' . number_format($valorNeto, 2, '.', ',') . ' </td>
										<td align = right>' . number_format($deprAnterior, 2, '.', ',') . ' </td>
										<td align = right>' . number_format($gastoDepr, 2, '.', ',') . ' </td>
										<td align = right>' . number_format($deprAcumulada, 2, '.', ',') . ' </td>
										<td align = right>' . number_format($valorPorDepr, 2, '.', ',') . ' </td>
									</tr>';
							$totalValorCompra     =  $valorCompra;
							$totalValorResidu     =  $valorResidu;
							$totalValorNeto       =  $valorNeto;
							$totalDeprAnterior    =  $deprAnterior;
							$totalGastoDepr       =  $gastoDepr;
							$totalDeprAcumulada   =  $deprAcumulada;
							$totalValorPorDepr    =  $valorPorDepr;
						} else {
							if ($grupo == $grupoAnt) {
								$totalValorCompra   = $totalValorCompra   + $valorCompra;
								$totalValorResidu   = $totalValorResidu   + $valorResidu;
								$totalValorNeto     = $totalValorNeto     + $valorNeto;
								$totalDeprAnterior  = $totalDeprAnterior  + $deprAnterior;
								$totalGastoDepr     = $totalGastoDepr     + $gastoDepr;
								$totalDeprAcumulada = $totalDeprAcumulada + $deprAcumulada;
								$totalValorPorDepr  = $totalValorPorDepr  + $valorPorDepr;
								if ($subgrupo == $subgrupoAnt) {
									$html .= '<tr>
												<td>' . $clave . ' </td> 
												<td>' . $nombre . ' </td> 
												<td>' . $fechaDepre . ' </td>
												<td align = right>' . $vidaUtil . ' </td>
												<td align = right>' . $anio . ' </td>
												<td align = right>' . $mes . ' <span class="badge badge-info">Mes real depreciado</span></td>
												<td align = right>' . number_format($valorCompra, 2, '.', ',') . ' </td>
												<td align = right>' . number_format($valorResidu, 2, '.', ',') . ' </td>
												<td align = right>' . number_format($valorNeto, 2, '.', ',') . ' </td>
												<td align = right>' . number_format($deprAnterior, 2, '.', ',') . ' </td>
												<td align = right>' . number_format($gastoDepr, 2, '.', ',') . ' </td>
												<td align = right>' . number_format($deprAcumulada, 2, '.', ',') . ' </td>
												<td align = right>' . number_format($valorPorDepr, 2, '.', ',') . ' </td>
											</tr>';
								} else {
									$html .= '<tr>																						
												<td class="bg-info" colspan="13">&nbsp;&nbsp;&nbsp;&nbsp;' . $subgrupo . ' </td>
											</tr>
											<tr>
												<td>' . $clave . ' </td> 
												<td>' . $nombre . ' </td> 
												<td>' . $fechaDepre . ' </td>
												<td align = right>' . $vidaUtil . ' </td>
												<td align = right>' . $anio . ' </td>
												<td align = right>' . $mes . ' <span class="badge badge-info">Mes real depreciado</span></td>
												<td align = right>' . number_format($valorCompra, 2, '.', ',') . ' </td>
												<td align = right>' . number_format($valorResidu, 2, '.', ',') . ' </td>
												<td align = right>' . number_format($valorNeto, 2, '.', ',') . ' </td>
												<td align = right>' . number_format($deprAnterior, 2, '.', ',') . ' </td>
												<td align = right>' . number_format($gastoDepr, 2, '.', ',') . ' </td>
												<td align = right>' . number_format($deprAcumulada, 2, '.', ',') . ' </td>
												<td align = right>' . number_format($valorPorDepr, 2, '.', ',') . ' </td>
											</tr>';
								}
							} else {
								$html .= '<tr> <td colspan="6" style = "color:red">' . "TOTAL GRUPO" . '</td>   									
											<td align = right style = "color:red">' . number_format($totalValorCompra, 2, '.', ',') . ' </td> 
											<td align = right style = "color:red">' . number_format($totalValorResidu, 2, '.', ',') . ' </td> 
											<td align = right style = "color:red">' . number_format($totalValorNeto, 2, '.', ',') . ' </td> 
											<td align = right style = "color:red">' . number_format($totalDeprAnterior, 2, '.', ',') . ' </td> 
											<td align = right style = "color:red">' . number_format($totalGastoDepr, 2, '.', ',') . ' </td> 
											<td align = right style = "color:red">' . number_format($totalDeprAcumulada, 2, '.', ',') . ' </td> 
											<td align = right style = "color:red">' . number_format($totalValorPorDepr, 2, '.', ',') . ' </td> 
										</tr>
										<tr>										
											<td class="bg-info" colspan="13" style = "color:blue">' . $grupo . ' </td>
										</tr>
										<tr>										
											<td class="bg-info" colspan="13">&nbsp;&nbsp;&nbsp;&nbsp;' . $subgrupo . ' </td>
										</tr>
										<tr>
											<td>' . $clave . ' </td> 
											<td>' . $nombre . ' </td> 
											<td>' . $fechaDepre . ' </td>
											<td align = right>' . $vidaUtil . ' </td>
											<td align = right>' . $anio . ' </td>
											<td align = right>' . $mes . ' <span class="badge badge-info">Mes real depreciado</span></td>
											<td align = right>' . number_format($valorCompra, 2, '.', ',') . ' </td>
											<td align = right>' . number_format($valorResidu, 2, '.', ',') . ' </td>
											<td align = right>' . number_format($valorNeto, 2, '.', ',') . ' </td>
											<td align = right>' . number_format($deprAnterior, 2, '.', ',') . ' </td>
											<td align = right>' . number_format($gastoDepr, 2, '.', ',') . ' </td>
											<td align = right>' . number_format($deprAcumulada, 2, '.', ',') . ' </td>
											<td align = right>' . number_format($valorPorDepr, 2, '.', ',') . ' </td>
										</tr>';
								// GUARDAR TOTALES GENERALES
								$sumaValorCompra	  = $sumaValorCompra   + $totalValorCompra;
								$sumaValorResidu	  = $sumaValorResidu   + $totalValorResidu;
								$sumaValorNeto  	  = $sumaValorNeto     + $totalValorNeto;
								$sumaDeprAnterior	  = $sumaDeprAnterior  + $totalDeprAnterior;
								$sumaGastoDepr	      = $sumaGastoDepr     + $totalGastoDepr;
								$sumaDeprAcumulada	  = $sumaDeprAcumulada + $totalDeprAcumulada;
								$sumaValorPorDepr	  = $sumaValorPorDepr  + $totalValorPorDepr;
								// INICIAR TOTALES POR GRUPO
								$totalValorCompra     =  $valorCompra;
								$totalValorResidu     =  $valorResidu;
								$totalValorNeto       =  $valorNeto;
								$totalDeprAnterior    =  $deprAnterior;
								$totalGastoDepr       =  $gastoDepr;
								$totalDeprAcumulada   =  $deprAcumulada;
								$totalValorPorDepr    =  $valorPorDepr;
							}
							// TOTAL POR GRUPOS

							//$totalValorCompra = $totalValorCompra + $valorCompra;
						}
						$grupoAnt	 = $grupo;
						$subgrupoAnt = $subgrupo;
						$i++;
					} while ($oIfx->SiguienteRegistro());
					// ULTIMA FILA POR GRUPOS
					$html .= '<tr> <td colspan="6" style = "color:red">' . "TOTAL GRUPO" . '</td>   									
								<td align = right style = "color:red">' . number_format($totalValorCompra, 2, '.', ',') . ' </td> 
								<td align = right style = "color:red">' . number_format($totalValorResidu, 2, '.', ',') . ' </td> 
								<td align = right style = "color:red">' . number_format($totalValorNeto, 2, '.', ',') . ' </td> 
								<td align = right style = "color:red">' . number_format($totalDeprAnterior, 2, '.', ',') . ' </td> 
								<td align = right style = "color:red">' . number_format($totalGastoDepr, 2, '.', ',') . ' </td> 
								<td align = right style = "color:red">' . number_format($totalDeprAcumulada, 2, '.', ',') . ' </td> 
								<td align = right style = "color:red">' . number_format($totalValorPorDepr, 2, '.', ',') . ' </td> 
							</tr>';
					// ULTIMA FILA TOTALES	
					$sumaValorCompra	  = $sumaValorCompra   + $totalValorCompra;
					$sumaValorResidu	  = $sumaValorResidu   + $totalValorResidu;
					$sumaValorNeto  	  = $sumaValorNeto     + $totalValorNeto;
					$sumaDeprAnterior	  = $sumaDeprAnterior  + $totalDeprAnterior;
					$sumaGastoDepr	      = $sumaGastoDepr     + $totalGastoDepr;
					$sumaDeprAcumulada	  = $sumaDeprAcumulada + $totalDeprAcumulada;
					$sumaValorPorDepr	  = $sumaValorPorDepr  + $totalValorPorDepr;
					$html .= '<tr> <td colspan="6" style = "color:red">' . "TOTALES" . '</td>   									
								<td align = right style = "color:red">' . number_format($sumaValorCompra, 2, '.', ',') . ' </td> 
								<td align = right style = "color:red">' . number_format($sumaValorResidu, 2, '.', ',') . ' </td> 
								<td align = right style = "color:red">' . number_format($sumaValorNeto, 2, '.', ',') . ' </td> 
								<td align = right style = "color:red">' . number_format($sumaDeprAnterior, 2, '.', ',') . ' </td> 
								<td align = right style = "color:red">' . number_format($sumaGastoDepr, 2, '.', ',') . ' </td> 
								<td align = right style = "color:red">' . number_format($sumaDeprAcumulada, 2, '.', ',') . ' </td> 
								<td align = right style = "color:red">' . number_format($sumaValorPorDepr, 2, '.', ',') . ' </td> 
							</tr>';
				}
			}
			$oIfx->Free();
			if ($mostrar_aviso_mes) {
				$aviso_mes_html = '<div class="text-info"><em>Si el mes seleccionado aún no ha sido depreciado, el sistema mostrará el último mes disponible del activo.</em></div>';
			}
		} //CIERRE ELSE


		$html .= '</table>';

		if ($ctrl_reg != 0) {
			if (!empty($aviso_mes_html)) {
				$html = $aviso_mes_html . $html;
			}
			$_SESSION['ACT_REP_DEPR'] = $html;
		} else {
			$html = '<div style="font-size:14px;" ><b>..Sin Datos..<b/></div>';
		}


		$oReturn->assign("reporte", "innerHTML", $html);
		$oReturn->script('initDataTableReporte();');
		$oReturn->alert('Proceso Terminado con Exito');
		$oIfx->QueryT('COMMIT WORK;');
	} catch (Exception $e) {
		$oCon->QueryT('ROLLBACK');
		error_log('Reporte depreciación: ' . $e->getMessage());
		$oReturn->alert('Error al generar el reporte de depreciación. Revise la configuración del activo.');
	}
	return $oReturn;
}


/* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */
/* PROCESO DE REQUEST DE LAS FUNCIONES MEDIANTE AJAX NO MODIFICAR */
$xajax->processRequest();
/* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */
