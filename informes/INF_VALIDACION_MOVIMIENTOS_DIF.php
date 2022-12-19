<?php
require_once '../Conexion/conexion.php';
setlocale(LC_ALL,"es_ES");
date_default_timezone_set("America/Bogota");
@session_start();
$anno   = $_SESSION['anno'];
$sqlAnno="SELECT anno FROM gf_parametrizacion_anno
WHERE id_unico=$anno";
$resultAnno = $mysqli->query($sqlAnno);
$rowAnno = $resultAnno->fetch_assoc();
$annoNum = $rowAnno['anno'];
$tipo=$_GET['tipo'];

if ($tipo==1) {
    # code...

    require'../fpdf/fpdf.php';
    ob_start();
    class PDF extends FPDF
    { 
        function Header(){ 
            global $annoNum;
            $this->SetY(10);
            $this->SetFont('Arial','B',10);
            $this->Cell(200,5,utf8_decode('INFORME DE VALIDACIÓN DE COMPROBANTES '),0,0,'C');
            $this->ln(5);
        }    
             
    }
    $pdf = new PDF('P','mm','Letter');   
    $pdf->AddPage();
    $pdf->AliasNbPages();
    $pdf->SetY(20);
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(195,9,utf8_decode("Parametrización Movimiento Igual a Compañia y  Parametrización Cuenta Diferente a Compañia"),1,0,'C');
    $pdf->Ln();
    $pdf->Cell(30,9,"Tipo Movimiento",1,0,'C');
	$pdf->Cell(20,9,utf8_decode("Número"),1,0,'C');
	$pdf->Cell(20,9,"Fecha",1,0,'C');
	$pdf->Cell(50,9,utf8_decode("Parametrización Movimiento"),1,0,'C');
	$pdf->Cell(25,9,"Cod Cuenta",1,0,'C');
    $pdf->Cell(50,9,utf8_decode("Parametrización Cuenta"),1,0,'C'); 
    $pdf->Ln();
    $sqlVal="SELECT CONCAT(tc.sigla,'-',tc.nombre),c.numero,c.fecha,CONCAT(pa.anno,'-',terp.razonsocial) AS parame_movimiento,
             cu.codi_cuenta,CONCAT(pac.anno,'-',terc.razonsocial) as parame_cuenta 
             FROM gf_comprobante_cnt  c
             LEFT JOIN gf_detalle_comprobante  dc ON dc.comprobante=c.id_unico
             LEFT JOIN gf_cuenta  cu ON cu.id_unico=dc.cuenta
             LEFT JOIN gf_tipo_comprobante tc ON tc.id_unico=c.tipocomprobante
             LEFT JOIN gf_parametrizacion_anno pa ON pa.id_unico=c.parametrizacionanno
             LEFT JOIN gf_tercero terp ON terp.id_unico=pa.compania
             LEFT JOIN gf_parametrizacion_anno pac ON pac.id_unico=cu.parametrizacionanno
             LEFT JOIN gf_tercero terc ON terc.id_unico=pac.compania
             WHERE c.parametrizacionanno=$anno 
             AND cu.parametrizacionanno<>$anno 
             GROUP BY c.id_unico";
    $resultIE=$mysqli->query($sqlVal);
    if (mysqli_num_rows($resultIE) > 0) {
      while ($rowIE=mysqli_fetch_row($resultIE)) {
         $pdf->SetFont('Arial','',10);
         $pdf->CellFitScale(30,8,utf8_decode($rowIE[0]),1,0,'R');
         $pdf->CellFitScale(20,8,utf8_decode($rowIE[1]),1,0,'R');
         $pdf->CellFitScale(20,8,utf8_decode($rowIE[2]),1,0,'R');
         $pdf->CellFitScale(50,8,utf8_decode($rowIE[3]),1,0,'R');
         $pdf->CellFitScale(25,8,utf8_decode($rowIE[4]),1,0,'R');
         $pdf->CellFitScale(50,8,utf8_decode($rowIE[5]),1,0,'R');
         $pdf->Ln();
        }
    }else{
        $pdf->SetFont('Arial','',10);
        $pdf->Cell(195,9,utf8_decode("No hay Información"),1,0,'C');
    }
        // Inversa
        $pdf->Ln();
        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(195,9,utf8_decode("Parametrización Movimiento Diferente a Compañia y  Parametrización Cuenta Igual a Compañia"),1,0,'C');
        $pdf->Ln();
        $pdf->Cell(30,9,"Tipo Movimiento",1,0,'C');
        $pdf->Cell(20,9,utf8_decode("Número"),1,0,'C');
        $pdf->Cell(20,9,"Fecha",1,0,'C');
        $pdf->Cell(50,9,utf8_decode("Parametrización Movimiento"),1,0,'C');
        $pdf->Cell(25,9,"Cod Cuenta",1,0,'C');
        $pdf->Cell(50,9,utf8_decode("Parametrización Cuenta"),1,0,'C'); 
        $pdf->Ln();
        $sqlVal="SELECT CONCAT(tc.sigla,'-',tc.nombre),c.numero,c.fecha,CONCAT(pa.anno,'-',terp.razonsocial) AS parame_movimiento,
                 cu.codi_cuenta,CONCAT(pac.anno,'-',terc.razonsocial) as parame_cuenta 
                 FROM gf_comprobante_cnt  c
                 LEFT JOIN gf_detalle_comprobante  dc ON dc.comprobante=c.id_unico
                 LEFT JOIN gf_cuenta  cu ON cu.id_unico=dc.cuenta
                 LEFT JOIN gf_tipo_comprobante tc ON tc.id_unico=c.tipocomprobante
                 LEFT JOIN gf_parametrizacion_anno pa ON pa.id_unico=c.parametrizacionanno
                 LEFT JOIN gf_tercero terp ON terp.id_unico=pa.compania
                 LEFT JOIN gf_parametrizacion_anno pac ON pac.id_unico=cu.parametrizacionanno
                 LEFT JOIN gf_tercero terc ON terc.id_unico=pac.compania
                 WHERE c.parametrizacionanno<>$anno 
                 AND cu.parametrizacionanno==$anno 
                 GROUP BY c.id_unico";
        $resultIE=$mysqli->query($sqlVal);
        if (mysqli_num_rows($resultIE) > 0) {
          while ($rowIE=mysqli_fetch_row($resultIE)) {
             $pdf->SetFont('Arial','',10);
             $pdf->CellFitScale(30,8,utf8_decode($rowIE[0]),1,0,'R');
             $pdf->CellFitScale(20,8,utf8_decode($rowIE[1]),1,0,'R');
             $pdf->CellFitScale(20,8,utf8_decode($rowIE[2]),1,0,'R');
             $pdf->CellFitScale(50,8,utf8_decode($rowIE[3]),1,0,'R');
             $pdf->CellFitScale(25,8,utf8_decode($rowIE[4]),1,0,'R');
             $pdf->CellFitScale(50,8,utf8_decode($rowIE[5]),1,0,'R');
             $pdf->Ln();
            }
        }else{
            $pdf->SetFont('Arial','',10);   
            $pdf->Cell(195,9,utf8_decode("No hay Información"),1,0,'C');
        }
      
    ob_end_clean();     
    $pdf->Output(0,'IE_CIE.pdf',0);
}elseif($tipo==2){
    header("Content-type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=Informe_Validacion_Movimientos.xls");
    require_once("../Conexion/conexion.php");
    @session_start();
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
    ?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">
    <html>
        <head>
            <title>Informe Instituciones Sin CIE</title>
        </head>
        <body>
        <table width="100%" border="1" cellspacing="0" cellpadding="0">
            <thead>
                <tr>
                    <th colspan="6" bgcolor="skyblue">
                        <?php echo utf8_decode("INFORME DE VALIDACIÓN DE COMPROBANTES CONSOLIDADOS"); ?>
                    </th>
                </tr>
                <tr>
                          <th colspan="6"   >
                           <?php echo utf8_decode("Parametrización Movimiento Igual a Compañia y  Parametrización Cuenta Diferente a Compañia"); ?>
                          </th>
                          </tr>
                <tr>
                    <th align="center"> <?php echo utf8_decode("TIPO MOVIMIENTO"); ?></th>
                    <th align="center"> <?php echo utf8_decode("NHUMERO MOVIMIENTO"); ?></th>
                    <th align="center"> <?php echo utf8_decode("FECHA MOVIMIENTO"); ?></th>
                    <th align="center"> <?php echo utf8_decode("PARAMETRIZACIÓN MOVIMIENTO"); ?></th>
                    <th align="center"> <?php echo utf8_decode("CODIGO CUENTA"); ?></th>
                    <th align="center"> <?php echo utf8_decode("PARAMETRIZACIÓN CUENTA"); ?></th>
                </tr>
            </thead>
            <tbody>
            <?php
            $sqlIE="SELECT CONCAT(tc.sigla,'-',tc.nombre),c.numero,c.fecha,CONCAT(pa.anno,'-',terp.razonsocial) AS parame_movimiento,
                    cu.codi_cuenta,CONCAT(pac.anno,'-',terc.razonsocial) as parame_cuenta 
                    FROM gf_comprobante_cnt  c
                    LEFT JOIN gf_detalle_comprobante  dc ON dc.comprobante=c.id_unico
                    LEFT JOIN gf_cuenta  cu ON cu.id_unico=dc.cuenta
                    LEFT JOIN gf_tipo_comprobante tc ON tc.id_unico=c.tipocomprobante
                    LEFT JOIN gf_parametrizacion_anno pa ON pa.id_unico=c.parametrizacionanno
                    LEFT JOIN gf_tercero terp ON terp.id_unico=pa.compania
                    LEFT JOIN gf_parametrizacion_anno pac ON pac.id_unico=cu.parametrizacionanno
                    LEFT JOIN gf_tercero terc ON terc.id_unico=pac.compania
                    WHERE c.parametrizacionanno=$anno 
                    AND cu.parametrizacionanno<>$anno 
                    GROUP BY c.id_unico";
              $resultIE=$mysqli->query($sqlIE);
              while ($rowIE=mysqli_fetch_row($resultIE)) {
              
                         ?>
                         <tr>
                         <td style='text-align: left;'><?php echo utf8_decode($rowIE[0]); ?></td><br>
                         <td style='text-align: left;'><?php echo utf8_decode($rowIE[1]); ?></td><br>
                         <td style='text-align: left;'><?php echo utf8_decode($rowIE[2]); ?></td><br>
                         <td style='text-align: left;'><?php echo utf8_decode($rowIE[3]); ?></td><br>
                         <td style='text-align: left;'><?php echo utf8_decode($rowIE[4]); ?></td><br>
                         <td style='text-align: left;'><?php echo utf8_decode($rowIE[5]); ?></td><br>
                         </tr>
                         <?php
                   }
                         ?>
                          <tr>
                          </tr>
                          <tr>
                          <th colspan="6"   >
                           <?php echo utf8_decode("Parametrización Movimiento Diferente a Compañia y  Parametrización Cuenta Igual a Compañia"); ?>
                          </th>
                          </tr>
                          <tr>
                          <th align="center"> <?php echo utf8_decode("TIPO MOVIMIENTO"); ?></th>
                          <th align="center"> <?php echo utf8_decode("NHUMERO MOVIMIENTO"); ?></th>
                          <th align="center"> <?php echo utf8_decode("FECHA MOVIMIENTO"); ?></th>
                          <th align="center"> <?php echo utf8_decode("PARAMETRIZACIÓN MOVIMIENTO"); ?></th>
                          <th align="center"> <?php echo utf8_decode("CODIGO CUENTA"); ?></th>
                          <th align="center"> <?php echo utf8_decode("PARAMETRIZACIÓN CUENTA"); ?></th>
                          </tr>
                          <?php
            $sqlIE="SELECT CONCAT(tc.sigla,'-',tc.nombre),c.numero,c.fecha,CONCAT(pa.anno,'-',terp.razonsocial) AS parame_movimiento,
                    cu.codi_cuenta,CONCAT(pac.anno,'-',terc.razonsocial) as parame_cuenta 
                    FROM gf_comprobante_cnt  c
                    LEFT JOIN gf_detalle_comprobante  dc ON dc.comprobante=c.id_unico
                    LEFT JOIN gf_cuenta  cu ON cu.id_unico=dc.cuenta
                    LEFT JOIN gf_tipo_comprobante tc ON tc.id_unico=c.tipocomprobante
                    LEFT JOIN gf_parametrizacion_anno pa ON pa.id_unico=c.parametrizacionanno
                    LEFT JOIN gf_tercero terp ON terp.id_unico=pa.compania
                    LEFT JOIN gf_parametrizacion_anno pac ON pac.id_unico=cu.parametrizacionanno
                    LEFT JOIN gf_tercero terc ON terc.id_unico=pac.compania
                    WHERE c.parametrizacionanno<>$anno 
                    AND cu.parametrizacionanno=$anno 
                    GROUP BY c.id_unico";
              $resultIE=$mysqli->query($sqlIE);
              while ($rowIE=mysqli_fetch_row($resultIE)) {
              
                         ?>
                         <tr>
                         <td style='text-align: left;'><?php echo utf8_decode($rowIE[0]); ?></td><br>
                         <td style='text-align: left;'><?php echo utf8_decode($rowIE[1]); ?></td><br>
                         <td style='text-align: left;'><?php echo utf8_decode($rowIE[2]); ?></td><br>
                         <td style='text-align: left;'><?php echo utf8_decode($rowIE[3]); ?></td><br>
                         <td style='text-align: left;'><?php echo utf8_decode($rowIE[4]); ?></td><br>
                         <td style='text-align: left;'><?php echo utf8_decode($rowIE[5]); ?></td><br>
                         </tr>
                         <?php
                   }
                         ?>
                         </tbody>
                     </table>
                 </body>
                 </html>             
             



<?php
}

  


?>