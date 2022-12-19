<?php
##############################################################################################################
#                           INFORME CUENTA POR PAGAR
#                                   Excel
##############################################################################################################
#28/04/2018 | Erica G.   | Creado
##############################################################################################################

header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Informe_Cuentas_Pagar.xls");

require_once('../estructura_apropiacion.php');
require_once('../estructura_saldo_obligacion.php');
require_once('../numeros_a_letras.php');
require'../fpdf/fpdf.php';
require'../Conexion/conexion.php';
session_start();
ini_set('max_execution_time', 0);
ob_start();
$meses = array('no', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
#********Comprobante pptal ********#
$compp = "SELECT id_unico, numero, tipocomprobante , DATE_FORMAT(fecha,'%d/%m/%Y') FROM gf_comprobante_pptal WHERE id_unico=".$_SESSION['id_comp_pptal_CP'];
$compp = $mysqli->query($compp);
$compp = mysqli_fetch_row($compp);
$tipocomprobantepptal  = $compp[2];
$fechaComprobante = $compp[3];


$sqlComp = "SELECT
  compCnt.id_unico,
  compCnt.numero,
  compCnt.fecha,
  compCnt.descripcion,
  compCnt.fecha,
  compCnt.tipocomprobante,
  tipCom.sigla,
  tipCom.nombre,
  compCnt.tercero,
  compCnt.clasecontrato,
  compCnt.numerocontrato,
  clasec.nombre
FROM
  gf_comprobante_cnt compCnt
LEFT JOIN
  gf_tipo_comprobante tipCom ON compCnt.tipocomprobante = tipCom.id_unico
LEFT JOIN
  gf_clase_contrato clasec ON compCnt.clasecontrato = clasec.id_unico
LEFT JOIN 
  gf_tipo_contrato tcon ON tcon.id_unico = clasec.tipocontrato
WHERE
  compCnt.tipocomprobante = tipCom.id_unico AND compCnt.id_unico = " . $_SESSION['idCompCntV'];

$comp = $mysqli->query($sqlComp);
$rowComp = mysqli_fetch_array($comp);
$idComp = $rowComp[0];
$nomcomp = $rowComp[1]; //Número de comprobante      
$fechaComp = $rowComp[2]; //Fecha       
$descripcion = $rowComp[3]; //Descripción  
$fechaVen = $rowComp[4]; //Fecha de vencimiento  
$tipocomprobante = $rowComp[5]; //id tipo comprobante  
$codigo = $rowComp[6]; //Código de tipo comprobante  
$nombre = $rowComp[7]; //Nombre de tipo comprobante  
$terceroComp = intval($rowComp[8]); //Tercero del comprobante
$claseContra = $rowComp[9];
$numroContra = $rowComp[10];
$claseContraNom = ucwords(mb_strtolower($rowComp[11]));    

$sqlTerc = 'SELECT nombreuno, nombredos, apellidouno, apellidodos, numeroidentificacion, razonsocial  
      FROM gf_tercero
      WHERE id_unico = ' . $terceroComp;

$terc = $mysqli->query($sqlTerc);
$rowT = mysqli_fetch_array($terc);

$razonSoc = $rowT[0] . ' ' . $rowT[1] . ' ' . $rowT[2] . ' ' . $rowT[3] . ' ' . $rowT[5];
$nit = $rowT[4];

$compania = $_SESSION['compania'];
$sqlRutaLogo = 'SELECT ter.ruta_logo, ciu.nombre 
  FROM gf_tercero ter 
  LEFT JOIN gf_ciudad ciu ON ter.ciudadidentificacion = ciu.id_unico 
  WHERE ter.id_unico = ' . $compania;
$rutaLogo = $mysqli->query($sqlRutaLogo);
$rowLogo = mysqli_fetch_array($rutaLogo);
$ruta = $rowLogo[0];
$ciudadCompania = $rowLogo[1];

$sqlValorTot = 'SELECT  SUM(valor) '
        . 'FROM gf_detalle_comprobante dc '
        . 'WHERE naturaleza=2  AND valor >0 AND dc.comprobante = ' . $_SESSION['idCompCntV'];
$valortotDet = $mysqli->query($sqlValorTot);
$rowVTD = mysqli_fetch_array($valortotDet);
$totalValorDet = $rowVTD[0];
##COMPAÑIA##   
$compania = $_SESSION['compania'];
$sqlRutaLogo = 'SELECT
  ter.ruta_logo,
  ciu.nombre,
  ter.razonsocial,
  ter.numeroidentificacion,
  ter.digitoverficacion
FROM
  gf_tercero ter
LEFT JOIN
  gf_ciudad ciu ON ter.ciudadidentificacion = ciu.id_unico
WHERE
  ter.id_unico =' . $compania;
$rutaLogo = $mysqli->query($sqlRutaLogo);
$rowLogo = mysqli_fetch_row($rutaLogo);
$ruta = $rowLogo[0];
$ciudadCompania = $rowLogo[1];
$nombre_com = $rowLogo[2];
if (empty($rowLogo[4])) {
    $numICompañia = $rowLogo[3];
} else {
    $numICompañia = $rowLogo[3] . '-' . $rowLogo[4];
}
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Informe Cuentas Por Pagar</title>
</head>
<body>
<table width="100%" border="1" cellspacing="0" cellpadding="0">
  <tr>
    <td colspan="6"><CENTER><strong><?php echo mb_strtoupper($nombre_com);?>
      <br/>&nbsp;<?php echo 'NIT:' . $numICompañia;?>
      <br/>&nbsp;<?php echo mb_strtoupper($nombre) . ' ' . 'No: ' . $nomcomp;?>
      <br/>&nbsp;<?php echo 'Fecha:'.$fechaComp;?>
      <br/>&nbsp;
    </strong></CENTER></td>
  </tr>
  <tr>
    <td align="left"></td>
  </tr>
  <tr>
    <td colspan="6"><center><strong> <br/>&nbsp;1.DATOS DEL BENEFICIARIO <br/>&nbsp;</strong></center></td>
  </tr>
  <tr>
    <td colspan="6"><STRONG>Nombre: </STRONG><?php echo ucwords(mb_strtolower($razonSoc));?></td>
  </tr>
  <tr>
    <td colspan="3"><strong>CC. o NIT: </strong><?php echo $nit;?></td>
    <?php 
    $telef = "";
    $tel = "SELECT valor FROM gf_telefono WHERE tercero = $terceroComp";
    $tel = $mysqli->query($tel);
    if(mysqli_num_rows($tel)>0) { 
      while ($row = mysqli_fetch_row($tel)) {
          $telef =$telef." - ".$row[0];
      }
    }?>
    <td colspan="3"><strong>Teléfonos: </strong><?php echo $telef;?></td>
  </tr>
  <tr>
    <?php 
    $direc = "";
    $dir = "SELECT CONCAT(d.direccion, '  ', c.nombre) "
         . "FROM gf_direccion d LEFT JOIN gf_ciudad c ON c.id_unico = d.ciudad_direccion "
         . "WHERE d.tercero = $terceroComp";
    $dir = $mysqli->query($dir);
    if(mysqli_num_rows($dir)>0) {   
      while ($rowD = mysqli_fetch_row($dir)) {
          $direc =$direc." - ".$rowD[0];
      }
    }
    ?>
    <td colspan="6"><strong>Dirección: </strong><?php echo $direc;?></td>
  </tr>
  <tr>
    <td colspan="6"><strong>Descripción: </strong><?php echo $descripcion;?></td>
  </tr><tr>
    <td colspan="3"><strong>Tipo Contato: </strong><?php echo $claseContraNom;?></td>
    <td colspan="3"><strong>N° de Contato: </strong><?php echo $numroContra;?></td>
  </tr>
  <?php 
  if ($totalValorDet < 0) {
      $totalValorDet = $totalValorDet * -1;
  } else {
      $totalValorDet = $totalValorDet;
  }?>
  <tr>
    <td colspan="3"><strong>N° de Documento: </strong><?php echo $nomcomp;?></td>
    <td colspan="3"><strong>Valor: </strong><?php echo '  $ '. number_format($totalValorDet, 2, '.', ',');?></td>
  </tr>

  <tr>
    <td colspan="6"><center><strong> <br/>&nbsp;2.MOVIMIENTO PRESUPUESTAL <br/>&nbsp;</strong></center></td>
  </tr>
  <tr>
    <td><strong><center>Disponibilidad Presupuestal</center></strong></td>
    <td><strong><center>Registro Presupuestal</center></strong></td>
    <td><strong><center>Código</center></strong></td>
    <td><strong><center>Nombre Rubro</center></strong></td>
    <td><strong><center>Fuente</center></strong></td>
    <td><strong><center>Valor</center></strong></td>
  </tr>
    <?php 

    $sqlDetallPptal = "SELECT DISTINCT
          cpop.numero,
          cpr.numero,
          cpd.numero,
          dcpcx.valor,
          rpptal.codi_presupuesto,
          rpptal.nombre, 
          f.id_unico, 
          f.nombre, rf.id_unico, dcpcx.id_unico  
        FROM
          gf_comprobante_cnt cn
        LEFT JOIN
          gf_detalle_comprobante dc ON cn.id_unico = dc.comprobante
        LEFT JOIN
          gf_detalle_comprobante_pptal dcpcx ON dc.detallecomprobantepptal = dcpcx.id_unico
        LEFT JOIN
          gf_comprobante_pptal cpcx ON dcpcx.comprobantepptal = cpcx.id_unico
        LEFT JOIN
          gf_tipo_comprobante_pptal tccx ON tccx.id_unico = cpcx.tipocomprobante
        LEFT JOIN
          gf_detalle_comprobante_pptal dcpop ON dcpcx.comprobanteafectado = dcpop.id_unico
        LEFT JOIN
          gf_comprobante_pptal cpop ON dcpop.comprobantepptal = cpop.id_unico
        LEFT JOIN
          gf_tipo_comprobante_pptal tcop ON cpop.tipocomprobante = tcop.id_unico
        LEFT JOIN
          gf_detalle_comprobante_pptal dcpr ON dcpop.comprobanteafectado = dcpr.id_unico
        LEFT JOIN
          gf_comprobante_pptal cpr ON dcpr.comprobantepptal = cpr.id_unico
        LEFT JOIN
          gf_tipo_comprobante_pptal tcr ON cpr.tipocomprobante = tcr.id_unico
        LEFT JOIN
          gf_detalle_comprobante_pptal dcpd ON dcpr.comprobanteafectado = dcpd.id_unico
        LEFT JOIN
          gf_comprobante_pptal cpd ON dcpd.comprobantepptal = cpd.id_unico
        LEFT JOIN
          gf_tipo_comprobante_pptal tcd ON cpd.tipocomprobante = tcd.id_unico
        LEFT JOIN
          gf_rubro_fuente rf ON dcpop.rubrofuente = rf.id_unico
        LEFT JOIN
          gf_rubro_pptal rpptal ON rf.rubro = rpptal.id_unico
        LEFT JOIN
          gf_fuente f ON rf.fuente = f.id_unico
        WHERE
          dc.detallecomprobantepptal IS NOT NULL AND cn.id_unico=" . $_SESSION['idCompCntV'];

        $detallePtal = $mysqli->query($sqlDetallPptal);


        $totalValor1 = 0;
        while ($rowDetallPtal = mysqli_fetch_array($detallePtal)) {

            if (empty($rowDetallPtal[2])) {
                $numComPtalDisponibilidad = $rowDetallPtal[1];
                $numComPtalRegistro = $rowDetallPtal[0];
            } else {
                $numComPtalDisponibilidad = $rowDetallPtal[2];
                $numComPtalRegistro = $rowDetallPtal[1];
            }

            $fuente = $rowDetallPtal[7];
            //$detPtal = $rowDetallPtal[3];
            $idRubroFuen = $rowDetallPtal[8];
            $codRubro = $rowDetallPtal[4];
            $nomRubro = $rowDetallPtal[5];
            $halla = 0;
            $totalValor1 += $rowDetallPtal[3];
            $saldoDisponible = apropiacion($idRubroFuen) - disponibilidades($idRubroFuen);
            
             
            echo '<tr>';
            echo '<td>'.$numComPtalDisponibilidad.'</td>';
            echo '<td>'.$numComPtalRegistro.'</td>';
            echo '<td>'.$codRubro.'</td>';
            echo '<td>'.ucwords(mb_strtolower($nomRubro)).'</td>';
            echo '<td>'.ucwords(($fuente)).'</td>';
            echo '<td>'.number_format($rowDetallPtal[3], 2, '.', ',').'</td>';
            echo '</tr>';
            
        }
       
        echo '<tr>';
        echo '<td colspan="5"><strong>Total</strong></td>';
        echo '<td ><strong>'.number_format($totalValor1, 2,'.', ',').'</strong></td>';
        echo '</tr>';

    ?>
  <tr>
    <td colspan="6"><center><strong> <br/>&nbsp;3.MOVIMIENTO FINANCIERO Y CONTABLE <br/>&nbsp;</strong></center></td>
  </tr>
 <tr>
    <td><strong><center>Cuenta</center></strong></td>
    <td colspan="2"><strong><center>Nombre Cuenta</center></strong></td>
    <td><strong><center>Tercero</center></strong></td>
    <td><strong><center>Débito</center></strong></td>
    <td><strong><center>Crédito</center></strong></td>
  </tr>
  <?php 
            
            $comp = $_SESSION['idCompCntV'];
            $sqlMovFina = "SELECT DISTINCT detComp.id_unico idDetalleComp, detComp.valor valorDetalle, 
                cuen.nombre nombreCuenta, cuen.codi_cuenta codigoCuenta, 
                cuen.naturaleza naturalezaCuenta, IF( CONCAT_WS(' ',
                                                    tr.nombreuno,
                                                    tr.nombredos,
                                                    tr.apellidouno,
                                                    tr.apellidodos
                                                  ) IS NULL OR CONCAT_WS(' ',
                                                    tr.nombreuno,
                                                    tr.nombredos,
                                                    tr.apellidouno,
                                                    tr.apellidodos) = '',
                                                  (tr.razonsocial),
                                                  CONCAT_WS(' ',
                                                    tr.nombreuno,
                                                    tr.nombredos,
                                                    tr.apellidouno,
                                                    tr.apellidodos )) AS NOMBRE,
                                                    claseC.nombre 
                FROM gf_detalle_comprobante detComp 
                LEFT JOIN gf_centro_costo cc ON detComp.centrocosto = cc.id_unico 
                LEFT JOIN gf_tercero tr ON detComp.tercero = tr.id_unico 
            LEFT JOIN gf_retencion ret ON ret.comprobante = detComp.comprobante
            LEFT JOIN gf_cuenta cuen ON cuen.id_unico = detComp.cuenta
            LEFT JOIN gf_clase_cuenta claseC ON claseC.id_unico = cuen.clasecuenta
            WHERE detComp.comprobante = $comp AND cuen.nombre IS NOT NULL";
            $movimientoFinanciero = $mysqli->query($sqlMovFina);
            $totalDebito = 0;
            $totalCredito = 0;

            while ($rowMF = mysqli_fetch_array($movimientoFinanciero)) {
                
                $debito = 0;
                $credito = 0;
                $cod = $rowMF[3];

                $nombCuen = mb_strtolower($rowMF[2], 'utf-8');
                $nombCuen = ucwords($nombCuen);

                $centroCost = mb_strtolower($rowMF[5], 'utf-8');
                $centroCost = ucwords($centroCost);
                if ($rowMF[4] == 1) {
                    if ($rowMF[1] >= 0) {
                        $debito = $rowMF[1];
                    } else {
                        $debito = '0.00';
                    }
                } else if ($rowMF[4] == 2) {
                    if ($rowMF[1] <= 0) {
                        $x = ($rowMF[1] * -1);
                        $debito = $x;
                    } else {
                        $debito = 0;
                    }
                }

                if ($rowMF[4] == 1) {
                    if ($rowMF[1] >= 0) {

                        $credito = '0.00';
                    } else {
                        $x = ($rowMF[1] * -1);
                        $credito = $x;
                    }
                } else if ($rowMF[4] == 2) {
                    if ($rowMF[1] <= 0) {
                        $credito = '0.00';
                    } else {

                        $credito = $rowMF[1];
                    }
                }


                if (strcasecmp($rowMF[6], 'pasivo general') || strcasecmp($rowMF[6], 'cuentas por pagar')) {
                    $totalValor = $rowMF[1];
                }

                $totalDebito += $debito;
                $totalCredito += $credito;
                
                echo '<tr>';
                echo '<td>'.$cod.'</td>';
                echo '<td colspan="2">'.ucwords(mb_strtolower($nombCuen)).'</td>';
                echo '<td>'.ucwords(mb_strtolower($centroCost)).'</td>';
                echo '<td>'.number_format($debito, 2, '.', ',').'</td>';
                echo '<td>'.number_format($credito, 2, '.', ',').'</td>';
                echo '</tr>';
            }
            echo '<tr>';
            echo '<td colspan="4"><strong>Totales</strong></td>';
            echo '<td ><strong>'.number_format($totalDebito, 2, '.', ',').'</strong></td>';
            echo '<td ><strong>'.number_format($totalCredito, 2, '.', ',').'</strong></td>';
            echo '</tr>';
    #########################################################################################################
    #VALIDAR SI EL TIPO DE COMPROBANTE CNT APLICA RETENCIÓN
    $tc = "SELECT
      tc.retencion
    FROM
      gf_comprobante_cnt cn
    LEFT JOIN
      gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico
    WHERE
      cn.id_unico = $comp";
    $tc1 =$mysqli->query($tc);
    if(mysqli_num_rows($tc1)>0){
        $r = mysqli_fetch_row($tc1);
        $ret=$r[0];
    } else {
        $ret=0;
    }
    if($ret==1){
      $sqlR = "SELECT tpr.nombre,tpr.porcentajeaplicar,rt.valorretencion,rt.retencionbase 
      FROM gf_retencion rt 
      LEFT JOIN gf_tipo_retencion tpr ON tpr.id_unico = rt.tiporetencion 
      LEFT JOIN gf_comprobante_cnt cnt ON rt.comprobante = cnt.id_unico 
      WHERE cnt.id_unico=$idComp";

      $resultR = $mysqli->query($sqlR);
      $valorR = 0;
      if(mysqli_num_rows($resultR)>0){ ?>
       <tr>
        <td colspan="6"><center><strong> <br/>&nbsp;4.RETENCIÓN Y DESCUENTOS <br/>&nbsp;</strong></center></td>
      </tr>
      <tr>
        <td colspan="3"><strong><center>Tipo Retención</center></strong></td>
        <td><strong><center>Porcentaje</center></strong></td>
        <td><strong><center>Valor Retención</center></strong></td>
        <td><strong><center>Retención Base</center></strong></td>
      </tr>

        <?php while($f= mysqli_fetch_row($resultR)){
                echo '<tr>';
                echo '<td colspan="3">'.$f[0].'</td>';
                echo '<td>'.ucwords(mb_strtolower($f[1])).'</td>';
                echo '<td>'.number_format($f[2], 2, '.', ',').'</td>';
                echo '<td>'.number_format($f[3], 2, '.', ',').'</td>';
                echo '</tr>';
        }

      }
    }

$sqlValorT = "SELECT SUM(valor) 
FROM
  gf_detalle_comprobante dc
LEFT JOIN 
  gf_cuenta c ON dc.cuenta = c.id_unico 
WHERE
  (c.clasecuenta = 4 OR c.clasecuenta=8) AND valor >0 AND dc.comprobante =" . $_SESSION['idCompCntV'];
$ValorT = $mysqli->query($sqlValorT);
if (mysqli_num_rows($ValorT) > 0) {
    $ValorT = mysqli_fetch_row($ValorT);
    if (empty($ValorT[0])) {
        $ValorT = 0.00;
    } else {
        $ValorT = $ValorT[0];
    }
} else {
    $ValorT = 0.00;
}
$valorLetras = numtoletras($ValorT);
        echo '<tr>';
        echo '<td colspan="6"><strong> <br/>&nbsp;Total: $'.number_format($ValorT, 2, '.', ',').'</strong> <br/>&nbsp;</td>';
        echo '</tr>';
        echo '<tr>';
        echo '<td colspan="6"><strong> <br/>&nbsp;Valor en letras:  <br/>&nbsp;</strong>'.$valorLetras.'</td>';
        echo '</tr>';

  
  ?>

 


