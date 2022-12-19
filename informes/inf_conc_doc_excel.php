<?php 
######################################################################################################
# ***************************************** Modificaciones ***************************************** #
######################################################################################################
#08/02/2017 | Erica G. | Cuentas y saldos Vigencias Anteriores
#11/05/2017 |ERICA G. |PARTIDAS CONCILIATORIAS SALDOS INICIALES
#10/05/2017 |ERICA G. |GIROS SIN COBRAR FECHA<=FECHAF // PARTIDAS PERIODOS ANTERIORES
######################################################################################################
# Creado por : Jhon Numpaque | Fecha de creación : 06/03/2017

header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Informe_Conciliacion_Bancaria.xls");
require_once("../Conexion/conexion.php");
session_start();
ini_set('max_execution_time', 0);
require_once('../Conexion/ConexionPDO.php');
require_once('../jsonPptal/funcionesPptal.php');
$con = new ConexionPDO();

$mes = $_GET['mes']; 
$cuenta = $_GET['cuenta']; 
$annov  = $_SESSION['anno'];

$a = 0;
$cuentaA = 0;
while($a==0){
    $nannov = anno($annov);
    #Año Anterior
    $anno2 = $nannov-1;
    $an2   = $con->Listar("SELECT * FROM gf_parametrizacion_anno WHERE anno = '$anno2' AND compania = $compania");
    if(count($an2)>0){ 
        $annova = $an2[0][0];
        $ca = $con->Listar("SELECT id_unico,codi_cuenta, equivalente_va FROM gf_cuenta WHERE md5(id_unico) = '$cuenta'");
        $id_cuenta = $ca[0][0];
        $codCuenta = $ca[0][1];
        $equivalente =$ca[0][2];
        if(!empty($equivalente)){
            #echo '1'."SELECT id_unico FROM gf_cuenta WHERE codi_cuenta = $equivalente AND parametrizacionanno = $annova";
            $ctaa =$con->Listar("SELECT id_unico FROM gf_cuenta WHERE codi_cuenta = $equivalente AND parametrizacionanno = $annova");
            if(count($ctaa)>0){
                if(!empty($ctaa[0][0])){
                    $cuentaA .= ','.$ctaa[0][0];
                }
            } else {
                #echo '2'."SELECT id_unico FROM gf_cuenta WHERE codi_cuenta = $equivalente AND parametrizacionanno = $annova";
                $ctaa =$con->Listar("SELECT id_unico FROM gf_cuenta WHERE codi_cuenta = $codCuenta AND parametrizacionanno = $annova");
                if(!empty($ctaa[0][0])){
                    $cuentaA .= ','.$ctaa[0][0];
                }
            }
        } else {
            #echo '3'."SELECT id_unico FROM gf_cuenta WHERE codi_cuenta = $codCuenta AND parametrizacionanno = $annova";
            $ctaa =$con->Listar("SELECT id_unico FROM gf_cuenta WHERE codi_cuenta = $codCuenta AND parametrizacionanno = $annova");
            if(!empty($ctaa[0][0])){
                    $cuentaA .= ','.$ctaa[0][0];
                }
        }
        $annov = $annova;
    } else {
    	$ca = $con->Listar("SELECT id_unico,codi_cuenta, equivalente_va FROM gf_cuenta WHERE md5(id_unico) = '$cuenta'");
        $id_cuenta = $ca[0][0];
        $a += 1;
    }
}

$cuentas =($cuentaA.','.$id_cuenta);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html >
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Informe de conciliaciones</title>
</head>
<body>
<table width="100%" border="1" cellspacing="0" cellpadding="0">

<?php    
$compania = $_SESSION['compania'];
$sqlC = "SELECT 	ter.id_unico,
					ter.razonsocial,
					UPPER(ti.nombre),
					ter.numeroidentificacion,
					dir.direccion,
					tel.valor,
					ter.ruta_logo
		FROM 		gf_tercero ter
		LEFT JOIN 	gf_tipo_identificacion ti 	ON 	ter.tipoidentificacion = ti.id_unico
		LEFT JOIN   gf_direccion dir 			ON	dir.tercero = ter.id_unico
		LEFT JOIN 	gf_telefono  tel 			ON 	tel.tercero = ter.id_unico
		WHERE 		ter.id_unico = $compania";
$resultC = $mysqli->query($sqlC);
$rowC = mysqli_fetch_row($resultC);
$razonsocial = $rowC[1];
$nombreIdent = $rowC[2];
$numeroIdent = $rowC[3];
$direccinTer = $rowC[4];
$telefonoTer = $rowC[5];
?> 
<tr>
<td colspan="6" align="center"><strong><?php echo $razonsocial; ?><br/>
    <?php echo $nombreIdent.":".$numeroIdent."<br/>".$direccinTer."Tel:".$telefonoTer; ?> </strong>
</td>
</tr>
<?php 
####################################################################################################################################################
# Consulta de banco
#
####################################################################################################################################################
$sqlBanco = "SELECT 	cta.id_unico,
						CONCAT(cta.codi_cuenta,' ',UPPER(cta.nombre))
			FROM		gf_cuenta cta
			WHERE 		md5(cta.id_unico) 	= '$cuenta'";
$resultBanco = $mysqli->query($sqlBanco);
$banco = mysqli_fetch_row($resultBanco);
####################################################################################################################################################
# Impresión de banco
#
####################################################################################################################################################
?>
<tr>
<td colspan="1" align="left"><strong>Banco:</td>
<td colspan="5" align="left"><?php echo $banco[1]; ?></td>
</tr>
<?php 
##########################################################################################################################################
# Consulta de periodo,mes
#
##########################################################################################################################################
$sqlMes = "SELECT 		mes.id_unico,
						mes.mes,
						param.anno, mes.numero 
		  FROM 			gf_mes mes 
		  LEFT JOIN 	gf_parametrizacion_anno param ON mes.parametrizacionanno = param.id_unico
		  WHERE 		md5(mes.id_unico) = '$mes'";
$resultMes = $mysqli->query($sqlMes);
$rowMes = mysqli_fetch_row($resultMes);
##########################################################################################################################################
# Asignación de valores de consulta
#
##########################################################################################################################################
$idMes = $rowMes[0];
$nomMes = $rowMes[1];
$annoMes = $rowMes[2];
$numMes = $rowMes[3];
##########################################################################################################################################
# Consulta de nombre de mes
#
##########################################################################################################################################
#
$sqlM = "SELECT id_unico FROM gf_mes WHERE id_unico = $idMes";
$resultM = $mysqli->query($sqlM);
$noM = mysqli_fetch_row($resultM);
##########################################################################################################################################
# Array con los numeros de los meses
#
##########################################################################################################################################
#
$meses = array( "Enero" => '01', "Febrero" => '02', "Marzo" => '03',"Abril" => '04', "Mayo" => '05', "Junio" => '06', 
                "Julio" => '07', "Agosto" => '08', "Septiembre" => '09', "Octubre" => '10', "Noviembre" => '11', "Diciembre" => '12'); 
$mess = $noM[0];



$calendario = CAL_GREGORIAN;
$diaF = cal_days_in_month($calendario, $numMes, $annoMes); 
$fechaF= $annoMes.'/'.$numMes.'/'.$diaF;
$fechaI = $annoMes.'/'.$numMes.'/01';


##fecha formato 
##ULTIMO DIA DEL MES
$calendario = CAL_GREGORIAN;
$diaF = cal_days_in_month($calendario, $numMes, $annoMes); 
$fechaC = $diaF.'/'.$mess.'/'.$annoMes;

####################################################################################################################################################
# Impresión de periodo y fecha
#
####################################################################################################################################################
?>
<tr>
<td colspan="1" border="1" align="left"><strong>Periodo:</strong></td>
<td colspan="2" border="1" align="left"><?php echo PHP_EOL.$nomMes.PHP_EOL.'de'.PHP_EOL.$annoMes; ?></td>
<td colspan="1" border="1" align="left"><strong>Fecha:</strong></td>
<td colspan="2" border="1" align="left"><?php echo PHP_EOL.date($fechaC); ?></td>
</tr>
<?php     

####################################################################################################################################################
# Consulta saldo en libros
#
####################################################################################################################################################
$sqlS = "SELECT 	SUM(dtc.valor)
		FROM		gf_detalle_comprobante dtc 
		LEFT JOIN	gf_comprobante_cnt cnt 			ON dtc.comprobante 		= cnt.id_unico		
		WHERE 		cnt.fecha <= '$fechaF' 
		AND 	    dtc.cuenta = $banco[0]";
$resultS = $mysqli->query($sqlS);
$saldo = mysqli_fetch_row($resultS);
####################################################################################################################################################
# Captura de valor de saldo en libros
#
####################################################################################################################################################
$saldoLibro = $saldo[0];
####################################################################################################################################################
# Consulta saldo extracto
#
####################################################################################################################################################
$sqlSE = "SELECT saldo_extracto FROM gf_partida_conciliatoria WHERE md5(id_unico) = '".$_GET['partida']."'";
$resultE = $mysqli->query($sqlSE);
$saldoE = mysqli_fetch_row($resultE);
####################################################################################################################################################
# Captura de valor saldo extracto
#
####################################################################################################################################################
$saldoExtracto = $saldoE[0];
####################################################################################################################################################
# Impresión saldo en libros y en saldo extracto
#
####################################################################################################################################################
?>
<tr>
<td colspan="1" align="left"><strong>Saldo Libros:</strong></td>
<td colspan="2" align="left"><?php echo number_format($saldo[0],2,'.',',')?></td>
<td colspan="1" border="1" align="left"><strong>Saldo extracto:</strong></td>
<td colspan="2" border="1" align="left"><?php echo number_format($saldoE[0],2,'.',','); ?> </td>
</tr>
<?php     
####################################################################################################################################################
# Titulo de Giros sin Cobrar
#
####################################################################################################################################################
?>
<tr>
<td colspan="6" align="center"><strong>GIROS SIN COBRAR</strong></td>
</tr>
<?php     
####################################################################################################################################################
# Cabeza de tabla
#
####################################################################################################################################################
?> 
<tr>
<td><strong>FECHA</strong></td>
<td><strong>COMPROBANTE</strong></td>
<td><strong>NO DOC</strong></td>
<td><strong>DETALLE</strong></td>
<td><strong>TERCERO</strong></td>
<td><strong>VALOR</strong></td>
</tr>
<?php 
####################################################################################################################################################
# Variable de suma
#
####################################################################################################################################################
$sumaGiros = "";
$sumaG = 0;
####################################################################################################################################################
# Consulta de Giros sin cobrar
#
####################################################################################################################################################
$sqlG = "SELECT DISTINCT	dtc.id_unico,
                date_format(cnt.fecha,'%d/%m/%Y'),
                CONCAT(tpc.sigla,' - ',cnt.numero),
                mov.numero,
                dtc.descripcion,
                IF(CONCAT_WS(' ',tr.nombreuno,tr.nombredos,tr.apellidouno,tr.apellidodos) IS NULL 
                  OR CONCAT_WS(' ', tr.nombreuno, tr.nombredos, tr.apellidouno, tr.apellidodos) = '',
                (tr.razonsocial),
                CONCAT_WS(' ', tr.nombreuno, tr.nombredos, tr.apellidouno, tr.apellidodos  )) AS NOMBRE,
					IF(dtc.valor>0,dtc.valor*-1,dtc.valor)
		FROM		gf_detalle_comprobante dtc 
		LEFT JOIN	gf_comprobante_cnt cnt 			ON dtc.comprobante 		= cnt.id_unico
		LEFT JOIN 	gf_tipo_comprobante tpc 		ON cnt.tipocomprobante	= tpc.id_unico
		LEFT JOIN 	gf_tercero tr 					ON dtc.tercero 			= tr.id_unico
		LEFT JOIN  	gf_detalle_comprobante_mov mov 	ON mov.comprobantecnt 	= dtc.id_unico
		WHERE 		dtc.valor<0
		AND 		dtc.conciliado IS NULL
		AND 		cnt.fecha <= '$fechaF' 
		AND 		tpc.clasecontable != 5 
		AND 	    dtc.cuenta IN ($cuentas)
		";
$resultG = $mysqli->query($sqlG);
####################################################################################################################################################
# Impresión de valores de consulta
#
####################################################################################################################################################
while ($rowG = mysqli_fetch_row($resultG)) { ?>
    
	<tr>
	<td><?php echo $rowG[1]; ?></td>
	<td><?php echo $rowG[2]; ?></td>
	<td><?php echo $rowG[3]; ?></td>
        <td><?php echo (ucwords(mb_strtolower($rowG[4]))); ?></td>";
	<td><?php echo (ucwords(mb_strtolower($rowG[5]))); ?></td>
	<td><?php echo number_format($rowG[6]<0?$rowG[6]*-1:$rowG[6],2,'.',','); ?></td>
	</tr>
    <?php 
	################################################################################################################################################
	# Impresión de valor total
	#
	################################################################################################################################################
	$sumaGiros += $rowG[6];
	$sumaG += $rowG[6];
}
####################################################################################################################################################
# Consulta de Giros sin cobrar conciliados en otros periodos
#
####################################################################################################################################################
 $sqlG = "SELECT DISTINCT	dtc.id_unico,
                date_format(cnt.fecha,'%d/%m/%Y'),
                CONCAT(tpc.sigla,' - ',cnt.numero),
                mov.numero,
                dtc.descripcion,
                IF(CONCAT_WS(' ',tr.nombreuno,tr.nombredos,tr.apellidouno,tr.apellidodos) IS NULL 
                  OR CONCAT_WS(' ', tr.nombreuno, tr.nombredos, tr.apellidouno, tr.apellidodos) = '',
                (tr.razonsocial),
                CONCAT_WS(' ', tr.nombreuno, tr.nombredos, tr.apellidouno, tr.apellidodos  )) AS NOMBRE,
					IF(dtc.valor>0,dtc.valor*-1,dtc.valor)
		FROM		gf_detalle_comprobante dtc 
		LEFT JOIN	gf_comprobante_cnt cnt 			ON dtc.comprobante 		= cnt.id_unico
		LEFT JOIN 	gf_tipo_comprobante tpc 		ON cnt.tipocomprobante	= tpc.id_unico
		LEFT JOIN 	gf_tercero tr 					ON dtc.tercero 			= tr.id_unico
		LEFT JOIN  	gf_detalle_comprobante_mov mov 	ON mov.comprobantecnt 	= dtc.id_unico
		WHERE 		dtc.valor<0
		AND 		dtc.conciliado IS NOT NULL
		AND 		cnt.fecha <=  '$fechaF' 
                AND             dtc.periodo_conciliado > $mess 
		AND 		tpc.clasecontable != 5 
		AND 	    dtc.cuenta IN ($cuentas)
		";
$resultG = $mysqli->query($sqlG);
####################################################################################################################################################
# Impresión de valores de consulta
#
####################################################################################################################################################
while ($rowG = mysqli_fetch_row($resultG)) { ?>
        <tr>
	<td><?php echo $rowG[1]; ?></td>
	<td><?php echo $rowG[2]; ?></td>
	<td><?php echo $rowG[3]; ?></td>
        <td><?php echo (ucwords(mb_strtolower($rowG[4]))); ?></td>";
	<td><?php echo (ucwords(mb_strtolower($rowG[5]))); ?></td>
	<td><?php echo number_format($rowG[6]<0?$rowG[6]*-1:$rowG[6],2,'.',','); ?></td>
	</tr>
	<?php 
	################################################################################################################################################
	# Impresión de valor total
	#
	################################################################################################################################################
	$sumaGiros += $rowG[6];
	$sumaG += $rowG[6];
}
####################################################################################################################################################
# Impresión de valores totales
#
####################################################################################################################################################
?>
<tr>
<td colspan="5" align="right"><strong>Totales:</strong></td>
<td><?php echo number_format($sumaG<0?$sumaG*-1:$sumaG,2,'.',',')?></td>
</tr>
<?php     
####################################################################################################################################################
# Titulo de Ingresos sin cobrar
#
####################################################################################################################################################
?>
<tr>
<td colspan="6" align="center"><strong>INGRESOS SIN COBRAR</strong></td>
</tr>
<?php     
####################################################################################################################################################
# Cabeza de tabla
#
####################################################################################################################################################
?>
    
<tr>
<td><strong>FECHA</strong></td>
<td><strong>COMPROBANTE</strong></td>
<td><strong>NO DOC</strong></td>
<td><strong>DETALLE</strong></td>
<td><strong>TERCERO</strong></td>
<td><strong>VALOR</strong></td>
</tr>
<?php 
####################################################################################################################################################
# Variable de suma
#
####################################################################################################################################################
$sumaIngresos = "";
$sumaI = 0;
####################################################################################################################################################
# Consulta de ingresos por cobrar
#
####################################################################################################################################################
$sqlI = "SELECT DISTINCT	dtc.id_unico,
                date_format(cnt.fecha,'%d/%m/%Y'),
                CONCAT(tpc.sigla,' - ',cnt.numero),
                mov.numero,
                dtc.descripcion,
                IF(CONCAT_WS(' ',tr.nombreuno,tr.nombredos,tr.apellidouno,tr.apellidodos) IS NULL 
                  OR CONCAT_WS(' ', tr.nombreuno, tr.nombredos, tr.apellidouno, tr.apellidodos) = '',
                (tr.razonsocial),
                CONCAT_WS(' ', tr.nombreuno, tr.nombredos, tr.apellidouno, tr.apellidodos  )) AS NOMBRE,
					dtc.valor
		FROM		gf_detalle_comprobante dtc 
		LEFT JOIN	gf_comprobante_cnt cnt 			ON dtc.comprobante 		= cnt.id_unico
		LEFT JOIN 	gf_tipo_comprobante tpc 		ON cnt.tipocomprobante	= tpc.id_unico
		LEFT JOIN 	gf_tercero tr 					ON dtc.tercero 			= tr.id_unico
		LEFT JOIN  	gf_detalle_comprobante_mov mov 	ON mov.comprobantecnt 	= dtc.id_unico
		WHERE 		dtc.valor>0
		AND 		dtc.conciliado IS NULL
		AND 		cnt.fecha <= '$fechaF' 
		AND 	    dtc.cuenta IN ($cuentas)
		AND 		tpc.clasecontable != 5 
		";
$resultI = $mysqli->query($sqlI);
####################################################################################################################################################
# Impresión de valores
#
####################################################################################################################################################
while ($rowI = mysqli_fetch_row($resultI)) { ?>
	<tr>
	<td><?php echo $rowI[1]; ?></td>
	<td><?php echo $rowI[2]; ?></td>
	<td><?php echo $rowI[3]; ?></td>
        <td><?php echo (ucwords(mb_strtolower($rowI[4]))); ?></td>
	<td><?php echo (ucwords(mb_strtolower($rowI[5]))); ?></td>
	<td><?php echo number_format($rowI[6]<0?$rowI[6]*-1:$rowI[6],2,'.',','); ?></td>
	</tr>
        <?php 
	################################################################################################################################################
	# Impresión de valor total
	#
	################################################################################################################################################
	$sumaIngresos += $rowI[6];
	$sumaI += $rowI[6];
}
####################################################################################################################################################
# Consulta de ingresos sin cobrar de que fueron conciliados en otros periodos
#
####################################################################################################################################################
$sqlI = "SELECT DISTINCT	dtc.id_unico,
                date_format(cnt.fecha,'%d/%m/%Y'),
                CONCAT(tpc.sigla,' - ',cnt.numero),
                mov.numero,
                dtc.descripcion,
                IF(CONCAT_WS(' ',tr.nombreuno,tr.nombredos,tr.apellidouno,tr.apellidodos) IS NULL 
                  OR CONCAT_WS(' ', tr.nombreuno, tr.nombredos, tr.apellidouno, tr.apellidodos) = '',
                (tr.razonsocial),
                CONCAT_WS(' ', tr.nombreuno, tr.nombredos, tr.apellidouno, tr.apellidodos  )) AS NOMBRE,
					dtc.valor
		FROM		gf_detalle_comprobante dtc 
		LEFT JOIN	gf_comprobante_cnt cnt 			ON dtc.comprobante 		= cnt.id_unico
		LEFT JOIN 	gf_tipo_comprobante tpc 		ON cnt.tipocomprobante	= tpc.id_unico
		LEFT JOIN 	gf_tercero tr 					ON dtc.tercero 			= tr.id_unico
		LEFT JOIN  	gf_detalle_comprobante_mov mov 	ON mov.comprobantecnt 	= dtc.id_unico
		WHERE 		dtc.valor>0
		AND 		dtc.conciliado IS NOT NULL
		AND 		cnt.fecha <= '$fechaF' 
                AND             dtc.periodo_conciliado > $mess 
		AND 	        dtc.cuenta IN ($cuentas)
		AND 		tpc.clasecontable != 5 
		";
$resultI = $mysqli->query($sqlI);
####################################################################################################################################################
# Impresión de valores
#
####################################################################################################################################################
while ($rowI = mysqli_fetch_row($resultI)) {?>
        <tr>
	<td><?php echo $rowI[1]; ?></td>
	<td><?php echo $rowI[2]; ?></td>
	<td><?php echo $rowI[3]; ?></td>
	<td><?php echo (ucwords(mb_strtolower($rowI[4]))); ?></td>
	<td><?php echo (ucwords(mb_strtolower($rowI[5]))); ?></td>
	<td><?php echo number_format($rowI[6]<0?$rowI[6]*-1:$rowI[6],2,'.',','); ?></td>
	</tr>
	<?php 
	################################################################################################################################################
	# Impresión de valor total
	#
	################################################################################################################################################
	$sumaIngresos += $rowI[6];
	$sumaI += $rowI[6];
}
####################################################################################################################################################
# Impresión de valores totales
#
####################################################################################################################################################
?>
<tr>
<td colspan="5" align="right"><strong>Totales:</strong></td>
<td><?php echo number_format($sumaI<0?$sumaI*-1:$sumaI,2,'.',','); ?></td>
</tr>
<?php     
####################################################################################################################################################
# Titulo de movimientos de otros periodos
#
####################################################################################################################################################
?>


<tr>
<td colspan="6" align="center"><strong>MOVIMIENTOS DE OTROS PERIODOS</strong></td>
</tr>    
<?php 
####################################################################################################################################################
# Cabeza de tabla
#
####################################################################################################################################################
?>
<tr>
<td><strong>FECHA</strong></td>
<td><strong>COMPROBANTE</strong></td>
<td><strong>NO DOC</strong></td>
<td><strong>DETALLE</strong></td>
<td><strong>TERCERO</strong></td>
<td><strong>VALOR</strong></td>
</tr>  
<?php 
####################################################################################################################################################
# Variable de suma
#
####################################################################################################################################################
$sumaMovimientos = "";
$sumaM = 0;
####################################################################################################################################################
# Consulta de movimientos de otros periodos
#
####################################################################################################################################################
$sqlM = "SELECT DISTINCT	dtc.id_unico,
                    date_format(cnt.fecha,'%d/%m/%Y'),
                    CONCAT(tpc.sigla,' - ',cnt.numero),
                    mov.numero,
                    dtc.descripcion,
                    IF(CONCAT_WS(' ',tr.nombreuno,tr.nombredos,tr.apellidouno,tr.apellidodos) IS NULL 
                  OR CONCAT_WS(' ', tr.nombreuno, tr.nombredos, tr.apellidouno, tr.apellidodos) = '',
                (tr.razonsocial),
                CONCAT_WS(' ', tr.nombreuno, tr.nombredos, tr.apellidouno, tr.apellidodos  )) AS NOMBRE, 
					dtc.valor
		FROM		gf_detalle_comprobante dtc 
		LEFT JOIN	gf_comprobante_cnt cnt 			ON dtc.comprobante 		= cnt.id_unico
		LEFT JOIN 	gf_tipo_comprobante tpc 		ON cnt.tipocomprobante	= tpc.id_unico
		LEFT JOIN 	gf_tercero tr 				ON dtc.tercero 		= tr.id_unico
		LEFT JOIN  	gf_detalle_comprobante_mov mov 	ON mov.comprobantecnt 	= dtc.id_unico
		WHERE 		dtc.conciliado IS NOT NULL
		AND 		cnt.fecha > '$fechaF' 
                AND             dtc.periodo_conciliado = $mess 
		AND             dtc.cuenta IN ($cuentas)
		AND 		tpc.clasecontable != 5 ";
$resultM = $mysqli->query($sqlM);
####################################################################################################################################################
# Impresión de valores
#
####################################################################################################################################################
while ($rowM = mysqli_fetch_row($resultM)) { ?>
    
	<tr>
	<td><?php echo $rowM[1]; ?></td>
	<td><?php echo $rowM[2]; ?></td>
	<td><?php echo $rowM[3]; ?></td>
        <td><?php echo (ucwords(mb_strtolower($rowM[4]))); ?></td>
	<td><?php echo (ucwords(mb_strtolower($rowM[5]))); ?></td>
	<td><?php echo number_format($rowM[6]<0?$rowM[6]*-1:$rowM[6],2,'.',','); ?></td>
	</tr>
    <?php
	################################################################################################################################################
	# Impresión de valor total
	#
	################################################################################################################################################
	$sumaMovimientos += $rowM[6];
	$sumaM += $rowM[6];
}
####################################################################################################################################################
# Impresión de valores totales
#
####################################################################################################################################################
?>
<tr>
<td colspan="5" align="right"><strong>Totales:</strong></td>
<td><?php number_format($sumaM,2,'.',','); ?></td>
</tr>
<?php 
##########################################################################################################################################
# Titulo de cabeza
##########################################################################################################################################
?>
<tr>
<td colspan="6" align="center"><strong>PARTIDAS CONCILIATORIAS SALDOS INICIALES</strong></td>
</tr> 
<?php     
##########################################################################################################################################
# Cabeza de tabla
##########################################################################################################################################
?>
<tr>
<td><strong>FECHA</strong></td>
<td><strong>TIPO PARTIDA</strong></td>
<td><strong>TIPO DOC</strong></td>
<td><strong>NO DOC</strong></td>
<td><strong>DETALLE</strong></td>
<td><strong>VALOR</strong></td>
</tr> 
<?php 
##########################################################################################################################################
# Variable de movimientos
#
##########################################################################################################################################
$sumaPartidasSI = "";
$sumaSI = "";
##########################################################################################################################################
# Captura de id partida
#
##########################################################################################################################################
$partida = $_GET['partida'];
##########################################################################################################################################
# Consulta de partida conciliatoria id
#
##########################################################################################################################################
$sqlP = "SELECT		id_unico
		FROM 		gf_partida_conciliatoria 
		WHERE 		md5(id_unico) = '$partida'";
$resultP = $mysqli->query($sqlP);
$rowPP = mysqli_fetch_row($resultP);
##########################################################################################################################################
# Asignación de variables
#
##########################################################################################################################################
$idPartida = $rowPP[0];
##########################################################################################################################################
# CONSULTA PARTIDAS CONCILIATORIAS SALDOS INICIALES
#
##########################################################################################################################################
#FECHA <01/01
$fechaMenor =$annoMes.'-01-01';

$part = $_GET['partida'];
 $sqlPCA = "SELECT DISTINCT
    DATE_FORMAT(dtp.fecha_partida, '%d/%m/%Y'),
    tpp.nombre,
    tpd.nombre,
    dtp.numero_documento,
    dtp.descripcion_detalle_partida,
    dtp.valor,
    dtp.tipo_partida,
    dtp.fecha_conciliacion,
    dtp.conciliado
FROM
    gf_partida_conciliatoria pc
LEFT JOIN
    gf_detalle_partida dtp
ON
    dtp.id_partida = pc.id_unico
LEFT JOIN
    gf_tipo_partida tpp
ON
    dtp.tipo_partida = tpp.id_unico
LEFT JOIN
    gf_tipo_documento tpd
ON
    dtp.tipo_documento = tpd.id_unico
WHERE
    (
        dtp.conciliado IS NULL OR dtp.conciliado IN(0, 2) 
        AND dtp.fecha_partida < '$fechaI' 
        AND pc.id_cuenta IN ($cuentas) 
        AND dtp.fecha_partida < '$fechaMenor'
        
    ) OR(
        dtp.conciliado = 1 
        AND dtp.fecha_conciliacion > '$fechaF' 
        AND pc.id_cuenta IN ($cuentas)  
        AND dtp.fecha_partida < '$fechaMenor'  
    ) AND pc.id_cuenta IN ($cuentas)  AND dtp.valor IS NOT NULL";


$resultPCA = $mysqli->query($sqlPCA);
$sumaPartidasSI =0;
$sumaSI =0;
##########################################################################################################################################
# Impresión de valores
#
##########################################################################################################################################
while ($rowPCA = mysqli_fetch_row($resultPCA)) { ?>
	<tr>
	<td><?php echo $rowPCA[0]; ?></td>
        <td><?php echo (ucwords(mb_strtolower($rowPCA[1]))); ?></td>
        <td><?php echo (ucwords(mb_strtolower($rowPCA[2]))); ?></td>
	<td><?php echo (ucwords(mb_strtolower($rowPCA[3]))); ?></td>
	<td><?php echo (ucwords(mb_strtolower($rowPCA[4]))); ?></td>
	<td><?php echo number_format($rowPCA[5],2,'.',','); ?></td>
	</tr>
        <?php 
	if($rowPCA[6]==1){
		$sumaPartidasSI += $rowPCA[5];
		$sumaSI += $rowPCA[5];
	}else{
		$sumaPartidasSI += $rowPCA[5] *-1;
		$sumaSI += $rowPCA[5] *-1;
	}	
	
		
}
##########################################################################################################################################
# Impresión de valor total
#
##########################################################################################################################################
?>
<tr>
<td colspan="5" align="right"><strong>Totales:</strong></td>
<td><?php echo number_format($sumaSI,2,'.',','); ?></td>
</tr>
<?php     
##########################################################################################################################################


####################################################################################################################################################
# PARTIDAS CONCILIATORIAS PERIODOS ANTERIORES
#
####################################################################################################################################################
?>
<tr>
<td colspan="6" align="center"><strong>PARTIDAS CONCILIATORIAS PERIODOS ANTERIORES</strong></td>
</tr>  
<?php     
####################################################################################################################################################
# Cabeza de tabla
#
####################################################################################################################################################
?>
<tr>
<td><strong>FECHA</strong></td>
<td><strong>TIPO PARTIDA</strong></td>
<td><strong>TIPO DOC</strong></td>
<td><strong>NO DOC</strong></td>
<td><strong>DETALLE</strong></td>
<td><strong>VALOR</strong></td>
</tr> 
<?php 
$part = $_GET['partida'];
####################################################################################################################################################
# Variable de suma
#
####################################################################################################################################################
$sumaPartidasA = "";
$sumaPA = 0;
####################################################################################################################################################
# Consulta de partidas conciliatorias
#
####################################################################################################################################################
$sqlPCA = "SELECT DISTINCT
    DATE_FORMAT(dtp.fecha_partida, '%d/%m/%Y'),
    tpp.nombre,
    tpd.nombre,
    dtp.numero_documento,
    dtp.descripcion_detalle_partida,
    dtp.valor,
    dtp.tipo_partida,
    dtp.fecha_conciliacion,
    dtp.conciliado
FROM
    gf_partida_conciliatoria pc
LEFT JOIN
    gf_detalle_partida dtp
ON
    dtp.id_partida = pc.id_unico
LEFT JOIN
    gf_tipo_partida tpp
ON
    dtp.tipo_partida = tpp.id_unico
LEFT JOIN
    gf_tipo_documento tpd
ON
    dtp.tipo_documento = tpd.id_unico
WHERE
    (
        dtp.conciliado IS NULL OR dtp.conciliado IN(0, 2) 
        AND pc.id_cuenta IN ($cuentas) 
        AND dtp.valor IS NOT NULL
        AND (dtp.fecha_partida < '$fechaI' and dtp.fecha_partida > '$fechaMenor') 
    ) OR(
        dtp.conciliado = 1 
        AND dtp.fecha_conciliacion > '$fechaF' 
        AND pc.id_cuenta IN ($cuentas)  AND dtp.valor IS NOT NULL 
        AND( dtp.fecha_partida > '$fechaMenor' and dtp.fecha_partida < '$fechaI' )
    ) AND pc.id_cuenta IN ($cuentas)   AND dtp.valor IS NOT NULL";

$resultPCA = $mysqli->query($sqlPCA);
####################################################################################################################################################
# Impresión de valores
#
####################################################################################################################################################
while ($rowPCA = mysqli_fetch_row($resultPCA)) { ?>
	<tr>
	<td><?php echo $rowPCA[0]; ?></td>
        <td><?php echo (ucwords(mb_strtolower($rowPCA[1]))); ?></td>
        <td><?php echo (ucwords(mb_strtolower($rowPCA[2]))); ?></td>
	<td><?php echo (ucwords(mb_strtolower($rowPCA[3]))); ?></td>
	<td><?php echo (ucwords(mb_strtolower($rowPCA[4]))); ?></td>
	<td><?php echo number_format($rowPCA[5],2,'.',','); ?></td>
	</tr>
        <?php 
	################################################################################################################################################
	# Validación de valores +,-
	#
	################################################################################################################################################
	if($rowPCA[6]==1){
		$sumaPartidasA += $rowPCA[5];
		$sumaPA += $rowPCA[5];
	}else{
		$sumaPartidasA += $rowPCA[5] *-1;
		$sumaPA += $rowPCA[5] *-1;
	}
		
}
####################################################################################################################################################
# Impresión de valores totales
#
####################################################################################################################################################
?>
<tr>
<td colspan="5" align="right"><strong>Totales:</strong></td>
<td><?php echo number_format($sumaPA,2,'.',','); ?></td>
</tr>
<?php 
####################################################################################################################################################
# Titulo de partidas conciliatorias peridos an teriores
#
####################################################################################################################################################
?>
<tr>
<td colspan="6" align="center"><strong>PARTIDAS CONCILIATORIAS DEL PERIODO</strong></td>
</tr>  
<?php    
####################################################################################################################################################
# Cabeza de tabla
#
####################################################################################################################################################
?>
<tr>
<td><strong>FECHA</strong></td>
<td><strong>TIPO PARTIDA</strong></td>
<td><strong>TIPO DOC</strong></td>
<td><strong>NO DOC</strong></td>
<td><strong>DETALLE</strong></td>
<td><strong>VALOR</strong></td>
</tr>
    
<?php 
####################################################################################################################################################
# Variable de suma
#
####################################################################################################################################################
$sumaPartidas = "";
$sumaP = 0;
####################################################################################################################################################
# Consulta de partidas conciliatorias
#
####################################################################################################################################################
$sqlPC = "SELECT	date_format(dtp.fecha_partida,'%d/%m/%Y'),
					tpp.nombre,
					tpd.nombre,
					dtp.numero_documento,
					dtp.descripcion_detalle_partida,
					dtp.valor,
					dtp.tipo_partida
		FROM 		gf_partida_conciliatoria pc
		LEFT JOIN 	gf_detalle_partida dtp 			ON dtp.id_partida 		= pc.id_unico
		LEFT JOIN 	gf_tipo_partida tpp 			ON dtp.tipo_partida 	= tpp.id_unico
		LEFT JOIN 	gf_tipo_documento tpd 			ON dtp.tipo_documento 	= tpd.id_unico
		WHERE 		dtp.fecha_partida BETWEEN '$fechaI' and '$fechaF' 
		AND 		pc.id_cuenta 	IN ($cuentas)
		AND 		dtp.conciliado 	!= 1";
$resultPC = $mysqli->query($sqlPC);
####################################################################################################################################################
# Impresión de valores
#
####################################################################################################################################################
while ($rowPC = mysqli_fetch_row($resultPC)) { ?>
    
	<tr>
	<td><?php echo $rowPC[0]; ?></td>
	<td><?php echo (ucwords(mb_strtolower($rowPC[1]))); ?></td>
	<td><?php echo (ucwords(mb_strtolower($rowPC[2]))); ?></td>
        <td><?php echo (ucwords(mb_strtolower($rowPC[3]))); ?></td>
	<td><?php echo (ucwords(mb_strtolower($rowPC[4]))); ?></td>
	<td><?php echo number_format($rowPC[5]<0?$rowPC[5]*-1:$rowPC[5],2,'.',','); ?></td>
	</tr>
    <?php 
	################################################################################################################################################
	# Validación de valores +,-
	#
	################################################################################################################################################
	if($rowPC[6]==1){
		$sumaPartidas += $rowPC[5];
		$sumaPA += $rowPC[5];
	}else{
		$sumaPartidas += $rowPC[5] *-1;
		$sumaP += $rowPC[5] *-1;
	}	
}

$sqlPC = "SELECT	date_format(dtp.fecha_partida,'%d/%m/%Y'),
					tpp.nombre,
					tpd.nombre,
					dtp.numero_documento,
					dtp.descripcion_detalle_partida,
					dtp.valor,
					dtp.tipo_partida
		FROM 		gf_partida_conciliatoria pc
		LEFT JOIN 	gf_detalle_partida dtp 			ON dtp.id_partida 		= pc.id_unico
		LEFT JOIN 	gf_tipo_partida tpp 			ON dtp.tipo_partida 	= tpp.id_unico
		LEFT JOIN 	gf_tipo_documento tpd 			ON dtp.tipo_documento 	= tpd.id_unico
		WHERE 		dtp.fecha_partida BETWEEN '$fechaI' and '$fechaF' 
		AND 		pc.id_cuenta 	IN ($cuentas)
		AND 		dtp.conciliado 	= 1 AND dtp.fecha_conciliacion BETWEEN '$fechaI' and '$fechaF' ";
$resultPC = $mysqli->query($sqlPC);
##########################################################################################################################################
# Impresión de valores
#
##########################################################################################################################################
while ($rowPC = mysqli_fetch_row($resultPC)) { ?>
	<tr>
	<td><?php echo $rowPC[0]; ?></td>
	<td><?php echo (ucwords(mb_strtolower($rowPC[1]))); ?></td>
	<td><?php echo (ucwords(mb_strtolower($rowPC[2]))); ?></td>
        <td><?php echo (ucwords(mb_strtolower($rowPC[3]))); ?></td>
	<td><?php echo (ucwords(mb_strtolower($rowPC[4]))); ?></td>
	<td><?php echo number_format($rowPC[5]<0?$rowPC[5]*-1:$rowPC[5],2,'.',','); ?></td>
	</tr>
    <?php 
	if($rowPC[6]==1){
		$sumaPartidas += $rowPC[5];
		$sumaP += $rowPC[5];
	}else{
		$sumaPartidas += $rowPC[5] *-1;
		$sumaP += $rowPC[5] *-1;
	}	
	$pdf->Ln(5);
}




####################################################################################################################################################
# Impresión de valores totales
#
####################################################################################################################################################
?>
<tr>
<td colspan="5" align="right"><strong>Totales:</strong></td>
<td><?php echo number_format($sumaP,2,'.',','); ?></td>
</tr>
<tr height="30"></tr>
<?php 
####################################################################################################################################################
# Suma de saldo conciliado
#
####################################################################################################################################################
$saldoConciliado = ($saldoLibro + $sumaMovimientos + $sumaPartidas + $sumaPartidasA + $sumaPartidasSI) - $saldoExtracto - ($sumaIngresos + $sumaGiros);
####################################################################################################################################################
# Impresión valor de conciliación
#
####################################################################################################################################################
?>
<tr>
<td colspan="5" align="right"><strong>Saldo Conciliado:</strong></td>
<td colspan="1" align="right"><?php echo number_format($saldoConciliado,2,',','.'); ?></td>
</tr>
<?php 
####################################################################################################################################################
# Impresión Final de html
#
####################################################################################################################################################
?>
</table>
</body>
</html>