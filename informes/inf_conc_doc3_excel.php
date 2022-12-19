<?php 
######################################################################################################
# ***************************************** Modificaciones ***************************************** #
######################################################################################################
#08/02/2017 | Erica G. | Cuentas y saldos Vigencias Anteriores
#16/11/2017 |Erica G. | ARCHIVO CREADO SOLO FOMVIDU
#######################################################################################################
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Informe_Conciliacion_Bancaria.xls");
require_once("../Conexion/conexion.php");
session_start();
ini_set('max_execution_time', 0);
require_once('../Conexion/ConexionPDO.php');
require_once('../jsonPptal/funcionesPptal.php');
$con = new ConexionPDO();
##########################################################################################################################################
# Captura de variables
##########################################################################################################################################
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
# Captura de variable compañia
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
    <?php echo $nombreIdent.":".$numeroIdent."<br/>".$direccinTer."Tel:".$telefonoTer; ?> </strong></td>
</tr>
<?php
##########################################################################################################################################
# Consulta de Banco
##########################################################################################################################################
$sqlBanco = "SELECT 	cta.id_unico,
						CONCAT(cta.codi_cuenta,' ',UPPER(cta.nombre))
			FROM		gf_cuenta cta
			WHERE 		md5(cta.id_unico) 	= '$cuenta'";
$resultBanco = $mysqli->query($sqlBanco);
$banco = mysqli_fetch_row($resultBanco);

##########################################################################################################################################
# Consulta de periodo,mes
#########################################################################################################################################
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
#########################################################################################################################################
$idMes = $rowMes[0];
$nomMes = $rowMes[1];
$annoMes = $rowMes[2];
$numMes = $rowMes[3];
##########################################################################################################################################
# Consulta de nombre de mes
##########################################################################################################################################
$sqlM = "SELECT id_unico FROM gf_mes WHERE id_unico = $idMes";
$resultM = $mysqli->query($sqlM);
$noM = mysqli_fetch_row($resultM);
##########################################################################################################################################
# Array con los numeros de los meses
##########################################################################################################################################
$meses = array( "Enero" => '01', "Febrero" => '02', "Marzo" => '03',"Abril" => '04', "Mayo" => '05', "Junio" => '06', 
                "Julio" => '07', "Agosto" => '08', "Septiembre" => '09', "Octubre" => '10', "Noviembre" => '11', "Diciembre" => '12'); 
$mess = $noM[0];
$calendario = CAL_GREGORIAN;
$diaF = cal_days_in_month($calendario, $numMes, $annoMes); 
$fechaF= $annoMes.'/'.$numMes.'/'.$diaF;
$fechaI =$annoMes.'/'.$numMes.'/'.'01';
##fecha formato 
##ULTIMO DIA DEL MES
$calendario = CAL_GREGORIAN;
$diaF = cal_days_in_month($calendario, $numMes, $annoMes); 
$fechaC = $diaF.'/'.$numMes.'/'.$annoMes;
##########################################################################################################################################
echo '
    <tr>
        <td colspan="3"><strong>CONCILACIÓN BANCARIA CUENTA</strong></td>
        <td colspan="3"><strong>'.$banco[1].'</strong></td>
    </tr>
    <tr>
        <td colspan="3"><strong>Mes </td>
        <td colspan="3"><strong>'.$nomMes.PHP_EOL.'de'.PHP_EOL.$annoMes.'</strong></td>
    </tr>
    <tr><td colspan="6"><br/><br/></td></tr>
    ';

##########################################################################################################################################
# Saldo en libros
##########################################################################################################################################

##########################################################################################################################################
# Consulta saldo en libros
##########################################################################################################################################
$sqlS = "SELECT 	SUM(dtc.valor)
		FROM		gf_detalle_comprobante dtc 
		LEFT JOIN	gf_comprobante_cnt cnt 			ON dtc.comprobante 		= cnt.id_unico		
		WHERE 		cnt.fecha <= '$fechaF' 
		AND 	    dtc.cuenta = $banco[0]";
$resultS = $mysqli->query($sqlS);
$saldo = mysqli_fetch_row($resultS);
##########################################################################################################################################
# Captura de valor de saldo en libros
##########################################################################################################################################
$saldoLibro = $saldo[0];
##########################################################################################################################################
# Consulta saldo extracto
##########################################################################################################################################
$sqlSE = "SELECT saldo_extracto FROM gf_partida_conciliatoria WHERE md5(id_unico) = '".$_GET['partida']."'";
$resultE = $mysqli->query($sqlSE);
$saldoE = mysqli_fetch_row($resultE);
##########################################################################################################################################
# Captura de valor de saldo extracto
##########################################################################################################################################
$saldoExtracto = $saldoE[0];
$dif = $saldoExtracto -$saldo[0];
##########################################################################################################################################
# Impresión saldo en libros
##########################################################################################################################################
echo '<tr>
        <td colspan="3">SALDO SEGÚN EXTRACTO</td>
        <td colspan="3">$'.number_format($saldoExtracto,2,',','.').'</td>
     </tr>
     <tr>
        <td colspan="3">SALDO SEGÚN LIBRO</td>
        <td colspan="3">$'.number_format($saldo[0],2,',','.').'</td>
     </tr>
     <tr>
        <td colspan="3"><strong>DIFERENCIA</strong></td>
        <td colspan="3">$'.number_format($dif,2,',','.').'</td>
     </tr>';



# Titulo de cabeza
##########################################################################################################################################

##########################################################################################################################################
# Variable de suma
##########################################################################################################################################
$sumaGiros = "";
$sumaG = "";
##########################################################################################################################################
# Consulta de Giros sin cobrar
##########################################################################################################################################
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
		LEFT JOIN 	gf_tercero tr 					ON cnt.tercero 			= tr.id_unico
		LEFT JOIN  	gf_detalle_comprobante_mov mov 	ON mov.comprobantecnt 	= dtc.id_unico
		WHERE 		dtc.valor<0
		AND 		dtc.conciliado IS NULL
		AND 		cnt.fecha <= '$fechaF' 
		AND 		tpc.clasecontable != 5 
		AND 	    dtc.cuenta IN ($cuentas)
		";
$resultG = $mysqli->query($sqlG);

##########################################################################################################################################
# Consulta de Giros sin cobrar conciliados en otros periodos
##########################################################################################################################################
 $sqlG2 = "SELECT DISTINCT dtc.id_unico,
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
		LEFT JOIN 	gf_tercero tr 					ON cnt.tercero 			= tr.id_unico
		LEFT JOIN  	gf_detalle_comprobante_mov mov 	ON mov.comprobantecnt 	= dtc.id_unico
		WHERE 		dtc.valor<0
		AND 		dtc.conciliado IS NOT NULL
		AND 		cnt.fecha <= '$fechaF' 
                AND             dtc.periodo_conciliado > $mess 
		AND 		tpc.clasecontable != 5 
		AND 	    dtc.cuenta IN ($cuentas)
		";
$resultG2 = $mysqli->query($sqlG2);
##########################################################################################################################################
# Impresión de valores
##########################################################################################################################################
if(mysqli_num_rows($resultG)>0 || mysqli_num_rows($resultG2)>0){
    echo '<tr>
        <td colspan="6"><strong><br/>GIROS SIN COBRAR<br/></strong><br/></td>
     </tr>';
    echo '<tr>
            <td><strong>FECHA</strong></td>
            <td><strong>COMPROBANTE</strong></td>
            <td><strong>NO DOC</strong></td>
            <td><strong>DETALLE</strong></td>
            <td><strong>TERCERO</strong></td>
            <td><strong>VALOR</strong></td>
         </tr>';
    while ($rowG = mysqli_fetch_row($resultG)) {
        echo '<tr>
                <td>'.$rowG[1].'</td>
                <td>'.$rowG[2].'</td>
                <td>'.$rowG[3].'</td>
                <td>'.(ucwords(mb_strtolower($rowG[4]))).'</td>
                <td>'.(ucwords(mb_strtolower($rowG[5]))).'</td>
                <td>'.number_format($rowG[6]<0?$rowG[6]*-1:$rowG[6],2,'.',',').'</td>
             </tr>';
            $sumaGiros += $rowG[6];
            $sumaG += $rowG[6];
    }
    while ($rowG2 = mysqli_fetch_row($resultG2)) {
        echo '<tr>
                <td>'.$rowG2[1].'</td>
                <td>'.$rowG2[2].'</td>
                <td>'.$rowG2[3].'</td>
                <td>'.(ucwords(mb_strtolower($rowG2[4]))).'</td>
                <td>'.(ucwords(mb_strtolower($rowG2[5]))).'</td>
                <td>'.number_format($rowG2[6]<0?$rowG2[6]*-1:$rowG2[6],2,'.',',').'</td>
             </tr>';
	$sumaGiros += $rowG2[6];
	$sumaG += $rowG2[6];
    }
    ##########################################################################################################################################
    # Impresión de valor total
    ##########################################################################################################################################
    echo '<tr>
            <td colspan="5" align="right"><strong>TOTAL GIROS SIN COBRAR:</strong></td>
            <td><strong>'.number_format($sumaG<0?$sumaG*-1:$sumaG,2,'.',',').'</strong></td>
         </tr>';
    ##########################################################################################################################################
}


# Titulo de cabeza
##########################################################################################################################################

##########################################################################################################################################
# Variable de suma
#
##########################################################################################################################################
$sumaIngresos = "";
$sumaI = "";
##########################################################################################################################################
# Consulta de ingresos sin cobrar
##########################################################################################################################################
 $sqlI = "SELECT DISTINCT dtc.id_unico,
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
		LEFT JOIN 	gf_tercero tr 					ON cnt.tercero 			= tr.id_unico
		LEFT JOIN  	gf_detalle_comprobante_mov mov 	ON mov.comprobantecnt 	= dtc.id_unico
		WHERE 		dtc.valor>0
		AND 		dtc.conciliado IS NULL
		AND 		cnt.fecha <= '$fechaF' 
		AND 	    dtc.cuenta IN ($cuentas)
		AND 		tpc.clasecontable != 5 
		";
$resultI = $mysqli->query($sqlI);
##########################################################################################################################################
# Consulta de ingresos sin cobrar de que fueron conciliados en otros periodos
##########################################################################################################################################
 $sqlI2 = "SELECT DISTINCT dtc.id_unico,
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
		LEFT JOIN 	gf_tercero tr 					ON cnt.tercero 			= tr.id_unico
		LEFT JOIN  	gf_detalle_comprobante_mov mov 	ON mov.comprobantecnt 	= dtc.id_unico
		WHERE 		dtc.valor>0
		AND 		dtc.conciliado IS NOT NULL
		AND 		cnt.fecha <= '$fechaF' 
                AND             dtc.periodo_conciliado > $mess 
		AND 	        dtc.cuenta IN ($cuentas)
		AND 		tpc.clasecontable != 5 
		";
$resultI2 = $mysqli->query($sqlI2);
if(mysqli_num_rows($resultI2)>0 || mysqli_num_rows($resultI)>0){
    echo '<tr>
        <td colspan="6"><strong><br/>CONSIGNACIONES EN TRÁNSITO<br/></strong><br/></td>
     </tr>';
##########################################################################################################################################
# Cabeza de tabla
##########################################################################################################################################
echo '<tr>
            <td><strong>FECHA</strong></td>
            <td><strong>COMPROBANTE</strong></td>
            <td><strong>NO DOC</strong></td>
            <td><strong>DETALLE</strong></td>
            <td><strong>TERCERO</strong></td>
            <td><strong>VALOR</strong></td>
         </tr>';
while ($rowI = mysqli_fetch_row($resultI)) {
        echo '<tr>
                <td>'.$rowI[1].'</td>
                <td>'.$rowI[2].'</td>
                <td>'.$rowI[3].'</td>
                <td>'.(ucwords(mb_strtolower($rowI[4]))).'</td>
                <td>'.(ucwords(mb_strtolower($rowI[5]))).'</td>
                <td>'.number_format($rowI[6]<0?$rowI[6]*-1:$rowI[6],2,'.',',').'</td>
             </tr>';
	$sumaIngresos += $rowI[6];
	$sumaI += $rowI[6];
}

##########################################################################################################################################
# Impresión de valores
##########################################################################################################################################
while ($rowI2 = mysqli_fetch_row($resultI2)) {
    echo '<tr>
            <td>'.$rowI2[1].'</td>
            <td>'.$rowI2[2].'</td>
            <td>'.$rowI2[3].'</td>
            <td>'.(ucwords(mb_strtolower($rowI2[4]))).'</td>
            <td>'.(ucwords(mb_strtolower($rowI2[5]))).'</td>
            <td>'.number_format($rowI2[6]<0?$rowI2[6]*-1:$rowI2[6],2,'.',',').'</td>
         </tr>';
}
##########################################################################################################################################
# Impresión de valor total
##########################################################################################################################################
 echo '<tr>
        <td colspan="5" align="right"><strong>TOTAL CONSIGNACIONES EN TRÁNSITO:</strong></td>
        <td><strong>'.number_format($sumaI<0?$sumaI*-1:$sumaI,2,'.',',').'</strong></td>
     </tr>';
}




##########################################################################################################################################
# Cabeza de tabla
##########################################################################################################################################

##########################################################################################################################################
# Variable de movimientos
##########################################################################################################################################
$sumaMovimientos = "";
$sumaM = "";
##########################################################################################################################################
# Consulta de otros periodos
##########################################################################################################################################
$sqlM = "SELECT DISTINCT dtc.id_unico,
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
		LEFT JOIN 	gf_tercero tr 				ON cnt.tercero 		= tr.id_unico
		LEFT JOIN  	gf_detalle_comprobante_mov mov 	ON mov.comprobantecnt 	= dtc.id_unico
		WHERE 		dtc.conciliado IS NOT NULL
		AND 		cnt.fecha > '$fechaF' 
                AND             dtc.periodo_conciliado = $mess 
		AND             dtc.cuenta IN ($cuentas)
		AND 		tpc.clasecontable != 5 ";
$resultM = $mysqli->query($sqlM);
##########################################################################################################################################
# Impresión de valores
##########################################################################################################################################
if(mysqli_num_rows($resultM)>0){
    ##########################################################################################################################################
    # Titulo de cabeza
    ##########################################################################################################################################
    echo '<tr>
            <td colspan="6"><strong><br/>TRANSACCIONES CONCILIADAS Y REGISTRADAS CON FECHA POSTERIOR<br/></strong><br/></td>
         </tr>';
    echo '<tr>
            <td><strong>FECHA</strong></td>
            <td><strong>COMPROBANTE</strong></td>
            <td><strong>NO DOC</strong></td>
            <td><strong>DETALLE</strong></td>
            <td><strong>TERCERO</strong></td>
            <td><strong>VALOR</strong></td>
         </tr>';

while ($rowM = mysqli_fetch_row($resultM)) {
    echo '<tr>
            <td>'.$rowM[1].'</td>
            <td>'.$rowM[2].'</td>
            <td>'.$rowM[3].'</td>
            <td>'.(ucwords(mb_strtolower($rowM[4]))).'</td>
            <td>'.(ucwords(mb_strtolower($rowM[5]))).'</td>
            <td>'.number_format($rowM[6]<0?$rowM[6]*-1:$rowM[6],2,'.',',').'</td>
         </tr>';
	$sumaMovimientos += $rowM[6];
	$sumaM += $rowM[6];
}
##########################################################################################################################################
# Impresión de valor total
##########################################################################################################################################
 echo '<tr>
        <td colspan="5" align="right"><strong>TOTAL TRANSACCIONES CONCILIADAS Y REGISTRADAS CON FECHA POSTERIOR:</strong></td>
        <td><strong>'.number_format($sumaM,2,'.',',').'</strong></td>
     </tr>';
}


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
INNER JOIN
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
##########################################################################################################################################
# Impresión de valores
#
##########################################################################################################################################
IF(mysqli_num_rows($resultPCA)>0){
    ##########################################################################################################################################
    # Titulo de cabeza
    ##########################################################################################################################################
    echo '<tr>
            <td colspan="6"><br/><strong>PARTIDAS CONCILIATORIAS SALDOS INICIALES<br/></strong><br/></td>
         </tr>';
    ##########################################################################################################################################
    # Cabeza de tabla
    ##########################################################################################################################################
    echo '<tr>
            <td><strong>FECHA</strong></td>
            <td><strong>TIPO PARTIDA</strong></td>
            <td><strong>TIPO DOC</strong></td>
            <td><strong>NO DOC</strong></td>
            <td><strong>DETALLE</strong></td>
            <td><strong>VALOR</strong></td>
         </tr>';

while ($rowPCA = mysqli_fetch_row($resultPCA)) {
    echo '<tr>
            <td>'.$rowPCA[0].'</td>
            <td>'.$rowPCA[1].'</td>
            <td>'.$rowPCA[2].'</td>
            <td>'.(ucwords(mb_strtolower($rowPCA[3]))).'</td>
            <td>'.(ucwords(mb_strtolower($rowPCA[4]))).'</td>
            <td>'.number_format($rowPCA[5],2,'.',',').'</td>
         </tr>';
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
 echo '<tr>
        <td colspan="5" align="right"><strong>TOTAL PARTIDAS CONCILIATORIAS SALDOS INICIALES</strong></td>
        <td><strong>'.number_format($sumaSI,2,'.',',').'</strong></td>
     </tr>';
##########################################################################################################################################
}

##########################################################################################################################################
# Variable de movimientos
#
##########################################################################################################################################
$sumaPartidasA = "";
$sumaPA = "";
##########################################################################################################################################

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
INNER JOIN
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
        AND (dtp.fecha_partida < '$fechaI' and dtp.fecha_partida > '$fechaMenor') 
        
    ) OR(
        dtp.conciliado = 1 
        AND dtp.fecha_conciliacion > '$fechaF' 
        AND pc.id_cuenta IN ($cuentas)  
        AND( dtp.fecha_partida > '$fechaMenor' and dtp.fecha_partida < '$fechaI' )
    ) AND pc.id_cuenta IN ($cuentas)  AND dtp.valor IS NOT NULL";


$resultPCA = $mysqli->query($sqlPCA);
##########################################################################################################################################
# Impresión de valores
#
##########################################################################################################################################
IF(mysqli_num_rows($resultPCA)>0){
    ##########################################################################################################################################
    echo '<tr>
        <td colspan="6"><br/><strong>PARTIDAS CONCILIATORIAS PERIODOS ANTERIORES</strong><br/><br/></td>
     </tr>';
while ($rowPCA = mysqli_fetch_row($resultPCA)) {
    echo '<tr>
            <td>'.$rowPCA[0].'</td>
            <td>'.$rowPCA[1].'</td>
            <td>'.$rowPCA[2].'</td>
            <td>'.(ucwords(mb_strtolower($rowPCA[3]))).'</td>
            <td>'.(ucwords(mb_strtolower($rowPCA[4]))).'</td>
            <td>'.number_format($rowPCA[5],2,'.',',').'</td>
         </tr>';
	if($rowPCA[6]==1){
		$sumaPartidasA += $rowPCA[5];
		$sumaPA += $rowPCA[5];
	}else{
		$sumaPartidasA += $rowPCA[5] *-1;
		$sumaPA += $rowPCA[5] *-1;
	}
       
}


##########################################################################################################################################
# Impresión de valor total
#
##########################################################################################################################################
 echo '<tr>
        <td colspan="5" align="right"><strong>TOTAL PARTIDAS CONCILIATORIAS PERIODOS ANTERIORES</strong></td>
        <td><strong>'.number_format($sumaPA,2,'.',',').'</strong></td>
     </tr>';
}
#########################################################################################################################################
# CONSULTA PARTIDAS CONCILIATORIAS DEL PERIODO
#


##########################################################################################################################################
# Consulta de partidas conciliatorias
#
##########################################################################################################################################
$sumaPartidas=0;
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


$sqlPC2 = "SELECT	date_format(dtp.fecha_partida,'%d/%m/%Y'),
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
$resultPC2 = $mysqli->query($sqlPC2);
##########################################################################################################################################
# Impresión de valores
#
##########################################################################################################################################
if(mysqli_num_rows($resultPC)>0 || mysqli_num_rows($resultPC2)>0){
    ##########################################################################################################################################
    # Titulo de cabeza
    #
    ##########################################################################################################################################
    echo '<tr>
            <td colspan="6"><br/><strong>PARTIDAS CONCILIATORIAS DEL PERIODO</strong><br/>.</td>
         </tr>';
    ##########################################################################################################################################
# Cabeza de tabla
#
##########################################################################################################################################
echo '<tr>
            <td><strong>FECHA</strong></td>
            <td><strong>TIPO PARTIDA</strong></td>
            <td><strong>TIPO DOC</strong></td>
            <td><strong>NO DOC</strong></td>
            <td><strong>DETALLE</strong></td>
            <td><strong>VALOR</strong></td>
         </tr>';


##########################################################################################################################################
# Impresión de valores
#
##########################################################################################################################################
while ($rowPC2 = mysqli_fetch_row($resultPC2)) {
    echo '<tr>
            <td>'.$rowPC2[0].'</td>
            <td>'.$rowPC2[1].'</td>
            <td>'.$rowPC2[2].'</td>
            <td>'.(ucwords(mb_strtolower($rowPC2[3]))).'</td>
            <td>'.(ucwords(mb_strtolower($rowPC2[4]))).'</td>
            <td>'.number_format($rowPC2[5]<0?$rowPC2[5]*-1:$rowPC2[5],2,'.',',').'</td>
         </tr>';
	if($rowPC2[6]==1){
		$sumaPartidas += $rowPC2[5];
		$sumaP += $rowPC2[5];
	}else{
		$sumaPartidas += $rowPC2[5] *-1;
		$sumaP += $rowPC2[5] *-1;
	}	
	$pdf->Ln(5);
}
while ($rowPC = mysqli_fetch_row($resultPC)) {
      echo '<tr>
            <td>'.$rowPC[0].'</td>
            <td>'.$rowPC[1].'</td>
            <td>'.$rowPC[2].'</td>
            <td>'.(ucwords(mb_strtolower($rowPC[3]))).'</td>
            <td>'.(ucwords(mb_strtolower($rowPC[4]))).'</td>
            <td>'.number_format($rowPC[5]<0?$rowPC[5]*-1:$rowPC[5],2,'.',',').'</td>
         </tr>';
	if($rowPC[6]==1){
		$sumaPartidas += $rowPC[5];
		$sumaP += $rowPC[5];
	}else{
		$sumaPartidas += $rowPC[5] *-1;
		$sumaP += $rowPC[5] *-1;
	}	
	$pdf->Ln(5);
}

##########################################################################################################################################
# Impresión de valor total
#
##########################################################################################################################################
echo '<tr>
        <td colspan="5" align="right"><strong>TOTAL PARTIDAS CONCILIATORIAS DEL PERIODO</strong></td>
        <td><strong>'.number_format($sumaP,2,'.',',').'</strong></td>
     </tr>';
}

##########################################################################################################################################
# Saldo conciliado
#
##########################################################################################################################################
$saldoConciliado = ($saldoLibro + $sumaMovimientos + $sumaPartidas + $sumaPartidasA + $sumaPartidasSI) - $saldoExtracto - ($sumaIngresos + $sumaGiros);

##########################################################################################################################################
# Saldo extracto
##########################################################################################################################################
echo '<tr><td colspan="6"><br/><br/></td></tr>';
echo '<tr>
        <td colspan="2">SALDO SEGÚN EXTRACTO</td>
        <td colspan="2"></td>
        <td colspan="2">'.number_format($saldoE[0],2,',','.').'</td>
     </tr>';
if($sumaGiros!=0){
if($sumaGiros<0){$sumaGiros=$sumaGiros*-1;}else{$sumaGiros=$sumaGiros;}
echo '<tr>
        <td colspan="2">GIROS SIN COBRAR</td>
        <td colspan="2">'.number_format($sumaGiros,2,',','.').'</td>
        <td colspan="2"></td>
     </tr>';
}
echo '<tr>
        <td colspan="2">SALDO SEGÚN LIBRO</td>
        <td colspan="2">'.number_format($saldoLibro,2,',','.').'</td>
        <td colspan="2"></td>
     </tr>';
$uno =$sumaGiros+$saldoLibro;
$dos =$saldoExtracto+$sumaPartidasSI;
echo '<tr>
        <td colspan="2"><strong>SUMAS IGUALES</strong></td>
        <td colspan="2"><strong>'.number_format($uno,2,',','.').'</strong></td>
        <td colspan="2"><strong>'.number_format($dos,2,',','.').'</strong></td>    
     </tr>';


?>
</table>
</body>
</html>