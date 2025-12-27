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

 
        function genera_cabecera_filtro() {
            xajax_genera_cabecera_formulario('filtro', xajax.getFormValues("form1"));
        }		
        function generar(){
            if (document.getElementById("btnProcesar").getAttribute("data-disabled") === "true") {
                return;
            }
            if(ProcesarFormulario() == true){
                xajax_generar(xajax.getFormValues("form1"));
            }
        }

        function listarPendientes(){
            if (document.getElementById("btnProcesar").getAttribute("data-disabled") === "true") {
                return;
            }
            if (ProcesarFormulario() == true){
                xajax_listar_pendientes(xajax.getFormValues("form1"));
            }
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

        function f_filtro_anio_desde(data){
            xajax_f_filtro_anio_desde(xajax.getFormValues("form1"), data);           
        }
   
        function eliminar_lista_anio_desde() {
            var sel = document.getElementById("anio_desde");
            for (var i = (sel.length - 1); i >= 1; i--) {
                aBorrar = sel.options[i];
                aBorrar.parentNode.removeChild(aBorrar);
            }
        }
        
        function anadir_elemento_anio_desde(x, i, elemento) {
            var lista = document.form1.anio_desde;
            var option = new Option(elemento, i);
            lista.options[x] = option;
            document.form1.anio_desde.value = i;
        }

        function f_filtro_anio_hasta(data){
            xajax_f_filtro_anio_hasta(xajax.getFormValues("form1"), data);           
        }
   
        function eliminar_lista_anio_hasta() {
            var sel = document.getElementById("anio_hasta");
            for (var i = (sel.length - 1); i >= 1; i--) {
                aBorrar = sel.options[i];
                aBorrar.parentNode.removeChild(aBorrar);
            }
        }
        
        function anadir_elemento_anio_hasta(x, i, elemento) {
            var lista = document.form1.anio_hasta;
            var option = new Option(elemento, i);
            lista.options[x] = option;
            document.form1.anio_hasta.value = i;
        }

        function f_filtro_mes_desde(data){
            xajax_f_filtro_mes_desde(xajax.getFormValues("form1"), data);           
        }
   
        function eliminar_lista_mes_desde() {
            var sel = document.getElementById("mes_desde");
            for (var i = (sel.length - 1); i >= 1; i--) {
                aBorrar = sel.options[i];
                aBorrar.parentNode.removeChild(aBorrar);
            }
        }
        
        function anadir_elemento_mes_desde(x, i, elemento) {
            var lista = document.form1.mes_desde;
            var option = new Option(elemento, i);
            lista.options[x] = option;
            document.form1.mes_desde.value = i;
        }

        function f_filtro_mes_hasta(data){
            xajax_f_filtro_mes_hasta(xajax.getFormValues("form1"), data);           
        }
   
        function eliminar_lista_mes_hasta() {
            var sel = document.getElementById("mes_hasta");
            for (var i = (sel.length - 1); i >= 1; i--) {
                aBorrar = sel.options[i];
                aBorrar.parentNode.removeChild(aBorrar);
            }
        }
        
        function anadir_elemento_mes_hasta(x, i, elemento) {
            var lista = document.form1.mes_hasta;
            var option = new Option(elemento, i);
            lista.options[x] = option;
            document.form1.mes_hasta.value = i;
        }

        function validar_rango_periodo() {
            var anioDesde = document.getElementById("anio_desde").value;
            var mesDesde = document.getElementById("mes_desde").value;
            var anioHasta = document.getElementById("anio_hasta").value;
            var mesHasta = document.getElementById("mes_hasta").value;
            var mensaje = document.getElementById("mensaje_rango");
            var boton = document.getElementById("btnProcesar");

            var periodoDesde = parseInt(anioDesde, 10) * 100 + parseInt(mesDesde, 10);
            var periodoHasta = parseInt(anioHasta, 10) * 100 + parseInt(mesHasta, 10);

            if (!anioDesde || !mesDesde || !anioHasta || !mesHasta) {
                mensaje.textContent = '';
                boton.classList.add("disabled");
                boton.setAttribute("data-disabled", "true");
                return false;
            }

            if (periodoDesde > periodoHasta) {
                mensaje.textContent = 'El rango es inválido: Año/Mes Desde debe ser menor o igual a Año/Mes Hasta.';
                boton.classList.add("disabled");
                boton.setAttribute("data-disabled", "true");
                return false;
            }

            mensaje.textContent = '';
            boton.classList.remove("disabled");
            boton.setAttribute("data-disabled", "false");
            return true;
        }

        function mostrarResumenDepreciacion() {
            var modal = document.getElementById("modalResumenDepre");
            if (!modal) {
                return;
            }
            modal.style.display = "block";
            modal.classList.add("in");
            modal.setAttribute("aria-hidden", "false");
            var backdrop = document.createElement("div");
            backdrop.className = "modal-backdrop fade in";
            backdrop.id = "modalResumenDepreBackdrop";
            document.body.appendChild(backdrop);
        }

        function cerrarResumenDepreciacion() {
            var modal = document.getElementById("modalResumenDepre");
            if (modal) {
                modal.style.display = "none";
                modal.classList.remove("in");
                modal.setAttribute("aria-hidden", "true");
            }
            var backdrop = document.getElementById("modalResumenDepreBackdrop");
            if (backdrop && backdrop.parentNode) {
                backdrop.parentNode.removeChild(backdrop);
            }
        }

        function mostrarPendientesDepreciacion() {
            var modal = document.getElementById("modalPendientesDepre");
            if (!modal) {
                return;
            }
            modal.style.display = "block";
            modal.classList.add("in");
            modal.setAttribute("aria-hidden", "false");
            var backdrop = document.createElement("div");
            backdrop.className = "modal-backdrop fade in";
            backdrop.id = "modalPendientesDepreBackdrop";
            document.body.appendChild(backdrop);
        }

        function cerrarPendientesDepreciacion() {
            var modal = document.getElementById("modalPendientesDepre");
            if (modal) {
                modal.style.display = "none";
                modal.classList.remove("in");
                modal.setAttribute("aria-hidden", "true");
            }
            var backdrop = document.getElementById("modalPendientesDepreBackdrop");
            if (backdrop && backdrop.parentNode) {
                backdrop.parentNode.removeChild(backdrop);
            }
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
                        <h4 class="text-primary">PROCESO <small> CALCULO DEPRECIACIONES </small></h4>
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
                                // FECHAS
                                $id_anio = date("Y");
                                $id_mes  = date("m");
								$fechaActual = date("Y-m-d");
                                $sql = "select ejer_cod_ejer from saeejer where date_part('year',ejer_fec_inil) = $id_anio and ejer_cod_empr = $idempresa ";
                                $ejer_cod_ejer = consulta_string_func($sql, 'ejer_cod_ejer', $oIfx, 0);

                                $sql = "select ejer_cod_ejer,  date_part('year',ejer_fec_inil) as anio from saeejer where
                                                ejer_cod_empr = $idempresa order by 2 desc ";
                                $lista_ejer = lista_boostrap_func($oIfx, $sql, $id_anio, 'anio',  'anio' );   

                                $sql = "select prdo_num_prdo, prdo_nom_prdo from saeprdo where
                                                prdo_cod_empr = $idempresa and
                                                prdo_cod_ejer = $ejer_cod_ejer
                                                order by 1 ";
                                $lista_mes = lista_boostrap_func($oIfx, $sql, $id_mes, 'prdo_num_prdo',  'prdo_nom_prdo' );
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
                        <div class="form-row">
                            <div class="col-md-3">
                                <label for="empresa">* Empresa </label>
                                <select id="empresa" name="empresa" class="form-control input-sm select2" data-placeholder="Seleccione una empresa" onchange="cargar_sucu();" required>
                                    <option value="0">Seleccione una opcion..</option>
                                    <?=$lista_empr;?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="sucursal">* Sucursal </label>
                                <select id="sucursal" name="sucursal" class="form-control input-sm select2" data-placeholder="Seleccione una sucursal" onchange="f_filtro_anio_desde(); f_filtro_anio_hasta(); f_filtro_grupo(); validar_rango_periodo();" required>
                                    <option value="0">Seleccione una opcion..</option>  
                                    <?=$lista_sucu;?>                                  
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="anio_desde"> * Año Desde </label>
                                <select id="anio_desde" name="anio_desde" class="form-control input-sm select2" data-placeholder="Seleccione un año" onchange="f_filtro_mes_desde(); validar_rango_periodo();" required>
                                    <option value="">Seleccione una opcion..</option>
                                    <?=$lista_ejer;?>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label for="mes_desde"> Mes Desde </label>
                                <select id="mes_desde" name="mes_desde" class="form-control input-sm select2" data-placeholder="Seleccione un mes" onchange="validar_rango_periodo();">
                                    <option value="">Seleccione una opcion..</option>
                                    <?=$lista_mes;?>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-3">
                                <label for="anio_hasta"> * Año Hasta </label>
                                <select id="anio_hasta" name="anio_hasta" class="form-control input-sm select2" data-placeholder="Seleccione un año" onchange="f_filtro_mes_hasta(); validar_rango_periodo();" required>
                                    <option value="">Seleccione una opcion..</option>
                                    <?=$lista_ejer;?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="mes_hasta"> Mes Hasta </label>
                                <select id="mes_hasta" name="mes_hasta" class="form-control input-sm select2" data-placeholder="Seleccione un mes" onchange="validar_rango_periodo();">
                                    <option value="">Seleccione una opcion..</option>
                                    <?=$lista_mes;?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label>&nbsp;</label>
                                <div id="mensaje_rango" class="text-danger"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-row">                            
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
                        <div class="form-row"> 
                            <div class="col-md-12">
                                    <div><label for="consultar">* Consultar:</label></div>
                                    <div id="btnProcesar" class="btn btn-primary btn-sm disabled" data-disabled="true" onclick="generar();" style="width: 100%">
                                        <span class="glyphicon glyphicon-cog"></span>
                                        Procesar
                                    </div>
                                    <div class="btn btn-info btn-sm" onclick="listarPendientes();" style="width: 100%; margin-top: 8px;">
                                        <span class="glyphicon glyphicon-list-alt"></span>
                                        Ver meses pendientes
                                    </div>
                                </div>
                            </div>
                    </div>
                </div>
            </form>
        </div>
        <div id="modalResumenDepre" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
            <div class="modal-dialog modal-lg">
                <div class="modal-content" style="max-height: 90vh;">
                    <div class="modal-header">
                        <button type="button" class="close" onclick="cerrarResumenDepreciacion();" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title">Resumen de Ejecución</h4>
                    </div>
                    <div class="modal-body" style="max-height: calc(90vh - 140px); overflow-y: auto;">
                        <div id="divResumenDepreciacion"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" onclick="cerrarResumenDepreciacion();">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
        <div id="modalPendientesDepre" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
            <div class="modal-dialog modal-lg">
                <div class="modal-content" style="max-height: 90vh;">
                    <div class="modal-header">
                        <button type="button" class="close" onclick="cerrarPendientesDepreciacion();" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title">Meses pendientes de depreciación</h4>
                    </div>
                    <div class="modal-body" style="max-height: calc(90vh - 140px); overflow-y: auto;">
                        <div id="divPendientesDepreciacion"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" onclick="cerrarPendientesDepreciacion();">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    </body>
         
    <script>genera_cabecera_formulario(); generaSelect2(); validar_rango_periodo();/*genera_detalle();genera_form_detalle();*/</script> 
    <? /*     * ***************************************************************** */ ?>
    <? /* NO MODIFICAR ESTA SECCION */ ?>
<? } ?>
<? include_once(FOOTER_MODULO); ?>
<? /* * ***************************************************************** */ ?>
