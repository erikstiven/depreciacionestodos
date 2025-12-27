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
    
    if (isset($_REQUEST['nomGrupo'])){
        $nomGrupo = $_REQUEST['nomGrupo'];
		if(($nomGrupo!='0')&&($nomGrupo!='')){
			$con_nom="and (gact_des_gact like upper ('%$nomGrupo%'))";
		}else{
			$con_nom=null;
		}
	}
    else{
        $nomGrupo = null;
		$con_nom = null;
	}

    $tabla = '';

    $sql = "select gact_cod_gact, gact_des_gact
    		from saegact
			where gact_cod_empr = $idempresa
            $con_nom";
			
			//echo $sql;exit;
	$i=1;
    if($oIfx->Query($sql)){
    	if($oIfx->NumFilas() > 0){
    		do{
				$codigo_grupo = $oIfx->f('gact_cod_gact');
				$nombre_grupo = $oIfx->f('gact_des_gact');

				$img = '<div align=\"center\"><div class=\"btn btn-success btn-sm\" onclick=\"seleccionaItem(\'' . $codigo_grupo . '\', \'' . $nombre_grupo . '\')\"><span class=\"glyphicon glyphicon-ok\"><span></div> </div>';
    			$tabla.='{
				  "codigo_grupo":"'.$codigo_grupo.'",
				  "nombre_grupo":"'.$nombre_grupo.'",
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
