<?php
#####################################################################################
#     ************************** MODIFICACIONES **************************          #                                                                                                      Modificaciones
#####################################################################################
#15/08/2018 | Erica G. | Archivo Creado
#####################################################################################
require_once("../Conexion/ConexionPDO.php");
require_once("../Conexion/conexion.php");
require_once("../jsonPptal/funcionesPptal.php");
ini_set('max_execution_time', 0);
session_start();
$con        = new ConexionPDO();
$compania   = $_SESSION['compania'];
$calendario = CAL_GREGORIAN;
$parmanno   = $mysqli->real_escape_string(''.$_POST['sltAnnio'].'');
$anno       = anno($parmanno); 
$mesI       = $mysqli->real_escape_string(''.$_POST['sltmesi'].'');
$diaI       = '01';
$fechaInicial = $anno.'-'.$mesI.'-'.$diaI;
$mesF       = $mysqli->real_escape_string(''.$_POST['sltmesf'].'');
$diaF       = cal_days_in_month($calendario, $mesF, $anno); 
$fechaFinal = $anno.'-'.$mesF.'-'.$diaF;
$fechaComparar = $anno.'-'.'01-01';
$codigoI    =1;
$codigoF    =9;

$bl     = generarBalance($anno, $parmanno, $fechaInicial, $fechaFinal, $codigoI, $codigoF, $compania, 1);
#   ************   Datos Compañia   ************    #

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
$activo_c       = 0;
    $pasivo_c       = 0;
    $activo_t       = 0;
    $pasivo_t       = 0;
    $patrimonio     = 0;
    $utilidad_per   = 0;
    $gastos         = 0;
    
    $c1  = 0;
    $c2  = 0;
    $c3  = 0;
    $c4  = 0;
    $c5  = 0;
    $c6  = 0;
    $c11 = 0;
    $c13 = 0;
    $c53 = 0;
    
    $row  = $con->Listar("SELECT id_unico, naturaleza, nuevo_saldo_debito, nuevo_saldo_credito FROM temporal_balance$compania WHERE numero_cuenta = 1");
    if(count($row)>0){
        if($row[0][1]==1){
            $c1  = $row[0][2]-$row[0][3];
        } else {
            $c1  = $row[0][3]-$row[0][2];
        }
    }
    $row  = $con->Listar("SELECT id_unico, naturaleza, nuevo_saldo_debito, nuevo_saldo_credito FROM temporal_balance$compania WHERE numero_cuenta = 2");
    if(count($row)>0){
        if($row[0][1]==1){
            $c2  = $row[0][2]-$row[0][3];
        } else {
            $c2  = $row[0][3]-$row[0][2];
        }
    }
    $row  = $con->Listar("SELECT id_unico, naturaleza, nuevo_saldo_debito, nuevo_saldo_credito FROM temporal_balance$compania WHERE numero_cuenta = 3");
    if(count($row)>0){
        if($row[0][1]==1){
            $c3  = $row[0][2]-$row[0][3];
        } else {
            $c3  = $row[0][3]-$row[0][2];
        }
    }
    $row  = $con->Listar("SELECT id_unico, naturaleza, nuevo_saldo_debito, nuevo_saldo_credito FROM temporal_balance$compania WHERE numero_cuenta = 4");
    if(count($row)>0){
        if($row[0][1]==1){
            $c4  = $row[0][2]-$row[0][3];
        } else {
            $c4  = $row[0][3]-$row[0][2];
        }
    }
    $row  = $con->Listar("SELECT id_unico, naturaleza, nuevo_saldo_debito, nuevo_saldo_credito FROM temporal_balance$compania WHERE numero_cuenta = 5");
    if(count($row)>0){
        if($row[0][1]==1){
            $c5  = $row[0][2]-$row[0][3];
        } else {
            $c5  = $row[0][3]-$row[0][2];
        }
    }
    $row  = $con->Listar("SELECT id_unico, naturaleza, nuevo_saldo_debito, nuevo_saldo_credito FROM temporal_balance$compania WHERE numero_cuenta = 6");
    if(count($row)>0){
        if($row[0][1]==1){
            $c6  = $row[0][2]-$row[0][3];
        } else {
            $c6  = $row[0][3]-$row[0][2];
        }
    }
    $row  = $con->Listar("SELECT id_unico, naturaleza, nuevo_saldo_debito, nuevo_saldo_credito FROM temporal_balance$compania WHERE numero_cuenta = 11");
    if(count($row)>0){
        if($row[0][1]==1){
            $c11  = $row[0][2]-$row[0][3];
        } else {
            $c11  = $row[0][3]-$row[0][2];
        }
    }
    $row  = $con->Listar("SELECT id_unico, naturaleza, nuevo_saldo_debito, nuevo_saldo_credito FROM temporal_balance$compania WHERE numero_cuenta = 13");
    if(count($row)>0){
        if($row[0][1]==1){
            $c13  = $row[0][2]-$row[0][3];
        } else {
            $c13  = $row[0][3]-$row[0][2];
        }
    }
    $row  = $con->Listar("SELECT id_unico, naturaleza, nuevo_saldo_debito, nuevo_saldo_credito FROM temporal_balance$compania WHERE numero_cuenta = 53");
    if(count($row)>0){
        if($row[0][1]==1){
            $c53  = $row[0][2]-$row[0][3];
        } else {
            $c53  = $row[0][3]-$row[0][2];
        }
    }
    $activo_c       = $c11+$c13;
    $pasivo_c       = $c2;
    $activo_t       = $c1;
    $pasivo_t       = $c2;
    $patrimonio     = $c3;
    $utilidad_per   = $c4-$c5-$c6;
    $gastos         = $c53;
    require'../fpdf/fpdf.php';
    ob_start();
    class PDF extends FPDF
    {
        function Header(){ 
            global $razonsocial;
            global $nombreIdent;
            global $numeroIdent;
            global $direccinTer;
            global $telefonoTer;
            global $ruta_logo;
            global $numpaginas;
            global $mesI;
            global $mesF;
            global $anno;
            
            $numpaginas=$numpaginas+1;

            $this->SetFont('Arial','B',10);

            if($ruta_logo != '')
            {
              $this->Image('../'.$ruta_logo,10,5,28);
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
            $this->Cell(190,5,utf8_decode('INDICADORES FINANCIEROS'),0,0,'C');
            $this->Ln(5);
            $this->Cell(190,5,utf8_decode('Periodo Inicial '.$mesI.' Periodo Final:'.$mesF),0,0,'C');
            $this->Ln(5);
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
    $pdf->Ln(10);
    
    
    $pdf->SetFont('Arial','B',10);
    #** INDICE DE LIQUIDEZ **#
    $pdf->Cell(50,10,utf8_decode('INDICE DE LIQUIDEZ'),0,0,'L');
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(50,5,utf8_decode('ACTIVO CORRIENTE'),0,0,'C');
    $pdf->Cell(10,5,utf8_decode(' '),0,0,'C');
    $pdf->Cell(40,5, number_format($activo_c,2,',','.'),0,0,'C');
    $pdf->Ln(5);
    $pdf->Cell(50,0.1,utf8_decode('  '),0,0,'C');
    $pdf->Cell(50,0.1,utf8_decode(''),1,0,'C');
    $pdf->Cell(10,0.1,utf8_decode(' = '),0,0,'C');
    $pdf->Cell(40,0.1,utf8_decode(''),1,0,'C');
    $pdf->Cell(10,0.1,utf8_decode(' = '),0,0,'C');
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(30,0.1,number_format(($activo_c/$pasivo_c),2,',','.'),0,0,'R');
    $pdf->SetFont('Arial','',10);
    $pdf->Ln(0.1);
    $pdf->Cell(50,5,utf8_decode('  '),0,0,'C');
    $pdf->Cell(50,5,utf8_decode('PASIVO CORRIENTE'),0,0,'C');
    $pdf->Cell(10,5,utf8_decode('  '),0,0,'C');
    $pdf->Cell(40,5, number_format($pasivo_c,2,',','.'),0,0,'C');
    $pdf->Ln(15);
    
    #** INDICE DE ENDEUDAMIENTO **#
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(50,5,utf8_decode('INDICE DE '),0,0,'L');
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(50,5,utf8_decode('PASIVO TOTAL'),0,0,'C');
    $pdf->Cell(10,5,utf8_decode('  '),0,0,'C');
    $pdf->Cell(40,5, number_format($pasivo_t,2,',','.'),0,0,'C');
    $pdf->Ln(5);
    $pdf->Cell(50,0.1,utf8_decode('  '),0,0,'C');
    $pdf->Cell(50,0.1,utf8_decode(''),1,0,'C');
    $pdf->Cell(10,0.1,utf8_decode(' = '),0,0,'C');
    $pdf->Cell(40,0.1,utf8_decode(''),1,0,'C');
    $pdf->Cell(10,0.1,utf8_decode(' = '),0,0,'C');
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(30,0.1,number_format(($pasivo_t/$activo_t),2,',','.'),0,0,'R');
    $pdf->SetFont('Arial','',10);
    $pdf->Ln(0.1);
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(50,5,utf8_decode('ENDEUDAMIENTO '),0,0,'L');
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(50,5,utf8_decode('ACTIVO TOTAL'),0,0,'C');
    $pdf->Cell(10,5,utf8_decode('  '),0,0,'C');
    $pdf->Cell(40,5, number_format($activo_t,2,',','.'),0,0,'C');
    $pdf->Ln(15);
    #** RAZON DE COBERTURRA DE INTERES **#
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(50,5,utf8_decode('RAZÓN DE COBERTURA'),0,0,'L');
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(50,5,utf8_decode('UTILIDAD O PÉRDIDA OPERACIONAL'),0,0,'C');
    $pdf->Cell(10,5,utf8_decode('  '),0,0,'C');
    $pdf->Cell(40,5, number_format($utilidad_per,2,',','.'),0,0,'C');
    $pdf->Ln(5);
    $pdf->Cell(50,0.1,utf8_decode('  '),0,0,'C');
    $pdf->Cell(50,0.1,utf8_decode(''),1,0,'C');
    $pdf->Cell(10,0.1,utf8_decode(' = '),0,0,'C');
    $pdf->Cell(40,0.1,utf8_decode(''),1,0,'C');
    $pdf->Cell(10,0.1,utf8_decode(' = '),0,0,'C');
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(30,0.1,number_format(($utilidad_per/$gastos),2,',','.'),0,0,'R');
    $pdf->SetFont('Arial','',10);
    $pdf->Ln(0.1);
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(50,5,utf8_decode('DE INTERESES'),0,0,'L');
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(50,5,utf8_decode('GASTOS DE INTERÉS'),0,0,'C');
    $pdf->Cell(10,5,utf8_decode('  '),0,0,'C');
    $pdf->Cell(40,5, number_format($gastos,2,',','.'),0,0,'C');
    $pdf->Ln(15);
    #** RENTABILIDAD DEL PATRIMONIO **#
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(50,5,utf8_decode('RENTABILIDAD DEL'),0,0,'L');
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(50,5,utf8_decode('UTILIDAD O PÉRDIDA OPERACIONAL'),0,0,'C');
    $pdf->Cell(10,5,utf8_decode('  '),0,0,'C');
    $pdf->Cell(40,5, number_format($utilidad_per,2,',','.'),0,0,'C');
    $pdf->Ln(5);
    $pdf->Cell(50,0.1,utf8_decode('  '),0,0,'C');
    $pdf->Cell(50,0.1,utf8_decode(''),1,0,'C');
    $pdf->Cell(10,0.1,utf8_decode(' = '),0,0,'C');
    $pdf->Cell(40,0.1,utf8_decode(''),1,0,'C');
    $pdf->Cell(10,0.1,utf8_decode(' = '),0,0,'C');
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(30,0.1,number_format(($utilidad_per/$patrimonio),2,',','.'),0,0,'R');
    $pdf->SetFont('Arial','',10);
    $pdf->Ln(0.1);
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(50,5,utf8_decode('PATRIMONIO'),0,0,'L');
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(50,5,utf8_decode('PATRIMONIO'),0,0,'C');
    $pdf->Cell(10,5,utf8_decode('  '),0,0,'C');
    $pdf->Cell(40,5, number_format($patrimonio,2,',','.'),0,0,'C');
    $pdf->Ln(15);
    #** RENTABILIDAD DEL ACTIVO **#
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(50,5,utf8_decode('RENTABILIDAD DEL'),0,0,'L');
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(50,5,utf8_decode('UTILIDAD O PÉRDIDA OPERACIONAL'),0,0,'C');
    $pdf->Cell(10,5,utf8_decode('  '),0,0,'C');
    $pdf->Cell(40,5, number_format($utilidad_per,2,',','.'),0,0,'C');
    $pdf->Ln(5);
    $pdf->Cell(50,0.1,utf8_decode('  '),0,0,'C');
    $pdf->Cell(50,0.1,utf8_decode(''),1,0,'C');
    $pdf->Cell(10,0.1,utf8_decode(' = '),0,0,'C');
    $pdf->Cell(40,0.1,utf8_decode(''),1,0,'C');
    $pdf->Cell(10,0.1,utf8_decode(' = '),0,0,'C');
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(30,0.1,number_format(($utilidad_per/$activo_t),2,',','.'),0,0,'R');
    $pdf->SetFont('Arial','',10);
    $pdf->Ln(0.1);
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(50,5,utf8_decode('ACTIVO'),0,0,'L');
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(50,5,utf8_decode('ACTIVO TOTAL'),0,0,'C');
    $pdf->Cell(10,5,utf8_decode('  '),0,0,'C');
    $pdf->Cell(40,5, number_format($activo_t,2,',','.'),0,0,'C');
    $pdf->Ln(15);
    #** CAPITAL DE TRABAJO **#
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(50,10,utf8_decode('CAPITAL DE TRABAJO'),0,0,'L');
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(50,10,utf8_decode('ACTIVO CORRIENTE'),0,0,'C');
    $pdf->Cell(10,10,utf8_decode(' - '),0,0,'C');
    $pdf->Cell(40,10,utf8_decode('PASIVO CORRIENTE'),0,0,'C');
    $pdf->Cell(10,10,utf8_decode(' = '),0,0,'C');
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(30,10, number_format(($activo_c-$pasivo_c),2,',','.'),0,0,'R');
    $pdf->Ln(15);
    
    ob_end_clean();		
    $pdf->Output(0,'Indicadores_Financieros.pdf',0);
?>