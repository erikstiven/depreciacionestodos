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
	<script src="js/lenguajeusuario_.js"></script>   
    <script>

        function genera_cabecera_formulario() {
            xajax_genera_cabecera_formulario('nuevo', xajax.getFormValues("form1"));
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
		
		function seleccionaItem(codigoEstado, descripcionEstado, metodo, calculo, tipo){	
			//alert(calculo);
			if (tipo == "S") {								
				document.getElementById("tipo").checked = 1;				
			} else {
				document.getElementById("tipo").checked = 0;
			}
			document.getElementById("codigo").value = codigoEstado;
			document.getElementById("descripcion").value = descripcionEstado;
			document.getElementById("metodo").value = metodo;
			document.getElementById("tiempo").value = calculo;
		}
		function recarga(){
			var table = $('#example').DataTable(); 
			table.destroy();
			recarga_lista();			
		}

    </script>
    <!--DIBUJA FORMULARIO FILTRO-->
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <body>
        <form id="form1" name="form1" action="javascript:void(null);">
			<div class="col-md-4"> 
				<table id="example" class="table table-striped table-bordered table-hover table-condensed"  style="width: 100%;" align="center">
						<thead>
							<tr>
								<td colspan="5" class="bg-primary">LISTA TIPO DE DEPRECIACIONES</td>
							</tr>
							<tr class="info">
								<td>C&oacutedigo</td>
								<td>Nombre</td>
								<td>Seleccionar</td>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>        
			</div> 		
			<div class="col-md-8"> 
				<table align="center" border="0" cellpadding="2" cellspacing="0" width="100%">
					<tr>
						<td align="center">
							<div id="divFormularioTipoDepr"></div>
						</td>
					</tr>
				</table>
			</div> 	
        </form>
    </body>
    <script>genera_cabecera_formulario();/*genera_detalle();genera_form_detalle();*/</script>
    <? /*     * ***************************************************************** */ ?>
    <? /* NO MODIFICAR ESTA SECCION */ ?>
<? } ?>
<? include_once(FOOTER_MODULO); ?>
<? /* * ***************************************************************** */ ?>