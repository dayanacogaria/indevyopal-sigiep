<?php
#creado : | Nestor Bautista | 01/08/2018 | 
header("Content-Type: text/html;charset=utf-8");
require'../fpdf/fpdf.php';
require'../Conexion/conexion.php';
session_start();
ob_start();
ini_set('max_execution_time', 0);

$tipo = $_POST['sltOpcion'];
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

if($tipo ==1){
    $tip = "";
}elseif($tipo ==2){
    $tip = "ACTIVOS";
}elseif($tipo ==3){
    $tip = "INACTIVOS";
}

class PDF extends FPDF
{
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
    $this->SetY(10);
    if($ruta != '')
        {
            
          $this->Image('../'.$ruta,20,8,20);
        }
    $this->SetX(8.2);
    $this->Cell(330,5,utf8_decode($nombreCompania),0,0,'C');
    $this->Ln(5);
    
    $this->SetX(8.2);
    $this->Cell(330, 5,$nitcompania,0,0,'C'); 
    $this->Ln(5);

    $this->SetX(8.2);
    $this->Cell(330,5,utf8_decode('LISTADO ACTIVIDAD CONTRIBUYENTES '.$tip),0,0,'C');
    $this->Ln(10);
    

    $this->Ln(8);
    //ENTRE
    
    $this->SetX(8);
    $this->SetFont('Arial','B',8);
    $this->Cell(15,7,utf8_decode('COD MAT'),1,0,'C');
    $this->Cell(75,7,utf8_decode('CONTRIBUYENTE'),1,0,'C');
    $this->Cell(75,7,utf8_decode('REPRESENTANTE LEGAL'),1,0,'C');
    $this->Cell(35,7,utf8_decode('DIRECCION'),1,0,'C');
    $this->Cell(20,7,utf8_decode('TELEFONO'),1,0,'C');
    $this->Cell(65,7,utf8_decode('ACTIVIDAD'),1,0,'C');
    $this->Cell(25,7,utf8_decode('FECHA INICIO'),1,0,'C');
    $this->Cell(25,7,utf8_decode('FECHA CIERRE'),1,0,'C');

    /*$this->Cell(36,9,utf8_decode('SALDO EXTRACTO'),1,0,'C');
    $this->Cell(36,9,utf8_decode('DIFERENCIA'),1,0,'C');*/
    
    $this->Ln(7);
    
    
    }      
    
    function Footer()
    {
    // Posición: a 1,5 cm del final
    global $hoy;
    global $usuario;
    $this->SetY(-15);
    // Arial italic 8
    $this->SetFont('Arial','B',8);
    $this->SetX(15);
    $this->Cell(90,10,utf8_decode('Fecha: '.date('d/m/Y')),0,0,'L');
    $this->Cell(90,10,utf8_decode('Máquina: '.gethostname()),0,0,'C');
    $this->Cell(90,10,utf8_decode('Usuario: '.strtoupper($usuario)),0,0,'C');
    $this->Cell(65,10,utf8_decode('Página '.$this->PageNo().'/{nb}'),0,0,'R');
    }
}

$pdf = new PDF('L','mm','Legal');   
$pdf->AddPage();
$pdf->AliasNbPages();
$yp=$pdf->GetY();


//Valida si el tipo es uno, es decir todos los contribuyentes registrados
if($tipo == 1){
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
                c.dir_correspondencia
                
        FROM gc_contribuyente c 
        LEFT JOIN gf_tercero t ON t.id_unico=c.tercero";

//Valida si el tipo es dos, es decir todos los contribuyentes registrados activos
}elseif($tipo == 2){
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
                c.dir_correspondencia
                
        FROM gc_contribuyente c 
        LEFT JOIN gf_tercero t ON t.id_unico=c.tercero
        WHERE c.estado = 1 ";

//Valida si el tipo es tre, es decir todos los contribuyentes registrados inactivos
}else{
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
                c.dir_correspondencia
                
        FROM gc_contribuyente c 
        LEFT JOIN gf_tercero t ON t.id_unico=c.tercero
        WHERE c.estado = 2";
}



$resultado=$mysqli->query($sql);


while($row=mysqli_fetch_array($resultado)){
    $yp=$pdf->GetY();
    if($yp > 180){
        $pdf->AddPage();
    }
    #Consulta el representante legal del contribuyente 
    if(!empty($row[3])){
        $Representante = "SELECT    IF(CONCAT_WS(' ',
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
                                t.apellidodos)) AS NOMBRE
                    FROM gf_tercero t 
                    WHERE t.id_unico = '$row[3]'";

        $repre = $mysqli->query($Representante);
        $RE = mysqli_fetch_row($repre);
    }else{
        $RE[0] = "";
    }


    #consulta las activades del contribuyente
    $actividad = "SELECT    ac.codigo,
                            ac.descripcion,
                            DATE_FORMAT(aco.fechainicio,'%d/%m/%Y') AS fechaInicio, 
                            DATE_FORMAT(aco.fechacierre,'%d/%m/%Y') AS fechaCierre
                FROM gc_actividad_comercial ac
                LEFT JOIN gc_actividad_contribuyente aco ON aco.actividad = ac.id_unico
                WHERE aco.contribuyente = '$row[0]' ORDER BY aco.fechainicio ASC";
    $activi = $mysqli->query($actividad);

    
    //llenar datos
    $pdf->SetFont('Arial','',9);
    $ypr=$pdf->GetY();

    
    $pdf->SetX(8);
    $xpr=$pdf->GetX();
    $pdf->Cell(15,5,utf8_decode(ucwords(mb_strtolower($row[2]))),0,0,'C');

    $x1 = $pdf->GetX();
    $y1 = $pdf->GetY();
    $pdf->MultiCell(75,5,utf8_decode(ucwords(mb_strtolower($row[1]))),0,'J');
    $y2 = $pdf->GetY();
    $h1 = $y2-$y1;
    
    $px1 = $x1+75;

    $pdf->SetXY($px1,$ypr);
    

    $x2=$pdf->GetX();
    $y2=$pdf->GetY();
    $pdf->MultiCell(75,5,utf8_decode(ucwords(mb_strtolower($RE[0]))),0,'J');
    $y22=$pdf->GetY();
    $h2 = $y22-$y2;
    $px2 = $x2+75;
    
    $pdf->SetXY($px2,$ypr);
    

    $x3=$pdf->GetX();
    $y3=$pdf->GetY();
    $pdf->MultiCell(35,5,utf8_decode(ucwords(mb_strtolower($row[4]))),0,'J');
    $y33=$pdf->GetY();
    $h3 = $y33-$y3;
    $px3 = $x3+35;
    
    $pdf->SetXY($px3,$ypr);
    

    $pdf->Cell(20,4,utf8_decode(''),0,0,'C');

    $x5 = $pdf->GetX();
    $y5 = $pdf->GetY();
    while($AC = mysqli_fetch_row($activi)){
        $pdf->SetX($x5);
        $yx = $pdf->GetY();
        $pdf->MultiCell(65,5,utf8_decode(ucwords(mb_strtolower($AC[0]." - ".$AC[1]))),0,'J');
        $y7 = $pdf->GetY();
        $pdf->SetXY($x5+69,$yx);
        $pdf->Cell(25,5,utf8_decode(ucwords(mb_strtolower($AC[2]))),0,'J');
        $pdf->Cell(25,5,utf8_decode(ucwords(mb_strtolower($AC[3]))),0,'J');
        $hx = $y7 - $yx;
        $yyyy = $pdf->GetY();
        if($yyyy <= 170){
            $pdf->Ln($hx);    
        }
        
        
        $yp1=$pdf->GetY();
        if($yp1 > 170){
            $h4 = $yp1-$ypr;
            $alto = max($h1,$h2,$h3,$h4);
            $pdf->SetXY($xpr,$ypr);
            $pdf->Cell(15,$alto,utf8_decode(''),1,0,'C');
            $pdf->Cell(75,$alto,utf8_decode(' '),1,0,'C');
            $pdf->Cell(75,$alto,utf8_decode(' '),1,0,'C');
            $pdf->Cell(35,$alto,utf8_decode(' '),1,0,'C');
            $pdf->Cell(20,$alto,utf8_decode(' '),1,0,'C');
            $pdf->Cell(65,$alto,utf8_decode(' '),1,0,'C');
            $pdf->Cell(25,$alto,utf8_decode(' '),1,0,'C');
            $pdf->Cell(25,$alto,utf8_decode(' '),1,0,'C');
            $pdf->AddPage();
            $pdf->SetX(8);
            $xpr=$pdf->GetX();
            $ypr=$pdf->GetY();
            $y5 = $pdf->GetY();
        }
    }
    
    $y44 = $pdf->GetY();
    $h4  = $y44-$y5;
    $px5 = $x5+65;
    $pdf->SetXY($px5,$ypr); 
    $alto = max($h1,$h2,$h3,$h4);
    
    
    $pdf->SetXY($xpr,$ypr);
    $pdf->Cell(15,$alto,utf8_decode(' '),1,0,'C');
    $pdf->Cell(75,$alto,utf8_decode(' '),1,0,'C');
    $pdf->Cell(75,$alto,utf8_decode(' '),1,0,'C');
    $pdf->Cell(35,$alto,utf8_decode(' '),1,0,'C');
    $pdf->Cell(20,$alto,utf8_decode(' '),1,0,'C');
    $pdf->Cell(65,$alto,utf8_decode(' '),1,0,'C');
    $pdf->Cell(25,$alto,utf8_decode(' '),1,0,'C');
    $pdf->Cell(25,$alto,utf8_decode(' '),1,0,'C');

    $pdf->Ln($alto); 
    
}



while (ob_get_length()) {
    ob_end_clean();
}
//ob_end_clean();
$pdf->Output(0,'Listado Actividad Contribuyente ('.date('d/m/Y').').pdf',0);
?>