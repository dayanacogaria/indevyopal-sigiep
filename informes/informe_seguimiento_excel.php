<?php
###############MODIFICACIONES###############################
#02/03/2017 | ERICA G. | ARCHIVO CREADO
############################################################
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Informe_Seguimiento_Disponibilidad.xls");
require_once("../Conexion/conexion.php");
session_start();
$compania   = $_SESSION['compania'];
$anno       = $_SESSION['anno'];  

## Función de consulta recursiva para imprimir los detalles de comprobante en disponibilidad presupuestal
function documento($id,$rubroF,$valor){
	# LLmado de conexión
	require'../Conexion/conexion.php';	
	# Llamado de librerias para saldo
	require_once('../estructura_apropiacion.php');
	# Consulta de datos de comprobantes que afectan al detalle
	if(!empty($id)){
		$sqlD = "SELECT 	tpc.codigo,
							comp.numero,
							date_format(comp.fecha,'%d/%m/%Y'),
							comp.descripcion,
							IF(CONCAT_WS(' ',
                                                          ter.nombreuno,
                                                          ter.nombredos,
                                                          ter.apellidouno,
                                                          ter.apellidodos) 
                                                          IS NULL OR CONCAT_WS(' ',
                                                          ter.nombreuno,
                                                          ter.nombredos,
                                                          ter.apellidouno,
                                                          ter.apellidodos) = '',
                                                        (ter.razonsocial),
                                                        CONCAT_WS(' ',
                                                          ter.nombreuno,
                                                          ter.nombredos,
                                                          ter.apellidouno,
                                                          ter.apellidodos)) AS NOMBRE,
							(dtp.valor),
							dtp.comprobanteafectado,
							dtp.rubrofuente,
							dtp.id_unico
				FROM		gf_comprobante_pptal comp 
				LEFT JOIN 	gf_detalle_comprobante_pptal dtp 	ON comp.id_unico 	= dtp.comprobantepptal
				LEFT JOIN	gf_tipo_comprobante_pptal tpc		ON tpc.id_unico 	= comp.tipocomprobante
				LEFT JOIN 	gf_tercero ter						ON ter.id_unico 	= comp.tercero
				WHERE		dtp.comprobanteafectado	=$id 
				AND 		dtp.rubrofuente =$rubroF";	
	}
	$resultD = $mysqli->query($sqlD); 	
	while ($r = mysqli_fetch_row($resultD)) {
		##########################################################################################################################################################
		# obtención de saldo disponible
		$saldo = $valor-$r[5]<0?'0':$valor-$r[5];
		##########################################################################################################################################################
		# Impresión de comprobantes que afectan al detalle
		?>
                <tr>
                    <td><?php echo $r[0];?></td>
                    <td><?php echo $r[1];?></td>
                    <td><?php echo $r[2];?></td>
                    <td><?php echo $r[3];?></td>
                    <td><?php echo $r[4];?></td>
                    <td align="right"><?php echo number_format($r[5],2,',','.');?></td>
                </tr>
                <tr>
                    <td colspan="6" align="right"><strong>Total Afectaciones:</strong> <?php echo number_format($r[5],2,',','.');?></td>
                </tr>
                <tr>
                    <td colspan="6" align="right"><strong>Saldo <?php echo $r[0]; ?></strong><?php echo ':'.number_format($saldo,2,',','.');?></td>
                </tr>

		<?php 
                #Validamos si detalleafecatdo es diferente de nulo
		if(!empty($r[6])){
			documento($r[8],$r[7], $r[5]);
		}	
	}
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Informe Seguimiento Disponibilidad</title>
</head>
<body>
<table width="100%" border="1" cellspacing="0" cellpadding="0">
  <tr>
      <td colspan="6" bgcolor="skyblue"><center><strong>INFORME SEGUIMIENTO DISPONIBILIDAD</strong></center></td>
  </tr>
  <?php
  #CONSULTAR LOS COMPROBANTES POR VARIABLES DE ENTRADA
if(empty($_POST['tipo'])) { 
        if(empty($_POST['disI']) && empty($_POST['disF'])){
        $consulta = "SELECT DISTINCT 
          cp.id_unico 
        FROM
          gf_comprobante_pptal cp
        LEFT JOIN
          gf_tipo_comprobante_pptal tc ON tc.id_unico = cp.tipocomprobante
        LEFT JOIN
          gf_tercero ter ON cp.tercero = ter.id_unico
        WHERE
          tc.clasepptal = 14 AND tc.tipooperacion = 1 
          AND cp.parametrizacionanno = $anno 
        ORDER BY
          cp.numero ASC";
        } else {
            ####SI TRAE SOLO DISI#####
            if(!empty($_POST['disI']) && empty($_POST['disF'])){
            $disI=$_POST['disI'];
            $consulta = "SELECT DISTINCT 
                cp.id_unico 
              FROM
                gf_comprobante_pptal cp
              LEFT JOIN
                gf_tipo_comprobante_pptal tc ON tc.id_unico = cp.tipocomprobante
              LEFT JOIN
                gf_tercero ter ON cp.tercero = ter.id_unico
              WHERE
                tc.clasepptal = 14 AND tc.tipooperacion = 1 AND numero >='$disI' 
                AND cp.parametrizacionanno = $anno     
              ORDER BY
                cp.numero ASC ";
            } else {
                ##SI TRAE SOLO DISF##
                if(empty($_POST['disI']) && !empty($_POST['disF'])){

                    $disF=$_POST['disF'];
                    $consulta = "SELECT DISTINCT 
                        cp.id_unico 
                      FROM
                        gf_comprobante_pptal cp
                      LEFT JOIN
                        gf_tipo_comprobante_pptal tc ON tc.id_unico = cp.tipocomprobante
                      LEFT JOIN
                        gf_tercero ter ON cp.tercero = ter.id_unico
                      WHERE
                        tc.clasepptal = 14 AND tc.tipooperacion = 1 AND numero <='$disF' 
                        AND cp.parametrizacionanno = $anno     
                      ORDER BY
                        cp.numero ASC ";
                } else {
                    $disI=$_POST['disI'];
                    $disF=$_POST['disF'];
                    $consulta = "SELECT DISTINCT 
                        cp.id_unico 
                      FROM
                        gf_comprobante_pptal cp
                      LEFT JOIN
                        gf_tipo_comprobante_pptal tc ON tc.id_unico = cp.tipocomprobante
                      LEFT JOIN
                        gf_tercero ter ON cp.tercero = ter.id_unico
                      WHERE
                        tc.clasepptal = 14 AND tc.tipooperacion = 1 AND numero BETWEEN '$disI' AND '$disF' 
                        AND cp.parametrizacionanno = $anno     
                      ORDER BY
                        cp.numero ASC ";
                }
            }
    }
} else {
    $tipo = $_POST['tipo'];
    if(empty($_POST['disI']) && empty($_POST['disF'])){
    $consulta = "SELECT DISTINCT 
      cp.id_unico 
    FROM
      gf_comprobante_pptal cp
    LEFT JOIN
      gf_tipo_comprobante_pptal tc ON tc.id_unico = cp.tipocomprobante
    LEFT JOIN
      gf_tercero ter ON cp.tercero = ter.id_unico
    WHERE
      tc.id_unico ='$tipo'   
      AND cp.parametrizacionanno = $anno     
    ORDER BY
      cp.numero ASC";
    } else {
        ####SI TRAE SOLO DISI#####
        if(!empty($_POST['disI']) && empty($_POST['disF'])){
        $disI=$_POST['disI'];
        $consulta = "SELECT DISTINCT 
            cp.id_unico 
          FROM
            gf_comprobante_pptal cp
          LEFT JOIN
            gf_tipo_comprobante_pptal tc ON tc.id_unico = cp.tipocomprobante
          LEFT JOIN
            gf_tercero ter ON cp.tercero = ter.id_unico
          WHERE
            tc.id_unico ='$tipo'  AND numero >='$disI' 
             AND cp.parametrizacionanno = $anno    
          ORDER BY
            cp.numero ASC ";
        } else {
            ##SI TRAE SOLO DISF##
            if(empty($_POST['disI']) && !empty($_POST['disF'])){

                $disF=$_POST['disF'];
                $consulta = "SELECT DISTINCT 
                    cp.id_unico 
                  FROM
                    gf_comprobante_pptal cp
                  LEFT JOIN
                    gf_tipo_comprobante_pptal tc ON tc.id_unico = cp.tipocomprobante
                  LEFT JOIN
                    gf_tercero ter ON cp.tercero = ter.id_unico
                  WHERE
                    tc.id_unico ='$tipo'  AND numero <='$disF' 
                    AND cp.parametrizacionanno = $anno     
                  ORDER BY
                    cp.numero ASC ";
            } else {
                $disI=$_POST['disI'];
                $disF=$_POST['disF'];
                $consulta = "SELECT DISTINCT 
                    cp.id_unico 
                  FROM
                    gf_comprobante_pptal cp
                  LEFT JOIN
                    gf_tipo_comprobante_pptal tc ON tc.id_unico = cp.tipocomprobante
                  LEFT JOIN
                    gf_tercero ter ON cp.tercero = ter.id_unico
                  WHERE
                    tc.id_unico ='$tipo'  AND numero BETWEEN '$disI' AND '$disF' 
                    AND cp.parametrizacionanno = $anno     
                  ORDER BY
                    cp.numero ASC ";
            }
        }
}
}


$consulta = $mysqli->query($consulta);
$numDis= mysqli_num_rows($consulta);
while ($row1 = mysqli_fetch_row($consulta)) {
    
$numDis=$numDis-1;

# Variable capturada por get
$id_Pptal = $row1[0];

# Consulta de comprobante
 $sql = "SELECT 		comP.id_unico,
					tipCom.nombre,
					tipCom.codigo,
					comP.numero,
					date_format(comP.fecha,'%d/%m/%Y'),
					SUM(dtp.valor),
					CONCAT(ELT(WEEKDAY( comP.fecha) + 1, 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado', 'Domingo')) AS DIA_SEMANA 
		FROM 		gf_comprobante_pptal comP
		LEFT JOIN	gf_tipo_comprobante_pptal tipCom
		ON 			tipCom.id_unico =	comP.tipocomprobante
		LEFT JOIN	gf_detalle_comprobante_pptal dtp	
		ON 			dtp.comprobantepptal = 	comP.id_unico
		WHERE	comP.id_unico = '$id_Pptal'
		";
$result = $mysqli->query($sql);
$row = mysqli_fetch_row($result);
# Obtención de valores
$id 	= $row[0];
$tipo 	= $row[2].' - '.$row[1];
$numero = $row[3];
$fecha 	= $row[4];
$valor  = $row[5];
$diaF 	= $row[6];

# Impresión de cabeza
?>
<!-- Impresión de tipo-->
    <tr>
        <td colspan="6"><strong>Tipo: <i><?php echo $tipo;?></i></strong></td>
    </tr>    
<!--Impresión de número de comprobante-->
    <tr>
        <td colspan="6"><strong>Numero: <i><?php echo $numero;?></i></strong></td>
    </tr> 
<!--Impresión de fecha de comprobante-->
    <tr>
        <td colspan="6"><strong>Fecha: <i><?php echo $fecha;?></i></strong></td>
    </tr>
<!--Valor-->
    <tr>
        <td colspan="6"><strong>Valor: <i><?php echo number_format($valor,2,'.',',');?></i></strong></td>
    </tr>
<tr><td colspan="6"></td></tr>
<?php
# Consulta de código, nombre y valor de rubros
 $sqlRB = "SELECT	dtcp.id_unico,
					rub.codi_presupuesto,
					rub.nombre,
					fte.nombre,
					dtcp.valor,
					dtcp.comprobanteafectado,
					dtcp.rubrofuente
		FROM		gf_detalle_comprobante_pptal dtcp
		LEFT JOIN	gf_rubro_fuente rbf 	ON 	rbf.id_unico 	= dtcp.rubrofuente
		LEFT JOIN	gf_rubro_pptal rub		ON 	rub.id_unico 	= rbf.rubro
		LEFT JOIN	gf_fuente fte			ON 	fte.id_unico 	= rbf.fuente
		WHERE		dtcp.comprobantepptal 	= 	$id";		
$resultRB = $mysqli->query($sqlRB);
# Impresión de valores obtenidos en la consulta

#### CONSULTA DETALLES ####
 
while ( $rw = mysqli_fetch_row($resultRB)) {
    
    $consN="SELECT *
FROM
  gf_comprobante_pptal comp
LEFT JOIN
  gf_detalle_comprobante_pptal dtp ON comp.id_unico = dtp.comprobantepptal
LEFT JOIN
  gf_tipo_comprobante_pptal tpc ON tpc.id_unico = comp.tipocomprobante
LEFT JOIN
  gf_tercero ter ON ter.id_unico = comp.tercero
WHERE
  dtp.comprobanteafectado = $rw[0] AND dtp.rubrofuente = $rw[6]";
$consN=$mysqli->query($consN);
        
if(mysqli_num_rows($consN)>0) {
	
	# Impresión de código y nombre de rubro, fuente, valor
    ?>
    <tr>
        <td colspan="4"><strong>Rubro: </strong><i><?php echo ucwords(mb_strtolower($rw[2].' / '.$rw[3]));?></i></td>
    
        <td colspan="2"><strong>Valor: </strong><i><?php echo number_format($rw[4],2,',','.');?></i></td>
    </tr>
    <tr><td></td></tr>
    <!--Cabeza de tabla-->
    <tr>
        <td><strong>TIPO</strong></td>   
        <td><strong>NÚMERO</strong></td>
        <td><strong>FECHA</strong></td>
        <td><strong>DESCRIPCION</strong></td>
        <td><strong>TERCERO</strong></td>
        <td><strong>VALOR</strong></td>
        
    </tr>
	<?php
	documento($rw[0],$rw[6],$rw[4]);
	
	##########################################################################################################################################################
	# Salto final del ciclo	
	
    }
} ?>
    
    <tr><td colspan="6" rowspan="2"></td></tr>
    <tr></tr>
<?php }
  ?>
    
    
    
    
    
</table>
</body>
</html>