<?php
#####################################################################################
# ********************************* Modificaciones *********************************#
#####################################################################################
#10/01/2019|Erica G.| Creado
#####################################################################################
require'../../Conexion/conexion.php';
require'../../Conexion/ConexionPDO.php';
require'consultas.php';
ini_set('max_execution_time', 0);
$usuario     = $_SESSION['usuario'];
$fechaActual = date('d/m/Y');
$con         = new ConexionPDO();
ob_start();

$calendario = CAL_GREGORIAN;
$parmanno   = $mysqli->real_escape_string(''.$_POST['sltAnnio'].'');
$an         = $con->Listar("SELECT anno FROM gf_parametrizacion_anno WHERE id_unico =$parmanno");
$anno       = $an[0][0]; 
$mes        = $mysqli->real_escape_string(''.$_POST['sltmes'].'');
$dia        = cal_days_in_month($calendario, $mes, $anno); 
$fecha      = $anno.'-'.$mes.'-'.$dia;
$fechaInicial = $anno.'-'.'01-01';
$fuenteI    = $mysqli->real_escape_string(''.$_POST['fuenteI'].'');
$fuenteF    = $mysqli->real_escape_string(''.$_POST['fuenteF'].'');
$meses      = array('no', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
$mesIn      = $meses[$mes].' del '.$anno;

#**** VACIAR LA TABLA TEMPORAL ***#
$vaciarTabla = 'TRUNCATE temporal_consulta_pptal_gastos ';
$mysqli->query($vaciarTabla);


#CONSULTA TODAS LA CUENTAS
$ctas = "SELECT DISTINCT
        rpp.nombre,
        rpp.codi_presupuesto,
        f.id_unico,
        rpp2.codi_presupuesto, 
        rf.id_unico, 
        rpp.tipoclase 
      FROM
        gf_rubro_pptal rpp
      LEFT JOIN
        gf_rubro_fuente rf ON rf.rubro = rpp.id_unico
      LEFT JOIN
        gf_fuente f ON rf.fuente = f.id_unico
      LEFT JOIN
        gf_rubro_pptal rpp2 ON rpp.predecesor = rpp2.id_unico 
     WHERE f.id_unico BETWEEN '$fuenteI' AND '$fuenteF' 
         AND (rpp.tipoclase = 6  OR rpp.tipoclase = 7  OR rpp.tipoclase = 9 OR rpp.tipoclase = 10) 
         AND rpp.parametrizacionanno = $parmanno 
    ORDER BY rpp.codi_presupuesto ASC";
$ctass= $mysqli->query($ctas);
#*** GUARDA LOS DATOS EN LA TABLA TEMPORAL **#
while ($row1 = mysqli_fetch_row($ctass)) {
    $insert= "INSERT INTO temporal_consulta_pptal_gastos "
            . "(cod_rubro, nombre_rubro,cod_predecesor, cod_fuente, rubro_fuente) "
            . "VALUES ('$row1[1]','$row1[0]','$row1[3]','$row1[2]','$row1[4]' )";
    $mysqli->query($insert);
}
 
$select ="SELECT DISTINCT
    rpp.nombre,
    rpp.codi_presupuesto,
    f.id_unico, 
    rpp2.codi_presupuesto, 
    dcp.rubrofuente, 
    rpp.tipoclase 
  FROM
    gf_detalle_comprobante_pptal dcp
  LEFT JOIN
    gf_rubro_fuente rf ON dcp.rubrofuente = rf.id_unico
  LEFT JOIN
    gf_rubro_pptal rpp ON rf.rubro = rpp.id_unico
  LEFT JOIN
    gf_fuente f ON rf.fuente = f.id_unico
  LEFT JOIN
    gf_rubro_pptal rpp2 ON rpp.predecesor = rpp2.id_unico 
  LEFT JOIN 
    gf_comprobante_pptal cp ON dcp.comprobantepptal = cp.id_unico 
  WHERE f.id_unico BETWEEN '$fuenteI' AND '$fuenteF' 
  AND cp.parametrizacionanno = $parmanno 
  ORDER BY rpp.codi_presupuesto ASC";
$select1 = $mysqli->query($select);

while($row = mysqli_fetch_row($select1)){
    #PRESUPUESTO INICIAL
    $pptoInicial= presupuestos($row[4], 1, $fechaInicial, $fecha);
    #ADICION
    $adicion = presupuestos($row[4], 2, $fechaInicial, $fecha);
    #REDUCCION
    $reduccion = presupuestos($row[4], 3, $fechaInicial, $fecha);
    #TRAS.CRED Y CONT.
    $tras = presupuestos($row[4], 4, $fechaInicial, $fecha);
        if($tras>0){
            $trasCredito = $tras;
            $trasCont = 0;
        }else {
            $trasCredito = 0;
            $trasCont = $tras;
        }

    #PRESUPUESTO DEFINITIVO
    $presupuestoDefinitivo = $pptoInicial+$adicion-$reduccion+$trasCredito+$trasCont;
    if($row[5]==6){
        #RECAUDOS
        $presupuestoDefinitivoI = $presupuestoDefinitivo;
        $presupuestoDefinitivoG = 0;
        $recaudos  = disponibilidades($row[4], 18, $fechaInicial, $fecha);
        $registros = 0;
    } else {
        $presupuestoDefinitivoI =0;
        $presupuestoDefinitivoG = $presupuestoDefinitivo;
        $recaudos  = 0;
        #REGISTROS
        $registros = disponibilidades($row[4], 15, $fechaInicial, $fecha);
    }
    #ACTUALIZAR TABLA CON DATOS HALLADOS
    $update="UPDATE temporal_consulta_pptal_gastos SET 
            presupuesto_dfvo = '$presupuestoDefinitivoG', 
            ptto_inicial =  '$presupuestoDefinitivoI', 
            registros = '$registros', 
            recaudos = '$recaudos'
            WHERE rubro_fuente = '$row[4]'";
    $update = $mysqli->query($update); 
}  

#   ************   Datos Compañia   ************    #
$compania = $_SESSION['compania'];
$rowC = $con->Listar("SELECT 	ter.id_unico,
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
WHERE ter.id_unico = $compania");
$razonsocial = $rowC[0][1];
$nombreIdent = $rowC[0][2];
$numeroIdent = $rowC[0][3];
$direccinTer = $rowC[0][4];
$telefonoTer = $rowC[0][5];
$ruta_logo   = $rowC[0][6]; 

$row = $con->Listar("SELECT 
	t.cod_fuente, f.nombre, 
    SUM(t.presupuesto_dfvo), SUM(t.registros), 
    SUM(t.ptto_inicial), SUM(t.recaudos) 
FROM 
	temporal_consulta_pptal_gastos t 
LEFT JOIN 
	gf_fuente f ON t.cod_fuente = f.id_unico 
GROUP BY t.cod_fuente");
#** PDF ***#
if($_GET['t']==1){
    require'../../fpdf/fpdf.php';
    class PDF extends FPDF
    {
        function Header(){ 
            global $razonsocial;
            global $nombreIdent;
            global $numeroIdent;
            global $direccinTer;
            global $telefonoTer;
            global $ruta_logo;
            global $mesIn;
            
            $this->SetFont('Arial','B',10);

            if($ruta_logo != '')
            {
              $this->Image('../../'.$ruta_logo,10,5,28);
            }
            $this->SetFont('Arial','B',10);	
            $this->MultiCell(190,5,utf8_decode($razonsocial),0,'C');		
            $this->SetX(10);
            $this->Ln(1);
            $this->Cell(190,5,utf8_decode($nombreIdent.': '.$numeroIdent),0,0,'C');
            $this->ln(5);
            $this->SetX(10);
            $this->Cell(190,5,utf8_decode('Dirección: '.$direccinTer.' Tel: '.$telefonoTer),0,0,'C');
            $this->ln(5);
            $this->SetX(10);
            $this->Cell(190,5,utf8_decode('CONCILIACION DE INGRESOS - GASTOS POR FUENTES '),0,0,'C');
            $this->Ln(5);
            $this->Cell(190,5,utf8_decode('Mes Acumulado '.$mesIn),0,0,'C');
            $this->Ln(10);
            $this->SetX(15);
            
            $this->SetFont('Arial','B',8);
            $this->Cell(40,15,utf8_decode('FUENTE'),1,0,'C');
            $this->Cell(50,5,utf8_decode('INGRESOS'),1,0,'C');
            $this->Cell(50,5,utf8_decode('GASTOS'),1,0,'C');
            $this->Cell(50,5,utf8_decode('DIFERENCIA'),1,0,'C');
            $this->Ln(5);
            $this->SetX(15);
            $x = $this->GetX();
            $y = $this->GetY();
            $this->Cell(40,0,utf8_decode(''),0,0,'C');
            $this->Cell(25,5,utf8_decode('PRESUPUESTO'),0,0,'C');
            $this->Cell(25,10,utf8_decode('RECAUDO'),0,0,'C');
            $this->Cell(25,5,utf8_decode('PRESUPUESTO'),0,0,'C');
            $this->Cell(25,5,utf8_decode('TOTAL'),0,0,'C');
            $this->Cell(25,5,utf8_decode('PRESUPUESTO'),0,0,'C');
            $this->Cell(25,5,utf8_decode('SUPERAVIT/'),0,0,'C');
            $this->Ln(5);
            $this->SetX(15);
            $this->Cell(40,0,utf8_decode(''),0,0,'C');
            $this->Cell(25,5,utf8_decode('DEFINITIVO'),0,0,'C');
            $this->Cell(25,0,utf8_decode(''),0,0,'C');
            $this->Cell(25,5,utf8_decode('DEFINITIVO'),0,0,'C');
            $this->Cell(25,5,utf8_decode('COMPROMISOS'),0,0,'C');
            $this->Cell(25,5,utf8_decode('DEFINITIVO'),0,0,'C');
            $this->Cell(25,5,utf8_decode('DEFICIT'),0,0,'C');
            $this->SetX(15);
            $this->SetXY($x, $y);
            $this->Cell(40,10, utf8_decode(''),0,0,'C');#
            $this->Cell(25,10, utf8_decode(''),1,0,'C');#
            $this->Cell(25,10, utf8_decode(''),1,0,'C');#
            $this->Cell(25,10, utf8_decode(''),1,0,'C');#
            $this->Cell(25,10, utf8_decode(''),1,0,'C');#
            $this->Cell(25,10, utf8_decode(''),1,0,'C');#
            $this->Cell(25,10, utf8_decode(''),1,0,'C');#
            $this->Ln(10);
        }      

        function Footer(){
            $this->SetY(-15);
            $this->SetFont('Arial','B',8);
            $this->SetX(10);
            $this->Cell(190,10,utf8_decode('Página '.$this->PageNo().'/{nb}'),0,0,'R');
        }
    }
    $pdf = new PDF('P','mm','Letter');   
    $nb=$pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->AliasNbPages();
    $pdf->SetFont('Arial','',8);
    for ($i = 0; $i < count($row); $i++) {
        $b =$pdf->GetY();
        if($b>240){
            $pdf->AddPage();
        }
        $fuente     = $row[$i][1];
        $pptoI      = $row[$i][4];
        $recaudos   = $row[$i][5];
        $pptoG      = $row[$i][2];
        $registros  = $row[$i][3];
        $pptoD      = $pptoI - $pptoG;
        $sup_def    = $recaudos - $registros;
        
        $pdf->SetX(15);
        $x = $pdf->GetX();
        $y = $pdf->GetY();
        $pdf->MultiCell(40,5, utf8_decode(mb_strtoupper($fuente)),0,'L');
        $y2 = $pdf->GetY();
        $alt = $y2-$y;
        $pdf->SetXY($x,$y);
        $pdf->Cell(40,$alt, utf8_decode(''),1,0,'L');
        $pdf->Cell(25,$alt, number_format($pptoI, 2, '.',','),1,0,'R');
        $pdf->Cell(25,$alt, number_format($recaudos, 2, '.',','),1,0,'R');
        $pdf->Cell(25,$alt, number_format($pptoG, 2, '.',','),1,0,'R');
        $pdf->Cell(25,$alt, number_format($registros, 2, '.',','),1,0,'R');
        $pdf->Cell(25,$alt, number_format($pptoD, 2, '.',','),1,0,'R');
        $pdf->Cell(25,$alt, number_format($sup_def, 2, '.',','),1,0,'R');
        $pdf->Ln($alt);
    }
    
    ob_end_clean();		
    $pdf->Output(0,'Conciliacion_ingresos_gastos.pdf',0);
#** Excel **#;    
} else {
    header("Content-type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=Conciliacion_ingresos_gastos.xls");?>
    <html xmlns="http://www.w3.org/1999/xhtml">
    <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Ejecución de Ingresos</title>
    </head>
    <body>
    <table width="100%" border="1" cellspacing="0" cellpadding="0">
        <tr>
            <th colspan="7" align="center"><strong>
                <br/>&nbsp;
                <br/><?php echo $razonsocial ?>
                <br/><?php echo $nombreIdent.' : '.$numeroIdent."<br/>".$direccinTer.' Tel:'.$telefonoTer ?>
               <br/>&nbsp;
               <br/>CONCILIACION DE INGRESOS - GASTOS POR FUENTES
               <br/><?php echo 'Mes Acumulado '.$mesIn ?><br/>&nbsp;</strong>
            </th>
      </tr>
      <tr>
        <td rowspan="2" align="center"><strong>FUENTE</strong></td>
        <td colspan="2" align="center"><strong>INGRESOS</strong></td>
        <td colspan="2" align="center"><strong>GASTOS</strong></td> 
        <td colspan="2" align="center"><strong>DIFERENCIA</strong></td>
    </tr>
  <tr>
        <td  align="center"><strong>PRESUPUESTO DEFINITIVO</strong></td>
        <td  align="center"><strong>RECAUDOS</strong></td>
        <td  align="center"><strong>PRESUPUESTO DEFINITIVO</strong></td>
        <td  align="center"><strong>TOTAL COMPROMISOS</strong></td>
        <td  align="center"><strong>PRESUPUESTO DEFINITIVO</strong></td>
        <td  align="center"><strong>SUPERAVIT/ DEFICIT</strong></td>
    </tr>

<?php 
    for ($i = 0; $i < count($row); $i++) {
        $fuente     = $row[$i][1];
        $pptoI      = $row[$i][4];
        $recaudos   = $row[$i][5];
        $pptoG      = $row[$i][2];
        $registros  = $row[$i][3];
        $pptoD      = $pptoI - $pptoG;
        $sup_def    = $recaudos - $registros;
        echo '<tr>';
        echo '<td>'.mb_strtoupper($fuente).'</td>';
        echo '<td>'.number_format($pptoI, 2, '.',',').'</td>';
        echo '<td>'.number_format($recaudos, 2, '.',',').'</td>';
        echo '<td>'.number_format($pptoG, 2, '.',',').'</td>';
        echo '<td>'.number_format($registros, 2, '.',',').'</td>';
        echo '<td>'.number_format($pptoD, 2, '.',',').'</td>';
        echo '<td>'.number_format($sup_def, 2, '.',',').'</td>';
        
        echo '</tr>';
    }
 ?>
</table>
</body>
</html>
<?php } ?>