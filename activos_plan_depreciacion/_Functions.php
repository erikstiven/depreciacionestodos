<?php

function obtener_fecha_inicio_activo($fecha_depreciacion, $fecha_compra)
{
    $fecha_inicio = $fecha_depreciacion;
    if (empty($fecha_inicio)) {
        $fecha_inicio = $fecha_compra;
    }
    $inicio = DateTime::createFromFormat('Y-m-d', $fecha_inicio);
    return $inicio ?: null;
}

function calcular_plan_prorrateo_real(DateTime $fecha_inicio, $vida_util_meses, $valor_neto)
{
    $plan = [];
    $vida_util_meses = intval($vida_util_meses);
    if ($vida_util_meses <= 0) {
        return $plan;
    }

    $valor_neto = floatval($valor_neto);
    $valor_mensual = $valor_neto / $vida_util_meses;
    $acumulado = 0.0;

    $mes_actual = clone $fecha_inicio;

    for ($i = 0; $i < $vida_util_meses; $i++) {
        $inicio_mes = $i === 0 ? clone $mes_actual : new DateTime($mes_actual->format('Y-m-01'));
        $fin_mes = new DateTime($inicio_mes->format('Y-m-t'));
        $dias_mes = intval($fin_mes->format('d'));
        $dias_prorrateo = $i === 0
            ? ($dias_mes - intval($inicio_mes->format('d')) + 1)
            : $dias_mes;

        if ($i === $vida_util_meses - 1) {
            $valor_mes = round($valor_neto - $acumulado, 2);
        } else {
            $valor_mes = $i === 0
                ? round($valor_mensual * ($dias_prorrateo / $dias_mes), 2)
                : round($valor_mensual, 2);
        }

        $acumulado = round($acumulado + $valor_mes, 2);

        $plan[] = [
            'anio' => intval($fin_mes->format('Y')),
            'fecha_desde' => $inicio_mes->format('Y-m-d'),
            'fecha_hasta' => $fin_mes->format('Y-m-d'),
            'valor_mes' => $valor_mes,
            'dias_mes' => $dias_mes,
            'dias_prorrateo' => $dias_prorrateo,
        ];

        $mes_actual = (clone $inicio_mes)->modify('first day of next month');
    }

    return $plan;
}
