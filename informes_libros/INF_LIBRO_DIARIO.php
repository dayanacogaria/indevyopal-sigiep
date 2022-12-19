<?php
#######################################################################################################
#           *********       Modificaciones      *********       #
#######################################################################################################
#21/12/2017 |Erica G.| No tome en cuenta el comprobante cierre- Parametrizacion Año
#29/09/2017 |Erica G. | ARCHIVO CREADO
#######################################################################################################

require_once('../Conexion/conexion.php');
require_once('../jsonPptal/funcionesPptal.php');
session_start();
ob_start();
ini_set('max_execution_time', 0); 
$calendario = CAL_GREGORIAN;
#*************RECEPCION VARIABLES*****************#
$anno       = $_POST['anno'];
$mes        = $_POST['mes'];
$informe    = $_POST['informe'];
$foliador   = $_POST['foliador'];
$tipoI      = $_POST['TipoComprobanteI'];
$tipoF      = $_POST['TipoComprobanteF'];
$compania   = $_SESSION['compania'];
$annionom = anno($anno);
#************Fecha Inicial y Final***************#
$fechaI = '01/'.$mes.'/'.$annionom;
$diaF = cal_days_in_month($calendario, $mes, $annionom); 
$fechaF = $diaF.'/'.$mes.'/'.$annionom;
#***********************************************#
if($informe==1){
    $orientacion ='L';
    $tam = 220;
}elseif($informe==2){
    $orientacion ='P';
    $tam = 150;
}

#*************CONSULTA DATOS COMPAÑIA*************#
$compa=$_SESSION['compania'];
$comp="SELECT
  t.razonsocial,
  IF(t.digitoverficacion IS NULL OR t.digitoverficacion='',
  t.numeroidentificacion, 
  CONCAT(t.numeroidentificacion, ' - ', t.digitoverficacion)),
  t.ruta_logo,
  d.direccion,
  tel.valor
FROM
  gf_tercero t
LEFT JOIN
  gf_direccion d ON d.tercero = t.id_unico
LEFT JOIN
  gf_telefono tel ON tel.tercero = t.id_unico
WHERE
  t.id_unico =$compa";
$comp = $mysqli->query($comp);
$comp = mysqli_fetch_row($comp);
$nombreCompania = $comp[0];
$nitcompania = $comp[1];
$ruta = $comp[2];
$direccion = $comp[3];
$telefono = $comp[4];
$usuario = $_SESSION['usuario'];
$meses = array( "01" => 'Enero', "02" => 'Febrero', "03" => 'Marzo',"04" => 'Abril', "05" => 'Mayo', "06" => 'Junio', 
                "07" => 'Julio', "08" => 'Agosto', "09" => 'Septiembre', "10" => 'Octubre', "11" => 'Noviembre', "12" => 'Diciembre');

$mesnom = mb_strtoupper($meses[$mes]);

if($_GET['tipo']=='pdf') {
    #CREACION PDF, HEAD AND FOOTER
    header("Content-Type: text/html;charset=utf-8");
    require'../fpdf/fpdf.php';
    class PDF extends FPDF
    {
        function Header()
        { 
            global $foliador;
            global $nombreCompania;
            global $nitcompania;
            global $numpaginas;
            $numpaginas = $this->PageNo();
            global $tam;
            global $ruta;
            global $mesnom;
            global $annionom;
            if($foliador=='Si'){

                if ($ruta != '') {
                    $this->Image('../'.$ruta,10,8,20);
                }

                $this->SetFont('Arial', 'B', 10);
                $this->SetX(35);
                $this->MultiCell($tam, 5, utf8_decode($nombreCompania), 0, 'C');
                $this->SetX(35);
                $this->Cell($tam, 5, $nitcompania, 0, 0, 'C');
                $this->Ln(4);
                $this->SetX(35);
                $this->Cell($tam, 5, utf8_decode('LIBRO DIARIO OFICIAL'), 0, 0, 'C');
                $this->Ln(4);
                $this->SetX(35);
                $this->Cell($tam, 5, utf8_decode($mesnom.' DE '.$annionom), 0, 0, 'C');
                $this->Ln(10);
            } else {
                $this->SetFont('Arial', 'B', 10);
                $this->Ln(15);
                $this->SetX(35);
                $this->Cell($tam, 5, utf8_decode($mesnom.' DE '.$annionom), 0, 0, 'C');
                $this->Ln(10);
            }

        }
        function Footer()
        { 


        }

    }

    $pdf = new PDF($orientacion,'mm','Letter');   
    $pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->SetFont('Arial','B',10);
    ########**************INFORME DETALLADO*******************#########
    $totalMesDebito  =0;
    $totalMesCredito =0;
    if($informe==1){
        $totalMesDebito  =0;
        $totalMesCredito =0; 
       #**********For para los días**********#
       for ($i = 1; $i <= $diaF; $i++) {
            $fechaC = $annionom.'-'.$mes.'-'.$i;
            $dias = array('','Lunes','Martes','Miercoles','Jueves','Viernes','Sabado','Domingo');
            $nomdia = mb_strtoupper($dias[date('N', strtotime($fechaC))]);

            #*******Consulta comprobantes contables utilizados entre fechas y entre tipos de comprobante*****#
            $sqltc="SELECT DISTINCT tc.id_unico, UPPER(tc.sigla), LOWER(tc.nombre) 
                FROM gf_comprobante_cnt cn 
              LEFT JOIN gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico 
              WHERE cn.fecha =  '$fechaC' AND cn.parametrizacionanno = $anno 
              AND tc.id_unico BETWEEN '$tipoI' AND '$tipoF' 
              ORDER BY tc.id_unico ASC" ;
            $sqltc = $mysqli->query($sqltc);

            if(mysqli_num_rows($sqltc)>0) {
            #Mostrar La Fecha
            $alto = $pdf->GetY();
            if($alto>150){
                $pdf->AddPage();
            }
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(255, 5, utf8_decode('FECHA: '.$nomdia.','.$i.' DE '.$mesnom.' DE '.$annionom), 0, 0, 'L');
            $pdf->Ln(10);
            $totalFechadebito   = 0;
            $totalFechacredito  = 0;
            while ($row = mysqli_fetch_row($sqltc)) {
                #Mostrar Tipo Comprobante
                $alto = $pdf->GetY();
                if($alto>150){
                    $pdf->AddPage();
                }
                $pdf->SetFont('Arial','B',10);
                $pdf->Cell(255, 5, utf8_decode('TIPO COMPROBANTE : '.$row[1].' '.ucwords($row[2])), 1, 0, 'L');
                $pdf->Ln(10);

                #*******Consultar los comprobantes del tipo y fecha*****#
                $sqlcom = "SELECT id_unico, numero, descripcion 
                    FROM gf_comprobante_cnt 
                    WHERE fecha = '$fechaC' AND tipocomprobante = $row[0] AND parametrizacionanno = $anno ";
                $sqlcom = $mysqli->query($sqlcom);
                $totalTipoDebito =0;
                $totalTipoCredito =0;
                while ($rowcom = mysqli_fetch_row($sqlcom)) {

                    $alto = $pdf->GetY();
                    if($alto>150){
                        $pdf->AddPage();
                    }
                    #**********Imprimir Datos Comprobante*********###
                    $xcom = $pdf->GetX();
                    $ycom = $pdf->GetY();
                    $pdf->SetFont('Arial','B',10);
                    $pdf->Cell(35, 5, utf8_decode('Comprobante N°: '), 0, 0, 'L');
                    $pdf->SetFont('Arial','I',10);
                    $pdf->Cell(25, 5, utf8_decode($rowcom[1]), 0, 0, 'R');
                    $pdf->SetFont('Arial','',10);
                    $pdf->MultiCell(195, 5, utf8_decode($rowcom[2]),0, 'L');
                    $y2com = $pdf->GetY();
                    $h = $y2com-$ycom;
                    $pdf->SetXY($xcom, $ycom);
                    $pdf->Cell(35,$h, utf8_decode(''), 1, 0, 'L');
                    $pdf->Cell(25,$h, utf8_decode(''), 1, 0, 'L');
                    $pdf->Cell(195,$h, utf8_decode(''), 1, 0, 'L');
                    $pdf->Ln($h);
                    $pdf->Ln(3);

                    #**********Buscar Datos Comprobante*********###
                    $sqldetcom = "SELECT d.id_unico, c.id_unico, c.codi_cuenta, LOWER(c.nombre), 
                                LOWER(cc.nombre),  IF(CONCAT_WS(' ',
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
                                t.apellidodos)) AS NOMBRE, c.naturaleza, 
                                if(c.naturaleza=1 && d.valor>0, d.valor, if(c.naturaleza=2 && d.valor<0, d.valor*-1, 0)) as debito, 
                                if(c.naturaleza=2 && d.valor>0, d.valor, if(c.naturaleza=1 && d.valor<0, d.valor*-1, 0)) as credito , 
                                d.valor 
                                FROM gf_detalle_comprobante d 
                                LEFT JOIN gf_cuenta c ON d.cuenta = c.id_unico 
                                LEFT JOIN gf_tercero t ON d.tercero = t.id_unico 
                                LEFT JOIN gf_centro_costo cc ON d.centrocosto = cc.id_unico 
                                WHERE d.comprobante =$rowcom[0] ";
                    $sqldetcom = $mysqli->query($sqldetcom);
                    if(mysqli_num_rows($sqldetcom)>0){
                        $alto = $pdf->GetY();
                        if($alto>180){
                            $pdf->AddPage();
                        }
                        $pdf->SetFont('Arial','B',10);
                        $pdf->Cell(30, 5, utf8_decode('CÓDIGO'), 1, 0, 'C');
                        $pdf->Cell(65, 5, utf8_decode('NOMBRE'), 1, 0, 'C');
                        $pdf->Cell(32, 5, utf8_decode('CENTRO COSTO'), 1, 0, 'C');
                        $pdf->Cell(64, 5, utf8_decode('TERCERO'), 1, 0, 'C');
                        $pdf->Cell(32, 5, utf8_decode('VALOR DÉBITO'),1, 0, 'C');
                        $pdf->Cell(32, 5, utf8_decode('VALOR CRÉDITO'), 1, 0, 'C');
                        $pdf->Ln(5);
                        $totalcndebito =0;
                        $totalcncredito =0;
                        while ($rowdet = mysqli_fetch_row($sqldetcom)) {
                            $alto = $pdf->GetY();
                            if($alto > 180){
                                $pdf->AddPage();
                            }
                            $pdf->SetFont('Arial','',10);
                            #**********Imprimir Datos Detalle*********###
                            $xcom = $pdf->GetX();
                            $ycom = $pdf->GetY();
                            $pdf->Cell(30, 5, utf8_decode($rowdet[2]), 0, 0, 'L');
                            $pdf->MultiCell(65, 5, utf8_decode(ucwords($rowdet[3])), 0, 'L');
                            $y2com = $pdf->GetY();
                            $h1 = $y2com-$ycom;
                            $pdf->SetXY($xcom+95, $ycom);
                            $pdf->MultiCell(32, 5, utf8_decode(ucwords($rowdet[4])),0, 'L');
                            $y3com = $pdf->GetY();
                            $h3 = $y3com-$ycom;
                            $pdf->SetXY($xcom+127, $ycom);
                            $pdf->MultiCell(64, 5, utf8_decode(ucwords(mb_strtolower($rowdet[5]))),0, 'L');
                            $y4com = $pdf->GetY();
                            $h4 = $y4com-$ycom;
                            $pdf->SetXY($xcom+191, $ycom);
                            $pdf->Cell(32, 5, number_format($rowdet[7], 2, '.', ','), 0, 0, 'R');
                            $pdf->Cell(32, 5, number_format($rowdet[8], 2, '.', ','), 0, 0, 'R');
                            $y5com = $pdf->GetY();
                            $h5 = $y5com-$ycom;

                            $h = max($h1, $h2, $h3, $h4, $h5);
                            $pdf->SetXY($xcom, $ycom);
                            $pdf->Cell(30,$h, utf8_decode(''), 1, 0, 'L');
                            $pdf->Cell(65,$h, utf8_decode(''), 1, 0, 'L');
                            $pdf->Cell(32,$h, utf8_decode(''), 1, 0, 'L');
                            $pdf->Cell(64,$h, utf8_decode(''), 1, 0, 'L');
                            $pdf->Cell(32,$h, utf8_decode(''), 1, 0, 'L');
                            $pdf->Cell(32,$h, utf8_decode(''), 1, 0, 'L');
                            $pdf->Ln($h);
                            $totalcndebito  +=$rowdet[7];
                            $totalcncredito +=$rowdet[8];
                        }
                        $pdf->Ln(3);
                        $pdf->Cell(181,0.5, utf8_decode(''), 0, 0, 'L');
                        $pdf->Cell(74,0.5, utf8_decode(''), 1, 0, 'L');
                        $pdf->Ln(3);
                        $pdf->SetFont('Arial','B',10);
                        $pdf->Cell(100,5, utf8_decode('Subtotal Comprobante'), 0, 0, 'R');
                        $pdf->Cell(20,5, utf8_decode(''), 0, 0, 'R');
                        $x = $pdf->GetX();
                        $y = $pdf->GetY();
                        $pdf->MultiCell(71,5, utf8_decode($rowcom[1]), 0, 'L');
                        $pdf->SetXY($x+71, $y);
                        $pdf->Cell(32,5, number_format($totalcndebito, 2, '.', ','), 0, 0, 'R');
                        $pdf->Cell(32,5, number_format($totalcncredito, 2, '.', ','), 0, 0, 'R');
                    }
                    $pdf->Ln(10);
                    $totalTipoDebito  += $totalcndebito;
                    $totalTipoCredito += $totalcncredito;

                }
                $totalFechadebito   += $totalTipoDebito;
                $totalFechacredito  += $totalTipoCredito;
                #**********Subtotal Comprobante************#
                $pdf->Cell(181,0.5, utf8_decode(''), 0, 0, 'L');
                $pdf->Cell(74,0.5, utf8_decode(''), 1, 0, 'L');
                $pdf->Ln(3);
                $pdf->SetFont('Arial','B',10);
                $pdf->Cell(100,5, utf8_decode('Subtotal Tipo Comprobante'), 0, 0, 'R');
                $pdf->Cell(20,5, utf8_decode(''), 0, 0, 'R');
                $x = $pdf->GetX();
                $y = $pdf->GetY();
                $pdf->MultiCell(71,5, utf8_decode($row[1].' '.ucwords($row[2])), 0, 'L');
                $pdf->SetXY($x+71, $y);
                $pdf->Cell(32,5, number_format($totalTipoDebito, 2, '.', ','), 0, 0, 'R');
                $pdf->Cell(32,5, number_format($totalTipoCredito, 2, '.', ','), 0, 0, 'R');
                $pdf->Ln(10);
            }
            $totalMesDebito   += $totalFechadebito;
            $totalMesCredito  += $totalFechacredito;

            #**********Subtotal Fecha************#
            $pdf->Cell(181,0.5, utf8_decode(''), 0, 0, 'L');
            $pdf->Cell(74,0.5, utf8_decode(''), 1, 0, 'L');
            $pdf->Ln(3);
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(100,5, utf8_decode('Subtotal Fecha'), 0, 0, 'R');
            $pdf->Cell(20,5, utf8_decode(''), 0, 0, 'R');
            $pdf->Cell(71,5, utf8_decode($nomdia.','.$i.' DE '.$mesnom.' DE '.$annionom), 0, 0, 'L');
            $pdf->Cell(32,5, number_format($totalFechadebito, 2, '.', ','), 0, 0, 'R');
            $pdf->Cell(32,5, number_format($totalFechacredito, 2, '.', ','), 0, 0, 'R');
            $pdf->Ln(10);
            }        


       }
        #**********Total Mes************#
        $pdf->Cell(181,0.5, utf8_decode(''), 0, 0, 'L');
        $pdf->Cell(74,0.5, utf8_decode(''), 1, 0, 'L');
        $pdf->Ln(3);
        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(100,5, utf8_decode('Total Mes de'), 0, 0, 'R');
        $pdf->Cell(20,5, utf8_decode(''), 0, 0, 'R');
        $pdf->Cell(71,5, utf8_decode($mesnom), 0, 0, 'L');
        $pdf->Cell(32,5, number_format($totalMesDebito, 2, '.', ','), 0, 0, 'R');
        $pdf->Cell(32,5, number_format($totalMesCredito, 2, '.', ','), 0, 0, 'R');
        $pdf->Ln(10);
    ########**************FIN INFORME DETALLADO*******************#########
    ########**************INFORME CONSOLIDADO*******************#########    
    }elseif($informe==2){
        for ($i = 1; $i <= $diaF; $i++) {
            $fechaC = $annionom.'-'.$mes.'-'.$i;
            $dias = array('','Lunes','Martes','Miercoles','Jueves','Viernes','Sabado','Domingo');
            $nomdia = mb_strtoupper($dias[date('N', strtotime($fechaC))]);

            #*******Consulta cuentas por fecha*********#
            $sqltc="SELECT DISTINCT c.id_unico 
            FROM gf_detalle_comprobante dc 
            LEFT JOIN gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico 
            LEFT JOIN gf_cuenta c ON c.id_unico = dc.cuenta
            WHERE cn.fecha =  '$fechaC'  AND cn.parametrizacionanno = $anno 
            AND cn.tipocomprobante BETWEEN '$tipoI' AND '$tipoF' 
            ORDER BY c.codi_cuenta ASC" ;
            $sqltc = $mysqli->query($sqltc);

            if(mysqli_num_rows($sqltc)>0) {
            #Mostrar La Fecha
            $alto = $pdf->GetY();
            if($alto>230){
                $pdf->AddPage();
            }
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(185, 5, utf8_decode('FECHA: '.$nomdia.','.$i.' DE '.$mesnom.' DE '.$annionom), 0, 0, 'L');
            $pdf->Ln(10);
            $totalFechadebito   = 0;
            $totalFechacredito  = 0;
            $pdf->Cell(35, 5, utf8_decode('CÓDIGO'), 1, 0, 'C');
            $pdf->Cell(80, 5, utf8_decode('NOMBRE'), 1, 0, 'C');
            $pdf->Cell(35, 5, utf8_decode('DÉBITO'), 1, 0, 'C');
            $pdf->Cell(35, 5, utf8_decode('CRÉDITO'),1, 0, 'C');
            $pdf->Ln(5);
            while ($row = mysqli_fetch_row($sqltc)) {
                $pdf->SetFont('Arial','',10);
                $alto = $pdf->GetY();
                if($alto>230){
                    $pdf->AddPage();
                }
                #*******Consultar detalles y cuentas*****#
                $sqlcom = "SELECT c.id_unico, c.codi_cuenta, c.nombre, c.naturaleza, 
                        SUM(if(c.naturaleza=1 && d.valor>0, d.valor, if(c.naturaleza=2 && d.valor<0, d.valor*-1, 0))) as debito, 
                        SUM(if(c.naturaleza=2 && d.valor>0, d.valor, if(c.naturaleza=1 && d.valor<0, d.valor*-1, 0))) as credito 
                        FROM gf_detalle_comprobante d 
                        LEFT JOIN gf_comprobante_cnt cn ON d.comprobante = cn.id_unico 
                        LEFT JOIN gf_cuenta c ON d.cuenta = c.id_unico 
                        WHERE  cn.fecha = '$fechaC' AND c.id_unico = $row[0]  
                        AND cn.parametrizacionanno = $anno 
                        AND cn.tipocomprobante BETWEEN '$tipoI' AND '$tipoF' ";
                $sqlcom = $mysqli->query($sqlcom);
                $rowcom = mysqli_fetch_row($sqlcom);
                $xcom = $pdf->GetX();
                $ycom = $pdf->GetY();
                $pdf->Cell(35, 5, utf8_decode($rowcom[1]), 0, 0, 'L');
                $pdf->MultiCell(80, 5, utf8_decode(ucwords($rowcom[2])), 0, 'L');
                $y2com = $pdf->GetY();
                $h = $y2com-$ycom;
                $pdf->SetXY($xcom+115, $ycom);
                $pdf->Cell(35, 5, number_format($rowcom[4], 2, '.', ','), 0, 0, 'R');
                $pdf->Cell(35, 5, number_format($rowcom[5], 2, '.', ','), 0, 0, 'R');

                $pdf->SetXY($xcom, $ycom);
                $pdf->Cell(35,$h, utf8_decode(''), 1, 0, 'L');
                $pdf->Cell(80,$h, utf8_decode(''), 1, 0, 'L');
                $pdf->Cell(35,$h, utf8_decode(''), 1, 0, 'L');
                $pdf->Cell(35,$h, utf8_decode(''), 1, 0, 'L');
                $pdf->Ln($h);
                $totalFechadebito  +=$rowcom[4];
                $totalFechacredito +=$rowcom[5];

            }
            $totalMesDebito   += $totalFechadebito;
            $totalMesCredito  += $totalFechacredito;
            $pdf->Ln(3);
            #**********Subtotal Fecha************#
            $pdf->Cell(115,0.5, utf8_decode(''), 0, 0, 'L');
            $pdf->Cell(70,0.5, utf8_decode(''), 1, 0, 'L');
            $pdf->Ln(3);
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(30,5, utf8_decode('Subtotal Fecha'), 0, 0, 'R');
            $pdf->Cell(15,5, utf8_decode(''), 0, 0, 'R');
            $pdf->Cell(70,5, utf8_decode($nomdia.','.$i.' DE '.$mesnom.' DE '.$annionom), 0, 0, 'L');
            $pdf->Cell(35,5, number_format($totalFechadebito, 2, '.', ','), 0, 0, 'R');
            $pdf->Cell(35,5, number_format($totalFechacredito, 2, '.', ','), 0, 0, 'R');
            $pdf->Ln(10);
            }        


       }
       #**********Total Mes************#
        $pdf->Cell(115,0.5, utf8_decode(''), 0, 0, 'L');
        $pdf->Cell(70,0.5, utf8_decode(''), 1, 0, 'L');
        $pdf->Ln(3);
        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(30,5, utf8_decode('Total Mes de'), 0, 0, 'R');
        $pdf->Cell(15,5, utf8_decode(''), 0, 0, 'R');
        $pdf->Cell(70,5, utf8_decode($mesnom), 0, 0, 'L');
        $pdf->Cell(35,5, number_format($totalMesDebito, 2, '.', ','), 0, 0, 'R');
        $pdf->Cell(35,5, number_format($totalMesCredito, 2, '.', ','), 0, 0, 'R');
        $pdf->Ln(10);


    }
    ################################ ESTRUCTURA FIRMAS ##########################################
    ######### BUSQUEDA RESPONSABLE #########
     $pdf->SetFont('Arial','B',9);
     $pdf->Ln(30);

     $res = "SELECT rd.tercero, tr.nombre , tres.nombre FROM gf_responsable_documento rd 
            LEFT JOIN gf_tipo_documento td ON rd.tipodocumento = td.id_unico
            LEFT JOIN gg_tipo_relacion tr ON rd.tipo_relacion = tr.id_unico 
            LEFT JOIN gf_tipo_responsable tres ON rd.tiporesponsable = tres.id_unico 
            WHERE LOWER(td.nombre) ='libro diario' AND td.compania = $compania ORDER BY rd.orden ASC";
     $res= $mysqli->query($res);
     $i=0;
     $x=130;
     #ESTRUCTURA
     if(mysqli_num_rows($res)>0){
         $h=4;
         while ($row2 = mysqli_fetch_row($res)) {
             $ter = "SELECT IF(CONCAT_WS(' ',
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
                        tr.apellidodos)) AS NOMBREC, "
                     . "tr.numeroidentificacion, c.nombre, tr.tarjeta_profesional "
                     . "FROM gf_tercero tr "
                     . "LEFT JOIN gf_cargo_tercero ct ON tr.id_unico = ct.tercero "
                     . "LEFT JOIN gf_cargo c ON ct.cargo = c.id_unico "
                     . "WHERE tr.id_unico ='$row2[0]'";

             $ter = $mysqli->query($ter);
             $ter = mysqli_fetch_row($ter);
             if(!empty($ter[3])){
                     $responsable ="\n\n___________________________________ \n". (mb_strtoupper($ter[0]))."\n".mb_strtoupper($ter[2])."\n T.P:".(mb_strtoupper($ter[3]));
             } else {
                 $responsable ="\n\n___________________________________ \n". (mb_strtoupper($ter[0]))."\n".mb_strtoupper($ter[2])."\n";
             }

             $pdf->MultiCell(110,4, utf8_decode($responsable),0,'L');

             if($i==1){
               $pdf->Ln(15);
               $x=130;
               $i=0;
             } else {
             $pdf->Ln(-20);
             $pdf->SetX($x);
             $x=$x+110;
              $i=$i+1;
             }

         }

     } 
     ##################################################################################

    while (ob_get_length()) {
      ob_end_clean();
    }
    $pdf->Output(0,'Informe_Libro_Diario ('.date('d/m/Y').').pdf',0);
    
    
}elseif($_GET['tipo']=='excel') {
    header("Content-type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=Informe_Libro_Diario.xls");
    echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
    echo '<html xmlns="http://www.w3.org/1999/xhtml">'; 
    echo '<head>';
    echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
    echo '<title>Informe Libro Diario</title>';
    echo '</head>';
    echo '<body>';
    echo '<table width="100%" border="1" cellspacing="0" cellpadding="0">';
    if($informe==1){
    echo '<th colspan="6" align="center"><strong>';
    } else {
    echo '<th colspan="4" align="center"><strong>';
    }
    echo '<br/>&nbsp;';
    echo '<br/>'.$nombreCompania;
    echo '<br/>'.$nitcompania."<br/>".$direccion.' Tel:'.$telefono;
    echo '<br/>&nbsp;';
    echo '<br/>Informe Libro Diario<br/>&nbsp;</strong></th>';
    ########**************INFORME DETALLADO*******************#########
    $totalMesDebito  =0;
    $totalMesCredito =0;
    if($informe==1){
        $totalMesDebito  =0;
        $totalMesCredito =0; 
       #**********For para los días**********#
       for ($i = 1; $i <= $diaF; $i++) {
            $fechaC = $annionom.'-'.$mes.'-'.$i;
            $dias = array('','Lunes','Martes','Miercoles','Jueves','Viernes','Sabado','Domingo');
            $nomdia = mb_strtoupper($dias[date('N', strtotime($fechaC))]);

            #*******Consulta comprobantes contables utilizados entre fechas y entre tipos de comprobante*****#
            $sqltc="SELECT DISTINCT tc.id_unico, UPPER(tc.sigla), LOWER(tc.nombre) 
                FROM gf_comprobante_cnt cn 
              LEFT JOIN gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico 
              WHERE cn.fecha =  '$fechaC' AND cn.parametrizacionanno = $anno 
              AND tc.id_unico BETWEEN '$tipoI' AND '$tipoF' 
              ORDER BY tc.id_unico ASC" ;
            $sqltc = $mysqli->query($sqltc);

            if(mysqli_num_rows($sqltc)>0) {
            #Mostrar La Fecha
            echo '<tr>';
            echo '<td colspan="6"><br/><strong>FECHA: '.$nomdia.','.$i.' DE '.$mesnom.' DE '.$annionom.'</strong><br/>&nbsp;</td>';
            echo '</tr>';
            $totalFechadebito   = 0;
            $totalFechacredito  = 0;
            while ($row = mysqli_fetch_row($sqltc)) {
                #Mostrar Tipo Comprobante
                echo '<tr>';
                echo '<td colspan="6"><br/><strong><i>TIPO COMPROBANTE : '.$row[1].' '.ucwords($row[2]).'</i></strong><br/>&nbsp;</td>';
                echo '</tr>';

                #*******Consultar los comprobantes del tipo y fecha*****#
                $sqlcom = "SELECT id_unico, numero, descripcion 
                    FROM gf_comprobante_cnt 
                    WHERE fecha = '$fechaC' AND tipocomprobante = $row[0] AND parametrizacionanno = $anno ";
                $sqlcom = $mysqli->query($sqlcom);
                $totalTipoDebito =0;
                $totalTipoCredito =0;
                while ($rowcom = mysqli_fetch_row($sqlcom)) {

                    #**********Imprimir Datos Comprobante*********###
                    echo '<tr>';
                    echo '<td colspan="6"><strong><i>Comprobante N°:  '.$rowcom[1].' - '.$rowcom[2].'</i></strong></td>';
                    echo '</tr>';
                    

                    #**********Buscar Datos Comprobante*********###
                    $sqldetcom = "SELECT d.id_unico, c.id_unico, c.codi_cuenta, LOWER(c.nombre), 
                                LOWER(cc.nombre),  IF(CONCAT_WS(' ',
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
                                t.apellidodos)) AS NOMBRE, c.naturaleza, 
                                if(c.naturaleza=1 && d.valor>0, d.valor, if(c.naturaleza=2 && d.valor<0, d.valor*-1, 0)) as debito, 
                                if(c.naturaleza=2 && d.valor>0, d.valor, if(c.naturaleza=1 && d.valor<0, d.valor*-1, 0)) as credito , 
                                d.valor 
                                FROM gf_detalle_comprobante d 
                                LEFT JOIN gf_cuenta c ON d.cuenta = c.id_unico 
                                LEFT JOIN gf_tercero t ON d.tercero = t.id_unico 
                                LEFT JOIN gf_centro_costo cc ON d.centrocosto = cc.id_unico 
                                WHERE d.comprobante =$rowcom[0] ";
                    $sqldetcom = $mysqli->query($sqldetcom);
                    if(mysqli_num_rows($sqldetcom)>0){
                        echo '<tr>';
                        echo '<td><strong>CÓDIGO</strong></td>';
                        echo '<td><strong>NOMBRE</strong></td>';
                        echo '<td><strong>CENTRO COSTO</strong></td>';
                        echo '<td><strong>TERCERO</strong></td>';
                        echo '<td><strong>VALOR DÉBITO</strong></td>';
                        echo '<td><strong>VALOR CRÉDITO</strong></td>';
                        echo '</tr>';
                        $totalcndebito =0;
                        $totalcncredito =0;
                        while ($rowdet = mysqli_fetch_row($sqldetcom)) {
                            
                            #**********Imprimir Datos Detalle*********###
                            echo '<tr>';
                            echo '<td>'.$rowdet[2].'</td>';
                            echo '<td>'.ucwords($rowdet[3]).'</td>';
                            echo '<td>'.ucwords($rowdet[4]).'</td>';
                            echo '<td>'.ucwords(mb_strtolower($rowdet[5])).'</td>';
                            echo '<td>'.number_format($rowdet[7], 2, '.', ',').'</td>';
                            echo '<td>'.number_format($rowdet[8], 2, '.', ',').'</td>';
                            echo '</tr>';
                            $totalcndebito  +=$rowdet[7];
                            $totalcncredito +=$rowdet[8];
                        }
                        echo '<tr>';
                        echo '<td colspan="4"><br/><strong>Subtotal Comprobante '.$rowcom[1].'</strong><br/>&nbsp;</td>';
                        echo '<td><strong>'.number_format($totalcndebito, 2, '.', ',').'</strong></td>';
                        echo '<td><strong>'.number_format($totalcncredito, 2, '.', ',').'</strong></td>';
                        echo '</tr>';
                    }
                    $totalTipoDebito  += $totalcndebito;
                    $totalTipoCredito += $totalcncredito;

                }
                $totalFechadebito   += $totalTipoDebito;
                $totalFechacredito  += $totalTipoCredito;
                #**********Subtotal Comprobante************#
                echo '<tr>';
                echo '<td colspan="4"><br/><strong>Subtotal Tipo Comprobante '.$row[1].' '.ucwords($row[2]).'</strong><br/>&nbsp;</td>';
                echo '<td><strong>'.number_format($totalTipoDebito, 2, '.', ',').'</strong></td>';
                echo '<td><strong>'.number_format($totalTipoCredito, 2, '.', ',').'</strong></td>';
                echo '</tr>';
                
            }
            $totalMesDebito   += $totalFechadebito;
            $totalMesCredito  += $totalFechacredito;

            #**********Subtotal Fecha************#
            echo '<tr>';
            echo '<td colspan="4"><br/><strong>Subtotal Fecha '.$nomdia.','.$i.' DE '.$mesnom.' DE '.$annionom.'</strong><br/>&nbsp;</td>';
            echo '<td><strong>'.number_format($totalFechadebito, 2, '.', ',').'</strong></td>';
            echo '<td><strong>'.number_format($totalFechacredito, 2, '.', ',').'</strong></td>';
            echo '</tr>';
            }        


       }
        #**********Total Mes************#
        echo '<tr>';
        echo '<td colspan="4"><br/><strong><i>Total Mes de '.$mesnom.'</i></strong><br/>&nbsp;</td>';
        echo '<td><strong><i>'.number_format($totalMesDebito, 2, '.', ',').'</i></strong></td>';
        echo '<td><strong><i>'.number_format($totalMesCredito, 2, '.', ',').'</i></strong></td>';
        echo '</tr>';
    ########**************FIN INFORME DETALLADO*******************#########
    ########**************INFORME CONSOLIDADO*******************#########    
    }elseif($informe==2){
        for ($i = 1; $i <= $diaF; $i++) {
            $fechaC = $annionom.'-'.$mes.'-'.$i;
            $dias = array('','Lunes','Martes','Miercoles','Jueves','Viernes','Sabado','Domingo');
            $nomdia = mb_strtoupper($dias[date('N', strtotime($fechaC))]);

            #*******Consulta cuentas por fecha*********#
            $sqltc="SELECT DISTINCT c.id_unico 
            FROM gf_detalle_comprobante dc 
            LEFT JOIN gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico 
            LEFT JOIN gf_cuenta c ON c.id_unico = dc.cuenta
            WHERE cn.fecha =  '$fechaC'  AND cn.parametrizacionanno = $anno 
            AND cn.tipocomprobante BETWEEN '$tipoI' AND '$tipoF' 
            ORDER BY c.codi_cuenta ASC" ;
            $sqltc = $mysqli->query($sqltc);

            if(mysqli_num_rows($sqltc)>0) {
            echo '<tr>';
            echo '<td colspan="4"><br/><strong>FECHA '.$nomdia.','.$i.' DE '.$mesnom.' DE '.$annionom.'</strong><br/>&nbsp;</td>';
            echo '</tr>';
            $totalFechadebito   = 0;
            $totalFechacredito  = 0;
            echo '<tr>';
            echo '<td><strong>CÓDIGO</strong></td>';
            echo '<td><strong>NOMBRE</strong></td>';
            echo '<td><strong>VALOR DÉBITO</strong></td>';
            echo '<td><strong>VALOR CRÉDITO</strong></td>';
            echo '</tr>';
            while ($row = mysqli_fetch_row($sqltc)) {
                
                #*******Consultar detalles y cuentas*****#
                $sqlcom = "SELECT c.id_unico, c.codi_cuenta, c.nombre, c.naturaleza, 
                        SUM(if(c.naturaleza=1 && d.valor>0, d.valor, if(c.naturaleza=2 && d.valor<0, d.valor*-1, 0))) as debito, 
                        SUM(if(c.naturaleza=2 && d.valor>0, d.valor, if(c.naturaleza=1 && d.valor<0, d.valor*-1, 0))) as credito 
                        FROM gf_detalle_comprobante d 
                        LEFT JOIN gf_comprobante_cnt cn ON d.comprobante = cn.id_unico 
                        LEFT JOIN gf_cuenta c ON d.cuenta = c.id_unico 
                        WHERE  cn.fecha = '$fechaC' AND c.id_unico = $row[0]  
                        AND cn.parametrizacionanno = $anno 
                        AND cn.tipocomprobante BETWEEN '$tipoI' AND '$tipoF' ";
                $sqlcom = $mysqli->query($sqlcom);
                $rowcom = mysqli_fetch_row($sqlcom);
                echo '<tr>';
                echo '<td>'.$rowcom[1].'</td>';
                echo '<td>'.ucwords($rowcom[2]).'</td>';
                echo '<td>'.number_format($rowcom[4], 2, '.', ',').'</td>';
                echo '<td>'.number_format($rowcom[5], 2, '.', ',').'</td>';
                echo '</tr>';
                $totalFechadebito  +=$rowcom[4];
                $totalFechacredito +=$rowcom[5];

            }
            $totalMesDebito   += $totalFechadebito;
            $totalMesCredito  += $totalFechacredito;
            #**********Subtotal Fecha************#
            echo '<tr>';
            echo '<td colspan="2"><br/><strong>Subtotal Fecha '.$nomdia.','.$i.' DE '.$mesnom.' DE '.$annionom.'</strong><br/>&nbsp;</td>';
            echo '<td><strong>'.number_format($totalFechadebito, 2, '.', ',').'</strong></td>';
            echo '<td><strong>'.number_format($totalFechacredito, 2, '.', ',').'</strong></td>';
            echo '</tr>';
            }        


        }
        #**********Total Mes************#
        echo '<tr>';
        echo '<td colspan="2"><br/><strong>Total Mes de '.$mesnom.'</strong><br/>&nbsp;</td>';
        echo '<td><strong>'.number_format($totalMesDebito, 2, '.', ',').'</strong></td>';
        echo '<td><strong>'.number_format($totalMesCredito, 2, '.', ',').'</strong></td>';
        echo '</tr>';


    }
    
    echo '</table>';
    echo '</body>';
    echo '</html>';
 }