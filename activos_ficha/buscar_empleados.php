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
            $con_nom=" and ((empl_cod_empl like upper('%$nomCuenta%')) or (empl_ape_nomb like '%$nomCuenta%'))";
        }else{
            $con_nom=null;
        }
    }
    else{
        $nomCuenta = null;
    }

    $tabla = '';
		$sql = "   SELECT saeempl.empl_cod_empl,   
                         saeempl.empl_ape_nomb,   
                         saeesem.esem_cod_estr                              
                    FROM saeempl,   
                         saeesem                              
                   WHERE saeesem.esem_cod_empl = saeempl.empl_cod_empl and  
                         saeesem.esem_cod_empr = saeempl.empl_cod_empr and  
                         (esem_fec_sali is null or esem_fec_sali = ' ') and
                         saeempl.empl_cod_empr = $idempresa and
                         saeempl.empl_cod_eemp = 'A'
                $con_nom         
                order by 1";
			//	echo $sql;exit;
	$i=1;
    if($oIfx->Query($sql)){
    	if($oIfx->NumFilas() > 0){
    		do{
                $empl_cod_empl   = $oIfx->f('empl_cod_empl');
                $cargo           = $oIfx->f('esem_cod_estr');
                $empl_ape_nomb   = htmlentities($oIfx->f('empl_ape_nomb'));
				$empl_ape_nomb   = str_replace("'", " ", $empl_ape_nomb);
				$img = '<div align=\"center\"> <div class=\"btn btn-success btn-sm\" onclick=\"bajar_empleados(\'' . $empl_cod_empl . '\', \'' . $id . '\', \'' . $cargo . '\', \'' . $empl_ape_nomb . '\')\"><span class=\"glyphicon glyphicon-ok\"><span></div> </div>';
    			//echo $nomPais;exit;
				$tabla.='{
				  "codigo":"'.$empl_cod_empl.'",
				  "nombre":"'.$empl_ape_nomb.'",
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