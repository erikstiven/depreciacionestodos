<?php

require ("_Ajax.comun.php"); // No modificar esta linea
$mayorizacion_path = __DIR__ . '/mayorizacion.inc.php';
$mayorizacion_available = true;
if (file_exists($mayorizacion_path)) {
    include_once $mayorizacion_path;
} else {
    $mayorizacion_available = false;
}
/* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
  // S E R V I D O R   A J A X //
  :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */

/* * ******************************************* */
/* PROCESO DE REVALORIZACION DE ACTIVOS FIJOS */
/* FECHA: 14-OCT-2018 */
/* XAVIER REYES */
/* * ******************************************* */

function genera_cabecera_formulario($sAccion = 'nuevo', $aForm = '') {
    //Definiciones
    global $DSN_Ifx, $DSN;

    if (session_status() !== PHP_SESSION_ACTIVE) {session_start();}

    $oIfx = new Dbo ( );
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    $oIfx1 = new Dbo ( );
    $oIfx1->DSN = $DSN_Ifx;
    $oIfx1->Conectar();

    $oCon = new Dbo ( );
    $oCon->DSN = $DSN;
    $oCon->Conectar();

    $fu = new Formulario;
    $fu->DSN = $DSN;

    $ifu = new Formulario;
    $ifu->DSN = $DSN_Ifx;
	// PRUEBA ABRE CMD
	//$cmd = popen("start C:/Windows/System32/notepad.exe 2>&1", "r");
	//
    $oReturn = new xajaxResponse ( );

    //variables de sesion
    $idempresa = $_SESSION['U_EMPRESA'] ?? '';
    $sucursal = $_SESSION['U_SUCURSAL'] ?? '';
    $user_ifx = $_SESSION['U_USER_INFORMIX'] ?? '';
    $usuario_web = $_SESSION['U_ID'] ?? '';
    if ($idempresa === '' || $sucursal === '' || $user_ifx === '' || $usuario_web === '') {
        $oReturn->alert('Error: sesión inválida o incompleta. Verifica el inicio de sesión y vuelve a intentar.');
        return $oReturn;
    }
    //variables del formulario
	 $empresa = $aForm['empresa'];

     if (empty($empresa)) {
		$empresa = $idempresa;
     }
    switch ($sAccion) {
        case 'nuevo':
			$ifu->AgregarCampoListaSQL('cod_grupo', "Grupo|left","select gact_cod_gact, gact_des_gact 
																from saegact 
																where gact_cod_empr = '$empresa'
																order by gact_des_gact",false, 150, 150);
			$ifu->AgregarComandoAlCambiarValor('cod_grupo', 'f_filtro_subgrupo()');
            $ifu->AgregarCampoListaSQL('cod_subgrupo', "Subgrupo|left","",false, 150, 150);
			$ifu->AgregarComandoAlCambiarValor('cod_subgrupo', 'f_filtro_activos()');
			$ifu->AgregarCampoListaSQL('cod_activo', 'Activo|left','', false, 150, 150);  
			$ifu->AgregarComandoAlCambiarValor('cod_activo', 'f_calcula_vida_util()');			
			$ifu->AgregarCampoFecha('fecha', 'Fecha|left', true,'', 170, 150);																				
            $ifu->AgregarCampoNumerico('valor', "Valor|left","",false, 150, 150);			
			$ifu->AgregarCampoNumerico('vida_util', 'Vida Util|left','', false, 170, 150);
	}   
			
    $table_op .='<table class="table table-striped table-condensed" style="width: 50%; margin-bottom: 0px;" >
		<tr> 
                    <td colspan="8" align="center" class="bg-primary">REVALORIZACION DE ACTIVOS FIJOS</td>
		</tr>
                <tr>
                    <td colspan = "8">    
                        <div class="btn-group">
                            <div class="btn btn-primary btn-sm" onclick="generar();" id = "generar">
                                    <span class="glyphicon glyphicon-cog"></span>
                                    Generar
                            </div>
                        </div>
                    </td>                   
                </tr>
                <tr class="msgFrm">
                    <td colspan="8" align="center">Los campos con * son de ingreso obligatorio</td>
                </tr>
				 <tr>
                    <td>' . $ifu->ObjetoHtmlLBL('cod_grupo') . '</td>
                    <td>' . $ifu->ObjetoHtml('cod_grupo') . '</td>
                    <td>' . $ifu->ObjetoHtmlLBL('cod_subgrupo') . '</td>
                    <td>' . $ifu->ObjetoHtml('cod_subgrupo') . '</td>
                </tr>
				 <tr>
                    <td>' . $ifu->ObjetoHtmlLBL('cod_activo') . '</td>
                    <td>' . $ifu->ObjetoHtml('cod_activo') . '</td>
                    <td>' . $ifu->ObjetoHtmlLBL('fecha') . '</td>
                    <td>' . $ifu->ObjetoHtml('fecha') . '</td>
                </tr>
				<tr>
					<td>' . $ifu->ObjetoHtmlLBL('valor') . '</td>
                    <td>' . $ifu->ObjetoHtml('valor') . '</td>
					<td>' . $ifu->ObjetoHtmlLBL('vida_util') . '</td>
                    <td>' . $ifu->ObjetoHtml('vida_util') . '</td>					
				</tr>
				<tr>
                    <td align = "center" colspan = "8">    
                        <div class="btn-group">
							<div class="btn btn-primary btn-sm" onclick="f_informacion_activo();" id = "consultar">
										<span class="glyphicon glyphicon-search"></span>
										Consultar  
							</div>								
                        </div>
                    </td>                   
                </tr>
                </table>				
				
				<br>
				<div id = "reporte_activo"> </div>';
    $table_op .= '</fieldset>';
    $oReturn->assign("divFormularioReportesGrupos", "innerHTML", $table_op);

    return $oReturn;
}

function f_calcula_vida_util($aForm = ''){
    //Definiciones
    global $DSN, $DSN_Ifx;
    if (session_status() !== PHP_SESSION_ACTIVE) {session_start();}

    $oCon = new Dbo ( );
    $oCon->DSN = $DSN;
    $oCon->Conectar();

    $oIfx = new Dbo ( );
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();
    $oReturn = new xajaxResponse ( );
    
	//variables formulario
    $cod_activo = $aForm['cod_activo'];
	$empresa = $_SESSION['U_EMPRESA'];
	$sucursal = $_SESSION['U_SUCURSAL'];
	// BUSCAR TIPO DE DEPRECIACION Y VIDA UTIL
	$sql = "select act_vutil_act, tdep_cod_tdep from saeact where act_cod_act = $cod_activo and act_cod_empr = $empresa and act_cod_sucu = $sucursal";
	$vUtil	  = consulta_string($sql,'act_vutil_act',$oIfx, 0);
	$tdep_cod_tdep = consulta_string($sql,'tdep_cod_tdep',$oIfx, '');
	$sql = "select tdep_tip_val from saetdep where tdep_cod_empr = $empresa and tdep_cod_tdep = '$tdep_cod_tdep'";
	$tdep_tip_val = consulta_string($sql,'tdep_tip_val',$oIfx, '');
	if ($tdep_tip_val == '' ) {
		$tdep_tip_val = 'M';
	}
	$sql = "select count(*) as numero from saecdep where cdep_cod_acti = $cod_activo and act_cod_empr = $empresa and act_cod_sucu = $sucursal";
	$numDepr = consulta_string($sql,'numero',$oIfx, 0);

	if ($tdep_tip_val == 'M'){
		$vUtil = ($vUtil * 12);
	}
	if ($numDepr < $vUtil){
		$numDepr = round(($vUtil - $numDepr) / 12, 2) ;		
	} else {
		$numDepr = round($vUtil / 12, 2);
	}
	
	//$oReturn->alert($numDepr);
	$oReturn->assign('vida_util', 'value', $numDepr);
    return $oReturn;
	
}

function generar($aForm = ''){
    //Definiciones
    global $DSN, $DSN_Ifx;

    if (session_status() !== PHP_SESSION_ACTIVE) {session_start();}

    $oCon = new Dbo ( );
    $oCon->DSN = $DSN;
    $oCon->Conectar();

    $oIfx = new Dbo ( );
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();
 
	$oIfxA = new Dbo ( );
    $oIfxA->DSN = $DSN_Ifx;
    $oIfxA->Conectar();

	$oReturn = new xajaxResponse ( );
    if (!class_exists('mayorizacion_class')) {
        $oReturn->alert('Error: no se pudo cargar mayorizacion.inc.php. Verifica que el archivo exista y sea accesible.');
        return $oReturn;
    }

    //variables de sesion
    $array       	= $_SESSION['ARRAY_PINTA'];
    $usuario_web 	= $_SESSION['U_ID'];
	$user_ifx    	= $_SESSION['U_USER_INFORMIX'];
	$empresa     	= $_SESSION['U_EMPRESA'];
	$sucursal    	= $_SESSION['U_SUCURSAL'];
	//variables formulario
	$codigoActivo 	= $aForm['cod_activo'];
    $fechaRevalo	= $aForm['fecha'];
	$valorRevalo    = $aForm['valor'];
	$vidaUtilRevalo = $aForm['vida_util'];
	$fechaRevalo    = fecha_informix_func($fechaRevalo);
	//$anio = date("Y", $fechaRevalo);
	$anio_revalo	= 	substr($fechaRevalo, 6, 4);
	$mes_revalo 	= 	substr($fechaRevalo, 0, 2);
	$dia_revalo 	= 	substr($fechaRevalo, 3, 2);
	
	$fechaServer    = 	date("Y-m-d");
	$class = new mayorizacion_class();
    try {
        $oIfx->QueryT('BEGIN WORK;');	
		$sql = "select count(*) as contador
				from saereva 
				where reva_cod_acti = $codigoActivo
				and reva_cod_empr = $empresa
				and reva_cod_sucu = $sucursal
				and reva_fec_reva = '$fechaRevalo'";
		$existe = consulta_string($sql, 'contador', $oIfx, 0);
		if ($existe == 0) {		
			$sql_act = "select act_val_comp, gact_cod_gact 
						from saeact 
						where act_cod_act = $codigoActivo 
						and act_cod_empr = $empresa 
						and act_cod_sucu = $sucursal";				
			if ($oIfx->Query($sql_act)) {
				if ($oIfx->NumFilas() > 0) {
					$valorCompra = $oIfx->f('act_val_comp');
					$codigoGrupo = $oIfx->f('gact_cod_gact');
				}
			}
			$oIfx->Free();
			// BUSCAMOS CUENTAS CONTABLES DE GRUPO
			$sql_ctas = "select gact_cta_reva, gact_cta_supe, gact_cta_desv, gact_cta_defi 
					from saegact 
					where gact_cod_gact = '$codigoGrupo' 
					and gact_cod_empr = $empresa";			
			if ($oIfx->Query($sql_ctas)) {
				if ($oIfx->NumFilas() > 0) {
					$ctaRevalorizacion = $oIfx->f('gact_cta_reva');
					$ctasSuperavit = $oIfx->f('gact_cta_supe');
					$ctaDesvalorizacion = $oIfx->f('gact_cta_desv');
					$ctasDeficit = $oIfx->f('gact_cta_defi');					
				}
			}
			$oIfx->Free();			
			if (empty($ctaRevalorizacion) or empty($ctasSuperavit) or empty($ctaDesvalorizacion) or empty($ctasDeficit)){
				$oReturn->alert('Cuentas Contables de Revalorizacion no Definidas, Favor ir a Grupos de Activos Fijos');				
			} else {				
					// MODEDA BASE - LOCAL	
					$sql_moneda = "select pcon_mon_base from saepcon where pcon_cod_empr = $empresa ";
					$moneda = consulta_string($sql_moneda, 'pcon_mon_base', $oIfx, '');
					$fecha_servidor = date("m-d-Y");
					// TIPO DE CAMBIO
					$sql_tcambio = "select tcam_fec_tcam, tcam_cod_tcam, tcam_val_tcam 
									from saetcam 
									where tcam_cod_mone = $moneda 
									and mone_cod_empr = $empresa 
									and tcam_fec_tcam = (select max(tcam_fec_tcam) 
														 from saetcam 
														 where tcam_cod_mone = $moneda 
														 and tcam_fec_tcam <= '$fecha_servidor' 
														 and mone_cod_empr = $empresa)";
					//echo $sql_tcambio; exit;
					if ($oIfx->Query($sql_tcambio)) {
						if ($oIfx->NumFilas() > 0) {
							$tcambio = $oIfx->f('tcam_cod_tcam');
							$val_tcambio = $oIfx->f('tcam_val_tcam');
						} else {
							$tcambio = 0;
							$val_tcambio = 0;
						}
					}
					$oIfx->Free();	
					if ( $vidaUtilRevalo == 0 ){
						$oReturn->alert('Tiempo de vida util debe ser mayor a cero');
						exit;
					}
					//$num_registros = $vidaUtilRevalo * 12;
					
					// FECHA INICIAL
					if ($dia_revalo > 15){
						if ($mes_revalo == 12){
							$mes = 1;
							$anio = $anio_revalo + 1;
						}else{
							$mes = $mes_revalo + 1;
							$anio = $anio_revalo;
						}
					}else{
						$mes = $mes_revalo;
						$anio = $anio_revalo;
					}	
					// VALOR A DEPRECIAR
					$costoHistorico = $valorCompra;
					$sql_dep_acumulada = "select sum(cdep_gas_depn) as depr_acumulada
										from saecdep
										where cdep_cod_acti = $codigoActivo 
										and cdep_fec_depr <= '$fechaRevalo'
										and cdep_gas_depn > 0
										group by cdep_gas_depn";
					$depr_acumulada = consulta_string($sql_dep_acumulada,'depr_acumulada', $oIfx,0);
					
					$saldo_libros   	=	$costoHistorico - $depr_acumulada;
					$valorRevalMonLoc 	=	$valorRevalo - $saldo_libros;
					$valorRevalMonExt	= 	$valorRevalMonLoc * $val_tcambio;
					$tidu 		  = '041';
					$tipoComp	  = 'DI';
					$modulo		  = '11';
					$tran		  = null;
					$clpv_nom	  = 'ACTIVOS FIJOS';
					// ESTADO REVALORIZADO
					$estadoRevalorizado = "N";
					$sql = "select act_est_reva
							from saeact 
							where act_cod_act = $codigoActivo
							and act_cod_empr = $empresa
							and act_cod_sucu = $sucursal";	
					$estado_reva = consulta_string($sql, 'act_est_reva', $oIfx, 'N');		
							
					if ($valorRevalMonLoc > 0){		
						// ASIENTO CONTABLE - MAESTRO
						$array = $class->secu_asto($oIfx, $empresa,  $sucursal, $modulo, $fechaRevalo, $user_ifx, $tidu, $tipoComp);
						foreach ($array as $val) {
							$asto_cod  = $val[0];
							$secu_dia  = $val[1];
							$tidu      = $val[2];
							$idejer    = $val[3];
							$idprdo    = $val[4];
							$usua_nom  = $val[8];
						}	
						$detalle_asto = "REVALORIZACION DE ACTIVOS FIJOS - AVALUO" . $nombreGrupo.' '. $fechaNormal;			
						$total = 0;			 		
						$class->saeasto($oIfx, $asto_cod, $empresa, $sucursal, $idejer, $idprdo, $moneda, $user_ifx, $tran, 
											 $clpv_nom, $valorRevalMonLoc, $fechaRevalo, $detalle_asto, $secu_dia,  $fechaRevalo , $tidu,  
											 $usua_nom, $usuario_web, $modulo, $tipoComp);
						
						//echo $$asto_cod; exit;
						// DETALLES ASIENTO - DEBITO
						$class->saedasi($oIfx, $empresa, $sucursal, $ctaRevalorizacion, $idprdo, $idejer, '', $valorRevalMonLoc, 0, $valorRevalMonExt, 0, $val_tcambio, 
										$det_asto, $cliente, $tran, $usuario_web, $asto_cod);
						// 	DETALLES ASIENTO - CREDITO			
						$class->saedasi($oIfx, $empresa, $sucursal, $ctasSuperavit, $idprdo, $idejer, '', 0, $valorRevalMonLoc, 0, $valorRevalMonExt, $val_tcambio, 
										$det_asto, $cliente, $tran, $usuario_web, $asto_cod);
						// ESTADO REVALORIZADO
						$estadoRevalorizado = "R";
					}else {
						$valorRevalMonLoc = $valorRevalMonLoc * (-1);
						$valorRevalMonExt = $valorRevalMonExt * (-1);
						// ASIENTO CONTABLE - MAESTRO
						$array = $class->secu_asto($oIfx, $empresa,  $sucursal, $modulo, $fechaRevalo, $user_ifx, $tidu, $tipoComp);
						foreach ($array as $val) {
							$asto_cod  = $val[0];
							$secu_dia  = $val[1];
							$tidu      = $val[2];
							$idejer    = $val[3];
							$idprdo    = $val[4];
							$usua_nom  = $val[8];
						}	
						$detalle_asto = "DESVALORIZACION DE ACTIVOS FIJOS - DETERIORO" . $nombreGrupo.' '. $fechaNormal;			
						$total = 0;			 		
						$class->saeasto($oIfx, $asto_cod, $empresa, $sucursal, $idejer, $idprdo, $moneda, $user_ifx, $tran, 
											 $clpv_nom, $valorRevalMonLoc, $fechaRevalo, $detalle_asto, $secu_dia,  $fechaRevalo , $tidu,  
											 $usua_nom, $usuario_web, $modulo, $tipoComp);
						
						//echo $$asto_cod; exit;
						// VALIDACION PARA CUANDO EL ACTIVO FIJA YA HAYA SIDO REVALUADO ANTES
						if ($estado_reva = "D") {
							$ctaDesvalorizacion = $ctasSuperavit;
						}
						
						// DETALLES ASIENTO - DEBITO
						$class->saedasi($oIfx, $empresa, $sucursal, $ctaDesvalorizacion, $idprdo, $idejer, '', $valorRevalMonLoc, 0, $valorRevalMonExt, 0, $val_tcambio, 
										$det_asto, $cliente, $tran, $usuario_web, $asto_cod);
						// 	DETALLES ASIENTO - CREDITO			
						$class->saedasi($oIfx, $empresa, $sucursal, $ctasDeficit, $idprdo, $idejer, '', 0, $valorRevalMonLoc, 0, $valorRevalMonExt, $val_tcambio, 
										$det_asto, $cliente, $tran, $usuario_web, $asto_cod);
						// ESTADO REVALORIZADO
						$estadoRevalorizado = "D";				
					}
					// CREAR NUEVO ACTIVO
					$sql = "select tact_cod_tact, act_cod_empr, act_cod_sucu, sgac_cod_sgac, eact_cod_eact, act_clave_act,
						act_nom_act, act_marc_act, act_colr_act, act_seri_act, act_mode_act, act_fcmp_act, act_refr_act,
						act_comp_act, act_vutil_act, act_vres_act, act_part_act, act_tcam_act, act_cant_act, tdep_cod_tdep,
						gact_cod_gact, sgac_cod_empr, ccos_cod_ccos, act_prov_act, act_fiman_act, act_foto_act, act_fdep_act,
						act_fcorr_act, act_ext_act, act_gar_act, act_usua_act, act_val_comp, act_cod_ramo, act_nom_prop,
						act_cod_area, act_des_ubic, act_cod_pres, act_path_foto, act_cod_rela
					from saeact
					where act_cod_act = $codigoActivo
					and act_cod_empr = $empresa
					and act_cod_sucu = $sucursal";
					if ($oIfx->Query($sql)) {
						if ($oIfx->NumFilas() > 0) {
							$tact_cod_tact	=	$oIfx->f('tact_cod_tact');
							$act_cod_empr	=	$oIfx->f('act_cod_empr'); 
							$act_cod_sucu 	=	$oIfx->f('act_cod_sucu');
							$sgac_cod_sgac 	=	$oIfx->f('sgac_cod_sgac'); 
							$eact_cod_eact	=	$oIfx->f('eact_cod_eact'); 
							$act_clave_act 	=	$oIfx->f('act_clave_act');
							$act_nom_act 	=	$oIfx->f('act_nom_act');
							$act_marc_act 	=	$oIfx->f('act_marc_act');
							$act_colr_act 	=	$oIfx->f('act_colr_act'); 
							$act_seri_act	=	$oIfx->f('act_seri_act'); 
							$act_mode_act 	=	$oIfx->f('act_mode_act'); 
							$act_fcmp_act 	=	$fechaRevalo; 
							$act_refr_act 	=	$oIfx->f('act_refr_act');
							$act_comp_act 	=	$oIfx->f('act_comp_act'); 
							$act_vutil_act 	=	$oIfx->f('act_vutil_act'); 
							$act_vres_act 	=	$oIfx->f('act_vres_act'); 
							$act_part_act 	=	$oIfx->f('act_part_act'); 
							$act_tcam_act 	=	$val_tcambio;
							$act_cant_act 	=	$oIfx->f('act_cant_act'); 
							$tdep_cod_tdep 	=	$oIfx->f('tdep_cod_tdep');
							$gact_cod_gact 	=	$oIfx->f('gact_cod_gact'); 
							$sgac_cod_empr 	=	$oIfx->f('sgac_cod_empr'); 
							$ccos_cod_ccos 	=	$oIfx->f('ccos_cod_ccos');
							$act_prov_act 	=	$oIfx->f('act_prov_act'); 
							$act_fiman_act 	=	$oIfx->f('act_fiman_act'); 
							$act_foto_act 	=	$oIfx->f('act_foto_act'); 
							$act_fdep_act 	=	$oIfx->f('act_fdep_act');
							$act_fcorr_act 	=	$oIfx->f('act_fcorr_act'); 
							$act_ext_act 	=	$oIfx->f('act_ext_act');
							$act_gar_act 	=	$oIfx->f('act_gar_act'); 
							$act_usua_act 	=	$oIfx->f('act_usua_act');
							$act_val_comp 	=	$oIfx->f('act_val_comp'); 
							$act_cod_ramo 	=	$oIfx->f('act_cod_ramo'); 
							$act_nom_prop 	=	$oIfx->f('act_nom_prop');
							$act_cod_area 	=	$oIfx->f('act_cod_area'); 
							$act_des_ubic 	=	$oIfx->f('act_des_ubic'); 
							$act_cod_pres 	=	$oIfx->f('act_cod_pres'); 
							$act_path_foto 	=	$oIfx->f('act_path_foto'); 
							$act_cod_rela 	=	$oIfx->f('act_cod_rela');
							if (empty($act_usua_act)) {
								$act_usua_act = null;
							}
							if (empty($act_cod_ramo)) {
								$act_cod_ramo = null;
								}
							if (empty($act_cod_rela)) {
								$act_cod_rela = null;
							}
							if (empty($act_vres_act)){
								$act_vres_act = 0;
							}
						}
					}
					$oIfx->Free();		
					// CREAR NUEVO ACTIVO
					$sql = "insert into saeact (act_cod_act, tact_cod_tact, act_cod_empr, act_cod_sucu, sgac_cod_sgac, eact_cod_eact, act_clave_act,
												act_nom_act, act_marc_act, act_colr_act, act_seri_act, act_mode_act, act_fcmp_act, act_refr_act,
												act_comp_act, act_vutil_act, act_vres_act, act_part_act, act_tcam_act, act_cant_act, tdep_cod_tdep,
												gact_cod_gact, sgac_cod_empr, ccos_cod_ccos, act_prov_act, act_fiman_act, act_foto_act, act_fdep_act,
												act_fcorr_act, act_ext_act, act_gar_act, act_val_comp, act_cod_ramo, act_nom_prop,
												act_des_ubic, act_cod_pres, act_path_foto, act_clave_padr, act_est_reva)
										values	(0, $tact_cod_tact, $act_cod_empr, $act_cod_sucu, '$sgac_cod_sgac', $eact_cod_eact, '$act_clave_act',
												'$act_nom_act', '$act_marc_act', '$act_colr_act', '$act_seri_act', '$act_mode_act', '$act_fcmp_act', '$act_refr_act',
												'$act_comp_act', $vidaUtilRevalo, $act_vres_act, $act_part_act, $act_tcam_act, $act_cant_act, '$tdep_cod_tdep',
												'$gact_cod_gact', $act_cod_empr, '$ccos_cod_ccos', '$act_prov_act', '$act_fiman_act', '$act_foto_act', '$act_fdep_act',
												'$act_fcorr_act', $act_ext_act, $act_gar_act, $valorRevalMonLoc, '$act_cod_ramo', '$act_nom_prop',
												'$act_des_ubic', '$act_cod_pres', '$act_path_foto', '$act_clave_act', '$estadoRevalorizado')";
					$oIfx->QueryT($sql);	
					$sql = "select max(act_cod_act) as activo
								from saeact 
								where act_cod_empr = '$empresa'
								and act_cod_sucu = '$sucursal'";		
					$codigoNuevo = consulta_string($sql,'activo', $oIfx,0);
					
					// GENERAR INDICES
					f_genera_index($codigoNuevo, $act_fcmp_act, $vidaUtilRevalo, $valorRevalMonLoc);
					// INSERTAR TABLA DE REVALORIZACION	
					$sql = "insert into saereva (reva_cod_reva, reva_cod_acti, reva_cod_empr, 
												 reva_cod_sucu, reva_fec_reva, reva_val_reva, 
												 reva_vid_util, reva_val_comp, reva_cod_usua, reva_cod_asto)
										values  (0, 			$codigoActivo, $empresa, 
												$sucursal, 		'$fechaRevalo',  $valorRevalo, 
												$vidaUtilRevalo,$valorCompra,  '$user_ifx', $asto_cod)";
					$oIfx->QueryT($sql);
					
					// INSERTAR CUSTODIO POR ACTIVO FIJO SAECXA
					$sql = "select empl_cod_empl, cxa_ubic_cxa, act_cod_empr, act_cod_sucu,
							estr_cod_estr, cxa_fech_cxa, cxa_obs_cxa, cxa_ban_cxa, cxa_est_cxa
							from saecxa
							where act_cod_act = $codigoActivo
							and act_cod_empr = $empresa
							and act_cod_sucu = $sucursal";
					if ($oIfx->Query($sql)) {
						if ($oIfx->NumFilas() > 0) {
							do{
								$empl_cod_empl = $oIfx->f('empl_cod_empl');
								$cxa_ubic_cxa  = $oIfx->f('cxa_ubic_cxa');
								$act_cod_empr  = $oIfx->f('act_cod_empr');
								$act_cod_sucu  = $oIfx->f('act_cod_sucu');
								$estr_cod_estr = $oIfx->f('estr_cod_estr');
								$cxa_fech_cxa  = $oIfx->f('cxa_fech_cxa');
								$cxa_obs_cxa   = $oIfx->f('cxa_obs_cxa');
								$cxa_ban_cxa   = $oIfx->f('cxa_ban_cxa');
								$cxa_est_cxa   = $oIfx->f('cxa_est_cxa');
								if (empty($cxa_ban_cxa)){
									$cxa_ban_cxa = null;
								}
								if (empty($cxa_est_cxa)){
									$cxa_est_cxa = null;
								}

								$sql = " insert into saecxa (cxa_cod_cxa,   empl_cod_empl,	   cxa_ubic_cxa,	 act_cod_act,   act_cod_empr,
															 act_cod_sucu,  estr_cod_estr,	   cxa_fech_cxa,  	 cxa_obs_cxa)
													 values (0			 ,  '$empl_cod_empl',  '$cxa_ubic_cxa',  $codigoNuevo, $act_cod_empr,
															 $act_cod_sucu, '$estr_cod_estr',  '$cxa_fech_cxa',  '$cxa_obs_cxa')";
								$oIfxA->QueryT($sql);						
							}while($oIfx->SiguienteRegistro());	
							$oIfx->Free();			
						}
					}
					// PARTES Y PIEZAS DEL ACTIVO 
					
					//echo $sql;
					$sql = "update saeasto set asto_est_asto = 'MY',
							asto_vat_asto = $valorRevalMonLoc
							where asto_cod_asto = '$asto_cod' and
							asto_cod_empr = $empresa and
							asto_cod_sucu = $sucursal and
							asto_cod_ejer = $idejer and
							asto_num_prdo = $idprdo";						
					$oIfx->QueryT($sql);										
					$oReturn->alert('Proceso Terminado con Exito');
					$oIfx->QueryT('COMMIT WORK;');	
				}	
		} else{
			$oReturn->alert('Activo Fijo ya Tiene una Revalorizacion a esta Fecha');
		}		
    } catch (Exception $e) {
        $oIfx->QueryT('ROLLBACK');       
        $oReturn->alert($e->getMessage());
    }
    return $oReturn;	
}

// GENERAR INDICES SAEMET 
function f_genera_index($codigoActivo, $fechaInicio, $vidaUtil, $valorAdepreciar){
   //Definiciones
    global $DSN, $DSN_Ifx;

    if (session_status() !== PHP_SESSION_ACTIVE) {session_start();}

    $oCon = new Dbo ( );
    $oCon->DSN = $DSN;
    $oCon->Conectar();

    $oIfx = new Dbo ( );
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();
 
	$oIfxA = new Dbo ( );
    $oIfxA->DSN = $DSN_Ifx;
    $oIfxA->Conectar();

    $oReturn = new xajaxResponse ( );
	//echo $fechaInicio; exit;
    //variables de sesion
    $array       	= $_SESSION['ARRAY_PINTA'];
    $usuario_web 	= $_SESSION['U_ID'];
	$user_ifx    	= $_SESSION['U_USER_INFORMIX'];
	$empresa     	= $_SESSION['U_EMPRESA'];
	$sucursal    	= $_SESSION['U_SUCURSAL'];

	//variables formulario
	$fecha_compra       =   $fechaInicio;
	$valor_compra 		= 	$valorAdepreciar;
	$anio_compra 		= 	substr($fecha_compra, 6, 4);
	$mes_compra 		= 	substr($fecha_compra, 0, 2);
	$dia_compra 		= 	substr($fecha_compra, 3, 2);	
	
	$fechaServer    	= date("Y-m-d");
	//$fechaRevalo    = fecha_informix_func($fechaInicio);
	
	//echo $fechaRevalo; exit;
	// TIPO DE DEPRECIACION
	$sql_activo = "select tdep_cod_tdep, act_vres_act
					from saeact
					where act_cod_act = $codigoActivo
					and act_cod_empr = $empresa
					and act_cod_sucu = $sucursal";
	//echo $sql_activo; exit;					
	
	$tipo_depreciacion 	= consulta_string($sql_activo,'tdep_tip_val', $oIfx,0);	
	$valor_residual 	= consulta_string($sql_activo,'act_vres_act', $oIfx,0);	
	$sql_tipo = "select tdep_tip_val 
				from saetdep
				where tdep_cod_tdep = '$tipo_depreciacion'
				and tdep_cod_empr = $empresa";
	$intervalo = consulta_string($sql_tipo,'tdep_tip_val', $oIfx,0);							
	if (empty($intervalo)) {
		$intervalo = 'M';				
	}
	if ($intervalo == 'M'){
		$nMeses = 12;
	} else{
		$nMeses = 1;
	}
		
	if ( $vidaUtil == 0 ){
		continue;
	}
	$num_registros =  round($vidaUtil, 2) * $nMeses;
	// FECHA INICIAL
	if ($dia_compra > 15){
		if ($mes_compra == 12){
			$mes = 01;
			$anio = $anio_compra + 1;			
		}else{
			$mes = $mes_compra + 1;
			$anio = $anio_compra;
			if ($mes < 10){
				$mes = '0'.$mes;
			}
		}
	}else{
		$mes = $mes_compra;
		$anio = $anio_compra;
		if ($mes < 10){
				$mes = '0'.$mes;
			}
	}	
	// VALOR A DEPRECIAR
	$compra_origen = $valor_compra;
	$valor_compra   = round(($valor_compra - $valor_residual) / $num_registros, 2);				
	$porcentaje_dep =  (1/$num_registros); 
	$ultimaFila = $num_registros - 1;
	//echo $num_registros.' '.$ultimaFila .' '.$valor_compra.' '.$compra_origen; exit;
	$ajuste = 0;
	 for($i = 0; $i < $num_registros; $i++){
		$fecha_desde = $mes.'/01/'.$anio;
		$ultimo_dia = date("d", (mktime(0, 0, 0, $mes + 1, 1, $anio) - 1));
		$fecha_hasta = $mes.'/'.$ultimo_dia.'/'.$anio;
		//echo $fecha_desde.' '.$fecha_hasta; exit;
		if ($i == $ultimaFila){
			$valor_compra = ($compra_origen - $valor_residual) - $ajuste;
			//echo $valor_compra; exit;
		}
		$ajuste = $ajuste + $valor_compra;
		$sql_met = "insert into saemet
					values ($anio,'$fecha_desde', '$fecha_hasta', $empresa, $codigoActivo, $empresa, $sucursal, $porcentaje_dep, $valor_compra, 0, 0)";
		//echo $sql_met; exit;
		$oIfx->QueryT($sql_met);
		if ($mes == 12) { 
			$mes = 1;						
			$anio = $anio + 1;
		}else{ 						
			$mes++; 
		}	
	}					
    return $oReturn;		
}


function f_filtro_subgrupo($aForm, $data){
    //Definiciones
    global $DSN, $DSN_Ifx;

    if (session_status() !== PHP_SESSION_ACTIVE) {session_start();}

    $oCon = new Dbo ( );
    $oCon->DSN = $DSN;
    $oCon->Conectar();

    $oIfx = new Dbo ( );
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    $oReturn = new xajaxResponse ( );

    //variables formulario
    $codigoGrupo = $aForm['cod_grupo'];
	$empresa = $_SESSION['U_EMPRESA'];
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

function f_filtro_activos($aForm, $data){
    //Definiciones
    global $DSN, $DSN_Ifx;
    if (session_status() !== PHP_SESSION_ACTIVE) {session_start();}

    $oCon = new Dbo ( );
    $oCon->DSN = $DSN;
    $oCon->Conectar();

    $oIfx = new Dbo ( );
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    $oReturn = new xajaxResponse ( );

    //variables formulario
    $subgrupo = $aForm['cod_subgrupo'];
	$empresa = $_SESSION['U_EMPRESA'];
	$sucursal = $_SESSION['U_SUCURSAL'];

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
    $oReturn->assign('cod_activo', 'value', $data);
	//echo 'Hola',$data;
	//exit;
    return $oReturn;
}

// INFORMACION DEL ACTIVO
function f_informacion_activo($aForm){
  //Definiciones
    global $DSN, $DSN_Ifx;

    if (session_status() !== PHP_SESSION_ACTIVE) {session_start();}
    $oIfx = new Dbo ( );
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();
	
	$oIfxA = new Dbo ( );
    $oIfxA->DSN = $DSN_Ifx;
    $oIfxA->Conectar();
	
 	 $oReturn = new xajaxResponse ( );
	$empresa = $_SESSION['U_EMPRESA'];
	$sucursal = $_SESSION['U_SUCURSAL'];	
	$codigoActivo = $aForm['cod_activo'];
	// LISTA DE INDICES DE ACTIVOS
	$sql = "select saeact.act_clave_act,   
			 saeact.act_nom_act,   
			 saeact.act_fcmp_act,   
			 saeact.act_vutil_act,   
			 COALESCE(saeact.act_vres_act, '0') as act_vres_act,   
			 saeact.act_val_comp,
			 (saeact.act_val_comp - COALESCE(saeact.act_vres_act, 0)) as neto
		from saeact   
		where saeact.act_cod_act = $codigoActivo
		and saeact.act_cod_empr = $empresa
		and saeact.act_cod_sucu = $sucursal";
	//echo $sql; exit;
	if($oIfxA->Query($sql)){
		if($oIfxA->NumFilas() > 0){	
			// ULTIMOS DATOS DE DEPRECIACION
			$sql_info = "select max(cdep_fec_depr) as cdep_fec_depr
			from saecdep
			where cdep_cod_acti = $codigoActivo
			and act_cod_empr = $empresa
			and act_cod_sucu = $sucursal";
			$fecha_depre = consulta_string($sql_info, 'cdep_fec_depr', $oIfx, '');
			//echo $fecha_depre; exit;
			$sql_ficha = "select cdep_dep_acum, cdep_gas_depn 
						from saecdep 
						where saecdep.cdep_cod_acti = $codigoActivo
						and saecdep.act_cod_empr  = $empresa
						and saecdep.act_cod_sucu  = $sucursal						
						and saecdep.cdep_fec_depr = '$fecha_depre'";
			//echo $sql_ficha; exit;		
			
			$dep_acum = consulta_string($sql_ficha, 'cdep_dep_acum', $oIfx, '');
			$gas_depr = consulta_string($sql_ficha, 'cdep_gas_depn', $oIfx, '');
			//echo $dep_acum; exit;
			$html.='<table class="table table-bordered table-striped table-condensed" style="width: 70%; margin-bottom: 0px;">
					<tr>
						<td class="bg-primary" align = center colspan="10"> INFORMACION ACTUAL DEL ACTIVO FIJO </td>
					</tr>
					<tr class="msgFrm">					
						<td class="bg-primary"> Clave </td>
						<td class="bg-primary"> Nombre </td>
						<td class="bg-primary"> Vida Util </td>
						<td class="bg-primary"> Valor Residual</td>
						<td class="bg-primary"> Valor Compra </td>
						<td class="bg-primary"> Valor Neto </td>
						<td class="bg-primary"> Depr. Anterior </td>
						<td class="bg-primary"> Gasto Depr</td>
						<td class="bg-primary"> Depr. Acumulada</td>
						<td class="bg-primary"> Valor por Depreciar</td>
					</tr> ';					
			do{
				$clave   	   =  $oIfxA->f('act_clave_act');
				$nombre        =  $oIfxA->f('act_nom_act');
				$fecha_compra  =  $oIfxA->f('act_fcmp_act');
				$vida_util     =  $oIfxA->f('act_vutil_act');
				$valor_resi    =  $oIfxA->f('act_vres_act'); 
				$valor_compra  =  $oIfxA->f('act_val_comp');
				$valor_neto    =  $oIfxA->f('neto');
				$valor_por_depre = $valor_neto - ($dep_acum +  $gas_depr);
				$depr_acumulada_actual = $dep_acum + $gas_depr;
				$html.='<tr>
							<td>'.$clave.' </td> 
							<td>'.$nombre.' </td> 
							<td align = right>'.$vida_util.' </td> 
							<td align = right>'.$valor_resi.' </td>
							<td align = right>'.$valor_compra.' </td>
							<td align = right>'.$valor_neto.' </td>
							<td align = right>'.$dep_acum.' </td>
							<td align = right>'.$gas_depr.' </td>
							<td align = right>'.$depr_acumulada_actual.' </td>
							<td align = right>'.$valor_por_depre.' </td>
						</tr>';											
			}while($oIfxA->SiguienteRegistro());			
			$html.= '</table>';
		}
	}
	$oReturn->assign("reporte_activo","innerHTML", $html);
	$oIfxA->Free();
	return $oReturn;
}

/* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */
/* PROCESO DE REQUEST DE LAS FUNCIONES MEDIANTE AJAX NO MODIFICAR */
$xajax->processRequest();
/* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */
?>
