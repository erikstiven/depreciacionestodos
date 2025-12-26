<?
include_once('../../Include/config.inc.php');
include_once(path(DIR_INCLUDE) . 'conexiones/db_conexion.php');
include_once(path(DIR_INCLUDE) . 'comun.lib.php');

if (session_status() !== PHP_SESSION_ACTIVE) {
	session_start();
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>

	<link rel="stylesheet" type="text/css" href="<?= $_COOKIE["JIREH_INCLUDE"] ?>css/general.css">
	<link href="<?= $_COOKIE["JIREH_INCLUDE"] ?>Clases/Formulario/Css/Formulario.css" rel="stylesheet" type="text/css" />
	<link rel="stylesheet" type="text/css" href="css/estilo.css">

	<!--CSS-->
	<link rel="stylesheet" type="text/css" href="<?= $_COOKIE["JIREH_INCLUDE"] ?>css/bootstrap-3.3.7-dist/css/bootstrap.min.css" media="screen" />
	<link rel="stylesheet" type="text/css" href="js/jquery/plugins/simpleTree/style.css" />
	<link rel="stylesheet" href="media/css/bootstrap.css">
	<link rel="stylesheet" href="media/css/dataTables.bootstrap.min.css">
	<link rel="stylesheet" href="media/font-awesome/css/font-awesome.css">
	<link type="text/css" href="css/style.css" rel="stylesheet">
	</link>

	<!--Javascript-->
	<script type="text/javascript" src="js/jquery.js"></script>
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script src="media/js/jquery-1.10.2.js"></script>
	<script src="media/js/jquery.dataTables.min.js"></script>
	<script src="media/js/dataTables.bootstrap.min.js"></script>
	<script src="media/js/bootstrap.js"></script>
	<script type="text/javascript" language="javascript" src="<?= $_COOKIE["JIREH_INCLUDE"] ?>css/bootstrap-3.3.7-dist/js/bootstrap.min.js"></script>
	<script src="media/js/lenguajeusuario_producto.js"></script>

	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>CODIGO ACTIVO FIJO</title>

	<script>
		function formato() {
			document.getElementById('tres').style.display = "none";
			window.print();
		}
	</script>
</head>

<style type="text/css">
	#cabecera {
		border-spacing: 0;
		border-collapse: collapse;
	}

	#table-striped>tbody>tr:nth-of-type(odd) {
		background-color: #f9f9f9;
	}

	.table-condensed>thead>tr>th,
	.table-condensed>tbody>tr>th,
	.table-condensed>tfoot>tr>th,
	.table-condensed>thead>tr>td,
	.table-condensed>tbody>tr>td,
	.table-condensed>tfoot>tr>td {
		padding: 5px;
	}
</style>

<body>
	<?
	$oCnx = new Dbo();
	$oCnx->DSN = $DSN;
	$oCnx->Conectar();

	$oIfx = new Dbo;
	$oIfx->DSN = $DSN_Ifx;
	$oIfx->Conectar();

	$oIfxA = new Dbo;
	$oIfxA->DSN = $DSN_Ifx;
	$oIfxA->Conectar();
	$empresa      = $_SESSION['U_EMPRESA'];
	$sucursal      = $_SESSION['U_SUCURSAL'];

	$codigoActivo       = 'archivos/' . $_GET['codigo'] . '.jpg';
	$clave = $_GET['codigo'];
	$sql = "select act_nom_act, act_fcmp_act, empr_nom_empr
		from saeact, saeempr 
		where act_cod_empr = empr_cod_empr
		and act_clave_act = '$clave' 
		and act_cod_empr = $empresa 
		-- and act_cod_sucu = $sucursal 
		";
	$nombreActivo =  consulta_string($sql, 'act_nom_act', $oIfx, '');
	$feccha_compra =  consulta_string($sql, 'act_fcmp_act', $oIfx, '');
	$nombreEmpresa =  consulta_string($sql, 'empr_nom_empr', $oIfx, '');


	//echo $sql; exit;
	//////////
	//echo path(DIR_INCLUDE) . $codigoActivo;
	//exit;
	$html1 = '';
	$html1 .= '<div id="tres">
				<table boder = 1>
				  <tr>
					<td align="center"><label>
					  <input name="Submit2" type="submit" class="Estilo2" value="Imprimir" onclick="formato();" />
					</label></td>
				  </tr>
				</table>
		  </div>';
	$html1 .= '<div id="cuatro">
				<table style = "margin-left: 30px;">
					<tr>
						<td style="font-size: 12px;">' . $nombreEmpresa . '	</td>
					</tr>
					<tr>
						<td>
							<img  src="codigo_de_barras/' . $clave . '.jpg"> </p>
						</td>
					<tr/>
					<tr>
						<td style="font-size: 12px;">' . $nombreActivo . '	</td>
					</tr>
					<tr>
						<td style="font-size: 12px;">F. Compra: ' . $feccha_compra . '	</td>
					</tr>
				</table>
		  </div>';

	echo  $html1;

	?>

</body>

</html>