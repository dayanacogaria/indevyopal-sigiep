<?php
################ MODIFICACIONES ####################
#31/05/2017 | Anderson Alarcon | mejore filtros
#31/05/2017 | Anderson Alarcon | diseño de todos los informes pdf recaudo
############################################
require'../fpdf/fpdf.php';
require'../Conexion/conexion.php';
session_start();
ob_start();
ini_set('max_execution_time', 360);
?>


      

<?php


/*SE ELIGE EL TIPO DE INFORME PDF, GENERAL ***  */
$tipoInforme=$_POST['tipoInforme'];

if($tipoInforme=="general"){

                /*ELABORACION TIPO INFORME GENERAL*/


                $fechaini       = $mysqli->real_escape_string(''.$_POST["fechaInicial"].''); 
                $fechafin       = $mysqli->real_escape_string(''.$_POST["fechaFinal"].'');

                //Conversion fecha para consulta sql

                $fechaI = DateTime::createFromFormat('d/m/Y', "$fechaini");
                $fechaI= $fechaI->format('Y/m/d');


                $fechaF = DateTime::createFromFormat('d/m/Y', "$fechafin");
                $fechaF= $fechaF->format('Y/m/d');



                #Conversión Fecha para Cabecera pdf
                $fecha1 = $fechaini;
                $fecha1 = trim($fecha1, '"');
                $fecha_div = explode("/", $fecha1);
                $dia1 = $fecha_div[0];
                $mes1 = $fecha_div[1];
                $anio1 = $fecha_div[2];
                $fecha1 = $dia1.'/'.$mes1.'/'.$anio1;

                $fecha2 = $fechafin;
                $fecha2 = trim($fecha2, '"');
                $fecha_div = explode("/", $fecha2);
                $dia2 = $fecha_div[0];
                $mes2 = $fecha_div[1];
                $anio2 = $fecha_div[2];
                $fecha2 = $dia2.'/'.$mes2.'/'.$anio2;

                 
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
                        $this->Cell(315,5,utf8_decode('LISTADO DE FACTURACIÓN RECAUDO'),0,0,'C');
                        $this->SetFont('Arial','B',8);
                        $this->SetX(25);
                        $this->Cell(315,10,utf8_decode('FECHA SGC'),0,0,'R');

                        $this->Ln(3);

                      

                        $this->SetFont('Arial','',7);
                        $this->SetX(25);
                        $this->Cell(315,5,utf8_decode('entre Fechas '.$fecha1.' y '.$fecha2),0,0,'C');

                                          $this->Ln(12);

                        $this->SetFont('Arial','B',8);
                        $this->SetX(10);
                        
                        $this->Cell(30.7,9,utf8_decode(''),1,0,'C');
                        $this->Cell(30.7,9,utf8_decode(''),1,0,'C');
                        $this->Cell(30.7,9,utf8_decode(''),1,0,'C');
                        $this->Cell(70.7,9,utf8_decode(''),1,0,'C');
                        $this->Cell(70.7,9,utf8_decode('Tercero'),1,0,'C');
                        $this->Cell(35.7,9,utf8_decode('Valor factura'),1,0,'C');
                        $this->Cell(35.7,9,utf8_decode('Valor recaudo'),1,0,'C');
                        $this->Cell(35.7,9,utf8_decode('Saldo'),1,0,'C');
                     
                        
                        $this->SetX(10);
                        $this->Cell(30.7,9,utf8_decode('Tipo'),0,0,'C');
                        $this->Cell(30.7,9,utf8_decode('Numero'),0,0,'C');
                        $this->Cell(30.7,9,utf8_decode('Fecha'),0,0,'C');
                        $this->Cell(70.7,9,utf8_decode('Descripción'),0,0,'C');
                        $this->Cell(35.7,9,utf8_decode(''),0,0,'C');
                        $this->Cell(35.7,9,utf8_decode(''),0,0,'C');
                        
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
                        $this->Cell(80,10,utf8_decode('Fecha: '.$hoy),0,0,'L');
                        $this->Cell(80,10,utf8_decode('Máquina: '.gethostname()),0,0,'C');
                        $this->Cell(80,10,utf8_decode('Usuario: '.strtoupper($usuario)),0,0,'C');
                        $this->Cell(65,10,utf8_decode('Página '.$this->PageNo().'/{nb}'),0,0,'R');
                    }
                }


                // Creación del objeto de la clase heredada
                $pdf = new PDF('L','mm','Legal');


                    $fechauno = $fechaini;       
                    $fechados = $fechafin;       

                    
                    
                    $compania = $_SESSION['compania'];
                    $usuario = $_SESSION['usuario'];

                $consulta = "SELECT         t.razonsocial as traz,
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



                //CONSULTO LAS FACTURAS QUE ESTEN ENTRE LA FECHA INICIAL Y FINAL


                 $sqlF="SELECT f.*, tf.prefijo,   
                                DATE_FORMAT(f.fecha_factura,'%d/%m/%Y') AS fechaFacConvertida,

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
                                t.apellidodos)) AS NOMBRE 

                              FROM gp_factura f
                              LEFT JOIN gp_tipo_factura tf ON f.tipofactura=tf.id_unico 
                              LEFT JOIN gf_tercero t ON f.tercero=t.id_unico
                              WHERE fecha_factura BETWEEN '$fechaI' AND '$fechaF' ORDER BY  fecha_factura ASC";

                    $lf=$mysqli->query($sqlF);


                    while($f=mysqli_fetch_array($lf)){



                                if($ruta != '')
                                {
                                  $pdf->Image('../'.$ruta,30,8,20);
                                }



                                $id_unico_factura=$f['id_unico'];

                                //CONSULTAR LOS DETALLES DE LA FACTURA $f Y SUMAR EL VALOR


                               $sqldf="SELECT   SUM(df.valor_total_ajustado) AS tvDetallesFactura 
                                    FROM gp_detalle_factura df
                                    WHERE df.factura='$id_unico_factura'";

                                $ldf= $mysqli->query($sqldf);

                                $df=mysqli_fetch_array($ldf);



                            //CONSULTAR LA SUMATORIA DE LOS DETALLES DE PAGO

                         $sqldp="SELECT SUM(dp.valor) AS tvDetallePago 
                             FROM `gp_detalle_pago` dp
                             LEFT JOIN gp_detalle_factura df ON df.id_unico=dp.detalle_factura 
                             LEFT JOIN gp_factura f ON f.id_unico=df.factura
                             WHERE f.id_unico='$id_unico_factura'";

                          $ldp= $mysqli->query($sqldp);

                            $dp=mysqli_fetch_array($ldp);

                        


                                    $pdf->SetFont('Arial','',8);

                                    $pdf->SetX(10);
                                    $pdf->Cell(30.7,4,utf8_decode($f['prefijo']),0,0,'L');
                                    $pdf->Cell(30.7,4,utf8_decode($f['numero_factura']),0,0,'L');
                                    $pdf->Cell(30.7,4,utf8_decode($f['fechaFacConvertida']),0,0,'C');

                                 $y2 = $pdf->GetY();
                                $x2 = $pdf->GetX();
                                $pdf->MultiCell(70,4,utf8_decode(ucwords(mb_strtolower($f['descripcion'] ))),0,'L');
                                $y22 = $pdf->GetY();
                                $h1 = $y22-$y2;
                                $px2 = $x2+70;

                                if($numpaginas>$paginactual){
                                    $pdf->SetXY($px2,$yp);
                                    $h1=$y22-$yp;
                                } else {
                                    $pdf->SetXY($px2,$y2);
                                }

                                $y1 = $pdf->GetY();
                                $x1 = $pdf->GetX();
                                $pdf->MultiCell(70,4,utf8_decode(ucwords(mb_strtolower($f['NOMBRE'] ))),0,'L');
                                $y2 = $pdf->GetY();
                                $h = $y2-$y1;
                                $px = $x1+70;

                                if($numpaginas>$paginactual){
                                    $pdf->SetXY($px,$yp);
                                    $h=$y2-$yp;
                                } else {
                                    $pdf->SetXY($px,$y1);
                                }




                                    $pdf->Cell(35.7,4,utf8_decode(number_format($df['tvDetallesFactura'],2,'.',',')),0,0,'R');
                                    $pdf->Cell(35.7,4,utf8_decode(number_format($dp['tvDetallePago'],2,'.',',')),0,0,'R');

                                    $saldo=$df['tvDetallesFactura']-$dp['tvDetallePago'];


                                    $pdf->Cell(35.7,4,utf8_decode(number_format($saldo,2,'.',',')),0,0,'R');

                                $alto = max($h,$h1);
                                $pdf->Ln($alto);
                                $paginactual=$numpaginas;

                                 while (ob_get_length()) {
                                    ob_end_clean();
                                }
                            


                    }

                $pdf->Output(0,'Informe_Listado_Facturacion_General ('.date('d/m/Y').').pdf',0);







}else{
    if($tipoInforme=="detallado"){

                /*ELABORACION TIPO INFORME DETALLADO*/

                $facturaInicial=$_POST['facturaInicial'];
                $facturaFinal=$_POST['facturaFinal'];


                $fechaini       = $mysqli->real_escape_string(''.$_POST["fechaInicial"].''); 
                $fechafin       = $mysqli->real_escape_string(''.$_POST["fechaFinal"].'');

                //Conversion fecha para consulta sql

                $fechaI = DateTime::createFromFormat('d/m/Y', "$fechaini");
                $fechaI= $fechaI->format('Y/m/d');


                $fechaF = DateTime::createFromFormat('d/m/Y', "$fechafin");
                $fechaF= $fechaF->format('Y/m/d');



                #Conversión Fecha para Cabecera pdf
                $fecha1 = $fechaini;
                $fecha1 = trim($fecha1, '"');
                $fecha_div = explode("/", $fecha1);
                $dia1 = $fecha_div[0];
                $mes1 = $fecha_div[1];
                $anio1 = $fecha_div[2];
                $fecha1 = $dia1.'/'.$mes1.'/'.$anio1;

                $fecha2 = $fechafin;
                $fecha2 = trim($fecha2, '"');
                $fecha_div = explode("/", $fecha2);
                $dia2 = $fecha_div[0];
                $mes2 = $fecha_div[1];
                $anio2 = $fecha_div[2];
                $fecha2 = $dia2.'/'.$mes2.'/'.$anio2;

                 
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
                        $this->Cell(315,5,utf8_decode('LISTADO FACTURACIÓN'),0,0,'C');
                        $this->SetFont('Arial','B',8);
                        $this->SetX(25);
                        $this->Cell(315,10,utf8_decode('FECHA SGC'),0,0,'R');

                        $this->Ln(3);

                      

                        $this->SetFont('Arial','',7);
                        $this->SetX(25);
                        $this->Cell(315,5,utf8_decode('entre Fechas '.$fecha1.' y '.$fecha2),0,0,'C');

                        $this->Ln(12);

                         $this->SetFont('Arial','B',8);
                        $this->SetX(20);
                        
                        $this->Cell(40.7,9,utf8_decode(''),1,0,'C');
                        $this->Cell(40.7,9,utf8_decode(''),1,0,'C');
                        $this->Cell(40.7,9,utf8_decode(''),1,0,'C');
                        $this->Cell(40.7,9,utf8_decode(''),1,0,'C');
                        
                        $this->Cell(40.7,9,utf8_decode('Iva'),1,0,'C');
                        $this->Cell(40.7,9,utf8_decode('Impoconsumo'),1,0,'C');
                     
                        
                        $this->SetX(20);
                        
                        $this->Cell(40.7,9,utf8_decode('Número pago'),0,0,'C');
                        $this->Cell(40.7,9,utf8_decode('Tipo'),0,0,'C');
                        $this->Cell(40.7,9,utf8_decode('Fecha'),0,0,'C');
                        $this->Cell(40.7,9,utf8_decode('Valor'),0,0,'C');
                        $this->Cell(40.7,9,utf8_decode(''),0,0,'C');
                        $this->Cell(40.7,9,utf8_decode(''),0,0,'C');
                        $this->Cell(40.7,9,utf8_decode('Ajuste del peso'),1,0,'C');
                        $this->Cell(33.7,9,utf8_decode('Saldo'),1,0,'C');

                        

                        
                        $this->Ln(6.7);

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


                        // Creación del objeto de la clase heredada
                    $pdf = new PDF('L','mm','Legal');


   
                    $fechauno = $fechaini;       
                    $fechados = $fechafin;       

                    
                    
                    $compania = $_SESSION['compania'];
                    $usuario = $_SESSION['usuario'];


                  $consulta = "SELECT         t.razonsocial as traz,
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


                //FACTURAS

                $sqlF="SELECT f.*, tf.prefijo,   
                                DATE_FORMAT(f.fecha_factura,'%d/%m/%Y') AS fechaFacConvertida,

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
                                t.apellidodos)) AS NOMBRE 

                              FROM gp_factura f
                              LEFT JOIN gp_tipo_factura tf ON f.tipofactura=tf.id_unico 
                              LEFT JOIN gf_tercero t ON f.tercero=t.id_unico
                 
               
                              WHERE  f.id_unico BETWEEN '$facturaInicial' AND '$facturaFinal'

                            ";

                $lf=$mysqli->query($sqlF);


                         

                            
                //ITERO FACTURAS

                while($f=mysqli_fetch_array($lf)){     




                        //logo
                        if($ruta != '')
                        {
                          $pdf->Image('../'.$ruta,30,8,20);
                        }




                        $factura_id_unico=$f['id_unico'];


                         //Suma de los detalles de la factura 

                        $sqlsumdf="SELECT SUM(detfac.valor_total_ajustado) AS valordf FROM gp_detalle_factura detfac
                                    WHERE detfac.factura='$factura_id_unico'";

                        $lsum=$mysqli->query($sqlsumdf);
                        $s=mysqli_fetch_array($lsum);

                        //DETALLES PAGO

                        $sqlDP="SELECT p.*,tp.nombre,dp.*,
                                DATE_FORMAT(p.fecha_pago,'%d/%m/%Y') AS fechaPagoConvertida

                                FROM gp_detalle_pago dp 
                                LEFT JOIN gp_pago p ON p.id_unico=dp.pago 
                                LEFT JOIN gp_tipo_pago tp ON tp.id_unico=p.tipo_pago 
                                LEFT JOIN gp_detalle_factura df ON dp.detalle_factura=df.id_unico 
                                LEFT JOIN gp_factura f ON df.factura=f.id_unico
                                WHERE f.id_unico='$factura_id_unico' AND p.fecha_pago BETWEEN '$fechaI' AND '$fechaF'";  

                        $ldp=$mysqli->query($sqlDP);
                       


                        if($ldp->num_rows > 0){

                       
                            $pdf->SetFont('Arial','B',8);
                            $pdf->SetX(20);

                            $pdf->Cell(18,9,utf8_decode($f['numero_factura']),0,0,'L');
                            $pdf->Cell(11,9,utf8_decode($f['prefijo']),0,0,'L');
                            $pdf->Cell(52,9,utf8_decode(ucwords(mb_strtolower($f['NOMBRE']))),0,0,'L');
                            $pdf->Cell(25,9,utf8_decode($f['fechaFacConvertida']),0,0,'L');
                            $pdf->Cell(10,9,utf8_decode("$".number_format($s['valordf'],2,'.',',')),0,0,'R');

                            $pdf->Ln(6);
                 
                      


                        //ITERO DETALLES PAGO
                            
                       $vDetallePago=0; 

                            
                        while ($dp=mysqli_fetch_array($ldp)) {   
                            
                            $vDetallePago=$vDetallePago+($dp['valor']+$dp['iva']+$dp['impoconsumo']+$dp['ajuste_peso']);
                                
                            $saldo=$s['valordf']-$vDetallePago; 
                            
                            
                            $pdf->SetFont('Arial','',8);
                        
                            $pdf->SetX(20);
                           
                            $pdf->Cell(40.7,4,utf8_decode($dp['numero_pago']));
                            $pdf->Cell(40.7,4,utf8_decode(ucwords(mb_strtolower($dp['nombre'])))); //tipo pago
                            $pdf->Cell(40.7,4,utf8_decode($dp['fechaPagoConvertida']));
                            $pdf->Cell(40.7,4,utf8_decode(number_format($dp['valor'],2,'.',',')),0,0,'R');
                            $pdf->Cell(40.7,4,utf8_decode(number_format($dp['iva'],2,'.',',')),0,0,'R');
                            $pdf->Cell(40.7,4,utf8_decode(number_format($dp['impoconsumo'],2,'.',',')),0,0,'R');
                            $pdf->Cell(40.7,4,utf8_decode(number_format($dp['ajuste_peso'],2,'.',',')),0,0,'R');
                            $pdf->Cell(33.7,4,utf8_decode(number_format($saldo,2,'.',',')),0,0,'R');



                            $pdf->Ln(4);


                        }

                          
                    }



                }
              



                        while (ob_get_length()) {
                            ob_end_clean();
                        }


                    

                $pdf->Output(0,'Informe_Listado_Facturacion_Detallado ('.date('d/m/Y').').pdf',0);



    }
}