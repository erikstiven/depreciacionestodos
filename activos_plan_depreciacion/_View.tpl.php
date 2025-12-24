<div class="content-wrapper" style="min-height: 400px;">
    <section class="content-header">
        <h1>Generar Plan de Depreciación</h1>
    </section>

    <section class="content">
        <div class="box box-primary">
            <div class="box-body">
                <form id="form1" name="form1" action="javascript:void(null);">
                    <div class="row">
                        <div class="col-md-3">
                            <label>* Empresa</label>
                            <select class="form-control select2" id="empresa" name="empresa" onchange="f_filtro_sucursal(); f_filtro_grupo();">
                                <option value="">Seleccione una opción</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>* Sucursal</label>
                            <select class="form-control select2" id="sucursal" name="sucursal" onchange="f_filtro_subgrupo();">
                                <option value="">Seleccione una opción</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Grupo</label>
                            <select class="form-control select2" id="cod_grupo" name="cod_grupo" onchange="f_filtro_subgrupo();">
                                <option value="">Seleccione una opción</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Subgrupo</label>
                            <select class="form-control select2" id="cod_subgrupo" name="cod_subgrupo" onchange="f_filtro_activos_desde(); f_filtro_activos_hasta();">
                                <option value="">Seleccione una opción</option>
                            </select>
                        </div>
                    </div>

                    <div class="row" style="margin-top: 15px;">
                        <div class="col-md-6">
                            <label>Activo Desde</label>
                            <select class="form-control select2" id="cod_activo_desde" name="cod_activo_desde">
                                <option value="">Seleccione una opción</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>Activo Hasta</label>
                            <select class="form-control select2" id="cod_activo_hasta" name="cod_activo_hasta">
                                <option value="">Seleccione una opción</option>
                            </select>
                        </div>
                    </div>

                    <div class="row" style="margin-top: 20px;">
                        <div class="col-md-12">
                            <button type="button" class="btn btn-primary" onclick="generar_plan();">
                                <span class="glyphicon glyphicon-cog"></span>
                                Generar Plan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="box box-info">
            <div class="box-body" id="reporte">
                <div style="font-size:14px;"><b>..Sin Datos..</b></div>
            </div>
        </div>
    </section>
</div>
