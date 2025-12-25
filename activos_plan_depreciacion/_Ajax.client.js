function generaSelect2() {
    if (typeof $ === 'undefined' || typeof $.fn.select2 === 'undefined') {
        return;
    }
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

function generar_plan() {
    if (ProcesarFormulario() === true) {
        xajax_generar_plan(xajax.getFormValues("form1"));
    }
}

function f_filtro_sucursal() {
    xajax_f_filtro_sucursal(xajax.getFormValues("form1"));
}

function f_filtro_grupo() {
    xajax_f_filtro_grupo(xajax.getFormValues("form1"));
}

function f_filtro_subgrupo() {
    xajax_f_filtro_subgrupo(xajax.getFormValues("form1"));
}

function f_filtro_activos_desde() {
    xajax_f_filtro_activos_desde(xajax.getFormValues("form1"));
}

function f_filtro_activos_hasta() {
    xajax_f_filtro_activos_hasta(xajax.getFormValues("form1"));
}

function eliminar_lista_empresa() {
    var sel = document.getElementById("empresa");
    for (var i = (sel.length - 1); i >= 1; i--) {
        var aBorrar = sel.options[i];
        aBorrar.parentNode.removeChild(aBorrar);
    }
}

function anadir_elemento_empresa(x, i, elemento) {
    var lista = document.form1.empresa;
    var option = new Option(elemento, i);
    lista.options[x] = option;
}

function eliminar_lista_sucursal() {
    var sel = document.getElementById("sucursal");
    for (var i = (sel.length - 1); i >= 1; i--) {
        var aBorrar = sel.options[i];
        aBorrar.parentNode.removeChild(aBorrar);
    }
}

function anadir_elemento_sucursal(x, i, elemento) {
    var lista = document.form1.sucursal;
    var option = new Option(elemento, i);
    lista.options[x] = option;
}

function eliminar_lista_grupo() {
    var sel = document.getElementById("cod_grupo");
    for (var i = (sel.length - 1); i >= 1; i--) {
        var aBorrar = sel.options[i];
        aBorrar.parentNode.removeChild(aBorrar);
    }
}

function anadir_elemento_grupo(x, i, elemento) {
    var lista = document.form1.cod_grupo;
    var option = new Option(elemento, i);
    lista.options[x] = option;
}

function eliminar_lista_subgrupo() {
    var sel = document.getElementById("cod_subgrupo");
    for (var i = (sel.length - 1); i >= 1; i--) {
        var aBorrar = sel.options[i];
        aBorrar.parentNode.removeChild(aBorrar);
    }
}

function anadir_elemento_subgrupo(x, i, elemento) {
    var lista = document.form1.cod_subgrupo;
    var option = new Option(elemento, i);
    lista.options[x] = option;
}

function eliminar_lista_activo_desde() {
    var sel = document.getElementById("cod_activo_desde");
    for (var i = (sel.length - 1); i >= 1; i--) {
        var aBorrar = sel.options[i];
        aBorrar.parentNode.removeChild(aBorrar);
    }
}

function anadir_elemento_activo_desde(x, i, elemento) {
    var lista = document.form1.cod_activo_desde;
    var option = new Option(elemento, i);
    lista.options[x] = option;
}

function eliminar_lista_activo_hasta() {
    var sel = document.getElementById("cod_activo_hasta");
    for (var i = (sel.length - 1); i >= 1; i--) {
        var aBorrar = sel.options[i];
        aBorrar.parentNode.removeChild(aBorrar);
    }
}

function anadir_elemento_activo_hasta(x, i, elemento) {
    var lista = document.form1.cod_activo_hasta;
    var option = new Option(elemento, i);
    lista.options[x] = option;
}
