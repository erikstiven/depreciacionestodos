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
	
    <script>

        function genera_cabecera_formulario() {
            xajax_genera_cabecera_formulario('nuevo', xajax.getFormValues("form1"));
        }

 
        function genera_cabecera_filtro() {
            xajax_genera_cabecera_formulario('filtro', xajax.getFormValues("form1"));
        }		
        function generar(){
            if(ProcesarFormulario() == true){
                xajax_generar(xajax.getFormValues("form1"));
            }
        }
		function f_calcula_vida_util(){
            xajax_f_calcula_vida_util(xajax.getFormValues("form1"));           
        }
		
        function f_filtro_mes(data){
            xajax_f_filtro_mes(xajax.getFormValues("form1"), data);           
        }
   
        function eliminar_lista_mes() {
            var sel = document.getElementById("mes");
            for (var i = (sel.length - 1); i >= 1; i--) {
                aBorrar = sel.options[i];
                aBorrar.parentNode.removeChild(aBorrar);
            }
        }
        
        function anadir_elemento_mes(x, i, elemento) {
            var lista = document.form1.mes;
            var option = new Option(elemento, i);
            lista.options[x] = option;
            document.form1.mes.value = i;
        }
        function f_filtro_mes_fin(data){
            xajax_f_filtro_mes_fin(xajax.getFormValues("form1"), data);           
        }
   
        function eliminar_lista_mes_fin() {
            var sel = document.getElementById("mes_fin");
            for (var i = (sel.length - 1); i >= 1; i--) {
                aBorrar = sel.options[i];
                aBorrar.parentNode.removeChild(aBorrar);
            }
        }
        
        function anadir_elemento_mes_fin(x, i, elemento) {
            var lista = document.form1.mes_fin;
            var option = new Option(elemento, i);
            lista.options[x] = option;
            document.form1.mes_fin.value = i;
        }
		
		function f_filtro_subgrupo(data){
            xajax_f_filtro_subgrupo(xajax.getFormValues("form1"), data);           
        }
   
		function eliminar_lista_subgrupo() {
            var sel = document.getElementById("cod_subgrupo");
            for (var i = (sel.length - 1); i >= 1; i--) {
                aBorrar = sel.options[i];
                aBorrar.parentNode.removeChild(aBorrar);
            }
        }
        
        function anadir_elemento_subgrupo(x, i, elemento) {
            var lista = document.form1.cod_subgrupo;
            var option = new Option(elemento, i);
            lista.options[x] = option;
            document.form1.cod_subgrupo.value = i;
        }
		function f_filtro_activos(data){
            xajax_f_filtro_activos(xajax.getFormValues("form1"), data);           
        }
   
		function eliminar_lista_activo() {
            var sel = document.getElementById("cod_activo");
            for (var i = (sel.length - 1); i >= 1; i--) {
                aBorrar = sel.options[i];
                aBorrar.parentNode.removeChild(aBorrar);
            }
        }
        
        function anadir_elemento_activo(x, i, elemento) {
            var lista = document.form1.cod_activo;
            var option = new Option(elemento, i);
            lista.options[x] = option;
            document.form1.cod_activo.value = i;
        }
		function f_filtro_activos1(data){
            xajax_f_filtro_activos1(xajax.getFormValues("form1"), data);           
        }
   
		function eliminar_lista_activo1() {
            var sel = document.getElementById("cod_activo_hasta");
            for (var i = (sel.length - 1); i >= 1; i--) {
                aBorrar = sel.options[i];
                aBorrar.parentNode.removeChild(aBorrar);
            }
        }
        
        function anadir_elemento_activo1(x, i, elemento) {
            var lista = document.form1.cod_activo_hasta;
            var option = new Option(elemento, i);
            lista.options[x] = option;
            document.form1.cod_activo_hasta.value = i;
        }
	// FUNCION PARA EXPORTAR DATA A EXCEL	
	function f_exportar(){
		document.location = "excel.php";
	}
	
	function f_informacion_activo(){
	  xajax_f_informacion_activo(xajax.getFormValues("form1")); 
	}
    </script>
    <!--DIBUJA FORMULARIO FILTRO-->
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <body>
        <form id="form1" name="form1" action="javascript:void(null);">
			<div class="col-md-12"> 
				<table align="center" border="0" cellpadding="2" cellspacing="0" width="100%">
					<tr>
						<td align="center">
							<div id="divFormularioReportesGrupos"></div>
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