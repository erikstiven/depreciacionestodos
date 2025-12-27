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
	
    <!-- Select2 -->
    <link rel="stylesheet" href="<?=$_COOKIE["JIREH_COMPONENTES"]?>bower_components/select2/dist/css/select2.min.css">
    
    <!-- Theme style -->
    <link rel="stylesheet" href="<?=$_COOKIE["JIREH_COMPONENTES"]?>dist/css/AdminLTE.min.css">
    <!--Javascript--> 
    
  
    <script src="<?=$_COOKIE["JIREH_INCLUDE"]?>js/dataTables/jquery.dataTables.min.js"></script>
    <script src="<?=$_COOKIE["JIREH_INCLUDE"]?>js/dataTables/dataTables.bootstrap.min.js"></script>          
    <script src="<?=$_COOKIE["JIREH_INCLUDE"]?>js/dataTables/bootstrap.js"></script>
	<script type="text/javascript" language="JavaScript" src="<?=$_COOKIE["JIREH_INCLUDE"]?>js/treeview/js/bootstrap-treeview.js"></script>
    <script type="text/javascript" language="javascript" src="<?=$_COOKIE["JIREH_INCLUDE"]?>css/bootstrap-3.3.7-dist/js/bootstrap.min.js"></script>
	
    <!-- Select2 -->
    <script src="<?=$_COOKIE["JIREH_COMPONENTES"]?>bower_components/select2/dist/js/select2.full.min.js"></script>
    
    <script>
        function generaSelect2(){
            $('.select2').select2({
                width: '100%',
                allowClear: true,
                placeholder: function () {
                    return $(this).data('placeholder') || 'Seleccione una opción';
                }
            });
        }
        function genera_cabecera_formulario() {
            xajax_genera_cabecera_formulario('nuevo', xajax.getFormValues("form1"));
        }

        function consultarPlan(){
            if (document.getElementById("btnConsultarPlan").getAttribute("data-disabled") === "true") {
                return;
            }
            if(ProcesarFormulario() == true){
                listarPlan();
                validarPlan();
            }
        }

        function listarPlan(){
            xajax_listarPlan(xajax.getFormValues("form1"));
        }

        function validarPlan(){
            xajax_validarPlan(xajax.getFormValues("form1"));
        }

        function prorrogarPlan(){
            var check = document.getElementById("aplicar_prorroga");
            if (!check || !check.checked) {
                alert("Debe activar la prórroga para continuar.");
                return;
            }
            var inputMeses = document.getElementById("meses_prorroga");
            var meses = parseInt(inputMeses.value, 10);
            if (isNaN(meses) || meses <= 0) {
                alert("Debe ingresar un número de meses válido.");
                return;
            }
            document.getElementById("meses_prorroga").value = meses;
            xajax_prorrogarPlan(xajax.getFormValues("form1"));
        }
		
        function f_filtro_sucursal(data){
            xajax_f_filtro_sucursal(xajax.getFormValues("form1"), data);
        }
   
        function eliminar_lista_sucursal() {
            var sel = document.getElementById("sucursal");
            for (var i = (sel.length - 1); i >= 1; i--) {
                aBorrar = sel.options[i];
                aBorrar.parentNode.removeChild(aBorrar);
            }
        }
        
        function anadir_elemento_sucursal(x, i, elemento) {
            var lista = document.form1.sucursal;
            var option = new Option(elemento, i);
            lista.options[x] = option;
            document.form1.sucursal.value = i;
        }

		function f_filtro_grupo(data){
            xajax_f_filtro_grupo(xajax.getFormValues("form1"), data);
        }
   
		function eliminar_lista_grupo() {
            var sel = document.getElementById("cod_grupo");
            for (var i = (sel.length - 1); i >= 1; i--) {
                aBorrar = sel.options[i];
                aBorrar.parentNode.removeChild(aBorrar);
            }
        }
        
        function anadir_elemento_grupo(x, i, elemento) {
            var lista = document.form1.cod_grupo;
            var option = new Option(elemento, i);
            lista.options[x] = option;
            document.form1.cod_grupo.value = i;
        }
		
		function f_filtro_subgrupo(){         
            xajax_f_filtro_subgrupo(xajax.getFormValues("form1"));
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
            if(x == '1'){
                var option = new Option(elemento, i, true,true);
            }else{
                var option = new Option(elemento, i);
            }
            lista.options[x] = option;
        }
		function f_filtro_activos_desde(){
            xajax_f_filtro_activos_desde(xajax.getFormValues("form1"));           
        }
   
		function eliminar_lista_activo_desde() {
            var sel = document.getElementById("cod_activo_desde");
            for (var i = (sel.length - 1); i >= 1; i--) {
                aBorrar = sel.options[i];
                aBorrar.parentNode.removeChild(aBorrar);
            }
        }
        
        function anadir_elemento_activo_desde(x, i, elemento) {
            var lista = document.form1.cod_activo_desde;
            if(x == '1'){
                var option = new Option(elemento, i, true,true);
            }else{
                var option = new Option(elemento, i);
            }
            lista.options[x] = option;
        }
		function f_filtro_activos_hasta(data){
            xajax_f_filtro_activos_hasta(xajax.getFormValues("form1"));           
        }

        function toggleProrroga() {
            var check = document.getElementById("aplicar_prorroga");
            var input = document.getElementById("meses_prorroga");
            var boton = document.getElementById("btnProrrogarPlan");
            if (!check || !input || !boton) {
                return;
            }
            if (check.checked) {
                input.disabled = false;
                boton.style.display = 'inline-block';
            } else {
                input.disabled = true;
                input.value = '';
                boton.style.display = 'none';
            }
        }

        function initPlanDataTable() {
            if (!window.jQuery || !jQuery.fn.DataTable) {
                return;
            }
            var tabla = $('#tablaPlanDepreciacion');
            if (!tabla.length) {
                return;
            }
            if ($.fn.DataTable.isDataTable(tabla)) {
                tabla.DataTable().destroy();
            }
            tabla.DataTable({
                paging: true,
                searching: true,
                pageLength: 25,
                order: [[0, 'asc'], [2, 'asc']],
                language: {
                    sProcessing: "Procesando...",
                    sLengthMenu: "Mostrar _MENU_ registros",
                    sZeroRecords: "No se encontraron resultados",
                    sEmptyTable: "Ningún dato disponible en esta tabla",
                    sInfo: "Mostrando _START_ a _END_ de _TOTAL_ registros",
                    sInfoFiltered: "(filtrado de un total de _MAX_ registros)",
                    sSearch: "Buscar:",
                    oPaginate: {
                        sFirst: "Primero",
                        sLast: "Último",
                        sNext: "Siguiente",
                        sPrevious: "Anterior"
                    }
                }
            });
        }
   
		function eliminar_lista_activo_hasta() {
            var sel = document.getElementById("cod_activo_hasta");
            for (var i = (sel.length - 1); i >= 1; i--) {
                aBorrar = sel.options[i];
                aBorrar.parentNode.removeChild(aBorrar);
            }
        }
        
        function anadir_elemento_activo_hasta(x, i, elemento) {
            var lista = document.form1.cod_activo_hasta;
            var option = new Option(elemento, i);
            lista.options[x] = option;
            document.form1.cod_activo_hasta.value = i;
        }
		

    </script>
    <!--DIBUJA FORMULARIO FILTRO-->
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <body>
        <div class="row" id="Div_Principal">
            <form id="form1" class="form-horizontal" name="form1" action="javascript:void(null);">
                <div class="main-row col-md-12">
                    <div class="col-md-12">
                        <h4 class="text-primary">PROCESO <small> PLAN DE DEPRECIACIÓN </small></h4>
                            <?
                                global $DSN_Ifx, $DSN;
                                if (session_status() !== PHP_SESSION_ACTIVE) {session_start();}
                                $idempresa  = $_SESSION['U_EMPRESA'];
                                $idsucursal = $_SESSION['U_SUCURSAL'];
                                $idPerfil   = $_SESSION['U_PERFIL'];

                                $oCon = new Dbo;
                                $oCon->DSN = $DSN;
                                $oCon->Conectar();
                                
                                $oIfx = new Dbo;
                                $oIfx->DSN = $DSN_Ifx;
                                $oIfx->Conectar();

                                $fu = new Formulario;
                                $fu->DSN = $DSN;

                                $sql_empr = '';
                                if ($idPerfil != 1 && $idPerfil != 2) {
                                    $sql_empr = " where empr_cod_empr = $idempresa ";
                                }

                                // EMPRESA
                                $sql = "select empr_cod_empr, empr_nom_empr from saeempr $sql_empr ";
                                $lista_empr = lista_boostrap_func($oIfx, $sql, $idempresa, 'empr_cod_empr',  'empr_nom_empr' );

                                $sqlSucu = "";
                                if ($idPerfil != 1 && $idPerfil != 2) {
                                    $sqlSucu = " and sucu_cod_sucu = $idsucursal";
                                }

                                $sql = "select sucu_cod_sucu, sucu_nom_sucu
                                        from saesucu  where sucu_cod_empr = $idempresa
                                        $sqlSucu";
                                $lista_sucu = lista_boostrap_func($oIfx, $sql, $idsucursal, 'sucu_cod_sucu',  'sucu_nom_sucu' );    
                                // LISTA GRUPOS
                                $sql = " SELECT gact_cod_gact, gact_des_gact
                                        FROM saegact
                                        WHERE gact_cod_empr  = $idempresa ";                               								
                                $listaGrupo = lista_boostrap_func($oIfx, $sql, '', 'gact_cod_gact',  'gact_des_gact' );

                                // LISTA SUBGRUPOS
                                $sql = " SELECT sgac_cod_sgac, sgac_des_sgac from saesgac where sgac_cod_empr = $idempresa ";
                                $listaSubGrupo = lista_boostrap_func($oIfx, $sql, '', 'sgac_cod_sgac',  'sgac_des_sgac' );
                            ?>
                    </div>
                    <div class="col-md-12">
                            <div class="btn-group">
                                <div class="btn btn-primary btn-sm" onclick="location.reload();">
                                    <span class="glyphicon glyphicon-file"></span>
                                    Nuevo
                                </div>                                
                            </div>                
                    </div>                  

                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="empresa">* Empresa </label>
                                <select id="empresa" name="empresa" class="form-control input-sm select2" data-placeholder="Seleccione una empresa" onchange="cargar_sucu();" required>
                                    <option value="0">Seleccione una opcion..</option>
                                    <?=$lista_empr;?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="sucursal">* Sucursal </label>
                                <select id="sucursal" name="sucursal" class="form-control input-sm select2" data-placeholder="Seleccione una sucursal" onchange="f_filtro_grupo();" required>
                                    <option value="0">Seleccione una opcion..</option>  
                                    <?=$lista_sucu;?>                                  
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="row" style="margin-top: 10px;">
                            <div class="col-md-3">
                                <label for="cod_grupo"> Grupo </label>
                                <select id="cod_grupo" name="cod_grupo" class="form-control input-sm select2" data-placeholder="Seleccione un grupo" onchange="f_filtro_subgrupo();">
                                    <option value="0">Seleccione una opcion..</option>
                                    <?=$listaGrupo;?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="cod_subgrupo"> Subgrupo </label>
                                <select id="cod_subgrupo" name="cod_subgrupo" class="form-control input-sm select2" data-placeholder="Seleccione un subgrupo" onchange="f_filtro_activos_desde();f_filtro_activos_hasta();">
                                    <option value="0">Seleccione una opcion..</option>
                                    <?=$listaSubGrupo;?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="cod_activo_desde"> Activo Desde </label>
                                <select id="cod_activo_desde" name="cod_activo_desde" class="form-control input-sm select2" data-placeholder="Activo desde" >
                                    <option value="0">Seleccione una opcion..</option>
                                    <?=$listaActivos;?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="cod_activo_hasta"> Activo Hasta </label>
                                <select id="cod_activo_hasta" name="cod_activo_hasta" class="form-control input-sm select2" data-placeholder="Activo hasta" >
                                    <option value="0">Seleccione una opcion..</option>
                                    <?=$listaActivos;?>
                                </select>
                            </div>
                        </div>
                        <div class="row" style="margin-top: 10px;">
                            <div class="col-md-12">
                                <div class="checkbox" style="margin-top: 0;">
                                    <label>
                                        <input type="checkbox" id="aplicar_prorroga" name="aplicar_prorroga" value="1" onclick="toggleProrroga();">
                                        Aplicar prórroga de vida útil
                                        <span class="glyphicon glyphicon-info-sign text-info" title="Extiende la vida útil agregando nuevos meses al plan. Se aplica solo si selecciona un activo específico y el plan ya terminó."></span>
                                    </label>
                                </div>
                                <div class="input-group">
                                    <span class="input-group-addon">Meses</span>
                                    <input type="number" min="1" id="meses_prorroga" name="meses_prorroga" class="form-control input-sm" disabled>
                                    <span class="input-group-btn">
                                        <button id="btnProrrogarPlan" type="button" class="btn btn-warning btn-sm" onclick="prorrogarPlan();" style="display: none;">
                                            <span class="glyphicon glyphicon-time"></span>
                                            Aplicar Prórroga
                                        </button>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="row" style="margin-top: 10px;">
                            <div class="col-md-12">
                                <div id="btnConsultarPlan" class="btn btn-primary btn-sm" data-disabled="false" onclick="consultarPlan();" style="width: 100%">
                                    <span class="glyphicon glyphicon-search"></span>
                                    Consultar Plan
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div id="divPlanMensajes" style="margin-top: 15px; display: none;"></div>
                    <div id="divPlanTabla" class="table-responsive" style="margin-top: 10px;"></div>
                </div>
            </form>
        </div>
    </body>
         
    <script>genera_cabecera_formulario(); generaSelect2(); toggleProrroga();</script> 
    <? /*     * ***************************************************************** */ ?>
    <? /* NO MODIFICAR ESTA SECCION */ ?>
<? } ?>
<? include_once(FOOTER_MODULO); ?>
<? /* * ***************************************************************** */ ?>
