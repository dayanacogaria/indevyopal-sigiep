<?php

require'../fpdf/fpdf.php';
require'../Conexion/conexion.php';
session_start();
ob_start();
ini_set('max_execution_time', 360);
?>



<?php
                //Fin Conversión Fecha / Hora
                $hoy = date('d-m-Y');
                $hoy = trim($hoy, '"');
                $fecha_div = explode("-", $hoy);
                $anioh = $fecha_div[2];
                $mesh = $fecha_div[1];
                $diah = $fecha_div[0];
                $hoy = $diah.'/'.$mesh.'/'.$anioh;

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
    
                        global $fecha1;
                        global $fecha2;
  



                        global $numpaginas;
                        $numpaginas=$numpaginas+1;

                        $this->SetFont('Arial','B',10);
                        //$this->Ln(1);
                        // Título
                        $this->SetY(10);
                        //$this->image('../LOGOABC.png', 20,10,20,15,'PNG');
                        //$pdf->SetFillColor(232,232,232);

                        $this->SetX(25);
                        $this->Cell(315,5,utf8_decode($nomcomp),0,0,'C');
                        // Salto de línea
                        $this->setX(25);
                        $this->SetFont('Arial','B',8);
                        $this->Cell(315,10,utf8_decode('CÓDIGO SGC'),0,0,'R');

                        $this->Ln(5);

                        $this->SetFont('Arial','',8);
                        $this->SetX(25);
                        $this->Cell(315, 5,$tipodoc.': '.$numdoc,0,0,'C');
                        $this->SetFont('Arial','B',8);
                        $this->SetX(25);
                        $this->Cell(315,10,utf8_decode('VERSIÓN SGC'),0,0,'R');

                        $this->Ln(5);

                        $this->SetFont('Arial','',8);
                        $this->SetX(25);
                        $this->Cell(315,5,utf8_decode('LISTADO CONTRIBUYENTE'),0,0,'C');
                        $this->SetFont('Arial','B',8);
                        $this->SetX(25);
                        $this->Cell(315,10,utf8_decode('FECHA SGC'),0,0,'R');

                        $this->Ln(17);

                 
                        $this->SetFont('Arial','B',8);
                        $this->SetX(10);

                        $this->Cell(68,9,utf8_decode(''),1,0,'C');
                        $this->Cell(68,9,utf8_decode(''),1,0,'C');
                 
                        $this->Cell(68,9,utf8_decode('Tercero'),1,0,'C');
                        $this->Cell(68,9,utf8_decode('Concepto Postal'),1,0,'C');
                        $this->Cell(68,9,utf8_decode('Representante Legal'),1,0,'C');


                        $this->SetX(10);
                        $this->Cell(68,9,utf8_decode('Código Actual'),0,0,'C');
                        $this->Cell(68,9,utf8_decode('Código Anterior'),0,0,'C');
                       

                        $this->Ln(9);
                        
                    }

                    // Pie de página
                    function Footer()
                    {
                        // Posición: a 1,5 cm del final
                        global $hoy;
                        global $usuario;
                        $this->SetY(-15);
                        // Arial italic 8
                        $this->SetFont('Arial','B',8);
                        $this->SetX(10);
                        $this->Cell(90,10,utf8_decode('Fecha: '.$hoy),0,0,'L');
                        $this->Cell(90,10,utf8_decode('Máquina: '.gethostname()),0,0,'C');
                        $this->Cell(90,10,utf8_decode('Usuario: '.strtoupper($usuario)),0,0,'C');
                        $this->Cell(65,10,utf8_decode('Página '.$this->PageNo().'/{nb}'),0,0,'R');
                    }
                }

                $pdf = new PDF('L','mm','Legal');

                    $fechauno = $fechaini;       
                    $fechados = $fechafin;       
                    $compania = $_SESSION['compania'];
                    $usuario = $_SESSION['usuario'];

                    $consulta = "SELECT     t.razonsocial as traz,
                                            t.tipoidentificacion as tide,
                                            ti.id_unico as tid,
                                            ti.nombre as tnom,
                                            t.numeroidentificacion tnum
                            FROM gf_tercero t
                            LEFT JOIN gf_tipo_identificacion ti ON t.tipoidentificacion = ti.id_unico
                            WHERE t.id_unico = $compania";

                     $cmp = $mysqli->query($consulta);

                    $nomcomp = "";
                    $tipodoc = "";
                    $numdoc = "";
                    
                    while ($fila = mysqli_fetch_array($cmp))
                        {
                            $nomcomp = utf8_decode($fila['traz']);       
                            $tipodoc = utf8_decode($fila['tnom']);       
                            $numdoc = utf8_decode($fila['tnum']);   
                        }


                    $pdf->AddPage();
                    $pdf->AliasNbPages();
                    $pdf->SetFont('Arial','B',10);

                    $pdf->SetFont('Arial','',8);
                    $pdf->SetX(50);
                    $yp = $pdf->GetY();


                    $codd    = 0;
                    $totales = 0;
                    $valorA = 0;

                    //LOGO
                    $sqlRutaLogo =  'SELECT ter.ruta_logo, ciu.nombre 
                      FROM gf_tercero ter 
                      LEFT JOIN gf_ciudad ciu ON ter.ciudadidentificacion = ciu.id_unico 
                      WHERE ter.id_unico = '.$compania;
                    $rutaLogo = $mysqli->query($sqlRutaLogo);
                    $rowLogo = mysqli_fetch_array($rutaLogo);
                    $ruta = $rowLogo[0];
                    if($ruta != '')
                        {
                          $pdf->Image('../'.$ruta,30,8,20);
                        }

                    //CONSULTA
                                     
                    $sql = "SELECT c.id_unico,c.codigo_mat,c.codigo_mat_ant,
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
                                                    t.apellidodos)) AS NOMBRETERCERO ,
                                                    c.cod_postal,

                                                    IF(CONCAT_WS(' ',
                                                    rl.nombreuno,
                                                    rl.nombredos,
                                                    rl.apellidouno,
                                                    rl.apellidodos) 
                                                    IS NULL OR CONCAT_WS(' ',
                                                    rl.nombreuno,
                                                    rl.nombredos,
                                                    rl.apellidouno,
                                                    rl.apellidodos) = '',
                                                    (rl.razonsocial),
                                                    CONCAT_WS(' ',
                                                    rl.nombreuno,
                                                    rl.nombredos,
                                                    rl.apellidouno,
                                                    rl.apellidodos)) AS repreLegal
                                                    
                            FROM gc_contribuyente c 
                            LEFT JOIN gf_tercero t ON t.id_unico=c.tercero
                            LEFT JOIN gf_tercero rl ON rl.id_unico=c.repre_legal";

                    $resultado=$mysqli->query($sql);

                    while($row=mysqli_fetch_array($resultado)){
                       
                        //llenar datos
                        $pdf->SetX(10);
                        $pdf->SetFont('Arial','',8);
                        $pdf->Cell(68,4,utf8_decode($row[1]),0,0,'C');
                        $pdf->Cell(68,4,utf8_decode($row[2]),0,0,'C');

                       $y1 = $pdf->GetY();
                       $x1 = $pdf->GetX();
                       $pdf->MultiCell(68,4,utf8_decode(ucwords(mb_strtolower($row[3] ))),0,'L');
                       $y2 = $pdf->GetY();
                       $h = $y2-$y1;
                       $px = $x1+68;

                       if($numpaginas>$paginactual){
                           $pdf->SetXY($px,$yp);
                           $h=$y2-$yp;
                       } else {
                           $pdf->SetXY($px,$y1);
                       }

                        $pdf->Cell(68,4,utf8_decode($row[4]),0,0,'C');

                           $y7 = $pdf->GetY();
                           $x7 = $pdf->GetX();
                           $pdf->MultiCell(68,4,utf8_decode(ucwords(mb_strtolower($row[5]))),0,'L');
                           $y27 = $pdf->GetY();
                           $h7 = $y27-$y7;
                           $px7 = $x7+68;

                           if($numpaginas>$paginactual){
                               $pdf->SetXY($px7,$yp);
                               $h7=$y27-$yp;
                           } else {
                               $pdf->SetXY($px7,$y7);
                           }

                        //$pdf->Cell(68,4,utf8_decode(ucwords(mb_strtolower($row[5]))),0,0,'C');
                       
                        //salto
                        $alto = max($h,$h7);
                        $pdf->Ln($alto);
                        $paginactual=$numpaginas;
                    }


                    while (ob_get_length()) {
                        ob_end_clean();
                    }
         //nombre del archivo de  descarga           
        $pdf->Output(0,'Listado Contribuyente('.date('d/m/Y').').pdf',0);


?>
