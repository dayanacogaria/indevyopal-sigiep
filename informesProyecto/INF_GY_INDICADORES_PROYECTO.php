<?php
require_once("../Conexion/ConexionPDO.php");
require_once("../Conexion/conexion.php");
require_once("../jsonPptal/funcionesPptal.php");
require'../fpdf/fpdf.php';
ini_set('max_execution_time', 0);
ob_start();
session_start();
$con        = new ConexionPDO();
$compania   = $_SESSION['compania'];
$rpro       = $_REQUEST['sltpryi'];
$arpro      = explode(",", $rpro);
$id_proyF   = $arpro[0];
$id_proyY   = $arpro[1];
#* Datos Proyecto Y
$rowp = $con->Listar("SELECT LOWER(titulo), monto_total FROM gy_proyecto WHERE id_unico = $id_proyY");
$nombre_p = ucwords($rowp[0][0]);
$monto_t  = ucwords($rowp[0][1]);
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

$row_me = $con->Listar("SELECT SUM(dc.valor) 
    FROM gf_detalle_comprobante dc 
    LEFT JOIN gf_cuenta c ON dc.cuenta = c.id_unico 
    LEFT JOIN gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico 
    LEFT JOIN gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico 
    WHERE dc.proyecto = $id_proyF AND c.clasecuenta IN(7,18)  ");
$monto_e = $row_me[0][0];
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
            global $nombre_p;
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
            $this->Cell(190,5,utf8_decode('INDICADORES POR PROYECTO'),0,0,'C');
            $this->Ln(5);
            $this->Cell(190,5,utf8_decode('PROYECTO: '.$nombre_p),0,0,'C');
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
    $pdf->Ln(20);
    
    
    $pdf->SetFont('Arial','B',10);
    #** INDICE DE LIQUIDEZ **#
    $pdf->Cell(50,10,utf8_decode('ÍNDICE DE CUMPLIMIENTO'),0,0,'L');
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(50,5,utf8_decode('MONTO EJECUTADO'),0,0,'C');
    $pdf->Cell(10,5,utf8_decode(' '),0,0,'C');
    $pdf->Cell(40,5, number_format($monto_e,2,',','.'),0,0,'C');
    $pdf->Ln(5);
    $pdf->Cell(50,0.1,utf8_decode('  '),0,0,'C');
    $pdf->Cell(50,0.1,utf8_decode(''),1,0,'C');
    $pdf->Cell(10,0.1,utf8_decode(' = '),0,0,'C');
    $pdf->Cell(40,0.1,utf8_decode(''),1,0,'C');
    $pdf->Cell(10,0.1,utf8_decode(' = '),0,0,'C');
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(30,0.1,number_format(($monto_e/$monto_t)*100,2,',','.').'%',0,0,'R');
    $pdf->SetFont('Arial','',10);
    $pdf->Ln(0.1);
    $pdf->Cell(50,5,utf8_decode('  '),0,0,'C');
    $pdf->Cell(50,5,utf8_decode('MONTO TOTAL'),0,0,'C');
    $pdf->Cell(10,5,utf8_decode('  '),0,0,'C');
    $pdf->Cell(40,5, number_format($monto_t,2,',','.'),0,0,'C');
    $pdf->Ln(15);
    
    ob_end_clean();		
    $pdf->Output(0,'Indicadores_Por_Proyecto.pdf',0);
?>