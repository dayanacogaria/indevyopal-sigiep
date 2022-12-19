<?php
#creado : | Nestor Bautista | 01/08/2018 | 
header("Content-Type: text/html;charset=utf-8");
require'../fpdf/fpdf.php';
require'../Conexion/conexion.php';
session_start();
ob_start();
ini_set('max_execution_time', 0);

$contI = $_POST['sltCont1'];
$contF = $_POST['sltCont2'];
$anno = $_SESSION['anno'];
##CONSULTA DATOS COMPAÑIA##
$compa=$_SESSION['compania'];
$comp="SELECT t.razonsocial, t.numeroidentificacion, t.digitoverficacion, t.ruta_logo "
        . "FROM gf_tercero t WHERE id_unico=$compa";
$comp = $mysqli->query($comp);
$comp = mysqli_fetch_row($comp);
$nombreCompania = $comp[0];
if(empty($comp[2])) {
    $nitcompania = $comp[1];
} else {
    $nitcompania = $comp[1].' - '.$comp[2];
}
$ruta = $comp[3];
$usuario = $_SESSION['usuario'];
#CREACION PDF, HEAD AND FOOTER

echo $anio = "SELECT anno FROM  gf_parametrizacion_anno WHERE id_unico = '$anno'";
$ann = $mysqli->query($anio);
$ANI = mysqli_fetch_row($ann);
echo "<br/>";
$AN = $ANI[0] - 1;

if($tipo ==1){
    $tip = "";
}elseif($tipo ==2){
    $tip = "ACTIVOS";
}elseif($tipo ==3){
    $tip = "INACTIVOS";
}

$hoy = date('d-m-Y');

$hoy2 = date('Y-m-d');
#$hoy2 = date('2018-09-04');
$num    = date("j", strtotime($hoy));
$anno   = date("Y", strtotime($hoy));
$mes    = array('enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre');
$mes    = $mes[(date('m', strtotime($hoy))*1)-1];
$fechaA = $num.' de '.$mes.' del '.$anno;


class PDF extends FPDF
{

function obtenerFechaEnLetra($fecha){
    
}

function Header()
{ 
    
    global $fecha1;
    global $fecha2;
    global $cuentaI;
    global $cuentaF;
    global $nombreCompania;
    global $nitcompania;
    global $numpaginas;
    global $ruta;
    global $mesNomn;
    global $tip;
    $numpaginas=$this->PageNo();
    
    $this->SetFont('Arial','B',10);
    $this->SetY(15);
    if($ruta != '')
        {
            
          $this->Image('../'.$ruta,25,15,20);
        }
    $this->SetX(8.2);
    $this->Cell(200,5,utf8_decode($nombreCompania),0,0,'C');
    $this->Ln(5);
    
    $this->SetX(8.2);
    $this->Cell(200, 5,'NIT: '.$nitcompania,0,0,'C'); 
    $this->Ln(5);

    $this->SetX(8.2);
    #$this->Cell(330,5,utf8_decode('LISTADO CONTRIBUYENTES DEUDORES '.$tip),0,0,'C');
    $this->Ln(10);
    

    $this->Ln(8);
    //ENTRE
    /*
    $this->SetX(20);
    $this->SetFont('Arial','B',8);
    $this->Cell(15,7,utf8_decode('COD MAT'),1,0,'C');
    $this->Cell(85,7,utf8_decode('CONTRIBUYENTE'),1,0,'C');
    $this->Cell(85,7,utf8_decode('REPRESENTANTE LEGAL'),1,0,'C');
    $this->Cell(50,7,utf8_decode('DIRECCION'),1,0,'C');
    $this->Cell(20,7,utf8_decode('TELEFONO'),1,0,'C');
    $this->Cell(65,7,utf8_decode('AÑOS DEUDA'),1,0,'C');
    */

    /*$this->Cell(36,9,utf8_decode('SALDO EXTRACTO'),1,0,'C');
    $this->Cell(36,9,utf8_decode('DIFERENCIA'),1,0,'C');*/
    
    $this->Ln(20);
    
    
    }      
    
    function Footer()
    {
    // Posición: a 1,5 cm del final
    global $hoy;
    global $usuario;
    $this->SetY(-15);
    // Arial italic 8
    
    /*
    $this->SetFont('Arial','B',8);
    $this->SetX(15);
    $this->Cell(90,10,utf8_decode('Fecha: '.date('d/m/Y')),0,0,'L');
    $this->Cell(90,10,utf8_decode('Máquina: '.gethostname()),0,0,'C');
    $this->Cell(90,10,utf8_decode('Usuario: '.strtoupper($usuario)),0,0,'C');
    $this->Cell(65,10,utf8_decode('Página '.$this->PageNo().'/{nb}'),0,0,'R');
    */
    }
}

$pdf = new PDF('P','mm','Letter');   
$pdf->AddPage();
$pdf->AliasNbPages();
$yp=$pdf->GetY();

$firmas = "SELECT   c.nombre, 
                        rd.orden,
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
                        tr.apellidodos)) AS NOMBRE 

                FROM gf_responsable_documento rd 
                LEFT JOIN gf_tercero tr ON rd.tercero = tr.id_unico
                LEFT JOIN gf_cargo_tercero ct ON ct.tercero = tr.id_unico
                LEFT JOIN gf_cargo c ON ct.cargo = c.id_unico
                LEFT JOIN gf_tipo_documento td ON rd.tipodocumento = td.id_unico
                WHERE td.nombre = 'Cobro Persuasivo' AND 
                (rd.fecha_inicio <= '$hoy2' AND rd.fecha_fin IS NULL OR rd.fecha_inicio <= '$hoy2' AND rd.fecha_fin >= '$hoy2' )
                ORDER BY rd.orden ASC";

$fi = $mysqli->query($firmas);
$F = mysqli_fetch_row($fi);


$sql = "SELECT c.id_unico,
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
                t.apellidodos)) AS NOMBRETERCEROCONTRIBUYENTE, 
                c.codigo_mat,
                t.representantelegal,
                c.dir_correspondencia,
                c.telefono,
                c.fechainscripcion
                
        FROM gc_contribuyente c 
        LEFT JOIN gf_tercero t ON t.id_unico=c.tercero
        WHERE c.estado = 1  AND c.id_unico BETWEEN $contI AND $contF";




$resultado=$mysqli->query($sql);
$nre = mysqli_num_rows($resultado);
$cant = 0;

while($row=mysqli_fetch_array($resultado)){
    $cant++;

    $fechaIDiv = explode('-',$row[6]);
    $annoI = $fechaIDiv[0];

    echo $deuda2 = "SELECT COUNT(DISTINCT ac.vigencia) FROM  gc_anno_comercial ac

                    LEFT JOIN gc_declaracion d ON d.periodo = ac.id_unico
                    LEFT JOIN gc_recaudo_comercial rc ON rc.declaracion = d.id_unico
                    LEFT JOIN gc_contribuyente c ON d.contribuyente = c.id_unico
                    WHERE c.estado = 1 AND  ac.vigencia BETWEEN '$annoI' AND '$AN' AND ac.mes = 0 AND  ac.id_unico NOT IN(SELECT de.periodo FROM gc_recaudo_comercial rco LEFT JOIN gc_declaracion de ON rco.declaracion = de.id_unico WHERE contribuyente = $row[0] AND de.clase = 1)";
    $deu2 = $mysqli->query($deuda2);            
    $ndeu = mysqli_fetch_row($deu2);
    echo "<br/>";
    if($ndeu[0] > 0){
        
        $pdf->SetFont('Arial','',10);
        $pdf->SetX(25);
        $pdf->Cell(150,5,utf8_decode(ucwords(mb_strtolower('Barbosa, '.$fechaA))),0,0,'L');
        $pdf->Ln(15);
        
        $pdf->SetX(25);
        $pdf->Cell(150,5,utf8_decode(ucwords(mb_strtolower('Señor'))),0,0,'L');
        $pdf->Ln(5);
        
        $pdf->SetFont('Arial','B',10);
        $pdf->SetX(25);
        $pdf->Cell(300,5,utf8_decode(ucwords(mb_strtolower($row[1]))),0,0,'L');
        $pdf->Ln(5);
        
        $pdf->SetFont('Arial','',10);
        $pdf->SetX(25);
        $pdf->Cell(300,5,utf8_decode(ucwords(mb_strtolower($row[4]))),0,0,'L');
        $pdf->Ln(15);
        
        $pdf->SetFont('Arial','B',10);
        $pdf->SetX(25);
        $pdf->Cell(200,5,utf8_decode(ucwords(mb_strtolower('REF. Requerimiento Cobro Persuasivo'.' - '.'Impuesto de Industria y Comercio. '))),0,0,'L');
        $pdf->Ln(12);
        
        $pdf->SetFont('Arial','',10);
        $pdf->SetX(25);
        $pdf->MultiCell(160,5,utf8_decode('Me permito solicitarle se presente a la Secretaria de Hacienda del Municipio de Barbosa en el termino de cinco (05) días hábiles a partir del recibo de este requerimiento, con el fin de aclarar asuntos relacionados con el pago del impuesto de industria y comercio, identificado con código de matrícula No.'.$row[2].'.'),0,'J');
        $pdf->Ln(8);
        
        $pdf->SetX(25);
        $pdf->MultiCell(160,5,utf8_decode('Una vez vencido el termino anterior, sin obtener respuesta alguna a este requerimiento, se continuara en forma eficaz y efectiva con el proceso de cobro administrativo  coactivo, según lo demás consecuencias que conlleva el sustraerse del pago del mencionado impuesto.'),0,'J');
        $pdf->Ln(8);
        
        $pdf->SetX(25);
        $pdf->MultiCell(160,5,utf8_decode('Si ya cancelo la presente obligación, por favor suministrar los soportes de pago a esta Secretaria, con el fin de evitar que se continúe con el tramite de cobro.'),0,'J');
        $pdf->Ln(8);
        
        
        $pdf->SetX(25);
        $pdf->Cell(200,5,utf8_decode(ucwords(mb_strtolower('Cordialmente'))),0,0,'L');
        $pdf->Ln(35);
        
        $pdf->SetFont('Arial','B',10);
        $pdf->SetX(25);
        $pdf->Cell(60,0.1,'',1);  
        $pdf->Ln(2);
        $pdf->SetX(25);
        $pdf->cellfitscale(60,5,utf8_decode($F[2]),0,0,'L');
        $pdf->Ln(4);
        $pdf->SetFont('Arial','',10);
        $pdf->SetX(25);
        $pdf->cellfitscale(60,5,utf8_decode($F[0]),0,0,'L'); 
        $pdf->Ln(4);
        
        $pdf->SetX(25);
        $pdf->Cell(200,5,utf8_decode(ucwords(mb_strtolower('Municipio de Barbosa'))),0,0,'L');
        $pdf->Ln(5);

        if($cant < $nre){
            $pdf->AddPage();
        }
    }

    
    
    
    
}

while (ob_get_length()) {
    ob_end_clean();
}
//ob_end_clean();
$pdf->Output(0,'Cobro Persuasivo ('.date('d/m/Y').').pdf',0);
?>