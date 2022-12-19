<?php
require_once("../Conexion/ConexionPDO.php");
require_once("../Conexion/conexion.php");
require_once("../jsonPptal/funcionesPptal.php");
require_once('../Conexion/conexionsql.php');
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Proyección Mensual por Tipo Crédito.xls");

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
$ruta_logo = $rowC[0][6];
?>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>PROYECCIÓN MENSUAL POR TIPO CRÉDITO</title>
    </head>
    <body>
        <table width="100%" border="1" cellspacing="0" cellpadding="0">
            <th colspan="13" align="center">
                <strong><br/><?= $razonsocial ?>
            <?php
            if ($tipoinforme == 1) {
                    echo ' <br/>&nbsp;PROYECCIÓN MENSUAL POR TIPO CRÉDITO'?>           
                    <br/>&nbsp;
                    </strong>
                </th>
                <?php
                if ($tipocredito != "") {
                    $sqltc = "SELECT Identificador, Nombre_Tipo_Credito as Nombre from TIPO_CREDITO 
                                          WHERE Identificador = $tipocredito
                                          ORDER BY TIPO_CREDITO.Nombre_Tipo_Credito ASC";

                    $stmt = sqlsrv_query($conn, $sqltc);
                    $rowtc = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
                    $credito = $rowtc['Nombre'];
                    echo '<tr>';
                    echo '<td colspan="13" align="center">';
                    echo '<strong>' . 'TIPO CRÉDITO: ' . ucwords($credito) . '</strong></td>';
                    echo '</tr>';
                    echo '<tr>';
                    echo '<td><strong>MES</strong></td>';
                    echo '<td><strong>AÑO</strong></td>';
                    echo '<td><strong>CAPITAL</strong></td>';
                    echo '<td><strong>INTERÉS</strong></td>';
                    echo '<td><strong>INTERÉS ACUERDO</strong></td>';
                    echo '<td><strong>RECARGO</strong></td>';
                    echo '<td><strong>SEGURO</strong></td>';
                    echo '<td><strong>HONORARIOS</strong></td>';
                    echo '<td><strong>OTROS CONCEPTOS</strong></td>';
                    echo '<td><strong>GASTOS ADMINISTRATIVOS</strong></td>';
                    echo '<td><strong>COSTOS JURÍDICOS</strong></td>';
                    echo '<td><strong>SALDOS A FAVOR</strong></td>';
                    echo '<td><strong>TOTAL MES</strong></td>';
                    echo '</tr>';
                    $sqla = "SELECT DISTINCT year(d.Fecha_Posible_pago) as  yr
                                     FROM DETALLE_CREDITO as d 
                                     WHERE year(d.Fecha_Posible_pago) BETWEEN $anioini AND $aniofin
                                     order by year(d.Fecha_Posible_pago) desc";
                    $stmt = sqlsrv_query($conn, $sqla);

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
                            echo '<tr>';
                            echo '<td>' . utf8_decode($mes) . '</td>';
                            echo '<td>' . $anio . '</td>';
                            echo '<td>' . number_format($capital, 2, '.', ',') . '</td>';
                            echo '<td>' . number_format($interes, 2, '.', ',') . '</td>';
                            echo '<td>' . number_format($interesa, 2, '.', ',') . '</td>';
                            echo '<td>' . number_format($recargos, 2, '.', ',') . '</td>';
                            echo '<td>' . number_format($seguros, 2, '.', ',') . '</td>';
                            echo '<td>' . number_format($honorarios, 2, '.', ',') . '</td>';
                            echo '<td>' . number_format($otros, 2, '.', ',') . '</td>';
                            echo '<td>' . number_format($gastos, 2, '.', ',') . '</td>';
                            echo '<td>' . number_format($costos, 2, '.', ',') . '</td>';
                            echo '<td>' . number_format($costos, 2, '.', ',') . '</td>';
                            echo '<td>' . number_format($total, 2, '.', ',') . '</td>';
                            echo '</tr>';

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
                        } // end for
                        echo '<td>TOTALES</td>';
                        echo '<td></td>';
                        echo '<td>' . number_format($ts_capital, 2, '.', ',') . '</td>';
                        echo '<td>' . number_format($ts_interes, 2, '.', ',') . '</td>';
                        echo '<td>' . number_format($ts_interesa, 2, '.', ',') . '</td>';
                        echo '<td>' . number_format($ts_recargos, 2, '.', ',') . '</td>';
                        echo '<td>' . number_format($ts_seguros, 2, '.', ',') . '</td>';
                        echo '<td>' . number_format($ts_honorarios, 2, '.', ',') . '</td>';
                        echo '<td>' . number_format($ts_otros, 2, '.', ',') . '</td>';
                        echo '<td>' . number_format($ts_gastos, 2, '.', ',') . '</td>';
                        echo '<td>' . number_format($ts_costos, 2, '.', ',') . '</td>';
                        echo '<td>' . number_format($ts_costos, 2, '.', ',') . '</td>';
                        echo '<td>' . number_format($ts_total, 2, '.', ',') . '</td>';
                    } //end when
                } else { // sino se escoge tipo crédito  muetra todos los créditos     
                    $sqltc = "SELECT DISTINCT tc.Identificador, tc.Nombre_Tipo_credito 
                        FROM DETALLE_CREDITO DC
                                LEFT JOIN CREDITO C ON DC.Numero_Credito = C.Numero_Credito 
                                LEFT JOIN TIPO_CREDITO tc ON tc.Identificador = C.Id_Tipo_Credito 
                        WHERE year(DC.Fecha_Posible_pago) BETWEEN '$anioini' AND '$aniofin'
                        ORDER BY tc.Nombre_Tipo_credito";
                    $stmt = sqlsrv_query($conn, $sqltc);
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
                    while ($rows = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
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
                        echo '<tr>';
                        echo '<td colspan="13" align="center">';
                        echo '<strong>' . 'TIPO CRÉDITO: ' . ucwords($rows['Nombre_Tipo_credito']) . '</strong></td>';
                        echo '</tr>';
                        echo '<tr>';
                        echo '<td><strong>MES</strong></td>';
                        echo '<td><strong>AÑO</strong></td>';
                        echo '<td><strong>CAPITAL</strong></td>';
                        echo '<td><strong>INTERÉS</strong></td>';
                        echo '<td><strong>INTERÉS ACUERDO</strong></td>';
                        echo '<td><strong>RECARGO</strong></td>';
                        echo '<td><strong>SEGURO</strong></td>';
                        echo '<td><strong>HONORARIOS</strong></td>';
                        echo '<td><strong>OTROS CONCEPTOS</strong></td>';
                        echo '<td><strong>GASTOS ADMINISTRATIVOS</strong></td>';
                        echo '<td><strong>COSTOS JURÍDICOS</strong></td>';
                        echo '<td><strong>SALDOS A FAVOR</strong></td>';
                        echo '<td><strong>TOTAL MES</strong></td>';
                        echo '</tr>';
                        $identificador = $rows['Identificador'];
                        $sqla = "SELECT DISTINCT year(d.Fecha_Posible_pago) as  yr
                                         FROM DETALLE_CREDITO as d 
                                         WHERE year(d.Fecha_Posible_pago) BETWEEN $anioini AND $aniofin
                                         order by year(d.Fecha_Posible_pago) desc";
                        $stmta = sqlsrv_query($conn, $sqla);
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


                                echo '<tr>';
                                echo '<td>' . utf8_decode($mes) . '</td>';
                                echo '<td>' . $anio . '</td>';
                                echo '<td>' . number_format($capital, 2, '.', ',') . '</td>';
                                echo '<td>' . number_format($interes, 2, '.', ',') . '</td>';
                                echo '<td>' . number_format($interesa, 2, '.', ',') . '</td>';
                                echo '<td>' . number_format($recargos, 2, '.', ',') . '</td>';
                                echo '<td>' . number_format($seguros, 2, '.', ',') . '</td>';
                                echo '<td>' . number_format($honorarios, 2, '.', ',') . '</td>';
                                echo '<td>' . number_format($otros, 2, '.', ',') . '</td>';
                                echo '<td>' . number_format($gastos, 2, '.', ',') . '</td>';
                                echo '<td>' . number_format($costos, 2, '.', ',') . '</td>';
                                echo '<td>' . number_format($costos, 2, '.', ',') . '</td>';
                                echo '<td>' . number_format($total, 2, '.', ',') . '</td>';
                                echo '</tr>';
                            }
                        }
                        echo '<td>TOTALES</td>';
                        echo '<td></td>';
                        echo '<td>' . number_format($ts_capital, 2, '.', ',') . '</td>';
                        echo '<td>' . number_format($ts_interes, 2, '.', ',') . '</td>';
                        echo '<td>' . number_format($ts_interesa, 2, '.', ',') . '</td>';
                        echo '<td>' . number_format($ts_recargos, 2, '.', ',') . '</td>';
                        echo '<td>' . number_format($ts_seguros, 2, '.', ',') . '</td>';

                        echo '<td>' . number_format($ts_honorarios, 2, '.', ',') . '</td>';
                        echo '<td>' . number_format($ts_otros, 2, '.', ',') . '</td>';
                        echo '<td>' . number_format($ts_gastos, 2, '.', ',') . '</td>';
                        echo '<td>' . number_format($ts_costos, 2, '.', ',') . '</td>';
                        echo '<td>' . number_format($ts_costos, 2, '.', ',') . '</td>';
                        echo '<td>' . number_format($ts_total, 2, '.', ',') . '</td>';
                    }//end while tipos crédito                 
                } // end if tipo crédito

            } else if ($tipoinforme == 2)  {  //else tipoinforme
                echo ' <br/>&nbsp;CONSOLIDADO PROYECCIÓN MENSUAL'
                ?>
                <br/>&nbsp;
                </strong>
                </th>
                <?php
                echo '<tr>';
                echo '<td><strong>MES</strong></td>';
                echo '<td><strong>AÑO</strong></td>';
                echo '<td><strong>CAPITAL</strong></td>';
                echo '<td><strong>INTERÉS</strong></td>';
                echo '<td><strong>INTERÉS ACUERDO</strong></td>';
                echo '<td><strong>RECARGO</strong></td>';
                echo '<td><strong>SEGURO</strong></td>';
                echo '<td><strong>HONORARIOS</strong></td>';
                echo '<td><strong>OTROS CONCEPTOS</strong></td>';
                echo '<td><strong>GASTOS ADMINISTRATIVOS</strong></td>';
                echo '<td><strong>COSTOS JURÍDICOS</strong></td>';
                echo '<td><strong>SALDOS A FAVOR</strong></td>';
                echo '<td><strong>TOTAL MES</strong></td>';
                echo '</tr>';
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

                        echo '<tr>';
                        echo '<td>' . utf8_decode($mes) . '</td>';
                        echo '<td>' . $anio . '</td>';
                        echo '<td>' . number_format($ts_capital, 2, '.', ',') . '</td>';
                        echo '<td>' . number_format($ts_interes, 2, '.', ',') . '</td>';
                        echo '<td>' . number_format($ts_interesa, 2, '.', ',') . '</td>';
                        echo '<td>' . number_format($ts_recargos, 2, '.', ',') . '</td>';
                        echo '<td>' . number_format($ts_seguros, 2, '.', ',') . '</td>';

                        echo '<td>' . number_format($ts_honorarios, 2, '.', ',') . '</td>';
                        echo '<td>' . number_format($ts_otros, 2, '.', ',') . '</td>';
                        echo '<td>' . number_format($ts_gastos, 2, '.', ',') . '</td>';
                        echo '<td>' . number_format($ts_costos, 2, '.', ',') . '</td>';
                        echo '<td>' . number_format($ts_saldofavor, 2, '.', ',') . '</td>';
                        echo '<td>' . number_format($ts_total, 2, '.', ',') . '</td>';
                        echo '</tr>';
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
                    }
                    echo '<td>TOTALES</td>';
                    echo '<td></td>';
                    echo '<td>' . number_format($ts_capital1, 2, '.', ',') . '</td>';
                    echo '<td>' . number_format($ts_interes1, 2, '.', ',') . '</td>';
                    echo '<td>' . number_format($ts_interesa1, 2, '.', ',') . '</td>';
                    echo '<td>' . number_format($ts_recargos1, 2, '.', ',') . '</td>';
                    echo '<td>' . number_format($ts_seguros1, 2, '.', ',') . '</td>';

                    echo '<td>' . number_format($ts_honorarios1, 2, '.', ',') . '</td>';
                    echo '<td>' . number_format($ts_otros1, 2, '.', ',') . '</td>';
                    echo '<td>' . number_format($ts_gastos1, 2, '.', ',') . '</td>';
                    echo '<td>' . number_format($ts_costos1, 2, '.', ',') . '</td>';
                    echo '<td>' . number_format($ts_saldofavor1, 2, '.', ',') . '</td>';
                    echo '<td>' . number_format($ts_total1, 2, '.', ',') . '</td>';
                }

            }else if ($tipoinforme == 3) {
                 echo ' <br/>&nbsp;RECAUDO MENSUAL'
                ?>
                <br/>&nbsp;
                </strong>
                </th>
                <?php
                echo '<tr>';
                echo '<td><strong>MES</strong></td>';
                echo '<td><strong>AÑO</strong></td>';
                echo '<td><strong>CAPITAL</strong></td>';
                echo '<td><strong>INTERÉS</strong></td>';
                echo '<td><strong>INTERÉS ACUERDO</strong></td>';
                echo '<td><strong>RECARGO</strong></td>';
                echo '<td><strong>SEGURO</strong></td>';
                echo '<td><strong>HONORARIOS</strong></td>';
                echo '<td><strong>OTROS CONCEPTOS</strong></td>';
                echo '<td><strong>GASTOS ADMINISTRATIVOS</strong></td>';
                echo '<td><strong>COSTOS JURÍDICOS</strong></td>';
                echo '<td><strong>SALDOS A FAVOR</strong></td>';
                echo '<td><strong>TOTAL MES</strong></td>';
                echo '</tr>';
                      
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
                        #PAGO NORMAL
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
                        }//end while
                        
                        #PAGO ANULADO
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
                        }//end while
                        
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
                        echo '<tr>';
                        echo '<td>' . utf8_decode($mes) . '</td>';
                        echo '<td>' . $anio . '</td>';
                        echo '<td>' . number_format($ts_capital, 2, '.', ',') . '</td>';
                        echo '<td>' . number_format($ts_interes, 2, '.', ',') . '</td>';
                        echo '<td>' . number_format($ts_interesa, 2, '.', ',') . '</td>';
                        echo '<td>' . number_format($ts_recargos, 2, '.', ',') . '</td>';
                        echo '<td>' . number_format($ts_seguros, 2, '.', ',') . '</td>';
                        echo '<td>' . number_format($ts_honorarios, 2, '.', ',') . '</td>';
                        echo '<td>' . number_format($ts_otros, 2, '.', ',') . '</td>';
                        echo '<td>' . number_format($ts_gastos, 2, '.', ',') . '</td>';
                        echo '<td>' . number_format($ts_costos, 2, '.', ',') . '</td>';
                        echo '<td>' . number_format($ts_saldofavor, 2, '.', ',') . '</td>';
                        echo '<td>' . number_format($ts_total, 2, '.', ',') . '</td>';
                        echo '</tr>';
                    }
                }
                echo '<td colspan="2"><strong><i>TOTALES</td>';
                echo '<td><strong><i>' . number_format($ts_capital1, 2, '.', ',') . '</i></strong></td>';
                echo '<td><strong><i>' . number_format($ts_interes1, 2, '.', ',') . '</i></strong></td>';
                echo '<td><strong><i>' . number_format($ts_interesa1, 2, '.', ',') . '</i></strong></td>';
                echo '<td><strong><i>' . number_format($ts_recargos1, 2, '.', ',') . '</i></strong></td>';
                echo '<td><strong><i>' . number_format($ts_seguros1, 2, '.', ',') . '</i></strong></td>';
                echo '<td><strong><i>' . number_format($ts_honorarios1, 2, '.', ',') . '</i></strong></td>';
                echo '<td><strong><i>' . number_format($ts_otros1, 2, '.', ',') . '</i></strong></td>';
                echo '<td><strong><i>' . number_format($ts_gastos1, 2, '.', ',') . '</i></strong></td>';
                echo '<td><strong><i>' . number_format($ts_costos1, 2, '.', ',') . '</i></strong></td>';
                echo '<td><strong><i>' . number_format($ts_saldofavor1, 2, '.', ',') . '</i></strong></td>';
                echo '<td><strong><i>' . number_format($ts_total1, 2, '.', ',') . '</i></strong></td>';
                
            }else{
               echo ' <br/>&nbsp;RECAUDO MENSUAL POR TIPO CRÉDITO'?>           
                <br/>&nbsp;
                 </strong>
                </th>
                <?php
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
                    echo '<tr>';
                    echo '<td colspan="13" align="center">';
                    echo '<strong>' . 'TIPO CRÉDITO: ' . ucwords($credito) . '</strong></td>';
                    echo '</tr>';
                    echo '<tr>';
                    echo '<td><strong>MES</strong></td>';
                    echo '<td><strong>AÑO</strong></td>';
                    echo '<td><strong>CAPITAL</strong></td>';
                    echo '<td><strong>INTERÉS</strong></td>';
                    echo '<td><strong>INTERÉS ACUERDO</strong></td>';
                    echo '<td><strong>RECARGO</strong></td>';
                    echo '<td><strong>SEGURO</strong></td>';
                    echo '<td><strong>HONORARIOS</strong></td>';
                    echo '<td><strong>OTROS CONCEPTOS</strong></td>';
                    echo '<td><strong>GASTOS ADMINISTRATIVOS</strong></td>';
                    echo '<td><strong>COSTOS JURÍDICOS</strong></td>';
                    echo '<td><strong>SALDOS A FAVOR</strong></td>';
                    echo '<td><strong>TOTAL MES</strong></td>';
                    echo '</tr>';
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
                            }  //end while consulta
                            
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
                            }  //end while consulta
                            
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
                            echo '<tr>';
                            echo '<td>' . utf8_decode($mes) . '</td>';
                            echo '<td>' . $anio . '</td>';
                            echo '<td>' . number_format($ts_capital, 2, '.', ',') . '</td>';
                            echo '<td>' . number_format($ts_interes, 2, '.', ',') . '</td>';
                            echo '<td>' . number_format($ts_interesa, 2, '.', ',') . '</td>';
                            echo '<td>' . number_format($ts_recargos, 2, '.', ',') . '</td>';
                            echo '<td>' . number_format($ts_seguros, 2, '.', ',') . '</td>';
                            echo '<td>' . number_format($ts_honorarios, 2, '.', ',') . '</td>';
                            echo '<td>' . number_format($ts_otros, 2, '.', ',') . '</td>';
                            echo '<td>' . number_format($ts_gastos, 2, '.', ',') . '</td>';
                            echo '<td>' . number_format($ts_costos, 2, '.', ',') . '</td>';
                            echo '<td>' . number_format($ts_saldofavor, 2, '.', ',') . '</td>';
                            echo '<td>' . number_format($ts_total, 2, '.', ',') . '</td>';
                            echo '</tr>';
                        } // end for
                    } //end when
                    echo '<tr>';
                    echo '<td colspan="2"><strong><i>TOTALES</i></strong></td>';
                    echo '<td><strong><i>' . number_format($ts_capital1, 2, '.', ',') . '</i></strong></td>';
                    echo '<td><strong><i>' . number_format($ts_interes1, 2, '.', ',') . '</i></strong></td>';
                    echo '<td><strong><i>' . number_format($ts_interesa1, 2, '.', ',') . '</i></strong></td>';
                    echo '<td><strong><i>' . number_format($ts_recargos1, 2, '.', ',') . '</i></strong></td>';
                    echo '<td><strong><i>' . number_format($ts_seguros1, 2, '.', ',') . '</i></strong></td>';
                    echo '<td><strong><i>' . number_format($ts_honorarios1, 2, '.', ',') . '</i></strong></td>';
                    echo '<td><strong><i>' . number_format($ts_otros1, 2, '.', ',') . '</i></strong></td>';
                    echo '<td><strong><i>' . number_format($ts_gastos1, 2, '.', ',') . '</i></strong></td>';
                    echo '<td><strong><i>' . number_format($ts_costos1, 2, '.', ',') . '</i></strong></td>';
                    echo '<td><strong><i>' . number_format($ts_saldofavor1, 2, '.', ',') . '</i></strong></td>';
                    echo '<td><strong><i>' . number_format($ts_total1, 2, '.', ',') . '</i></strong></td>';
                    echo '</tr>';
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
                echo '<tr>';
                echo '<td colspan="2"><strong>TOTALES</strong></td>';
                echo '<td><strong>' . number_format($ts_capital1tt, 2, '.', ',').'</strong></td>';
                echo '<td><strong>' . number_format($ts_interes1tt, 2, '.', ',').'</strong></td>';
                echo '<td><strong>' . number_format($ts_interesa1tt, 2, '.', ',').'</strong></td>';
                echo '<td><strong>' . number_format($ts_recargos1tt, 2, '.', ',').'</strong></td>';
                echo '<td><strong>' . number_format($ts_seguros1tt, 2, '.', ',').'</strong></td>';
                echo '<td><strong>' . number_format($ts_honorarios1tt, 2, '.', ',').'</strong></td>';
                echo '<td><strong>' . number_format($ts_otros1tt, 2, '.', ',').'</strong></td>';
                echo '<td><strong>' . number_format($ts_gastos1tt, 2, '.', ',').'</strong></td>';
                echo '<td><strong>' . number_format($ts_costos1tt, 2, '.', ',').'</strong></td>';
                echo '<td><strong>' . number_format($ts_saldofavor1tt, 2, '.', ',').'</strong></td>';
                echo '<td><strong>' . number_format($ts_total1tt, 2, '.', ',').'</strong></td>';
                echo '</tr>';
            }

            ?>   
        </table>
    </body>
</html>   