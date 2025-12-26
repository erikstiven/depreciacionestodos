<?php

require ("_Ajax.comun.php"); // No modificar esta linea
/* :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
  // S E R V I D O R   A J A X //
  :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::: */

/* * ******************************************* */
/* FACF03 :: FICHA DEL ACTIVO FIJO  */
/* * ******************************************* */

function form_empleados( $id=0, $aForm=''){
    
    global $DSN, $DSN_Ifx;
    if (session_status() !== PHP_SESSION_ACTIVE) {session_start();}

    $oIfx = new Dbo ();
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

	$oIfxA = new Dbo;
    $oIfxA->DSN = $DSN_Ifx;
    $oIfxA->Conectar();

    $oCon = new Dbo;
    $oCon->DSN = $DSN;
    $oCon->Conectar();

    $oReturn = new xajaxResponse();

       //varibales de sesion
       $idempresa = $_SESSION['U_EMPRESA'];
       $idsucursal = $_SESSION['U_SUCURSAL'];

	
    if($id!=0){
        $con_nom=" and ((empl_cod_empl like upper('%$id%')) or (empl_ape_nomb like '%$id%'))";
    }
   
	$sHtml = '<table id="tbempleados" class="table table-striped table-bordered table-hover table-condensed" style="width: 98%; margin-bottom: 0px;" align="center">';
    $sHtml .='<thead>';

		   $sHtml .='<tr>
		   <th>N.-</th>
		   <th>C&oacute;digo</th>
		   <th>Nombre</th>
           <th>Seleccionar</th>
		   </tr>';
		   $sHtml .='</thead>';
		   $sHtml.='<tbody>';


           $sql = "   SELECT saeempl.empl_cod_empl,   
           saeempl.empl_ape_nomb,   
           saeesem.esem_cod_estr                              
      FROM saeempl,   
           saeesem                              
     WHERE saeesem.esem_cod_empl = saeempl.empl_cod_empl and  
           saeesem.esem_cod_empr = saeempl.empl_cod_empr and  
           (esem_fec_sali is null) and
           saeempl.empl_cod_empr = $idempresa and
           saeempl.empl_cod_eemp = 'A'
  $con_nom         
  order by 1";

        $i=1;
        if($oIfx->Query($sql)){
            if($oIfx->NumFilas() > 0){
                do{
                $empl_cod_empl   = $oIfx->f('empl_cod_empl');
                $cargo           = $oIfx->f('esem_cod_estr');
                $empl_ape_nomb   = htmlentities($oIfx->f('empl_ape_nomb'));
                $empl_ape_nomb   = str_replace("'", " ", $empl_ape_nomb);
                $img = '<div align="center"> <div class="btn btn-success btn-sm" onclick="bajar_empleados(\'' . $empl_cod_empl . '\', \'' . $id . '\', \'' . $cargo . '\', \'' . $empl_ape_nomb . '\')"><span class="glyphicon glyphicon-ok"><span></div> </div>';
                
                $sHtml.='<tr>
                <td style=align="center">' . $i . '</td>
                <td style=align="center">' . $empl_cod_empl . '</td>
                <td style=align="center">' . $empl_ape_nomb . '</td>
                <td style=align="center">' . $img . '</td>
                </tr>';

                $i++;
                }while($oIfx->SiguienteRegistro());
            }
        }


$oIfx->Free();



	$sHtml.='</tbody>';
	$sHtml .='</table>';	
	

						
	$modal  .= '<div class="modal-dialog modal-lg">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal">&times;</button>
			<h4 class="modal-title">LISTA DE EMPLEADOS</h4>
		</div>
		<div class="modal-body">';
    $modal .= $sHtml;                
    $modal .='          </div>
                        <div class="modal-footer">
						    <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>	
                        </div>
                    </div>
                </div>
             </div>';    
	
    $oReturn->assign("myModalEmpleados", "innerHTML", $modal);


	$table='tbempleados';
	$oReturn->script("init('$table')");


    return $oReturn;
}



function genera_cabecera_formulario($sAccion = 'nuevo', $aForm = '') {
    //Definiciones
    global $DSN_Ifx, $DSN;

    if (session_status() !== PHP_SESSION_ACTIVE) {session_start();}

    $oIfx = new Dbo ( );
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    $fu = new Formulario;
    $fu->DSN = $DSN;

    $ifu = new Formulario;
    $ifu->DSN = $DSN_Ifx;

    $oReturn = new xajaxResponse ( );

    //variables de sesion
    $idempresa = $_SESSION['U_EMPRESA'];
	$fecha_servidor = date("Y-m-d");
    //variables del formulario
    $empresa        = $aForm['empresa'];
	$codigoActivo 	= $aForm['act_cod_act'];
    $clave          = $aForm['act_clave_act'];
    $nombre         = $aForm['act_nom_act'];
	
	if (empty($empresa)) {
        $empresa = $idempresa;
    }
	
	// MONEDA LOCAL
	$sql_moneda = "select pcon_mon_base from saepcon where pcon_cod_empr = $idempresa ";
	$moneda = consulta_string($sql_moneda, 'pcon_mon_base', $oIfx, '');

	// TIPO DE CAMBIO
	$sql_tcambio = "select tcam_fec_tcam, tcam_cod_tcam, tcam_val_tcam from saetcam where
					tcam_cod_mone = $moneda and
					mone_cod_empr = $idempresa and
					tcam_fec_tcam = (select max(tcam_fec_tcam) from saetcam where
					tcam_cod_mone = $moneda and
					tcam_fec_tcam <= '$fecha_servidor' and
					mone_cod_empr = $idempresa) ";
	//$oReturn->alert($sql_tcambio);				
	if ($oIfx->Query($sql_tcambio)) {
		if ($oIfx->NumFilas() > 0) {
			$tcambio = $oIfx->f('tcam_cod_tcam');
			$val_tcambio = $oIfx->f('tcam_val_tcam');
		} else {
			$tcambio = 0;
			$val_tcambio = 0;
		}
	}
	$_SESSION['aLabelGirdProd'] = array('Cuenta', 'Centro de Costos', '%');
    switch ($sAccion) {
        case 'nuevo':
			$ifu->AgregarCampoListaSQL('gact_cod_gact', "Grupo|left","select gact_cod_gact, gact_des_gact from saegact where gact_cod_empr = '$empresa'",true, 150, 150);
			$ifu->AgregarComandoAlCambiarValor('gact_cod_gact', 'f_filtro_subgrupo()');
            $ifu->AgregarCampoListaSQL('sgac_cod_sgac', "Subgrupo|left","",true, 150, 150);
			// ARMAR CODIGO EN FUNCION DE GRUPRO Y SUBGRUPO
			$ifu->AgregarComandoAlCambiarValor('sgac_cod_sgac', 'f_arma_codigo()');

            $ifu->AgregarCampoListaSQL('tdep_cod_tdep', "Tipo Depreciaci&oacuten|left","select tdep_cod_tdep, tdep_desc_tdep 
                                                                                  from saetdep 
                                                                                  where tdep_cod_empr =  '$empresa'",true, 150, 150);
			$ifu->AgregarComandoAlCambiarValor('tdep_cod_tdep', 'f_tipoDepreciacion()');																	  

            $ifu->AgregarCampoListaSQL('tact_cod_tact', "Tipo Activo|left","select tact_cod_tact, tact_des_tact 
                                                                                  from saetact 
                                                                                  where tact_cod_empr =  '$empresa'",true, 150, 150);
            $ifu->AgregarCampoTexto('act_clave_act', 'Clave Activo|left', true, '', 150, 150);
            $ifu->AgregarCampoTexto('act_est_reva', 'Estado Revalorizado|left', false, '', 150, 150);
			
			$ifu->AgregarCampoOculto('act_cod_act', '');			
            $ifu->AgregarCampoTexto('act_nom_act', 'Nombre|left', true, '', 715, 150);
            $ifu->AgregarCampoTexto('act_otras_esp_act', 'Otras Especificaciones|left', false, '', 715, 150);
			$ifu->AgregarComandoAlEscribir('act_nom_act', 'form1.act_nom_act.value=form1.act_nom_act.value.toUpperCase();');
            $ifu->AgregarCampoTexto('act_marc_act', 'Marca|left', false, '', 150, 150);
            $ifu->AgregarCampoTexto('act_mode_act', 'Modelo|left', false, '', 150, 150);
            $ifu->AgregarCampoTexto('act_colr_act', 'Color|left', false, '', 150, 150);

            $ifu->AgregarCampoTexto('act_seri_act', 'Serie|left', false, '', 150, 150);
            $ifu->AgregarCampoNumerico('act_cant_act', 'Cantidad|left', true, '', 150, 150);
            $ifu->AgregarCampoNumerico('act_val_comp', 'Costo de Compra|left', true, '', 150, 150);

            $ifu->AgregarCampoTexto('act_prov_act', 'Proveedor|left', false, '', 425, 150);
			$ifu->AgregarComandoAlEscribir('act_prov_act', 'form1.act_prov_act.value=form1.act_prov_act.value.toUpperCase();');
            $ifu->AgregarCampoListaSQL('eact_cod_eact', 'Estado|left', "select eact_cod_eact, eact_desc_eact 
                                                                            from saeeact
                                                                            where eact_cod_empr = '$empresa'",true, 150, 150);

            $ifu->AgregarCampoFecha('act_fcmp_act', 'Fecha Compra|left',false, '', 150, 150);
			$ifu->AgregarCampoNumerico('act_vutil_act', 'Vida Util|left', true, '', 150, 150);
            $ifu->AgregarCampoFecha('act_fdep_act', 'Fecha Depreciaci&oacuten|left',true, '""', 150, 150);

            $ifu->AgregarCampoFecha('act_fcorr_act', 'Fecha Correcci&oacuten|left',false, '', 150, 150);
            $ifu->AgregarCampoNumerico('act_vres_act', 'Valor Residual|left',true, '', 150, 150);

            $ifu->AgregarCampoTexto('act_refr_act', 'Referencia|left', false, '', 445, 150);
			$ifu->AgregarComandoAlEscribir('act_refr_act', 'form1.act_refr_act.value=form1.act_refr_act.value.toUpperCase();');

            $ifu->AgregarCampoTexto('act_comp_act', 'Comprobante|left', false, '', 150, 150);
            $ifu->AgregarCampoNumerico('act_tcam_act', 'Cotizaci&oacuten|left', false, '', 150, 150);

            $ifu->AgregarCampoTexto('act_nom_prop', 'Responsable|left', false, '', 445, 150);
			$ifu->AgregarComandoAlEscribir('act_nom_prop', 'form1.act_nom_prop.value=form1.act_nom_prop.value.toUpperCase();');
           	$ifu->AgregarCampoArchivo('act_path_foto', 'foto|left',   "", false,100,50);

            $ifu->AgregarCampoLista('act_part_act', 'Partes|left', false, '', 150, 150);
			$ifu->AgregarOpcionCampoLista('act_part_act', 'SI', 1);
			$ifu->AgregarOpcionCampoLista('act_part_act', 'NO', 0);
        
            $ifu->AgregarCampoLista('act_gar_act', 'Garant&iacutea|left', false, '', 150, 150);
			$ifu->AgregarOpcionCampoLista('act_gar_act', 'SI', 1);
			$ifu->AgregarOpcionCampoLista('act_gar_act', 'NO', 0);
			
            $ifu->AgregarCampoLista('act_ext_act', 'Existencia|left', false, '', 150, 150);
			$ifu->AgregarOpcionCampoLista('act_ext_act', 'SI', 1);
			$ifu->AgregarOpcionCampoLista('act_ext_act', 'NO', 0);
			

            $ifu->AgregarCampoListaSQL('act_cod_ramo', 'Ramo|left', "select aram_cod_ramo, aram_des_ramo 
																	from saearam
																	where aram_cod_empr = '$empresa'",false, 150, 150);
            $ifu->AgregarCampoFecha('act_fiman_act', 'Fecha de Baja|left', false, '" "', 150, 150);
            $ifu->AgregarCampoTexto('act_cod_pres', 'Partida Presupuestaria|left', false, '', 150, 150);
			
			//act_flo_act, act_pla_act, act_kms_act, act_ani_act
			 $ifu->AgregarCampoTexto('act_pla_act', 'Placa|left', false, '', 150, 150);
			// $ifu->AgregarComandoAlPonerEnfoque('act_pla_act', 'this.blur()');
			 $ifu->AgregarCampoNumerico('act_kms_act', 'Kilometraje|left', false, '', 150, 150);
			// $ifu->AgregarComandoAlPonerEnfoque('act_kms_act', 'this.blur()');
			 $ifu->AgregarCampoNumerico('act_ani_act', 'A&ntildeo|left', false, '', 150, 150);
			// $ifu->AgregarComandoAlPonerEnfoque('act_ani_act', 'this.blur()');
			
	        $ifu->cCampos["gact_cod_gact"]->xValor = $cod_grupo;
			$ifu->cCampos["act_cod_act"]->xValor = 0;
			$ifu->cCampos["act_part_act"]->xValor = 0;
			$ifu->cCampos["act_gar_act"]->xValor = 0;
			$ifu->cCampos["act_ext_act"]->xValor = 1;
			$ifu->cCampos["act_tcam_act"]->xValor = $val_tcambio;
			
			// CUENTAS DE GASTO
			$ifu->AgregarCampoTexto('gasd_cod_cuen', 'Cuenta|left', false, '', 150, 150);
			$ifu->AgregarComandoAlEscribir('gasd_cod_cuen', 'autocompletar(event, id); form1.gasd_cod_cuen.value=form1.gasd_cod_cuen.value.toUpperCase();');
			$ifu->AgregarCampoTexto('gasd_cod_ccos', 'Centro de Costo|left', false, '', 150, 150);
			$ifu->AgregarComandoAlEscribir('gasd_cod_ccos', 'buca_ccostos(event, id); form1.gasd_cod_ccos.value=form1.gasd_cod_ccos.value.toUpperCase();');
			$ifu->AgregarCampoNumerico('gasd_val_porc', '%|left', false, '', 50, 150);
			$ifu->AgregarCampoLista('gasd_rev_sn', 'Tipo|left', false, '', 100, 150);
			$ifu->AgregarOpcionCampoLista('gasd_rev_sn', 'Normal', 'N');
			$ifu->AgregarOpcionCampoLista('gasd_rev_sn', 'Revalorizacion', 'S');			

			// DETALLES DE RESPONSABLES
            $ifu->AgregarCampoNumerico('cxa_codigo', 'ID|left', false,0,50,150);
            $ifu->AgregarCampoTexto('cod_empleado', 'C.I|center', false, '', 100, 150);
			$ifu->AgregarComandoAlEscribir('cod_empleado', 'buca_empleado(event, id); form1.cod_empleado.value=form1.cod_empleado.value.toUpperCase();');
            $ifu->AgregarCampoTexto('nom_empleado', 'Empleado|center', false, '', 250, 150);
            $ifu->AgregarCampoListaSQL('cargo_empleado', 'Cargo|left', "select estr_cod_estr, estr_des_estr
	                                                                from saeestr
	                                                                where estr_cod_empr = '$empresa'",false, 200, 150);
			$ifu->AgregarCampoTexto('ubicacion_empleado', 'Ubicacion|center', false, '', 150, 150);
            $ifu->AgregarComandoAlEscribir('ubicacion_empleado', 'form1.ubicacion_empleado.value=form1.ubicacion_empleado.value.toUpperCase();');
            $ifu->AgregarCampoFecha('fecha', 'Fecha|center', false, '', 90, 150);
            $ifu->AgregarCampoTexto('observacion', 'Observacones|center', false, '', 765, 150);
            $ifu->AgregarComandoAlEscribir('observacion', 'form1.observacion.value=form1.observacion.value.toUpperCase();');
			$ifu->AgregarCampoOculto('codigo_cargo', $codigo_cargo);
			// DETALLE DE PARTES
			$ifu->AgregarCampoOculto('part_cod_part', '');
			$ifu->AgregarCampoListaSQL('estado', 'Estado|left', "select eact_cod_eact, eact_desc_eact 
                                                                            from saeeact
                                                                            where eact_cod_empr = '$empresa'", false,150,150);
			$ifu->AgregarCampoTexto('part_nom_part', 'Parte|left', false,'',390,150);
			$ifu->AgregarCampoTexto('part_marc_part', 'Marca|left', false,'',250,150);
			$ifu->AgregarCampoTexto('part_modl_part', 'Modelo|left', false,'',150,150);
			$ifu->AgregarCampoTexto('part_seri_part', 'Serie|left', false,'',150,150);
			$ifu->AgregarCampoNumerico('part_cant_part', 'Cantidad|left', false,'',150,150);
			$ifu->AgregarCampoTexto('part_colr_pat', 'Color|left', false,'',150,150);			
			$ifu->AgregarCampoTexto('part_obs_part', 'Observaci&oacuten|left', false,'',740,150);
			
			// MANTENIMIENTO
			$ifu->AgregarCampoNumerico('mant_cod_mant', 'ID|left', false,'',150,150);
		    // $ifu->AgregarCampoTexto('mant_tip_movi', 'Movimiento|left', false,'',50,150);
			$ifu->AgregarCampoLista('mant_tip_movi', 'Movimiento|left', false,'',150,150);
			$ifu->AgregarOpcionCampoLista('mant_tip_movi','Ingreso', 'I');
			$ifu->AgregarOpcionCampoLista('mant_tip_movi','Salida', 'S');
		   
			$ifu->AgregarCampoNumerico('mant_sec_docu', 'No. Docu|left', false,'',150,150);
			$ifu->AgregarCampoTexto('mant_caus_mant', 'Causa|left', false,'',150,150);
			$ifu->AgregarCampoTexto('mant_ref_mant', 'Referencia|left', false,'',150,150);
			$ifu->AgregarCampoTexto('mant_tall_mant', 'Taller|left', false,'',150,150);
			$ifu->AgregarCampoFecha('mant_fini_mant', 'Fecha Inicio|left', false,'',150,150);
			$ifu->AgregarCampoFecha('mant_fent_mant', 'Fecha Entrega|left', false,'',150,150);
			$ifu->AgregarCampoNumerico('mant_cost_mant', 'Costo|left', false,'',150,150);
			$ifu->AgregarCampoTexto('mant_resp_mant', 'Responsable|left', false,'',150,150);
			$ifu->AgregarCampoTexto('mant_obs_mant', 'Observacion|left', false,'',730,150);
			
			
            // OTROS DETALLES
			$ifu->AgregarCampoOculto('sact_cod_sact', '');
			$ifu->AgregarCampoTexto('sact_poli_sact', 'No. Poliza|left', false,'',150,150);
			$ifu->AgregarCampoFecha('sact_fech_sact', 'Fecha Emision|left', false,'',150,150);
			$ifu->AgregarCampoFecha('sact_fven_sact', 'Fecha Vencimineto|left', false,'',150,150);
			$ifu->AgregarCampoNumerico('sact_val_sact', 'Monto Asegurado|left', false,'',150,150);
			$ifu->AgregarCampoNumerico('sact_dedu_sact', 'Valor Deducible|left', false,'',150,150);
			$ifu->AgregarCampoTexto('sact_obs_sact', 'Observacion|left', false,'',718,150);
			$ifu->AgregarCampoTexto('sac_num_matr', 'No.Matricula|left', false,'',150,150);
			$ifu->AgregarCampoTexto('sac_num_motr', 'No. Motor|left', false,'',150,150);
			$ifu->AgregarCampoTexto('sac_num_chsis', 'No. Chasis|left', false,'',150,150);
			$ifu->AgregarCampoTexto('sac_num_plac', 'No. Placa|left', false,'',150,150);
			$ifu->AgregarCampoNumerico('sac_val_come', 'Valor Comercial|left', false,'',150,150);
			$ifu->AgregarCampoListaSQL('sac_cod_aseg', 'Cod. Aseguradora|left', "SELECT saeaseg.aseg_cod_aseg, 
																						saeaseg.aseg_des_aseg,   
																						saeaseg.aseg_cod_empr  
																				 FROM saeaseg",false,150,150);
			// IMAGEN
			
    }
	$table_botones .='<table class="table table-bordered table-striped table-condensed" style="width: 100%; margin-bottom: 0px;" >
				<tr>
					<td colspan="6" align="right" class="text-primary" id="descripcion"></td>
				</tr>
	</table>';
	
	$oReturn->assign("divFormularioBotones", "innerHTML", $table_botones);
	
    $table_op .='<table class="table table-bordered table-striped table-condensed" style="width: 100%; margin-bottom: 0px;" >
					<tr>
						<td colspan="6">
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
								<div class="btn btn-primary btn-sm" onClick="javascript:vista_previa()">
									<span class="glyphicon glyphicon-print"></span>
									imprimir
								</div>								
								<div class="btn btn-primary btn-sm" onClick="javascript:imprime_etiqueta()">
									<span class="glyphicon glyphicon-print"></span>
									Codigo de Barras
								</div>								
							</div>
						</td>
					</tr> 
					<tr> 
						<td colspan="6" align="center" class="bg-primary">
							<span>FICHA DE ACTIVOS FIJOS</span> 
						</td>
					</tr>
                    <tr> 
                        <td colspan="6" align="center" class="info">Los campos con * son de ingreso obligatorio</td>
                    </tr>
                    <tr>
                        <td>' . $ifu->ObjetoHtmlLBL('gact_cod_gact') . '</td>	
                        <td>' . $ifu->ObjetoHtml('gact_cod_gact') . '</td>
                        <td>' . $ifu->ObjetoHtmlLBL('sgac_cod_sgac') . '</td>
                        <td>' . $ifu->ObjetoHtml('sgac_cod_sgac') .'</td>
                        <td>' . $ifu->ObjetoHtmlLBL('tdep_cod_tdep') . '</td>
                        <td>' . $ifu->ObjetoHtml('tdep_cod_tdep') .'</td>
                    </tr>
					 <tr>
                        <td>' . $ifu->ObjetoHtmlLBL('tact_cod_tact') . '</td>
                        <td>' . $ifu->ObjetoHtml('tact_cod_tact') . ' </td>
						<td>' . $ifu->ObjetoHtml('act_cod_act') . '' . $ifu->ObjetoHtmlLBL('act_clave_act') . '</td>
                        <td>' . $ifu->ObjetoHtml('act_clave_act') . ' </td>	
                        <td>' . $ifu->ObjetoHtmlLBL('act_est_reva') . '</td>
						<td><input type="text" id="act_est_reva" name="act_est_reva" readonly="readonly"></td>	
                    </tr>
                    <tr>
                        <td>' . $ifu->ObjetoHtmlLBL('act_nom_act') . '</td>
                        <td colspan="5"> ' . $ifu->ObjetoHtml('act_nom_act') . '</td>
                    </tr>
                    <tr>
                        <td>' . $ifu->ObjetoHtmlLBL('act_otras_esp_act') . '</td>
                        <td colspan="5"> ' . $ifu->ObjetoHtml('act_otras_esp_act') . '</td>
                    </tr>
                    <tr>
                        <td>' . $ifu->ObjetoHtmlLBL('act_marc_act') . '</td>	
                        <td>' . $ifu->ObjetoHtml('act_marc_act') . '</td>
                        <td>' . $ifu->ObjetoHtmlLBL('act_mode_act') . '</td>
                        <td>' . $ifu->ObjetoHtml('act_mode_act') .'</td>
                        <td>' . $ifu->ObjetoHtmlLBL('act_colr_act') . '</td>
                        <td>' . $ifu->ObjetoHtml('act_colr_act') .'</td>
                    </tr>
                    <tr>
                        <td>' . $ifu->ObjetoHtmlLBL('act_seri_act') . '</td>	
                        <td>' . $ifu->ObjetoHtml('act_seri_act') . '</td>
                        <td>' . $ifu->ObjetoHtmlLBL('act_cant_act') . '</td>
                        <td>' . $ifu->ObjetoHtml('act_cant_act') .'</td>
                        <td>' . $ifu->ObjetoHtmlLBL('act_val_comp') . '</td>
                        <td>' . $ifu->ObjetoHtml('act_val_comp') .'</td>
                    </tr>
                    <tr>
                        <td>' . $ifu->ObjetoHtmlLBL('act_prov_act') . '</td>	
                        <td colspan="3">' . $ifu->ObjetoHtml('act_prov_act') . '</td>
						<td>' . $ifu->ObjetoHtmlLBL('eact_cod_eact') . '</td>
                        <td>' . $ifu->ObjetoHtml('eact_cod_eact') .'</td>

                    </tr>
                    <tr>
                        <td>' . $ifu->ObjetoHtmlLBL('act_fcmp_act') . '</td>	
                        <td>
							<input type="date" name="act_fcmp_act" id="act_fcmp_act" value="'.date("Y-m-d").'" onchange="f_tipoDepreciacion();">
						</td>
                        <td>' . $ifu->ObjetoHtmlLBL('act_vutil_act') . '</td>
                        <td>' . $ifu->ObjetoHtml('act_vutil_act') .'</td>
                        <td>' . $ifu->ObjetoHtmlLBL('act_vres_act') . '</td>
                        <td>' . $ifu->ObjetoHtml('act_vres_act') .'</td>
                    </tr>
                    <tr>
                        <td>' . $ifu->ObjetoHtmlLBL('act_fcorr_act') . '</td>	
                        <td>
							<input type="date" name="act_fcorr_act" id="act_fcorr_act" value="">
						</td>
                        <td>' . $ifu->ObjetoHtmlLBL('act_refr_act') . '</td>
                        <td colspan="3">' . $ifu->ObjetoHtml('act_refr_act') . '</td>

                    </tr>
                    <tr>
                        <td>' . $ifu->ObjetoHtmlLBL('act_fdep_act') . '</td>
                        <td>
							<input type="date" name="act_fdep_act" id="act_fdep_act" value="">
						</td>
                        <td>' . $ifu->ObjetoHtmlLBL('act_nom_prop') . '</td>	
                        <td colspan="3">' . $ifu->ObjetoHtml('act_nom_prop') . '</td>
                    </tr>
                    <tr>
                        <td>' . $ifu->ObjetoHtmlLBL('act_comp_act') . '</td>	
                        <td>' . $ifu->ObjetoHtml('act_comp_act') . '</td>
                        <td>' . $ifu->ObjetoHtmlLBL('act_tcam_act') . '</td>
                        <td>' . $ifu->ObjetoHtml('act_tcam_act') .'</td>
                        <td>' . $ifu->ObjetoHtmlLBL('act_path_foto') .'</td>
                        <td>' . $ifu->ObjetoHtml('act_path_foto') .'</td>
                    </tr>   
                    <tr>
                        <td>' . $ifu->ObjetoHtmlLBL('act_part_act') . '</td>	
                        <td>' . $ifu->ObjetoHtml('act_part_act') . '</td>
                        <td>' . $ifu->ObjetoHtmlLBL('act_gar_act') . '</td>
                        <td>' . $ifu->ObjetoHtml('act_gar_act') .'</td>
                        <td>' . $ifu->ObjetoHtmlLBL('act_ext_act') . '</td>
                        <td>' . $ifu->ObjetoHtml('act_ext_act') .'</td>
                    </tr>
                    <tr>
                        <td>' . $ifu->ObjetoHtmlLBL('act_cod_ramo') . '</td>	
                        <td>' . $ifu->ObjetoHtml('act_cod_ramo') . '</td>
                        <td>' . $ifu->ObjetoHtmlLBL('act_fiman_act') . '</td>
                        <td>
							<input type="date" name="act_fiman_act" id="act_fiman_act" value="">
						</td>
                        <td>' . $ifu->ObjetoHtmlLBL('act_cod_pres') . '</td>
                        <td>' . $ifu->ObjetoHtml('act_cod_pres') .'</td>
                    </tr>
					<tr>
						<td><label for="est_super">Flota</td> 	
						<td><input type="checkbox" name="act_flo_act" id="act_flo_act" value="S"/></td>						
					</tr>
					<tr>
						<td>' . $ifu->ObjetoHtmlLBL('act_pla_act') . '</td>	
                        <td>' . $ifu->ObjetoHtml('act_pla_act') . '</td>
                        <td>' . $ifu->ObjetoHtmlLBL('act_kms_act') . '</td>
                        <td>' . $ifu->ObjetoHtml('act_kms_act') .'</td>
                        <td>' . $ifu->ObjetoHtmlLBL('act_ani_act') . '</td>
                        <td>' . $ifu->ObjetoHtml('act_ani_act') .'</td>
					</tr>
                  </table>
				  <hr>
				  <div id = "lis_reporte" style = "overflow: scroll; height:50%" align = center></div>
				  ';
    $table_op .= '</fieldset>';
	//  DIV CODIGO DE BARRAS
	//<div id = "codigo_barras"> </div>
	$oReturn->assign("divFormularioActivoFijo", "innerHTML", $table_op);

	$table_ctas .= '<table class="table table-bordered table-striped table-condensed" style="width: 100%; margin-bottom: 0px;" >
					<tr> 
						<td colspan="8" align="center" class="bg-primary">CUENTAS DE GASTO</td>									
					</tr>
					<tr class="info">
                        <td style colspan="8" align="center"> Los campos con * son de ingreso obligatorio &nbsp;
							<div class="btn-group">
								<div class=" btn btn-success btn-xs " onclick="nuevoDetalle()">
									<span class="glyphicon glyphicon-file"></span>
									Nuevo
								</div>
								<div class="btn btn-success btn-xs " onclick="grabarDetalle(id)">
									<span class="glyphicon glyphicon-floppy-disk"></span>
									Grabar
								</div>
							</div>							
						</td>
                    </tr>
                    <tr>
                        <td>' . $ifu->ObjetoHtmlLBL('gasd_cod_cuen') . '</td>	
                        <td>' . $ifu->ObjetoHtml('gasd_cod_cuen') . '
							<div id ="gasd_cod_cuen" class="btn btn-primary btn-sm" onclick="buscar_cuentas(id)">
						         <span class="glyphicon glyphicon-list-alt"><span>
						    </div></td>
                        <td>' . $ifu->ObjetoHtmlLBL('gasd_cod_ccos') . '</td>
						</td>
                        <td>' . $ifu->ObjetoHtml('gasd_cod_ccos') .'
							<div id ="gasd_cod_ccos" class="btn btn-primary btn-sm" onclick="buscar_ccostos(id)">
						         <span class="glyphicon glyphicon-list-alt"><span>
						    </div></td>
						</td>
                        <td>' . $ifu->ObjetoHtmlLBL('gasd_val_porc') . '</td>
                        <td>' . $ifu->ObjetoHtml('gasd_val_porc') .'</td>
                        <td>' . $ifu->ObjetoHtmlLBL('gasd_rev_sn') . '</td>
                        <td>' . $ifu->ObjetoHtml('gasd_rev_sn') .'</td>						
                    </tr>
					</table>
					<div id = "cuentasGrabadas"> </div>';

	$oReturn->assign("divFormularioCuentas", "innerHTML", $table_ctas);

    $table_resposable .= '<table class="table table-bordered table-striped table-condensed" style="width: 100%; margin-bottom: 0px;" >
					<tr> 
						<td colspan="6" align="center" class="bg-primary">DETALLES DE RESPONSABLES</td>
					</tr>
					<tr class="info">
                        <td colspan="6" align="center"> Los campos con * son de ingreso obligatorio &nbsp;	
							<div class="btn-group">
								<div class=" btn btn-success btn-xs btn-xs " onclick="nuevoResponsable()">
									<span class="glyphicon glyphicon-file"></span>
									Nuevo
								</div>
								<div class="btn btn-success btn-xs " onclick="guardarResponsables(id)">
									<span class="glyphicon glyphicon-floppy-disk"></span>
									Grabar
								</div>
							</div>							
						</td >
                    </tr>
                    <tr>
                        <td>' . $ifu->ObjetoHtmlLBL('cxa_codigo') . '</td>
                        <td>' . $ifu->ObjetoHtml('cxa_codigo') . '</td>
                        <td>' . $ifu->ObjetoHtmlLBL('cod_empleado') . '</td>
                        <td>' . $ifu->ObjetoHtml('cod_empleado') . '
							<div id ="cod_empleado" class="btn btn-primary btn-sm" onclick="buscar_empleados()">
								<span class="glyphicon glyphicon-list-alt"><span>
							</div>
						</td>
						<td>' . $ifu->ObjetoHtmlLBL('nom_empleado') . '</td>
                        <td>' . $ifu->ObjetoHtml('nom_empleado') . '</td>
                    </tr>                                   
                    <tr>
                        
                        <td>' . $ifu->ObjetoHtmlLBL('cargo_empleado') . '</td>
                        <td>' . $ifu->ObjetoHtml('cargo_empleado') . '</td>

                        <td>' . $ifu->ObjetoHtmlLBL('ubicacion_empleado') . '</td>                   
                        <td>' . $ifu->ObjetoHtml('ubicacion_empleado') .'</td>
                        
                        <td>' . $ifu->ObjetoHtmlLBL('fecha') . '</td>
                        <td>' . $ifu->ObjetoHtml('fecha') .'</td>
                        
                    </tr>
                    <tr>                      
                        <td>' . $ifu->ObjetoHtmlLBL('observacion') . '</td>
                        <td colspan="5">' . $ifu->ObjetoHtml('observacion') .'</td>
                    </tr>
                    
					</table>
					<!-- RECUPERAR GRID DE RESPOSABLES POR ACTIVO FIJO SELECCIONADO -->
					<div id = "responsablesGrabadas"> </div>';
    $oReturn->assign("divFormularioResponsable", "innerHTML", $table_resposable);
	$tabla_partes .='
					<input type="hidden" id="tipo_mov" name="tipo_mov" readonly="readonly" value="">
					<table class="table table-bordered table-striped table-condensed" style="width: 100%; margin-bottom: 0px;" >
					<tr>
						<td colspan="6" align="center" class="bg-primary">DETALLES DE PARTES</td>
					</tr>
					<tr class="info">
                        <td colspan="6" align="center"> Los campos con * son de ingreso obligatorio &nbsp;							
							<div class="btn-group">
								<div class=" btn btn-success btn-xs btn-xs " onclick="nuevoPartes()">
									<span class="glyphicon glyphicon-file"></span>
									Nuevo
								</div>
								<div class="btn btn-success btn-xs " onclick="grabarPartes(id)">
									<span class="glyphicon glyphicon-floppy-disk"></span>
									Grabar
								</div>
							</div>							
						</td >
                    </tr>
                    <tr>
					    <td>' . $ifu->ObjetoHtmlLBL('estado') . '</td>
                        <td>'. $ifu->ObjetoHtml('part_cod_part') . '' . $ifu->ObjetoHtml('estado') . '</td>						                        					
						<td>' . $ifu->ObjetoHtmlLBL('part_nom_part') . '</td> 
                        <td colspan = "3">' . $ifu->ObjetoHtml('part_nom_part') . '</td>						
					</tr>
					<tr>
                        <td>' . $ifu->ObjetoHtmlLBL('part_marc_part') . '</td>
                        <td>' . $ifu->ObjetoHtml('part_marc_part') . '</td>
                        <td>' . $ifu->ObjetoHtmlLBL('part_modl_part') . '</td>
                        <td>' . $ifu->ObjetoHtml('part_modl_part') . '</td>						
						<td>' . $ifu->ObjetoHtmlLBL('part_seri_part') . '</td>
                        <td>' . $ifu->ObjetoHtml('part_seri_part') . '</td>	
					</tr>
					<tr>              
                        <td>' . $ifu->ObjetoHtmlLBL('part_colr_pat') . '</td>
                        <td>' . $ifu->ObjetoHtml('part_colr_pat') . '</td>	
						<td>' . $ifu->ObjetoHtmlLBL('part_cant_part') . '</td> 
                        <td>' . $ifu->ObjetoHtml('part_cant_part') . '</td>
					</tr>
					<tr>
                        <td>' . $ifu->ObjetoHtmlLBL('part_obs_part') . '</td>
                        <td colspan = "5">' . $ifu->ObjetoHtml('part_obs_part') . '</td>	
					</tr>
					</table>
						<div id = "gridPartes"> </div>'; 
	$oReturn->assign("divPartes", "innerHTML", $tabla_partes);					
	$table_mantenimiento .= '
					<input type="hidden" id="tipo_mov" name="tipo_mov" readonly="readonly" value="">
					<table class="table table-bordered table-striped table-condensed" style="width: 100%; margin-bottom: 0px;" >
					<tr>
						<td colspan="6" align="center" class="bg-primary">DETALLES DE MANTENIMIENTO</td>
					</tr>
					<tr class="info">
                        <td colspan="6" align="center"> Los campos con * son de ingreso obligatorio &nbsp;							
							<div class="btn-group">
								<div class=" btn btn-success btn-xs btn-xs " onclick="nuevoMantenimiento()">
									<span class="glyphicon glyphicon-file"></span>
									Nuevo
								</div>
								<div class="btn btn-success btn-xs " onclick="guardarMantenimiento(id)">
									<span class="glyphicon glyphicon-floppy-disk"></span>
									Grabar
								</div>
							</div>							
						</td >
                    </tr>
                    <tr>
                        <td>' . $ifu->ObjetoHtmlLBL('mant_tip_movi') . '</td>
                        <td>' . $ifu->ObjetoHtml('mant_tip_movi') . '</td>
                        <td>' . $ifu->ObjetoHtmlLBL('mant_sec_docu') . '</td>
                        <td><input type="text" id="sec_docu" name="sec_docu" readonly="readonly"></td>
						<td>' . $ifu->ObjetoHtmlLBL('mant_caus_mant') . '</td>
                        <td>' . $ifu->ObjetoHtml('mant_caus_mant') . '</td>

                    </tr>
                    <tr>
                        
                        <td>' . $ifu->ObjetoHtmlLBL('mant_ref_mant') . '</td>
                        <td>' . $ifu->ObjetoHtml('mant_ref_mant') . '</td>
                        <td>' . $ifu->ObjetoHtmlLBL('mant_tall_mant') . '</td>
                        <td>' . $ifu->ObjetoHtml('mant_tall_mant') .'</td>
                        <td>' . $ifu->ObjetoHtmlLBL('mant_fini_mant') . '</td>
                        <td>' . $ifu->ObjetoHtml('mant_fini_mant') .'</td>                      
                    </tr>
                    <tr>
                        <td>' . $ifu->ObjetoHtmlLBL('mant_fent_mant') . '</td>
                        <td>' . $ifu->ObjetoHtml('mant_fent_mant') .'</td>
                        <td>' . $ifu->ObjetoHtmlLBL('mant_cost_mant') . '</td>
                        <td>' . $ifu->ObjetoHtml('mant_cost_mant') .'</td>
                        <td>' . $ifu->ObjetoHtmlLBL('mant_resp_mant') . '</td>
                        <td>' . $ifu->ObjetoHtml('mant_resp_mant') .'</td>
                    </tr>
                    <tr>
                        <td>' . $ifu->ObjetoHtmlLBL('mant_obs_mant') . '</td>
                        <td colspan = "5">' . $ifu->ObjetoHtml('mant_obs_mant') .'</td>
                    </tr>
                    
					</table>
					<!-- RECUPERAR GRID DE RESPOSABLES POR ACTIVO FIJO SELECCIONADO -->
					<div id = "divMantenimientoGrabadas"> </div>';
	$oReturn->assign("divMantenimiento", "innerHTML", $table_mantenimiento);

    $table_otros .= '<table class="table table-bordered table-striped table-condensed" style="width: 100%; margin-bottom: 0px;" >
					<tr> 
						<td colspan="6" align="center" class="bg-primary">ASEGURADORAS</td>
					</tr>
					<tr class="info">
                        <td colspan="6" align="center"> Los campos con * son de ingreso obligatorio &nbsp;	
							<div class="btn-group">
								<div class=" btn btn-success btn-xs btn-xs " onclick="nuevoAseguradoras()">
									<span class="glyphicon glyphicon-file"></span>
									Nuevo
								</div>
								<div class="btn btn-success btn-xs " onclick="guardarOtros(id)">
									<span class="glyphicon glyphicon-floppy-disk"></span>
									Grabar
								</div>
							</div>							

						</td >
                    </tr>
                    <tr>
                        <td>' . $ifu->ObjetoHtml('sact_cod_sact') . '' . $ifu->ObjetoHtmlLBL('sact_poli_sact') . '</td>
                        <td>' . $ifu->ObjetoHtml('sact_poli_sact') . '
                        <td>' . $ifu->ObjetoHtmlLBL('sac_cod_aseg') . '</td>
                        <td>' . $ifu->ObjetoHtml('sac_cod_aseg') . '</td>
						<td>' . $ifu->ObjetoHtmlLBL('sact_fech_sact') . '</td>
                        <td>' . $ifu->ObjetoHtml('sact_fech_sact') . '</td>
                    </tr>                                   
                    <tr>
                        <td>' . $ifu->ObjetoHtmlLBL('sact_fven_sact') . '</td>
                        <td>' . $ifu->ObjetoHtml('sact_fven_sact') . '</td>

                        <td>' . $ifu->ObjetoHtmlLBL('sact_val_sact') . '</td>                   
                        <td>' . $ifu->ObjetoHtml('sact_val_sact') .'</td>
                        
                        <td>' . $ifu->ObjetoHtmlLBL('sact_dedu_sact') . '</td>
                        <td>' . $ifu->ObjetoHtml('sact_dedu_sact') .'</td>
                    </tr>
                    <tr>                                                                        
                        <td>' . $ifu->ObjetoHtmlLBL('sac_num_matr') . '</td>
                        <td>' . $ifu->ObjetoHtml('sac_num_matr') .'</td>
                                                
                        <td>' . $ifu->ObjetoHtmlLBL('sac_num_motr') . '</td>
                        <td>' . $ifu->ObjetoHtml('sac_num_motr') .'</td>
                        
                        <td>' . $ifu->ObjetoHtmlLBL('sac_num_chsis') . '</td>
                        <td>' . $ifu->ObjetoHtml('sac_num_chsis') .'</td>                        
                    </tr>
                    <tr>                                                                        
                        <td>' . $ifu->ObjetoHtmlLBL('sac_num_plac') . '</td>
                        <td>' . $ifu->ObjetoHtml('sac_num_plac') .'</td>
                                                
                        <td>' . $ifu->ObjetoHtmlLBL('sac_val_come') . '</td>
                        <td>' . $ifu->ObjetoHtml('sac_val_come') .'</td>                                                
                    </tr>
                    <tr>
                        <td>' . $ifu->ObjetoHtmlLBL('sact_obs_sact') . '</td>
                        <td colspan="5">' . $ifu->ObjetoHtml('sact_obs_sact') .'</td>
                    </tr>
					</table>
					<!-- RECUPERAR GRID DE OTROS DETALLES -->
					<div id = "otrosGrabadas"> </div>';
    $oReturn->assign("divFormularioOtros", "innerHTML", $table_otros);

	$table_imagen.='<div id = "divImagenG"> </div>';
	$oReturn->assign("divImagen", "innerHTML", $table_imagen);
				
    return $oReturn;
}
// ARMAR CODIGO DE ACTIVO FIJO EN FUNCION DE LAS INICIALES DEL GRUPO Y SUBGRUPO
function f_arma_codigo($aForm){
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
    $idempresa = $_SESSION['U_EMPRESA'];
    $array = ($_SESSION['ARRAY_PINTA']);
    $usuario_web = $_SESSION['U_ID'];

    //variables formulario
    $codigoSubgrupo = $aForm['sgac_cod_sgac'];
    $codigoGrupo    = $aForm['gact_cod_gact'];

    //$oReturn->alert($continente);
    try {
        $oIfx->QueryT('BEGIN');
        if ($codigoSubgrupo != '' and $codigoGrupo != ''){
            // grupo
            $sql = "select gact_cod_ini 
                    from saegact 
                    where gact_cod_empr = '$idempresa' 
                    and gact_cod_gact = '$codigoGrupo'";
            //$oReturn->alert($sql);
            $inicialesGrupo = consulta_string($sql,'gact_cod_ini', $oIfx,0);
            // subgrupo
            $sql = "select sgac_cod_ini 
                    from saesgac 
                    where sgac_cod_empr = '$idempresa' 
                    and sgac_cod_sgac = '$codigoSubgrupo'";
            //$oReturn->alert($sql);
            $inicialesSubgrupo = consulta_string($sql,'sgac_cod_ini', $oIfx,0);
            //BUSCAR CODIGO MAXIMO DEL ACTIVO POR GRUPO Y SUBGRUPO
            $sql = "select count(*) as contador
                    from saeact
                    where act_cod_empr = '$idempresa'
                    and gact_cod_gact = '$codigoGrupo'
                    and sgac_cod_sgac = '$codigoSubgrupo'";
            $contador = consulta_string($sql,'contador', $oIfx,0);
            /*$contador ++;
            if ($contador > 0 and $contador < 10){
                $contador1 = '000'.$contador;
            } elseif ($contador > 9 and $contador < 100){
                $contador1 = '00'.$contador;
            } elseif ($contador > 99 and $contador < 1000){
                $contador1 = '00'.$contador;
            }*/
            $contador1=secuencial(2, '0', $contador, 5);
            $codigoActivo =  $inicialesGrupo.'-'.$inicialesSubgrupo.'-'.$contador1;
            $oReturn->assign('act_clave_act', 'value',$codigoActivo );
        }

    } catch (Exception $e) {
        $oCon->QueryT('ROLLBACK');
        $oReturn->alert($e->getMessage());
    }
    return $oReturn;
}

// SI tdep_dep_fcom (DEPRECIACION DESDE LA FECHA DE COMPRA) ES SI, LA FECHA DE DEPRECIACION SERA IGUAL A LA FECHA DE COMPRA DEL ACTIVO
function f_tipoDepreciacion($aForm){
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
    $idempresa = $_SESSION['U_EMPRESA'];
    $array = ($_SESSION['ARRAY_PINTA']);
    $usuario_web = $_SESSION['U_ID'];

    //variables formulario
    $tipoDepreciacion = $aForm['tdep_cod_tdep'];
	$fechaCompra      = $aForm['act_fcmp_act'];
    //$oReturn->alert($continente);
    try {
        $oIfx->QueryT('BEGIN');
        if (!empty($tipoDepreciacion)){
            // DEPRESIACION DESDE LA FECHA DE COMPRA
            $sql = "select tdep_dep_fcom 
                    from saetdep 
                    where tdep_cod_empr = '$idempresa' 
                    and tdep_cod_tdep = '$tipoDepreciacion'";
            //$oReturn->alert($sql);
            $depreciacionFechaCompra = consulta_string($sql,'tdep_dep_fcom', $oIfx,'');
			if ($depreciacionFechaCompra == 'S'){
				$oReturn->assign('act_fdep_act', 'value',$fechaCompra );
			} else {
				$oReturn->script('copiaFecha_("N");');
			}
            
        }

    } catch (Exception $e) {
        $oCon->QueryT('ROLLBACK');
        $oReturn->alert($e->getMessage());
    }
    return $oReturn;	
}

// CARGAR RESTO DE DATOS DE ACTIVO EN FUNCION DEL CODIGO Y LA EMPRESA
function f_cargar_datos($aForm){
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
    $idempresa = $_SESSION['U_EMPRESA'];
    $array = ($_SESSION['ARRAY_PINTA']);
    $usuario_web = $_SESSION['U_ID'];

    //variables formulario
    $codigoActivo = $aForm['act_cod_act'];
    
    //$oReturn->alert($continente);
    try {
        $oIfx->QueryT('BEGIN');
        if ($codigoActivo != '' and $idempresa != ''){
            // DATOS DEL ACTIVO
            $sql = "select gact_cod_gact, sgac_cod_sgac, tdep_cod_tdep, act_nom_act,act_otras_esp,
					tact_cod_tact, act_marc_act, act_mode_act, 
					act_colr_act, act_seri_act, act_cant_act, 
					act_val_comp, act_prov_act, eact_cod_eact,
					act_fcmp_act, act_vutil_act, act_fdep_act,
					act_fcorr_act, act_vres_act, act_refr_act,
					act_comp_act, act_tcam_act, act_nom_prop,
					act_path_foto, act_part_act, act_gar_act,
					act_ext_act, act_cod_ramo, act_fiman_act,
					act_cod_pres, act_foto_act, act_clave_act, act_est_reva, act_flo_act, act_ani_act, act_kms_act, act_pla_act
					from saeact 
					where act_cod_act = $codigoActivo
					and act_cod_empr = $idempresa";
					//echo $sql; exit;
					//$oReturn->alert($sql);
			if($oIfx->Query($sql))	{
				if ($oIfx->NumFilas() > 0){
					$codigoGrupo = $oIfx->f('gact_cod_gact');
					$oReturn->assign('gact_cod_gact', 'value', $codigoGrupo);
					$codigoSubGrupo = $oIfx->f('sgac_cod_sgac');
					$oReturn->script("cargar_subgrupo_por_grupo('".addslashes($codigoGrupo)."', '".addslashes($codigoSubGrupo)."')");
					$oReturn->assign('tdep_cod_tdep', 'value',$oIfx->f('tdep_cod_tdep'));
					
					if ($oIfx->f('act_est_reva') == 'R'){
						$estadoActivoRevalorizado = 'REVALORIZADO';
					} elseif ($oIfx->f('act_est_reva') == 'D') {
						$estadoActivoRevalorizado = 'DESVALORIZADO';
					} else{
						$estadoActivoRevalorizado = null;
					}
					$oReturn->assign('act_est_reva', 'value', $estadoActivoRevalorizado);
					
					$oReturn->assign('tact_cod_tact', 'value',$oIfx->f('tact_cod_tact'));
					$oReturn->assign('act_marc_act', 'value',$oIfx->f('act_marc_act'));
					$oReturn->assign('act_mode_act', 'value',$oIfx->f('act_mode_act'));
					
					$oReturn->assign('act_colr_act', 'value',$oIfx->f('act_colr_act'));
					$oReturn->assign('act_seri_act', 'value',$oIfx->f('act_seri_act'));
					$oReturn->assign('act_cant_act', 'value',$oIfx->f('act_cant_act'));
					
					$oReturn->assign('act_val_comp', 'value',$oIfx->f('act_val_comp'));
					$oReturn->assign('act_prov_act', 'value',$oIfx->f('act_prov_act'));
					$oReturn->assign('eact_cod_eact', 'value',$oIfx->f('eact_cod_eact'));
					
					$oReturn->assign('act_fcmp_act', 'value',$oIfx->f('act_fcmp_act'));
					$oReturn->assign('act_vutil_act', 'value',$oIfx->f('act_vutil_act'));
					$oReturn->assign('act_fdep_act', 'value',$oIfx->f('act_fdep_act'));
					
					$oReturn->assign('act_fcorr_act', 'value',$oIfx->f('act_fcorr_act'));
					$oReturn->assign('act_vres_act', 'value',$oIfx->f('act_vres_act'));
					$oReturn->assign('act_refr_act', 'value',$oIfx->f('act_refr_act'));
					
					$oReturn->assign('act_comp_act', 'value',$oIfx->f('act_comp_act'));
					$oReturn->assign('act_tcam_act', 'value',$oIfx->f('act_tcam_act'));
					$oReturn->assign('act_nom_prop', 'value',$oIfx->f('act_nom_prop'));
					
					$oReturn->assign('act_path_foto', 'value',$oIfx->f('act_path_foto'));
					$oReturn->assign('act_part_act', 'value',$oIfx->f('act_part_act'));
					$oReturn->assign('act_gar_act', 'value',$oIfx->f('act_gar_act'));
					
					$oReturn->assign('act_ext_act', 'value',$oIfx->f('act_ext_act'));
					$oReturn->assign('act_cod_ramo', 'value',$oIfx->f('act_cod_ramo'));
					$oReturn->assign('act_fiman_act', 'value',$oIfx->f('act_fiman_act'));
					$oReturn->assign('act_cod_pres', 'value',$oIfx->f('act_cod_pres'));	
					$oReturn->assign('act_foto_act', 'value',$oIfx->f('act_foto_act'));
					if ($oIfx->f('act_flo_act') == 'S') {
						$oReturn->assign('act_flo_act', 'checked',true);
					} else {
						$oReturn->assign('act_flo_act', 'checked',false);
					}
					
					$oReturn->assign('act_ani_act', 'value',$oIfx->f('act_ani_act'));
					$oReturn->assign('act_kms_act', 'value',$oIfx->f('act_kms_act'));	
					$oReturn->assign('act_pla_act', 'value',$oIfx->f('act_pla_act'));
					$oReturn->assign('act_otras_esp_act', 'value',$oIfx->f('act_otras_esp'));

					//$oReturn->assign('gact_cod_gact', 'disabled',true);
					


					// RECUPERA IMAGEN ACTIVO FIJO
					$html='';
					
					//$nombreActivo =  $oIfx->f('act_nom_act');					
					if($oIfx->f('act_path_foto')!=''){
					//	$foto=$oIfx->f('act_foto_act');
						$html.='<img src="' . path(DIR_INCLUDE) . 'clases/formulario/plugins/reloj/' .  substr($oIfx->f('act_path_foto'),3) . '" width="80%"/>';						
					}
					$oReturn->assign('divImagenG','innerHTML',$html);
                    
                    

					//RECUPERA CODIGO DE BARRAS
					$html_cod_barras='';
					$nombArch = $oIfx->f('act_clave_act');
					$rutaCodi ='codigo_de_barras/' . $nombArch . '.jpg';

                    //rutaCodi
                    //echo $rutaCodi;exit;
                    new barCodeGenrator($nombArch, 1, $rutaCodi, 450, 100, true);

					if ($oIfx->f('act_clave_act')!=''){
						$html_cod_barras.='<tr>												
												<td>' . $nombArch . ' </td>												
												<td colspan=2 align="right"> 
                                                <img width="350px;" src="codigo_de_barras/' . $nombArch . '.jpg"></td>
										</tr>
										';
					}			
					$oReturn->assign("codigo_barras","innerHTML", $html_cod_barras);
				}
			}
            //$oReturn->alert($codigoSubGrupo);
        }

    } catch (Exception $e) {
        $oCon->QueryT('ROLLBACK');
        $oReturn->alert($e->getMessage());
    }
    return $oReturn;
}

function f_filtro_subgrupo($aForm, $data){
    //Definiciones
    global $DSN, $DSN_Ifx;

    if (session_status() !== PHP_SESSION_ACTIVE) {session_start();}

    $oIfx = new Dbo ( );
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    $oReturn = new xajaxResponse ( );

    //variables formulario
    $codigoGrupo = $aForm['gact_cod_gact'];
	$empresa = $_SESSION['U_EMPRESA'];
	// DATOS DEL ACTIVO
	$sql = "select sgac_cod_sgac, sgac_des_sgac 
			 from saesgac where sgac_cod_empr = '$empresa'                                                                  
			 and gact_cod_gact = '$codigoGrupo'";
	
    $i = 1;
    if ($oIfx->Query($sql)) {
        $oReturn->script('eliminar_lista_subgrupo();');
        if ($oIfx->NumFilas() > 0) {
            do {
                $oReturn->script(('anadir_elemento_subgrupo(' . $i++ . ',\'' . $oIfx->f('sgac_cod_sgac') . '\', \'' . $oIfx->f('sgac_des_sgac') . '\' )'));
            } while ($oIfx->SiguienteRegistro());
        }
    }
	//$oReturn->alert($sql);
	//$oReturn->alert($data);
    $oReturn->assign('sgac_cod_sgac', 'value', $data);
	//$oReturn->assign('sgac_cod_sgac', 'disabled', false);

    return $oReturn;
}

// RESETEAR DATOS DE FORMA DE CTAS GASTOS
 function nuevoDetalle($aForm = ''){
	 $oReturn = new xajaxResponse ( );
	 $oReturn->script('nuevoDetalle();');
	 return $oReturn;
}

// RESETEAR DATOS DE FORMA RESPONSABLES
 function nuevoResponsable($aForm = ''){
	 $oReturn = new xajaxResponse ( );
	 $oReturn->script('nuevoResponsable();');
	 return $oReturn;
}

// RESETEAR DATOS DE FORMA RESPONSABLES
 function nuevoAseguradoras($aForm = ''){
	 $oReturn = new xajaxResponse ( );
	 $oReturn->script('nuevoAseguradoras();');
	 return $oReturn;
}

// RESETEAR DATOS MANTENIMIENTO
 function nuevoMantenimiento($aForm = ''){
	 $oReturn = new xajaxResponse ( );
	 $oReturn->script('nuevoMantenimiento();');
	 return $oReturn;
}

// RESETEAR DATOS DETALLE DE PARTES
 function nuevoPartes($aForm = ''){
	 $oReturn = new xajaxResponse ( );
	 $oReturn->script('nuevoPartes();');
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
    $idempresa = $_SESSION['U_EMPRESA'];
	$idsucursal = $_SESSION['U_SUCURSAL'];

    $array = ($_SESSION['ARRAY_PINTA']);
    $usuario_web = $_SESSION['U_ID'];
	
    //variables formulario
    $codigoActivo 	= $aForm['act_cod_act'];
    $codigoGrupo    = $aForm['gact_cod_gact'];
    $codigoSubgrupo = $aForm['sgac_cod_sgac'];
    $tipoDeprecia 	= $aForm['tdep_cod_tdep'];
    $tipoActivo     = $aForm['tact_cod_tact'];
    $clave          = $aForm['act_clave_act'];
    $nombre         = $aForm['act_nom_act'];
    $act_otras_esp_act         = $aForm['act_otras_esp_act'];
    $marca          = $aForm['act_marc_act'];
    $modelo         = $aForm['act_mode_act'];
    $color          = $aForm['act_colr_act'];
    $serie          = $aForm['act_seri_act'];
    $cantidad       = $aForm['act_cant_act'];
    $valorCompra    = $aForm['act_val_comp'];
    $proveedor      = $aForm['act_prov_act'];
    $estado         = $aForm['eact_cod_eact'];
    $fechaCompra    = $aForm['act_fcmp_act'];
    $vidaUtil       = $aForm['act_vutil_act'];
    $fechaDeprAct   = $aForm['act_fdep_act'];
    $fechaCorrec 	= $aForm['act_fcorr_act'];
    $valorResidual  = $aForm['act_vres_act'];
    $referencia     = $aForm['act_refr_act'];
    $comprobante    = $aForm['act_comp_act'];
    $tipoCambio     = $aForm['act_tcam_act'];
    $responsable    = $aForm['act_nom_prop'];
    $direcFoto      = $aForm['act_path_foto'];
    $partes         = $aForm['act_part_act'];
    $garantia       = $aForm['act_gar_act'];
    $existencia     = $aForm['act_ext_act'];
    $ramo           = $aForm['act_cod_ramo'];
    $fechaBaja      = $aForm['act_fiman_act'];
    $partidaPresup  = $aForm['act_cod_pres'];
    $act_foto_act   = $aForm['act_foto_act'];
    $act_flo_act    = $aForm['act_flo_act'];
    $act_pla_act    = $aForm['act_pla_act'];
    $act_kms_act    = $aForm['act_kms_act'];
    $act_ani_act    = $aForm['act_ani_act'];
	

	
	if (empty($fechaCorrec)) {
        $fechaCorrec = $fechaCorrec != '' ? "'".$fechaCorrec."'" : 'NULL';
	}
    else{
        $fechaCorrec ="'".$fechaCorrec."'";
    }
    

	if (empty($fechaBaja)) {
        $fechaBaja = $fechaBaja != '' ? "'".$fechaBaja."'" : 'NULL';	
	}
    else{
        $fechaBaja ="'".$fechaBaja."'";
    }
	
    if(empty($fechaDeprAct)){
        $fechaDeprAct = $fechaDeprAct != '' ? "'".$fechaDeprAct."'" : 'NULL';
    }
    else{
        $fechaDeprAct ="'".$fechaDeprAct."'";
    }
	$tipoCambio		= 1;
    //validar campos
    $ramo = $ramo != '' ? "'".$ramo."'" : 'NULL';	
	if (empty($act_foto_act)) {
			$act_foto_act = '';
	}	
	
	if (empty($act_kms_act)) {
		$act_kms_act = 0;
	}	
	
	if (empty($act_ani_act)) {
		$act_ani_act = 0;
	}	
	//echo $codigoActivo; //exit;
   try {
        $oIfx->QueryT('BEGIN');
		if ($codigoActivo != ''){
            $sql = "select count(*) as contador  from saeact where act_cod_empr = '$idempresa' and act_cod_act = '$codigoActivo'";            
            $contador = consulta_string($sql,'contador', $oIfx,0);

            if ($contador > 0) { // MODIFICAR ACTIVO FIJO
				$sql = "select DATE_PART('year', act_fdep_act) as anio  from saeact where act_cod_empr = '$idempresa' and act_cod_act = '$codigoActivo'";
                $anio_act = consulta_string_func($sql,'anio', $oIfx,0);
                
				$sql = "select cdep_est_cdep from saecdep where
							act_cod_empr  = $idempresa and
							cdep_ani_depr = $anio_act and
							cdep_cod_acti = $codigoActivo ";
                $cdep_est_cdep = consulta_string_func($sql,'cdep_est_cdep', $oIfx,'PE');

                if(empty($partes)){
                    $partes = 'NULL';
                }
                 if(empty($existencia)){
                	$existencia='NUll';
                }
                if(empty($garantia)){
                	$garantia='NUll';
                }
                
				if( $cdep_est_cdep == 'PE' ){
					$sql = "UPDATE saeact
									set 
                                    eact_cod_eact = $estado, 
                                    act_nom_act = '$nombre', 
                                    act_marc_act = '$marca', 	
                                    act_colr_act = '$color' ,   
									act_seri_act = '$serie', 
                                    act_mode_act = '$modelo', 
                                    act_fcmp_act = '$fechaCompra', 
                                    act_path_foto = '$direcFoto',
									act_part_act = $partes, 
                                    act_foto_act='$act_foto_act', 
                                    act_nom_prop = '$responsable', 
                                    act_fdep_act = $fechaDeprAct,
									act_cod_ramo = $ramo, 
                                    act_tcam_act = $tipoCambio, 
                                    act_ext_act = $existencia, 
                                    act_gar_act = $garantia, 
                                    act_prov_act = '$proveedor',
									act_vres_act = $valorResidual, 
                                    act_comp_act = '$comprobante', 
                                    sgac_cod_empr = $idempresa, 
                                    act_flo_act = '$act_flo_act',
									act_pla_act = '$act_pla_act', 
                                    act_kms_act = $act_kms_act, 
                                    act_ani_act = $act_ani_act, 
                                    act_val_comp = $valorCompra,
									act_vutil_act = '$vidaUtil', 
                                    act_otras_esp= '$act_otras_esp_act',
                                    act_fiman_act=$fechaBaja,
                                    gact_cod_gact='$codigoGrupo',
                                    sgac_cod_sgac='$codigoSubgrupo'
									where act_cod_act = '$codigoActivo'
									and act_cod_empr = '$idempresa'";

                                  //echo $sql;exit;
                    $mensaje = "Datos Modificados";
				
					$oIfx->QueryT($sql);
					
					// GENERAR INDICES
					$sql = "delete from saemet where act_cod_empr = $idempresa and metd_cod_acti = $codigoActivo ";
					$oIfx->QueryT($sql);
                    $oIfx->QueryT('COMMIT WORK;');
					$mensajef= f_genera_index($aForm, $codigoActivo);
                    $oReturn->alert($mensajef);
					
				}else{
					$mensaje = 'Error, No se Puede Actualizar, Activo Fijo Contabilizado, por favor Anular el Comprobante...!!!! ';
				}
				
            } else{ // INSERTAR NUEVO ACTIVO FIJO
					//$codigoActivo = 0;

                    $sql = "select max(act_cod_act) as max  from saeact";            
                    $maximo = consulta_string($sql,'max', $oIfx,0) + 1;
                
					$sql = " INSERT INTO saeact  
							( act_cod_act, 	tact_cod_tact, 	act_cod_empr, 	act_cod_sucu, 	sgac_cod_sgac,   
							eact_cod_eact, 	act_clave_act, 	act_nom_act, 	act_marc_act, 	act_colr_act,   
							act_seri_act, 	act_mode_act, 	act_fcmp_act, 	act_refr_act, 	act_comp_act,   
							act_vutil_act, 	act_vres_act, 	act_part_act, 	act_tcam_act, 	act_cant_act,   
							tdep_cod_tdep, 	gact_cod_gact, 	sgac_cod_empr, 	act_prov_act,   act_fiman_act, 	
							act_fdep_act,   act_fcorr_act, 	act_ext_act,   	act_gar_act,   	act_val_comp,   
							act_cod_ramo,   act_nom_prop,  	act_cod_pres,   act_path_foto, act_foto_act, 
							act_flo_act,	act_pla_act,	act_kms_act,	act_ani_act, act_otras_esp)
					VALUES( $maximo,  $tipoActivo,	$idempresa,		$idsucursal,	'$codigoSubgrupo',
							$estado,		'$clave',		'$nombre', 		'$marca',		'$color',
							'$serie',		'$modelo',		'$fechaCompra',	'$referencia',	'$comprobante',
							$vidaUtil, 		$valorResidual,	$partes,		$tipoCambio, 	$cantidad,
							'$tipoDeprecia','$codigoGrupo', $idempresa, 	'$proveedor',	$fechaBaja,	
							$fechaDeprAct,$fechaCorrec, $existencia,	$garantia,		$valorCompra, 	
							$ramo, 		'$responsable', '$partidaPresup','$direcFoto', '$act_foto_act',
							'$act_flo_act', '$act_pla_act', $act_kms_act,  $act_ani_act ,'$act_otras_esp_act')";
                           // echo $sql,exit;
					$mensaje = "Datos Grabados";
					$oIfx->QueryT($sql);				
					// BUSCO SECUENCIAL
					$sql = "select max(act_cod_act) as activo
							from saeact 
							where act_cod_empr = '$idempresa'
							and act_cod_sucu = '$idsucursal'";
					$codigoActivo = consulta_string($sql,'activo', $oIfx,0);
                    $oIfx->QueryT('COMMIT WORK;');
					// GENERAR INDICES
					f_genera_index($aForm, $codigoActivo);
					//$oReturn->alert("Indices Generados");
				}
		}			
		//$oReturn->alert($codigoActivo);
		$oReturn->alert($mensaje);
	
		$oReturn->assign('gact_cod_gact', 'disabled', true);
		$oReturn->assign('sgac_cod_sgac', 'disabled', true);
		$oReturn->assign('act_cod_act', 'value',$codigoActivo);
		// $oReturn->script('recarga()');
		// $oReturn->script('seleccionaItem('.$codigoActivo.', \''.$clave.'\', '.$nombre.', \''.$codigoSubgrupo.'\')'); 		
		//$oIfx->QueryT('COMMIT WORK;');		
		$oReturn->script('lista_reporte_index('.$codigoActivo.')'); 	
    } catch (Exception $e) {
        $oCon->QueryT('ROLLBACK');
        $oReturn->alert($e->getMessage());
    }
    return $oReturn;
}

// GENERAR INDICES SAEMET 
function f_genera_index($aForm = '', $codigo){
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

    //variables de sesion
    $array = ($_SESSION['ARRAY_PINTA']);
    $usuario_web = $_SESSION['U_ID'];
	$empresa = $_SESSION['U_EMPRESA'];
    // $sucursal = $_SESSION['U_SUCURSAL'];
    
    $sql = "select act_cod_sucu from saeact where act_cod_empr = $empresa and act_cod_act = $codigo ";
    $sucursal = consulta_string_func($sql,'act_cod_sucu', $oIfx,'');    
	//variables formulario	
	$vida_util			=	$aForm['act_vutil_act'];			
	$tipo_depreciacion	=	$aForm['tdep_cod_tdep'];
	$valor_residual		=	$aForm['act_vres_act'];
	$valor_compra 		= 	$aForm['act_val_comp'];
	
	

	// TIPO DE DEPRECIACION
	$sql_tipo = "select tdep_tip_val, tdep_dep_fcom from saetdep
				where tdep_cod_tdep = '$tipo_depreciacion'
				and tdep_cod_empr = $empresa ";
	$intervalo = consulta_string($sql_tipo,'tdep_tip_val', $oIfx,0);
	$depreciacionAFecha = consulta_string($sql_tipo,'tdep_dep_fcom', $oIfx,'');
	
	if ($depreciacionAFecha == 'S') {
		$fecha_calculo  =   $aForm['act_fcmp_act'];	
	} else {
		$fecha_calculo  =   $aForm['act_fdep_act'];	
	}


	$anio_calculo 		= 	substr($fecha_calculo, 0, 4);
	$mes_calculo 		= 	substr($fecha_calculo, 5, 2);
	$dia_calculo 		= 	substr($fecha_calculo, 8, 2);
	
	
	if (empty($intervalo)) {
		$intervalo = 'M';				
	}
	if ($intervalo == 'M'){
		$nMeses = 12;
	} else{
		$nMeses = 1;
	}


   
	if ( intval($vida_util) == 0 ){
		$mensaje='Ingrese un valor mayor a cero en vida util para generar la depreciacion';
	}
    else{

        $num_registros = $vida_util * $nMeses;
	if ($depreciacionAFecha == 'S') {
			$mes = $mes_calculo;
			$anio = $anio_calculo;	
	} else {	
		// FECHA INICIAL
		if ($dia_calculo > 15){
			if ($mes_calculo == 12){
				$mes = 1;
				$anio = $anio_calculo + 1;
			}else{
				$mes = $mes_calculo + 1;
				$anio = $anio_calculo;
			}
		}else{
			$mes = $mes_calculo;
			$anio = $anio_calculo;
		}	
	}
	// VALOR A DEPRECIAR
	$compra_origen = $valor_compra;
	$valor_compra   = round(($valor_compra - $valor_residual) / $num_registros, 2);	
	$valor_aux	= $valor_compra;
	$porcentaje_dep =  (1/$num_registros); 
	$ultimaFila = $num_registros - 1;
	$ajuste = 0;
	for($i = 0; $i < $num_registros; $i++){
		if (($i == 0) && ($depreciacionAFecha == 'S')) {
			$ultimo_dia = date("d", (mktime(0, 0, 0, $mes + 1, 1, $anio) - 1));
			$valor_compra = (($valor_compra) / $ultimo_dia) * ($ultimo_dia - $dia_calculo + 1);			
		} else {
			$valor_compra = $valor_aux;
		}
		$fecha_desde = date($anio.'-'.$mes.'-01');
		$fecha_hasta = date('Y-m-t', strtotime($fecha_desde));
		if ($i == $ultimaFila){
			$valor_compra = ($compra_origen - $valor_residual) - $ajuste;			
		}
		$ajuste = $ajuste + $valor_compra;
		// if(empty($anio)){
		// 	$anio='NULL';
		// }
		 $sql_met = "insert into saemet (met_anio_met, metd_des_fech, metd_has_fech, metd_cod_empr, metd_cod_acti, act_cod_empr, act_cod_sucu, met_porc_met, metd_val_metd, met_num_dias, metd_cod_reva)
					values ($anio,'$fecha_desde', '$fecha_hasta', $empresa, $codigo, $empresa, $sucursal, $porcentaje_dep, $valor_compra, 0, 0);";
            // echo  $sql_met;exit;       
        $oIfx->QueryT($sql_met);
		if ($mes == 12) { 
			$mes = 1;						
			$anio = $anio + 1;
		}else{ 						
			$mes++; 
		}
	}	

    $mensaje='Depreciacion generada correctamente';

    }
	
    
    return $mensaje;		
}


// LISTA DE INDICES
function lista_reporte_index($codigoActivo){
  //Definiciones
    global $DSN, $DSN_Ifx;

    if (session_status() !== PHP_SESSION_ACTIVE) {session_start();}
    $oIfx = new Dbo ( );
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();
 	 $oReturn = new xajaxResponse ( );
	$empresa = $_SESSION['U_EMPRESA'];
    //  $sucursal = $_SESSION['U_SUCURSAL'];	
    
    $sql = "select act_cod_sucu from saeact where act_cod_empr = $empresa and act_cod_act = $codigoActivo ";
    $sucursal = consulta_string_func($sql,'act_cod_sucu', $oIfx,'');    

	// LISTA DE INDICES DE ACTIVOS
	$sql = "select met_anio_met, 
				   metd_des_fech, 
				   metd_has_fech, 
				   metd_val_metd
			from saemet where metd_cod_acti = $codigoActivo
			and act_cod_empr = $empresa
			and act_cod_sucu = $sucursal
			order by 1,2,3";
	if($oIfx->Query($sql)){
		if($oIfx->NumFilas() > 0){	
			$html.='<table class="table table-bordered table-striped table-condensed" style="width: 90%; margin-bottom: 0px;">
					<tr>
						<td class="bg-primary" align = center colspan="5"> VALORES A DEPRECIAR </td>
					</tr>
					<tr class="info"">					
						<td align = center> Fila </td>
						<td align = center> Anio </td>
						<td align = center> Fecha Inicio </td>
						<td align = center> Fecha Fin</td>
						<td align = center> Valor por Depreciar </td>
					</tr> ';	
			$fila  = 0;
			$total = 0;
			do{
				$fila++;
				$anio   = $oIfx->f('met_anio_met');
				$desde  = $oIfx->f('metd_des_fech');
				$hasta  = $oIfx->f('metd_has_fech');
				$valor  = $oIfx->f('metd_val_metd');
				$html.='<tr>
							<td>'.$fila.' </td> 
							<td>'.$anio.' </td> 
							<td>'.$desde.' </td> 
							<td>'.$hasta.' </td>
							<td align = right>'.$valor.' </td>
						</tr>';	
				$total = number_format($total + $valor,2,'.','');			
			}while($oIfx->SiguienteRegistro());	
			// TOTALES DE LA TABLA					
			$html.= '<td bgcolor = "#CCCCCC" colspan="5" style = "color:blue" align = "right"> TOTAL: '. $total .'</td>
					</table>';
		}
	}
	
	$oReturn->assign("lis_reporte","innerHTML", $html);
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
    $idempresa = $_SESSION['U_EMPRESA'];
	// $idsucursal = $_SESSION['U_SUCURSAL'];
    $array = ($_SESSION['ARRAY_PINTA']);
    $usuario_web = $_SESSION['U_ID'];

    //variables formulario
    $codigoActivo  = $aForm['act_cod_act'];
	//$oReturn->alert($continente);
    try {
        $oIfx->QueryT('BEGIN');
            $sql = "select act_cod_sucu from saeact where act_cod_empr = $idempresa and act_cod_act = $codigoActivo ";
            $idsucursal = consulta_string_func($sql,'act_cod_sucu', $oIfx,''); 	

			$sql = "select count(*) as contador 
					from saegasd where gasd_cod_acti  =  $codigoActivo
					and gasd_cod_empr = $idempresa 
					and gasd_cod_sucu = $idsucursal";
			$contador = consulta_string($sql,'contador', $oIfx,0);
			if ($contador > 0){
				$mensaje = "No se puede eliminar el registro, existen dependencias relacionadas";
			} else
			{
                $sql="delete from saemet where metd_cod_acti=$codigoActivo and act_cod_empr=$idempresa and act_cod_sucu = $idsucursal";
                $oIfx->QueryT($sql);

			$sql = "delete 
					from saeact 
					where act_cod_act =  $codigoActivo 
					and act_cod_empr = $idempresa 
					and act_cod_sucu = $idsucursal";
			$mensaje = "Datos Borrados";
			}		
		$oIfx->QueryT($sql);
		$oReturn->alert($mensaje);
		$oReturn->script('recarga()');
		$oReturn->script('seleccionaItem('.$codigoActivo.', \''.$clave.'\', '.$nombre.', \''.$codigoSubgrupo.'\')'); 		
        $oIfx->QueryT('COMMIT WORK;');
    } catch (Exception $e) {
        $oCon->QueryT('ROLLBACK');
        $oReturn->alert($e->getMessage());
    }
    $oReturn->script('nuevoFormulario()');
    return $oReturn;	
}

function grabarDetalle($aForm = ''){
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
    $idempresa = $_SESSION['U_EMPRESA'];
    // $idsucursal = $_SESSION['U_SUCURSAL'];
    $array = ($_SESSION['ARRAY_PINTA']);

    //variables formulario
    $codigoActivo 	 =  $aForm['act_cod_act'];
    $codigoCuenta    =  $aForm['gasd_cod_cuen'];
    $centroCosto     =  $aForm['gasd_cod_ccos'];
    $valorPorcentaje =  $aForm['gasd_val_porc'];
	$ctaRevaloSN	 =  $aForm['gasd_rev_sn'];
    //$oReturn->alert($continente);
    try {
        $oIfx->QueryT('BEGIN');
        if ($codigoActivo != ''){
            $sql = "select act_cod_sucu from saeact where act_cod_empr = $idempresa and act_cod_act = $codigoActivo ";
            $idsucursal = consulta_string_func($sql,'act_cod_sucu', $oIfx,'');    


            $sql = "select sum(gasd_val_porc) as total_porcentaje 
                    from saegasd
                    where gasd_cod_empr = '$idempresa' 
					and gasd_cod_acti   = '$codigoActivo'
					and gasd_cod_sucu   = '$idsucursal'
					and gasd_rev_sn     = '$ctaRevaloSN'
					and gasd_cod_ccos  <> '$centroCosto'";
			//echo $sql; exit;
            $suma = consulta_string($sql,'total_porcentaje', $oIfx,0);
			$suma = $suma + $valorPorcentaje;
           // $oReturn->alert($suma);
            if ($suma > 100){
                $oReturn->alert('Error a sobrepasado el 100 %');
            }else {
                $sql = "select
                            count(*) as contador
                          from saegasd
                          where
                                gasd_cod_empr  = '$idempresa' and
                                gasd_cod_acti  = '$codigoActivo' and
                                gasd_cod_sucu  = '$idsucursal' and
								gasd_rev_sn	   = '$ctaRevaloSN' and
                                gasd_cod_ccos  = '$centroCosto'";

                $contador = consulta_string($sql, 'contador', $oIfx, 0);
                if ($contador == 0) {
                    $sql = " INSERT INTO saegasd
                               ( gasd_cod_acti, gasd_cod_empr, 	gasd_cod_sucu, 	gasd_cod_cuen, 	  gasd_val_porc,    gasd_cod_ccos,  gasd_rev_sn )
                         VALUES( $codigoActivo, $idempresa,		$idsucursal,	'$codigoCuenta',  $valorPorcentaje, '$centroCosto', '$ctaRevaloSN' )";
                } else {
                    $sql = "update saegasd
                            set gasd_cod_cuen='$codigoCuenta', 	gasd_val_porc='$valorPorcentaje', gasd_rev_sn = '$ctaRevaloSN'
                             where
                                gasd_cod_empr = '$idempresa' and
                                gasd_cod_acti = '$codigoActivo' and
                                gasd_cod_sucu='$idsucursal' and
                                gasd_cod_ccos='$centroCosto'";
                }
                $mensaje = "Datos Grabados";
                $oIfx->QueryT($sql);
            }
        }
        $oReturn->alert($mensaje);
        $oReturn->script("recarga_cta_gasto();");
        $oIfx->QueryT('COMMIT WORK;');
    } catch (Exception $e) {
        $oCon->QueryT('ROLLBACK');
        $oReturn->alert($e->getMessage());
    }
    return $oReturn;

}

//Guardar Responsables
function guardarResponsables($aForm = ''){
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
    $idempresa = $_SESSION['U_EMPRESA'];
    // $idsucursal = $_SESSION['U_SUCURSAL'];
    $array = ($_SESSION['ARRAY_PINTA']);

    //variables formulario
    $codigoActivo 	 =  $aForm['act_cod_act'];
    $codigoEmpleado  =  $aForm['cod_empleado'];
    $cargoEmpleado   =  $aForm['cargo_empleado'];
    $ubicacion       =  $aForm['ubicacion_empleado'];
    $fecha           =  $aForm['fecha'];
    $observacion     =  $aForm['observacion'];

    $fecha           = $fecha;
    //$oReturn->alert($continente);

    if(empty($codigoEmpleado )){
        $oReturn->alert('Seleccione el Empleado');
        $oReturn->script("foco('cod_empleado')");
    }
    elseif(empty($fecha)){
        $oReturn->alert('Seleccione la Fecha');
    }
    else{

        try {
            $oIfx->QueryT('BEGIN');
            if ($codigoActivo != ''){
                $sql = "select act_cod_sucu from saeact where act_cod_empr = $idempresa and act_cod_act = $codigoActivo ";
                $idsucursal = consulta_string_func($sql,'act_cod_sucu', $oIfx,'');    

                $sql = "select
                            count(*) as contador
                          from saecxa
                          where
                                   act_cod_empr  = '$idempresa' and
                                   act_cod_sucu  = '$idsucursal' and
                                   act_cod_act   = '$codigoActivo' and
                                   estr_cod_estr = '$cargoEmpleado' and
                                   empl_cod_empl = '$codigoEmpleado'";
                  
                $contador = consulta_string($sql,'contador', $oIfx,0);
                if($contador==0){
                    $sql = " INSERT INTO saecxa
                               ( empl_cod_empl,     cxa_ubic_cxa, act_cod_act, 	 act_cod_empr, 	act_cod_sucu,    estr_cod_estr,   cxa_fech_cxa,   cxa_obs_cxa )
                         VALUES( '$codigoEmpleado','$ubicacion',  $codigoActivo, $idempresa,    $idsucursal,     '$cargoEmpleado', '$fecha',      '$observacion' )";
                }else{
                    $sql="update saecxa
                             set empl_cod_empl='$codigoEmpleado', cxa_ubic_cxa='$ubicacion', estr_cod_estr = '$cargoEmpleado', cxa_fech_cxa = '$fecha', cxa_obs_cxa = '$observacion'
                              where act_cod_empr  = '$idempresa' and
                                     act_cod_sucu  = '$idsucursal' and
                                     act_cod_act   = '$codigoActivo' and
                                     estr_cod_estr = '$cargoEmpleado' and
                                     empl_cod_empl = '$codigoEmpleado'";
                }
    
    
                $mensaje = "Datos Grabados";
                $oIfx->QueryT($sql);
            }
    
    
            $oReturn->alert($mensaje);
            $oReturn->script("recarga_responsables();");
            $oIfx->QueryT('COMMIT WORK;');
        } catch (Exception $e) {
            $oCon->QueryT('ROLLBACK');
            $oReturn->alert($e->getMessage());
        }

    }
    
    return $oReturn;

}

function validar_cuentas($aForm = '', $cod_cuenta){
	//Definiciones
    global $DSN, $DSN_Ifx;

    if (session_status() !== PHP_SESSION_ACTIVE) {session_start();}
    $oIfx = new Dbo ( );
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    $oReturn = new xajaxResponse ( );
    //variables de sesion
    $idempresa = $_SESSION['U_EMPRESA'];
    $idsucursal = $_SESSION['U_SUCURSAL'];
    $array = ($_SESSION['ARRAY_PINTA']);
	//echo $cod_cuenta;
	
    try {
        $oIfx->QueryT('BEGIN');
        if ($cod_cuenta != ''){
            $sql = "select cuen_mov_cuen
					  from saecuen
					  where	cuen_cod_empr  = '$idempresa' and
					   		cuen_cod_cuen  = '$cod_cuenta'";			
            $tipo_cuenta = consulta_string($sql,'cuen_mov_cuen', $oIfx,'');
			if($tipo_cuenta == 0){
                $mensaje = "Cuenta Contable debe ser de Movimiento";
				$oReturn->assign('gasd_cod_cuen', 'value','');
				$oReturn->alert($mensaje);
            }                    
        }
        
    } catch (Exception $e) {
        $oCon->QueryT('ROLLBACK');
        $oReturn->alert($e->getMessage());
    }
    return $oReturn;
}

function validar_ccostos($aForm = '', $cod_ccosto){
	//Definiciones
    global $DSN, $DSN_Ifx;

    if (session_status() !== PHP_SESSION_ACTIVE) {session_start();}
    $oIfx = new Dbo ( );
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    $oReturn = new xajaxResponse ( );
    //variables de sesion
    $idempresa = $_SESSION['U_EMPRESA'];
    $idsucursal = $_SESSION['U_SUCURSAL'];
    $array = ($_SESSION['ARRAY_PINTA']);
	//echo $cod_cuenta;
	
    try {
        $oIfx->QueryT('BEGIN');
        if ($cod_ccosto != ''){
            $sql = "select ccosn_mov_ccosn
					  from saeccosn
					  where	ccosn_cod_empr  = '$idempresa' and
					   		ccosn_cod_ccosn  = '$cod_ccosto'";			
            $tipo_cuenta = consulta_string($sql,'ccosn_mov_ccosn', $oIfx,'');
			if($tipo_cuenta == 0){
                $mensaje = "Centro de Costos debe ser de Movimiento";
				$oReturn->assign('gasd_cod_ccos', 'value','');
				$oReturn->alert($mensaje);
            }                    
        }
        
    } catch (Exception $e) {
        $oCon->QueryT('ROLLBACK');
        $oReturn->alert($e->getMessage());
    }
    return $oReturn;
}

// CARGAR DATOS DE LA PESTAÑA CUENTAS CONTABLES
function cargarDatosCuentas($aForm = ''){
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
    $idempresa = $_SESSION['U_EMPRESA'];
    // $idsucursal = $_SESSION['U_SUCURSAL'];
    $array = ($_SESSION['ARRAY_PINTA']);
    $usuario_web = $_SESSION['U_ID'];

    //variables formulario
    $codigoActivo = $aForm['act_cod_act'];

    //$oReturn->alert($continente);
    try {
        $oIfx->QueryT('BEGIN');
        if ($codigoActivo != '' and $idempresa != ''){
            // DATOS DEL ACTIVO
            $sql = "select act_cod_sucu from saeact where act_cod_empr = $idempresa and act_cod_act = $codigoActivo ";
            $idsucursal = consulta_string_func($sql,'act_cod_sucu', $oIfx,'');    

            $sql = "select gasd_cod_cuen, gasd_cod_ccos, gasd_val_porc, gasd_rev_sn
					from saegasd 
					where gasd_cod_acti = $codigoActivo
					and gasd_cod_empr = $idempresa
					and gasd_cod_sucu = $idsucursal
					order by gasd_rev_sn";
            //$oReturn->alert($sql);
            if($oIfx->Query($sql))	{
                if ($oIfx->NumFilas() > 0){
                    $html.='<table class="table table-bordered table-striped table-condensed" style="width: 100%; margin-bottom: 0px;">
                                <tr class="msgFrm">
                                    <td class="bg-primary" align="center"> Cuenta </td>
                                    <td class="bg-primary" align="center"> Centro de Costos </td>
                                    <td class="bg-primary" align="center"> Valor % </td>
                                    <td class="bg-primary" align="center"> Tipo </td>									
                                    <td class="bg-primary" align="center"> Editar </td>
                                    <td class="bg-primary" align="center"> Eliminar </td>
                                </tr> ';
                    do {
                        $codigoCuenta 	=  $oIfx->f('gasd_cod_cuen');
                        $codigoCCos 	=  $oIfx->f('gasd_cod_ccos');
                        $valorPorc 	    =  $oIfx->f('gasd_val_porc');
                        $gasd_rev_sn 	=  $oIfx->f('gasd_rev_sn');
						if ($gasd_rev_sn == 'S'){
							$tipo_reva_sn = 'Revalorizacion';
						} else {
							$tipo_reva_sn = 'Normal';
						}
                        $html.='<tr>
                                    <td>'.$codigoCuenta.' </td> 
                                    <td>'.$codigoCCos.' </td> 
                                    <td>'.$valorPorc.' </td>
                                    <td>'.$tipo_reva_sn.' </td>									
                                    <td style="width: 10%;">
                                        <div id ="'.$codigoCCos.'" class="btn btn-success btn-sm" onclick="editar_cta_gast(\'' . $codigoCuenta . '\',\'' . $codigoCCos . '\',\'' . $valorPorc . '\',\'' . $gasd_rev_sn . '\')">
								            <span class="glyphicon glyphicon-pencil"><span>
							            </div></td> 
                                    <td style="width: 10%;"> 
                                        <div id ="'.$codigoCCos.'" class="btn btn-success btn-sm" onclick="eliminar_cta_gast(\'' . $codigoCuenta . '\',\'' . $codigoCCos . '\',\'' . $valorPorc . '\',\'' . $gasd_rev_sn . '\')">
								            <span class="glyphicon glyphicon-remove"><span>
							            </div>
							        </td> 
                                                                        
                                </tr>';

                    }while ($oIfx->SiguienteRegistro());
                    $html.= '</table>';
                }
            }
            //$oReturn->alert($sql);
        }
    $oReturn->assign("cuentasGrabadas","innerHTML", $html);
    } catch (Exception $e) {
        $oCon->QueryT('ROLLBACK');
        $oReturn->alert($e->getMessage());
    }
    return $oReturn;
}

// ELIMINA DATOS DE LA PESTAÑA CUENTAS CONTABLES
function eliminar_cta_gast($aForm = '', $cuenta='', $ccos='', $valor=''){
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
	$idempresa = $_SESSION['U_EMPRESA'];
	$idsucursal = $_SESSION['U_SUCURSAL'];
	$array = ($_SESSION['ARRAY_PINTA']);
	
	//variables formulario
	$codigoActivo 	 =  $aForm['act_cod_act'];
	$codigoCuenta    =  $aForm['gasd_cod_cuen'];
	$centroCosto     =  $aForm['gasd_cod_ccos'];
	$valorPorcentaje =  $aForm['gasd_val_porc'];
	$revalo_sn 		 =  $aForm['gasd_rev_sn'];
	
	//$oReturn->alert($continente);
	try {
		$oIfx->QueryT('BEGIN');
		if ($codigoActivo != ''){
		
		
		$sql="delete from saegasd
                 where
			        gasd_cod_empr = '$idempresa' and
			        gasd_cod_acti = '$codigoActivo' and
			        gasd_cod_sucu = '$idsucursal' and
			        gasd_cod_ccos = '$ccos' and
					gasd_rev_sn   = '$revalo_sn'";
	//echo $sql;exit;
			$oIfx->QueryT($sql);
		}
		$oReturn->script("recarga_cta_gasto();");
		$oIfx->QueryT('COMMIT WORK;');
	} catch (Exception $e) {
		$oCon->QueryT('ROLLBACK');
		$oReturn->alert($e->getMessage());
	}
	return $oReturn;
	
}

function cargarDatosResponsables($aForm = ''){
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
    $idempresa = $_SESSION['U_EMPRESA'];
    // $idsucursal = $_SESSION['U_SUCURSAL'];
    $array = ($_SESSION['ARRAY_PINTA']);
    $usuario_web = $_SESSION['U_ID'];

    //variables formulario
    $codigoActivo = $aForm['act_cod_act'];

    //$oReturn->alert($continente);
    try {
        $oIfx->QueryT('BEGIN');
        if ($codigoActivo != '' and $idempresa != ''){
            $sql = "select act_cod_sucu from saeact where act_cod_empr = $idempresa and act_cod_act = $codigoActivo ";
            $idsucursal = consulta_string_func($sql,'act_cod_sucu', $oIfx,'');    

            // DATOS DEL ACTIVO
            $sql = "select cxa_cod_cxa, empl_cod_empl,
                            (select empl_ape_nomb from saeempl 
                             where empl_cod_empl = saecxa.empl_cod_empl
                             and empl_cod_empr = saecxa.act_cod_empr) as empleado,
                             cxa_ubic_cxa, estr_cod_estr,
                            (select estr_des_estr from saeestr
                             where estr_cod_estr = saecxa.estr_cod_estr
                             and estr_cod_empr = saecxa.act_cod_empr) as cargo,
                             cxa_fech_cxa, cxa_obs_cxa
                    from saecxa
                    where act_cod_act = $codigoActivo
                    and act_cod_empr = $idempresa
                    and act_cod_sucu = $idsucursal";
            //$oReturn->alert($sql);
            if($oIfx->Query($sql))	{
                if ($oIfx->NumFilas() > 0){
                    $html.='<table class="table table-bordered table-striped table-condensed" style="width: 100%; margin-bottom: 0px;">
                                <tr class="msgFrm">                                
                                    <td class="bg-primary"> ID </td>
                                    <td class="bg-primary"> Empleado </td>
                                    <td class="bg-primary"> Cargo </td>
                                    <td class="bg-primary"> Ubicacion </td>
                                    <td class="bg-primary"> Fecha </td>
                                    <td class="bg-primary"> Observaciones </td>
                                    <td class="bg-primary"> Editar </td>
                                    <td class="bg-primary"> Eliminar </td>
                                </tr> ';
                    do {
                        $cod_cxa 	 =  $oIfx->f('cxa_cod_cxa');
                        $empleado 	 =  $oIfx->f('empleado');
                        $cargo 	     =  $oIfx->f('cargo');
                        $ubicacion 	 =  $oIfx->f('cxa_ubic_cxa');
                        $fecha 	     =  $oIfx->f('cxa_fech_cxa');
                        $obs 	     =  $oIfx->f('cxa_obs_cxa');
                        $codEmpleado =  $oIfx->f('empl_cod_empl');
                        $codCargo    =  $oIfx->f('estr_cod_estr');

                        $html.='<tr>
                                    <td>'.$cod_cxa.' </td> 
                                    <td>'.$empleado.' </td> 
                                    <td>'.$cargo.' </td>
                                    <td>'.$ubicacion.' </td>
                                    <td>'.$fecha.' </td>
                                    <td>'.$obs.' </td>
                                    <td style="width: 10%;">
                                        <div id ="'.$cod_cxa.'" class="btn btn-success btn-sm" onclick="editar_responsables(\'' . $codEmpleado . '\',\'' . $empleado . '\',\'' . $codCargo . '\',\'' . $ubicacion . '\',\'' . $fecha . '\',\'' . $obs . '\',\'' . $cod_cxa . '\')">
								            <span class="glyphicon glyphicon-pencil"><span>
							            </div></td> 
                                    <td style="width: 10%;"> 
                                        <div id ="'.$cod_cxa.'" class="btn btn-success btn-sm" onclick="eliminar_responsables(\'' . $cod_cxa . '\',\'' . $codEmpleado . '\',\'' . $codCargo . '\')">
								            <span class="glyphicon glyphicon-remove"><span>
							            </div>
							        </td> 
                                                                        
                                </tr>';

                    }while ($oIfx->SiguienteRegistro());
                    $html.= '</table>';
                }
            }
            //$oReturn->alert($sql);
        }
        $oReturn->assign("responsablesGrabadas","innerHTML", $html);
    } catch (Exception $e) {
        $oCon->QueryT('ROLLBACK');
        $oReturn->alert($e->getMessage());
    }
    return $oReturn;
}

function grabarDetallePartes($aForm = ''){
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
    $idempresa = $_SESSION['U_EMPRESA'];
    // $idsucursal = $_SESSION['U_SUCURSAL'];
    $array = ($_SESSION['ARRAY_PINTA']);
	
    //variables formulario
    $codigoActivo 	 =  $aForm['act_cod_act'];
	$codParte	 	 =  $aForm['part_cod_part'];
    if(empty($codParte)){
        $codParte='null';
    }
    $nombre  		 =  $aForm['part_nom_part'];
    $estado   		 =  $aForm['estado'];
    $marca       	 =  $aForm['part_marc_part'];
    $modelo       	 =  $aForm['part_modl_part'];	
    $serie           =  $aForm['part_seri_part'];
    $color           =  $aForm['part_colr_pat'];
    $observacion     =  $aForm['part_obs_part'];
	$cantidad		 =  $aForm['part_cant_part'];
	
    //VALIDCIONES
    $part_sn= $aForm['act_part_act'];
    if($part_sn==0){
        $oReturn->alert('Marque la opcion SI en la pestaña Datos Generales Partes:');
    }
    elseif(empty($estado)){
        $oReturn->alert('Seleccione el Estado');
    }
    elseif(empty($nombre)){
        $oReturn->alert('Ingrese el nombre de la parte');
        $oReturn->script("foco('part_nom_part')");
    }
    elseif(empty($marca)){
        $oReturn->alert('Ingrese la marca');
        $oReturn->script("foco('part_marc_part')");
    }
    elseif(empty($modelo)){
        $oReturn->alert('Ingrese el modelo');
        $oReturn->script("foco('part_modl_part')");
    }
    elseif(empty($serie)){
        $oReturn->alert('Ingrese la serie');
        $oReturn->script("foco('part_seri_part')");
    }
    elseif(empty($color)){
        $oReturn->alert('Ingrese el color');
        $oReturn->script("foco('part_colr_pat')");
    }
    elseif($cantidad<=0){
        $oReturn->alert('La cantidad debe ser mayo a 0');
        $oReturn->script("foco('part_cant_part')");
    }
    else{

        try {
            $oIfx->QueryT('BEGIN');
            if ($codigoActivo != ''){
                $sql = "select act_cod_sucu from saeact where act_cod_empr = $idempresa and act_cod_act = $codigoActivo ";
                $idsucursal = consulta_string_func($sql,'act_cod_sucu', $oIfx,''); 	
    
                $sql = "select count(*) as contador
                        from saepact
                        where	act_cod_empr  = '$idempresa' and
                                   act_cod_sucu  = '$idsucursal' and
                                   act_cod_act   = '$codigoActivo' and
                                   part_cod_part = $codParte";

                $contador = consulta_string($sql,'contador', $oIfx,0);
                if($contador==0){
                    $sql = " INSERT INTO saepact (act_cod_empr,	act_cod_sucu,	eact_cod_eact,	part_nom_part,
                                                  act_cod_act,      part_marc_part,	part_modl_part, part_seri_part, part_colr_pat, 
                                                  part_obs_part, 	part_cant_part)
                                           VALUES($idempresa,     $idsucursal,    '$estado', 	'$nombre', 
                                                  $codigoActivo,	'$marca', 		'$modelo',		'$serie', 	'$color',
                                                  '$observacion',	$cantidad )";
                }else{
                    $sql="update saepact
                             set eact_cod_eact = $estado, part_marc_part = '$marca', part_modl_part = '$modelo',
                                part_seri_part = '$serie', part_colr_pat = '$color', part_obs_part = '$observacion', part_cant_part = $cantidad
                              where act_cod_empr  = $idempresa and
                                     act_cod_sucu  = $idsucursal and
                                     act_cod_act   = '$codigoActivo' and
                                     part_cod_part = $codParte";
                }
    
    
                $mensaje = "Datos Grabados";
                $oIfx->QueryT($sql);
            }
            else{
                $mensaje = "Seleccione un Activo";
            }
    
    
            $oReturn->alert($mensaje);
            $oReturn->script("recargaPartes();");
            $oIfx->QueryT('COMMIT WORK;');
        } catch (Exception $e) {
            $oCon->QueryT('ROLLBACK');
            $oReturn->alert($e->getMessage());
        }

    }
    
    
    return $oReturn;
}

// CARGAR DATOS DETALLES DE PARTES
function cargarDatosPartes($aForm = ''){
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
    $idempresa = $_SESSION['U_EMPRESA'];
    // $idsucursal = $_SESSION['U_SUCURSAL'];
    $array = ($_SESSION['ARRAY_PINTA']);
    $usuario_web = $_SESSION['U_ID'];

    //variables formulario
    $codigoActivo = $aForm['act_cod_act'];

    //$oReturn->alert($continente);
    try {
        $oIfx->QueryT('BEGIN');
        if ($codigoActivo != '' and $idempresa != ''){
            $sql = "select act_cod_sucu from saeact where act_cod_empr = $idempresa and act_cod_act = $codigoActivo ";
            $idsucursal = consulta_string_func($sql,'act_cod_sucu', $oIfx,''); 	

            // DATOS PARTES
            $sql = "select eact_cod_eact, part_nom_part, part_marc_part, part_modl_part, part_seri_part, 
					part_colr_pat,  part_obs_part, part_cant_part, part_cod_part                     
                    from saepact
                    where act_cod_act = $codigoActivo
                    and act_cod_empr = $idempresa
                    and act_cod_sucu = $idsucursal";
            //$oReturn->alert($sql);
            if($oIfx->Query($sql))	{
                if ($oIfx->NumFilas() > 0){
                    $html.='<table class="table table-bordered table-striped table-condensed" style="width: 100%; margin-bottom: 0px;">
                                <tr class="msgFrm">                                
                                    <td class="bg-primary" align = "center"> Estado </td>
                                    <td class="bg-primary" align = "center"> Parte </td>
                                    <td class="bg-primary" align = "center"> Marca </td>
                                    <td class="bg-primary" align = "center"> Modelo </td>
                                    <td class="bg-primary" align = "center"> Serie </td>
                                    <td class="bg-primary" align = "center"> Color </td>
                                    <td class="bg-primary" align = "center"> Cantidad </td>
                                    <td class="bg-primary" align = "center"> Observaci&oacuten </td>
                                    <td class="bg-primary" align = "center"> Editar </td>
                                    <td class="bg-primary" align = "center"> Eliminar </td>
                                </tr> ';
                    do {
						$part_cod_part   =  $oIfx->f('part_cod_part');
                        $eact_cod_eact 	 =  $oIfx->f('eact_cod_eact');

                        
                        $sql="select eact_desc_eact 
                        from saeeact where eact_cod_eact= $eact_cod_eact ";
                        $est_eact=consulta_string($sql,'eact_desc_eact',$oCon,'');

                        $part_nom_part 	 =  $oIfx->f('part_nom_part');
                        $part_marc_part  =  $oIfx->f('part_marc_part');
                        $part_modl_part  =  $oIfx->f('part_modl_part');
                        $part_seri_part  =  $oIfx->f('part_seri_part');
                        $part_colr_pat 	 =  $oIfx->f('part_colr_pat');
                        $part_cant_part  =  $oIfx->f('part_cant_part');
                        $part_obs_part   =  $oIfx->f('part_obs_part');

                        $html.='<tr>
                                    <td>'.$est_eact.' </td> 
                                    <td>'.$part_nom_part.' </td> 
                                    <td>'.$part_marc_part.' </td>
                                    <td>'.$part_modl_part.' </td>
                                    <td>'.$part_seri_part.' </td>
                                    <td>'.$part_colr_pat.' </td>
                                    <td>'.$part_cant_part.' </td>
                                    <td>'.$part_obs_part.' </td>
                                    <td style="width: 10%;">
                                        <div class="btn btn-success btn-sm" onclick="editarPartes(\'' . $part_cod_part . '\',\'' . $eact_cod_eact . '\',\'' . $part_nom_part . '\',\'' . $part_marc_part . '\',\'' . $part_modl_part . '\',\'' . $part_seri_part . '\',\'' . $part_colr_pat . '\',\'' . $part_cant_part . '\',\'' . $part_obs_part . '\')">
								            <span class="glyphicon glyphicon-pencil"><span>
							            </div></td> 
                                    <td style="width: 10%;"> 
                                        <div id ="'.$part_cod_part.'" class="btn btn-success btn-sm" onclick="eliminarPartes(\'' . $part_cod_part . '\',\'' . $codigoActivo . '\')">
								            <span class="glyphicon glyphicon-remove"><span>
							            </div>
							        </td> 
                                                                        
                                </tr>';

                    }while ($oIfx->SiguienteRegistro());
                    $html.= '</table>';
                }
            }
            //$oReturn->alert($sql);
        }
        $oReturn->assign("gridPartes","innerHTML", $html);
    } catch (Exception $e) {
        $oCon->QueryT('ROLLBACK');
        $oReturn->alert($e->getMessage());
    }
    return $oReturn;
}

function eliminarDetallesPartes($aForm = '', $codigoParte = '', $codigoActivo = ''){
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
    $idempresa  =  $_SESSION['U_EMPRESA'];
    $idsucursal =  $_SESSION['U_SUCURSAL'];
    $array      = ($_SESSION['ARRAY_PINTA']);

    //$oReturn->alert($continente);
    try {
        $oIfx->QueryT('BEGIN');
        if ($codigoActivo != ''){


            $sql="delete from saepact
                 where part_cod_part = '$codigoParte' 
			     and act_cod_empr  = '$idempresa' 
			     and act_cod_act   = '$codigoActivo' 
			     and act_cod_sucu  = '$idsucursal'";
            //echo $sql;exit;
            $oIfx->QueryT($sql);
        }


        $oReturn->script("recargaPartes();");
        $oIfx->QueryT('COMMIT WORK;');
    } catch (Exception $e) {
        $oCon->QueryT('ROLLBACK');
        $oReturn->alert($e->getMessage());
    }
    return $oReturn;	
}

function eliminar_responsables($aForm = '', $codCxa='', $codEmpleado='', $codCargo='' ){
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
    $idempresa = $_SESSION['U_EMPRESA'];
    $idsucursal = $_SESSION['U_SUCURSAL'];

    $array = ($_SESSION['ARRAY_PINTA']);

    //variables formulario
    $codigoActivo 	 =  $aForm['act_cod_act'];
    //$oReturn->alert($continente);
    try {
        $oIfx->QueryT('BEGIN');
        if ($codigoActivo != ''){


            $sql="delete from saecxa
                 where cxa_cod_cxa = $codCxa 
			     and empl_cod_empl = '$codEmpleado'
			     and act_cod_empr  = $idempresa 
			     and act_cod_act   = $codigoActivo 
			     and estr_cod_estr = '$codCargo'";
            $oIfx->QueryT($sql);
        }
        $oIfx->QueryT('COMMIT WORK;');
        $oReturn->alert('Eliminado Correctamente');
        $oReturn->script("recarga_responsables();");
        
    } catch (Exception $e) {
        $oCon->QueryT('ROLLBACK');
        $oReturn->alert($e->getMessage());
    }
    return $oReturn;
}

/// cargar mantenimineto
function cargarDatosMantenimiento($aForm = ''){
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
	$idempresa = $_SESSION['U_EMPRESA'];
	// $idsucursal = $_SESSION['U_SUCURSAL'];
	$array = ($_SESSION['ARRAY_PINTA']);
	$usuario_web = $_SESSION['U_ID'];
	
	//variables formulario
	$codigoActivo = $aForm['act_cod_act'];
	
	//$oReturn->alert($continente);
	try {
		$oIfx->QueryT('BEGIN');
		if ($codigoActivo != '' and $idempresa != ''){
            $sql = "select act_cod_sucu from saeact where act_cod_empr = $idempresa and act_cod_act = $codigoActivo ";
            $idsucursal = consulta_string_func($sql,'act_cod_sucu', $oIfx,''); 	

			// secuencial
			$sql = "select count(*) as condicion from saemant where act_cod_empr='$idempresa' and act_cod_sucu='$idsucursal' and act_cod_act='$codigoActivo'";
			$condicion=consulta_string($sql, 'condicion', $oIfx, 0);
			
			$sql="select max(mant_sec_docu) as secuencial from saemant where act_cod_empr='$idempresa' and act_cod_sucu='$idsucursal' and act_cod_act='$codigoActivo'";
			$secuencial=consulta_string($sql, 'secuencial', $oIfx, 0);
            $secuencial++;
		//	echo $condicion%2;exit;
			if ($condicion%2==0){
				
				
				$oReturn->assign("tipo_mov", "value","S" );
			}else{
				$oReturn->assign("tipo_mov", "value","" );
			}
			
			$oReturn->assign("sec_docu", "value",$secuencial );
			
			// DATOS DEL ACTIVO
			$sql = "select *
                    from saemant
                    where act_cod_empr='$idempresa' and act_cod_sucu='$idsucursal'
                     and act_cod_act='$codigoActivo' ORDER  by mant_sec_docu asc, mant_tip_movi asc";
			//$oReturn->alert($sql);
			if($oIfx->Query($sql))	{
				if ($oIfx->NumFilas() > 0){
					$html.='<table class="table table-bordered table-striped table-condensed" style="width: 100%; margin-bottom: 0px;">
                                <tr class="msgFrm">
                                    <td class="bg-primary"> Movimiento </td>
                                    <td class="bg-primary"> Documento </td>
                                    <td class="bg-primary"> Causa </td>
                                    <td class="bg-primary"> Referencia </td>
                                    <td class="bg-primary"> Taller </td>
                                    <td class="bg-primary"> Fecha Inicio </td>
                                    <td class="bg-primary"> Fecha Entrega </td>
                                    <td class="bg-primary"> Costo </td>
                                    <td class="bg-primary"> Responsable </td>
                                    <td class="bg-primary"> Observaciones </td>
                                    <td class="bg-primary"> Editar </td>
                                    <td class="bg-primary"> Eliminar </td>
                                </tr> ';
					do {
						$mant_tip_movi 	 =  $oIfx->f('mant_tip_movi');
						$mant_sec_docu 	 =  $oIfx->f('mant_sec_docu');
						$mant_caus_mant  =  $oIfx->f('mant_caus_mant');
						$mant_ref_mant 	 =  $oIfx->f('mant_ref_mant');
						$mant_tall_mant  =  $oIfx->f('mant_tall_mant');
						$mant_fini_mant  =  $oIfx->f('mant_fini_mant');
						$mant_fent_mant  =  $oIfx->f('mant_fent_mant');
						$mant_cost_mant  =  $oIfx->f('mant_cost_mant');
						$mant_resp_mant	 =  $oIfx->f('mant_resp_mant');
						$mant_obs_mant	 =  $oIfx->f('mant_obs_mant');
						
						if($mant_tip_movi=='I'){
							$mant_tip='Ingreso';
						}else{
							$mant_tip='Salida';
						}
						
						$html.='<tr>
                                    <td>'.$mant_tip.' </td>
                                    <td>'.$mant_sec_docu.' </td>
                                    <td>'.$mant_caus_mant.' </td>
                                    <td>'.$mant_ref_mant.' </td>
                                    <td>'.$mant_tall_mant.' </td>
                                    <td>'.$mant_fini_mant.' </td>
                                    <td>'.$mant_fent_mant.' </td>
                                    <td>'.$mant_cost_mant.' </td>
                                    <td>'.$mant_resp_mant.' </td>
                                    <td>'.$mant_obs_mant.' </td>
                                   
                                    <td style="width: 10%;">
                                        <div class="btn btn-success btn-sm" onclick="editar_mantenimineto(\'' . $mant_tip_movi . '\',\'' . $mant_sec_docu . '\',\'' . $mant_caus_mant . '\',\'' . $mant_ref_mant . '\',\'' . $mant_tall_mant . '\',\'' . $mant_fini_mant . '\',\'' . $mant_fent_mant . '\',\'' . $mant_cost_mant . '\',\'' . $mant_resp_mant . '\',\'' . $mant_obs_mant . '\')">
								            <span class="glyphicon glyphicon-pencil"><span>
							            </div></td>
                                    <td style="width: 10%;">
                                        <div  class="btn btn-success btn-sm" onclick="eliminar_mantenimineto(\'' . $mant_tip_movi . '\',\'' . $mant_sec_docu . '\')">
								            <span class="glyphicon glyphicon-remove"><span>
							            </div>
							        </td>
                                
                                </tr>';
						
					}while ($oIfx->SiguienteRegistro());
					$html.= '</table>';
				}
			}
			//$oReturn->alert($sql);
		}
		$oReturn->assign("divMantenimientoGrabadas","innerHTML", $html);
	} catch (Exception $e) {
		$oCon->QueryT('ROLLBACK');
		$oReturn->alert($e->getMessage());
	}
	return $oReturn;
}

/// guarda mantenimineto
function guardarMantenimiento($aForm=""){
	//Definiciones
	global $DSN, $DSN_Ifx;
	
	if (session_status() !== PHP_SESSION_ACTIVE) {session_start();}
	
	$oCon = new Dbo ( );
	$oCon->DSN = $DSN;
	$oCon->Conectar();
	
	$oIfx = new Dbo ( );
	$oIfx->DSN = $DSN_Ifx;
	$oIfx->Conectar();
	$mant_tip_movi=$aForm['mant_tip_movi'];
	$mant_sec_ducu=$aForm['sec_docu'];

    if(empty($mant_sec_ducu)){
        $mant_sec_ducu='null';
    }
	//echo $mant_sec_ducu;exit;
	$mant_caus_mant=$aForm['mant_caus_mant'];
	$mant_ref_mant=$aForm['mant_ref_mant'];
	$mant_tall_mant=$aForm['mant_tall_mant'];
	$mant_fini_mant=$aForm['mant_fini_mant'];
	$mant_fent_mant=$aForm['mant_fent_mant'];
	$mant_cost_mant=$aForm['mant_cost_mant'];
	$mant_resp_mant=$aForm['mant_resp_mant'];
	$mant_obs_mant=$aForm['mant_obs_mant'];
	
	$codigoActivo 	 =  $aForm['act_cod_act'];
	$tipo_mov 	 =  $aForm['tipo_mov'];
	$oReturn = new xajaxResponse ( );
	//variables de sesion
	$idempresa = $_SESSION['U_EMPRESA'];
    // $idsucursal = $_SESSION['U_SUCURSAL'];
    
    $sql = "select act_cod_sucu from saeact where act_cod_empr = $idempresa and act_cod_act = $codigoActivo ";
    $idsucursal = consulta_string_func($sql,'act_cod_sucu', $oIfx,''); 	
    
    if(empty($mant_caus_mant)){
        $oReturn->alert('Ingrese la Causa');
        $oReturn->script("foco('mant_caus_mant')");
    }
    elseif(empty($mant_ref_mant)){
        $oReturn->alert('Ingrese la Referencia');
        $oReturn->script("foco('mant_ref_mant')");
    }
    elseif(empty($mant_tall_mant)){
        $oReturn->alert('Ingrese el Taller');
        $oReturn->script("foco('mant_tall_mant')");
    }
    elseif(empty($mant_fini_mant)){
        $oReturn->alert('Ingrese la Fecha de Inicio');
        $oReturn->script("foco('mant_fini_mant')");
    }
    elseif(empty($mant_fent_mant)){
        $oReturn->alert('Ingrese la Fecha de Entrega');
        $oReturn->script("foco('mant_fent_mant')");
    }
    elseif(empty($mant_cost_mant)){
        $oReturn->alert('Ingrese el Costo');
        $oReturn->script("foco('mant_cost_mant')");
    }
    elseif(empty($mant_resp_mant)){
        $oReturn->alert('Ingrese el Responsable');
        $oReturn->script("foco('mant_resp_mant')");
    }
    else{

        try {
            $oIfx->QueryT('BEGIN');
        
        $sql="select count (*) as contador
                 from saemant
                  WHERE
                  act_cod_empr=$idempresa and
                   act_cod_sucu=$idsucursal and
                   act_cod_act=$codigoActivo AND
                   mant_sec_docu=$mant_sec_ducu and
                   mant_tip_movi='$mant_tip_movi'";
        
        //	echo $sql;exit;
        $control=consulta_string($sql, 'contador', $oIfx, 0);
        if($control==0){
            if($mant_tip_movi=='S'){
                if($mant_tip_movi=="S"){
                    
                    $sql="insert into saemant (mant_caus_mant, act_cod_act, act_cod_empr, act_cod_sucu, mant_ref_mant, mant_tall_mant, mant_fini_mant,mant_fent_mant, mant_cost_mant,mant_resp_mant,mant_obs_mant, mant_sec_docu , mant_tip_movi  )
                                values('$mant_caus_mant', '$codigoActivo', '$idempresa','$idsucursal','$mant_ref_mant', '$mant_tall_mant', '$mant_fini_mant', '$mant_fent_mant ',
                                '$mant_cost_mant','$mant_resp_mant',  '$mant_obs_mant', '$mant_sec_ducu','$mant_tip_movi')";
                    $mensaje="Datos Procesados";
                }else{
                    $mensaje="Debe de ingresa una salida";
                }
            
            }else{
                if($mant_tip_movi=="I"){
                    $sql="insert into saemant (mant_caus_mant, act_cod_act, act_cod_empr, act_cod_sucu, mant_ref_mant, mant_tall_mant, mant_fini_mant,mant_fent_mant, mant_cost_mant,mant_resp_mant,mant_obs_mant, mant_sec_docu, mant_tip_movi   )
                                values('$mant_caus_mant', '$codigoActivo', '$idempresa','$idsucursal','$mant_ref_mant', '$mant_tall_mant', '$mant_fini_mant', '$mant_fent_mant ',
                                '$mant_cost_mant','$mant_resp_mant',  '$mant_obs_mant', '$mant_sec_ducu', '$mant_tip_movi')";
                    $mensaje="Datos Procesados";
                }
            }
            
        }else{
            $sql="update saemant set mant_tip_movi='$mant_tip_movi', mant_sec_docu='$mant_sec_ducu',mant_caus_mant='$mant_caus_mant', mant_ref_mant='$mant_ref_mant',
                                     mant_tall_mant='$mant_tall_mant', mant_fini_mant='$mant_fini_mant', mant_fent_mant='$mant_fent_mant',mant_cost_mant='$mant_cost_mant',
                                     mant_resp_mant='$mant_resp_mant', mant_obs_mant='$mant_obs_mant'
                                      WHERE
                  act_cod_empr='$idempresa' and
                   act_cod_sucu='$idsucursal' and
                   act_cod_act='$codigoActivo' AND
                   mant_sec_docu='$mant_sec_ducu' and
                   mant_tip_movi='$mant_tip_movi'";
            $mensaje="Datos Procesados";
        }
        //echo $sql;exit;
        $oIfx->QueryT($sql);
        $oIfx->QueryT('COMMIT WORK;');
        $oReturn->alert($mensaje);
        $oReturn->script('recarga_Mantenimiento()');
        $sql="select max(mant_sec_docu) as secuencial from saemant where act_cod_empr='$idempresa' and act_cod_sucu='$idsucursal' and act_cod_act='$codigoActivo'";
			$secuencial=consulta_string($sql, 'secuencial', $oIfx, 0);
            $secuencial++;
			$oReturn->assign("sec_docu", "value",$secuencial );
            $oReturn->assign("mant_caus_mant", "value",'' );
            $oReturn->assign("mant_ref_mant", "value",'' );
            $oReturn->assign("mant_tall_mant", "value",'' );
            $oReturn->assign("mant_fent_mant", "value",'' );
            $oReturn->assign("mant_fini_mant", "value",'' );
            $oReturn->assign("mant_cost_mant", "value",'' );
            $oReturn->assign("mant_resp_mant", "value",'' );
            $oReturn->assign("mant_obs_mant", "value",'' );

        } catch (Exception $e) {
            $oCon->QueryT('ROLLBACK');
            $oReturn->alert($e->getMessage());
        }

    }

    
	
	return $oReturn;
}

/// elimina mantenimineto
function eliminar_mantenimineto($aForm = '',$mant_tip_movi="",$mant_sec_docu =""){
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
	$idempresa = $_SESSION['U_EMPRESA'];
	$idsucursal = $_SESSION['U_SUCURSAL'];
	
	
	//variables formulario
	$codigoActivo 	 =  $aForm['act_cod_act'];
	
	//$oReturn->alert($continente);
	try {
		$oIfx->QueryT('BEGIN');
		if ($codigoActivo != ''){
			
			
			$sql="delete from
                 saemant
 			 WHERE
 			 act_cod_empr=$idempresa and
 			  act_cod_sucu=$idsucursal and
 			  act_cod_act=$codigoActivo AND
 			  mant_sec_docu=$mant_sec_docu and
 			  mant_tip_movi='$mant_tip_movi'";
		//	echo $sql;exit;
			$oIfx->QueryT($sql);
		}
		
		
		
		$oReturn->script("recarga_Mantenimiento();");
		$oIfx->QueryT('COMMIT WORK;');
	} catch (Exception $e) {
		$oCon->QueryT('ROLLBACK');
		$oReturn->alert($e->getMessage());
	}
	return $oReturn;
	
}

// Guardar Otros Detalles
function guardarOtros($aForm = ''){
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
    $idempresa = $_SESSION['U_EMPRESA'];
    // $idsucursal = $_SESSION['U_SUCURSAL'];
    $array = ($_SESSION['ARRAY_PINTA']);

    //variables formulario
   // $codigo          =  $aForm['sac_cod_aseg'];
    $codSeguroActivo =  $aForm['sact_cod_sact'];
    $codigoActivo 	 =  $aForm['act_cod_act'];
    $codAseguradora  =  $aForm['sac_cod_aseg'];
    $numeroPoliza    =  $aForm['sact_poli_sact'];
    $fechaEmision    =  $aForm['sact_fech_sact'];
    $fechaVecimineto =  $aForm['sact_fven_sact'];
    $montoAsegurado  =  $aForm['sact_val_sact'];
    $valorDeducible  =  $aForm['sact_dedu_sact'];
    $matricula       =  $aForm['sac_num_matr'];
    $motor           =  $aForm['sac_num_motr'];
    $chasis          =  $aForm['sac_num_chsis'];
    $placa           =  $aForm['sac_num_plac'];
    $valorComercial  =  $aForm['sac_val_come'];
 
    $observacion     =  $aForm['sact_obs_sact'];

    $fechaEmision    =  $fechaEmision;

    //$oReturn->alert($continente);

    if(empty($numeroPoliza)){
        $oReturn->alert('Ingrese el Numero de Poliza');
        $oReturn->script("foco('sact_poli_sact')");
    }
    elseif(empty($codAseguradora)){
        $oReturn->alert('Ingrese la Aseguradora');
        $oReturn->script("foco('sact_cod_aseg')");
    }
    elseif(empty($fechaEmision)){
        $oReturn->alert('Ingrese la Fecha de Emision');
        $oReturn->script("foco('sact_fech_sact')");
    }
    elseif(empty($fechaVecimineto)){
        $oReturn->alert('Ingrese la Fecha de Vnecimiento');
        $oReturn->script("foco('sact_fven_sact')");
    }
    elseif(empty($montoAsegurado)){
        $oReturn->alert('Ingrese el Monto');
        $oReturn->script("foco('sact_val_sact')");
    }
    elseif(empty($valorDeducible)){
        $oReturn->alert('Ingrese el Valor Deducible');
        $oReturn->script("foco('sact_dedu_sact')");
    }
    elseif(empty($valorComercial)){
        $oReturn->alert('Ingrese el Valor Comercial');
        $oReturn->script("foco('sac_val_come')");
    }
    else{
        try {
            $oIfx->QueryT('BEGIN');
            if ($codigoActivo != ''){
                $sql = "select act_cod_sucu from saeact where act_cod_empr = $idempresa and act_cod_act = $codigoActivo ";
                $idsucursal = consulta_string_func($sql,'act_cod_sucu', $oIfx,''); 	
    
                $sql = "select
                            count(*) as contador
                          from saesac
                          where
                                   act_cod_empr   = $idempresa and
                                   act_cod_sucu   = $idsucursal and
                                   act_cod_act    = $codigoActivo and
                                   sac_cod_aseg   = $codAseguradora and
                                   sact_poli_sact = '$numeroPoliza'";
               // echo $sql;exit;
                $contador = consulta_string($sql,'contador', $oIfx,0);
                if($contador==0){
                    $sql = " INSERT INTO saesac
                               ( sact_nom_sact ,   act_cod_act, 	 
                                 act_cod_empr, 	    act_cod_sucu,     sact_poli_sact,
                                 sact_fech_sact,    sact_fven_sact,   sact_val_sact, 
                                 sact_dedu_sact,    sact_obs_sact,    sac_num_matr, 
                                 sac_num_motr,      sac_num_chsis,    sac_num_plac, 
                                 sac_val_come,      sac_cod_aseg )
                         VALUES( NULL,             $codigoActivo, 
                                 $idempresa,        $idsucursal,     '$numeroPoliza', 
                                 '$fechaEmision',   '$fechaVecimineto', $montoAsegurado,
                                  $valorDeducible,  '$observacion',     '$matricula',
                                 '$motor',          '$chasis',          '$placa',
                                 $valorComercial,   $codAseguradora )";
                }else{
                    $sql="update saesac
                             set sact_poli_sact = '$numeroPoliza',  sact_fech_sact = '$fechaEmision',  sact_fven_sact = '$fechaVecimineto',
                                 sact_val_sact  =  $montoAsegurado, sact_dedu_sact =  $valorDeducible, sact_obs_sact  = '$observacion',
                                 sac_num_matr   = '$matricula',     sac_num_motr   = '$motor',         sac_num_chsis  = '$chasis',
                                 sac_num_plac   = '$placa',         sac_val_come   =  $valorComercial, sac_cod_aseg   = '$codAseguradora'
                              
                              where act_cod_empr  = '$idempresa' and
                                   act_cod_sucu  = '$idsucursal' and
                                   act_cod_act   = '$codigoActivo' and
                                   sact_cod_sact = '$codSeguroActivo'";
                }
                $mensaje = "Datos Grabados";
                $oIfx->QueryT($sql);
            }
    
    
            $oReturn->alert($mensaje);
            $oReturn->script("recargar_otros();");
            $oIfx->QueryT('COMMIT WORK;');
        } catch (Exception $e) {
            $oCon->QueryT('ROLLBACK');
            $oReturn->alert($e->getMessage());
        }

    }

   
    return $oReturn;

}

// Cargar Datos Otros Detalles - Datos Aseguadora
function cargarDatosOtros($aForm = ''){
    //Definiciones
    global $DSN, $DSN_Ifx;

    if (session_status() !== PHP_SESSION_ACTIVE) {session_start();}
    $oIfx = new Dbo ( );
    $oIfx->DSN = $DSN_Ifx;
    $oIfx->Conectar();

    $oReturn = new xajaxResponse ( );
    //variables de sesion
    $idempresa = $_SESSION['U_EMPRESA'];
    // $idsucursal = $_SESSION['U_SUCURSAL'];
    $array = ($_SESSION['ARRAY_PINTA']);
    $usuario_web = $_SESSION['U_ID'];

    //variables formulario
    $codigoActivo = $aForm['act_cod_act'];

    //$oReturn->alert($continente);
    try {
        $oIfx->QueryT('BEGIN');
        if ($codigoActivo != '' and $idempresa != ''){
            $sql = "select act_cod_sucu from saeact where act_cod_empr = $idempresa and act_cod_act = $codigoActivo ";
            $idsucursal = consulta_string_func($sql,'act_cod_sucu', $oIfx,''); 	

            // DATOS DEL ACTIVO
            $sql = " SELECT  saesac.sact_cod_sact,   
                             saesac.act_cod_act,   
                             saesac.act_cod_empr,   
                             saesac.act_cod_sucu,   
                             saesac.sact_poli_sact,   
                             saesac.sact_fech_sact,   
                             saesac.sact_fven_sact,   
                             saesac.sact_val_sact,   
                             saesac.sact_dedu_sact,   
                             saesac.sact_obs_sact,   
                             saesac.sac_num_matr,   
                             saesac.sac_num_motr,   
                             saesac.sac_num_chsis,   
                             saesac.sac_num_plac,   
                             saesac.sac_val_come,   
                             saesac.sac_cod_aseg,
                             (select aseg_des_aseg from saeaseg 
                              where aseg_cod_aseg = sac_cod_aseg and aseg_cod_empr = act_cod_empr) as nom_aseguradora
                        FROM saesac  
                       WHERE ( saesac.act_cod_act   = $codigoActivo ) AND  
                             ( saesac.act_cod_empr  = $idempresa ) AND  
                             ( saesac.act_cod_sucu  = $idsucursal )";
           // $oReturn->alert($sql);
            if($oIfx->Query($sql))	{
                if ($oIfx->NumFilas() > 0){
                    $html.='<table class="table table-bordered table-striped table-condensed" style="width: 100%; margin-bottom: 0px;">
                                <tr class="msgFrm">                                
                                    <td class="bg-primary"> ID </td>
                                    <td class="bg-primary"> Poliza </td>
                                    <td class="bg-primary"> Aseguradora </td>
                                    <td class="bg-primary"> Fecha Emis </td>
                                    <td class="bg-primary"> Fecha Venc</td>
                                    <td class="bg-primary"> Monto </td>
                                    <td class="bg-primary"> Deducible </td>
                                    <td class="bg-primary"> Placa </td>
                                    <td class="bg-primary"> Motor </td>
                                    <td class="bg-primary"> Chasis </td>                                    
                                    <td class="bg-primary"> Valor Comer. </td>
                                    <td class="bg-primary"> Editar </td>
                                    <td class="bg-primary"> Eliminar </td>
                                </tr> ';
                    do {
                        $cod_sact 	 =  $oIfx->f('sact_cod_sact');
                        $numPoliza 	 =  $oIfx->f('sact_poli_sact');
                        $codAsegur 	 =  $oIfx->f('sac_cod_aseg');
                        $nomAsegur 	 =  $oIfx->f('nom_aseguradora');
                        $fechaEmis 	 =  $oIfx->f('sact_fech_sact');
                        $fechaVenc 	 =  $oIfx->f('sact_fven_sact');
                        $motoAsegu	 =  $oIfx->f('sact_val_sact');
                        $deducible   =  $oIfx->f('sact_dedu_sact');
                        $numPlaca    =  $oIfx->f('sac_num_plac');
                        $numMotor    =  $oIfx->f('sac_num_motr');
                        $numChasis   =  $oIfx->f('sac_num_chsis');
                        $valorComer  =  $oIfx->f('sac_val_come');

                        $html.='<tr>
                                    <td>'.$cod_sact.' </td> 
                                    <td>'.$numPoliza.' </td> 
                                    <td>'.$nomAsegur.' </td>
                                    <td>'.$fechaEmis.' </td>
                                    <td>'.$fechaVenc.' </td>
                                    <td>'.$motoAsegu.' </td>
                                    <td>'.$deducible.' </td>
                                    <td>'.$numPlaca.' </td>
                                    <td>'.$numMotor.' </td>
                                    <td>'.$numChasis.' </td>                                    
                                    <td>'.$valorComer.' </td>                                   
                                    <td style="width: 10%;">
                                        <div id ="'.$cod_sact.'" class="btn btn-success btn-sm" onclick="editar_otros(\'' . $cod_sact . '\', \'' . $fechaEmis . '\',\'' . $fechaVenc . '\')">
								            <span class="glyphicon glyphicon-pencil"><span>
							            </div></td> 
                                    <td style="width: 10%;"> 
                                        <div id ="'.$cod_sact.'" class="btn btn-success btn-sm" onclick="eliminar_otros(\'' . $cod_sact . '\')">
								            <span class="glyphicon glyphicon-remove"><span>
							            </div>
							        </td> 
                                                                        
                                </tr>';

                    }while ($oIfx->SiguienteRegistro());
                    $html.= '</table>';
                }
            }
            //$oReturn->alert($sql);
        }
        $oReturn->assign("otrosGrabadas","innerHTML", $html);
    } catch (Exception $e) {
        $oCon->QueryT('ROLLBACK');
        $oReturn->alert($e->getMessage());
    }
    return $oReturn;
}

// Editar Otros Detalles editar_otros_detalles
function editar_otros_detalles($aForm = '', $cod_sact = "",  $fecha_emis = "", $fecha_venc = ""){
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
    $idempresa = $_SESSION['U_EMPRESA'];
    $idsucursal = $_SESSION['U_SUCURSAL'];
    $array = ($_SESSION['ARRAY_PINTA']);
    $usuario_web = $_SESSION['U_ID'];

    //variables formulario
    $codigoActivo = $aForm['act_cod_act'];

    //$oReturn->alert($continente);
    try {
        $oIfx->QueryT('BEGIN');
        if ($codigoActivo != '' and $idempresa != ''){
            // DATOS DEL ACTIVO
            $sql = "SELECT  saesac.sact_cod_sact,   
                             saesac.act_cod_act,   
                             saesac.act_cod_empr,   
                             saesac.act_cod_sucu,   
                             saesac.sact_poli_sact,   
                             saesac.sact_fech_sact,   
                             saesac.sact_fven_sact,   
                             saesac.sact_val_sact,   
                             saesac.sact_dedu_sact,   
                             saesac.sact_obs_sact,   
                             saesac.sac_num_matr,   
                             saesac.sac_num_motr,   
                             saesac.sac_num_chsis,   
                             saesac.sac_num_plac,   
                             saesac.sac_val_come,   
                             saesac.sac_cod_aseg
                        FROM saesac  
                       WHERE ( saesac.act_cod_act   = $codigoActivo ) AND  
                             ( saesac.act_cod_empr  = $idempresa ) AND  
                             ( saesac.act_cod_sucu  = $idsucursal ) AND 
                             ( saesac.sact_cod_sact = $cod_sact)";
            //$oReturn->alert($sql);
            if($oIfx->Query($sql))	{
                if ($oIfx->NumFilas() > 0){
                    $oReturn->assign('sact_poli_sact', 'value',$oIfx->f('sact_poli_sact'));
                    $oReturn->assign('sact_val_sact', 'value',$oIfx->f('sact_val_sact'));
                    $oReturn->assign('sact_dedu_sact', 'value',$oIfx->f('sact_dedu_sact'));
                    $oReturn->assign('sact_obs_sact', 'value',$oIfx->f('sact_obs_sact'));
                    $oReturn->assign('sac_num_matr', 'value',$oIfx->f('sac_num_matr'));
                    $oReturn->assign('sac_num_motr', 'value',$oIfx->f('sac_num_motr'));
                    $oReturn->assign('sac_num_chsis', 'value',$oIfx->f('sac_num_chsis'));
                    $oReturn->assign('sac_num_plac', 'value',$oIfx->f('sac_num_plac'));
                    $oReturn->assign('sac_val_come', 'value',$oIfx->f('sac_val_come'));
                    $oReturn->assign('sac_cod_aseg', 'value',$oIfx->f('sac_cod_aseg'));
                }
            }
            //$oReturn->alert($sql);
        }

    } catch (Exception $e) {
        $oCon->QueryT('ROLLBACK');
        $oReturn->alert($e->getMessage());
    }
    return $oReturn;
}

function eliminar_otros_detalles($aForm = '', $cod_sact = ""){
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
    $idempresa = $_SESSION['U_EMPRESA'];
    $idsucursal = $_SESSION['U_SUCURSAL'];
    $array = ($_SESSION['ARRAY_PINTA']);

    //variables formulario
    $codigoActivo 	 =  $aForm['act_cod_act'];
    //$oReturn->alert($continente);
    try {
        $oIfx->QueryT('BEGIN');
        if (($codigoActivo != '') && ($cod_sact != '')){


            $sql="delete from saesac
                 where sact_cod_sact = '$cod_sact' 
			     and act_cod_empr  = '$idempresa' 
			     and act_cod_act   = '$codigoActivo' 
			     and act_cod_sucu  = '$idsucursal'";
            //echo $sql;exit;
            $oIfx->QueryT($sql);
        }


        $oReturn->script("recargar_otros();");
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
