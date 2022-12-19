<?php
##########################################################################################################################################################################
#                                                                                              Modificaciones
##########################################################################################################################################################################
#29/08/2017 | Erica G. | Encabezado
#16/05/2017 | ERICA G. | FILTRO TERCEROS
#03/03/2017 | ERICA G. | ARREGLO BUSQUEDA MODIFICACION Y AFECTADO
##########################################################################################################################################################################

header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Informe_Auxiliar_Por_Terceros.xls");
require'../../Conexion/conexion.php';
session_start();
ini_set('max_execution_time',0);


$rubini         = $mysqli->real_escape_string(''.$_POST["sltrubi"].'');
$rubfin         = $mysqli->real_escape_string(''.$_POST["sltrubf"].'');
$fechaini       = $mysqli->real_escape_string(''.$_POST["fechaini"].''); 
$fechafin       = $mysqli->real_escape_string(''.$_POST["fechafin"].'');
$compini        = $mysqli->real_escape_string(''.$_POST["sltTci"].'');
$compfin        = $mysqli->real_escape_string(''.$_POST["sltTcf"].'');
$terInicial     = $mysqli->real_escape_string(''.$_POST["sltTi"].'');
$terFinal       = $mysqli->real_escape_string(''.$_POST["sltTf"].'');

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
<title>Auxiliar Por Terceros</title>
</head>
<body>
<table width="100%" border="1" cellspacing="0" cellpadding="0">
    <tr>
        <th colspan="8" align="center"><strong>
            <br/>&nbsp;
            <br/><?php echo $razonsocial ?>
            <br/><?php echo $nombreIdent.' : '.$numeroIdent."<br/>".$direccinTer.' Tel:'.$telefonoTer ?>
           <br/>&nbsp;
           <br/>LISTADO AUXILIAR POR TERCEROS
           <br/>Rubros Del <?php echo $rubini.' al '.$rubfin?>
           <br/>Comprobantes Del <?php echo $compini.' al '.$compfin?>
           <br/>Fecha: Del <?php echo $fechaini.' al '.$fechafin?><br/>&nbsp;</strong>
        </th>
  </tr>
  <tr>
        <td rowspan="2" align="center"><strong>NÚMERO</strong></td>
        <td rowspan="2" align="center"><strong>TIPO</strong></td>
        <td rowspan="2" align="center"><strong>FECHA</strong></td>
        <td rowspan="2" align="center"><strong>RUBRO</strong></td>
        <td rowspan="2" align="center"><strong>NOMBRE</strong></td>
        <td rowspan="2" align="center"><strong>TIPO AFECTADO</strong></td>
        <td rowspan="2" align="center"><strong>CRÉDITOS</strong></td>
        <td rowspan="2" align="center"><strong>CONTRACRÉDITOS</strong></td>
    </tr>
    <TR></TR>
<?php
if(!empty($terInicial) &&  empty($terFinal)) {
$rubroP ="SELECT DISTINCT "
        . "tr.numeroidentificacion as Numter, 
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
                tr.apellidodos)) AS NOMBRE, "
        . "tr.id_unico as idter FROM gf_detalle_comprobante_pptal dcp "
        . "LEFT JOIN gf_rubro_fuente rf ON dcp.rubrofuente = rf.id_unico "
        . "LEFT JOIN gf_rubro_pptal rp ON rf.rubro = rp.id_unico "
        . "LEFT JOIN gf_comprobante_pptal cp ON dcp.comprobantepptal = cp.id_unico "
        . "LEFT JOIN gf_tipo_comprobante_pptal tcp ON cp.tipocomprobante = tcp.id_unico  "
        . "LEFT JOIN gf_tercero tr ON tr.id_unico = dcp.tercero "
        . "WHERE rp.codi_presupuesto BETWEEN '$rubini' AND '$rubfin' "
        . "AND  cp.fecha BETWEEN '$fechaI' AND '$fechaF' AND "
        . "tr.numeroidentificacion BETWEEN '$terInicial' AND '$terFinal' "
        . "AND tcp.codigo BETWEEN '$compini' AND '$compfin' "
        . "ORDER BY Numter ASC ";
} else {
    $rubroP ="SELECT DISTINCT "
        . "tr.numeroidentificacion as Numter, 
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
            tr.apellidodos)) AS NOMBRE, "
        . "tr.id_unico as idter FROM gf_detalle_comprobante_pptal dcp "
        . "LEFT JOIN gf_rubro_fuente rf ON dcp.rubrofuente = rf.id_unico "
        . "LEFT JOIN gf_rubro_pptal rp ON rf.rubro = rp.id_unico "
        . "LEFT JOIN gf_comprobante_pptal cp ON dcp.comprobantepptal = cp.id_unico "
        . "LEFT JOIN gf_tipo_comprobante_pptal tcp ON cp.tipocomprobante = tcp.id_unico  "
        . "LEFT JOIN gf_tercero tr ON tr.id_unico = dcp.tercero "
        . "WHERE rp.codi_presupuesto BETWEEN '$rubini' AND '$rubfin' "
        . "AND  cp.fecha BETWEEN '$fechaI' AND '$fechaF' AND "
        . "tr.numeroidentificacion BETWEEN '$terInicial' AND '$terFinal' "
        . "AND tcp.codigo BETWEEN '$compini' AND '$compfin'"
        . "ORDER BY Numter ASC ";
}

$rubroP1 =$mysqli->query($rubroP);
while ($rubro = mysqli_fetch_assoc($rubroP1)){
    $ter = $rubro['idter'];
    $comp = "SELECT
      tcp.codigo            as tcpcod 
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
    WHERE t.id_unico = '$ter' AND 
        rp.codi_presupuesto BETWEEN '$rubini' AND '$rubfin' AND  
        cp.fecha BETWEEN '$fechaI' AND '$fechaF' "
            . "AND tcp.codigo BETWEEN '$compini' AND '$compfin' " ;
    //$pdf->CellFitScale(100,5,utf8_decode($comp));
     $com = $mysqli->query($comp);
     $cnum=0;
     if(mysqli_num_rows($com)>0){
         while ($row1 = mysqli_fetch_array($com)) {
            $tipo = $row1['tcpcod'];
            $comprobanteI= "SELECT codigo as cod FROM gf_tipo_comprobante_pptal WHERE codigo ='$tipo' "
                . "AND (clasepptal ='13' OR clasepptal ='14' OR clasepptal ='15' OR clasepptal ='16' OR clasepptal ='17')";
            $comprobanteI = $mysqli->query($comprobanteI);
            if(mysqli_num_rows($comprobanteI)>0){ 
                $cnum=$cnum+1;
            }
          }
          if($cnum>0){
              
              
              
              
              ?>
          <tr>
            <td colspan="8" ><strong><i><?php echo 'Tercero: '.mb_strtoupper($rubro['Numter']).' - '. ucwords(mb_strtolower($rubro['NOMBRE']))?></i></strong></td>
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
      tr.numeroidentificacion as numTer, 
      dcp.comprobanteafectado as comAfec, 
      tcp.codigo as codTipo 
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
    WHERE 
        rp.codi_presupuesto BETWEEN '$rubini' AND '$rubfin' AND  
        cp.fecha BETWEEN '$fechaI' AND '$fechaF' "
            . "AND tcp.codigo BETWEEN '$compini' AND '$compfin'"
              . "ORDER BY cp.fecha asc";
     $total1=0;
      $total2=0;
    $con1 = $mysqli->query($con);
    while ($row = mysqli_fetch_array($con1)) {
        $tipo = $row['tcpcod'];
        $comprobanteI= "SELECT codigo as cod FROM gf_tipo_comprobante_pptal WHERE codigo ='$tipo' "
                . "AND (clasepptal ='13' OR clasepptal ='14' OR clasepptal ='15' OR clasepptal ='16' OR clasepptal ='17')";
        $comprobanteI = $mysqli->query($comprobanteI);
   if(mysqli_num_rows($comprobanteI)>0){

    if($row['numTer'] ==$rubro['Numter']){
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
        ##AFECTADO ##
        $commA = $row['comAfec'];
        $comm= "SELECT DISTINCT
            tcp.codigo as codigoA,
            cp.numero as numeroA,
            cp.descripcion as descripA 
          FROM
            gf_detalle_comprobante_pptal dcp
          LEFT JOIN
            gf_comprobante_pptal cp ON dcp.comprobantepptal = cp.id_unico
          LEFT JOIN
            gf_tipo_comprobante_pptal tcp ON cp.tipocomprobante = tcp.id_unico
          WHERE
            tcp.tipooperacion = '1' AND dcp.id_unico = '$commA'";
        $comm = $mysqli->query($comm);
        if(mysqli_num_rows($comm)>0){
            $comAfec = mysqli_fetch_array($comm);
            $comprobanteAfectado = mb_strtoupper($comAfec['codigoA']).' - '.$comAfec['numeroA'].' - '.ucwords(mb_strtolower($comAfec['descripA']));
        } else {
          
            $comprobanteAfectado ='';
        }
        
        #TOTAL
        #SALDO
        $saldo=$total-$afectado;
        
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
            <td align="center"><?php echo mb_strtoupper($row['codTipo']);?></td>
            <td align="center"><?php echo $dat;?></td>
            <td align="right"><?php echo $row['rpcodp'];?></td>
            <td align="right"><?php echo ucwords(mb_strtolower($row['rpnom']));?></td>
            <td align="left"><?php echo ucwords(mb_strtolower($comprobanteAfectado));?></td>
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
    ?>
    <tr>
        <td colspan="6" ><strong><i><?php echo 'TOTALES';?></i></strong></td>
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
