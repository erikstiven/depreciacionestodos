<?php
	
	include_once('../../Include/config.inc.php');
	include_once(path(DIR_INCLUDE).'conexiones/db_conexion.php');
	include_once(path(DIR_INCLUDE).'comun.lib.php');

	if (session_status() !== PHP_SESSION_ACTIVE) {session_start();}
    global $DSN_Ifx, $DSN;

	$oIfx = new Dbo;
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    //varibales de sesion
    $idempresa = $_SESSION['U_EMPRESA'];
    $idsucursal = $_SESSION['U_SUCURSAL'];
    
    if (isset($_REQUEST['nombreEstado'])){
        $nombreEstado = $_REQUEST['nombreEstado'];
		if(($nombreEstado!='0')&&($nombreEstado!='')){
			$con_nom=" and (tdep_desc_tdep like upper ('%$nombreEstado%'))";
		}else{
			$con_nom=null;
		}
	}
    else{
        $nombreEstado = null;
	}
    //lectura sucia
    //////////////

    $tabla = '';

    $sql = "select tdep_cod_tdep, tdep_desc_tdep, tdep_tip_meto, tdep_tip_val, tdep_dep_fcom 	
    		from saetdep
			where tdep_cod_empr = $idempresa
			$con_nom";
	//echo $sql;			
	$i=1;
    if($oIfx->Query($sql)){
    	if($oIfx->NumFilas() > 0){
    		do{
				$tdep_cod_tdep 	= $oIfx->f('tdep_cod_tdep');
				$tdep_tip_meto 	= $oIfx->f('tdep_tip_meto');
				$tdep_tip_val 	= $oIfx->f('tdep_tip_val');	
				$tdep_dep_fcom 	= $oIfx->f('tdep_dep_fcom');				
				$tdep_desc_tdep = $oIfx->f('tdep_desc_tdep');
				$tdep_desc_tdep = str_replace("'", " ", $tdep_desc_tdep);

				$img = '<div align=\"center\"> <div class=\"btn btn-success btn-sm\" onclick=\"seleccionaItem(\'' . $tdep_cod_tdep . '\', \'' . $tdep_desc_tdep . '\', \'' . $tdep_tip_meto . '\', \'' . $tdep_tip_val . '\', \'' . $tdep_dep_fcom . '\')\"><span class=\"glyphicon glyphicon-ok\"><span></div> </div>';
    			$tabla.='{
				  "codigo":"'.$tdep_cod_tdep.'",
				  "nombre":"'.$tdep_desc_tdep.'",
				  "selecciona":"'.$img.'"
				},';
				$i++;
			}while($oIfx->SiguienteRegistro());
    	}
	}

	
	$oIfx->Free();

	//eliminamos la coma que sobra
	$tabla = substr($tabla,0, strlen($tabla) - 1);

	echo '{"data":['.$tabla.']}';
	
?>