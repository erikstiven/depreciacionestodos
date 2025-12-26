<?
include_once('../../Include/config.inc.php');
include_once(path(DIR_INCLUDE).'conexiones/db_conexion.php');
include_once(path(DIR_INCLUDE).'comun.lib.php');

if (session_status() !== PHP_SESSION_ACTIVE) {session_start();}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<link rel="stylesheet" type = "text/css" href="<?=$_COOKIE["JIREH_INCLUDE"]?>css/general.css">
<link href="<?=$_COOKIE["JIREH_INCLUDE"]?>Clases/Formulario/Css/Formulario.css" rel="stylesheet" type="text/css"/>
<link rel="stylesheet" type = "text/css" href="css/estilo.css">

<!--CSS--> 
<link rel="stylesheet" type="text/css" href="<?=$_COOKIE["JIREH_INCLUDE"]?>css/bootstrap-3.3.7-dist/css/bootstrap.min.css" media="screen" />
<link rel="stylesheet" type="text/css" href="js/jquery/plugins/simpleTree/style.css" />
<link rel="stylesheet" href="media/css/bootstrap.css">
<link rel="stylesheet" href="media/css/dataTables.bootstrap.min.css">
<link rel="stylesheet" href="media/font-awesome/css/font-awesome.css">
<link type="text/css" href="css/style.css" rel="stylesheet"></link>

<!--Javascript--> 
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript" src="js/jquery.min.js"></script>
<script src="media/js/jquery-1.10.2.js"></script>
<script src="media/js/jquery.dataTables.min.js"></script>
<script src="media/js/dataTables.bootstrap.min.js"></script>          
<script src="media/js/bootstrap.js"></script>
<script type="text/javascript" language="javascript" src="<?=$_COOKIE["JIREH_INCLUDE"]?>css/bootstrap-3.3.7-dist/js/bootstrap.min.js"></script>    
<script src="media/js/lenguajeusuario_producto.js"></script>   

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>FICHA ACTIVO FIJO</title>

<script>
    function formato(){
        document.getElementById('dos').style.display= "none";
        window.print();
    }
</script>
</head>

<style type="text/css">
	#cabecera{
		border-spacing: 0;
		border-collapse: collapse;
	}
	
	#table-striped > tbody > tr:nth-of-type(odd) {
	  background-color: #f9f9f9;
	}
	
	.table-condensed > thead > tr > th,
	.table-condensed > tbody > tr > th,
	.table-condensed > tfoot > tr > th,
	.table-condensed > thead > tr > td,
	.table-condensed > tbody > tr > td,
	.table-condensed > tfoot > tr > td {
	  padding: 5px;
	}

</style>

<body>
<?
$oCnx = new Dbo ( );
$oCnx->DSN = $DSN;
$oCnx->Conectar();

$oIfx = new Dbo;
$oIfx->DSN = $DSN_Ifx;
$oIfx->Conectar();

$oIfxA = new Dbo;
$oIfxA->DSN = $DSN_Ifx;
$oIfxA->Conectar();
$empresa      = $_SESSION['U_EMPRESA'];
// $sucursal      = $_SESSION['U_SUCURSAL'];

$codigoActivo       = $_GET['codigo'];
//echo $codigoActivo;
$sql = "select act_cod_sucu from saeact where act_cod_empr = $empresa and act_cod_act = $codigoActivo ";
$sucursal = consulta_string_func($sql,'act_cod_sucu', $oIfx,''); 	
			
//////////

	$html = '';
    $sql = "select empr_nom_empr, empr_ruc_empr , empr_dir_empr, empr_conta_sn, empr_num_resu, empr_path_logo, empr_iva_empr
            from saeempr where empr_cod_empr = $empresa ";
    if ($oIfx->Query($sql)) {
        if ($oIfx->NumFilas() > 0) {
            $razonSocial = trim($oIfx->f('empr_nom_empr'));
            $ruc_empr = $oIfx->f('empr_ruc_empr');
            $dirMatriz = trim($oIfx->f('empr_dir_empr'));
            $empr_path_logo = $oIfx->f('empr_path_logo');
            if ($oIfx->f('empr_conta_sn') == 'S')
                $empr_conta_sn = 'SI';
            else
                $empr_conta_sn = 'NO';

            $empr_num_resu = $oIfx->f('empr_num_resu');
            $empr_iva_empr = $oIfx->f('empr_iva_empr');
        }
    }
	//echo $empr_path_logo; exit;
    $oIfx->Free();
	
    //  AMBIENTE - EMISION
    $sql = "select sucu_tip_ambi, sucu_tip_emis  from saesucu where sucu_cod_empr = $empresa and sucu_cod_sucu = $sucursal ";
    if ($oIfx->Query($sql)) {
        if ($oIfx->NumFilas() > 0) {
            $ambiente_sri = $oIfx->f('sucu_tip_ambi');
            $emision_sri = $oIfx->f('sucu_tip_emis');
        }
    }
    $oIfx->Free();

    if ($ambiente_sri == 1) {
        $ambiente_sri = 'PRUEBAS';
    } elseif ($ambiente_sri == 2) {
        $ambiente_sri = 'PRODUCCION';
    }

    if ($emision_sri == 1) {
        $emision_sri = 'NORMAL';
    } elseif ($emision_sri == 2) {
        $emision_sri = 'POR INDISPONIBLIDAD DEL SISTEMA';
    }

	$html .= '<div id="uno">';
    $html .= '<table align="center"  width="100%" cellspacing="1" cellpadding="0" border="0">';
    $html .= '<tr>';
    $html .= '<b><td style="font:Brandon Grotesque Regular, sans-serif; font-size:24px; height:25px; text-align:center;">';
    $html .= '<table align="center" style="border-collapse:collapse;border-color:#ddd;" width="100%" cellspacing="1" cellpadding="0" bordercolor="#000000" border="0">';
    $html .= '<tr><td>&nbsp;</td></tr>';
    $html .= '<tr><td style="font:Brandon Grotesque Regular, sans-serif; font-size:24px; height:25px; text-align:center;">' . $razonSocial . '</td></tr>';
    $html .= '<tr><td>&nbsp;</td></tr>';
    $html .= '<tr><td align="center" style="font-size: 16px;">RUC : ' . $ruc_empr . '</td></tr>';
    $html .= '<tr><td>&nbsp;</td></tr>';

    //selecciona sucursales y direcciones
    $sql_sucu = "select sucu_nom_sucu, sucu_dir_sucu from saesucu where sucu_cod_empr = $empresa and sucu_cod_sucu = $sucursal ";
    if ($oIfx->Query($sql_sucu)) {
        if ($oIfx->NumFilas() > 0) {
            do {
                $sucu_nom_sucu = $oIfx->f('sucu_nom_sucu');
                $sucu_dir_sucu = $oIfx->f('sucu_dir_sucu');

                $html .= '<tr><td align="center" style="font-size: 16px">'. htmlentities($sucu_dir_sucu) . '</td></tr>';
            } while ($oIfx->SiguienteRegistro());
        }
    }
	$hoy = date("d/m/Y H:i:s");
    $html .= '<tr><td>&nbsp;</td></tr>';
	//$html .= '<tr><td align="center" style="font-size: 12px;">Contribuyente Especial #:' . $empr_num_resu . '</td></tr>';
    $html .= '<tr><td align="center" style="font-size: 12px;">Obligado a llevar Contabilidad :' . $empr_conta_sn . '</td></tr>';
	$html .= '<tr><td>&nbsp;</td></tr>';
    $html .= ' </table>';
    $html .= '</td>';
	$html .= '</tr>';
	$html .= ' </table>';
	$html .= ' <br/>';
	$html .= ' <div align="center">Fecha Impresi&oacuten: '.$hoy.'</div>';
	//$html .= '<hr size="30px">';
	$html .= ' <br/>';
	//echo $html; 
	// LISTA DE INDICES DE ACTIVOS
	$sql = "select tact_cod_tact, act_cod_empr, act_cod_sucu, sgac_cod_sgac, eact_cod_eact, 
				act_clave_act, act_nom_act, act_marc_act, act_colr_act, act_seri_act, 
				act_mode_act, act_fcmp_act, act_refr_act, act_comp_act, act_vutil_act, 
				act_vres_act, act_part_act, act_tcam_act, act_cant_act, tdep_cod_tdep,
				gact_cod_gact, sgac_cod_empr, ccos_cod_ccos, act_prov_act, act_fiman_act, 
				act_foto_act, act_fdep_act, act_fcorr_act, act_ext_act, act_gar_act, 
				act_usua_act, act_val_comp, act_cod_ramo, act_nom_prop, act_cod_area, 
				act_des_ubic, act_cod_pres, act_cod_rela
			from saeact
			where act_cod_act = $codigoActivo
			and act_cod_empr = $empresa
			and act_cod_sucu = $sucursal";
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
			$html.='<table table width="70%"  border="1" align="center" font-family: "Verdana"; >
						<tr>
							<td align="center" colspan="4" height="25px"> <font face="verdana">FICHA ACTIVO FIJO </font></td>	
						</tr>';					
			do{
					$tact_cod_tact	=	$oIfxA->f('tact_cod_tact');
					$act_cod_empr	=	$oIfxA->f('act_cod_empr'); 
					$act_cod_sucu 	=	$oIfxA->f('act_cod_sucu');
					$sgac_cod_sgac 	=	$oIfxA->f('sgac_cod_sgac'); 
					$eact_cod_eact	=	$oIfxA->f('eact_cod_eact'); 
					$act_clave_act 	=	$oIfxA->f('act_clave_act');
					$act_nom_act 	=	$oIfxA->f('act_nom_act');
					$act_marc_act 	=	$oIfxA->f('act_marc_act');
					$act_colr_act 	=	$oIfxA->f('act_colr_act'); 
					$act_seri_act	=	$oIfxA->f('act_seri_act'); 
					$act_mode_act 	=	$oIfxA->f('act_mode_act'); 
					$act_fcmp_act 	=	$oIfxA->f('act_fcmp_act');  
					$act_refr_act 	=	$oIfxA->f('act_refr_act');
					$act_comp_act 	=	$oIfxA->f('act_comp_act'); 
					$act_vutil_act 	=	$oIfxA->f('act_vutil_act'); 
					$act_vres_act 	=	$oIfxA->f('act_vres_act'); 
					$act_part_act 	=	$oIfxA->f('act_part_act'); 
					$act_tcam_act 	=	$oIfxA->f('act_tcam_act'); 
					$act_cant_act 	=	$oIfxA->f('act_cant_act'); 
					$tdep_cod_tdep 	=	$oIfxA->f('tdep_cod_tdep');
					$gact_cod_gact 	=	$oIfxA->f('gact_cod_gact'); 
					$sgac_cod_empr 	=	$oIfxA->f('sgac_cod_empr');
					if(empty($sgac_cod_empr))
					$sgac_cod_empr=$act_cod_empr;
					$ccos_cod_ccos 	=	$oIfxA->f('ccos_cod_ccos');
					$act_prov_act 	=	$oIfxA->f('act_prov_act'); 
					$act_fiman_act 	=	$oIfxA->f('act_fiman_act'); 
					$act_foto_act 	=	$oIfxA->f('act_foto_act'); 
					$act_fdep_act 	=	$oIfxA->f('act_fdep_act');
					$act_fcorr_act 	=	$oIfxA->f('act_fcorr_act'); 
					$act_ext_act 	=	$oIfxA->f('act_ext_act');
					$act_gar_act 	=	$oIfxA->f('act_gar_act'); 
					$act_usua_act 	=	$oIfxA->f('act_usua_act');
					$act_val_comp 	=	$oIfxA->f('act_val_comp'); 
					$act_cod_ramo 	=	$oIfxA->f('act_cod_ramo'); 
					$act_nom_prop 	=	$oIfxA->f('act_nom_prop');
					$act_cod_area 	=	$oIfxA->f('act_cod_area'); 
					$act_des_ubic 	=	$oIfxA->f('act_des_ubic'); 
					$act_cod_pres 	=	$oIfxA->f('act_cod_pres'); 
					$act_cod_rela 	=	$oIfxA->f('act_cod_rela');
					// NOMBRE DE GRUPO 
					$sql_grupo = "select gact_des_gact from saegact where gact_cod_empr = $sgac_cod_empr and gact_cod_gact = '$gact_cod_gact'";
					$nobreGrupo = consulta_string($sql_grupo, 'gact_des_gact', $oIfx, '');
					
					$sql_subgrupo = "select sgac_des_sgac from saesgac where sgac_cod_empr = $sgac_cod_empr and sgac_cod_sgac = '$sgac_cod_sgac'";
					$nobreSubGrupo = consulta_string($sql_subgrupo, 'sgac_des_sgac', $oIfx, '');					
					
					$sql_estado = "select eact_desc_eact from saeeact where eact_cod_eact = $eact_cod_eact and eact_cod_empr = $empresa";
					$estado = consulta_string($sql_estado, 'eact_desc_eact', $oIfx, '');
					
					$sql_tipo = "select tact_des_tact from saetact where tact_cod_empr = $empresa and tact_cod_tact = $tact_cod_tact";					
					$tipo = consulta_string($sql_tipo, 'tact_des_tact', $oIfx, '');

						
				$html.='<tr>
							<td colspan="4" style= "width=100%">GRUPO</td>
						</tr>
						<tr>
							<td align = "left" style= "width=25%";>Grupo: </td> 
							<td align = "left" style= "width=25%";>'.$nobreGrupo.' </td> 																		
							<td align = "left" style= "width=25%";>SubGrupo: </td> 
							<td align = "left" style= "width=25%";>'.$nobreSubGrupo.' </td> 
						</tr>
						<tr>
							<td colspan="4" style= "width=100%">ACTIVO</td>
						</tr>
						<tr>
							<td align = "left" style= "width=25%";>Codigo: </td> 
							<td align = "left" style= "width=25%";>'.$codigoActivo.' </td> 
							<td align = "left" style= "width=25%";>Clave: </td> 
							<td align = "left" style= "width=25%";>'.$act_clave_act.' </td> 
						</tr>
						<tr>
							<td align = "left" style= "width=25%";>Nombre: </td> 
							<td align = "left" colspan="3" style= "width=25%";>'.$act_nom_act.' </td> 
						</tr>
						<tr>
							<td align = "left" style= "width=25%";>Valor: </td> 
							<td align = "right" style= "width=25%";>'.$act_val_comp.' </td> 
							<td align = "left" style= "width=25%";>Cantidad: </td> 
							<td align ="right" style= "width=25%";>'.$act_cant_act.' </td> 							
						</tr>	
						<tr>	
							<td align = "left" style= "width=25%";>Vida Util: </td>
							<td align = "right" style= "width=25%";>'.$act_vutil_act.' </td>
							<td align = "left" style= "width=25%";>F. Compra: </td>
							<td align = "left" style= "width=25%";>'.$act_fcmp_act.' </td>
						</tr>
						
						<tr>
							<td align = "left" style= "width=25%"; >Estado: </td>
							<td align = "left" style= "width=25%";>'.$estado.' </td>
							<td align = "left" style= "width=25%";>Tipo:</td>
							<td align = "left" style= "width=25%";>'.$tipo.' </td>
						</tr>
						<tr>
							<td colspan="4" style= "width=100%">DEPRECIACION</td>
						</tr>
						<tr>
							<td align = "left" style= "width=25%";>Tipo:</td>
							<td align = "left" style= "width=25%";>'.$tdep_cod_tdep.' </td>
							<td align = "left" style= "width=25%";>Fecha Inicio:</td>
							<td align = "left" style= "width=25%";>'.$act_fdep_act.' </td>
						</tr>
						<tr>
							<td align = "left" style= "width=25%";>Fecha Baja:</td>
							<td align = "left" style= "width=25%";>'.$act_fiman_act.' </td>
							<td align = "left" style= "width=25%";>Fecha Correcion:</td>
							<td align = "left" style= "width=25%";>'.$act_fcorr_act.' </td>							
						</tr>						
						';											
			}while($oIfxA->SiguienteRegistro());			
			$html.= '</table>';
			$html.= '<br/>';
	
		}
	}
	// tabla cuetas
	// CUENTAS DE GASTO
	$sql_cuentas = "select gasd_cod_cuen, cuen_nom_cuen, gasd_cod_ccos, gasd_val_porc
					from saegasd, saecuen
					where gasd_cod_cuen = cuen_cod_cuen
					and gasd_cod_empr = cuen_cod_empr
					and gasd_cod_acti = $codigoActivo ";
	if($oIfx->Query($sql_cuentas)){
		if($oIfx->NumFilas() > 0){	
			$html.='<table width="95%"  border="1" align="center" font-family: "Trebuchet MS", Verdana;>
					<tr>
						<td align="center" colspan="4" height="25px"> <font face="verdana"> CUENTAS DE GASTO </font></td>
					</tr>
					<tr>
						<td align = center>Codigo</td>
						<td align = center>Nombre</td>
						<td align = center>Centro Costos</td>
						<td align = center>%</td>						
					</tr>';					

			do{		
				$gasd_cod_cuen 	=	$oIfx->f('gasd_cod_cuen');
				$cuen_nom_cuen 	=	$oIfx->f('cuen_nom_cuen');
				$gasd_cod_ccos 	=	$oIfx->f('gasd_cod_ccos');
				$gasd_val_porc 	=	$oIfx->f('gasd_val_porc');	
				$html.='<tr>
							<td align = "left">'.$gasd_cod_cuen.'</td>
							<td align = "left">'.$cuen_nom_cuen.' </td>
							<td align = "left">'.$gasd_cod_ccos.' </td>							
							<td align = right>'.$gasd_val_porc.' </td>							
						</tr>';

			}while($oIfx->SiguienteRegistro());	
			$html.= '</table>';
			$html.= '<br/>';
		}
	}
	// CUSTODIOS DE ACTIVOS
	$sql_custodios = " SELECT saecxa.empl_cod_empl, saeempl.empl_ape_nomb,   
							 saeestr.estr_des_estr, saecxa.cxa_ubic_cxa,   
							 saecxa.cxa_fech_cxa, saecxa.cxa_obs_cxa  
						FROM saecxa,   
							 saeempl,   
							 saeestr  
					   WHERE ( saeempl.empl_cod_empl = saecxa.empl_cod_empl ) and  
							 ( saeempl.empl_cod_empr = saecxa.act_cod_empr ) and  
							 ( saecxa.estr_cod_estr = saeestr.estr_cod_estr ) and 
							 ( saeestr.estr_cod_empr = saecxa.act_cod_empr ) and							 
							 ( saecxa.act_cod_act = $codigoActivo ) AND  
							 ( saecxa.act_cod_empr = $empresa ) AND  
							 ( saecxa.act_cod_sucu = $sucursal )";
	//echo $sql_custodios; exit;						 
	if($oIfx->Query($sql_custodios)){
	
		if($oIfx->NumFilas() > 0){	
			$html.='<table width="95%"  border="1" align="center">
			<tr>
				<td align="center" colspan="6" height="25px" style = "font-family: Verdana;"> RESPONSABLE </td>
			</tr>
			<tr>
				<td align="center" width="5%">Codigo</td>
				<td align="center" width="10%">Nombre</td>
				<td align="center" width="20%">Cargo</td>
				<td align="center" width="20%">Ubicacion</td>
				<td align="center" width="20%">Fecha</td>
				<td align="center" width="25%">Observacion</td>				
			</tr>';					

			do{		
				$empl_cod_empl 	=	htmlentities($oIfx->f('empl_cod_empl'));
				$empl_ape_nomb 	=	htmlentities($oIfx->f('empl_ape_nomb'));
				$estr_des_estr 	=	htmlentities($oIfx->f('estr_des_estr'));
				$cxa_ubic_cxa 	=	htmlentities($oIfx->f('cxa_ubic_cxa'));
				$cxa_fech_cxa 	=	$oIfx->f('cxa_fech_cxa');
				$cxa_obs_cxa 	=	htmlentities($oIfx->f('cxa_obs_cxa'));
				$html.='<tr>
							<td align = "left"  width="5%">'.$empl_cod_empl.'</td>
							<td align = "left" width="10%">'.$empl_ape_nomb.' </td>
							<td align = "left" width="20%">'.$estr_des_estr.' </td>							
							<td align = "left" width="20%">'.$cxa_ubic_cxa.' </td>							
							<td align = "left" width="20%">'.$cxa_fech_cxa.' </td>							
							<td align = "left" width="25%">'.$cxa_obs_cxa.' </td>							
						</tr>';
			}while($oIfx->SiguienteRegistro());	
			$html.= '</table>';
			$html.= '<br/>';
		}
	}
	// MANTENIMIENTO
	$sql_manteni = " SELECT mant_caus_mant, mant_tall_mant, mant_resp_mant, mant_ref_mant, 
							mant_fini_mant, mant_fent_mant, mant_cost_mant  
						FROM saemant   							   
					   WHERE act_cod_act = $codigoActivo  AND  
							 act_cod_empr = $empresa  AND  
							 act_cod_sucu = $sucursal ";
	if($oIfx->Query($sql_manteni)){
	
		if($oIfx->NumFilas() > 0){	
			$html.='<table width="95%"  border="1" align="center">
			<tr>
				<td align = center colspan="7" > MANTENIMIENTO </td>
			</tr>
			<tr>
				<td align = center>Causa</td>
				<td align = center>Taller</td>
				<td align = center>Responsable</td>
				<td align = center>Referencia</td>
				<td align = center>F. Salida</td>				
				<td align = center>F. Ingreso</td>				
				<td align = center>Costo</td>				
			</tr>';					

			do{		
				$mant_caus_mant 	=	$oIfx->f('mant_caus_mant');
				$mant_tall_mant 	=	$oIfx->f('mant_tall_mant');
				$mant_resp_mant 	=	$oIfx->f('mant_resp_mant');
				$mant_ref_mant 	    =	$oIfx->f('mant_ref_mant');
				$mant_fini_mant 	=	$oIfx->f('mant_fini_mant');
				$mant_fent_mant 	=	$oIfx->f('mant_fent_mant');
				$mant_cost_mant 	=	$oIfx->f('mant_cost_mant');
				$html.='<tr>
							<td align = "left">'.$mant_caus_mant.'</td>
							<td align = "left">'.$mant_tall_mant.' </td>
							<td align = "left">'.$mant_resp_mant.' </td>							
							<td align = "left">'.$mant_ref_mant.' </td>							
							<td align = "left">'.$mant_fini_mant.' </td>							
							<td align = "left">'.$mant_fent_mant.' </td>							
							<td align = "left">'.$mant_cost_mant.' </td>														
						</tr>';
			}while($oIfx->SiguienteRegistro());	
			$html.= '</table>';
		}
	}
	// OTROS - ASEGUADORAS
	$sql_manteni = " SELECT saesac.sact_poli_sact, saesac.sact_fech_sact, saesac.sact_fven_sact,   
							 saesac.sact_val_sact,  saesac.sact_dedu_sact, saesac.sac_val_come,   
							 saeaseg.aseg_des_aseg  
						FROM saeaseg,   
							 saesac  
					   WHERE ( saesac.sac_cod_aseg = saeaseg.aseg_cod_aseg ) and  
							 ( saesac.act_cod_empr = saeaseg.aseg_cod_empr ) and  
							 ( saesac.act_cod_act = $codigoActivo ) AND  
							 ( saesac.act_cod_empr = $empresa) AND  
							 ( saesac.act_cod_sucu = $sucursal )";
	if($oIfx->Query($sql_manteni)){
	
		if($oIfx->NumFilas() > 0){	
			$html.='<br> </br>
					<table width="95%"  border="1" align="center">
			<tr>
				<td align = center colspan="7" > ASEGURADORAS </td>
			</tr>
			<tr>
				<td align = "center">Poliza</td>
				<td align = "center">F. Emision</td>
				<td align = "center">F. Vencimiento</td>
				<td align = "center">Valor</td>
				<td align = "center">V. Dedicible</td>				
				<td align = "center">V. Comercial</td>				
				<td align = "center">Aseguradoras</td>				
			</tr>';					

			do{		
				$sact_poli_sact 	=	$oIfx->f('sact_poli_sact');
				$sact_fech_sact 	=	$oIfx->f('sact_fech_sact');
				$sact_fven_sact 	=	$oIfx->f('sact_fven_sact');
				$sact_val_sact 	    =	$oIfx->f('sact_val_sact');
				$sact_dedu_sact 	=	$oIfx->f('sact_dedu_sact');
				$sac_val_come   	=	$oIfx->f('sac_val_come');
				$aseg_des_aseg 	    =	$oIfx->f('aseg_des_aseg');
				$html.='<tr>
							<td align = "left">'.$sact_poli_sact.'</td>
							<td align = "left">'.$sact_fech_sact.' </td>
							<td align = "left">'.$sact_fven_sact.' </td>							
							<td align = "left">'.$sact_val_sact.' </td>							
							<td align = "left">'.$sact_dedu_sact.' </td>							
							<td align = "left">'.$sac_val_come.' </td>							
							<td align = "left">'.$aseg_des_aseg.' </td>														
						</tr>';
			}while($oIfx->SiguienteRegistro());	
			$html.= '</table>';
		}
	}


//////////
//arma pdf
// $table.= '<page>';
// $table.= $html;
// $table.= '</page>';
$html .= '</div>';

$html .= '<div id="dos">
				<table width="464" border="0" align="center">
				  <tr>
					<td align="center"><label>
					  <input name="Submit2" type="submit" class="Estilo2" value="Imprimir" onclick="formato();" />
					</label></td>
				  </tr>
				</table>
		  </div>';
echo $html;
		  
?>		 

</body>
</html>