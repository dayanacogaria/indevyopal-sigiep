<?php
#######################################################################################################
# ************************************   Modificaciones   ******************************************* #
#######################################################################################################
#26/07/2018 |Erica G. | Encabezados
#24/08/2017 |Erica G. | Modificacion logo, firmas
#28/06/2017 |ERICA G. | ARCHIVO CREADO
#######################################################################################################
?>
<?php
require'../../fpdf/fpdf.php';
require'../../Conexion/conexion.php';
require'consultas.php';
ini_set('max_execution_time', 0);
ob_start();
?>

<?php
$calendario = CAL_GREGORIAN;
$mesI = $mysqli->real_escape_string(''.$_POST['sltmesi'].'');
$mesF = $mysqli->real_escape_string(''.$_POST['sltmesf'].'');
$codigoI =$mysqli->real_escape_string(''.$_POST['sltcodi'].'');
$codigoF = $mysqli->real_escape_string(''.$_POST['sltcodf'].'');
$parmanno = $mysqli->real_escape_string(''.$_POST['sltAnnio'].'');
$an = "SELECT anno FROM gf_parametrizacion_anno WHERE id_unico =$parmanno";
$an = $mysqli->query($an);
$an = mysqli_fetch_row($an);
$anno =$an[0]; 
$dia = cal_days_in_month($calendario, $mesI, $anno); 
$fecha = $anno.'-'.$mes.'-'.$dia;
$meses = array('no', 'ENERO', 'FEBRERO', 'MARZO', 'ABRIL', 'MAYO', 'JUNIO', 
    'JULIO', 'AGOSTO', 'SEPTIEMBRE', 'OCTUBRE', 'NOVIEMBRE', 'DICIEMBRE');
$fechaFir= date('Y-m-d');
 $mesInicial = $meses[(int)$mesI];
 $mesFinal = $meses[(int)$mesF];
 $annoInforme = anno($parmanno);
#VACIAR LA TABLA TEMPORAL
$vaciarTabla = 'TRUNCATE temporal_consulta_pptal_gastos ';
$mysqli->query($vaciarTabla);

#CONSULTA TODAS LA CUENTAS
$ctas = "SELECT DISTINCT
            rpp.nombre,
            rpp.codi_presupuesto,
            f.id_unico,
            rpp2.codi_presupuesto, 
            rf.id_unico 
          FROM
            gf_rubro_pptal rpp
          LEFT JOIN
            gf_rubro_fuente rf ON rf.rubro = rpp.id_unico
          LEFT JOIN
            gf_fuente f ON rf.fuente = f.id_unico
          LEFT JOIN
            gf_rubro_pptal rpp2 ON rpp.predecesor = rpp2.id_unico 
            WHERE rpp.codi_presupuesto BETWEEN '$codigoI' AND '$codigoF' 
             AND rpp.tipoclase = 6 AND rpp.parametrizacionanno = $parmanno 
        ORDER BY rpp.codi_presupuesto ASC";
$ctass= $mysqli->query($ctas);
#GUARDA LOS DATOS EN LA TABLA TEMPORAL
while ($row1 = mysqli_fetch_row($ctass)) {
    $insert= "INSERT INTO temporal_consulta_pptal_gastos "
            . "(cod_rubro, nombre_rubro,cod_predecesor, cod_fuente, rubro_fuente) "
            . "VALUES ('$row1[1]','$row1[0]','$row1[3]','$row1[2]','$row1[4]' )";
    $mysqli->query($insert);
    
}
    
#CONSULTA CUENTAS DEL DETALLE SEGUN VARIABLES QUE RECIBE

$select ="SELECT DISTINCT
            rpp.nombre,
            rpp.codi_presupuesto,
            f.id_unico, 
            rpp2.codi_presupuesto, 
            dcp.rubrofuente 
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
          WHERE rpp.codi_presupuesto BETWEEN '$codigoI' AND '$codigoF' 
         AND cp.parametrizacionanno = $parmanno AND rpp.parametrizacionanno = $parmanno";

$select1 = $mysqli->query($select);

while($row = mysqli_fetch_row($select1)){
##########################################################################################################################################################################    
    $fechaInicial = $anno.'-01-01';
    $diaF = cal_days_in_month($calendario, $mesF, $anno); 
    $fechaFinal = $anno.'-'.$mesF.'-'.$diaF;    
    #PRESUPUESTO INICIAL
    $pptoInicial= presupuestos($row[4], 1, $fechaInicial, $fechaFinal);
    #ADICION
    $adicion = presupuestos($row[4], 2, $fechaInicial, $fechaFinal);
    #REDUCCION
    $reduccion = presupuestos($row[4], 3, $fechaInicial, $fechaFinal);
    #PRESUPUESTO DEFINITIVO
    $presupuestoDefinitivo = $pptoInicial+$adicion-$reduccion;
##########################################################################################################################################################################    
#   ANTERIORES
##########################################################################################################################################################################    
    $fechaIAn = $anno.'-01-01';    
    $fechaFinAn = $anno.'-'.$mesI.'-01';
    $nuevafechaAn = strtotime ( '-1 day' , strtotime ( $fechaFinAn ) ) ;
    $fechaFAn = date ( 'Y-m-d' , $nuevafechaAn );
    #RECAUDOS
    $recaudosAn = disponibilidades($row[4], 18, $fechaIAn, $fechaFAn);
    #SALDOS POR RECAUDAR
    $saldosAn = $pptoInicial-$recaudosAn;

##########################################################################################################################################################################    
#   ACTUALES
##########################################################################################################################################################################    
    $fechaIAc = $anno.'-'.$mesI.'-01';
    $diaFAc = cal_days_in_month($calendario, $mesF, $anno); 
    $fechaFinAc = $anno.'-'.$mesF.'-'.$diaFAc;
    #RECAUDOS
    $recaudosAc = disponibilidades($row[4], 18, $fechaIAc, $fechaFinAc);
    #SALDOS POR RECAUDAR
    $saldosAc = $pptoInicial-$recaudosAc;

######################################################################################################################################################
#   ACUMULADO
######################################################################################################################################################
    $fechaIAcum = $anno.'-01-01';  
    $diaFAc = cal_days_in_month($calendario, $mesF, $anno); 
    $fechaFAcum = $anno.'-'.$mesF.'-'.$diaFAc;
    #RECAUDOS
    $recaudosAcum = disponibilidades($row[4], 18, $fechaIAcum, $fechaFAcum);
    #SALDOS POR RECAUDAR
    $saldosAcum = $presupuestoDefinitivo-$recaudosAcum;
######################################################################################################################################################
###ACTUALIZAR TABLA CON DATOS HALLADOS
    $update="UPDATE temporal_consulta_pptal_gastos SET "
            . "ptto_inicial ='$pptoInicial', "
            . "adicion = '$adicion', "
            . "reduccion = '$reduccion', "
            . "presupuesto_dfvo = '$presupuestoDefinitivo', "
            . "disponibilidades ='$recaudosAn', "
            . "disponibilidad_abierta = '$recaudosAc',"
            . "recaudos = '$recaudosAcum', "
            . "registros = '$saldosAn', "
            . "registros_abiertos ='$saldosAc',"
            . "saldos_x_recaudar = '$saldosAcum' "
            . "WHERE rubro_fuente = '$row[4]'";
    $update = $mysqli->query($update);
          
}   
//#CONSULTAR LA TABLA TEMPORAL PARA HACER ACUMULADO
$acum = "SELECT id_unico, "
        . "cod_rubro,"
        . "cod_predecesor, "
        . "ptto_inicial, adicion, reduccion, "
        . "presupuesto_dfvo, "
        . "disponibilidades, disponibilidad_abierta,recaudos, "
        . "registros, registros_abiertos, saldos_x_recaudar "
        . "FROM temporal_consulta_pptal_gastos "
        . "ORDER BY cod_rubro DESC ";
$acum = $mysqli->query($acum);

while ($rowa1= mysqli_fetch_row($acum)){
    $acumd = "SELECT id_unico, "
        . "cod_rubro,"
        . "cod_predecesor, "
        . "ptto_inicial, adicion, reduccion, "
        . "presupuesto_dfvo, "
        . "disponibilidades, disponibilidad_abierta,recaudos, "
        . "registros, registros_abiertos, saldos_x_recaudar "
        . "FROM temporal_consulta_pptal_gastos WHERE id_unico ='$rowa1[0]' "
        . "ORDER BY cod_rubro DESC ";
    $acumd = $mysqli->query($acumd);
    while ($rowa= mysqli_fetch_row($acumd)){
        if(!empty($rowa[2])){
            $va11= "SELECT id_unico, "
            . "cod_rubro,"
            . "cod_predecesor, "
            . "ptto_inicial, adicion, reduccion, "
            . "presupuesto_dfvo, "
            . "disponibilidades, disponibilidad_abierta,recaudos, "
            . "registros, registros_abiertos, saldos_x_recaudar "
            . "FROM temporal_consulta_pptal_gastos WHERE cod_rubro ='$rowa[2]'";
        $va1 = $mysqli->query($va11);
        $va= mysqli_fetch_row($va1);
        $pptoInicialM = $rowa[3]+$va[3];
        $adicionM = $rowa[4]+$va[4];
        $reduccionM = $rowa[5]+$va[5];
        $presupuestoDefinitivoM = $rowa[6]+$va[6];
        $recaudosAnM =$rowa[7]+$va[7];
        $recaudosAcM = $rowa[8]+$va[8];
        $recaudosAcumM = $rowa[9]+$va[9];
        $saldosAnM = $rowa[10]+$va[10];
        $saldosAcM = $rowa[11]+$va[11];
        $saldosAcumM = $rowa[12]+$va[12];
        
        #ACTUALIZAR TABLA CON DATOS HALLADOS
         $updateA="UPDATE temporal_consulta_pptal_gastos SET "
                . "ptto_inicial ='$pptoInicialM', "
                . "adicion = '$adicionM', "
                . "reduccion = '$reduccionM', "
                . "presupuesto_dfvo = '$presupuestoDefinitivoM', "
                . "disponibilidades ='$recaudosAnM', "
                . "disponibilidad_abierta = '$recaudosAcM',"
                . "recaudos = '$recaudosAcumM', "
                . "registros = '$saldosAnM', "
                . "registros_abiertos ='$saldosAcM',"
                . "saldos_x_recaudar = '$saldosAcumM' "
                . "WHERE cod_rubro = '$rowa[2]'";
        $updateA = $mysqli->query($updateA);
        echo '<br/>';
        }
    }
}



class PDF extends FPDF
{
// Cabecera de página  
function Header()
{ 
    // Logo
    //$this->Image('logo_pb.png',10,8,33);
    //Arial bold 15
    global $nomcomp;
    global $tipodoc;
    global $numdoc;

    global $month1;
    global $month2;
    global $anno;
    global $ruta;
    
    global $mesInicial;
    global $mesFinal;
    global $annoInforme;
    
    $this->SetFont('Arial','B',10);
        // Título
    $this->SetY(10);
     if($ruta != '')
    {
        $this->Image('../../'.$ruta,60,6,20);
    }
    $this->Cell(340,5,utf8_decode($nomcomp),0,0,'C');
    // Salto de línea
    $this->setX(20);
    $this->SetFont('Arial','B',8);
    $this->Cell(320,10,utf8_decode('CÓDIGO SGC'),0,0,'R');

    $this->Ln(5);
    
    $this->SetFont('Arial','',8);
    $this->Cell(340, 5,$tipodoc.': '.$numdoc,0,0,'C'); 
    $this->SetFont('Arial','B',8);
    $this->SetX(20);
    $this->Cell(320,10,utf8_decode('VERSIÓN SGC'),0,0,'R');

    $this->Ln(5);

    $this->SetFont('Arial','',8);
    $this->Cell(340,5,utf8_decode('EJECUCION DEL PRESUPUESTO DE RENTAS E INGRESOS POR PERIODO'),0,0,'C');
    $this->SetFont('Arial','B',8);
    $this->SetX(0);
    $this->Cell(340,10,utf8_decode('FECHA SGC'),0,0,'R');
    
    $this->Ln(5);
    $this->SetFont('Arial','B',8);
    $this->Cell(340, 5,utf8_decode('DE '.$mesInicial.' A '.$mesFinal.' - '.$annoInforme),0,0,'C'); 
    $this->Ln(8);
    
    $this->SetX(10);
    $this->SetFont('Arial','B',8);
    $this->Cell(20,9,utf8_decode(''),1,0,'C');#
    $this->Cell(75,9,utf8_decode(''),1,0,'C');#
    $this->Cell(12,9,utf8_decode(''),1,0,'C');#
    $this->Cell(30,9,utf8_decode(''),1,0,'C');#
    $this->Cell(50,9,utf8_decode(''),1,0,'C');#
    $this->Cell(30,9,utf8_decode(''),1,0,'C');#
    $this->Cell(90,9,utf8_decode(''),1,0,'C');
    $this->Cell(30,9,utf8_decode(''),1,0,'C');
    
    
        $this->SetX(10);
    
    $this->Cell(20,9,utf8_decode('RUBRO'),1,0,'C');#
    $this->Cell(75,9,utf8_decode('DETALLE'),1,0,'C');#
    $this->Cell(12,9,utf8_decode('FUENTE'),1,0,'C');#
    $this->Cell(30,7,utf8_decode('PRESUPUESTO'),0,0,'C');#
    $this->Cell(50,4,utf8_decode('MODIFICACIONES'),0,0,'C');#
    $this->Cell(30,7,utf8_decode('PRESUPUESTO'),0,0,'C');#
    $this->Cell(90,4,utf8_decode('RECAUDO'),0,0,'C');#
    $this->Cell(30,7,utf8_decode('SALDOS POR'),0,0,'C');#
    
   
    $this->Ln(4);
    
    $this->SetX(10);
    
    $this->Cell(20,5,utf8_decode(''),0,0,'C');#
    $this->Cell(75,5,utf8_decode(''),0,0,'C');#
    $this->Cell(12,5,utf8_decode(''),0,0,'C');#
    $this->Cell(30,5,utf8_decode('INICIAL'),0,0,'C');#
    $this->Cell(25,5,utf8_decode('ADICIÓN'),1,0,'C');
    $this->Cell(25,5,utf8_decode('REDUCCIÓN'),1,0,'C');
    $this->Cell(30,5,utf8_decode('DEFINITIVO'),0,0,'C');
    $this->Cell(30,5,utf8_decode('ANTERIOR'),1,0,'C');#
    $this->Cell(30,5,utf8_decode('ACTUAL'),1,0,'C');#
    $this->Cell(30,5,utf8_decode('ACUMULADO'),1,0,'C');#
    $this->Cell(30,5,utf8_decode('RECAUDAR'),0,0,'C');#
    

    
    $this->Ln(5);
    $this->Cell(326,5,'',0);
    }      
    
function Footer()
    {
    // Posición: a 1,5 cm del final
    global $hoy;
    $this->SetY(-15);
    // Arial italic 8
    $this->SetFont('Arial','B',8);
    // Número de página
        $this->Cell(15);
        $this->Cell(326,10,utf8_decode('Pagina '.$this->PageNo().'/{nb}'),0,0,'R');
    }
}


// Creación del objeto de la clase heredada
$pdf = new PDF('L','mm','Legal');        

////Asingación de valor a Mes 1    
  
        
        #Igualación de Variable local a POST
        $annio = $anno;
    
    #Consulta Compañía para Encabezado
    $compania = $_SESSION['compania'];

    $consulta = "SELECT         t.razonsocial as traz,
                                t.tipoidentificacion as tide,
                                ti.id_unico as tid,
                                ti.nombre as tnom,
                                t.numeroidentificacion tnum, 
                                t.ruta_logo as ruta 
            FROM gf_tercero t
            LEFT JOIN gf_tipo_identificacion ti ON t.tipoidentificacion = ti.id_unico
            WHERE t.id_unico = $compania";

$cmp = $mysqli->query($consulta);

#Inicialización parámetros Header
$nomcomp = "";
$tipodoc = "";
$numdoc = "";
    
#Consulta para obtener parámetros Header

while ($fila = mysqli_fetch_array($cmp))
    {
        $nomcomp = ($fila['traz']);       
        $tipodoc = ($fila['tnom']);       
        $numdoc = ($fila['tnum']);   
        $ruta = ($fila['ruta']);   
    }

 #Declaración Variable Número de Páginas
$nb=$pdf->AliasNbPages();

#Creación Objeto FPDF

$pdf->AddPage();
//$pdf->SetMargins(20,20,20);

$pdf->AliasNbPages();
$pdf->SetFont('Arial','',6);


#Consulta Cuentas
$sql2 = "SELECT DISTINCT 
                        cod_rubro               as codrub, 
                        nombre_rubro            as nomrub,
                        ptto_inicial            as ppti,
                        adicion                 as adi,
                        reduccion               as red,
                        presupuesto_dfvo        as ppdf,
                        cod_fuente              as fuente , 
                        disponibilidades        as recaudosAn,
                        disponibilidad_abierta  as recaudosAc, 
                        recaudos                as recaudosAcum,
                        registros               as saldosAn, 
                        registros_abiertos      as saldosAc, 
                        saldos_x_recaudar       as saldosAcum 
        FROM temporal_consulta_pptal_gastos ORDER BY cod_rubro ASC";
$conejc  = $mysqli->query($sql2);

    $pdf->SetY(38);
    $pdf->Ln(5);
    $pdf->SetFont(Arial,'',7);
  
    
while ($filactas = mysqli_fetch_array($conejc)) 
{
    $p1  = (float) $filactas['ppti'];
    $p2  = (float) $filactas['adi'];
    $p3  = (float) $filactas['red'];
    $p4  = (float) $filactas['ppdf'];
    
    $p5  = (float) $filactas['recaudosAn'];
    $p6  = (float) $filactas['recaudosAc'];
    $p7  = (float) $filactas['recaudosAcum'];
    $p8  = (float) $filactas['saldosAn'];
    $p9  = (float) $filactas['saldosAc'];
    $p10  = (float) $filactas['saldosAcum'];
    $a = $pdf->GetY();
    if($a > 175)
    {
        #$pdf->Ln(10);
        $pdf->AddPage();
        $pdf->SetX(10);
        
    }
       # $codd = $codd + 1;
    if ($p1 == 0  && $p2 == 0  && $p3 == 0 && $p4==0 && $p5==0 && $p6==0 && $p7==0
             && $p8==0 && $p9==0 && $p10==0)
        { } else {
         #Actualización 10/02/2017   
        if($filactas['codrub']!="")
            $pdf->cellfitscale(20,4,utf8_decode($filactas['codrub']),0,0,'R');
        else
            $pdf->Cell(20,4,'',0,0,'L');
        $y = $pdf->GetY();
        $x = $pdf->GetX();        
        $pdf->MultiCell(75,4,utf8_decode($filactas['nomrub']),0,'L');
        $y2 = $pdf->GetY();
        $h = $y2-$y;
        $px = $x + 75;
        $pdf->Ln(-$h);
        $pdf->SetX($px);
        if(!empty($filactas['fuente']))
            $pdf->cellfitscale(12,4,utf8_decode($filactas['fuente']),0,0,'C');
        else
            $pdf->Cell(12,4,'',0,0,'L');        
        $pdf->cellfitscale(30,4,number_format($p1,2,'.',','),0,0,'R');
        $pdf->cellfitscale(25,4,number_format($p2,2,'.',','),0,0,'R');
        $pdf->cellfitscale(25,4,number_format($p3,2,'.',','),0,0,'R');
        $pdf->cellfitscale(30,4,number_format($p4,2,'.',','),0,0,'R');
        $pdf->cellfitscale(30,4,number_format($p5,2,'.',','),0,0,'R');
        $pdf->cellfitscale(30,4,number_format($p6,2,'.',','),0,0,'R');
        $pdf->cellfitscale(30,4,number_format($p7,2,'.',','),0,0,'R');
        $pdf->cellfitscale(30,4,number_format($p10,2,'.',','),0,0,'R');
        $pdf->Ln($h);
        #Actualización 10/02/2017
}
}
        //$pdf->SetX(30);
        $pdf->Cell(330,0.5,utf8_decode(''),1,0,'C');
        $pdf->Cell(30,4,utf8_decode($filactas['cnnom']),0,0,'C');
        
        ################################ ESTRUCTURA FIRMAS ##########################################
 ######### BUSQUEDA RESPONSABLE #########
 $pdf->SetFont('Arial','B',9);
 $pdf->Ln(15);
 $compania = $_SESSION['compania'];
 $res = "SELECT  c.nombre,rd.orden,IF(CONCAT_WS(' ',
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
                    tr.apellidodos)) AS NOMBREC, 
                 tr.numeroidentificacion, c.nombre, tr.tarjeta_profesional 
        FROM gf_responsable_documento rd 
        LEFT JOIN gf_tipo_documento td ON rd.tipodocumento = td.id_unico
        LEFT JOIN gg_tipo_relacion trel ON rd.tipo_relacion = trel.id_unico 
        LEFT JOIN gf_tipo_responsable tres ON rd.tiporesponsable = tres.id_unico 
        LEFT JOIN gf_tercero tr ON rd.tercero = tr.id_unico 
        LEFT JOIN gf_cargo_tercero ct ON ct.tercero = tr.id_unico
        LEFT JOIN gf_cargo c ON ct.cargo = c.id_unico
        WHERE LOWER(td.nombre) ='Ejecución Presupuestal Ingresos' 
        AND if(rd.fecha_inicio IS NULL, rd.fecha_inicio IS NULL, rd.fecha_inicio <= '$fechaFir') 
        AND if(rd.fecha_fin IS NULL,rd.fecha_fin IS NULL, rd.fecha_fin >= '$fechaFir') 
        AND td.compania = $compania  ORDER BY rd.orden ASC";
 $res= $mysqli->query($res);
 $i=0;
 $x=130;
 #ESTRUCTURA
$y =$pdf->GetY();
$x = $pdf->GetX();
 if(mysqli_num_rows($res)>0){  $h=4;
     $cant=mysqli_num_rows($res);
    while($F = mysqli_fetch_row($res)){
           if ($cant==3) {
               $xc=40;
            }elseif ($cant==4) {
                $xc=26;
            }elseif ($cant==2){
               $xc=80;
            }
            $pdf->SetXY($x+$xc, $y); 
            $pdf->Cell(55,0.1,'',1); 
            $pdf->Ln(2); 
            $y1 =$pdf->GetY();
            $pdf->SetXY($x+$xc, $y1); 
            $pdf->cellfitscale(45,5,utf8_decode($F[2]),0,0,'L');
            $pdf->Ln(3); 
            $y2 =$pdf->GetY();
            $pdf->SetXY($x+$xc, $y2); 
            $pdf->cellfitscale(45,5,utf8_decode($F[0]),0,0,'L');  
    
        $x = $pdf->GetX();  
     }
     
 } 
 ################################################################################################################################
 
while (ob_get_length()) {
  ob_end_clean();
}
 $pdf->Output(0,utf8_decode('Informe_Ejecuciones_Pptales_Rentas_Ingresos('.date('d-m-Y').').pdf'),0);
?>
