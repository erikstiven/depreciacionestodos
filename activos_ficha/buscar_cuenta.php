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
            $con_nom=" and ((cuen_cod_cuen like upper('%$nomCuenta%')) or (cuen_nom_cuen like '%$nomCuenta%'))";
        }else{
            $con_nom=null;
        }
    }
    else{
        $nomCuenta = null;
    }

    $tabla = '';
		$sql = "select cuen_cod_cuen, cuen_nom_cuen
				from saecuen
				where (cuen_cod_empr = $idempresa)
				$con_nom";
				//echo $sql;exit;
	$i=1;
    if($oIfx->Query($sql)){
    	if($oIfx->NumFilas() > 0){
    		do{
                $cuen_cod_cuen   = $oIfx->f('cuen_cod_cuen');
                $cuen_nom_cuen   = htmlentities($oIfx->f('cuen_nom_cuen'));
                $cuen_nom_cuen   = str_replace("'", " ", $cuen_nom_cuen);
				$img = '<div align=\"center\"> <div class=\"btn btn-success btn-sm\" onclick=\"bajar_cuentas(\'' . $cuen_cod_cuen . '\', \'' . $id . '\')\"><span class=\"glyphicon glyphicon-ok\"><span></div> </div>';
    			//echo $nomPais;exit;
				$tabla.='{
				  "codigo":"'.$cuen_cod_cuen.'",
				  "nombre":"'.$cuen_nom_cuen.'",
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