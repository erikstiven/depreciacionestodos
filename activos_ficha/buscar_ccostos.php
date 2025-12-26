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
    $id = $_REQUEST['id'];
    if (isset($_REQUEST['nomCuenta'])){
        $nomCuenta = $_REQUEST['nomCuenta'];
        if(($nomCuenta!='0')&&($nomCuenta!='')){
            $con_nom=" and ((ccosn_cod_ccosn like upper('%$nomCuenta%')) or (ccosn_nom_ccosn like '%$nomCuenta%'))";
        }else{
            $con_nom=null;
        }
    }
    else{
        $nomCuenta = null;
    }

    $tabla = '';
		$sql = "select ccosn_cod_ccosn, ccosn_nom_ccosn
				from saeccosn
				where (ccosn_cod_empr = $idempresa)
				$con_nom";
			//	echo $sql;exit;
	$i=1;
    if($oIfx->Query($sql)){
    	if($oIfx->NumFilas() > 0){
    		do{
                $ccosn_cod_ccosn   = $oIfx->f('ccosn_cod_ccosn');
                $ccosn_nom_ccosn   = htmlentities($oIfx->f('ccosn_nom_ccosn'));
                $ccosn_nom_ccosn   = str_replace("'", " ", $ccosn_nom_ccosn);
                $ccosn_nom_ccosn   = str_replace('"', ' ', $ccosn_nom_ccosn);
								
				$img = '<div align=\"center\"> <div class=\"btn btn-success btn-sm\" onclick=\"bajar_ccostos(\'' . $ccosn_cod_ccosn . '\', \'' . $id . '\')\"><span class=\"glyphicon glyphicon-ok\"><span></div> </div>';
    			//echo $nomPais;exit;
				$tabla.='{
				  "codigo":"'.$ccosn_cod_ccosn.'",
				  "nombre":"'.$ccosn_nom_ccosn.'",
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