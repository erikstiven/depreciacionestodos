<?php

require ("_Ajax.comun.php"); // No modificar esta linea
/* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
  // S E R V I D O R   A J A X //
  :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */

/* * ******************************************* */
/* FCA01 :: GENERA INGRESO TABLA PRESUPUESTO  */
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

    $oReturn = new xajaxResponse ( );

    //variables de sesion
    $idempresa = $_SESSION['U_EMPRESA'];

    //variables del formulario
    $empresa = $aForm['empresa'];

    if (empty($empresa)) {
        $empresa = $idempresa;
    }

    switch ($sAccion) {
        case 'nuevo':
            $ifu->AgregarCampoTexto('codigo', 'C&oacutedigo|left', true, '', 150, 150); 
			$ifu->AgregarComandoAlEscribir('codigo', 'form1.codigo.value=form1.codigo.value.toUpperCase()');
            $ifu->AgregarCampoTexto('descripcion', 'Estado Descripci&oacuten|left', true, '', 150, 150); 			
			$ifu->AgregarComandoAlEscribir('descripcion', 'form1.descripcion.value=form1.descripcion.value.toUpperCase()');
			$ifu->AgregarCampoTexto('metodo', 'M&eacutetodo|left', true, '', 150, 150); 
			$ifu->AgregarCampoLista('tiempo', 'Forma de C&aacutelculo|left', true, '', 150, 150);
			$ifu->AgregarOpcionCampoLista('tiempo', 'DIARIO', 'D');
			$ifu->AgregarOpcionCampoLista('tiempo', 'MENSUAL', 'M');
			$ifu->AgregarOpcionCampoLista('tiempo', 'ANUAL', 'A');
			

    }   
    $table_op .='<table class="table table-bordered table-striped table-condensed" style="width: 100%; margin-bottom: 0px;" >
					<tr> 
						<td colspan="4" align="center" class="bg-primary">CONFIGURACI&OacuteN CREACI&OacuteN TIPOS DE DEPRECIACION</td>
					</tr>
                    <tr>
                        <td colspan = "4">    
							<div class="btn-group">
								<div class="btn btn-primary btn-sm" onclick="genera_cabecera_formulario();">
									<span class="glyphicon glyphicon-file"></span>
										Nuevo
								</div>
								<div class="btn btn-primary btn-sm" onclick="guardar();" id = "guardar">
									<span class="glyphicon glyphicon-floppy-disk"></span>
									Guardar
								</div>	
								<div class="btn btn-primary btn-sm" onclick="eliminar();" id = "eliminar">
									<span class="glyphicon glyphicon-remove"></span>
									Eliminar
								</div>								
							</div>
                        </td>                   
                    </tr>
                    <tr class="msgFrm">
                        <td colspan="4" align="center">Los campos con * son de ingreso obligatorio</td>
                    </tr>
                    <tr>					
                        <td>' . $ifu->ObjetoHtmlLBL('codigo') . '</td>
						<td>' . $ifu->ObjetoHtml('codigo') . '</td>
                        <td>' . $ifu->ObjetoHtmlLBL('descripcion') . '</td>                        
                        <td>' . $ifu->ObjetoHtml('descripcion') . '</td>
                    </tr>
                    <tr>					
                        <td>' . $ifu->ObjetoHtmlLBL('metodo') . '</td>
						<td>' . $ifu->ObjetoHtml('metodo') . '</td>
                        <td>' . $ifu->ObjetoHtmlLBL('tiempo') . '</td>
						<td>' . $ifu->ObjetoHtml('tiempo') . '</td>
                    </tr>
					<tr>
						<td>
							<label for="tipo">Depreciacion desde la fecha de Compra</label>
						</td>	
						<td>
							<input type="checkbox" name="tipo" id="tipo" value="S">							
						</td>
					</tr>
                  </table>';
    $table_op .= '</fieldset>';

    $oReturn->assign("divFormularioTipoDepr", "innerHTML", $table_op);

    return $oReturn;
}

function guardar($aForm = '') {
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

    //variables de sesion
    $array = ($_SESSION['ARRAY_PINTA']);
    $usuario_web = $_SESSION['U_ID'];
	$idempresa = $_SESSION['U_EMPRESA'];

    //variables formulario
    $codigo 	 =  $aForm['codigo'];	
	$descripcion =  $aForm['descripcion'];	
	$metodo      =  $aForm['metodo'];
	$calculo     =  $aForm['tiempo'];
	$tipo		 =  $aForm['tipo'];
	switch ($calculo){
		case "DIARIO":
			$calculo = 'D';
			break;
		case "MESUAL":
			$calculo = 'M';
			break;
		case "ANUAL":	
			$calculo = 'A';		
			break;
	}	
	if (empty($tipo)){
		$tipo = 'N';
	}
	
	//$oReturn->alert($continente);
    try {
        $oIfx->QueryT('BEGIN');
		$sql = "select count(*) as contador from saetdep where tdep_cod_tdep = '$codigo' and tdep_cod_empr = $idempresa";
		$contador = consulta_string($sql,'contador', $oIfx,0);
		if ($contador > 0){
			$sql = "update saetdep 
					set tdep_desc_tdep = '$descripcion', tdep_tip_meto = '$metodo', tdep_tip_val = '$calculo', tdep_dep_fcom = '$tipo' 
					where tdep_cod_tdep = '$codigo'
					and tdep_cod_empr = $idempresa";
			$mensaje = "Datos Modificados";				
		} else {
			$sql = "insert into saetdep values ('$codigo', '$descripcion', $idempresa, '$metodo', '$calculo', '$tipo')";
			$mensaje = "Datos Grabados";
		}
		$oIfx->QueryT($sql);
		$oReturn->alert($mensaje);
		$oReturn->script("recarga();"); 
		$oIfx->QueryT('COMMIT WORK;');
    } catch (Exception $e) {
        $oIfx->QueryT('ROLLBACK');
        $oReturn->alert($e->getMessage());
    }
    return $oReturn;
}

function eliminar($aForm = ''){
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

    //variables de sesion
    $array = ($_SESSION['ARRAY_PINTA']);
    $usuario_web = $_SESSION['U_ID'];
	$idempresa = $_SESSION['U_EMPRESA'];

    //variables formulario
    $codigo = $aForm['codigo'];	
	$descripcion = $aForm['descripcion'];	
	$metodo      =  $aForm['metodo'];
	$calculo     =  $aForm['tiempo'];

	//$oReturn->alert($continente);
    try {
        $oIfx->QueryT('BEGIN');
			$sql = "select count(*) as contador from saetdep where tdep_cod_tdep =  '$codigo'";
			$contador = consulta_string($sql,'contador', $oIfx,0);
			if ($contador > 0){
				$mensaje = "No se puede Eliminar, hay Activos Fijos con este Tipo de Depreciacion";
			} else
			{
			$sql = "delete from saetdep where tdep_cod_tdep =  '$codigo' and tdep_cod_empr = $idempresa";
			$mensaje = "Datos Borrados";
			}		
		$oIfx->QueryT($sql);
		$oReturn->alert($mensaje);
		$oReturn->script("recarga();"); 
		$oReturn->assign("codigo","value","");
		$oReturn->assign("descripcion","value","");
		$oReturn->assign("metodo","value","");
		$oReturn->assign("calculo","value","");
		
        $oIfx->QueryT('COMMIT WORK;');
    } catch (Exception $e) {
        $oCon->QueryT('ROLLBACK');
        $oReturn->alert($e->getMessage());
    }


    return $oReturn;	
}

/* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */
/* PROCESO DE REQUEST DE LAS FUNCIONES MEDIANTE AJAX NO MODIFICAR */
$xajax->processRequest();
/* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */
?>