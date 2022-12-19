<?php
require_once("../Conexion/ConexionPDO.php");
require_once("../Conexion/conexion.php");
require_once("../jsonPptal/funcionesPptal.php");
require_once('../Conexion/conexionsql.php');
require '../code128.php';
header("Content-Type: text/html;charset=utf-8");

ini_set('max_execution_time', 0);
session_start();
$con        = new ConexionPDO();
$compania   = $_SESSION['compania'];
$parmanno   = $_SESSION['anno'];
$anno       = anno($parmanno);

$mesini     = $_REQUEST['mesI'];
$mesfin     = $_REQUEST['mesF'];
$anioini    = $_REQUEST['anioI'];
$aniofin    = $_REQUEST['anioF'];
$tipocredito = $_REQUEST['tipoCredito'];
$tipoinforme = $_REQUEST['tipoInforme'];

#   ************   Datos Compañia   ************    #

$rowC = $con->Listar("SELECT  ter.id_unico,
                ter.razonsocial,
                UPPER(ti.nombre),
                ter.numeroidentificacion,
                dir.direccion,
                tel.valor,
                ter.ruta_logo
FROM gf_tercero ter
LEFT JOIN   gf_tipo_identificacion ti ON ter.tipoidentificacion = ti.id_unico
LEFT JOIN   gf_direccion dir ON dir.tercero = ter.id_unico
LEFT JOIN   gf_telefono  tel ON tel.tercero = ter.id_unico
WHERE ter.id_unico = $compania");
$razonsocial = $rowC[0][1];
$nombreIdent = $rowC[0][2];
$numeroIdent = $rowC[0][3];
$direccinTer = $rowC[0][4];
$telefonoTer = $rowC[0][5];
$ruta_logo  = $rowC[0][6];

$ac = 260;
ob_start();
class PDF extends FPDF {

    function Header() {
        
    }

    function Footer() {
        
    }

}
$pdf = new PDF_Code128('L', 'mm', 'letter');
$nb = $pdf->AliasNbPages();
$pdf->AddPage();
$pdf->AliasNbPages();
$pdf->SetFont('Arial', 'B', 10);
if ($ruta_logo != '') {
    $pdf->Image('../' . $ruta_logo, 18, 15, 40);
}
$pdf->SetX(10);
$pdf->Cell(57, 30, '', 1, 0, 'C');
$pdf->SetX(67);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell($ac - 53, 20, utf8_decode($razonsocial), 1, 0, 'C');
$pdf->Ln(20);
$pdf->SetX(67);

if ($tipoinforme == 1) {

    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell($ac - 53, 10, utf8_decode('PROYECCIÓN MENSUAL POR TIPO CRÉDITO'), 1, 0, 'C');

    if ($tipocredito != "") {
        $pdf->Ln(15);
        $sqltc = "SELECT Identificador, Nombre_Tipo_Credito as Nombre from TIPO_CREDITO 
                              WHERE Identificador = $tipocredito
                              ORDER BY TIPO_CREDITO.Nombre_Tipo_Credito ASC";

        $stmt = sqlsrv_query($conn, $sqltc);
        $rowtc = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
        $credito = $rowtc['Nombre'];


        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(264, 6, utf8_decode('TIPO CRÉDITO: ' . $credito), 'LTR', 0, 'C');
        $pdf->Ln(6);
        $pdf->SetFont('Arial', 'B', 7);
        $pdf->Cell(16, 4, utf8_decode('MES'), 'LTR', 0, 'C');
        $pdf->Cell(13, 4, utf8_decode('AÑO'), 'LTR', 0, 'C');
        $pdf->Cell(21, 4, utf8_decode('CAPITAL'), 'LTR', 0, 'C');
        $pdf->Cell(21, 4, utf8_decode('INTERÉS'), 'LTR', 0, 'C');
        $pdf->Cell(21, 4, utf8_decode('INTERÉS'), 'LTR', 0, 'C');
        $pdf->Cell(21, 4, utf8_decode('RECARGO'), 'LTR', 0, 'C');
        $pdf->Cell(21, 4, utf8_decode('SEGURO'), 'LTR', 0, 'C');
        $pdf->Cell(21, 4, utf8_decode('HONORARIOS'), 'LTR', 0, 'C');
        $pdf->Cell(21, 4, utf8_decode('OTROS'), 'LTR', 0, 'C');
        $pdf->Cell(21, 4, utf8_decode('GASTOS'), 'LTR', 0, 'C');
        $pdf->Cell(21, 4, utf8_decode('COSTOS'), 'LTR', 0, 'C');
        $pdf->Cell(21, 4, utf8_decode('SALDOS'), 'LTR', 0, 'C');
        $pdf->Cell(25, 4, utf8_decode('TOTAL'), 'LTR', 0, 'C');
        $pdf->Ln(3);
        $pdf->Cell(16, 4, utf8_decode(''), 'LBR', 0, 'C');
        $pdf->Cell(13, 4, utf8_decode(''), 'LBR', 0, 'C');
        $pdf->Cell(21, 4, utf8_decode(''), 'LBR', 0, 'C');
        $pdf->Cell(21, 4, utf8_decode(''), 'LBR', 0, 'C');
        $pdf->Cell(21, 4, utf8_decode('ACUERDO'), 'LBR', 0, 'C');
        $pdf->Cell(21, 4, utf8_decode(''), 'LBR', 0, 'C');
        $pdf->Cell(21, 4, utf8_decode(''), 'LBR', 0, 'C');
        $pdf->Cell(21, 4, utf8_decode(''), 'LBR', 0, 'C');
        $pdf->Cell(21, 4, utf8_decode('CONCEPTOS'), 'LBR', 0, 'C');
        $pdf->Cell(21, 4, utf8_decode('ADMINISTRAT.'), 'LBR', 0, 'C');
        $pdf->Cell(21, 4, utf8_decode('JURÍDICOS'), 'LBR', 0, 'C');
        $pdf->Cell(21, 4, utf8_decode('A FAVOR'), 'LBR', 0, 'C');
        $pdf->Cell(25, 4, utf8_decode('MES'), 'LBR', 0, 'C');
        $pdf->Ln(4);


        //*****************************************************************
        $ts_capital     = 0;
        $ts_interes     = 0;
        $ts_interesa    = 0;
        $ts_recargos    = 0;
        $ts_seguros     = 0;
        $ts_honorarios  = 0;
        $ts_otros       = 0;
        $ts_gastos      = 0;
        $ts_costos      = 0;
        $ts_saldofavor  = 0;
        $ts_total       = 0;
        $sqla = "SELECT DISTINCT year(d.Fecha_Posible_pago) as  yr
                         FROM DETALLE_CREDITO as d 
                         WHERE year(d.Fecha_Posible_pago) BETWEEN $anioini AND $aniofin
                         order by year(d.Fecha_Posible_pago) desc";
        $stmt = sqlsrv_query($conn, $sqla);
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            for ($x = $mesini; $x <= $mesfin; $x++) {
                $sql = " declare @mes int = '" . $x . "'; 
                declare @year int = '" . $row['yr'] . "';
                declare @tipocredito int = '" . $tipocredito . "';
                select  c.Id_Tipo_Credito,  
                /**saldo capital**/
                (select sum(dp.Valor_Concepto) from DETALLE_CREDITO as dp     
                  LEFT JOIN CREDITO cd ON dp.Numero_Credito = cd.Numero_Credito     
                  where cd.Id_Tipo_Credito =@tipocredito  and dp.Id_Concepto='2'   
                  and (MONTH (dp.Fecha_Posible_pago))=@mes  and (year (dp.Fecha_Posible_pago))=@year    
                  and dp.Valor_Concepto>0) as Pago_Capital,   

                /**saldo interes **/
                 (select sum(dp.Valor_Concepto) from DETALLE_CREDITO as dp    
                 LEFT JOIN CREDITO cd ON dp.Numero_Credito = cd.Numero_Credito     
                 where cd.Id_Tipo_Credito =@tipocredito and dp.Id_Concepto='3'   
                 and (MONTH (dp.Fecha_Posible_pago))=@mes  and (year (dp.Fecha_Posible_pago))=@year    
                 and dp.Valor_Concepto>0) as Pago_Interes,    

                /**interes aceuerto  **/
                (select sum(dp.Valor_Concepto) from DETALLE_CREDITO as dp     
                LEFT JOIN CREDITO cd ON dp.Numero_Credito = cd.Numero_Credito     
                where cd.Id_Tipo_Credito =@tipocredito and dp.Id_Concepto='21'   
                and (MONTH (dp.Fecha_Posible_pago))=@mes  and (year (dp.Fecha_Posible_pago))=@year    
                and dp.Valor_Concepto>0) as Pago_Interes_Acuerdo,      

                /*recarcgo*/
                (select sum(dp.Valor_Concepto) from DETALLE_CREDITO as dp     
                LEFT JOIN CREDITO cd ON dp.Numero_Credito = cd.Numero_Credito     
                where cd.Id_Tipo_Credito =@tipocredito and dp.Id_Concepto='4'   
                and (MONTH (dp.Fecha_Posible_pago))=@mes  and (year (dp.Fecha_Posible_pago))=@year    
                and dp.Valor_Concepto>0) as Pago_Recargos,    

                /*seguro*/
                (select sum(dp.Valor_Concepto) from DETALLE_CREDITO as dp     
                LEFT JOIN CREDITO cd ON dp.Numero_Credito = cd.Numero_Credito     
                where cd.Id_Tipo_Credito =@tipocredito    
                and (MONTH (dp.Fecha_Posible_pago))=@mes  and (year (dp.Fecha_Posible_pago))=@year    
                and dp.Valor_Concepto>0 and dp.Id_Concepto in('5', '22')) as Pago_Seguro,   

                /*honorarios*/
                (select sum(dp.Valor_Concepto) from DETALLE_CREDITO as dp     
                LEFT JOIN CREDITO cd ON dp.Numero_Credito = cd.Numero_Credito     
                where cd.Id_Tipo_Credito =@tipocredito and dp.Id_Concepto='6'   
                and (MONTH (dp.Fecha_Posible_pago))=@mes  and (year (dp.Fecha_Posible_pago))=@year    
                and dp.Valor_Concepto>0) as Pago_Honorarios,    

                /*otros*/
                (select sum(dp.Valor_Concepto) from DETALLE_CREDITO as dp     
                LEFT JOIN CREDITO cd ON dp.Numero_Credito = cd.Numero_Credito     
                where cd.Id_Tipo_Credito =@tipocredito  and dp.Id_Concepto IN('7', '8')    
                and (MONTH (dp.Fecha_Posible_pago))=@mes  and (year (dp.Fecha_Posible_pago))=@year    
                and dp.Valor_Concepto>0 ) as Pago_Otros,    

                /*gastos*/
                (select sum(dp.Valor_Concepto) from DETALLE_CREDITO as dp     
                LEFT JOIN CREDITO cd ON dp.Numero_Credito = cd.Numero_Credito     
                where cd.Id_Tipo_Credito =@tipocredito  and dp.Id_Concepto='18'   
                and (MONTH (dp.Fecha_Posible_pago))=@mes  and (year (dp.Fecha_Posible_pago))=@year    
                and dp.Valor_Concepto>0 ) as Pago_Gastos,     

                /*costos*/
                (select sum(dp.Valor_Concepto) from DETALLE_CREDITO as dp     
                LEFT JOIN CREDITO cd ON dp.Numero_Credito = cd.Numero_Credito     
                where cd.Id_Tipo_Credito =@tipocredito  and dp.Id_Concepto='19'   
                and (MONTH (dp.Fecha_Posible_pago))=@mes  and (year (dp.Fecha_Posible_pago))=@year    
                and dp.Valor_Concepto>0 ) as Pago_Costos,     
                
                /*saldos a favor*/
                (select sum(dp.Valor_Concepto) from DETALLE_CREDITO as dp     
                LEFT JOIN CREDITO cd ON dp.Numero_Credito = cd.Numero_Credito     
                where cd.Id_Tipo_Credito =@tipocredito AND  dp.Id_Concepto='20'   
                and (MONTH (dp.Fecha_Posible_pago))=@mes  and (year (dp.Fecha_Posible_pago))=@year    
                and dp.Valor_Concepto>0 ) as Pago_Saldos_Favor  

                from CREDITO as c    

                where c.Id_Tipo_Credito = @tipocredito 
                /**saldo capital**/
                and (select sum(dp.Valor_Concepto) from DETALLE_CREDITO as dp 
                LEFT JOIN CREDITO cd ON dp.Numero_Credito = cd.Numero_Credito     
                where cd.Id_Tipo_Credito =  @tipocredito AND  dp.Id_Concepto='2'   
                and (MONTH (dp.Fecha_Posible_pago))=@mes  and (year (dp.Fecha_Posible_pago))=@year    
                and dp.Valor_Concepto>0) is not null  or   

                /**saldo interes **/
                 (select sum(dp.Valor_Concepto) from DETALLE_CREDITO as dp    
                 LEFT JOIN CREDITO cd ON dp.Numero_Credito = cd.Numero_Credito     
                 where cd.Id_Tipo_Credito =  @tipocredito AND dp.Id_Concepto='3'   
                 and (MONTH (dp.Fecha_Posible_pago))=@mes  and (year (dp.Fecha_Posible_pago))=@year    
                 and dp.Valor_Concepto>0) is not null  or   

                /**interes aceuerto  **/
                (select sum(dp.Valor_Concepto) from DETALLE_CREDITO as dp     
                LEFT JOIN CREDITO cd ON dp.Numero_Credito = cd.Numero_Credito     
                where cd.Id_Tipo_Credito =  @tipocredito AND  dp.Id_Concepto='21'   
                and (MONTH (dp.Fecha_Posible_pago))=@mes  and (year (dp.Fecha_Posible_pago))=@year    
                and dp.Valor_Concepto>0) is not null  or      

                /*recarcgo*/
                (select sum(dp.Valor_Concepto) from DETALLE_CREDITO as dp     
                LEFT JOIN CREDITO cd ON dp.Numero_Credito = cd.Numero_Credito     
                where cd.Id_Tipo_Credito =  @tipocredito AND  dp.Id_Concepto='4'   
                and (MONTH (dp.Fecha_Posible_pago))=@mes  and (year (dp.Fecha_Posible_pago))=@year    
                and dp.Valor_Concepto>0) is not null  or   

                /*seguro*/
                (select sum(dp.Valor_Concepto) from DETALLE_CREDITO as dp     
                LEFT JOIN CREDITO cd ON dp.Numero_Credito = cd.Numero_Credito     
                where cd.Id_Tipo_Credito =  @tipocredito AND  dp.Id_Concepto in('5', '22')
                and (MONTH (dp.Fecha_Posible_pago))=@mes  and (year (dp.Fecha_Posible_pago))=@year    
                and dp.Valor_Concepto>0 ) is not null  or  

                /*honorarios*/
                (select sum(dp.Valor_Concepto) from DETALLE_CREDITO as dp     
                LEFT JOIN CREDITO cd ON dp.Numero_Credito = cd.Numero_Credito     
                where cd.Id_Tipo_Credito =  @tipocredito AND  dp.Id_Concepto='6'   
                and (MONTH (dp.Fecha_Posible_pago))=@mes  and (year (dp.Fecha_Posible_pago))=@year    
                and dp.Valor_Concepto>0) is not null  or   

                /*otros*/
                (select sum(dp.Valor_Concepto) from DETALLE_CREDITO as dp     
                LEFT JOIN CREDITO cd ON dp.Numero_Credito = cd.Numero_Credito     
                where cd.Id_Tipo_Credito =  @tipocredito AND  dp.Id_Concepto IN('7', '8') 
                and (MONTH (dp.Fecha_Posible_pago))=@mes  and (year (dp.Fecha_Posible_pago))=@year    
                and dp.Valor_Concepto>0 ) is not null  or   

                /*gastos*/
                (select sum(dp.Valor_Concepto) from DETALLE_CREDITO as dp     
                LEFT JOIN CREDITO cd ON dp.Numero_Credito = cd.Numero_Credito     
                where cd.Id_Tipo_Credito =  @tipocredito AND dp.Id_Concepto='18'   
                and (MONTH (dp.Fecha_Posible_pago))=@mes  and (year (dp.Fecha_Posible_pago))=@year    
                and dp.Valor_Concepto>0 ) is not null  or   

                /*costos*/
                (select sum(dp.Valor_Concepto) from DETALLE_CREDITO as dp     
                LEFT JOIN CREDITO cd ON dp.Numero_Credito = cd.Numero_Credito     
                where cd.Id_Tipo_Credito =  @tipocredito AND dp.Id_Concepto='19'   
                and (MONTH (dp.Fecha_Posible_pago))=@mes  and (year (dp.Fecha_Posible_pago))=@year    
                and dp.Valor_Concepto>0 ) is not null  or    

                /*saldos a favor*/

                (select sum(dp.Valor_Concepto) from DETALLE_CREDITO as dp     
                LEFT JOIN CREDITO cd ON dp.Numero_Credito = cd.Numero_Credito     
                where cd.Id_Tipo_Credito =  @tipocredito AND dp.Id_Concepto='20'   
                and (MONTH (dp.Fecha_Posible_pago))=@mes  and (year (dp.Fecha_Posible_pago))=@year    
                and dp.Valor_Concepto>0 ) is not null    
                GROUP BY c.Id_Tipo_Credito
                order by c.Id_Tipo_Credito asc ";


                $stmtpm = sqlsrv_query($conn, $sql);
                $rows = sqlsrv_fetch_array($stmtpm, SQLSRV_FETCH_ASSOC);

                $mes        = $x;
                $anio       = $row['yr'];
                $capital    = $rows["Pago_Capital"];
                $interes    = $rows["Pago_Interes"];
                $interesa   = $rows["Pago_Interes_Acuerdo"];
                $recargos   = $rows["Pago_Recargos"];
                $seguros    = $rows["Pago_Seguro"];
                $honorarios = $rows["Pago_Honorarios"];
                $otros      = $rows["Pago_Otros"];
                $gastos     = $rows["Pago_Gastos"];
                $costos     = $rows["Pago_Costos"];
                $saldofavor = $rows["Pago_Saldos_Favor"];
                $total      = ($capital + $interes + $interesa + $recargos + $seguros + $honorarios + $otros + $gastos + $costos + $saldofavor);

                $ts_capital     += $rows["Pago_Capital"];
                $ts_interes     += $rows["Pago_Interes"];
                $ts_interesa    += $rows["Pago_Interes_Acuerdo"];
                $ts_recargos    += $rows["Pago_Recargos"];
                $ts_seguros     += $rows["Pago_Seguro"];
                $ts_honorarios  += $rows["Pago_Honorarios"];
                $ts_otros       += $rows["Pago_Otros"];
                $ts_gastos      += $rows["Pago_Gastos"];
                $ts_costos      += $rows["Pago_Costos"];
                $ts_saldofavor  += $rows["Pago_Saldos_Favor"];
                $ts_total       += $total;


                if ($mes == 1) {
                $mes = "Enero";
                }
                if ($mes == 2) {
                $mes = "Febrero";
                }
                if ($mes == 3) {
                $mes = "Marzo";
                }
                if ($mes == 4) {
                $mes = "Abril";
                }
                if ($mes == 5) {
                $mes = "Mayo";
                }
                if ($mes == 6) {
                $mes = "Junio";
                }
                if ($mes == 7) {
                $mes = "Julio";
                }
                if ($mes == 8) {
                $mes = "Agosto";
                }
                if ($mes == 9) {
                $mes = "Septiembre";
                }
                if ($mes == 10) {
                $mes = "Octubre";
                }
                if ($mes == 11) {
                $mes = "Noviembre";
                }
                if ($mes == 12) {
                $mes = "Diciembre";
                }

                $pdf->SetFont('Arial', '', 7);
                $pdf->Cell(16, 8, utf8_decode($mes), 1, 0, 'C');
                $pdf->Cell(13, 8, $anio, 1, 0, 'C');
                $pdf->Cell(21, 8, number_format($capital, 2, '.', ','), 1, 0, 'R');
                $pdf->Cell(21, 8, number_format($interes, 2, '.', ','), 1, 0, 'R');
                $pdf->Cell(21, 8, number_format($interesa, 2, '.', ','), 1, 0, 'R');
                $pdf->Cell(21, 8, number_format($recargos, 2, '.', ','), 1, 0, 'R');
                $pdf->Cell(21, 8, number_format($seguros, 2, '.', ','), 1, 0, 'R');
                $pdf->Cell(21, 8, number_format($honorarios, 2, '.', ','), 1, 0, 'R');
                $pdf->Cell(21, 8, number_format($otros, 2, '.', ','), 1, 0, 'R');
                $pdf->Cell(21, 8, number_format($gastos, 2, '.', ','), 1, 0, 'R');
                $pdf->Cell(21, 8, number_format($costos, 2, '.', ','), 1, 0, 'R');
                $pdf->Cell(21, 8, number_format($saldofavor, 2, '.', ','), 1, 0, 'R');
                $pdf->SetFont('Arial', 'B', 7);
                $pdf->Cell(25, 8, number_format($total, 2, '.', ','), 1, 0, 'R');
                $pdf->Ln(8);
            } // end for
        } //end when

        $pdf->SetFont('Arial', 'B', 7);
        $pdf->Cell(29, 8, utf8_decode('TOTALES'), 1, 0, 'L');
        $pdf->Cell(21, 8, number_format($ts_capital, 2, '.', ','), 1, 0, 'R');
        $pdf->Cell(21, 8, number_format($ts_interes, 2, '.', ','), 1, 0, 'R');
        $pdf->Cell(21, 8, number_format($ts_interesa, 2, '.', ','), 1, 0, 'R');
        $pdf->Cell(21, 8, number_format($ts_recargos, 2, '.', ','), 1, 0, 'R');
        $pdf->Cell(21, 8, number_format($ts_seguros, 2, '.', ','), 1, 0, 'R');
        $pdf->Cell(21, 8, number_format($ts_honorarios, 2, '.', ','), 1, 0, 'R');
        $pdf->Cell(21, 8, number_format($ts_otros, 2, '.', ','), 1, 0, 'R');
        $pdf->Cell(21, 8, number_format($ts_gastos, 2, '.', ','), 1, 0, 'R');
        $pdf->Cell(21, 8, number_format($ts_costos, 2, '.', ','), 1, 0, 'R');
        $pdf->Cell(21, 8, number_format($ts_saldofavor, 2, '.', ','), 1, 0, 'R');
        $pdf->Cell(25, 8, number_format($ts_total, 2, '.', ','), 1, 0, 'R');
        $pdf->Ln(10);
    } else { // sino se escoge tipo crédito  muetra todos los créditos
        $pdf->Ln(7);

        $sqltc = "SELECT DISTINCT tc.Identificador, tc.Nombre_Tipo_credito 
        FROM DETALLE_CREDITO DC
		LEFT JOIN CREDITO C ON DC.Numero_Credito = C.Numero_Credito 
		LEFT JOIN TIPO_CREDITO tc ON tc.Identificador = C.Id_Tipo_Credito 
        WHERE year(DC.Fecha_Posible_pago) BETWEEN '$anioini' AND '$aniofin'
        ORDER BY tc.Nombre_Tipo_credito";
        $stmt = sqlsrv_query($conn, $sqltc);
        while ($rows = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $pdf->Ln(8);

            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell(264, 6, utf8_decode('TIPO CRÉDITO: ' . $rows['Nombre_Tipo_credito']), 'LTR', 0, 'C');
            $pdf->Ln(6);
            $pdf->SetFont('Arial', 'B', 7);
            $pdf->Cell(16, 4, utf8_decode('MES'), 'LTR', 0, 'C');
            $pdf->Cell(13, 4, utf8_decode('AÑO'), 'LTR', 0, 'C');
            $pdf->Cell(21, 4, utf8_decode('CAPITAL'), 'LTR', 0, 'C');
            $pdf->Cell(21, 4, utf8_decode('INTERÉS'), 'LTR', 0, 'C');
            $pdf->Cell(21, 4, utf8_decode('INTERÉS'), 'LTR', 0, 'C');
            $pdf->Cell(21, 4, utf8_decode('RECARGO'), 'LTR', 0, 'C');
            $pdf->Cell(21, 4, utf8_decode('SEGURO'), 'LTR', 0, 'C');
            $pdf->Cell(21, 4, utf8_decode('HONORARIOS'), 'LTR', 0, 'C');
            $pdf->Cell(21, 4, utf8_decode('OTROS'), 'LTR', 0, 'C');
            $pdf->Cell(21, 4, utf8_decode('GASTOS'), 'LTR', 0, 'C');
            $pdf->Cell(21, 4, utf8_decode('COSTOS'), 'LTR', 0, 'C');
            $pdf->Cell(21, 4, utf8_decode('SALDOS'), 'LTR', 0, 'C');
            $pdf->Cell(25, 4, utf8_decode('TOTAL'), 'LTR', 0, 'C');
            $pdf->Ln(3);
            $pdf->Cell(16, 4, utf8_decode(''), 'LBR', 0, 'C');
            $pdf->Cell(13, 4, utf8_decode(''), 'LBR', 0, 'C');
            $pdf->Cell(21, 4, utf8_decode(''), 'LBR', 0, 'C');
            $pdf->Cell(21, 4, utf8_decode(''), 'LBR', 0, 'C');
            $pdf->Cell(21, 4, utf8_decode('ACUERDO'), 'LBR', 0, 'C');
            $pdf->Cell(21, 4, utf8_decode(''), 'LBR', 0, 'C');
            $pdf->Cell(21, 4, utf8_decode(''), 'LBR', 0, 'C');
            $pdf->Cell(21, 4, utf8_decode(''), 'LBR', 0, 'C');
            $pdf->Cell(21, 4, utf8_decode('CONCEPTOS'), 'LBR', 0, 'C');
            $pdf->Cell(21, 4, utf8_decode('ADMINISTRAT.'), 'LBR', 0, 'C');
            $pdf->Cell(21, 4, utf8_decode('JURÍDICOS'), 'LBR', 0, 'C');
            $pdf->Cell(21, 4, utf8_decode('A FAVOR'), 'LBR', 0, 'C');
            $pdf->Cell(25, 4, utf8_decode('MES'), 'LBR', 0, 'C');
            $pdf->Ln(4);

            $identificador = $rows['Identificador'];

            $sqla = "SELECT DISTINCT year(d.Fecha_Posible_pago) as  yr
            FROM DETALLE_CREDITO as d 
            WHERE year(d.Fecha_Posible_pago) BETWEEN $anioini AND $aniofin
            order by year(d.Fecha_Posible_pago) desc";
            $stmta = sqlsrv_query($conn, $sqla);
            $ts_capital     = 0;
            $ts_interes     = 0;
            $ts_interesa    = 0;
            $ts_recargos    = 0;
            $ts_seguros     = 0;
            $ts_honorarios  = 0;
            $ts_otros       = 0;
            $ts_gastos      = 0;
            $ts_costos      = 0;
            $ts_saldofavor  = 0;
            $ts_total       = 0;
            while ($row = sqlsrv_fetch_array($stmta, SQLSRV_FETCH_ASSOC)) {
                for ($x = $mesini; $x <= $mesfin; $x++) {                   
                    
                    $sql = " declare @mes int = '" . $x . "'; 
                    declare @year int = '" . $row['yr'] . "';
                    declare @tipocredito int = '" . $identificador . "';
                    select  c.Id_Tipo_Credito,   
                        /**saldo capital**/
                   (select sum(dp.Valor_Concepto) from DETALLE_CREDITO as dp     
                     LEFT JOIN CREDITO cd ON dp.Numero_Credito = cd.Numero_Credito     
                     where cd.Id_Tipo_Credito =@tipocredito and dp.Id_Concepto='2'   
                     and (MONTH (dp.Fecha_Posible_pago))=@mes  and (year (dp.Fecha_Posible_pago))=@year    
                     and dp.Valor_Concepto>0) as Pago_Capital,  

                    /**saldo interes **/
                     (select sum(dp.Valor_Concepto) from DETALLE_CREDITO as dp    
                     LEFT JOIN CREDITO cd ON dp.Numero_Credito = cd.Numero_Credito     
                     where cd.Id_Tipo_Credito =@tipocredito AND  dp.Id_Concepto='3'   
                     and (MONTH (dp.Fecha_Posible_pago))=@mes  and (year (dp.Fecha_Posible_pago))=@year    
                     and dp.Valor_Concepto>0) as Pago_Interes,    

                    /**interes aceuerto  **/
                    (select sum(dp.Valor_Concepto) from DETALLE_CREDITO as dp     
                    LEFT JOIN CREDITO cd ON dp.Numero_Credito = cd.Numero_Credito     
                    where cd.Id_Tipo_Credito =@tipocredito AND  dp.Id_Concepto='21'   
                    and (MONTH (dp.Fecha_Posible_pago))=@mes  and (year (dp.Fecha_Posible_pago))=@year    
                    and dp.Valor_Concepto>0) as Pago_Interes_Acuerdo,      

                    /*recarcgo*/
                    (select sum(dp.Valor_Concepto) from DETALLE_CREDITO as dp     
                    LEFT JOIN CREDITO cd ON dp.Numero_Credito = cd.Numero_Credito     
                    where cd.Id_Tipo_Credito =@tipocredito AND  dp.Id_Concepto='4'   
                    and (MONTH (dp.Fecha_Posible_pago))=@mes  and (year (dp.Fecha_Posible_pago))=@year    
                    and dp.Valor_Concepto>0) as Pago_Recargos,    

                    /*seguro*/
                    (select sum(dp.Valor_Concepto) from DETALLE_CREDITO as dp     
                    LEFT JOIN CREDITO cd ON dp.Numero_Credito = cd.Numero_Credito     
                    where cd.Id_Tipo_Credito =@tipocredito AND 
                    (MONTH (dp.Fecha_Posible_pago))=@mes  and (year (dp.Fecha_Posible_pago))=@year    
                    and dp.Valor_Concepto>0 and dp.Id_Concepto in('5', '22')) as Pago_Seguro,   

                    /*honorarios*/
                    (select sum(dp.Valor_Concepto) from DETALLE_CREDITO as dp     
                    LEFT JOIN CREDITO cd ON dp.Numero_Credito = cd.Numero_Credito     
                    where cd.Id_Tipo_Credito =@tipocredito AND  dp.Id_Concepto='6'   
                    and (MONTH (dp.Fecha_Posible_pago))=@mes  and (year (dp.Fecha_Posible_pago))=@year    
                    and dp.Valor_Concepto>0) as Pago_Honorarios,    

                    /*otros*/
                    (select sum(dp.Valor_Concepto) from DETALLE_CREDITO as dp     
                    LEFT JOIN CREDITO cd ON dp.Numero_Credito = cd.Numero_Credito     
                    where cd.Id_Tipo_Credito =@tipocredito AND  dp.Id_Concepto IN('7', '8')    
                    and (MONTH (dp.Fecha_Posible_pago))=@mes  and (year (dp.Fecha_Posible_pago))=@year    
                    and dp.Valor_Concepto>0 ) as Pago_Otros,    

                    /*gastos*/
                    (select sum(dp.Valor_Concepto) from DETALLE_CREDITO as dp     
                    LEFT JOIN CREDITO cd ON dp.Numero_Credito = cd.Numero_Credito     
                    where cd.Id_Tipo_Credito =@tipocredito AND  dp.Id_Concepto='18'   
                    and (MONTH (dp.Fecha_Posible_pago))=@mes  and (year (dp.Fecha_Posible_pago))=@year    
                    and dp.Valor_Concepto>0 ) as Pago_Gastos,     

                    /*costos*/
                    (select sum(dp.Valor_Concepto) from DETALLE_CREDITO as dp     
                    LEFT JOIN CREDITO cd ON dp.Numero_Credito = cd.Numero_Credito     
                    where cd.Id_Tipo_Credito =@tipocredito AND  dp.Id_Concepto='19'   
                    and (MONTH (dp.Fecha_Posible_pago))=@mes  and (year (dp.Fecha_Posible_pago))=@year    
                    and dp.Valor_Concepto>0 ) as Pago_Costos,     

                    /*saldos a favor*/
                    (select sum(dp.Valor_Concepto) from DETALLE_CREDITO as dp     
                    LEFT JOIN CREDITO cd ON dp.Numero_Credito = cd.Numero_Credito     
                    where cd.Id_Tipo_Credito =@tipocredito AND  dp.Id_Concepto='20'   
                    and (MONTH (dp.Fecha_Posible_pago))=@mes  and (year (dp.Fecha_Posible_pago))=@year    
                    and dp.Valor_Concepto>0 ) as Pago_Saldos_Favor  
                    
                    from CREDITO as c     
                    where c.Id_Tipo_Credito = @tipocredito 
                    
                    /**saldo capital**/
                    and (select sum(dp.Valor_Concepto) from DETALLE_CREDITO as dp 
                    LEFT JOIN CREDITO cd ON dp.Numero_Credito = cd.Numero_Credito     
                    where cd.Id_Tipo_Credito =  @tipocredito AND  dp.Id_Concepto='2'   
                    and (MONTH (dp.Fecha_Posible_pago))=@mes  and (year (dp.Fecha_Posible_pago))=@year    
                    and dp.Valor_Concepto>0) is not null  or   

                    /**saldo interes **/
                    (select sum(dp.Valor_Concepto) from DETALLE_CREDITO as dp    
                    LEFT JOIN CREDITO cd ON dp.Numero_Credito = cd.Numero_Credito     
                    where cd.Id_Tipo_Credito =  @tipocredito AND  dp.Id_Concepto='3'   
                    and (MONTH (dp.Fecha_Posible_pago))=@mes  and (year (dp.Fecha_Posible_pago))=@year    
                    and dp.Valor_Concepto>0) is not null  or   

                    /**interes aceuerto  **/
                    (select sum(dp.Valor_Concepto) from DETALLE_CREDITO as dp     
                    LEFT JOIN CREDITO cd ON dp.Numero_Credito = cd.Numero_Credito     
                    where cd.Id_Tipo_Credito =  @tipocredito AND dp.Id_Concepto='21'   
                    and (MONTH (dp.Fecha_Posible_pago))=@mes  and (year (dp.Fecha_Posible_pago))=@year    
                    and dp.Valor_Concepto>0) is not null  or      

                    /*recarcgo*/
                    (select sum(dp.Valor_Concepto) from DETALLE_CREDITO as dp     
                    LEFT JOIN CREDITO cd ON dp.Numero_Credito = cd.Numero_Credito     
                    where cd.Id_Tipo_Credito =  @tipocredito AND dp.Id_Concepto='4'   
                    and (MONTH (dp.Fecha_Posible_pago))=@mes  and (year (dp.Fecha_Posible_pago))=@year    
                    and dp.Valor_Concepto>0) is not null  or   

                    /*seguro*/
                    (select sum(dp.Valor_Concepto) from DETALLE_CREDITO as dp     
                    LEFT JOIN CREDITO cd ON dp.Numero_Credito = cd.Numero_Credito     
                    where cd.Id_Tipo_Credito =  @tipocredito AND dp.Id_Concepto in('5', '22')
                    and (MONTH (dp.Fecha_Posible_pago))=@mes  and (year (dp.Fecha_Posible_pago))=@year    
                    and dp.Valor_Concepto>0 ) is not null  or  

                    /*honorarios*/
                    (select sum(dp.Valor_Concepto) from DETALLE_CREDITO as dp     
                    LEFT JOIN CREDITO cd ON dp.Numero_Credito = cd.Numero_Credito     
                    where cd.Id_Tipo_Credito =  @tipocredito AND dp.Id_Concepto='6'   
                    and (MONTH (dp.Fecha_Posible_pago))=@mes  and (year (dp.Fecha_Posible_pago))=@year    
                    and dp.Valor_Concepto>0) is not null  or   

                    /*otros*/
                    (select sum(dp.Valor_Concepto) from DETALLE_CREDITO as dp     
                    LEFT JOIN CREDITO cd ON dp.Numero_Credito = cd.Numero_Credito     
                    where cd.Id_Tipo_Credito =  @tipocredito AND  dp.Id_Concepto IN('7', '8') 
                    and (MONTH (dp.Fecha_Posible_pago))=@mes  and (year (dp.Fecha_Posible_pago))=@year    
                    and dp.Valor_Concepto>0 ) is not null  or   

                    /*gastos*/
                    (select sum(dp.Valor_Concepto) from DETALLE_CREDITO as dp     
                    LEFT JOIN CREDITO cd ON dp.Numero_Credito = cd.Numero_Credito     
                    where cd.Id_Tipo_Credito =  @tipocredito AND  dp.Id_Concepto='18'   
                    and (MONTH (dp.Fecha_Posible_pago))=@mes  and (year (dp.Fecha_Posible_pago))=@year    
                    and dp.Valor_Concepto>0 ) is not null  or   

                    /*costos*/
                    (select sum(dp.Valor_Concepto) from DETALLE_CREDITO as dp     
                    LEFT JOIN CREDITO cd ON dp.Numero_Credito = cd.Numero_Credito     
                    where cd.Id_Tipo_Credito =  @tipocredito AND  dp.Id_Concepto='19'   
                    and (MONTH (dp.Fecha_Posible_pago))=@mes  and (year (dp.Fecha_Posible_pago))=@year    
                    and dp.Valor_Concepto>0 ) is not null  or    

                    /*saldos a favor*/
                    (select sum(dp.Valor_Concepto) from DETALLE_CREDITO as dp     
                    LEFT JOIN CREDITO cd ON dp.Numero_Credito = cd.Numero_Credito     
                    where cd.Id_Tipo_Credito =  @tipocredito AND  dp.Id_Concepto='20'   
                    and (MONTH (dp.Fecha_Posible_pago))=@mes  and (year (dp.Fecha_Posible_pago))=@year    
                    and dp.Valor_Concepto>0 ) is not null  
                    GROUP BY c.Id_Tipo_Credito
                    order by c.Id_Tipo_Credito asc ";

                    $stmtpm = sqlsrv_query($conn, $sql);
                    $rows   = sqlsrv_fetch_array($stmtpm, SQLSRV_FETCH_ASSOC);

                    $mes        = $x;
                    $anio       = $row['yr'];
                    $capital    = $rows["Pago_Capital"];
                    $interes    = $rows["Pago_Interes"];
                    $interesa   = $rows["Pago_Interes_Acuerdo"];
                    $recargos   = $rows["Pago_Recargos"];
                    $seguros    = $rows["Pago_Seguro"];
                    $honorarios = $rows["Pago_Honorarios"];
                    $otros      = $rows["Pago_Otros"];
                    $gastos     = $rows["Pago_Gastos"];
                    $costos     = $rows["Pago_Costos"];
                    $saldofavor = $rows["Pago_Saldos_Favor"];
                    $total      = ($capital + $interes + $interesa + $recargos + $seguros + $honorarios + $otros + $gastos + $costos + $saldofavor);

                    $ts_capital     += $rows["Pago_Capital"];
                    $ts_interes     += $rows["Pago_Interes"];
                    $ts_interesa    += $rows["Pago_Interes_Acuerdo"];
                    $ts_recargos    += $rows["Pago_Recargos"];
                    $ts_seguros     += $rows["Pago_Seguro"];
                    $ts_honorarios  += $rows["Pago_Honorarios"];
                    $ts_otros       += $rows["Pago_Otros"];
                    $ts_gastos      += $rows["Pago_Gastos"];
                    $ts_costos      += $rows["Pago_Costos"];
                    $ts_saldofavor  += $rows["Pago_Saldos_Favor"];
                    $ts_total       += $total;


                    if ($mes == 1) {
                        $mes = "Enero";
                    }
                    if ($mes == 2) {
                        $mes = "Febrero";
                    }
                    if ($mes == 3) {
                        $mes = "Marzo";
                    }
                    if ($mes == 4) {
                        $mes = "Abril";
                    }
                    if ($mes == 5) {
                        $mes = "Mayo";
                    }
                    if ($mes == 6) {
                        $mes = "Junio";
                    }
                    if ($mes == 7) {
                        $mes = "Julio";
                    }
                    if ($mes == 8) {
                        $mes = "Agosto";
                    }
                    if ($mes == 9) {
                        $mes = "Septiembre";
                    }
                    if ($mes == 10) {
                        $mes = "Octubre";
                    }
                    if ($mes == 11) {
                        $mes = "Noviembre";
                    }
                    if ($mes == 12) {
                        $mes = "Diciembre";
                    }


                    $pdf->SetFont('Arial', '', 7);
                    $pdf->Cell(16, 8, utf8_decode($mes), 1, 0, 'C');
                    $pdf->Cell(13, 8, $anio, 1, 0, 'C');
                    $pdf->Cell(21, 8, number_format($capital, 2, '.', ','), 1, 0, 'R');
                    $pdf->Cell(21, 8, number_format($interes, 2, '.', ','), 1, 0, 'R');
                    $pdf->Cell(21, 8, number_format($interesa, 2, '.', ','), 1, 0, 'R');
                    $pdf->Cell(21, 8, number_format($recargos, 2, '.', ','), 1, 0, 'R');
                    $pdf->Cell(21, 8, number_format($seguros, 2, '.', ','), 1, 0, 'R');
                    $pdf->Cell(21, 8, number_format($honorarios, 2, '.', ','), 1, 0, 'R');
                    $pdf->Cell(21, 8, number_format($otros, 2, '.', ','), 1, 0, 'R');
                    $pdf->Cell(21, 8, number_format($gastos, 2, '.', ','), 1, 0, 'R');
                    $pdf->Cell(21, 8, number_format($costos, 2, '.', ','), 1, 0, 'R');
                    $pdf->Cell(21, 8, number_format($saldofavor, 2, '.', ','), 1, 0, 'R');
                    $pdf->SetFont('Arial', 'B', 7);
                    $pdf->Cell(25, 8, number_format($total, 2, '.', ','), 1, 0, 'R');
                    $pdf->Ln(8);
                }
            }

            $pdf->SetFont('Arial', 'B', 7);
            $pdf->Cell(29, 8, utf8_decode('TOTALES'), 1, 0, 'L');
            $pdf->Cell(21, 8, number_format($ts_capital, 2, '.', ','), 1, 0, 'R');
            $pdf->Cell(21, 8, number_format($ts_interes, 2, '.', ','), 1, 0, 'R');
            $pdf->Cell(21, 8, number_format($ts_interesa, 2, '.', ','), 1, 0, 'R');
            $pdf->Cell(21, 8, number_format($ts_recargos, 2, '.', ','), 1, 0, 'R');
            $pdf->Cell(21, 8, number_format($ts_seguros, 2, '.', ','), 1, 0, 'R');
            $pdf->Cell(21, 8, number_format($ts_honorarios, 2, '.', ','), 1, 0, 'R');
            $pdf->Cell(21, 8, number_format($ts_otros, 2, '.', ','), 1, 0, 'R');
            $pdf->Cell(21, 8, number_format($ts_gastos, 2, '.', ','), 1, 0, 'R');
            $pdf->Cell(21, 8, number_format($ts_costos, 2, '.', ','), 1, 0, 'R');
            $pdf->Cell(21, 8, number_format($ts_saldofavor, 2, '.', ','), 1, 0, 'R');
            $pdf->Cell(25, 8, number_format($ts_total, 2, '.', ','), 1, 0, 'R');
            $pdf->Ln(10);
        }//end while tipos crédito
    } // end if tipo crédito
} else if ($tipoinforme == 2){  //else tipoinforme
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell($ac - 53, 10, utf8_decode('CONSOLIDADO PROYECCIÓN MENSUAL'), 1, 0, 'C');
    $pdf->Ln(15);
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->Cell(16, 4, utf8_decode('MES'), 'LTR', 0, 'C');
    $pdf->Cell(13, 4, utf8_decode('AÑO'), 'LTR', 0, 'C');
    $pdf->Cell(21, 4, utf8_decode('CAPITAL'), 'LTR', 0, 'C');
    $pdf->Cell(21, 4, utf8_decode('INTERÉS'), 'LTR', 0, 'C');
    $pdf->Cell(21, 4, utf8_decode('INTERÉS'), 'LTR', 0, 'C');
    $pdf->Cell(21, 4, utf8_decode('RECARGO'), 'LTR', 0, 'C');
    $pdf->Cell(21, 4, utf8_decode('SEGURO'), 'LTR', 0, 'C');
    $pdf->Cell(21, 4, utf8_decode('HONORARIOS'), 'LTR', 0, 'C');
    $pdf->Cell(21, 4, utf8_decode('OTROS'), 'LTR', 0, 'C');
    $pdf->Cell(21, 4, utf8_decode('GASTOS'), 'LTR', 0, 'C');
    $pdf->Cell(21, 4, utf8_decode('COSTOS'), 'LTR', 0, 'C');
    $pdf->Cell(21, 4, utf8_decode('SALDOS'), 'LTR', 0, 'C');
    $pdf->Cell(25, 4, utf8_decode('TOTAL'), 'LTR', 0, 'C');
    $pdf->Ln(3);
    $pdf->Cell(16, 4, utf8_decode(''), 'LBR', 0, 'C');
    $pdf->Cell(13, 4, utf8_decode(''), 'LBR', 0, 'C');
    $pdf->Cell(21, 4, utf8_decode(''), 'LBR', 0, 'C');
    $pdf->Cell(21, 4, utf8_decode(''), 'LBR', 0, 'C');
    $pdf->Cell(21, 4, utf8_decode('ACUERDO'), 'LBR', 0, 'C');
    $pdf->Cell(21, 4, utf8_decode(''), 'LBR', 0, 'C');
    $pdf->Cell(21, 4, utf8_decode(''), 'LBR', 0, 'C');
    $pdf->Cell(21, 4, utf8_decode(''), 'LBR', 0, 'C');
    $pdf->Cell(21, 4, utf8_decode('CONCEPTOS'), 'LBR', 0, 'C');
    $pdf->Cell(21, 4, utf8_decode('ADMINISTRAT.'), 'LBR', 0, 'C');
    $pdf->Cell(21, 4, utf8_decode('JURÍDICOS'), 'LBR', 0, 'C');
    $pdf->Cell(21, 4, utf8_decode('A FAVOR'), 'LBR', 0, 'C');
    $pdf->Cell(25, 4, utf8_decode('MES'), 'LBR', 0, 'C');
    $pdf->Ln(4);


    $sqla = "SELECT DISTINCT year(d.Fecha_Posible_pago) as  yr
                             FROM DETALLE_CREDITO as d 
                             WHERE year(d.Fecha_Posible_pago) BETWEEN $anioini AND $aniofin
                             order by year(d.Fecha_Posible_pago) desc";
    $stmta = sqlsrv_query($conn, $sqla);
    $ts_capital1    = 0;
    $ts_interes1    = 0;
    $ts_interesa1   = 0;
    $ts_recargos1   = 0;
    $ts_seguros1    = 0;
    $ts_honorarios1 = 0;
    $ts_otros1      = 0;
    $ts_gastos1     = 0;
    $ts_costos1     = 0;
    $ts_saldofavor1 = 0;
    $ts_total1      = 0;
    while ($row = sqlsrv_fetch_array($stmta, SQLSRV_FETCH_ASSOC)) {
        for ($x = $mesini; $x <= $mesfin; $x++) {
            $ts_capital     = 0;
            $ts_interes     = 0;
            $ts_interesa    = 0;
            $ts_recargos    = 0;
            $ts_seguros     = 0;
            $ts_honorarios  = 0;
            $ts_otros       = 0;
            $ts_gastos      = 0;
            $ts_costos      = 0;
            $ts_saldofavor  = 0;
            $ts_total       = 0;

            $sql = " declare @mes int = '" . $x . "'; 
            declare @year int = '" . $row['yr'] . "';                                        
            select          
            /**saldo capital**/
            (select sum(dp.Valor_Concepto) from DETALLE_CREDITO as dp     
             LEFT JOIN CREDITO cd ON dp.Numero_Credito = cd.Numero_Credito     
             where  dp.Numero_Credito=c.Numero_Credito and dp.Id_Concepto='2'   
             and (MONTH (dp.Fecha_Posible_pago))=@mes  and (year (dp.Fecha_Posible_pago))=@year    
             and dp.Valor_Concepto>0) as Pago_Capital,   
            /**saldo interes **/
             (select sum(dp.Valor_Concepto) from DETALLE_CREDITO as dp    
             LEFT JOIN CREDITO cd ON dp.Numero_Credito = cd.Numero_Credito     
             where  dp.Numero_Credito=c.Numero_Credito and dp.Id_Concepto='3'   
             and (MONTH (dp.Fecha_Posible_pago))=@mes  and (year (dp.Fecha_Posible_pago))=@year    
             and dp.Valor_Concepto>0) as Pago_Interes,    

            /**interes aceuerto  **/
             (select sum(dp.Valor_Concepto) from DETALLE_CREDITO as dp     
             LEFT JOIN CREDITO cd ON dp.Numero_Credito = cd.Numero_Credito     
             where dp.Numero_Credito=c.Numero_Credito and dp.Id_Concepto='21'   
             and (MONTH (dp.Fecha_Posible_pago))=@mes  and (year (dp.Fecha_Posible_pago))=@year    
             and dp.Valor_Concepto>0) as Pago_Interes_Acuerdo,      

            /*recarcgo*/
             (select sum(dp.Valor_Concepto) from DETALLE_CREDITO as dp     
             LEFT JOIN CREDITO cd ON dp.Numero_Credito = cd.Numero_Credito     
             where  dp.Numero_Credito=c.Numero_Credito and dp.Id_Concepto='4'   
             and (MONTH (dp.Fecha_Posible_pago))=@mes  and (year (dp.Fecha_Posible_pago))=@year    
             and dp.Valor_Concepto>0) as Pago_Recargos,    

            /*seguro*/
             (select sum(dp.Valor_Concepto) from DETALLE_CREDITO as dp     
             LEFT JOIN CREDITO cd ON dp.Numero_Credito = cd.Numero_Credito     
             where  dp.Numero_Credito=c.Numero_Credito     
             and (MONTH (dp.Fecha_Posible_pago))=@mes  and (year (dp.Fecha_Posible_pago))=@year    
             and dp.Valor_Concepto>0 and dp.Id_Concepto in('5', '22')) as Pago_Seguro,   

            /*honorarios*/
             (select sum(dp.Valor_Concepto) from DETALLE_CREDITO as dp     
             LEFT JOIN CREDITO cd ON dp.Numero_Credito = cd.Numero_Credito     
             where  dp.Numero_Credito=c.Numero_Credito and dp.Id_Concepto='6'   
             and (MONTH (dp.Fecha_Posible_pago))=@mes  and (year (dp.Fecha_Posible_pago))=@year    
             and dp.Valor_Concepto>0) as Pago_Honorarios,    

            /*otros*/
             (select sum(dp.Valor_Concepto) from DETALLE_CREDITO as dp     
             LEFT JOIN CREDITO cd ON dp.Numero_Credito = cd.Numero_Credito     
             where  dp.Numero_Credito=c.Numero_Credito and dp.Id_Concepto IN('7', '8')    
             and (MONTH (dp.Fecha_Posible_pago))=@mes  and (year (dp.Fecha_Posible_pago))=@year    
             and dp.Valor_Concepto>0 ) as Pago_Otros,    

            /*gastos*/
             (select sum(dp.Valor_Concepto) from DETALLE_CREDITO as dp     
             LEFT JOIN CREDITO cd ON dp.Numero_Credito = cd.Numero_Credito     
             where  dp.Numero_Credito=c.Numero_Credito and dp.Id_Concepto='18'   
             and (MONTH (dp.Fecha_Posible_pago))=@mes  and (year (dp.Fecha_Posible_pago))=@year    
             and dp.Valor_Concepto>0 ) as Pago_Gastos,     

            /*costos*/
             (select sum(dp.Valor_Concepto) from DETALLE_CREDITO as dp     
             LEFT JOIN CREDITO cd ON dp.Numero_Credito = cd.Numero_Credito     
             where dp.Numero_Credito=c.Numero_Credito and dp.Id_Concepto='19'   
             and (MONTH (dp.Fecha_Posible_pago))=@mes  and (year (dp.Fecha_Posible_pago))=@year    
             and dp.Valor_Concepto>0 ) as Pago_Costos,     

            /*saldos a favor*/

             (select sum(dp.Valor_Concepto) from DETALLE_CREDITO as dp     
             LEFT JOIN CREDITO cd ON dp.Numero_Credito = cd.Numero_Credito     
             where dp.Numero_Credito=c.Numero_Credito and dp.Id_Concepto='20'   
             and (MONTH (dp.Fecha_Posible_pago))=@mes  and (year (dp.Fecha_Posible_pago))=@year    
             and dp.Valor_Concepto>0 ) as Pago_Saldos_Favor  

             from CREDITO as c     

            /**saldo capital**/
            where (select sum(dp.Valor_Concepto) from DETALLE_CREDITO as dp 
             LEFT JOIN CREDITO cd ON dp.Numero_Credito = cd.Numero_Credito     
             where  dp.Numero_Credito=c.Numero_Credito and dp.Id_Concepto='2'   
             and (MONTH (dp.Fecha_Posible_pago))=@mes  and (year (dp.Fecha_Posible_pago))=@year    
             and dp.Valor_Concepto>0) is not null  or   

            /**saldo interes **/
             (select sum(dp.Valor_Concepto) from DETALLE_CREDITO as dp    
             LEFT JOIN CREDITO cd ON dp.Numero_Credito = cd.Numero_Credito     
             where  dp.Numero_Credito=c.Numero_Credito and dp.Id_Concepto='3'   
             and (MONTH (dp.Fecha_Posible_pago))=@mes  and (year (dp.Fecha_Posible_pago))=@year    
             and dp.Valor_Concepto>0) is not null  or   

            /**interes aceuerto  **/
             (select sum(dp.Valor_Concepto) from DETALLE_CREDITO as dp     
             LEFT JOIN CREDITO cd ON dp.Numero_Credito = cd.Numero_Credito     
             where  dp.Numero_Credito=c.Numero_Credito and dp.Id_Concepto='21'   
             and (MONTH (dp.Fecha_Posible_pago))=@mes  and (year (dp.Fecha_Posible_pago))=@year    
             and dp.Valor_Concepto>0) is not null  or      

            /*recarcgo*/
             (select sum(dp.Valor_Concepto) from DETALLE_CREDITO as dp     
             LEFT JOIN CREDITO cd ON dp.Numero_Credito = cd.Numero_Credito     
             where  dp.Numero_Credito=c.Numero_Credito and dp.Id_Concepto='4'   
             and (MONTH (dp.Fecha_Posible_pago))=@mes  and (year (dp.Fecha_Posible_pago))=@year    
             and dp.Valor_Concepto>0) is not null  or   

            /*seguro*/
             (select sum(dp.Valor_Concepto) from DETALLE_CREDITO as dp     
             LEFT JOIN CREDITO cd ON dp.Numero_Credito = cd.Numero_Credito     
             where  dp.Numero_Credito=c.Numero_Credito and dp.Id_Concepto in('5', '22')
             and (MONTH (dp.Fecha_Posible_pago))=@mes  and (year (dp.Fecha_Posible_pago))=@year    
             and dp.Valor_Concepto>0 ) is not null  or  

            /*honorarios*/
             (select sum(dp.Valor_Concepto) from DETALLE_CREDITO as dp     
             LEFT JOIN CREDITO cd ON dp.Numero_Credito = cd.Numero_Credito     
             where  dp.Numero_Credito=c.Numero_Credito and dp.Id_Concepto='6'   
             and (MONTH (dp.Fecha_Posible_pago))=@mes  and (year (dp.Fecha_Posible_pago))=@year    
             and dp.Valor_Concepto>0) is not null  or   

            /*otros*/
             (select sum(dp.Valor_Concepto) from DETALLE_CREDITO as dp     
             LEFT JOIN CREDITO cd ON dp.Numero_Credito = cd.Numero_Credito     
             where  dp.Numero_Credito=c.Numero_Credito and dp.Id_Concepto IN('7', '8') 
             and (MONTH (dp.Fecha_Posible_pago))=@mes  and (year (dp.Fecha_Posible_pago))=@year    
             and dp.Valor_Concepto>0 ) is not null  or   

            /*gastos*/
             (select sum(dp.Valor_Concepto) from DETALLE_CREDITO as dp     
             LEFT JOIN CREDITO cd ON dp.Numero_Credito = cd.Numero_Credito     
             where  dp.Numero_Credito=c.Numero_Credito and dp.Id_Concepto='18'   
             and (MONTH (dp.Fecha_Posible_pago))=@mes  and (year (dp.Fecha_Posible_pago))=@year    
             and dp.Valor_Concepto>0 ) is not null  or   

            /*costos*/
             (select sum(dp.Valor_Concepto) from DETALLE_CREDITO as dp     
             LEFT JOIN CREDITO cd ON dp.Numero_Credito = cd.Numero_Credito     
             where dp.Numero_Credito=c.Numero_Credito and dp.Id_Concepto='19'   
             and (MONTH (dp.Fecha_Posible_pago))=@mes  and (year (dp.Fecha_Posible_pago))=@year    
             and dp.Valor_Concepto>0 ) is not null  or    

            /*saldos a favor*/

             (select sum(dp.Valor_Concepto) from DETALLE_CREDITO as dp     
             LEFT JOIN CREDITO cd ON dp.Numero_Credito = cd.Numero_Credito     
             where  dp.Numero_Credito=c.Numero_Credito and dp.Id_Concepto='20'   
             and (MONTH (dp.Fecha_Posible_pago))=@mes  and (year (dp.Fecha_Posible_pago))=@year    
             and dp.Valor_Concepto>0 ) is not null  ";

            $stmtpm = sqlsrv_query($conn, $sql);
            while ($rows = sqlsrv_fetch_array($stmtpm, SQLSRV_FETCH_ASSOC)) {

                $mes        = $x;
                $anio       = $row['yr'];
                $capital    = $rows["Pago_Capital"];
                $interes    = $rows["Pago_Interes"];
                $interesa   = $rows["Pago_Interes_Acuerdo"];
                $recargos   = $rows["Pago_Recargos"];
                $seguros    = $rows["Pago_Seguro"];
                $honorarios = $rows["Pago_Honorarios"];
                $otros      = $rows["Pago_Otros"];
                $gastos     = $rows["Pago_Gastos"];
                $costos     = $rows["Pago_Costos"];
                $saldofavor = $rows["Pago_Saldos_Favor"];
                $total      = ($capital + $interes + $interesa + $recargos + $seguros + $honorarios + $otros + $gastos + $costos + $saldofavor);

                if ($mes == 1) {
                    $mes = "Enero";
                }
                if ($mes == 2) {
                    $mes = "Febrero";
                }
                if ($mes == 3) {
                    $mes = "Marzo";
                }
                if ($mes == 4) {
                    $mes = "Abril";
                }
                if ($mes == 5) {
                    $mes = "Mayo";
                }
                if ($mes == 6) {
                    $mes = "Junio";
                }
                if ($mes == 7) {
                    $mes = "Julio";
                }
                if ($mes == 8) {
                    $mes = "Agosto";
                }
                if ($mes == 9) {
                    $mes = "Septiembre";
                }
                if ($mes == 10) {
                    $mes = "Octubre";
                }
                if ($mes == 11) {
                    $mes = "Noviembre";
                }
                if ($mes == 12) {
                    $mes = "Diciembre";
                }

                $ts_capital     += $rows["Pago_Capital"];
                $ts_interes     += $rows["Pago_Interes"];
                $ts_interesa    += $rows["Pago_Interes_Acuerdo"];
                $ts_recargos    += $rows["Pago_Recargos"];
                $ts_seguros     += $rows["Pago_Seguro"];
                $ts_honorarios  += $rows["Pago_Honorarios"];
                $ts_otros       += $rows["Pago_Otros"];
                $ts_gastos      += $rows["Pago_Gastos"];
                $ts_costos      += $rows["Pago_Costos"];
                $ts_saldofavor  += $rows["Pago_Saldos_Favor"];
                $ts_total       += $total;
            }


            $ts_capital1    += $ts_capital;
            $ts_interes1    += $ts_interes;
            $ts_interesa1   += $ts_interesa;
            $ts_recargos1   += $ts_recargos;
            $ts_seguros1    += $ts_seguros;
            $ts_honorarios1 += $ts_honorarios;
            $ts_otros1      += $ts_otros;
            $ts_gastos1     += $ts_gastos;
            $ts_costos1     += $ts_costos;
            $ts_saldofavor1 += $ts_saldofavor;
            $ts_total1      += $ts_total;


            $pdf->SetFont('Arial', '', 7);
            $pdf->Cell(16, 8, utf8_decode($mes), 1, 0, 'C');
            $pdf->Cell(13, 8, $anio, 1, 0, 'C');
            $pdf->Cell(21, 8, number_format($ts_capital, 2, '.', ','), 1, 0, 'R');
            $pdf->Cell(21, 8, number_format($ts_interes, 2, '.', ','), 1, 0, 'R');
            $pdf->Cell(21, 8, number_format($ts_interesa, 2, '.', ','), 1, 0, 'R');
            $pdf->Cell(21, 8, number_format($ts_recargos, 2, '.', ','), 1, 0, 'R');
            $pdf->Cell(21, 8, number_format($ts_seguros, 2, '.', ','), 1, 0, 'R');
            $pdf->Cell(21, 8, number_format($ts_honorarios, 2, '.', ','), 1, 0, 'R');
            $pdf->Cell(21, 8, number_format($ts_otros, 2, '.', ','), 1, 0, 'R');
            $pdf->Cell(21, 8, number_format($ts_gastos, 2, '.', ','), 1, 0, 'R');
            $pdf->Cell(21, 8, number_format($ts_costos, 2, '.', ','), 1, 0, 'R');
            $pdf->Cell(21, 8, number_format($ts_saldofavor, 2, '.', ','), 1, 0, 'R');
            $pdf->Cell(25, 8, number_format($ts_total, 2, '.', ','), 1, 0, 'R');
            $pdf->Ln(8);
        }



        $pdf->SetFont('Arial', 'B', 5.5);
        $pdf->Cell(29, 8, utf8_decode('TOTALES'), 1, 0, 'L');
        $pdf->Cell(21, 8, number_format($ts_capital1, 2, '.', ','), 1, 0, 'R');
        $pdf->Cell(21, 8, number_format($ts_interes1, 2, '.', ','), 1, 0, 'R');
        $pdf->Cell(21, 8, number_format($ts_interesa1, 2, '.', ','), 1, 0, 'R');
        $pdf->Cell(21, 8, number_format($ts_recargos1, 2, '.', ','), 1, 0, 'R');
        $pdf->Cell(21, 8, number_format($ts_seguros1, 2, '.', ','), 1, 0, 'R');
        $pdf->Cell(21, 8, number_format($ts_honorarios1, 2, '.', ','), 1, 0, 'R');
        $pdf->Cell(21, 8, number_format($ts_otros1, 2, '.', ','), 1, 0, 'R');
        $pdf->Cell(21, 8, number_format($ts_gastos1, 2, '.', ','), 1, 0, 'R');
        $pdf->Cell(21, 8, number_format($ts_costos1, 2, '.', ','), 1, 0, 'R');
        $pdf->Cell(21, 8, number_format($ts_saldofavor1, 2, '.', ','), 1, 0, 'R');
        $pdf->Cell(25, 8, number_format($ts_total1, 2, '.', ','), 1, 0, 'R');
        $pdf->Ln(10);
    }
}else if ($tipoinforme == 3){

    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell($ac - 53, 10, utf8_decode('RECAUDO MENSUAL'), 1, 0, 'C');
    $pdf->Ln(15);
    $pdf->SetFont('Arial','B',8);
    $pdf->Cell(16,4, utf8_decode('MES'),'LTR',0,'C');
    $pdf->Cell(13,4, utf8_decode('AÑO'),'LTR',0,'C');
    $pdf->Cell(21,4, utf8_decode('CAPITAL'),'LTR',0,'C');
    $pdf->Cell(21,4,utf8_decode('INTERÉS'),'LTR',0,'C');
    $pdf->Cell(21,4,utf8_decode('INTERÉS'),'LTR',0,'C');
    $pdf->Cell(21,4,utf8_decode('RECARGO'),'LTR',0,'C');
    $pdf->Cell(21,4,utf8_decode('SEGURO'),'LTR',0,'C');
    $pdf->Cell(21,4,utf8_decode('HONORARIOS'),'LTR',0,'C');
    $pdf->Cell(21,4,utf8_decode('OTROS'),'LTR',0,'C');
    $pdf->Cell(21,4,utf8_decode('GASTOS'),'LTR',0,'C');
    $pdf->Cell(21,4,utf8_decode('COSTOS'),'LTR',0,'C');
    $pdf->Cell(21,4,utf8_decode('SALDOS'),'LTR',0,'C');
    $pdf->Cell(25,4,utf8_decode('TOTAL'),'LTR',0,'C');
    $pdf->Ln(3);
    $pdf->Cell(16,4, utf8_decode(''),'LBR',0,'C');
    $pdf->Cell(13,4, utf8_decode(''),'LBR',0,'C');
    $pdf->Cell(21,4, utf8_decode(''),'LBR',0,'C');
    $pdf->Cell(21,4,utf8_decode(''),'LBR',0,'C');
    $pdf->Cell(21,4,utf8_decode('ACUERDO'),'LBR',0,'C');
    $pdf->Cell(21,4,utf8_decode(''),'LBR',0,'C');
    $pdf->Cell(21,4,utf8_decode(''),'LBR',0,'C');
    $pdf->Cell(21,4,utf8_decode('CONCEPTOS'),'LBR',0,'C');
    $pdf->Cell(21,4,utf8_decode(''),'LBR',0,'C');
    $pdf->Cell(21,4,utf8_decode('ADMINISTRAT.'),'LBR',0,'C');
    $pdf->Cell(21,4,utf8_decode('JURÍDICOS'),'LBR',0,'C');
    $pdf->Cell(21,4,utf8_decode('A FAVOR'),'LBR',0,'C');
    $pdf->Cell(25,4,utf8_decode('MES'),'LBR',0,'C');
    $pdf->Ln(4);

    $sqla = "SELECT DISTINCT year(d.Fecha_Posible_pago) as  yr
    FROM DETALLE_CREDITO as d 
    WHERE year(d.Fecha_Posible_pago) BETWEEN $anioini AND $aniofin
    order by year(d.Fecha_Posible_pago) desc";
    $stmta = sqlsrv_query($conn, $sqla);

    $ts_capital1     = 0;
    $ts_interes1     = 0;
    $ts_interesa1    = 0;
    $ts_recargos1    = 0;
    $ts_seguros1     = 0;
    $ts_honorarios1  = 0;
    $ts_otros1       = 0;
    $ts_gastos1      = 0;
    $ts_costos1      = 0;
    $ts_saldofavor1  = 0;
    $ts_total1       = 0;
    while ($row = sqlsrv_fetch_array($stmta, SQLSRV_FETCH_ASSOC)) {
        for ($x = $mesini; $x <= $mesfin; $x++) { 
            $ts_capital     = 0;
            $ts_interes     = 0;
            $ts_interesa    = 0;
            $ts_recargos    = 0;
            $ts_seguros     = 0;
            $ts_honorarios  = 0;
            $ts_otros       = 0;
            $ts_gastos      = 0;
            $ts_costos      = 0;
            $ts_saldofavor  = 0;
            $ts_total       = 0;   

            #* PAGOS NORMALES
            $sql = "DECLARE @mes int = '" . $x . "'; 
                    DECLARE @year int = '" . $row['yr'] . "';                   
            SELECT 
            (SELECT sum(dp.Valor_pago) FROM DETALLE_PAGO as dp 
                LEFT JOIN CREDITO c ON dp.Numero_Credito = c.Numero_Credito   
                WHERE   dp.Numero_Recibo=pg.Numero_Recibo and dp.Id_Concepto='2') as Pago_Capital, 
            (SELECT sum(dp.Valor_pago) FROM DETALLE_PAGO as dp
                LEFT JOIN CREDITO c ON dp.Numero_Credito = c.Numero_Credito   
                WHERE   dp.Numero_Recibo=pg.Numero_Recibo and dp.Id_Concepto='3') as Pago_Interes, 
            (SELECT sum(dp.Valor_pago) FROM DETALLE_PAGO as dp  
                LEFT JOIN CREDITO c ON dp.Numero_Credito = c.Numero_Credito   
                WHERE   dp.Numero_Recibo=pg.Numero_Recibo and dp.Id_Concepto='21') as Pago_Interes_Acuerdo, 
            (SELECT sum(dp.Valor_pago) FROM DETALLE_PAGO as dp  
                LEFT JOIN CREDITO c ON dp.Numero_Credito = c.Numero_Credito   
                WHERE   dp.Numero_Recibo=pg.Numero_Recibo and dp.Id_Concepto='4') as Pago_Recargos, 
            (SELECT sum(dp.Valor_pago) FROM DETALLE_PAGO as dp  
                LEFT JOIN CREDITO c ON dp.Numero_Credito = c.Numero_Credito   
                WHERE   dp.Numero_Recibo=pg.Numero_Recibo and dp.Id_Concepto IN ('5', '22')) as Pago_Seguro,
            (SELECT sum(dp.Valor_pago) FROM DETALLE_PAGO as dp  
                LEFT JOIN CREDITO c ON dp.Numero_Credito = c.Numero_Credito   
                WHERE   dp.Numero_Recibo=pg.Numero_Recibo and dp.Id_Concepto='6') as Pago_Honorarios,  
            (SELECT sum(dp.Valor_pago) FROM DETALLE_PAGO as dp  
                LEFT JOIN CREDITO c ON dp.Numero_Credito = c.Numero_Credito   
                WHERE   dp.Numero_Recibo=pg.Numero_Recibo and dp.Id_Concepto IN('7', '8')) as Pago_Otros, 
            (SELECT sum(dp.Valor_pago) FROM DETALLE_PAGO as dp  
                LEFT JOIN CREDITO c ON dp.Numero_Credito = c.Numero_Credito   
                WHERE  dp.Numero_Recibo=pg.Numero_Recibo and dp.Id_Concepto='18') as Pago_Gastos,
            (SELECT sum(dp.Valor_pago) FROM DETALLE_PAGO as dp 
                LEFT JOIN CREDITO c ON dp.Numero_Credito = c.Numero_Credito   
                WHERE  dp.Numero_Recibo=pg.Numero_Recibo and dp.Id_Concepto='19') as Pago_Costos,  
            (SELECT sum(dp.Valor_pago) FROM DETALLE_PAGO as dp  
                LEFT JOIN CREDITO c ON dp.Numero_Credito = c.Numero_Credito   
                WHERE  dp.Numero_Recibo=pg.Numero_Recibo and dp.Id_Concepto='20') as Pago_Saldos_Favor 

            FROM PAGOS as pg   
            LEFT JOIN CREDITO as c on c.Numero_Credito=pg.Numero_Credito  
            WHERE  (MONTH (pg.Fecha_Pago))=@mes  AND (year(pg.Fecha_Pago))=@year  
                AND pg.Id_Estado_Pago IN ('1','2') 
            ORDER BY c.Id_Tipo_Credito ASC";

            $stmtpm = sqlsrv_query($conn, $sql);
            while ($rows = sqlsrv_fetch_array($stmtpm, SQLSRV_FETCH_ASSOC)) {
                    $capital    = $rows["Pago_Capital"];
                    $interes    = $rows["Pago_Interes"];
                    $interesa   = $rows["Pago_Interes_Acuerdo"];
                    $recargos   = $rows["Pago_Recargos"];
                    $seguros    = $rows["Pago_Seguro"];
                    $honorarios = $rows["Pago_Honorarios"];
                    $otros      = $rows["Pago_Otros"];
                    $gastos     = $rows["Pago_Gastos"];
                    $costos     = $rows["Pago_Costos"];
                    $saldofavor = $rows["Pago_Saldos_Favor"];
                    $total      = ($capital + $interes + $interesa + $recargos + $seguros + $honorarios + $otros + $gastos + $costos + $saldofavor);

                    $ts_capital     += $capital;
                    $ts_interes     += $interes;
                    $ts_interesa    += $interesa;
                    $ts_recargos    += $recargos;
                    $ts_seguros     += $seguros;
                    $ts_honorarios  += $honorarios;
                    $ts_otros       += $otros;
                    $ts_gastos      += $gastos;
                    $ts_costos      += $costos;
                    $ts_saldofavor  += $saldofavor;
                    $ts_total       += $total;

            }//END WHILE
            
            #* PAGOS ANULADOS
            $sql = "DECLARE @mes int = '" . $x . "'; 
                    DECLARE @year int = '" . $row['yr'] . "';                   
            SELECT 
            (SELECT sum(dp.Valor_pago) FROM DETALLE_PAGO as dp 
                LEFT JOIN CREDITO c ON dp.Numero_Credito = c.Numero_Credito   
                WHERE   dp.Numero_Recibo=pg.Numero_Recibo and dp.Id_Concepto='2') as Pago_Capital, 
            (SELECT sum(dp.Valor_pago) FROM DETALLE_PAGO as dp
                LEFT JOIN CREDITO c ON dp.Numero_Credito = c.Numero_Credito   
                WHERE   dp.Numero_Recibo=pg.Numero_Recibo and dp.Id_Concepto='3') as Pago_Interes, 
            (SELECT sum(dp.Valor_pago) FROM DETALLE_PAGO as dp  
                LEFT JOIN CREDITO c ON dp.Numero_Credito = c.Numero_Credito   
                WHERE   dp.Numero_Recibo=pg.Numero_Recibo and dp.Id_Concepto='21') as Pago_Interes_Acuerdo, 
            (SELECT sum(dp.Valor_pago) FROM DETALLE_PAGO as dp  
                LEFT JOIN CREDITO c ON dp.Numero_Credito = c.Numero_Credito   
                WHERE   dp.Numero_Recibo=pg.Numero_Recibo and dp.Id_Concepto='4') as Pago_Recargos, 
            (SELECT sum(dp.Valor_pago) FROM DETALLE_PAGO as dp  
                LEFT JOIN CREDITO c ON dp.Numero_Credito = c.Numero_Credito   
                WHERE   dp.Numero_Recibo=pg.Numero_Recibo and dp.Id_Concepto IN ('5', '22')) as Pago_Seguro,
            (SELECT sum(dp.Valor_pago) FROM DETALLE_PAGO as dp  
                LEFT JOIN CREDITO c ON dp.Numero_Credito = c.Numero_Credito   
                WHERE   dp.Numero_Recibo=pg.Numero_Recibo and dp.Id_Concepto='6') as Pago_Honorarios,  
            (SELECT sum(dp.Valor_pago) FROM DETALLE_PAGO as dp  
                LEFT JOIN CREDITO c ON dp.Numero_Credito = c.Numero_Credito   
                WHERE   dp.Numero_Recibo=pg.Numero_Recibo and dp.Id_Concepto IN('7', '8')) as Pago_Otros, 
            (SELECT sum(dp.Valor_pago) FROM DETALLE_PAGO as dp  
                LEFT JOIN CREDITO c ON dp.Numero_Credito = c.Numero_Credito   
                WHERE  dp.Numero_Recibo=pg.Numero_Recibo and dp.Id_Concepto='18') as Pago_Gastos,
            (SELECT sum(dp.Valor_pago) FROM DETALLE_PAGO as dp 
                LEFT JOIN CREDITO c ON dp.Numero_Credito = c.Numero_Credito   
                WHERE  dp.Numero_Recibo=pg.Numero_Recibo and dp.Id_Concepto='19') as Pago_Costos,  
            (SELECT sum(dp.Valor_pago) FROM DETALLE_PAGO as dp  
                LEFT JOIN CREDITO c ON dp.Numero_Credito = c.Numero_Credito   
                WHERE  dp.Numero_Recibo=pg.Numero_Recibo and dp.Id_Concepto='20') as Pago_Saldos_Favor 

            FROM PAGOS as pg   
            LEFT JOIN CREDITO as c on c.Numero_Credito=pg.Numero_Credito  
            WHERE  (MONTH (pg.Fecha_Pago))=@mes  AND (year(pg.Fecha_Pago))=@year  
                AND pg.Id_Estado_Pago IN ('8') 
            ORDER BY c.Id_Tipo_Credito ASC";

            $stmtpm = sqlsrv_query($conn, $sql);
            while ($rows = sqlsrv_fetch_array($stmtpm, SQLSRV_FETCH_ASSOC)) {
                    $capital    = $rows["Pago_Capital"];
                    $interes    = $rows["Pago_Interes"];
                    $interesa   = $rows["Pago_Interes_Acuerdo"];
                    $recargos   = $rows["Pago_Recargos"];
                    $seguros    = $rows["Pago_Seguro"];
                    $honorarios = $rows["Pago_Honorarios"];
                    $otros      = $rows["Pago_Otros"];
                    $gastos     = $rows["Pago_Gastos"];
                    $costos     = $rows["Pago_Costos"];
                    $saldofavor = $rows["Pago_Saldos_Favor"];
                    $total      = ($capital + $interes + $interesa + $recargos + $seguros + $honorarios + $otros + $gastos + $costos + $saldofavor);

                    $ts_capital     -= $capital;
                    $ts_interes     -= $interes;
                    $ts_interesa    -= $interesa;
                    $ts_recargos    -= $recargos;
                    $ts_seguros     -= $seguros;
                    $ts_honorarios  -= $honorarios;
                    $ts_otros       -= $otros;
                    $ts_gastos      -= $gastos;
                    $ts_costos      -= $costos;
                    $ts_saldofavor  -= $saldofavor;
                    $ts_total       -= $total;

            }//END WHILE
            $ts_capital1     += $ts_capital;
            $ts_interes1     += $ts_interes;
            $ts_interesa1    += $ts_interesa;
            $ts_recargos1    += $ts_recargos;
            $ts_seguros1     += $ts_seguros;
            $ts_honorarios1  += $ts_honorarios;
            $ts_otros1       += $ts_otros;
            $ts_gastos1      += $ts_gastos;
            $ts_costos1      += $ts_costos;
            $ts_saldofavor1  += $ts_saldofavor;
            $ts_total1       += $ts_total;

            $mes        = $x;
            $anio       = $row['yr'];
            if ($mes == 1) {
                $mes = "Enero";
            }
            if ($mes == 2) {
                $mes = "Febrero";
            }
            if ($mes == 3) {
                $mes = "Marzo";
            }
            if ($mes == 4) {
                $mes = "Abril";
            }
            if ($mes == 5) {
                $mes = "Mayo";
            }
            if ($mes == 6) {
                $mes = "Junio";
            }
            if ($mes == 7) {
                $mes = "Julio";
            }
            if ($mes == 8) {
                $mes = "Agosto";
            }
            if ($mes == 9) {
                $mes = "Septiembre";
            }
            if ($mes == 10) {
                $mes = "Octubre";
            }
            if ($mes == 11) {
                $mes = "Noviembre";
            }
            if ($mes == 12) {
                $mes = "Diciembre";
            }
            $pdf->SetFont('Arial', '', 7);
            $pdf->Cell(16, 8, utf8_decode($mes), 1, 0, 'C');
            $pdf->Cell(13, 8, $anio, 1, 0, 'C');
            $pdf->Cell(21, 8, number_format($ts_capital, 2, '.', ','), 1, 0, 'R');
            $pdf->Cell(21, 8, number_format($ts_interes, 2, '.', ','), 1, 0, 'R');
            $pdf->Cell(21, 8, number_format($ts_interesa, 2, '.', ','), 1, 0, 'R');
            $pdf->Cell(21, 8, number_format($ts_recargos, 2, '.', ','), 1, 0, 'R');
            $pdf->Cell(21, 8, number_format($ts_seguros, 2, '.', ','), 1, 0, 'R');
            $pdf->Cell(21, 8, number_format($ts_honorarios, 2, '.', ','), 1, 0, 'R');
            $pdf->Cell(21, 8, number_format($ts_otros, 2, '.', ','), 1, 0, 'R');
            $pdf->Cell(21, 8, number_format($ts_gastos, 2, '.', ','), 1, 0, 'R');
            $pdf->Cell(21, 8, number_format($ts_costos, 2, '.', ','), 1, 0, 'R');
            $pdf->Cell(21, 8, number_format($ts_saldofavor, 2, '.', ','), 1, 0, 'R');
            $pdf->SetFont('Arial', 'B', 7);
            $pdf->Cell(25, 8, number_format($ts_total, 2, '.', ','), 1, 0, 'R');
            $pdf->Ln(8);
        } //end for
    }

    $pdf->SetFont('Arial', 'B', 7);
    $pdf->Cell(29, 8, utf8_decode('TOTALES'), 1, 0, 'L');
    $pdf->Cell(21, 8, number_format($ts_capital1, 2, '.', ','), 1, 0, 'R');
    $pdf->Cell(21, 8, number_format($ts_interes1, 2, '.', ','), 1, 0, 'R');
    $pdf->Cell(21, 8, number_format($ts_interesa1, 2, '.', ','), 1, 0, 'R');
    $pdf->Cell(21, 8, number_format($ts_recargos1, 2, '.', ','), 1, 0, 'R');
    $pdf->Cell(21, 8, number_format($ts_seguros1, 2, '.', ','), 1, 0, 'R');
    $pdf->Cell(21, 8, number_format($ts_honorarios1, 2, '.', ','), 1, 0, 'R');
    $pdf->Cell(21, 8, number_format($ts_otros1, 2, '.', ','), 1, 0, 'R');
    $pdf->Cell(21, 8, number_format($ts_gastos1, 2, '.', ','), 1, 0, 'R');
    $pdf->Cell(21, 8, number_format($ts_costos1, 2, '.', ','), 1, 0, 'R');
    $pdf->Cell(21, 8, number_format($ts_saldofavor1, 2, '.', ','), 1, 0, 'R');
    $pdf->Cell(25, 8, number_format($ts_total1, 2, '.', ','), 1, 0, 'R');
    $pdf->Ln(10);
}else {

    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell($ac - 53, 10, utf8_decode('RECAUDO MENSUAL POR TIPO CRÉDITO'), 1, 0, 'C');
    $pdf->Ln(15);
    if(empty($tipocredito)){
        $sqltc = "SELECT DISTINCT tc.Identificador AS Id, tc.Nombre_Tipo_credito  as Nombre
            FROM PAGOS pg 
            LEFT JOIN CREDITO C ON pg.Numero_Credito = C.Numero_Credito 
            LEFT JOIN TIPO_CREDITO tc ON tc.Identificador = C.Id_Tipo_Credito 
            WHERE year(pg.Fecha_Pago) BETWEEN '$anioini' AND '$aniofin'
            ORDER BY tc.Nombre_Tipo_credito";
    } else {
        $sqltc = "SELECT Identificador AS Id, Nombre_Tipo_Credito as Nombre from TIPO_CREDITO 
        WHERE Identificador = $tipocredito
        ORDER BY TIPO_CREDITO.Nombre_Tipo_Credito ASC";
    }
    $stmttc = sqlsrv_query($conn, $sqltc);
    $ts_capital1tt     = 0;
    $ts_interes1tt     = 0;
    $ts_interesa1tt    = 0;
    $ts_recargos1tt    = 0;
    $ts_seguros1tt     = 0;
    $ts_honorarios1tt  = 0;
    $ts_otros1tt       = 0;
    $ts_gastos1tt      = 0;
    $ts_costos1tt      = 0;
    $ts_saldofavor1tt  = 0;
    $ts_total1tt       = 0;
    while ($rowtc = sqlsrv_fetch_array($stmttc, SQLSRV_FETCH_ASSOC)){
        $tipocredito = $rowtc['Id'];
        $credito     = $rowtc['Nombre'];

        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(264, 6, utf8_decode('TIPO CRÉDITO: ' . $credito), 'LTR', 0, 'C');
        $pdf->Ln(6);
        $pdf->SetFont('Arial', 'B', 7);
        $pdf->Cell(16, 4, utf8_decode('MES'), 'LTR', 0, 'C');
        $pdf->Cell(13, 4, utf8_decode('AÑO'), 'LTR', 0, 'C');
        $pdf->Cell(21, 4, utf8_decode('CAPITAL'), 'LTR', 0, 'C');
        $pdf->Cell(21, 4, utf8_decode('INTERÉS'), 'LTR', 0, 'C');
        $pdf->Cell(21, 4, utf8_decode('INTERÉS'), 'LTR', 0, 'C');
        $pdf->Cell(21, 4, utf8_decode('RECARGO'), 'LTR', 0, 'C');
        $pdf->Cell(21, 4, utf8_decode('SEGURO'), 'LTR', 0, 'C');
        $pdf->Cell(21, 4, utf8_decode('HONORARIOS'), 'LTR', 0, 'C');
        $pdf->Cell(21, 4, utf8_decode('OTROS'), 'LTR', 0, 'C');
        $pdf->Cell(21, 4, utf8_decode('GASTOS'), 'LTR', 0, 'C');
        $pdf->Cell(21, 4, utf8_decode('COSTOS'), 'LTR', 0, 'C');
        $pdf->Cell(21, 4, utf8_decode('SALDOS'), 'LTR', 0, 'C');
        $pdf->Cell(25, 4, utf8_decode('TOTAL'), 'LTR', 0, 'C');
        $pdf->Ln(3);
        $pdf->Cell(16, 4, utf8_decode(''), 'LBR', 0, 'C');
        $pdf->Cell(13, 4, utf8_decode(''), 'LBR', 0, 'C');
        $pdf->Cell(21, 4, utf8_decode(''), 'LBR', 0, 'C');
        $pdf->Cell(21, 4, utf8_decode(''), 'LBR', 0, 'C');
        $pdf->Cell(21, 4, utf8_decode('ACUERDO'), 'LBR', 0, 'C');
        $pdf->Cell(21, 4, utf8_decode(''), 'LBR', 0, 'C');
        $pdf->Cell(21, 4, utf8_decode(''), 'LBR', 0, 'C');
        $pdf->Cell(21, 4, utf8_decode(''), 'LBR', 0, 'C');
        $pdf->Cell(21, 4, utf8_decode('CONCEPTOS'), 'LBR', 0, 'C');
        $pdf->Cell(21, 4, utf8_decode('ADMINISTRAT.'), 'LBR', 0, 'C');
        $pdf->Cell(21, 4, utf8_decode('JURÍDICOS'), 'LBR', 0, 'C');
        $pdf->Cell(21, 4, utf8_decode('A FAVOR'), 'LBR', 0, 'C');
        $pdf->Cell(25, 4, utf8_decode('MES'), 'LBR', 0, 'C');
        $pdf->Ln(4);


        //*****************************************************************
     
        $sqla = "SELECT DISTINCT year(d.Fecha_Posible_pago) as  yr
            FROM DETALLE_CREDITO as d 
            WHERE year(d.Fecha_Posible_pago) BETWEEN $anioini AND $aniofin
            order by year(d.Fecha_Posible_pago) desc";
        $stmt = sqlsrv_query($conn, $sqla);

            $ts_capital1     = 0;
            $ts_interes1     = 0;
            $ts_interesa1    = 0;
            $ts_recargos1    = 0;
            $ts_seguros1     = 0;
            $ts_honorarios1  = 0;
            $ts_otros1       = 0;
            $ts_gastos1      = 0;
            $ts_costos1      = 0;
            $ts_saldofavor1  = 0;
            $ts_total1       = 0;
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            for ($x = $mesini; $x <= $mesfin; $x++) {
                $ts_capital     = 0;
                $ts_interes     = 0;
                $ts_interesa    = 0;
                $ts_recargos    = 0;
                $ts_seguros     = 0;
                $ts_honorarios  = 0;
                $ts_otros       = 0;
                $ts_gastos      = 0;
                $ts_costos      = 0;
                $ts_saldofavor  = 0;
                $ts_total       = 0;
               
                #PAGO NORMAL
                $sql =" DECLARE @mes int = '" . $x . "'; 
                       DECLARE @year int = '" . $row['yr'] . "';
                       DECLARE @tipocredito int = '" . $tipocredito . "';
                SELECT  c.Id_Tipo_Credito,        
                (SELECT sum(dp.Valor_pago) FROM DETALLE_PAGO as dp 
                LEFT JOIN CREDITO c ON dp.Numero_Credito = c.Numero_Credito   
                WHERE  c.Id_Tipo_Credito =@tipocredito AND  dp.Numero_Recibo=pg.Numero_Recibo 
                and dp.Id_Concepto='2') as Pago_Capital, 
                (SELECT sum(dp.Valor_pago) FROM DETALLE_PAGO as dp
                LEFT JOIN CREDITO c ON dp.Numero_Credito = c.Numero_Credito   
                WHERE  c.Id_Tipo_Credito =@tipocredito AND dp.Numero_Recibo=pg.Numero_Recibo 
                and dp.Id_Concepto='3') as Pago_Interes, 
                (SELECT sum(dp.Valor_pago) FROM DETALLE_PAGO as dp  
                LEFT JOIN CREDITO c ON dp.Numero_Credito = c.Numero_Credito   
                WHERE  c.Id_Tipo_Credito =@tipocredito AND dp.Numero_Recibo=pg.Numero_Recibo 
                and dp.Id_Concepto='21') as Pago_Interes_Acuerdo, 
                (SELECT sum(dp.Valor_pago) FROM DETALLE_PAGO as dp  
                LEFT JOIN CREDITO c ON dp.Numero_Credito = c.Numero_Credito   
                WHERE  c.Id_Tipo_Credito =@tipocredito AND dp.Numero_Recibo=pg.Numero_Recibo 
                and dp.Id_Concepto='4') as Pago_Recargos,
                (SELECT sum(dp.Valor_pago) FROM DETALLE_PAGO as dp  
                LEFT JOIN CREDITO c ON dp.Numero_Credito = c.Numero_Credito   
                WHERE  c.Id_Tipo_Credito =@tipocredito AND dp.Numero_Recibo=pg.Numero_Recibo 
                and dp.Id_Concepto IN ('5', '22') ) as Pago_Seguro,
                (SELECT sum(dp.Valor_pago) FROM DETALLE_PAGO as dp  
                LEFT JOIN CREDITO c ON dp.Numero_Credito = c.Numero_Credito   
                WHERE  c.Id_Tipo_Credito =@tipocredito AND dp.Numero_Recibo=pg.Numero_Recibo 
                and dp.Id_Concepto='6') as Pago_Honorarios,  
                (select sum(dp.Valor_pago) FROM DETALLE_PAGO as dp  
                LEFT JOIN CREDITO c ON dp.Numero_Credito = c.Numero_Credito   
                WHERE  c.Id_Tipo_Credito =@tipocredito AND dp.Numero_Recibo=pg.Numero_Recibo
                and dp.Id_Concepto IN ('7', '8') ) as Pago_Otros, 
                (SELECT sum(dp.Valor_pago) FROM DETALLE_PAGO as dp  
                LEFT JOIN CREDITO c ON dp.Numero_Credito = c.Numero_Credito   
                where  c.Id_Tipo_Credito =@tipocredito AND dp.Numero_Recibo=pg.Numero_Recibo 
                and dp.Id_Concepto='18') as Pago_Gastos,
                (SELECT sum(dp.Valor_pago) FROM DETALLE_PAGO as dp 
                LEFT JOIN CREDITO c ON dp.Numero_Credito = c.Numero_Credito   
                where  c.Id_Tipo_Credito =@tipocredito AND dp.Numero_Recibo=pg.Numero_Recibo 
                and dp.Id_Concepto='19') as Pago_Costos,  
                (SELECT sum(dp.Valor_pago) FROM DETALLE_PAGO as dp  
                LEFT JOIN CREDITO c ON dp.Numero_Credito = c.Numero_Credito   
                WHERE  c.Id_Tipo_Credito =@tipocredito AND dp.Numero_Recibo=pg.Numero_Recibo 
                and dp.Id_Concepto='20') as Pago_Saldos_Favor 
                FROM PAGOS as pg   left join CREDITO as c on c.Numero_Credito=pg.Numero_Credito  
                where c.Id_Tipo_Credito =@tipocredito
                and (MONTH (pg.Fecha_Pago))=@mes  and (year (pg.Fecha_Pago))=@year  
                AND pg.Id_Estado_Pago IN ('1','2') 
                order by c.Id_Tipo_Credito asc";
                $stmtpm = sqlsrv_query($conn, $sql);
                while ($rows = sqlsrv_fetch_array($stmtpm, SQLSRV_FETCH_ASSOC)) {
                    $capital    = $rows["Pago_Capital"];
                    $interes    = $rows["Pago_Interes"];
                    $interesa   = $rows["Pago_Interes_Acuerdo"];
                    $recargos   = $rows["Pago_Recargos"];
                    $seguros    = $rows["Pago_Seguro"];
                    $honorarios = $rows["Pago_Honorarios"];
                    $otros      = $rows["Pago_Otros"];
                    $gastos     = $rows["Pago_Gastos"];
                    $costos     = $rows["Pago_Costos"];
                    $saldofavor = $rows["Pago_Saldos_Favor"];
                    $total      = ($capital + $interes + $interesa + $recargos + $seguros + $honorarios + $otros + $gastos + $costos + $saldofavor);

                    $ts_capital     += $rows["Pago_Capital"];
                    $ts_interes     += $rows["Pago_Interes"];
                    $ts_interesa    += $rows["Pago_Interes_Acuerdo"];
                    $ts_recargos    += $rows["Pago_Recargos"];
                    $ts_seguros     += $rows["Pago_Seguro"];
                    $ts_honorarios  += $rows["Pago_Honorarios"];
                    $ts_otros       += $rows["Pago_Otros"];
                    $ts_gastos      += $rows["Pago_Gastos"];
                    $ts_costos      += $rows["Pago_Costos"];
                    $ts_saldofavor  += $rows["Pago_Saldos_Favor"];
                    $ts_total       += $total;
                } //end consulta
                #PAGO ANULADO
                $sql =" DECLARE @mes int = '" . $x . "'; 
                       DECLARE @year int = '" . $row['yr'] . "';
                       DECLARE @tipocredito int = '" . $tipocredito . "';
                SELECT  c.Id_Tipo_Credito,        
                (SELECT sum(dp.Valor_pago) FROM DETALLE_PAGO as dp 
                LEFT JOIN CREDITO c ON dp.Numero_Credito = c.Numero_Credito   
                WHERE  c.Id_Tipo_Credito =@tipocredito AND  dp.Numero_Recibo=pg.Numero_Recibo 
                and dp.Id_Concepto='2') as Pago_Capital, 
                (SELECT sum(dp.Valor_pago) FROM DETALLE_PAGO as dp
                LEFT JOIN CREDITO c ON dp.Numero_Credito = c.Numero_Credito   
                WHERE  c.Id_Tipo_Credito =@tipocredito AND dp.Numero_Recibo=pg.Numero_Recibo 
                and dp.Id_Concepto='3') as Pago_Interes, 
                (SELECT sum(dp.Valor_pago) FROM DETALLE_PAGO as dp  
                LEFT JOIN CREDITO c ON dp.Numero_Credito = c.Numero_Credito   
                WHERE  c.Id_Tipo_Credito =@tipocredito AND dp.Numero_Recibo=pg.Numero_Recibo 
                and dp.Id_Concepto='21') as Pago_Interes_Acuerdo, 
                (SELECT sum(dp.Valor_pago) FROM DETALLE_PAGO as dp  
                LEFT JOIN CREDITO c ON dp.Numero_Credito = c.Numero_Credito   
                WHERE  c.Id_Tipo_Credito =@tipocredito AND dp.Numero_Recibo=pg.Numero_Recibo 
                and dp.Id_Concepto='4') as Pago_Recargos,
                (SELECT sum(dp.Valor_pago) FROM DETALLE_PAGO as dp  
                LEFT JOIN CREDITO c ON dp.Numero_Credito = c.Numero_Credito   
                WHERE  c.Id_Tipo_Credito =@tipocredito AND dp.Numero_Recibo=pg.Numero_Recibo 
                and dp.Id_Concepto IN ('5', '22') ) as Pago_Seguro,
                (SELECT sum(dp.Valor_pago) FROM DETALLE_PAGO as dp  
                LEFT JOIN CREDITO c ON dp.Numero_Credito = c.Numero_Credito   
                WHERE  c.Id_Tipo_Credito =@tipocredito AND dp.Numero_Recibo=pg.Numero_Recibo 
                and dp.Id_Concepto='6') as Pago_Honorarios,  
                (select sum(dp.Valor_pago) FROM DETALLE_PAGO as dp  
                LEFT JOIN CREDITO c ON dp.Numero_Credito = c.Numero_Credito   
                WHERE  c.Id_Tipo_Credito =@tipocredito AND dp.Numero_Recibo=pg.Numero_Recibo
                and dp.Id_Concepto IN ('7', '8') ) as Pago_Otros, 
                (SELECT sum(dp.Valor_pago) FROM DETALLE_PAGO as dp  
                LEFT JOIN CREDITO c ON dp.Numero_Credito = c.Numero_Credito   
                where  c.Id_Tipo_Credito =@tipocredito AND dp.Numero_Recibo=pg.Numero_Recibo 
                and dp.Id_Concepto='18') as Pago_Gastos,
                (SELECT sum(dp.Valor_pago) FROM DETALLE_PAGO as dp 
                LEFT JOIN CREDITO c ON dp.Numero_Credito = c.Numero_Credito   
                where  c.Id_Tipo_Credito =@tipocredito AND dp.Numero_Recibo=pg.Numero_Recibo 
                and dp.Id_Concepto='19') as Pago_Costos,  
                (SELECT sum(dp.Valor_pago) FROM DETALLE_PAGO as dp  
                LEFT JOIN CREDITO c ON dp.Numero_Credito = c.Numero_Credito   
                WHERE  c.Id_Tipo_Credito =@tipocredito AND dp.Numero_Recibo=pg.Numero_Recibo 
                and dp.Id_Concepto='20') as Pago_Saldos_Favor 
                FROM PAGOS as pg   left join CREDITO as c on c.Numero_Credito=pg.Numero_Credito  
                where c.Id_Tipo_Credito =@tipocredito
                and (MONTH (pg.Fecha_Pago))=@mes  and (year (pg.Fecha_Pago))=@year  
                AND pg.Id_Estado_Pago IN ('8') 
                order by c.Id_Tipo_Credito asc";
                $stmtpm = sqlsrv_query($conn, $sql);
                while ($rows = sqlsrv_fetch_array($stmtpm, SQLSRV_FETCH_ASSOC)) {
                    $capital    = $rows["Pago_Capital"];
                    $interes    = $rows["Pago_Interes"];
                    $interesa   = $rows["Pago_Interes_Acuerdo"];
                    $recargos   = $rows["Pago_Recargos"];
                    $seguros    = $rows["Pago_Seguro"];
                    $honorarios = $rows["Pago_Honorarios"];
                    $otros      = $rows["Pago_Otros"];
                    $gastos     = $rows["Pago_Gastos"];
                    $costos     = $rows["Pago_Costos"];
                    $saldofavor = $rows["Pago_Saldos_Favor"];
                    $total      = ($capital + $interes + $interesa + $recargos + $seguros + $honorarios + $otros + $gastos + $costos + $saldofavor);

                    $ts_capital     -= $rows["Pago_Capital"];
                    $ts_interes     -= $rows["Pago_Interes"];
                    $ts_interesa    -= $rows["Pago_Interes_Acuerdo"];
                    $ts_recargos    -= $rows["Pago_Recargos"];
                    $ts_seguros     -= $rows["Pago_Seguro"];
                    $ts_honorarios  -= $rows["Pago_Honorarios"];
                    $ts_otros       -= $rows["Pago_Otros"];
                    $ts_gastos      -= $rows["Pago_Gastos"];
                    $ts_costos      -= $rows["Pago_Costos"];
                    $ts_saldofavor  -= $rows["Pago_Saldos_Favor"];
                    $ts_total       -= $total;
                } //end consulta
                $ts_capital1     += $ts_capital;
                $ts_interes1     += $ts_interes;
                $ts_interesa1    += $ts_interesa;
                $ts_recargos1    += $ts_recargos;
                $ts_seguros1     += $ts_seguros;
                $ts_honorarios1  += $ts_honorarios;
                $ts_otros1       += $ts_otros;
                $ts_gastos1      += $ts_gastos;
                $ts_costos1      += $ts_costos;
                $ts_saldofavor1  += $ts_saldofavor;
                $ts_total1       += $ts_total;
                $mes        = $x;
                $anio       = $row['yr'];
                 if ($mes == 1) {
                    $mes = "Enero";
                }
                if ($mes == 2) {
                    $mes = "Febrero";
                }
                if ($mes == 3) {
                    $mes = "Marzo";
                }
                if ($mes == 4) {
                    $mes = "Abril";
                }
                if ($mes == 5) {
                    $mes = "Mayo";
                }
                if ($mes == 6) {
                    $mes = "Junio";
                }
                if ($mes == 7) {
                    $mes = "Julio";
                }
                if ($mes == 8) {
                    $mes = "Agosto";
                }
                if ($mes == 9) {
                    $mes = "Septiembre";
                }
                if ($mes == 10) {
                    $mes = "Octubre";
                }
                if ($mes == 11) {
                    $mes = "Noviembre";
                }
                if ($mes == 12) {
                    $mes = "Diciembre";
                }
                $pdf->SetFont('Arial', '', 7);
                $pdf->Cell(16, 8, utf8_decode($mes), 1, 0, 'C');
                $pdf->Cell(13, 8, $anio, 1, 0, 'C');
                $pdf->Cell(21, 8, number_format($ts_capital, 2, '.', ','), 1, 0, 'R');
                $pdf->Cell(21, 8, number_format($ts_interes, 2, '.', ','), 1, 0, 'R');
                $pdf->Cell(21, 8, number_format($ts_interesa, 2, '.', ','), 1, 0, 'R');
                $pdf->Cell(21, 8, number_format($ts_recargos, 2, '.', ','), 1, 0, 'R');
                $pdf->Cell(21, 8, number_format($ts_seguros, 2, '.', ','), 1, 0, 'R');
                $pdf->Cell(21, 8, number_format($ts_honorarios, 2, '.', ','), 1, 0, 'R');
                $pdf->Cell(21, 8, number_format($ts_otros, 2, '.', ','), 1, 0, 'R');
                $pdf->Cell(21, 8, number_format($ts_gastos, 2, '.', ','), 1, 0, 'R');
                $pdf->Cell(21, 8, number_format($ts_costos, 2, '.', ','), 1, 0, 'R');
                $pdf->Cell(21, 8, number_format($ts_saldofavor, 2, '.', ','), 1, 0, 'R');
                $pdf->SetFont('Arial', 'B', 7);
                $pdf->Cell(25, 8, number_format($ts_total, 2, '.', ','), 1, 0, 'R');
                $pdf->Ln(8);

            } // end for
        } //end when

        $pdf->SetFont('Arial', 'B', 7);
        $pdf->Cell(29, 8, utf8_decode('TOTALES'), 1, 0, 'L');
        $pdf->Cell(21, 8, number_format($ts_capital1, 2, '.', ','), 1, 0, 'R');
        $pdf->Cell(21, 8, number_format($ts_interes1, 2, '.', ','), 1, 0, 'R');
        $pdf->Cell(21, 8, number_format($ts_interesa1, 2, '.', ','), 1, 0, 'R');
        $pdf->Cell(21, 8, number_format($ts_recargos1, 2, '.', ','), 1, 0, 'R');
        $pdf->Cell(21, 8, number_format($ts_seguros1, 2, '.', ','), 1, 0, 'R');
        $pdf->Cell(21, 8, number_format($ts_honorarios1, 2, '.', ','), 1, 0, 'R');
        $pdf->Cell(21, 8, number_format($ts_otros1, 2, '.', ','), 1, 0, 'R');
        $pdf->Cell(21, 8, number_format($ts_gastos1, 2, '.', ','), 1, 0, 'R');
        $pdf->Cell(21, 8, number_format($ts_costos1, 2, '.', ','), 1, 0, 'R');
        $pdf->Cell(21, 8, number_format($ts_saldofavor1, 2, '.', ','), 1, 0, 'R');
        $pdf->Cell(25, 8, number_format($ts_total1, 2, '.', ','), 1, 0, 'R');
        $pdf->Ln(8);
        $ts_capital1tt     += $ts_capital1;
        $ts_interes1tt     += $ts_interes1;
        $ts_interesa1tt    += $ts_interesa1;
        $ts_recargos1tt    += $ts_recargos1;
        $ts_seguros1tt     += $ts_seguros1;
        $ts_honorarios1tt  += $ts_honorarios1;
        $ts_otros1tt       += $ts_otros1;
        $ts_gastos1tt      += $ts_gastos1;
        $ts_costos1tt      += $ts_costos1;
        $ts_saldofavor1tt  += $ts_saldofavor1;
        $ts_total1tt       += $ts_total1;
    }  
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->Cell(29, 8, utf8_decode('TOTALES'), 1, 0, 'L');
    $pdf->Cell(21, 8, number_format($ts_capital1tt, 2, '.', ','), 1, 0, 'R');
    $pdf->Cell(21, 8, number_format($ts_interes1tt, 2, '.', ','), 1, 0, 'R');
    $pdf->Cell(21, 8, number_format($ts_interesa1tt, 2, '.', ','), 1, 0, 'R');
    $pdf->Cell(21, 8, number_format($ts_recargos1tt, 2, '.', ','), 1, 0, 'R');
    $pdf->Cell(21, 8, number_format($ts_seguros1tt, 2, '.', ','), 1, 0, 'R');
    $pdf->Cell(21, 8, number_format($ts_honorarios1tt, 2, '.', ','), 1, 0, 'R');
    $pdf->Cell(21, 8, number_format($ts_otros1tt, 2, '.', ','), 1, 0, 'R');
    $pdf->Cell(21, 8, number_format($ts_gastos1tt, 2, '.', ','), 1, 0, 'R');
    $pdf->Cell(21, 8, number_format($ts_costos1tt, 2, '.', ','), 1, 0, 'R');
    $pdf->Cell(21, 8, number_format($ts_saldofavor1tt, 2, '.', ','), 1, 0, 'R');
    $pdf->Cell(25, 8, number_format($ts_total1tt, 2, '.', ','), 1, 0, 'R');       
    $pdf->Ln(10);

}

ob_end_clean();
$pdf->Output(0, 'Proyección_Mensual_Tipo_Crédito.pdf', 0);
 
?>