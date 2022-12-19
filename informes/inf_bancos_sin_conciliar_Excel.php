<?php
##########################################################################################
# *********************************** Modificaciones *********************************** # 
##########################################################################################
#09/03/2018 |Erica G. |Saldos Vigencia Anterior
#11/05/2017 |ERICA G. |PARTIDAS CONCILIATORIAS SALDOS INICIALES
#10/05/2017 |ERICA G. |GIROS SIN COBRAR FECHA<=FECHAF // PARTIDAS PERIODOS ANTERIORES
##########################################################################################

header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Listado_Bancos_Sin_Conciliar.xls");
require_once("../Conexion/conexion.php");
require_once("../Conexion/ConexionPDO.php");
require_once('../jsonPptal/funcionesPptal.php');
$con = new ConexionPDO();
session_start();
ini_set('max_execution_time', 0);
##########RECEPCION VARIABLES###############
$mes = $_POST['mes'];
$annio = $_SESSION['anno'];

 $mesN ="SELECT m.mes, pa.anno , m.id_unico "
        . "FROM gf_mes m "
        . "LEFT JOIN gf_parametrizacion_anno pa "
        . "ON m.parametrizacionanno = pa.id_unico "
        . "WHERE m.parametrizacionanno = '$annio' "
        . "AND m.numero = '$mes'";
$mesN = $mysqli->query($mesN);
$mesN = mysqli_fetch_row($mesN);
$mesNomn = $mesN[0];
$annoP = $mesN[1];
$mesId= $mesN[2];

$calendario = CAL_GREGORIAN;
$diaF = cal_days_in_month($calendario, $mes, $annoP); 
$fechaF= $annoP.'/'.$mes.'/'.$diaF;
$fechaI = $annoP.'/'.$mes.'/01';

##CONSULTA DATOS COMPAÑIA##
$compa=$_SESSION['compania'];
$comp="SELECT t.razonsocial, t.numeroidentificacion, t.digitoverficacion, t.ruta_logo "
        . "FROM gf_tercero t WHERE id_unico=$compa";
$comp = $mysqli->query($comp);
$comp = mysqli_fetch_row($comp);
$nombreCompania = $comp[0];
if(empty($comp[2])) {
    $nitcompania = $comp[1];
} else {
    $nitcompania = $comp[1].' - '.$comp[2];
}
$ruta = $comp[3];
$usuario = $_SESSION['usuario'];
#***********************Datos Compañia***********************#
$compania = $_SESSION['compania'];
$rowC = $con->Listar("SELECT 
            ter.id_unico,
            ter.razonsocial,
            UPPER(ti.nombre),
            IF(ter.digitoverficacion IS NULL OR ter.digitoverficacion='',
                ter.numeroidentificacion, 
                CONCAT(ter.numeroidentificacion, ' - ', ter.digitoverficacion)),
            dir.direccion,
            tel.valor,
            ter.ruta_logo 
        FROM            
            gf_tercero ter
        LEFT JOIN 	
            gf_tipo_identificacion ti ON ter.tipoidentificacion = ti.id_unico
        LEFT JOIN       
            gf_direccion dir ON dir.tercero = ter.id_unico
        LEFT JOIN 	
            gf_telefono  tel ON tel.tercero = ter.id_unico
        WHERE 
            ter.id_unico = $compania");

$razonsocial = $rowC[0][1];
$nombreIdent = $rowC[0][2];
$numeroIdent = $rowC[0][3];
$direccinTer = $rowC[0][4];
$telefonoTer = $rowC[0][5];
$ruta_logo   = $rowC[0][6];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Listado Bancos Sin Conciliar</title>
</head>
<body>
<table width="100%" border="1" cellspacing="0" cellpadding="0">
  <th colspan="5" align="center"><strong>
    <br/>&nbsp;
    <br/><?php echo $razonsocial ?>
    <br/><?php echo $nombreIdent.' : '.$numeroIdent."<br/>".$direccinTer.' Tel:'.$telefonoTer ?>
    <br/>&nbsp;
    <br/>BANCOS SIN CONCILIAR
    <br/>MES: <?php echo mb_strtoupper($mesNomn);?>
    <br/>&nbsp;                 
    </strong>
    </th>
  <tr>
    <td><strong>CÓDIGO</strong></td>
    <td><strong>CUENTA</strong></td>
    <td><strong>SALDO EN LIBROS</strong></td>
    <td><strong>SALDO EXTRACTO</strong></td>
    <td><strong>DIFERENCIA</strong></td>
    
  </tr>
    
<?php 
#BANCOS CUENTAS BANCARIAS
$annov  = $_SESSION['anno'];
$nannov = anno($annov);
#Año Anterior
$anno2 = $nannov-1;
$an2   = $con->Listar("SELECT * FROM gf_parametrizacion_anno WHERE anno = '$anno2'");
if(count($an2)>0){
    $annova = $an2[0][0];
} else {
    $annova = 0;
}

$banco ="SELECT id_unico, codi_cuenta, nombre FROM gf_cuenta "
        . "WHERE clasecuenta = 11 AND parametrizacionanno = $annio ORDER BY codi_cuenta ASC";
$banco = $mysqli->query($banco);
$total =0;
$total2 =0;
while ($row = mysqli_fetch_row($banco)) {
    $id         = $row[0];
    $id_cuenta  = $row[0];
    $codigo     = $row[1];
    $nombre     =  ucwords(mb_strtolower($row[2]));
    $a          = 0;
    $cuentaA    = 0;
    $annov      = $_SESSION['anno'];
    while($a==0){
        $nannov = anno($annov);
        #Año Anterior
        $anno2 = $nannov-1;
        $an2   = $con->Listar("SELECT * FROM gf_parametrizacion_anno WHERE anno = '$anno2' AND compania = $compania");
        if(count($an2)>0){ 
            $annova = $an2[0][0];
            $ca = $con->Listar("SELECT id_unico,codi_cuenta, equivalente_va FROM gf_cuenta WHERE (id_unico) = '$id'");
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
            $ca = $con->Listar("SELECT id_unico,codi_cuenta, equivalente_va FROM gf_cuenta WHERE (id_unico) = '$id'");
            $id_cuenta = $ca[0][0];
            $a += 1;
        }
    }
    $cuentas =($cuentaA.','.$id_cuenta);
   
##########################################################################################################################################
# Saldo en libros
$sqlS = "SELECT 	SUM(dtc.valor)
		FROM		gf_detalle_comprobante dtc 
		LEFT JOIN	gf_comprobante_cnt cnt 			ON dtc.comprobante 		= cnt.id_unico		
		WHERE 		cnt.fecha <= '$fechaF' 
		AND 	    dtc.cuenta = $id";
$resultS = $mysqli->query($sqlS);
$saldo = mysqli_fetch_row($resultS);
$saldoLibro = $saldo[0];

##########################################################################################################################################
# Saldo extracto
$sqlSE = "SELECT saldo_extracto FROM gf_partida_conciliatoria "
        . "WHERE id_cuenta = '$id' AND mes = '$mesId'";
$resultE = $mysqli->query($sqlSE);
$saldoE = mysqli_fetch_row($resultE);
$saldoExtracto = $saldoE[0];
##########################################################################################################################################
##########################################################################################################################################
#GIROS SIN COBRAR
$sumaGiros = "";
$sumaG = "";
##########################################################################################################################################
# Consulta de Giros sin cobrar
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
		AND 		cnt.tipocomprobante != 1
		AND 	    dtc.cuenta IN ($cuentas)
		";
$resultG = $mysqli->query($sqlG);
while ($rowG = mysqli_fetch_row($resultG)) {
	$sumaGiros += $rowG[6];
	$sumaG += $rowG[6];
}
# Consulta de Giros sin cobrar conciliados en otros periodos
 $sqlG = "SELECT DISTINCT dtc.id_unico,
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
                AND             dtc.periodo_conciliado > '$mesId' 
		AND 		cnt.tipocomprobante != 1
		AND 	    dtc.cuenta IN ($cuentas)
		";
$resultG = $mysqli->query($sqlG);
while ($rowG = mysqli_fetch_row($resultG)) {
	$sumaGiros += $rowG[6];
	$sumaG += $rowG[6];
}
##########################################################################################################################################
# INGRESOS SIN COBRAR
$sumaIngresos = "";
$sumaI = "";
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
		AND 		cnt.tipocomprobante != 1
		";
$resultI = $mysqli->query($sqlI);
while ($rowI = mysqli_fetch_row($resultI)) {
	$sumaIngresos += $rowI[6];
	$sumaI += $rowI[6];
}
# Consulta de ingresos sin cobrar de que fueron conciliados en otros periodos
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
		AND 		dtc.conciliado IS NOT NULL
		AND 		cnt.fecha <= '$fechaF' 
                AND             dtc.periodo_conciliado > $mesId 
		AND 	        dtc.cuenta IN ($cuentas)
		AND 		cnt.tipocomprobante != 1
		";
$resultI = $mysqli->query($sqlI);
while ($rowI = mysqli_fetch_row($resultI)) {
	$sumaIngresos += $rowI[6];
	$sumaI += $rowI[6];
}
##########################################################################################################################################
# MOVIMIENTOS DE OTROS PERIODOS
$sumaMovimientos = "";
$sumaM = "";
# Consulta de otros periodos
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
                AND             dtc.periodo_conciliado = $mesId 
		AND             dtc.cuenta IN ($cuentas)
		AND 		cnt.tipocomprobante != 1";
$resultM = $mysqli->query($sqlM);
while ($rowM = mysqli_fetch_row($resultM)) {
	$sumaMovimientos += $rowM[6];
	$sumaM += $rowM[6];
}

##########################################################################################################################################
# PARTIDAS CONCILIATORIAS SALDOS INICIALES

#FECHA <01/01
$fechaMenor =$annoP.'-01-01';
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
    ) AND pc.id_cuenta = $id  AND dtp.valor IS NOT NULL";


$resultPCA = $mysqli->query($sqlPCA);
$sumaPartidasSI=0;
$sumaSI=0;
while ($rowPCA = mysqli_fetch_row($resultPCA)) {
	
        if($rowPCA[6]==1){
		$sumaPartidasSI += $rowPCA[5];
		$sumaSI += $rowPCA[5];
	}else{
		$sumaPartidasSI += $rowPCA[5] *-1;
		$sumaSI += $rowPCA[5] *-1;
	}	
}

##########################################################################################################################################
# PARTIDAS CONCILIATORIAS PERIODOS ANTERIORES
$sumaPartidasA = "";
$sumaPA = "";
##########################################################################################################################################
$sqlP = "SELECT		id_unico
		FROM 		gf_partida_conciliatoria 
		WHERE 		id_cuenta IN ($cuentas) AND mes = '$mesId'";
$resultP = $mysqli->query($sqlP);
$rowPP = mysqli_fetch_row($resultP);
$idPartida = $rowPP[0];
# Consulta de partidas conciliatorias periodos anteriores
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
while ($rowPCA = mysqli_fetch_row($resultPCA)) {
	if($rowPCA[6]==1){
		$sumaPartidasA += $rowPCA[5];
		$sumaPA += $rowPCA[5];
	}else{
		$sumaPartidasA += $rowPCA[5] *-1;
		$sumaPA += $rowPCA[5] *-1;
	}
	
}
//echo $sumaPartidasA;
##########################################################################################################################################
#PARTIDAS CONCILIATORIAS DEL PERIODO
###########################################################################################################################################
$sumaPartidas=0;
$sumaP=0;
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
while ($rowPC = mysqli_fetch_row($resultPC)) {
	if($rowPC[6]==1){
		$sumaPartidas += $rowPC[5];
		$sumaP += $rowPC[5];
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
while ($rowPC = mysqli_fetch_row($resultPC)) {
	if($rowPC[6]==1){
		$sumaPartidas += $rowPC[5];
		$sumaP += $rowPC[5];
	}else{
		$sumaPartidas += $rowPC[5] *-1;
		$sumaP += $rowPC[5] *-1;
	}
}
$saldoConciliado = ($saldoLibro + $sumaMovimientos + $sumaPartidas + $sumaPartidasA + $sumaPartidasSI) - $saldoExtracto - ($sumaIngresos + $sumaGiros);
     ?>
    <tr>
        <td><?php echo utf8_decode($codigo);?></td>
        <td><?php echo ($nombre)?></td>
        <td><?php echo utf8_decode('$'.number_format($saldoLibro,2,'.',','))?></td>
        <td><?php echo utf8_decode('$'.number_format($saldoExtracto,2,'.',','))?></td>
        <td><?php echo utf8_decode('$'.number_format($saldoConciliado,2,'.',','));?></td>
    </tr>
     
    <?php
    
    
}
?>
</table>
</body>
</html>