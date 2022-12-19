
<?php
##########################################################################################################################################################################
#                                                                                              Modificaciones
##########################################################################################################################################################################
#29/08/2017 | Erica G. | Encabezado
#04/03/2017 | ERICA G. | ARREGLO BUSQUEDA MODIFICACION Y AFECTADO
##########################################################################################################################################################################

header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Informe_Listado_Comprobantes_Presupuestales.xls");
require'../../Conexion/conexion.php';
session_start();
ini_set('max_execution_time', 0);
$usuario=$_SESSION['usuario'];
$anno   = $_SESSION['anno'];
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
<title>Listado Comprobantes Presupuestales</title>
</head>
<body>
<table width="100%" border="1" cellspacing="0" cellpadding="0">
    <tr>
        <th colspan="11" align="center"><strong>
            <br/>&nbsp;
            <br/><?php echo $razonsocial ?>
            <br/><?php echo $nombreIdent.' : '.$numeroIdent."<br/>".$direccinTer.' Tel:'.$telefonoTer ?>
           <br/>&nbsp;
           <br/>LISTADO COMPROBANTES PRESUPUESTALES
           <br/>Rubros Del <?php echo $rubini.' al '.$rubfin?>
           <br/>Comprobantes Del <?php echo $compini.' al '.$compfin?>
           <br/>Fecha: Del <?php echo $fechaini.' al '.$fechafin?><br/>&nbsp;</strong>
        </th>
  </tr>
  <tr>
        <td rowspan="2" align="center"><strong>NÚMERO</strong></td>
        <td rowspan="2"  align="center"><strong>FECHA</strong></td>
        <td rowspan="2"  align="center"><strong>RUBRO</strong></td>
        <td rowspan="2"  align="center"><strong>FUENTE</strong></td>
        <td rowspan="2"  align="center"><strong>TIPO AFECTADO</strong></td>
        <td rowspan="2"  align="center"><strong>NOMBRE RUBRO</strong></td>
        <td rowspan="2"  align="center"><strong>CONCEPTO</strong></td>
        <td rowspan="2"  align="center"><strong>TERCERO</strong></td>
        <td rowspan="2"  align="center"><strong>NOMBRE</strong></td>
        <td rowspan="2"  align="center"><strong>CRÉDITOS</strong></td>
        <td rowspan="2"  align="center"><strong>CONTRACRÉDITOS</strong></td>
    </tr>
    <TR></TR>
<?php
$rubroP ="SELECT DISTINCT "
        . "tcp.codigo as tcpCodigo, tcp.nombre as tcpNombre "
        . "FROM gf_detalle_comprobante_pptal dcp "
        . "LEFT JOIN gf_rubro_fuente rf ON dcp.rubrofuente = rf.id_unico "
        . "LEFT JOIN gf_rubro_pptal rp ON rf.rubro = rp.id_unico "
        . "LEFT JOIN gf_comprobante_pptal cp ON dcp.comprobantepptal = cp.id_unico "
        . "LEFT JOIN gf_tipo_comprobante_pptal tcp ON cp.tipocomprobante = tcp.id_unico  "
        . "WHERE rp.codi_presupuesto BETWEEN '$rubini' AND '$rubfin' "
        . "AND  cp.fecha BETWEEN '$fechaI' AND '$fechaF' "
        . "AND cp.parametrizacionanno = $anno "
        . "ORDER BY tcpCodigo ASC ";
$rubroP1 =$mysqli->query($rubroP);
while ($rubro = mysqli_fetch_assoc($rubroP1)){
    $tipo = $rubro['tcpCodigo'];
   $comprobanteI= "SELECT tc.codigo as cod 
                FROM gf_tipo_comprobante_pptal tc 
                LEFT JOIN gf_clase_pptal cl ON
                    tc.clasepptal = cl.id_unico
                WHERE
                    cl.tipoclase = 7
                   AND  tc.codigo = '$tipo' ";
        $comprobanteI = $mysqli->query($comprobanteI);
   if(mysqli_num_rows($comprobanteI)>0){
       
       
        $con1 = "SELECT tcp.id_unico as tipocom,tcp.codigo as tcpcod 
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
        gf_tercero t ON t.id_unico = dcp.tercero 
    WHERE 
        rp.codi_presupuesto BETWEEN '$rubini' AND '$rubfin' 
        AND cp.parametrizacionanno = $anno AND  
        cp.fecha BETWEEN '$fechaI' AND '$fechaF'";
     $numd=0;
    $con11 = $mysqli->query($con1);
    while ($row1 = mysqli_fetch_array($con11)) {
     $tipo1= $row1['tipocom'];
        $comprobanteI1= "SELECT tc.codigo as cod 
                FROM gf_tipo_comprobante_pptal tc 
                LEFT JOIN gf_clase_pptal cl ON
                    tc.clasepptal = cl.id_unico
                WHERE
                    cl.tipoclase = 7
                   AND  tc.id_unico ='$tipo1' ";
        $comprobanteI1 = $mysqli->query($comprobanteI1);
        if(mysqli_num_rows($comprobanteI1)>0){
        $row11 = mysqli_fetch_array($comprobanteI1);
        if($row11['cod']>=$compini && $row11['cod']<=$compfin){
    if($row1['tcpcod'] ==$rubro['tcpCodigo']){
        $numd=$numd+1;
        }
        }
        }
    }  
        
        if($numd>0) {
       ?>
        <tr>
        <td colspan="11" ><strong><i><?php echo 'Tipo: '.mb_strtoupper($rubro['tcpCodigo']).' - '.mb_strtoupper($rubro['tcpNombre'])?></i></strong></td>
    </tr>
        <?php 
     $con = "SELECT
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
     t.nombreuno,
     t.nombredos,
     t.apellidouno,
     t.apellidodos) 
     IS NULL OR CONCAT_WS(' ',
     t.nombreuno,
     t.nombredos,
     t.apellidouno,
     t.apellidodos) = '',
     (t.razonsocial),
     CONCAT_WS(' ',
     t.nombreuno,
     t.nombredos,
     t.apellidouno,
     t.apellidodos)) AS NOMBRE, 
      dcp.descripcion       as dcpdesc,
      tcp.tipooperacion     as tcptop, 
      dcp.id_unico  as idDetalle, 
      dcp.valor as valor, 
      tcp.id_unico as tipocom, 
      t.numeroidentificacion as numTer,
      dcp.comprobanteafectado as comprAfec 
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
        gf_tercero t ON t.id_unico = dcp.tercero 
    WHERE 
        rp.codi_presupuesto BETWEEN '$rubini' AND '$rubfin' 
        AND cp.parametrizacionanno = $anno AND  
        cp.fecha BETWEEN '$fechaI' AND '$fechaF' ORDER BY rpcodp asc";
     $total1=0;
      $total2=0;
    $con1 = $mysqli->query($con);
    while ($row = mysqli_fetch_array($con1)) {
     $tipo= $row['tipocom'];
        $comprobanteI= "SELECT tc.codigo as cod 
                FROM gf_tipo_comprobante_pptal tc 
                LEFT JOIN gf_clase_pptal cl ON
                    tc.clasepptal = cl.id_unico
                WHERE
                    cl.tipoclase = 7
                   AND tc.id_unico ='$tipo' ";
        $comprobanteI = $mysqli->query($comprobanteI);
        if(mysqli_num_rows($comprobanteI)>0){
        $row1 = mysqli_fetch_array($comprobanteI);
        if($row1['cod']>=$compini && $row1['cod']<=$compfin){
    if($row['tcpcod'] ==$rubro['tcpCodigo']){
        $valor = $row['valor'];    
        #AFECTADO
	$comp = $row['idDetalle'];    
        $a = "SELECT valor as value
                FROM
                  gf_detalle_comprobante_pptal dcp
                LEFT JOIN
                  gf_comprobante_pptal cp ON dcp.comprobantepptal = cp.id_unico
                LEFT JOIN
                  gf_tipo_comprobante_pptal tcp ON cp.tipocomprobante = tcp.id_unico
                LEFT JOIN
                  gf_tipo_operacion top ON tcp.tipooperacion = top.id_unico
                WHERE
                  dcp.comprobanteafectado = '$comp' AND top.id_unico = 1";
	$af = $mysqli->query($a);
        
        if(mysqli_num_rows($af)>0){
            $sum=0;
            while ($sum1= mysqli_fetch_array($af)) {
                $sum = $sum1['value']+$sum;
            }
        } else {
           $sum=0; 
        }
        $afectado = $sum;
        #MODIFICACIONES
        $mod= "SELECT valor as value, tcp.tipooperacion as idcom 
                FROM
                  gf_detalle_comprobante_pptal dcp
                LEFT JOIN
                  gf_comprobante_pptal cp ON dcp.comprobantepptal = cp.id_unico
                LEFT JOIN
                  gf_tipo_comprobante_pptal tcp ON cp.tipocomprobante = tcp.id_unico
                LEFT JOIN
                  gf_tipo_operacion top ON tcp.tipooperacion = top.id_unico
                WHERE
                  dcp.comprobanteafectado = '$comp' AND top.id_unico != 1";
        $modi = $mysqli->query($mod);
        if(mysqli_num_rows($modi)>0){
            $modifi=0;
            while ($modif= mysqli_fetch_array($modi)){
                $modificacion= $modif['value'];
                if($modif['idcom']==2){
                    $modifi = $modificacion+$modifi;
                } else {
                    if($modif['idcom']==3){
                        $modifi =$modifi+($modificacion*-1);
                    } else {
                        $modifi = 0; 
                    }
               }
            }
        } else {
            $modifi=0;
        }
        $modificacion1  = $modifi;
        if($modificacion1<0){
            $modificacion =$modificacion1*-1; 
        } else {
            $modificacion =$modificacion1;
        }
        
        #TOTAL
        $total = $valor+$modificacion1;
        #SALDO
        $saldo=$total-$afectado;
        
        ## TIPO AFECTADO 
       ##AFECTADO ##
        $commA = $row['comprAfec'];
        $comm= "SELECT DISTINCT
            tcp.codigo as codigoA,
            tcp.nombre as descripA 
          FROM
            gf_detalle_comprobante_pptal dcp
          LEFT JOIN
            gf_comprobante_pptal cp ON dcp.comprobantepptal = cp.id_unico
          LEFT JOIN
            gf_tipo_comprobante_pptal tcp ON cp.tipocomprobante = tcp.id_unico
          WHERE  dcp.id_unico = '$commA'";
        $comm = $mysqli->query($comm);
        if(mysqli_num_rows($comm)>0){
            $comAfec = mysqli_fetch_array($comm);
            $comprobanteAfectado =$comAfec['codigoA'].' - '.ucwords(mb_strtolower($comAfec['descripA']));
        } else {
            $comprobanteAfectado ='';
        } 
        
        
        $p1   = (float)($valor);
        $p2   = (float)($modificacion);
        $p3   = (float)($total);
        $p4   = (float)($afectado);
        $p5   = (float)($saldo);
        
         $dat = $row['cpfecha'];//date('Y-m-d');
         $dat = trim($dat, '"');
         $fecha_div = explode("-", $dat);
         $aniodat = $fecha_div[0];
         $mesdat = $fecha_div[1];
         $diadat = $fecha_div[2];
         $dat = $diadat.'/'.$mesdat.'/'.$aniodat;
         ?>
    <tr>
            <td align="center"><?php echo $row['cpnum'];?></td>
            <td align="center"><?php echo $dat;?></td>
            <td align="right"><?php echo $row['rpcodp'];?></td>
            <td align="right"><?php echo $row['fuente'];?></td>
            <td align="left"><?php echo $comprobanteAfectado;?></td>
            <td align="left"><?php echo $row['rpnom'];?></td>
            <td align="left"><?php echo ucwords(mb_strtolower($row['dcpdesc']));?></td>
            <td align="left"><?php echo $row['numTer'];?></td>
            <td align="left"><?php echo ucwords(mb_strtolower($row['NOMBRE']));?></td>
            <td align="right"><?php echo number_format($p1,2,'.',',');?></td>
            <td align="right"><?php echo number_format(0,2,'.',',');?></td>
            <?php 
            $total1= $total1+$p1;
            $total2= $total2+0;?>
    </tr>
    <?php
       }
        }
     }
   }
    ?>
    
    <tr>
        <td colspan="9" ><strong><i><?php echo 'TOTALES';?></i></strong></td>
        <td><strong><i><?php echo number_format($total1,2,'.',',')?></i></strong></td>
        <td><strong><i><?php echo number_format($total2,2,'.',',')?></i></strong></td>
    </tr>
    <?php
    }
   }
}
?>
</table>
</body>
</html>