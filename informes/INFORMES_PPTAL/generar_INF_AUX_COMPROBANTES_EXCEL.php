<?php
##########################################################################################################################################################################
#                                                                                              Modificaciones
##########################################################################################################################################################################
#29/08/2017 | Erica G. | Encabezado
#03/03/2017 | ERICA G. | ARREGLO BUSQUEDA MODIFICACION Y AFECTADO
##########################################################################################################################################################################

header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Informe_Auxiliar_Comprobantes_Presupuestales.xls");
require'../../Conexion/conexion.php';
session_start();
ini_set('max_execution_time', 0);
$usuario=$_SESSION['usuario'];
$fechaActual=date('d/m/Y');

$rubini         = $mysqli->real_escape_string(''.$_POST["sltrubi"].'');
$rubfin         = $mysqli->real_escape_string(''.$_POST["sltrubf"].'');
$fechaini       = $mysqli->real_escape_string(''.$_POST["fechaini"].''); 
$fechafin       = $mysqli->real_escape_string(''.$_POST["fechafin"].'');
$compini        = $mysqli->real_escape_string(''.$_POST["sltTci"].'');
$compfin        = $mysqli->real_escape_string(''.$_POST["sltTcf"].'');

$head        = $mysqli->real_escape_string(''.$_POST["headH"].'');
$foot        = $mysqli->real_escape_string(''.$_POST["footH"].'');

#Conversión Fecha 
$fechaI = DateTime::createFromFormat('d/m/Y', "$fechaini");
$fechaI= $fechaI->format('Y/m/d');

$fechaF = DateTime::createFromFormat('d/m/Y', "$fechafin");
$fechaF= $fechaF->format('Y/m/d');

#************Datos Compañia************#
$compania = $_SESSION['compania'];
$sqlC = "SELECT 	ter.id_unico,
                ter.razonsocial,
                UPPER(ti.nombre),
                ter.numeroidentificacion,
                dir.direccion,
                tel.valor,
                ter.ruta_logo
FROM gf_tercero ter
LEFT JOIN 	gf_tipo_identificacion ti ON ter.tipoidentificacion = ti.id_unico
LEFT JOIN   gf_direccion dir ON dir.tercero = ter.id_unico
LEFT JOIN 	gf_telefono  tel ON tel.tercero = ter.id_unico
WHERE ter.id_unico = $compania";
$resultC = $mysqli->query($sqlC);
$rowC = mysqli_fetch_row($resultC);
$razonsocial = $rowC[1];
$nombreIdent = $rowC[2];
$numeroIdent = $rowC[3];
$direccinTer = $rowC[4];
$telefonoTer = $rowC[5];
?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Auxiliar Comprobantes Presupuestales</title>
</head>
<body>
<table width="100%" border="1" cellspacing="0" cellpadding="0">
    <tr>
        <th colspan="9" align="center"><strong>
            <br/>&nbsp;
            <br/><?php echo $razonsocial ?>
            <br/><?php echo $nombreIdent.' : '.$numeroIdent."<br/>".$direccinTer.' Tel:'.$telefonoTer ?>
           <br/>&nbsp;
            <br/>AUXILIAR COMPROBANTES PRESUPUESTALES
            <br/>Rubros Del <?php echo $rubini.' al '.$rubfin?>
            <br/>Comprobantes Del <?php echo $compini.' al '.$compfin?>
            <br/>Fecha: Del <?php echo $fechaini.' al '.$fechafin?><br/>&nbsp;</strong>
        </th>
  </tr>
  <tr>
        
        <td rowspan="2" align="center"><strong>COMPROBANTE</strong></td>
        <td rowspan="2" align="center"><strong>FECHA</strong></td>
        <td rowspan="2" align="center"><strong>DESCRIPCIÓN</strong></td>
        <td rowspan="2" align="center"><strong>TERCERO</strong></td>
        <td rowspan="2" align="center"><strong>NOMBRE</strong></td>
        <td rowspan="2" align="center"><strong>VALOR</strong></td>
        <td colspan="2" align="center"><strong>MODIFICACIONES</strong></td>
        <td rowspan="2" align="center"><strong>DISPONIBILIDAD</strong></td>
    </tr>
    <tr>
        <td align="center"><strong>Adición</strong></td>
        <td align="center"><strong>Disminución</strong></td>
    </tr>
<?php
$rubroP ="SELECT DISTINCT "
        . "tcp.codigo as tcpCodigo, tcp.nombre as tcpNombre "
        . "FROM gf_detalle_comprobante_pptal dcp "
        . "LEFT JOIN gf_rubro_fuente rf ON dcp.rubrofuente = rf.id_unico "
        . "LEFT JOIN gf_rubro_pptal rp ON rf.rubro = rp.id_unico "
        . "LEFT JOIN gf_comprobante_pptal cp ON dcp.comprobantepptal = cp.id_unico "
        . "LEFT JOIN gf_tipo_comprobante_pptal tcp ON cp.tipocomprobante = tcp.id_unico  "
        . "WHERE rp.codi_presupuesto BETWEEN '$rubini' AND '$rubfin' "
        . "AND  cp.fecha BETWEEN '$fechaI' AND '$fechaF' AND tcp.codigo BETWEEN '$compini' AND '$compfin' "
        . "ORDER BY tcpCodigo ASC ";

$rubroP1 =$mysqli->query($rubroP);
while ($rubro = mysqli_fetch_assoc($rubroP1)){
    $tipo = $rubro['tcpCodigo'];
    $comprobanteI= "SELECT codigo as cod FROM gf_tipo_comprobante_pptal WHERE codigo ='$tipo' "
                 . "AND (clasepptal ='13' OR clasepptal ='14' OR clasepptal ='15' OR clasepptal ='16' OR clasepptal ='17')";
    $comprobanteI = $mysqli->query($comprobanteI);
    if(mysqli_num_rows($comprobanteI)>0){ ?>
    
       <tr>
           <td colspan="9" align="left"><strong><i><?php echo 'Tipo: '.mb_strtoupper($rubro['tcpCodigo']).' - '.mb_strtoupper($rubro['tcpNombre']);?></i></strong></td>
       
        </tr>
      <?php
      $con = "SELECT DISTINCT
          rp.codi_presupuesto   as rpcodp,
          rp.nombre             as rpnom,
          rf.fuente             as fuente,
          dcp.rubrofuente       as dcprf,
          dcp.tercero           as dcpter, 
          tcp.clasepptal        as tcpcla, 
          cp.fecha              as cpfecha, 
          tcp.codigo            as tcpcod, 
          cp.numero             as cpnum, 
          IF(CONCAT_WS(' ',
            tr.nombreuno,
            tr.nombredos,
            tr.apellidouno,
            tr.apellidodos) 
            IS NULL OR CONCAT_WS(' ',
            tr.nombreuno,
            tr.nombredos,
            tr.apellidouno,
            tr.apellidodos) = '',
          (tr.razonsocial),
          CONCAT_WS(' ',
            tr.nombreuno,
            tr.nombredos,
            tr.apellidouno,
            tr.apellidodos)) AS NOMBRE, 
          dcp.descripcion       as dcpdesc,
          tcp.tipooperacion     as tcptop, 
          dcp.id_unico  as idDetalle, 
          dcp.valor as valor, 
          tcp.id_unico as tipocom, 
          tr.numeroidentificacion as numTer , 
          dcp.comprobanteafectado as compAfectado, 
          ca.tipocomprobante AS tipoa 
        FROM
          gf_detalle_comprobante_pptal dcp
        LEFT JOIN
          gf_rubro_fuente rf ON dcp.rubrofuente = rf.id_unico
        LEFT JOIN 
          gf_fuente ff ON rf.fuente = ff.id_unico
        LEFT JOIN
          gf_rubro_pptal rp ON rf.rubro = rp.id_unico
        LEFT JOIN
          gf_comprobante_pptal cp ON dcp.comprobantepptal = cp.id_unico 
        LEFT JOIN 
          gf_tipo_comprobante_pptal tcp ON cp.tipocomprobante = tcp.id_unico 
        LEFT JOIN
            gf_tercero tr ON tr.id_unico = dcp.tercero
          LEFT JOIN
            gf_detalle_comprobante_pptal dcpa ON dcpa.id_unico = dcp.comprobanteafectado
          LEFT JOIN
            gf_comprobante_pptal ca ON dcpa.comprobantepptal = ca.id_unico 
        WHERE 
            rp.codi_presupuesto BETWEEN '$rubini' AND '$rubfin' AND 
            cp.fecha BETWEEN '$fechaI' AND '$fechaF' ORDER BY cp.numero asc";
        $total1=0;
        $total2=0;
        $con1 = $mysqli->query($con);
        while ($row = mysqli_fetch_array($con1)) {
            $tipo= $row['tipocom'];
            $comprobanteI= "SELECT codigo as cod FROM gf_tipo_comprobante_pptal WHERE id_unico ='$tipo' "
                    . "AND (clasepptal ='13' OR clasepptal ='14' OR clasepptal ='15' OR clasepptal ='16' OR clasepptal ='17')";
            $comprobanteI = $mysqli->query($comprobanteI);
            if(mysqli_num_rows($comprobanteI)>0){
            $row1 = mysqli_fetch_array($comprobanteI);

            if($row1['cod']>=$compini && $row1['cod']<=$compfin){

        if($row['tcpcod'] ==$rubro['tcpCodigo']){
            $valor = $row['valor'];    
            $tipocomp= $row['tipoa'];
            #adicion 
            $comprobante = $row['idDetalle'];
           $ad= "SELECT SUM(valor) "
                    . "FROM gf_detalle_comprobante_pptal dcp "
                    . "LEFT JOIN gf_comprobante_pptal cp ON dcp.comprobantepptal = cp.id_unico "
                    . "LEFT JOIN gf_tipo_comprobante_pptal tcp ON cp.tipocomprobante = tcp.id_unico "
                    . "WHERE dcp.comprobanteafectado='$comprobante'  AND tcp.tipooperacion='2'";
            $ad = $mysqli->query($ad);
            $numad= "SELECT * "
                    . "FROM gf_detalle_comprobante_pptal dcp "
                    . "LEFT JOIN gf_comprobante_pptal cp ON dcp.comprobantepptal = cp.id_unico "
                    . "LEFT JOIN gf_tipo_comprobante_pptal tcp ON cp.tipocomprobante = tcp.id_unico "
                    . "WHERE dcp.comprobanteafectado='$comprobante'  AND tcp.tipooperacion='2'";
            $numad = $mysqli->query($numad);
            if(mysqli_num_rows($numad)>0){
                $adicion =0;
                while($ad1= mysqli_fetch_row($ad)) {
                    $adicion = $ad1[0]+$adicion;
                }
            } else {
                $adicion =0;
            }
        
        
        #disminucion 
         $comprobante = $row['idDetalle'];
        $dis= "SELECT SUM(valor) "
                . "FROM gf_detalle_comprobante_pptal dcp "
                . "LEFT JOIN gf_comprobante_pptal cp ON dcp.comprobantepptal = cp.id_unico "
                . "LEFT JOIN gf_tipo_comprobante_pptal tcp ON cp.tipocomprobante = tcp.id_unico "
                . "WHERE dcp.comprobanteafectado='$comprobante'  AND tcp.tipooperacion='3'";
        $dis = $mysqli->query($dis);
        $numdis= "SELECT * "
                . "FROM gf_detalle_comprobante_pptal dcp "
                . "LEFT JOIN gf_comprobante_pptal cp ON dcp.comprobantepptal = cp.id_unico "
                . "LEFT JOIN gf_tipo_comprobante_pptal tcp ON cp.tipocomprobante = tcp.id_unico "
                . "WHERE dcp.comprobanteafectado='$comprobante'  AND tcp.tipooperacion='3'";
        $numdis = $mysqli->query($numdis);
        if(mysqli_num_rows($numdis)>0) {
            $disminucion=0;
            while($dis1= mysqli_fetch_row($dis)){
                $disminucion = $dis1[0]+$disminucion;
            }
        }else {
            $disminucion =0;
        }        
        
        $disponibilidad = $valor+$adicion-$disminucion;
        
        
        $p1   = (float)($valor);
        $p2   = (float)($adicion);
        $p3   = (float)($disminucion);
        $p4   = (float)($disponibilidad);
        
         $dat = $row['cpfecha'];
         $dat = trim($dat, '"');
         $fecha_div = explode("-", $dat);
         $aniodat = $fecha_div[0];
         $mesdat = $fecha_div[1];
         $diadat = $fecha_div[2];
         $dat = $diadat.'/'.$mesdat.'/'.$aniodat;
         
         
         
         ?>
        <tr>
            <td align="right"><?php echo $row['cpnum'];?></td>
            <td align="left"><?php echo $dat;?></td>
            <td align="left"><?php echo ucwords(mb_strtolower($row['dcpdesc']));?></td>
            <td align="left"><?php echo ucwords(mb_strtolower($row['numTer']));?></td>
            <td align="left"><?php echo ucwords(mb_strtolower($row['NOMBRE']));?></td>
            <td align="right"><?php echo number_format($p1,2,'.',',');?></td>
            <td align="right"><?php echo number_format($p2,2,'.',',');?></td>
            <td align="right"><?php echo number_format($p3,2,'.',',');?></td>
            <td align="right"><?php echo number_format($p4,2,'.',',');?></td>
            
        </tr>
    <?php
        $total1= $total1+$p1;
        $total2= $total2+$p4;
        }
       }
      }
    }
?>
    <tr>
            <td colspan="5" align="right"><strong>TOTALES: </strong></td>
            <td align="right"><strong><?php echo number_format($total1,2,'.',',');?></strong></td>
            <td colspan="3" align="right"><strong><?php echo number_format($total2,2,'.',',');?></strong></td>
      
        </tr>
<?php 
}
}
?>
</table>
</body>
</html>