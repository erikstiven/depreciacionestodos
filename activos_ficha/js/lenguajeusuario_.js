$(document).ready(function() {	
	var nomGrupo = '';
	$('#example').DataTable( {	
		"searching": true,
		"pageLength": 20,
		"bDeferRender": true,	
		"sPaginationType": "full_numbers",
		"ajax": {
			"url": "buscar_grupo.php?nomGrupo="+nomGrupo,
	    	"type": "POST"
		},					
		"columns": [
			{ "data": "clave" },
            { "data": "grupo" },
           	{ "data": "subgrupo" },
            { "data": "nombre" },
			{ "data": "selecciona" }
		],
		"keys": {
            "columns": ":not(:first-child)",
            "editor":  "editor"
        },
		"oLanguage": {
            "sProcessing":     "Procesando...",
		    "sLengthMenu": 'Mostrar <select id="cantidad_datos" name="cantidad_datos">'+ 
		        '<option value="20">20</option>'+
            	'<option value="30">30</option>'+
		        '<option value="60">60</option>'+
		        '<option value="90">90</option>'+
		        '<option value="120">120</option>'+
		        '<option value="150">150</option>'+
		        '<option value="-1">Todo</option>'+
		        '</select> registros',    
		    "sZeroRecords":    "No se encontraron resultados",
		    "sEmptyTable":     "Ningun dato disponible en esta tabla",
		    "sInfo":           "Mostrando (_START_ al _END_) de  _TOTAL_ registros",
		    
		    "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
		    "sInfoPostFix":    "",
		    "sSearch":         "Filtrar:",
		    "sUrl":            "",
		    "sInfoThousands":  ",",
		    "sLoadingRecords": "Por favor espere - cargando...",
		    "oPaginate": {
		        "sFirst":    "Primero",
		        "sLast":     "Ultimo",
		        "sNext":     "Siguiente",
		        "sPrevious": "Anterior"
		    },
		    "oAria": {
		        "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
		        "sSortDescending": ": Activar para ordenar la columna de manera descendente"
		    }
        }
	});
});

function recarga_lista() {
		var nomGrupo = '';
	$('#example').DataTable( {	
		"searching": true,
		"pageLength": 20,
		"bDeferRender": true,	
		"sPaginationType": "full_numbers",
		"ajax": {
			"url": "buscar_grupo.php?nomGrupo="+nomGrupo,
	    	"type": "POST"
		},					
		"columns": [
            { "data": "clave" },
            { "data": "grupo" },
            { "data": "subgrupo" },
            { "data": "nombre" },
            { "data": "selecciona" }
		],
		"keys": {
            "columns": ":not(:first-child)",
            "editor":  "editor"
        },
		"oLanguage": {
            "sProcessing":     "Procesando...",
		    "sLengthMenu": 'Mostrar <select id="cantidad_datos" name="cantidad_datos">'+ 
		        '<option value="20">20</option>'+
            	'<option value="30">30</option>'+
		        '<option value="60">60</option>'+
		        '<option value="90">90</option>'+
		        '<option value="120">120</option>'+
		        '<option value="150">150</option>'+
		        '<option value="-1">Todo</option>'+
		        '</select> registros',    
		    "sZeroRecords":    "No se encontraron resultados",
		    "sEmptyTable":     "Ningun dato disponible en esta tabla",
		    "sInfo":           "Mostrando (_START_ al _END_) de  _TOTAL_ registros",
		    
		    "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
		    "sInfoPostFix":    "",
		    "sSearch":         "Filtrar:",
		    "sUrl":            "",
		    "sInfoThousands":  ",",
		    "sLoadingRecords": "Por favor espere - cargando...",
		    "oPaginate": {
		        "sFirst":    "Primero",
		        "sLast":     "Ultimo",
		        "sNext":     "Siguiente",
		        "sPrevious": "Anterior"
		    },
		    "oAria": {
		        "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
		        "sSortDescending": ": Activar para ordenar la columna de manera descendente"
		    }
        }
	});
}
function listar_cuentas_contables(id){
    var nomCuenta = document.getElementById(id).value;
    $('#table_cuentas').DataTable( {
        "searching": true,
        "pageLength": 30,
        "bDeferRender": true,
        "sPaginationType": "full_numbers",
        "ajax": {
            "url": "buscar_cuenta.php?nomCuenta="+nomCuenta+"&id="+id,
            "type": "POST"
        },
        "columns": [
            { "data": "codigo" },
            { "data": "nombre" },
            { "data": "selecciona" }
        ],
        "keys": {
            "columns": ":not(:first-child)",
            "editor":  "editor"
        },
        "oLanguage": {
            "sProcessing":     "Procesando...",
            "sLengthMenu": 'Mostrar <select id="cantidad_datos" name="cantidad_datos">'+
            '<option value="30">30</option>'+
            '<option value="60">60</option>'+
            '<option value="90">90</option>'+
            '<option value="120">120</option>'+
            '<option value="150">150</option>'+
            '<option value="-1">Todo</option>'+
            '</select> registros',
            "sZeroRecords":    "No se encontraron resultados",
            "sEmptyTable":     "Ningun dato disponible en esta tabla",
            "sInfo":           "Mostrando (_START_ al _END_) de  _TOTAL_ registros",

            "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
            "sInfoPostFix":    "",
            "sSearch":         "Filtrar:",
            "sUrl":            "",
            "sInfoThousands":  ",",
            "sLoadingRecords": "Por favor espere - cargando...",
            "oPaginate": {
                "sFirst":    "Primero",
                "sLast":     "Ultimo",
                "sNext":     "Siguiente",
                "sPrevious": "Anterior"
            },
            "oAria": {
                "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
                "sSortDescending": ": Activar para ordenar la columna de manera descendente"
            }
        }
    });
}
function listar_centro_costos(id){
    var nomCuenta = document.getElementById(id).value;
    $('#table_ccostos').DataTable( {
        "searching": true,
        "pageLength": 30,
        "bDeferRender": true,
        "sPaginationType": "full_numbers",
        "ajax": {
            "url": "buscar_ccostos.php?nomCuenta="+nomCuenta+"&id="+id,
            "type": "POST"
        },
        "columns": [
            { "data": "codigo" },
            { "data": "nombre" },
            { "data": "selecciona" }
        ],
        "keys": {
            "columns": ":not(:first-child)",
            "editor":  "editor"
        },
        "oLanguage": {
            "sProcessing":     "Procesando...",
            "sLengthMenu": 'Mostrar <select id="cantidad_datos" name="cantidad_datos">'+
            '<option value="30">30</option>'+
            '<option value="60">60</option>'+
            '<option value="90">90</option>'+
            '<option value="120">120</option>'+
            '<option value="150">150</option>'+
            '<option value="-1">Todo</option>'+
            '</select> registros',
            "sZeroRecords":    "No se encontraron resultados",
            "sEmptyTable":     "Ningun dato disponible en esta tabla",
            "sInfo":           "Mostrando (_START_ al _END_) de  _TOTAL_ registros",

            "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
            "sInfoPostFix":    "",
            "sSearch":         "Filtrar:",
            "sUrl":            "",
            "sInfoThousands":  ",",
            "sLoadingRecords": "Por favor espere - cargando...",
            "oPaginate": {
                "sFirst":    "Primero",
                "sLast":     "Ultimo",
                "sNext":     "Siguiente",
                "sPrevious": "Anterior"
            },
            "oAria": {
                "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
                "sSortDescending": ": Activar para ordenar la columna de manera descendente"
            }
        }
    });
}
function listar_empleados(){
    var nomCuenta = document.getElementById('cod_empleado').value;
    $('#table_empleados').DataTable( {
        "searching": true,
        "pageLength": 30,
        "bDeferRender": true,
        "sPaginationType": "full_numbers",
        "ajax": {
            "url": "buscar_empleados.php?nomCuenta="+nomCuenta,
            "type": "POST"
        },
        "columns": [
            { "data": "codigo" },
            { "data": "nombre" },
            { "data": "selecciona" }
        ],
        "keys": {
            "columns": ":not(:first-child)",
            "editor":  "editor"
        },
        "oLanguage": {
            "sProcessing":     "Procesando...",
            "sLengthMenu": 'Mostrar <select id="cantidad_datos" name="cantidad_datos">'+
            '<option value="30">30</option>'+
            '<option value="60">60</option>'+
            '<option value="90">90</option>'+
            '<option value="120">120</option>'+
            '<option value="150">150</option>'+
            '<option value="-1">Todo</option>'+
            '</select> registros',
            "sZeroRecords":    "No se encontraron resultados",
            "sEmptyTable":     "Ningun dato disponible en esta tabla",
            "sInfo":           "Mostrando (_START_ al _END_) de  _TOTAL_ registros",

            "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
            "sInfoPostFix":    "",
            "sSearch":         "Filtrar:",
            "sUrl":            "",
            "sInfoThousands":  ",",
            "sLoadingRecords": "Por favor espere - cargando...",
            "oPaginate": {
                "sFirst":    "Primero",
                "sLast":     "Ultimo",
                "sNext":     "Siguiente",
                "sPrevious": "Anterior"
            },
            "oAria": {
                "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
                "sSortDescending": ": Activar para ordenar la columna de manera descendente"
            }
        }
    });
}
