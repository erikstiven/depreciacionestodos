<? /* * ***************************************************************** */ ?>
<? /* NO MODIFICAR ESTA SECCION */ ?>
<? include_once('../_Modulo.inc.php'); ?>
<? include_once(HEADER_MODULO); ?>
<? if ($ejecuta) { ?>
    <? /*     * ***************************************************************** */ ?>
    	
    <!--CSS--> 
	<link rel="stylesheet" type="text/css" href="<?=$_COOKIE["JIREH_INCLUDE"]?>css/bootstrap-3.3.7-dist/css/bootstrap.css" media="screen">
	<link rel="stylesheet" type="text/css" href="<?=$_COOKIE["JIREH_INCLUDE"]?>css/bootstrap-3.3.7-dist/css/bootstrap.min.css" media="screen">
	<link rel="stylesheet" type="text/css" href="<?=$_COOKIE["JIREH_INCLUDE"]?>js/treeview/css/bootstrap-treeview.css" media="screen"> 

    <!--Javascript--> 
    
    <script src="<?=$_COOKIE["JIREH_INCLUDE"]?>js/dataTables/jquery.dataTables.min.js"></script>
    <script src="<?=$_COOKIE["JIREH_INCLUDE"]?>js/dataTables/dataTables.bootstrap.min.js"></script>          
    <script src="<?=$_COOKIE["JIREH_INCLUDE"]?>js/dataTables/bootstrap.js"></script>
    <script type="text/javascript" language="JavaScript" src="<?=$_COOKIE["JIREH_INCLUDE"]?>js/treeview/js/bootstrap-treeview.js"></script>
    <script type="text/javascript" language="javascript" src="<?=$_COOKIE["JIREH_INCLUDE"]?>css/bootstrap-3.3.7-dist/js/bootstrap.min.js"></script>
	<script src="js/saeact.js"></script>   

    <script>
        function nuevoFormulario() {
            $("#form1")[0].reset();
			location.reload();
        }
        function init(table) {
                var table = $('#'+table).DataTable({
                scrollY:        '50vh',
                scrollX: true,
        scrollCollapse: true,
        paging:         true,
                dom: 'Bfrtip',
                processing: "<i class='fa fa-spinner fa-spin' style='font-size:24px; color: #34495e;'></i>",
                "language": {
                    "search": "<i class='fa fa-search'></i>",
                    "searchPlaceholder": "Buscar",
                    'paginate': {
                        'previous': 'Anterior',
                        'next': 'Siguiente'
                    },
                    "zeroRecords": "No se encontro datos",
                    "info": "Mostrando _START_ a _END_ de  _TOTAL_ Total",
                    "infoEmpty": "",
                    "infoFiltered": "(Mostrando _MAX_ Registros Totales)",
                },
                "paging": false,
                "ordering": true,
                "info": true,
            });
            table.buttons().remove();

        }



        function genera_cabecera_formulario() {
            xajax_genera_cabecera_formulario('nuevo', xajax.getFormValues("form1"));			
            // MOSTRAR PERTANIAS
            document.getElementById("cuentas").style.display='none';
            document.getElementById("reponsable").style.display='none';
			document.getElementById("cxa_codigo").disabled=true;
			document.getElementById("mantenimiento").style.display='none';
            document.getElementById("otros").style.display='none';
            document.getElementById("imagen").style.display='none';          
            document.getElementById("partes").style.display='none';          
        }
  
        function consultar() {
            empresa = document.getElementById('empresa').value;
            if (empresa != '') {
                xajax_consultar(xajax.getFormValues("form1"));
            } else {
				document.getElementById("form1").reset();
            }
        }    
        function guardar(){
            if(ProcesarFormulario() == true){
                xajax_guardar(xajax.getFormValues("form1"));
            }
        }

        function eliminar(){
            if(ProcesarFormulario() == true){
                xajax_eliminar(xajax.getFormValues("form1"));
            }
        }
		function nuevoDetalle(){
			 // RESET CTAS DE GASTO
            document.getElementById("gasd_cod_cuen").value='';
            document.getElementById("gasd_cod_ccos").value='';
            document.getElementById("gasd_val_porc").value='';
            document.getElementById("gasd_rev_sn").value='';
			
		}
		function nuevoResponsable(){
	        // RESET RESPONSABLES			
            document.getElementById("cxa_codigo").value='0';
            document.getElementById("cxa_codigo").disabled=true;			
			document.getElementById("cod_empleado").value='';
            document.getElementById("nom_empleado").value='';
            document.getElementById("cargo_empleado").value='';
            document.getElementById("ubicacion_empleado").value='';
            document.getElementById("fecha").value='';
            document.getElementById("observacion").value='';
		}
		function nuevoAseguradoras(){
            // RESET OTROS DETALLES - ASEGURADORAS
            document.getElementById("sact_poli_sact").value='';
            document.getElementById("sact_fech_sact").value='';
            document.getElementById("sact_fven_sact").value='';
            document.getElementById("sact_val_sact").value='';
            document.getElementById("sact_dedu_sact").value='';
            document.getElementById("sact_obs_sact").value='';
            document.getElementById("sac_num_matr").value='';
            document.getElementById("sac_num_motr").value='';
            document.getElementById("sac_num_chsis").value='';
            document.getElementById("sac_num_plac").value='';
            document.getElementById("sac_val_come").value='';
            document.getElementById("sac_cod_aseg").value='';
		}	
		function nuevoMantenimiento(){
			// RESET MANTENIMINETO
		    document.getElementById("mant_tip_movi").value='';
            //document.getElementById("sec_docu").value='';
            document.getElementById("mant_caus_mant").value='';
            document.getElementById("mant_ref_mant").value='';
            document.getElementById("mant_tall_mant").value='';
            document.getElementById("mant_fini_mant").value='';
            document.getElementById("mant_fent_mant").value='';
            document.getElementById("mant_cost_mant").value='';
            document.getElementById("mant_resp_mant").value='';
            document.getElementById("mant_obs_mant").value='';
		}
		function nuevoPartes(){
			document.getElementById("part_cod_part").value = '0';
			document.getElementById("estado").value = '';
			document.getElementById("part_nom_part").value = '';
			document.getElementById("part_marc_part").value = '';
			document.getElementById("part_modl_part").value = '';
			document.getElementById("part_seri_part").value = '';
			document.getElementById("part_colr_pat").value = '';
			document.getElementById("part_cant_part").value = '';
			document.getElementById("part_obs_part").value = '';			
			
		}
		
		function seleccionaItem(codigoActivo, clave, nombreActivo, subgrupo){
			// alert(codigoActivo); 
			// alert(clave); 
			// alert(nombreActivo); 
			// alert(subgrupo); 
			
			document.getElementById("act_cod_act").value = codigoActivo;
            document.getElementById("act_clave_act").value = clave;
           	document.getElementById("act_nom_act").value = nombreActivo;
           	document.getElementById("descripcion").innerHTML = clave + ' ' + nombreActivo;
						
			xajax_f_cargar_datos(xajax.getFormValues("form1"));
			xajax_lista_reporte_index(codigoActivo);
			xajax_cargarDatosCuentas(xajax.getFormValues("form1"));
            xajax_cargarDatosResponsables(xajax.getFormValues("form1"));
            xajax_cargarDatosMantenimiento(xajax.getFormValues("form1"));
            xajax_cargarDatosOtros(xajax.getFormValues("form1"));
            xajax_cargarDatosPartes(xajax.getFormValues("form1"));
			
            // MOSTRAR PESTAÑAS
            document.getElementById("cuentas").style.display='';
            document.getElementById("reponsable").style.display='';
            document.getElementById("mantenimiento").style.display='';
            document.getElementById("otros").style.display='';
            document.getElementById("imagen").style.display='';
            document.getElementById("partes").style.display='';
			
            // RESET RESPONSABLES
            document.getElementById("cxa_codigo").value='0';
			document.getElementById("cxa_codigo").disabled=true;
            document.getElementById("cod_empleado").value='';
            document.getElementById("nom_empleado").value='';
            document.getElementById("cargo_empleado").value='';
            document.getElementById("ubicacion_empleado").value='';
            document.getElementById("fecha").value='';
            document.getElementById("observacion").value='';
            // RESET CTAS DE GASTO
            document.getElementById("gasd_cod_cuen").value='';
            document.getElementById("gasd_cod_ccos").value='';
            document.getElementById("gasd_val_porc").value='';
			// RESET PARTES
			document.getElementById("gasd_cod_cuen").value='';
			document.getElementById("gasd_cod_cuen").value='';
			document.getElementById("gasd_cod_cuen").value='';
			document.getElementById("gasd_cod_cuen").value='';
			document.getElementById("gasd_cod_cuen").value='';
			document.getElementById("gasd_cod_cuen").value='';
			document.getElementById("gasd_cod_cuen").value='';
			
			
			// RESET MANTENIMINETO
		    document.getElementById("mant_tip_movi").value='';
            document.getElementById("sec_docu").value='';
            document.getElementById("mant_caus_mant").value='';
            document.getElementById("mant_ref_mant").value='';
            document.getElementById("mant_tall_mant").value='';
            document.getElementById("mant_fini_mant").value='';
            document.getElementById("mant_fent_mant").value='';
            document.getElementById("mant_cost_mant").value='';
            document.getElementById("mant_resp_mant").value='';
            document.getElementById("mant_obs_mant").value='';
            // RESET OTROS DETALLES - ASEGURADORAS
            document.getElementById("sact_poli_sact").value='';
            document.getElementById("sact_fech_sact").value='';
            document.getElementById("sact_fven_sact").value='';
            document.getElementById("sact_val_sact").value='';
            document.getElementById("sact_dedu_sact").value='';
            document.getElementById("sact_obs_sact").value='';
            document.getElementById("sac_num_matr").value='';
            document.getElementById("sac_num_motr").value='';
            document.getElementById("sac_num_chsis").value='';
            document.getElementById("sac_num_plac").value='';
            document.getElementById("sac_val_come").value='';
            document.getElementById("sac_cod_aseg").value='';			
        }
		
 	function vista_previa(){
		//alert(codigo);
		var sel = document.getElementById("act_cod_act").value;
		var opciones="toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=no, width=730, height=380, top=255, left=130";

		var pagina = '../activos_ficha/vista_previa.php?sesionId=<?=session_id()?>&codigo='+sel;
		//alert(sel);
		window.open(pagina,"",opciones);	
	}	
	function imprime_etiqueta(){
		//alert(codigo);
		var sel = document.getElementById("act_clave_act").value;
		var opciones="toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=no, width=730, height=380, top=255, left=130";

		var pagina = '../activos_ficha/imprime_etiqueta.php?sesionId=<?=session_id()?>&codigo='+sel;
		//alert(sel);
		window.open(pagina,"",opciones);
		
	}	

		
        function f_filtro_subgrupo(data){
            xajax_f_filtro_subgrupo(xajax.getFormValues("form1"), data);           
        }
		function lista_reporte_index(codigoActivo){
			    xajax_lista_reporte_index(codigoActivo);          
		}
		function recarga(){
			var table = $('#example').DataTable(); 
			table.destroy();
			recarga_lista();			
		}
        function f_arma_codigo() {
		    xajax_f_arma_codigo(xajax.getFormValues("form1"));
        }
        function f_tipoDepreciacion() {
		    xajax_f_tipoDepreciacion(xajax.getFormValues("form1"));
        }
        function eliminar_lista_subgrupo() {
            var sel = document.getElementById("sgac_cod_sgac");
            for (var i = (sel.length - 1); i >= 1; i--) {
                aBorrar = sel.options[i];
                aBorrar.parentNode.removeChild(aBorrar);
            }
        }
        
        function anadir_elemento_subgrupo(x, i, elemento) {
            var lista = document.form1.sgac_cod_sgac;
            var option = new Option(elemento, i);
            lista.options[x] = option;
            document.form1.sgac_cod_sgac.value = i;
        }
        function buscar_cuentas(id){
            $("#myModal").modal("show");
            var table = $('#table_cuentas').DataTable();
            table.destroy();
            listar_cuentas_contables(id);
        }

        function bajar_cuentas(cuenta,id) {
			document.getElementById(id).value = cuenta;
            $("#myModal").modal("hide");
			//VALIDAR SI CTA ES DE MOVIMIENTO
			xajax_validar_cuentas(xajax.getFormValues("form1"), cuenta);
		}	
		
        function buscar_ccostos(id){
            $("#myModal1").modal("show");
            var table = $('#table_ccostos').DataTable();
            table.destroy();
            listar_centro_costos(id);
        }
        function bajar_ccostos(cuenta,id) {
            document.getElementById(id).value = cuenta;
            $("#myModal1").modal("hide");
			//VALIDAR SI CCOSTOS ES DE MOVIMIENTO
			xajax_validar_ccostos(xajax.getFormValues("form1"), cuenta);			
		}	
	    function buscar_empleados(){
            $("#myModalEmpleados").modal("show");
            var id=document.getElementById("cod_empleado").value
            xajax_form_empleados(id, xajax.getFormValues("form1")); 
            //listar_empleados();
        }	
	    function bajar_empleados(cuenta, id, cargo, nombre, codigoCargo) {
            document.getElementById("cod_empleado").value = cuenta;
            document.getElementById("cargo_empleado").value = cargo;
            document.getElementById("nom_empleado").value = nombre;
            //document.getElementById("codigo_cargo").value = codigoCargo;
            $("#myModalEmpleados").modal("hide");
        }
			
        function grabarDetalle() {          
                var cuenta = document.getElementById("gasd_cod_cuen").value;
                var ccostos = document.getElementById("gasd_cod_ccos").value;
                var valor = document.getElementById("gasd_val_porc").value;
                if (cuenta != '') {
                    if (ccostos != '') {
                        if ((valor != '') || (valor > 0)) {							
                            xajax_grabarDetalle(xajax.getFormValues("form1"));
                        } else {
                            alert('Ingres Valor del Porcentaje');
                        }
                    } else {
                        alert('Seleccione Centro de Costos');
                    }
                }
                else {
                    alert('Seleccione Cuenta Contable');
                }
           
        }
        function guardarResponsables() {
            xajax_guardarResponsables(xajax.getFormValues("form1"));
        }
        function foco(idElemento) {
            document.getElementById(idElemento).focus();
        }

        function recarga_cta_gasto(){
            xajax_cargarDatosCuentas(xajax.getFormValues("form1"));
        }

		
        function grabarPartes(){
            xajax_grabarDetallePartes(xajax.getFormValues("form1"));
        }
        
		
        function editar_cta_gast(cuenta, ccos, valor, tipo){

            document.getElementById("gasd_cod_cuen").value=cuenta;
            document.getElementById("gasd_cod_ccos").value=ccos;
            document.getElementById("gasd_val_porc").value=valor;
            document.getElementById("gasd_rev_sn").value=tipo;
        }

        function eliminar_otros(cod_sact){
            xajax_eliminar_otros_detalles(xajax.getFormValues("form1"), cod_sact);
        }

        function editar_otros(cod_sact, fecha_emis, fecha_venc){
            //var fechaEmisN = fecha_emis.split('/');
            //var fecha_emis = fechaEmisN[2]+ '/' + fechaEmisN[0] + '/' +  fechaEmisN[1];
            //var fechaVencN = fecha_venc.split('/');
            //var fecha_venc = fechaVencN[2]+ '/' + fechaVencN[0] + '/' +  fechaVencN[1];

            document.getElementById("sact_cod_sact").value=cod_sact;
            document.getElementById("sact_fech_sact").value=fecha_emis;
            document.getElementById("sact_fven_sact").value=fecha_venc;
            xajax_editar_otros_detalles(xajax.getFormValues("form1"), cod_sact);
        }

        function eliminar_cta_gast(cuenta, ccos, valor){
            xajax_eliminar_cta_gast(xajax.getFormValues("form1"),cuenta, ccos, valor);
        }
        function recarga_responsables(){
            xajax_cargarDatosResponsables(xajax.getFormValues("form1"));
        }
        function editar_responsables(codEmpleado, nomEmpleado, codCargo, ubicacion, fecha, observacion, id){
            //var fechaNueva = fecha.split('/');
            //var fecha = fechaNueva[2]+ '/' + fechaNueva[0] + '/' +  fechaNueva[1];

            document.getElementById("cod_empleado").value=codEmpleado;
            document.getElementById("nom_empleado").value=nomEmpleado;
            document.getElementById("cargo_empleado").value=codCargo;
            document.getElementById("ubicacion_empleado").value=ubicacion;
            document.getElementById("fecha").value=fecha;
            document.getElementById("observacion").value=observacion;
            document.getElementById("cxa_codigo").value=id;

        }
        function editarPartes(part_cod_part, eact_cod_eact, part_nom_part, part_marc_part, part_modl_part, part_seri_part, part_colr_pat, part_cant_part, part_obs_part){
            //alert(eact_cod_eact);
			document.getElementById("part_cod_part").value  = part_cod_part;
            document.getElementById("estado").value  = eact_cod_eact;
            document.getElementById("part_nom_part").value  = part_nom_part;
            document.getElementById("part_marc_part").value = part_marc_part;
            document.getElementById("part_modl_part").value = part_modl_part;
            document.getElementById("part_seri_part").value = part_seri_part;
            document.getElementById("part_colr_pat").value  = part_colr_pat;
            document.getElementById("part_cant_part").value = part_cant_part;
            document.getElementById("part_obs_part").value  = part_obs_part;

        }
		
		function eliminarPartes(codigoParte, codigoActivo){
			xajax_eliminarDetallesPartes(xajax.getFormValues("form1"), codigoParte, codigoActivo);
		}

        function eliminar_responsables(codCxa, codEmpleado, codCargo){
            xajax_eliminar_responsables(xajax.getFormValues("form1"),codCxa, codEmpleado, codCargo );
        }

        function consultarEvent(event) {

            if (event.keyCode == 115 || event.keyCode == 13) { // F4 - ENTER
                $("#myModalEmpleados").modal("show");

                var id=document.getElementById("cod_empleado").value
            xajax_form_empleados(id, xajax.getFormValues("form1")); 
                //var table = $('#table_empleados').DataTable();
                //table.destroy();
                //listar_empleados();
            }
        }
        //// mantenimiento
        function guardarMantenimiento(){
            var movimiento = document.getElementById("mant_tip_movi").value;
            var tipo_mov =   document.getElementById("tipo_mov").value;
            if(movimiento!=''){
                
                    xajax_guardarMantenimiento(xajax.getFormValues("form1"));
                
            }
            else{
                alert("Selecciones el tipo de movimiento")
            }
        }
        function recarga_Mantenimiento(){
            xajax_cargarDatosMantenimiento(xajax.getFormValues("form1"));
        }
        function recargaPartes(){
            xajax_cargarDatosPartes(xajax.getFormValues("form1"));
        }       
        function editar_mantenimineto(mant_tip_movi,mant_sec_docu ,mant_caus_mant ,mant_ref_mant,mant_tall_mant,mant_fini_mant ,mant_fent_mant ,mant_cost_mant ,mant_resp_mant,mant_obs_mant){
            //var fini_mant = mant_fini_mant.split('/');
            //var fec_fini_mant = fini_mant[2]+ '/' + fini_mant[0] + '/' +  fini_mant[1];
            //var fent_mant = mant_fent_mant.split('/');
            //var fec_fent_mant = fent_mant[2]+ '/' + fent_mant[0] + '/' +  fent_mant[1];
            document.getElementById("mant_tip_movi").value=mant_tip_movi;
            document.getElementById("sec_docu").value=mant_sec_docu;
            document.getElementById("mant_caus_mant").value=mant_caus_mant;
            document.getElementById("mant_ref_mant").value=mant_ref_mant;
            document.getElementById("mant_tall_mant").value=mant_tall_mant;
            document.getElementById("mant_fini_mant").value=mant_fini_mant;
            document.getElementById("mant_fent_mant").value=mant_fent_mant;
            document.getElementById("mant_cost_mant").value=mant_cost_mant;
            document.getElementById("mant_resp_mant").value=mant_resp_mant;
            document.getElementById("mant_obs_mant").value=mant_obs_mant;
        }
        function eliminar_mantenimineto(mant_tip_movi,mant_sec_docu ){
            xajax_eliminar_mantenimineto(xajax.getFormValues("form1"),mant_tip_movi,mant_sec_docu);
        }

        function guardarOtros(){
            xajax_guardarOtros(xajax.getFormValues("form1"));
        }
        function recargar_otros() {
            xajax_cargarDatosOtros(xajax.getFormValues("form1"));
        }
		
		function copiaFecha(val){
			if(val.checked == true){
				var fecha = $("#act_fcmp_act").val();
				if(fecha != ''){
					$("#act_fdep_act").val(fecha);
				}
			}else{
				$("#act_fdep_act").val('');
			}
		}
		
		function copiaFecha_(depreciacionFechaCompra){
			var fecha = $("#act_fcmp_act").val();
			var dia; 
			var fechaInicio;
			//alert(depreciacionFechaCompra);
			if (depreciacionFechaCompra == 'N'){
				if(fecha != ''){
					dia = fecha.substring(8, 10);
					mes = fecha.substring(5, 7); 
					anio = fecha.substring(0, 4);				
					if(dia > 15){
						if (mes == 12){ 
							mes_ini = '01'; 
							anio ++;
						} else {
							mes ++;
							if (mes < 10){
								mes_ini = '0' + mes;
							}else {					
								mes_ini = mes;
							}
						}		
					} else {
						mes_ini = mes;					
					}	
					
					fechaTexto = anio + '-' + mes_ini + "-" + '01';
					//alert(fechaTexto);
					$("#act_fdep_act").val(fechaTexto);					
				}		
			} else {
				$("#act_fdep_act").val(fecha);
			}	
		}
		
		function copiaFechaCompra(){
			xajax_copiaFechaCompra(xajax.getFormValues("form1"));
		}	
		function autocompletar( event, id) {
          
            if (event.keyCode == 13 || event.keyCode == 115) { // F4                 
                $("#myModal").modal("show");
				var table = $('#table_cuentas').DataTable();
				table.destroy();
				listar_cuentas_contables(id);
            }

        }
		function buca_ccostos( event, id) {          
            if (event.keyCode == 13 || event.keyCode == 115) { // F4   
	           $("#myModal1").modal("show");
				var table = $('#table_ccostos').DataTable();
				table.destroy();
				listar_centro_costos(id);
			}
		}
		
		function buca_empleado( event, id) {          
            if (event.keyCode == 13 || event.keyCode == 115) { // F4   
				$("#myModalEmpleados").modal("show");
                var id=document.getElementById("cod_empleado").value
            xajax_form_empleados(id, xajax.getFormValues("form1")); 
				//var table = $('#table_empleados').DataTable();
				//table.destroy();
				//listar_empleados();
			}
		}
		
    </script>
    <!--DIBUJA FORMULARIO FILTRO-->
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <body>
        <form id="form1" name="form1" action="javascript:void(null);">
			<div class="col-md-5">
				<table id="example" class="table table-striped table-bordered table-hover table-condensed"  style="width: 100%;" align="center">
						<thead>
							<tr>
								<td colspan="6" class="bg-primary">LISTA DE ACTIVOS FIJOS</td>
							</tr>
							<tr class="info">
                                <td>Sucursal</td>
								<td>Clave</td>
                                <td>Grupo</td>
                                <td>Subgrupo</td>
								<td>Descripcion</td>
								<td>Seleccionar</td>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>        
			</div> 		
			<div class="col-md-7">				
				<!-- Nav tabs -->				
				<ul class="nav nav-pills" role="tablist">
					<li role="presentation" class="active"><a href="#divFormularioActivoFijo" aria-controls="divFormularioActivoFijo" role="tab" data-toggle="tab">DATOS GENERALES</a></li>
					<li role="presentation" id = "cuentas" style="display: none"><a href="#divFormularioCuentas" aria-controls="divFormularioCuentas" role="tab" data-toggle="tab">CUENTAS DE GASTOS</a></li>
					<li role="presentation" id = "reponsable" style="display: none"><a href="#divFormularioResponsable" aria-controls="divFormularioResponsable" role="tab" data-toggle="tab">DETALLES DE RESPONSABLES</a></li>
					<li role="presentation" id = "partes" style="display: none"><a href="#divPartes" aria-controls="divPartes" role="tab" data-toggle="tab"> PARTES</a></li>
					<li role="presentation" id = "mantenimiento" style="display: none"><a href="#divMantenimiento" aria-controls="divMantenimiento" role="tab" data-toggle="tab">MANTENIMIENTO</a></li>
					<li role="presentation" id = "otros" style="display: none"><a href="#divFormularioOtros" aria-controls="divFormularioOtros" role="tab" data-toggle="tab">ASEGURADORAS</a></li>
					<li role="presentation" id = "imagen" style="display: none"><a href="#divImagen" aria-controls="divImagen" role="tab" data-toggle="tab"> IMAGEN</a></li>                        
				</ul>
				<div role="tabpanel" class="tab-pane active" id="divFormularioBotones"></div>					
				<div  class="col-md-4" id="botones" style=" float:left;"></div>
				 <!-- Tab panes -->
				<div class="tab-content">
					<div role="tabpanel" class="tab-pane active" id="divFormularioActivoFijo"></div>						
					<div role="tabpanel" class="tab-pane" id="divFormularioCuentas"></div>
					<div role="tabpanel" class="tab-pane" id="divFormularioResponsable"></div>
					<div role="tabpanel" class="tab-pane" id="divPartes"></div>
					<div role="tabpanel" class="tab-pane" id="divMantenimiento"></div>
					<div role="tabpanel" class="tab-pane" id="divFormularioOtros"></div>
					<div role="tabpanel" class="tab-pane" id="divImagen"></div>                        
				</div>				
			</div>
            <div style="width: 100%;">
					<div class="modal fade" id="myModalEmpleados"  tabindex="-1" role="dialog" aria-labelledby="myModalLabel"  aria-hidden="true"></div>
			</div>
			<div id="cuentas_contables">
				<div id="myModal" class="modal fade" role="dialog">
                    <div class="modal-dialog">
                        <!-- Modal content-->
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title">LISTA CUENTAS CONTABLES</h4>
                            </div>
                            <div class="modal-body">
                                <table id="table_cuentas" class="table table-striped table-bordered table-hover table-condensed"  style="width: 100%;" align="center">
                                    <thead>
                                    <tr>
                                        <td colspan="5" class="bg-primary">LISTA CUENTAS CONTABLES</td>
                                    </tr>
                                    <tr class="info">
                                        <td>Codigo</td>
                                        <td>Descripcion</td>
                                        <td>Seleccionar</td>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-danger" data-dismiss="modal">CERRAR</button>
                            </div>
                        </div>
                    </div>
				</div>
            </div>
			<div id="centro_costos"> 
				<div id="myModal1" class="modal fade" role="dialog">
                    <div class="modal-dialog">
                        <!-- Modal content-->
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title">LISTA CENTROS DE COSTOS</h4>
                            </div>
                            <div class="modal-body">
                                <table id="table_ccostos" class="table table-striped table-bordered table-hover table-condensed"  style="width: 100%;" align="center">
                                    <thead>
                                    <tr>
                                        <td colspan="5" class="bg-primary">LISTA CENTROS DE COSTOS</td>
                                    </tr>
                                    <tr class="info">
                                        <td>Codigo</td>
                                        <td>Descripcion</td>
                                        <td>Seleccionar</td>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-danger" data-dismiss="modal">CERRAR</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


			
			<div id = "codigo_barras">	
			</div>	
        </form>
    </body>
    <script>genera_cabecera_formulario();/*genera_detalle();genera_form_detalle();*/</script>
    <? /*     * ***************************************************************** */ ?>
    <? /* NO MODIFICAR ESTA SECCION */ ?>
<? } ?>
<? include_once(FOOTER_MODULO); ?>
<? /* * ***************************************************************** */ ?>
