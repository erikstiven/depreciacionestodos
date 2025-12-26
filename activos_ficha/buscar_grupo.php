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
	
	// SUCURSAL
    $sql = "select sucu_cod_sucu,  sucu_nom_sucu from saesucu where sucu_cod_empr = $idempresa ";
    unset($array_sucu);
	$array_sucu = array_dato($oIfx, $sql, 'sucu_cod_sucu', 'sucu_nom_sucu');
	

    if (isset($_REQUEST['nomGrupo'])){
        $nomGrupo = $_REQUEST['nomGrupo'];
		if(($nomGrupo!='0')&&($nomGrupo!='')){
			$con_nom=" where (act_nom_act like upper ('%$nomGrupo%'))";
		}else{
			$con_nom=null;
		}
	}
    else{
        $nomGrupo = null;
	}

    $tabla = '';
	$sql = " select saeact.act_cod_act,   
					saeact.act_clave_act,   
					saegact.gact_des_gact,   
					saesgac.sgac_des_sgac,   
					saeact.act_nom_act	,		
					saeact.act_cod_sucu
			from saeact,   
					saegact,   
					saesgac  
			where saegact.gact_cod_gact = saeact.gact_cod_gact and  
					saegact.gact_cod_empr = saeact.act_cod_empr and  
					saesgac.sgac_cod_sgac = saeact.sgac_cod_sgac and 
					saeact.act_cod_empr = $idempresa 
					$nomGrupo";
	$i=1;

	//echo $sql;exit;
    if($oIfx->Query($sql)){
    	if($oIfx->NumFilas() > 0){
    		do{
                $act_cod_act   = $oIfx->f('act_cod_act');				
                $act_clave_act = $oIfx->f('act_clave_act');
				$gact_des_gact = $oIfx->f('gact_des_gact');
				$sgac_des_sgac = $oIfx->f('sgac_des_sgac');			
                $act_nom_act   = htmlentities($oIfx->f('act_nom_act'));
				$act_clave_act   = str_replace("'", " ", $act_clave_act);
                $gact_des_gact   = str_replace("'", " ", $gact_des_gact);
                $sgac_des_sgac   = str_replace("'", " ", $sgac_des_sgac);
                $act_nom_act   = str_replace('"', " ", $act_nom_act);
				$act_cod_sucu  = $array_sucu[$oIfx->f('act_cod_sucu')];	

				$img = '<div align=\"center\"> <div class=\"btn btn-success btn-sm\" onclick=\"seleccionaItem(\'' . $act_cod_act . '\',\'' . $act_clave_act . '\',\'' . $act_nom_act . '\',\'' . $sgac_des_sgac . '\')\"><span class=\"glyphicon glyphicon-ok\"><span></div> </div>';
    			//echo $nomPais;exit;
				
				$tabla.='{
				  "sucursal":"'.$act_cod_sucu.'",
				  "clave":"'.$act_clave_act.'",
				  "grupo":"'.$gact_des_gact.'",
				  "subgrupo":"'.$sgac_des_sgac.'",
				  "nombre":"'.$act_nom_act.'",				 
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