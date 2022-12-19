<?php
require'../Conexion/conexion.php';
require'../Conexion/ConexionPDO.php';
require_once('../jsonPptal/funcionesPptal.php');
@session_start();
ini_set('max_execution_time', 0);
$con = new ConexionPDO();
$parmanno   = $_SESSION['anno'];
$compania   = $_SESSION['compania'];
$annionom   = anno($parmanno);
$calendario = CAL_GREGORIAN;
$anno       = $annionom; 
$fechaCr    = $_REQUEST['txtFechaFinal'];
$fecha      = fechaC($_REQUEST['txtFechaFinal']);

$depI       = $_REQUEST['sltDepInicial'];
$depF       = $_REQUEST['sltDepFinal'];
#***********************Datos Compañia***********************#
$compania = $_SESSION['compania'];
$rowC = $con->Listar("SELECT 
            ter.id_unico,
            ter.razonsocial,
            UPPER(ti.nombre),
            IF(ter.digitoverficacion IS NULL OR ter.digitoverficacion='',
                ter.numeroidentificacion, 
                CONCAT(ter.numeroidentificacion, ' - ', ter.digitoverficacion)),
            dir.direccion,
            tel.valor,
            ter.ruta_logo 
        FROM            
            gf_tercero ter
        LEFT JOIN 	
            gf_tipo_identificacion ti ON ter.tipoidentificacion = ti.id_unico
        LEFT JOIN       
            gf_direccion dir ON dir.tercero = ter.id_unico
        LEFT JOIN 	
            gf_telefono  tel ON tel.tercero = ter.id_unico
        WHERE 
            ter.id_unico = $compania");

$razonsocial = $rowC[0][1];
$nombreIdent = $rowC[0][2];
$numeroIdent = $rowC[0][3];
$direccinTer = $rowC[0][4];
$telefonoTer = $rowC[0][5];
$ruta_logo   = $rowC[0][6];

if($_REQUEST['t']==1){
    header("Content-Type: text/html;charset=utf-8");
    require'../fpdf/fpdf.php';
    class PDF extends FPDF {
        function Header() { 

            global $razonsocial;
            global $nombreIdent;
            global $numeroIdent;
            global $direccinTer;
            global $telefonoTer;
            global $ruta_logo;
            global $fechaCr;

            if($ruta_logo != '') {
              $this->Image('../'.$ruta_logo,20,8,20);
            }
            $this->SetFont('Arial','B',10);
            $this->SetX(10);
            $this->Cell(330,10,utf8_decode($razonsocial),0,0,'C');
            $this->ln(5);
            $this->SetX(10);
            $this->Cell(330,10,utf8_decode($nombreIdent.': '.$numeroIdent),0,0,'C');
            $this->ln(5);
            $this->SetX(10);
            $this->Cell(330,10,utf8_decode('Dirección: '.$direccinTer.' Teléfono: '.$telefonoTer),0,0,'C');
            $this->ln(5);
            $this->SetX(10);
            $this->Cell(330,10,utf8_decode('LISTADO DE INVENTARIOS POR DEPENDENCIA GENERAL '),0,0,'C');
            $this->ln(5);
            $this->SetX(10);
            $this->Cell(330,10,utf8_decode('FECHA CORTE '.$fechaCr),0,0,'C');
            $this->ln(10);
        }      

        function Footer(){
            $this->SetY(-15);
            $this->SetFont('Arial','B',8);
            $this->SetX(10);
            $this->Cell(200,10,utf8_decode('Fecha: '.date('d/m/Y')),0,0,'L');
            $this->Cell(200,10,utf8_decode('Página '.$this->PageNo().'/{nb}'),0,0,'R');
        }
    }

    $pdf = new PDF('L','mm','Legal'); 
    $pdf->AliasNbPages();
    $pdf->AddPage();
    
    
    $ti = $con->Listar("SELECT DISTINCT ti.id_unico, UPPER(ti.nombre) 
            FROM gf_tipo_inventario ti 
            WHERE id_unico IN (1,2)
            ORDER BY ti.id_unico ASC");

        $pi1 = $ti[0][0];
        $pi2 = $ti[1][0];
        #* Buscar Dependencias 
        $rowd = $con->Listar("SELECT DISTINCT d.id_unico, d.sigla, UPPER(d.nombre) 
                FROM gf_movimiento m 
                LEFT JOIN gf_dependencia d ON m.dependencia = d.id_unico 
                WHERE (d.id_unico BETWEEN $depI AND $depF) AND d.compania = $compania 
                AND m.fecha <='$fecha'");
        for ($i = 0; $i < count($rowd); $i++) {
            $id_dependencia =  $rowd[$i][0];
            $rowter = $con->Listar("SELECT DISTINCT t.id_unico, 
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
                 t.apellidodos)) AS NOMBRE, t.numeroidentificacion , 
                 car.nombre 
                FROM gf_movimiento m
                LEFT JOIN gf_tercero t ON m.tercero = t.id_unico 
                LEFT JOIN gf_cargo_tercero cter ON cter.tercero = t.id_unico
                LEFT JOIN gf_cargo car  ON cter.cargo = car.id_unico
                WHERE m.dependencia =$id_dependencia AND m.fecha <='$fecha'");
            $dpi = '';
            $dpf = '';
            $tri = '';
            $trf = '';
            for ($t = 0; $t < count($rowter); $t++) {
                $imp = 0;
                $id_responsable = $rowter[$t][0];
                #CONSULTAS MOVIMIENTOS
                $dt1e = detaf(2, 1, $rowd[$i][0],$id_responsable);
                $dt1s = detaf(3, 1, $rowd[$i][0],$id_responsable);
                if(!empty($dt1e[0][0]) || !empty($dt1s[0][0]) ){
                    $imp = 1; 
                    $dpi = $rowd[$i][1];
                    if($dpi==$dpf){}else {
                        $pdf->SetFont('Arial','B',10);
                        $pdf->Cell(330,10,utf8_decode('DEPENDENCIA: '.$rowd[$i][1].' - '.$rowd[$i][2]),1,0,'L');
                        $pdf->Ln(10);
                    }
                    $dpf = $dpi;
                    $tri = $rowter[$t][2];
                    if($tri==$trf){}else {
                        $pdf->SetFont('Arial','BI',10);
                        $pdf->Cell(330,10,utf8_decode('RESPONSABLE: '.$rowter[$t][1].' - '.$rowter[$t][2]),1,0,'L');
                        $pdf->Ln(10);
                    }
                    
                    $pdf->SetFont('Arial','B',10);
                    $pdf->Cell(330,10,utf8_decode('TIPO INVENTARIO: '.$ti[0][1]),1,0,'L');
                    $pdf->Ln(10);
                    $pdf->SetFont('Arial','B',10);
                    
                    $pdf->Cell(35,10, utf8_decode(''),1,0,'C');
                    $pdf->Cell(50,10,utf8_decode(''),1,0,'C');
                    $pdf->Cell(35,10,utf8_decode(''),1,0,'C');
                    $pdf->Cell(105,10,utf8_decode(''),1,0,'C');
                    $pdf->Cell(105,10,utf8_decode(''),1,0,'C');
                    $pdf->Setx(10);
                    $pdf->Cell(35,10, utf8_decode('CÓDIGO'),0,0,'C');
                    $pdf->Cell(50,10,utf8_decode('NOMBRE'),0,0,'C');
                    $pdf->Cell(35,10,utf8_decode('VALOR'),0,0,'C');
                    $pdf->Cell(105,5,utf8_decode('ENTRADA'),0,0,'C');
                    $pdf->Cell(105,5,utf8_decode('SALIDA'),0,0,'C');
                    $pdf->Ln(5);
                    $pdf->Cell(35,5, utf8_decode(''),0,0,'C');
                    $pdf->Cell(50,5,utf8_decode(''),0,0,'C');
                    $pdf->Cell(35,5,utf8_decode(''),0,0,'C');
                    $pdf->Cell(40,5,utf8_decode('TIPO'),1,0,'C');
                    $pdf->Cell(35,5,utf8_decode('NÚMERO'),1,0,'C');
                    $pdf->Cell(30,5,utf8_decode('FECHA'),1,0,'C');
                    $pdf->Cell(40,5,utf8_decode('TIPO'),1,0,'C');
                    $pdf->Cell(35,5,utf8_decode('NÚMERO'),1,0,'C');
                    $pdf->Cell(30,5,utf8_decode('FECHA'),1,0,'C');
                    $pdf->Ln(5);
                    if($pdf->GetY()>150){
                        $pdf->AddPage();
                    }
                    $pdf->SetFont('Arial','',8);
                    for ($e = 0; $e < count($dt1e); $e++) {
                        $x  = $pdf->GetX();
                        $y  = $pdf->GetY();
                        $pdf->Cell(35,5,'',0,0,'L');
                        $pdf->MultiCell(50,5,utf8_decode($dt1e[$e][2]),0,'L');
                        $y1 = $pdf->GetY()-$y;
                        $h  = $y1;
                        #
                        $pdf->SetXY($x, $y);
                        $pdf->CellFitScale(35,$h, utf8_decode($dt1e[$e][1]),1,0,'L');
                        $pdf->Cell(50,$h,'',1,0,'L');
                        $pdf->Cell(35,$h,utf8_decode(number_format($dt1e[$e][3],2,'.',',')),1,0,'R');
                        $pdf->CellFitScale(40,$h,utf8_decode($dt1e[$e][4].' - '.ucwords($dt1e[$e][5])),1,0,'L');
                        $pdf->Cell(35,$h,utf8_decode($dt1e[$e][6]),1,0,'L');
                        $pdf->Cell(30,$h,utf8_decode($dt1e[$e][7]),1,0,'L');
                        $pdf->Cell(40,$h,utf8_decode(''),1,0,'C');
                        $pdf->Cell(35,$h,utf8_decode(''),1,0,'C');
                        $pdf->Cell(30,$h,utf8_decode(''),1,0,'C');
                        $pdf->Ln($h);
                        if($pdf->GetY()>160){
                            $pdf->AddPage();
                        }
                    }
                    for ($s = 0; $s < count($dt1s); $s++) {
                        #Buscar Afectado  
                        $ent   = detafectado($dt1s[$s][8]);
                        $x  = $pdf->GetX();
                        $y  = $pdf->GetY();
                        $pdf->Cell(35,5,'',0,0,'L');
                        $pdf->MultiCell(50,5,utf8_decode($dt1s[$s][2]),0,'L');
                        $y1 = $pdf->GetY()-$y;
                        $h  = $y1;
                        #
                        $pdf->SetXY($x, $y);
                        $pdf->CellFitScale(35,$h, utf8_decode($dt1s[$s][1]),1,0,'L');
                        $pdf->Cell(50,$h,'',1,0,'L');
                        $pdf->Cell(35,$h,utf8_decode(number_format($dt1s[$s][3],2,'.',',')),1,0,'R');
                        $pdf->CellFitScale(40,$h,utf8_decode($ent[0][4].' - '.ucwords($ent[0][5])),1,0,'L');
                        $pdf->Cell(35,$h,utf8_decode($ent[0][6]),1,0,'L');
                        $pdf->Cell(30,$h,utf8_decode($ent[0][7]),1,0,'L');
                        $pdf->CellFitScale(40,$h,utf8_decode($dt1s[$s][4].' - '.$dt1s[$s][5]),1,0,'L');
                        $pdf->Cell(35,$h,utf8_decode($dt1s[$s][6]),1,0,'L');
                        $pdf->Cell(30,$h,utf8_decode($dt1s[$s][7]),1,0,'L');
                        $pdf->Ln($h);
                        if($pdf->GetY()>160){
                            $pdf->AddPage();
                        }
                        
                    }
                    $dpi = $rowd[$i][1];
                    $trf = $rowter[$t][2];
                }
                #*********************************************************************************************#
                #TIPO2
                #CONSULTAS MOVIMIENTOS
                $dt1e = detalles(2, 2, $rowd[$i][0],$id_responsable);
                $dt1s = detalles(3, 2, $rowd[$i][0],$id_responsable);
                $dt1t = detalles(5, 2, $rowd[$i][0],$id_responsable);
                $dt1r = detalles(4, 2, $rowd[$i][0],$id_responsable);
                $dt1n = detalles(6, 2, $rowd[$i][0],$id_responsable);
                if(!empty($dt1e[0][0]) || !empty($dt1s[0][0]) || !empty($dt1t[0][0]) || !empty($dt1r[0][0]) || !empty($dt1n[0][0]) ){
                    $imp = 1;
                    $dpi = $rowd[$i][1];
                    if($dpi==$dpf){}else {
                        $pdf->SetFont('Arial','B',10);
                        $pdf->Cell(330,10,utf8_decode('DEPENDENCIA: '.$rowd[$i][1].' - '.$rowd[$i][2]),1,0,'L');
                        $pdf->Ln(10);
                    }    
                    $dpf = $dpi;
                    $tri = $rowter[$t][2];
                    if($tri==$trf){}else {
                        $pdf->SetFont('Arial','BI',10);
                        $pdf->Cell(330,10,utf8_decode('RESPONSABLE: '.$rowter[$t][1].' - '.$rowter[$t][2]),1,0,'L');
                        $pdf->Ln(10);
                    }
                    $pdf->SetFont('Arial','B',10);
                    $pdf->Cell(330,10,utf8_decode('TIPO INVENTARIO: '.$ti[1][1]),1,0,'L');
                    $pdf->Ln(10);
                    $pdf->SetFont('Arial','B',10);
                    
                    $pdf->Cell(20,10, utf8_decode(''),1,0,'C');
                    $pdf->Cell(40,10,utf8_decode(''),1,0,'C');
                    $pdf->Cell(20,10,utf8_decode(''),1,0,'C');
                    $pdf->Cell(15,10,utf8_decode(''),1,0,'C');
                    $pdf->Cell(43,10,utf8_decode(''),1,0,'C');
                    $pdf->Cell(48,10,utf8_decode(''),1,0,'C');
                    $pdf->Cell(48,10,utf8_decode(''),1,0,'C');
                    $pdf->Cell(48,10,utf8_decode(''),1,0,'C');
                    $pdf->Cell(48,10,utf8_decode(''),1,0,'C');
                    
                    $pdf->Setx(10);
                    $pdf->Cell(20,10, utf8_decode('CÓDIGO'),0,0,'C');
                    $pdf->Cell(40,10,utf8_decode('NOMBRE'),0,0,'C');
                    $pdf->Cell(20,10,utf8_decode('VALOR'),0,0,'C');
                    $pdf->Cell(15,10,utf8_decode('PLACA'),0,0,'C');
                    $pdf->Cell(43,10,utf8_decode('ESPECIFICACIONES'),0,0,'C');
                    $pdf->Cell(48,5,utf8_decode('ENTRADA'),0,0,'C');
                    $pdf->Cell(48,5,utf8_decode('SALIDA'),0,0,'C');
                    $pdf->Cell(48,5,utf8_decode('TRASLADO'),0,0,'C');
                    $pdf->Cell(48,5,utf8_decode('REINTEGRO'),0,0,'C');
                    $pdf->Ln(5);
                    $pdf->Cell(20,5, utf8_decode(''),0,0,'C');
                    $pdf->Cell(40,5,utf8_decode(''),0,0,'C');
                    $pdf->Cell(20,5,utf8_decode(''),0,0,'C');
                    $pdf->Cell(15,5,utf8_decode(''),0,0,'C');
                    $pdf->Cell(43,5,utf8_decode(''),0,0,'C');
                    $pdf->Cell(16,5,utf8_decode('TIPO'),1,0,'C');
                    $pdf->Cell(17,5,utf8_decode('NÚMERO'),1,0,'C');
                    $pdf->Cell(15,5,utf8_decode('FECHA'),1,0,'C');
                    $pdf->Cell(16,5,utf8_decode('TIPO'),1,0,'C');
                    $pdf->Cell(17,5,utf8_decode('NÚMERO'),1,0,'C');
                    $pdf->Cell(15,5,utf8_decode('FECHA'),1,0,'C');
                    $pdf->Cell(16,5,utf8_decode('TIPO'),1,0,'C');
                    $pdf->Cell(17,5,utf8_decode('NÚMERO'),1,0,'C');
                    $pdf->Cell(15,5,utf8_decode('FECHA'),1,0,'C');
                    $pdf->Cell(16,5,utf8_decode('TIPO'),1,0,'C');
                    $pdf->Cell(17,5,utf8_decode('NÚMERO'),1,0,'C');
                    $pdf->Cell(15,5,utf8_decode('FECHA'),1,0,'C');
                    $pdf->Ln(5);
                    $pdf->SetFont('Arial','',8);
                    if($pdf->GetY()>150){
                        $pdf->AddPage();
                    }
                    ###ENTRADAS 
                    for ($e = 0; $e < count($dt1e); $e++) {
                        #Datos Producto 
                        $da = $dt1e[$e][8];
                        $dp = datosp($dt1e[$e][9]);
                        $htde = $dp[0][2].chr(10);
                        for ($p = 0; $p < count($dp); $p++) {
                            $htde.=$dp[$p][0].': '.$dp[$p][1].chr(10);
                        }
                        if(strlen($htde)>250){
                            if($pdf->GetY()>140){
                                $pdf->AddPage();
                            }
                        }
                        $x  = $pdf->GetX();
                        $y  = $pdf->GetY();
                        
                        $pdf->CellFitScale(20,5, '',0,0,'L');
                        $pdf->MultiCell(40,5,utf8_decode($dt1e[$e][2]),0,'L');
                        $y1 = $pdf->GetY()-$y;
                        $pdf->SetXY($x+95, $y);
                        $pdf->MultiCell(43,4,utf8_decode($htde),0,'L');
                        $y2 = $pdf->GetY()-$y;
                        $y3 = $y2;
                        #* Entrada
                        $pdf->SetXY($x+138, $y);
                        $pdf->MultiCell(16,4,utf8_decode($dt1e[$e][4].' - '.ucwords($dt1e[$e][5])),0,'L');
                        $y3 = $pdf->GetY()-$y;
                        
                        
                        $h  = max($y1, $y2, $y3);
                        $pdf->SetXY($x, $y);
                        $pdf->CellFitScale(20,$h, utf8_decode($dt1e[$e][1]),1,0,'L');
                        $pdf->Cell(40,$h,utf8_decode(''),1,0,'L');
                        $pdf->Cell(20,$h,number_format($dt1e[$e][3],2,'.',','),1,0,'R');
                        $pdf->Cell(15,$h,utf8_decode($dt1e[$e][10]),1,0,'L');
                        $pdf->Cell(43,$h,utf8_decode(''),1,0,'L');
                        $pdf->Cell(16,$h,utf8_decode(''),1,0,'L');
                        $pdf->Cell(17,$h,utf8_decode($dt1e[$e][6]),1,0,'L');
                        $pdf->Cell(15,$h,utf8_decode($dt1e[$e][7]),1,0,'L');
                        $pdf->Cell(16,$h,utf8_decode(''),1,0,'L');
                        $pdf->Cell(17,$h,utf8_decode(''),1,0,'L');
                        $pdf->Cell(15,$h,utf8_decode(''),1,0,'L');
                        $pdf->Cell(16,$h,utf8_decode(''),1,0,'L');
                        $pdf->Cell(17,$h,utf8_decode(''),1,0,'L');
                        $pdf->Cell(15,$h,utf8_decode(''),1,0,'L');
                        $pdf->Cell(16,$h,utf8_decode(''),1,0,'L');
                        $pdf->Cell(17,$h,utf8_decode(''),1,0,'L');
                        $pdf->Cell(15,$h,utf8_decode(''),1,0,'L');
                        $pdf->Ln($h);
                        if($pdf->GetY()>160){
                            $pdf->AddPage();
                        }
                    }
                    
                    ###SALIDAS 
                    for ($s = 0; $s < count($dt1s); $s++) {
                        $da = detafectado($dt1s[$s][8]);
                        $a  = 0;
                        $dae="";
                        $das="";
                        $dat="";
                        $dar="";
                        while ($a==0){
                            switch ($da[0][8]) {
                                case 2:
                                    $dae = $da;
                                break;
                                case 3:
                                    $das = $da;
                                break;
                                case 5:
                                    $dat = $da;
                                break;
                                case 6:
                                    $dar = $da;
                                break;
                            }
                            if(!empty($da[0][9])){
                               $da = detafectado($da[0][9]); 
                            } else {
                                $a = 1;
                            }
                        }
                        #Datos Producto 
                        $dp = datosp($dt1s[$s][9]);
                        
                        $htde = $dp[0][2].chr(10);
                        for ($p = 0; $p < count($dp); $p++) {
                            $htde.=$dp[$p][0].': '.$dp[$p][1].chr(10);
                        }
                        if(strlen($htde)>250){
                            if($pdf->GetY()>140){
                                $pdf->AddPage();
                            }
                        }
                        $x  = $pdf->GetX();
                        $y  = $pdf->GetY();
                        
                        $pdf->CellFitScale(20,5, '',0,0,'L');
                        $pdf->MultiCell(40,5,utf8_decode($dt1s[$s][2]),0,'L');
                        $y1 = $pdf->GetY()-$y;
                        $pdf->SetXY($x+95, $y);
                        $pdf->MultiCell(43,4,utf8_decode($htde),0,'L');
                        $y2 = $pdf->GetY()-$y;
                        $y3 = $y2;
                        #* Entrada
                        if(!empty($dae[0][0])){
                            $pdf->SetXY($x+138, $y);
                            $pdf->MultiCell(16,4,utf8_decode($dae[0][4].' - '.ucwords($dae[0][5])),0,'L');
                            $y3 = $pdf->GetY()-$y;
                        }
                        #*Salida
                        $pdf->SetXY($x+186, $y);
                        $pdf->MultiCell(16,4,utf8_decode($dt1s[$s][4].' - '.ucwords($dt1s[$s][5])),0,'L');
                        $y4 = $pdf->GetY()-$y;
                        
                        #*Traslado
                        $y5 = $y4;
                        if(!empty($dat[0][0])){
                            $pdf->SetXY($x+234, $y);
                            $pdf->MultiCell(16,4,utf8_decode($dat[0][4].' - '.ucwords($dat[0][5])),0,'L');
                            $y5 = $pdf->GetY()-$y;
                        } 
                        $y6 = $y5;
                        #*Reintegro
                        if(!empty($dar[0][0])){
                            $pdf->SetXY($x+234, $y);
                            $pdf->MultiCell(16,4,utf8_decode($dar[0][4].' - '.ucwords($dar[0][5])),0,'L');
                            $y6 = $pdf->GetY()-$y;
                        } 
                        $h  = max($y1, $y2, $y3, $y4, $y5, $y6);
                        
                        #****Datos
                        $pdf->SetXY($x, $y);
                        $pdf->CellFitScale(20,$h, utf8_decode($dt1s[$s][1]),1,0,'L');
                        $pdf->Cell(40,$h,'',1,0,'L');
                        $pdf->Cell(20,$h,number_format($dt1s[$s][3],2,'.',','),1,0,'R');
                        $pdf->Cell(15,$h,utf8_decode($dt1s[$s][10]),1,0,'C');
                        $pdf->Cell(43,$h,'',1,0,'L');
                        #* Entrada
                        if(!empty($dae[0][0])){
                            $pdf->Cell(16,$h,'',1,0,'L');
                            $pdf->CellFitScale(17,$h,utf8_decode($dae[0][6]),1,0,'L');
                            $pdf->CellFitScale(15,$h,utf8_decode($dae[0][7]),1,0,'L');
                        } else {
                            $pdf->Cell(16,$h,utf8_decode(''),1,0,'L');
                            $pdf->Cell(17,$h,utf8_decode(''),1,0,'L');
                            $pdf->Cell(15,$h,utf8_decode(''),1,0,'L');
                        }
                        
                        $pdf->Cell(16,$h,'',1,0,'L');
                        $pdf->CellFitScale(17,$h,utf8_decode($dt1s[$s][6]),1,0,'L');
                        $pdf->CellFitScale(15,$h,utf8_decode($dt1s[$s][7]),1,0,'L');
                        
                        if(!empty($dat[0][0])){
                            $pdf->Cell(16,$h,utf8_decode(''),1,0,'L');
                            $pdf->CellFitScale(17,$h,utf8_decode($dat[0][6]),1,0,'L');
                            $pdf->CellFitScale(15,$h,utf8_decode($dat[0][7]),1,0,'L');
                        } else {
                            $pdf->Cell(16,$h,utf8_decode(''),1,0,'L');
                            $pdf->Cell(17,$h,utf8_decode(''),1,0,'L');
                            $pdf->Cell(15,$h,utf8_decode(''),1,0,'L');
                        }
                        if(!empty($dar[0][0])){
                            $pdf->Cell(16,$h,utf8_decode(''),1,0,'L');
                            $pdf->CellFitScale(17,$h,utf8_decode($dar[0][6]),1,0,'L');
                            $pdf->CellFitScale(15,$h,utf8_decode($dar[0][7]),1,0,'L');
                            
                        } else {
                            $pdf->Cell(16,$h,utf8_decode(''),1,0,'L');
                            $pdf->Cell(17,$h,utf8_decode(''),1,0,'L');
                            $pdf->Cell(15,$h,utf8_decode(''),1,0,'L');
                        }
                        $pdf->Ln($h);
                        if($pdf->GetY()>160){
                            $pdf->AddPage();
                        }
                        
                    }
                    ###TRASLADOS
                    for ($tr = 0; $tr < count($dt1t); $tr++) {
                        $da  = detafectado($dt1t[$tr][8]);
                        $a   = 0;
                        $dae = "";
                        $das = "";
                        $dat = "";
                        $dar = "";
                        while ($a==0){
                            switch ($da[0][8]) {
                                case 2:
                                    $dae = $da;
                                break;
                                case 3:
                                    $das = $da;
                                break;
                                case 5:
                                    $dat = $da;
                                break;
                                case 6:
                                    $dar = $da;
                                break;
                            }
                            if(!empty($da[0][9])){
                               $da = detafectado($da[0][9]); 
                            } else {
                                $a = 1;
                            }
                        }
                        #Datos Producto 
                        $da = $dt1t[$tr][8];
                        $dp = datosp($dt1t[$tr][9]);
                        $htde = $dp[0][2].chr(10);
                        for ($p = 0; $p < count($dp); $p++) {
                            $htde.=$dp[$p][0].': '.$dp[$p][1].chr(10);
                        }
                        if(strlen($htde)>250){
                            if($pdf->GetY()>140){
                                $pdf->AddPage();
                            }
                        }
                        $x  = $pdf->GetX();
                        $y  = $pdf->GetY();
                        
                        $pdf->CellFitScale(20,5, '',0,0,'L');
                        $pdf->MultiCell(40,5,utf8_decode($dt1t[$tr][2]),0,'L');
                        $y1 = $pdf->GetY()-$y;
                        $pdf->SetXY($x+95, $y);
                        $pdf->MultiCell(43,4,utf8_decode($htde),0,'L');
                        $y2 = $pdf->GetY()-$y;
                        $y3 = $y2;
                        #* Entrada
                        if(!empty($dae[0][0])){
                            $pdf->SetXY($x+138, $y);
                            $pdf->MultiCell(16,4,utf8_decode($dae[0][4].' - '.ucwords($dae[0][5])),0,'L');
                            $y3 = $pdf->GetY()-$y;
                        }
                        #*Salida
                        $y4 = $y3;
                        if(!empty($das[0][0])){
                            $pdf->SetXY($x+186, $y);
                            $pdf->MultiCell(16,4,utf8_decode($das[0][4].' - '.ucwords($das[0][5])),0,'L');
                            $y4 = $pdf->GetY()-$y;
                        }
                        
                        
                        
                        #*Traslado
                        $y5 = $y4;
                        $pdf->SetXY($x+234, $y);
                        $pdf->MultiCell(16,4,utf8_decode($dt1t[$tr][4].' - '.ucwords($dt1t[$tr][5])),0,'L');
                        $y5 = $pdf->GetY()-$y;
                        
                        $y6 = $y5;
                        #*Reintegro
                        if(!empty($dar[0][0])){
                            $pdf->SetXY($x+234, $y);
                            $pdf->MultiCell(16,4,utf8_decode($dar[0][4].' - '.ucwords($dar[0][5])),0,'L');
                            $y6 = $pdf->GetY()-$y;
                        } 
                        $h  = max($y1, $y2, $y3, $y4, $y5, $y6);
                        
                        #****Datos
                        $pdf->SetXY($x, $y);
                        $pdf->CellFitScale(20,$h, utf8_decode($dt1t[$tr][1]),1,0,'L');
                        $pdf->Cell(40,$h,'',1,0,'L');
                        $pdf->Cell(20,$h,number_format($dt1t[$tr][3],2,'.',','),1,0,'R');
                        $pdf->Cell(15,$h,utf8_decode($dt1t[$tr][10]),1,0,'C');
                        $pdf->Cell(43,$h,'',1,0,'L');
                        #* Entrada
                        if(!empty($dae[0][0])){
                            $pdf->Cell(16,$h,'',1,0,'L');
                            $pdf->CellFitScale(17,$h,utf8_decode($dae[0][6]),1,0,'L');
                            $pdf->CellFitScale(15,$h,utf8_decode($dae[0][7]),1,0,'L');
                        } else {
                            $pdf->Cell(16,$h,utf8_decode(''),1,0,'L');
                            $pdf->Cell(17,$h,utf8_decode(''),1,0,'L');
                            $pdf->Cell(15,$h,utf8_decode(''),1,0,'L');
                        }
                        
                        if(!empty($das[0][0])){
                            $pdf->Cell(16,$h,'',1,0,'L');
                            $pdf->CellFitScale(17,$h,utf8_decode($das[0][6]),1,0,'L');
                            $pdf->CellFitScale(15,$h,utf8_decode($das[0][7]),1,0,'L');
                        } else {
                            $pdf->Cell(16,$h,utf8_decode(''),1,0,'L');
                            $pdf->Cell(17,$h,utf8_decode(''),1,0,'L');
                            $pdf->Cell(15,$h,utf8_decode(''),1,0,'L');
                        }
                        
                        $pdf->Cell(16,$h,'',1,0,'L');
                        $pdf->CellFitScale(17,$h,utf8_decode($dt1t[$tr][6]),1,0,'L');
                        $pdf->CellFitScale(15,$h,utf8_decode($dt1t[$tr][7]),1,0,'L');
                        
                        
                        if(!empty($dar[0][0])){
                            $pdf->Cell(16,$h,utf8_decode(''),1,0,'L');
                            $pdf->CellFitScale(17,$h,utf8_decode($dar[0][6]),1,0,'L');
                            $pdf->CellFitScale(15,$h,utf8_decode($dar[0][7]),1,0,'L');
                            
                        } else {
                            $pdf->Cell(16,$h,utf8_decode(''),1,0,'L');
                            $pdf->Cell(17,$h,utf8_decode(''),1,0,'L');
                            $pdf->Cell(15,$h,utf8_decode(''),1,0,'L');
                        }
                        $pdf->Ln($h);
                        if($pdf->GetY()>160){
                            $pdf->AddPage();
                        }
                        
                    }
                    
                    ###REINTEGROS
                    for ($n = 0; $n < count($dt1n); $n++) {
                        $da = detafectado($dt1n[$s][8]);
                        $a  = 0;
                        $dae="";
                        $das="";
                        $dat="";
                        $dar="";
                        while ($a==0){
                            switch ($da[0][8]) {
                                case 2:
                                    $dae = $da;
                                break;
                                case 3:
                                    $das = $da;
                                break;
                                case 5:
                                    $dat = $da;
                                break;
                                case 6:
                                    $dar = $da;
                                break;
                            }
                            if(!empty($da[0][9])){
                               $da = detafectado($da[0][9]); 
                            } else {
                                $a = 1;
                            }
                        }
                        #Datos Producto 
                        $da = $dt1n[$n][8];
                        $dp = datosp($dt1n[$n][9]);
                        
                        $htde = $dp[0][2].chr(10);
                        for ($p = 0; $p < count($dp); $p++) {
                            $htde.=$dp[$p][0].': '.$dp[$p][1].chr(10);
                        }
                        if(strlen($htde)>250){
                            if($pdf->GetY()>140){
                                $pdf->AddPage();
                            }
                        }
                        $x  = $pdf->GetX();
                        $y  = $pdf->GetY();
                        
                        $pdf->CellFitScale(20,5, '',0,0,'L');
                        $pdf->MultiCell(40,5,utf8_decode($dt1n[$n][2]),0,'L');
                        $y1 = $pdf->GetY()-$y;
                        $pdf->SetXY($x+95, $y);
                        $pdf->MultiCell(43,4,utf8_decode($htde),0,'L');
                        $y2 = $pdf->GetY()-$y;
                        $y3 = $y2;
                        #* Entrada
                        if(!empty($dae[0][0])){
                            $pdf->SetXY($x+138, $y);
                            $pdf->MultiCell(16,4,utf8_decode($dae[0][4].' - '.ucwords($dae[0][5])),0,'L');
                            $y3 = $pdf->GetY()-$y;
                        }
                        #*Salida
                        $y4 = $y3;
                        if(!empty($das[0][0])){
                            $pdf->SetXY($x+186, $y);
                            $pdf->MultiCell(16,4,utf8_decode($das[0][4].' - '.ucwords($das[0][5])),0,'L');
                            $y4 = $pdf->GetY()-$y;
                        }
                        
                        
                        #*Traslado
                        $y5 = $y4;
                        if(!empty($dat[0][0])){
                            $pdf->SetXY($x+234, $y);
                            $pdf->MultiCell(16,4,utf8_decode($dat[0][4].' - '.ucwords($dat[0][5])),0,'L');
                            $y5 = $pdf->GetY()-$y;
                        }
                        $y6 = $y5;
                        $pdf->SetXY($x+282, $y);
                        $pdf->MultiCell(16,4,utf8_decode($dt1n[$n][4].' - '.ucwords($dt1n[$n][5])),0,'L');
                        $y5 = $pdf->GetY()-$y;
                        $h  = max($y1, $y2, $y3, $y4, $y5, $y6);
                        
                        #****Datos
                        $pdf->SetXY($x, $y);
                        $pdf->CellFitScale(20,$h, utf8_decode($dt1n[$n][1]),1,0,'L');
                        $pdf->Cell(40,$h,'',1,0,'L');
                        $pdf->Cell(20,$h,number_format($dt1n[$n][3],2,'.',','),1,0,'R');
                        $pdf->Cell(15,$h,utf8_decode($dt1n[$n][10]),1,0,'C');
                        $pdf->Cell(43,$h,'',1,0,'L');
                        #* Entrada
                        if(!empty($dae[0][0])){
                            $pdf->Cell(16,$h,'',1,0,'L');
                            $pdf->CellFitScale(17,$h,utf8_decode($dae[0][6]),1,0,'L');
                            $pdf->CellFitScale(15,$h,utf8_decode($dae[0][7]),1,0,'L');
                        } else {
                            $pdf->Cell(16,$h,utf8_decode(''),1,0,'L');
                            $pdf->Cell(17,$h,utf8_decode(''),1,0,'L');
                            $pdf->Cell(15,$h,utf8_decode(''),1,0,'L');
                        }
                        
                        if(!empty($das[0][0])){
                            $pdf->Cell(16,$h,'',1,0,'L');
                            $pdf->CellFitScale(17,$h,utf8_decode($das[0][6]),1,0,'L');
                            $pdf->CellFitScale(15,$h,utf8_decode($das[0][7]),1,0,'L');
                        } else {
                            $pdf->Cell(16,$h,utf8_decode(''),1,0,'L');
                            $pdf->Cell(17,$h,utf8_decode(''),1,0,'L');
                            $pdf->Cell(15,$h,utf8_decode(''),1,0,'L');
                        }
                        
                        if(!empty($dat[0][0])){
                            $pdf->Cell(16,$h,'',1,0,'L');
                            $pdf->CellFitScale(17,$h,utf8_decode($dat[0][6]),1,0,'L');
                            $pdf->CellFitScale(15,$h,utf8_decode($dat[0][7]),1,0,'L');
                        }else {
                            $pdf->Cell(16,$h,utf8_decode(''),1,0,'L');
                            $pdf->Cell(17,$h,utf8_decode(''),1,0,'L');
                            $pdf->Cell(15,$h,utf8_decode(''),1,0,'L');
                        }
                        
                        $pdf->Cell(16,$h,'',1,0,'L');
                        $pdf->CellFitScale(17,$h,utf8_decode($dt1n[$n][6]),1,0,'L');
                        $pdf->CellFitScale(15,$h,utf8_decode($dt1n[$n][7]),1,0,'L');
                        
                        $pdf->Ln($h);
                        if($pdf->GetY()>160){
                            $pdf->AddPage();
                        }
                        
                    }
                    $dpi = $rowd[$i][1];
                    $trf = $rowter[$t][2];
                }
                
                #* Firmas
                if($imp ==1){
                    $pdf->Ln(15);
                    $sql = "SELECT  DISTINCT  IF(CONCAT_WS(' ',ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) = '',
                                ter.razonsocial,
                                CONCAT_WS(' ',ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos)) as nombre,
                                car.nombre,
                                ti.nombre, CONCAT_WS(' ',ter.numeroidentificacion, ter.digitoverficacion),
                                UPPER(tpr.nombre), ter.id_unico
                    FROM      gf_dependencia d
                    LEFT JOIN gf_tipo_documento tpd         ON tpd.dependencia    = d.id_unico
                    LEFT JOIN gf_responsable_documento doc  ON doc.tipodocumento  = tpd.id_unico 
                    LEFT JOIN gf_tipo_responsable tpr       ON tpr.id_unico       = doc.tiporesponsable
                    LEFT JOIN gg_tipo_relacion tprl         ON doc.tipo_relacion  = tprl.id_unico
                    LEFT JOIN gf_tercero ter                ON doc.tercero        = ter.id_unico
                    LEFT JOIN gf_cargo_tercero cter         ON cter.tercero       = ter.id_unico
                    LEFT JOIN gf_cargo car                  ON cter.cargo         = car.id_unico
                    LEFT JOIN gf_tipo_identificacion ti     ON ti.id_unico        = ter.tipoidentificacion
                    WHERE     d.id_unico = $id_dependencia AND ter.id_unico != $id_responsable 
                    ORDER BY  doc.tipodocumento ASC";//
                    $res = $mysqli->query($sql);
                    $row = $res->fetch_all(MYSQLI_NUM);
                    $pdf->SetFont('Arial', 'B', 8);
                    $data_firmas = $row;
                    $xxx = 1;                    
                    $yyy = $pdf->GetY();
                    $pdf->Cell(60, 0, '', 'B');
                    $pdf->Ln(3);
                    $pdf->Cell(190, 2, utf8_decode($rowter[$t][1]), 0, 0, 'L');
                    $pdf->Ln(5);
                    $pdf->Cell(190,2,utf8_decode($rowter[$t][3]),0,0,'L');
                    foreach($data_firmas as $row_firma){
                        if($xxx == 0){
                            $yyy = $yy1;
                        }
                        $xxx++;
                        if($xxx % 2 == 0){
                            $pdf->SetXY(140, $yyy);
                            $pdf->Cell(60, 0, '', 'B');
                            $pdf->Ln(3);
                            $pdf->SetX(140);
                            $pdf->Cell(190, 2, utf8_decode($row_firma[0]), 0, 0, 'L');
                            $pdf->Ln(5);
                            $pdf->SetX(140);
                            $pdf->Cell(190,2,utf8_decode($row_firma[1]),0,0,'L');
                            $pdf->Ln(40);
                        }else{
                            $yyy = $pdf->GetY();
                            $pdf->Cell(60, 0, '', 'B');
                            $pdf->Ln(3);
                            $pdf->Cell(190, 2, utf8_decode($row_firma[0]), 0, 0, 'L');
                            $pdf->Ln(5);
                            $pdf->Cell(190,2,utf8_decode($row_firma[1]),0,0,'L');
                        }
                    }
                    if($t < (count($rowter)-1)){
                        $pdf->AddPage();
                        $imp =1;
                    } else {
                        $imp =0;
                    }
                } else {
                    $imp =0;
                }
            }
            if($imp ==0){
                if($i < (count($rowd)-1)){
                    $pdf->AddPage();
                }
            }
        }
    while (ob_get_length()) {
        ob_end_clean();
    }

    $pdf->Output(0,'Informe_Auxiliar_Retenciones ('.date('d/m/Y').').pdf',0);
} else {

    header("Content-type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=Informe_Listado_Dependencia.xls");
    ob_start();
    ?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">
    <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Listado de Inventarios Por Dependencia General</title>
    </head>
    <body>
    <table width="100%" border="1" cellspacing="0" cellpadding="0">
        <th colspan="21" align="center"><strong>
            <br/>&nbsp;
            <br/><?php echo $razonsocial ?>
            <br/><?php echo $nombreIdent.' : '.$numeroIdent."<br/>".$direccinTer.' Tel:'.$telefonoTer ?>
            <br/>&nbsp;
            <br/>LISTADO DE INVENTARIOS POR DEPENDENCIA GENERAL       
            <br/>FECHA CORTE <?php echo $_REQUEST['txtFechaFinal'] ?>
            <br/>&nbsp;                 
            </strong> 
        </th>   
        <?php 
        # BUSCAR NOMBRE TIPO INVENTARIO 
        $html = "";
        $ti = $con->Listar("SELECT DISTINCT ti.id_unico, UPPER(ti.nombre) 
            FROM gf_tipo_inventario ti 
            WHERE id_unico IN (1,2)
            ORDER BY ti.id_unico ASC");

        $pi1 = $ti[0][0];
        $pi2 = $ti[1][0];
        #* Buscar Dependencias 
        $rowd = $con->Listar("SELECT DISTINCT d.id_unico, d.sigla, UPPER(d.nombre) 
                FROM gf_movimiento m 
                LEFT JOIN gf_dependencia d ON m.dependencia = d.id_unico 
                WHERE (d.id_unico BETWEEN $depI AND $depF) AND d.compania = $compania 
                AND m.fecha <='$fecha'");
        for ($i = 0; $i < count($rowd); $i++) {
            $id_dependencia =  $rowd[$i][0];
            $rowter = $con->Listar("SELECT DISTINCT t.id_unico, 
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
                 t.apellidodos)) AS NOMBRE, t.numeroidentificacion 
                FROM gf_movimiento m
                LEFT JOIN gf_tercero t ON m.tercero = t.id_unico 
                WHERE m.dependencia =$id_dependencia AND m.fecha <='$fecha'");
            $dpi = '';
            $dpf = '';
            $tri = '';
            $trf = '';
            for ($t = 0; $t < count($rowter); $t++) {
                $id_responsable = $rowter[$t][0];
                #CONSULTAS MOVIMIENTOS
                $dt1e = detaf(2, 1, $rowd[$i][0],$id_responsable);
                $dt1s = detaf(3, 1, $rowd[$i][0],$id_responsable);
                if(!empty($dt1e[0][0]) || !empty($dt1s[0][0]) ){
                    $dpi = $rowd[$i][1];
                    if($dpi==$dpf){}else {
                        $html .= '<tr>';
                        $html .= '<td colspan="21"><strong><br/>&nbsp;DEPENDENCIA: '.$rowd[$i][1].' - '.$rowd[$i][2].'<br/>&nbsp;</strong></td>';
                        $html .= '</tr>';
                    }
                    $tri = $rowter[$t][2];
                    if($tri==$trf){}else {
                        $html .= '<tr>';
                        $html .= '<td colspan="21"><strong><i><br/>&nbsp;RESPONSABLE: '.$rowter[$t][1].' - '.$rowter[$t][2].'<br/>&nbsp;</i></strong></td>';
                        $html .= '</tr>';
                    }
                    $html .= '<tr>';
                    $html .= '<td colspan="21"><strong><br/>&nbsp;TIPO INVENTARIO: '.$ti[0][1].'<br/>&nbsp;</strong></td>';
                    $html .= '</tr>'; 
                    $html .= '<tr>';
                    $html .= '<td rowspan="2" colspan="3" align="center"><strong>CÓDIGO</strong></td>';
                    $html .= '<td colspan="3" rowspan="2" align="center"><strong>NOMBRE</strong></td>';
                    $html .= '<td colspan="3" rowspan="2" align="center"><strong>VALOR</strong></td>';
                    $html .= '<td colspan="6" align="center"><strong>ENTRADA</strong></td>';
                    $html .= '<td colspan="6" align="center"><strong>SALIDA</strong></td>';
                    $html .= '</tr>';
                    $html .= '<tr>';
                    $html .= '<td colspan="2" align="center"><strong>TIPO</strong></td>';
                    $html .= '<td colspan="2" align="center"><strong>NÚMERO</strong></td>';
                    $html .= '<td colspan="2" align="center"><strong>FECHA</strong></td>';
                    $html .= '<td colspan="2" align="center"><strong>TIPO</strong></td>';
                    $html .= '<td colspan="2" align="center"><strong>NÚMERO</strong></td>';
                    $html .= '<td colspan="2" align="center"><strong>FECHA</strong></td>';
                    $html .= '</tr>';
                    for ($e = 0; $e < count($dt1e); $e++) {
                        $html .= '<tr>';
                        $html .= '<td colspan="3">'.$dt1e[$e][1].'</td>';
                        $html .= '<td colspan="3">'.$dt1e[$e][2].'</td>';
                        $html .= '<td colspan="3">'.number_format($dt1e[$e][3],2,'.',',').'</td>';
                        $html .= '<td colspan="2">'.$dt1e[$e][4].' - '.ucwords($dt1e[$e][5]).'</td>';
                        $html .= '<td colspan="2">'.$dt1e[$e][6].'</td>';
                        $html .= '<td colspan="2">'.$dt1e[$e][7].'</td>';
                        $html .= '<td colspan="2"></td>';
                        $html .= '<td colspan="2"></td>';
                        $html .= '<td colspan="2"></td>';
                        $html .= '</tr>';
                    }
                    for ($s = 0; $s < count($dt1s); $s++) {
                        #Buscar Afectado  
                        $ent   = detafectado($dt1s[$s][8]);
                        $html .= '<tr>';
                        $html .= '<td colspan="3">'.$dt1s[$s][1].'</td>';
                        $html .= '<td colspan="3">'.$dt1s[$s][2].'</td>';
                        $html .= '<td colspan="3">'.number_format($dt1s[$s][3],2,'.',',').'</td>';
                        $html .= '<td colspan="2">'.$ent[0][4].' - '.$ent[0][5].'</td>';
                        $html .= '<td colspan="2">'.$ent[0][6].'</td>';
                        $html .= '<td colspan="2">'.$ent[0][7].'</td>';                
                        $html .= '<td colspan="2">'.$dt1s[$s][4].' - '.ucwords($dt1s[$s][5]).'</td>';
                        $html .= '<td colspan="2">'.$dt1s[$s][6].'</td>';
                        $html .= '<td colspan="2">'.$dt1s[$s][7].'</td>';
                        $html .= '</tr>';
                    }
                    $dpi = $rowd[$i][1];
                    $trf = $rowter[$t][2];
                }
                #*********************************************************************************************#
                #TIPO2
                #CONSULTAS MOVIMIENTOS
                $dt1e = detalles(2, 2, $rowd[$i][0],$id_responsable);
                $dt1s = detalles(3, 2, $rowd[$i][0],$id_responsable);
                $dt1t = detalles(5, 2, $rowd[$i][0],$id_responsable);
                $dt1r = detalles(4, 2, $rowd[$i][0],$id_responsable);
                $dt1n = detalles(6, 2, $rowd[$i][0],$id_responsable);
                if(!empty($dt1e[0][0]) || !empty($dt1s[0][0]) || !empty($dt1t[0][0]) || !empty($dt1r[0][0]) || !empty($dt1n[0][0]) ){
                    $dpi = $rowd[$i][1];
                    if($dpi==$dpf){}else {
                        $html .= '<tr>';
                        $html .= '<td colspan="21"><strong><br/>&nbsp;DEPENDENCIA: '.$rowd[$i][1].' - '.$rowd[$i][2].'<br/>&nbsp;</strong></td>';
                        $html .= '</tr>';
                    }    
                    $tri = $rowter[$t][2];
                    if($tri==$trf){}else {
                        $html .= '<tr>';
                        $html .= '<td colspan="21"><strong><i><br/>&nbsp;RESPONSABLE: '.$rowter[$t][1].' - '.$rowter[$t][2].'<br/>&nbsp;</i></strong></td>';
                        $html .= '</tr>';
                    }
                    $html .= '<tr>';
                    $html .= '<td colspan="21"><strong><br/>&nbsp;TIPO INVENTARIO: '.$ti[1][1].'<br/>&nbsp;</strong></td>';
                    $html .= '</tr>'; 
                    $html .= '<tr>';
                    $html .= '<td rowspan="2" colspan="2" align="center"><strong>CÓDIGO</strong></td>';
                    $html .= '<td rowspan="2" colspan="2" align="center"><strong>NOMBRE</strong></td>';
                    $html .= '<td rowspan="2" align="center"><strong>VALOR</strong></td>';
                    $html .= '<td rowspan="2" align="center"><strong>PLACA</strong></td>';
                    $html .= '<td rowspan="2" colspan="2"  align="center"><strong>ESPECIFICACIONES</strong></td>';
                    $html .= '<td colspan="3" align="center"><strong>ENTRADA</strong></td>';
                    $html .= '<td colspan="3" align="center"><strong>SALIDA</strong></td>';
                    $html .= '<td colspan="3" align="center"><strong>TRASLADO</strong></td>';
                    $html .= '<td colspan="3" align="center"><strong>REINTEGRO</strong></td>';
                    $html .= '</tr>';
                    $html .= '<tr>';
                    $html .= '<td align="center"><strong>TIPO</strong></td>';
                    $html .= '<td align="center"><strong>NÚMERO</strong></td>';
                    $html .= '<td align="center"><strong>FECHA</strong></td>';
                    $html .= '<td align="center"><strong>TIPO</strong></td>';
                    $html .= '<td align="center"><strong>NÚMERO</strong></td>';
                    $html .= '<td align="center"><strong>FECHA</strong></td>';
                    $html .= '<td align="center"><strong>TIPO</strong></td>';
                    $html .= '<td align="center"><strong>NÚMERO</strong></td>';
                    $html .= '<td align="center"><strong>FECHA</strong></td>';
                    $html .= '<td align="center"><strong>TIPO</strong></td>';
                    $html .= '<td align="center"><strong>NÚMERO</strong></td>';
                    $html .= '<td align="center"><strong>FECHA</strong></td>';
                    $html .= '</tr>';

                    ###ENTRADAS 
                    for ($e = 0; $e < count($dt1e); $e++) {
                        #Datos Producto 
                        $da = $dt1e[$e][8];
                        $dp = datosp($dt1e[$e][9]);
                        $html .= '<tr>';
                        $html .= '<td colspan="2">'.$dt1e[$e][1].'</td>';
                        $html .= '<td colspan="2">'.$dt1e[$e][2].'</td>';
                        $html .= '<td>'.number_format($dt1e[$e][3],2,'.',',').'</td>';
                        $html .= '<td>'.$dt1e[$e][10].'</td>';
                        $html .= '<td colspan="2">';
                        $html .= $dp[0][2].'<br/>';
                        for ($p = 0; $p < count($dp); $p++) {
                            $html.=$dp[$p][0].': '.$dp[$p][1].'<br/>';
                        }
                        $html .= '</td>';
                        $html .= '<td>'.$dt1e[$e][4].' - '.ucwords($dt1e[$e][5]).'</td>';
                        $html .= '<td>'.$dt1e[$e][6].'</td>';
                        $html .= '<td>'.$dt1e[$e][7].'</td>';
                        $html .= '<td></td>';
                        $html .= '<td></td>';
                        $html .= '<td></td>';
                        $html .= '<td></td>';
                        $html .= '<td></td>';
                        $html .= '<td></td>';
                        $html .= '<td></td>';
                        $html .= '<td></td>';
                        $html .= '<td></td>';
                        $html .= '</tr>';
                    }
                    ###SALIDAS 
                    for ($s = 0; $s < count($dt1s); $s++) {
                        $da = detafectado($dt1s[$s][8]);
                        $a  = 0;
                        $dae="";
                        $das="";
                        $dat="";
                        $dar="";
                        while ($a==0){
                            switch ($da[0][8]) {
                                case 2:
                                    $dae = $da;
                                break;
                                case 3:
                                    $das = $da;
                                break;
                                case 5:
                                    $dat = $da;
                                break;
                                case 6:
                                    $dar = $da;
                                break;
                            }
                            if(!empty($da[0][9])){
                               $da = detafectado($da[0][9]); 
                            } else {
                                $a = 1;
                            }
                        }
                        #Datos Producto 
                        $dp = datosp($dt1s[$s][9]);
                        $html .= '<tr>';
                        $html .= '<td colspan="2">'.$dt1s[$s][1].'</td>';
                        $html .= '<td colspan="2">'.$dt1s[$s][2].'</td>';
                        $html .= '<td>'.number_format($dt1s[$s][3],2,'.',',').'</td>';
                        $html .= '<td>'.$dt1s[$s][10].'</td>';
                        $html .= '<td colspan="2">';
                        $html .= $dp[0][2].'<br/>';
                        for ($p = 0; $p < count($dp); $p++) {
                            $html.=$dp[$p][0].': '.$dp[$p][1].'<br/>';
                        }
                        $html .= '</td>';
                        if(!empty($dae[0][0])){
                            $html .= '<td>'.$dae[0][4].' - '.ucwords($dae[0][5]).'</td>';
                            $html .= '<td>'.$dae[0][6].'</td>';
                            $html .= '<td>'.$dae[0][7].'</td>';
                        } else {
                            $html .= '<td></td>';
                            $html .= '<td></td>';
                            $html .= '<td></td>';
                        }
                        $html .= '<td>'.$dt1s[$s][4].' - '.ucwords($dt1s[$s][5]).'</td>';
                        $html .= '<td>'.$dt1s[$s][6].'</td>';
                        $html .= '<td>'.$dt1s[$s][7].'</td>';
                        if(!empty($dat[0][0])){
                            $html .= '<td>'.$dat[0][4].' - '.ucwords($dat[0][5]).'</td>';
                            $html .= '<td>'.$dat[0][6].'</td>';
                            $html .= '<td>'.$dat[0][7].'</td>';
                        } else {
                            $html .= '<td></td>';
                            $html .= '<td></td>';
                            $html .= '<td></td>';
                        }
                        if(!empty($dar[0][0])){
                            $html .= '<td>'.$dar[0][4].' - '.ucwords($dar[0][5]).'</td>';
                            $html .= '<td>'.$dar[0][6].'</td>';
                            $html .= '<td>'.$dar[0][7].'</td>';
                        } else {
                            $html .= '<td></td>';
                            $html .= '<td></td>';
                            $html .= '<td></td>';
                        }
                        $html .= '</tr>';
                    }
                    ###TRASLADOS
                    for ($tr = 0; $tr < count($dt1t); $tr++) {
                        $da  = detafectado($dt1t[$tr][8]);
                        $a   = 0;
                        $dae = "";
                        $das = "";
                        $dat = "";
                        $dar = "";
                        while ($a==0){
                            switch ($da[0][8]) {
                                case 2:
                                    $dae = $da;
                                break;
                                case 3:
                                    $das = $da;
                                break;
                                case 5:
                                    $dat = $da;
                                break;
                                case 6:
                                    $dar = $da;
                                break;
                            }
                            if(!empty($da[0][9])){
                               $da = detafectado($da[0][9]); 
                            } else {
                                $a = 1;
                            }
                        }
                        #Datos Producto 
                        $da = $dt1t[$tr][8];
                        $dp = datosp($dt1t[$tr][9]);
                        $html .= '<tr>';
                        $html .= '<td colspan="2">'.$dt1t[$tr][1].'</td>';
                        $html .= '<td colspan="2">'.$dt1t[$tr][2].'</td>';
                        $html .= '<td>'.number_format($dt1t[$tr][3],2,'.',',').'</td>';
                        $html .= '<td>'.$dt1t[$tr][10].'</td>';
                        $html .= '<td colspan="2">';
                        $html .= $dp[0][2].'<br/>';
                        for ($p = 0; $p < count($dp); $p++) {
                            $html.=$dp[$p][0].': '.$dp[$p][1].'<br/>';
                        }
                        $html .= '</td>';
                        if(!empty($dae[0][0])){
                            $html .= '<td>'.$dae[0][4].' - '.ucwords($dae[0][5]).'</td>';
                            $html .= '<td>'.$dae[0][6].'</td>';
                            $html .= '<td>'.$dae[0][7].'</td>';
                        } else {
                            $html .= '<td></td>';
                            $html .= '<td></td>';
                            $html .= '<td></td>';
                        }
                        if(!empty($das[0][0])){
                            $html .= '<td>'.$das[0][4].' - '.ucwords($das[0][5]).'</td>';
                            $html .= '<td>'.$das[0][6].'</td>';
                            $html .= '<td>'.$das[0][7].'</td>';
                        } else {
                            $html .= '<td></td>';
                            $html .= '<td></td>';
                            $html .= '<td></td>';
                        }
                        $html .= '<td>'.$dt1t[$tr][4].' - '.ucwords($dt1t[$tr][5]).'</td>';
                        $html .= '<td>'.$dt1t[$tr][6].'</td>';
                        $html .= '<td>'.$dt1t[$tr][7].'</td>';                
                        if(!empty($dar[0][0])){
                            $html .= '<td>'.$dar[0][4].' - '.ucwords($dar[0][5]).'</td>';
                            $html .= '<td>'.$dar[0][6].'</td>';
                            $html .= '<td>'.$dar[0][7].'</td>';
                        } else {
                            $html .= '<td></td>';
                            $html .= '<td></td>';
                            $html .= '<td></td>';
                        }
                        $html .= '</tr>';
                    }
                    ###REINTEGROS
                    for ($n = 0; $n < count($dt1n); $n++) {
                        $da = detafectado($dt1n[$s][8]);
                        $a  = 0;
                        $dae="";
                        $das="";
                        $dat="";
                        $dar="";
                        while ($a==0){
                            switch ($da[0][8]) {
                                case 2:
                                    $dae = $da;
                                break;
                                case 3:
                                    $das = $da;
                                break;
                                case 5:
                                    $dat = $da;
                                break;
                                case 6:
                                    $dar = $da;
                                break;
                            }
                            if(!empty($da[0][9])){
                               $da = detafectado($da[0][9]); 
                            } else {
                                $a = 1;
                            }
                        }
                        #Datos Producto 
                        $da = $dt1n[$n][8];
                        $dp = datosp($dt1n[$n][9]);
                        $html .= '<tr>';
                        $html .= '<td colspan="2">'.$dt1n[$n][1].'</td>';
                        $html .= '<td colspan="2">'.$dt1n[$n][2].'</td>';
                        $html .= '<td>'.number_format($dt1n[$n][3],2,'.',',').'</td>';
                        $html .= '<td>'.$dt1n[$n][10].'</td>';
                        $html .= '<td colspan="2">';
                        $html .= $dp[0][2].'<br/>';
                        for ($p = 0; $p < count($dp); $p++) {
                            $html.=$dp[$p][0].': '.$dp[$p][1].'<br/>';
                        }
                        $html .= '</td>';
                        if(!empty($dae[0][0])){
                            $html .= '<td>'.$dae[0][4].' - '.ucwords($dae[0][5]).'</td>';
                            $html .= '<td>'.$dae[0][6].'</td>';
                            $html .= '<td>'.$dae[0][7].'</td>';
                        } else {
                            $html .= '<td></td>';
                            $html .= '<td></td>';
                            $html .= '<td></td>';
                        }
                        if(!empty($das[0][0])){
                            $html .= '<td>'.$das[0][4].' - '.ucwords(das[0][5]).'</td>';
                            $html .= '<td>'.$das[0][6].'</td>';
                            $html .= '<td>'.$das[0][7].'</td>';
                        } else {
                            $html .= '<td></td>';
                            $html .= '<td></td>';
                            $html .= '<td></td>';
                        }
                        if(!empty($dat[0][0])){
                            $html .= '<td>'.$dat[0][4].' - '.ucwords($dat[0][5]).'</td>';
                            $html .= '<td>'.$dat[0][6].'</td>';
                            $html .= '<td>'.$dat[0][7].'</td>';
                        } else {
                            $html .= '<td></td>';
                            $html .= '<td></td>';
                            $html .= '<td></td>';
                        }
                        $html .= '<td>'.$dt1n[$n][4].' - '.ucwords($dt1n[$n][5]).'</td>';
                        $html .= '<td>'.$dt1n[$n][6].'</td>';
                        $html .= '<td>'.$dt1n[$n][7].'</td>';                
                        $html .= '</tr>';
                    }
                    $dpi = $rowd[$i][1];
                    $trf = $rowter[$t][2];
                }
            }
        }
        echo $html;
}
  
function detaf($clase, $tipoI, $dependencia, $responsable){
    global $con;
    global $compania;
    global $fecha;
    
    $row = $con->Listar("SELECT DISTINCT dm.id_unico, pi.codi, pi.nombre, 
    dm.valor, tm.sigla, LOWER(tm.nombre), m.numero, DATE_FORMAT(m.fecha, '%d/%m/%Y'), 
    dm.detalleasociado 
    FROM gf_detalle_movimiento dm 
    LEFT JOIN gf_movimiento m ON dm.movimiento = m.id_unico 
    LEFT JOIN gf_tipo_movimiento tm ON m.tipomovimiento = tm.id_unico 
    LEFT JOIN gf_plan_inventario pi ON dm.planmovimiento = pi.id_unico 
    WHERE m.compania = $compania AND tm.clase = $clase AND pi.tipoinventario = $tipoI
    AND dm.id_unico NOT IN (SELECT dma.detalleasociado FROM gf_detalle_movimiento dma 
            LEFT JOIN gf_movimiento ma ON dma.movimiento = ma.id_unico WHERE ma.compania = $compania 
            AND dm.planmovimiento = pi.id_unico AND dma.detalleasociado IS NOT NULL) 
    AND m.dependencia = $dependencia and m.fecha <='$fecha' AND m.tercero = $responsable");
    
    return $row;
}

function detafectado($id_afectado){
    global $con;
    $row = $con->Listar("SELECT DISTINCT dm.id_unico,  pi.codi, pi.nombre, 
        dm.valor, tm.sigla, LOWER(tm.nombre), m.numero, DATE_FORMAT(m.fecha, '%d/%m/%Y'), 
        tm.clase , dm.detalleasociado 
        FROM gf_detalle_movimiento dm 
        LEFT JOIN gf_movimiento m ON dm.movimiento = m.id_unico 
        LEFT JOIN gf_tipo_movimiento tm ON m.tipomovimiento = tm.id_unico 
        LEFT JOIN gf_plan_inventario pi ON dm.planmovimiento = pi.id_unico 
        WHERE dm.id_unico  = ".$id_afectado);
    
    return $row;
}

function detalles($clase, $tipoI, $dependencia,$responsable){
    global $con;
    global $compania;
    global $fecha;
    
    $row = $con ->Listar("SELECT DISTINCT dm.id_unico, pi.codi, pi.nombre, 
    dm.valor, tm.sigla, LOWER(tm.nombre), m.numero, DATE_FORMAT(m.fecha, '%d/%m/%Y'), 
    dm.detalleasociado , mp.producto, pe.valor 
    FROM gf_movimiento_producto mp 
    LEFT JOIN gf_detalle_movimiento dm ON dm.id_unico = mp.detallemovimiento
    LEFT JOIN gf_movimiento m ON dm.movimiento = m.id_unico 
    LEFT JOIN gf_tipo_movimiento tm ON m.tipomovimiento = tm.id_unico 
    LEFT JOIN gf_plan_inventario pi ON dm.planmovimiento = pi.id_unico 
    LEFT JOIN gf_producto p ON mp.producto = p.id_unico 
    LEFT JOIN gf_producto_especificacion pe ON p.id_unico = pe.producto AND pe.fichainventario = 6 
    WHERE m.compania = $compania AND tm.clase =$clase AND pi.tipoinventario = $tipoI
    AND m.dependencia = $dependencia and m.fecha <='$fecha' AND m.tercero = $responsable 
    AND mp.detallemovimiento = (SELECT MAX(mpa.detallemovimiento) FROM gf_movimiento_producto mpa WHERE mpa.producto = mp.producto)");
    return $row;
}
function datosp($id_producto){
    global $con;
    $row = $con->Listar("SELECT ef.nombre, pe.valor, p.descripcion 
	FROM gf_producto p 
    LEFT JOIN gf_producto_especificacion pe ON pe.producto = p.id_unico  
    LEFT JOIN gf_ficha_inventario fi ON pe.fichainventario = fi.id_unico 
    LEFT JOIN gf_elemento_ficha ef ON fi.elementoficha = ef.id_unico
    WHERE pe.producto = $id_producto AND pe.fichainventario !=6 AND pe.valor !=''");
    
    return $row;
}
?>

    