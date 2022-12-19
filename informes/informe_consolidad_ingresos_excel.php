<?php
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Informe_Comprobantes_Consolidado.xls");
require_once("../Conexion/conexion.php");
session_start();
echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">";
echo "<html xmlns=\"http://www.w3.org/1999/xhtml\">";
echo "<head>";
echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />";
echo "<title>Listado de comprobante por tipo de comprobante</title>";
echo "</head>";
echo "<body>";
echo "<table width=\"100%\" border=\"1\" cellspacing=\"0\" cellpadding=\"0\">";
echo "<tr>";
echo "<td colspan=\"6\" bgcolor=\"skyblue\"><CENTER><strong>LISTADO DE COMPROBANTES CONSOLIDADO</strong></CENTER></td>";
echo "</tr>";

if(!empty($_REQUEST['sltTipoComprobanteInicial']) && !empty($_REQUEST['sltTipoComprobanteFinal'])){
    list($tipoI, $tipoF, $fechaI, $fechaF) = array($_REQUEST['sltTipoComprobanteInicial'], $_REQUEST['sltTipoComprobanteFinal'], $_REQUEST['txtFechaInicial'], $_REQUEST['txtFechaFinal']);

    $fechaI = explode("/", $fechaI); $fechaI = "$fechaI[2]-$fechaI[1]-$fechaI[0]";
    $fechaF = explode("/", $fechaF); $fechaF = "$fechaF[2]-$fechaF[1]-$fechaF[0]";

    $sql = "SELECT DISTINCT tpc.id_unico,tpc.nombre,tpc.sigla
            FROM            gf_tipo_comprobante tpc
            LEFT JOIN       gf_comprobante_cnt     cnt  ON cnt.tipocomprobante = tpc.id_unico
            LEFT JOIN       gf_detalle_comprobante dtc  ON dtc.comprobante = cnt.id_unico
            WHERE           tpc.id_unico BETWEEN $tipoI    AND $tipoF
            AND             cnt.fecha    BETWEEN '$fechaI' AND '$fechaF'
            ORDER BY        cnt.fecha   ASC";
    $res = $mysqli->query($sql);

    while($row = $res->fetch_row()){
        echo "<tr>";
        echo "<td colspan=\"6\" border=\"1\"><strong>".strtoupper($row[1])." ".$row[2].": </strong></td>";
        echo "</tr>";
        echo "<tr>";
        echo "<td><strong>No</strong></td>";
        echo "<td><strong>FECHA</strong></td>";
        echo "<td><strong>TERCERO</strong></td>";
        echo "<td><strong>VALOR CONTABILIDAD</strong></td>";
        echo "<td><strong>VALOR PRESUPUESTO</strong></td>";
        echo "<td><strong>DIFERENCIA</strong></td>";
        echo "</tr>";
        $sql_t = "  SELECT DISTINCT     cnt.id_unico,
                                        cnt.numero,
                                        date_format(cnt.fecha,'%d/%m/%Y'),
                                        IF( CONCAT( IF(ter.nombreuno='','',ter.nombreuno),' ',
                                                    IF(ter.nombredos IS NULL,'',ter.nombredos),' ',
                                                    IF(ter.apellidouno IS NULL,'',
                                                    IF(ter.apellidouno IS NULL,'',ter.apellidouno)),' ',
                                                    IF(ter.apellidodos IS NULL,'',ter.apellidodos))=''
                                        OR  CONCAT( IF(ter.nombreuno='','',ter.nombreuno),' ',
                                                    IF(ter.nombredos IS NULL,'',ter.nombredos),' ',
                                                    IF(ter.apellidouno IS NULL,'',
                                                    IF(ter.apellidouno IS NULL,'',ter.apellidouno)),' ',
                                                    IF(ter.apellidodos IS NULL,'',ter.apellidodos)) IS NULL ,
                                        (ter.razonsocial),
                                            CONCAT( IF(ter.nombreuno='','',ter.nombreuno),' ',
                                                    IF(ter.nombredos IS NULL,'',ter.nombredos),' ',
                                                    IF(ter.apellidouno IS NULL,'',
                                                    IF(ter.apellidouno IS NULL,'',ter.apellidouno)),' ',
                                                    IF(ter.apellidodos IS NULL,'',ter.apellidodos))) AS 'NOMBRE',
                                        cnt.descripcion,
                                        tpc.comprobante_pptal
                    FROM                gf_comprobante_cnt          cnt
                    LEFT JOIN           gf_tercero                  ter  ON cnt.tercero                 = ter.id_unico
                    LEFT JOIN           gf_detalle_comprobante      dtc  ON dtc.comprobante             = cnt.id_unico
                    LEFT JOIN           gf_tipo_comprobante         tpc  ON cnt.tipocomprobante         = tpc.id_unico
                    WHERE               cnt.tipocomprobante = $row[0]
                    AND                 cnt.fecha >= '$fechaI' AND cnt.fecha <= '$fechaF'
                    AND                 dtc.valor IS NOT NULL
                    ORDER BY            cnt.numero ASC
                ";
            $res_t = $mysqli->query($sql_t);
            while ($rowD = $res_t->fetch_row()) {
                list ($vCnt, $vPtl, $vTot) = array(0, 0, 0);

                $sumar=0; $sumaT=0; $valorD = 0; $valorC =0;
                $sqlR = "SELECT DISTINCT cnt.naturaleza, dtc.valor , dtc.id_unico
                        FROM            gf_detalle_comprobante dtc
                        LEFT JOIN       gf_cuenta cnt ON dtc.cuenta = cnt.id_unico
                        WHERE           dtc.comprobante = $rowD[0]";
                $resR = $mysqli->query($sqlR);
                while($row = mysqli_fetch_row($resR)){
                    if($row[0] == 1){
                        if($row[1] >= 0){
                            $sumar += $row[1];
                        }
                    }else if($row[0] == 2){
                        if($row[1] <= 0){
                            $x = (float) substr($row[1],'1');
                            $sumar += $x;
                        }
                    }

                    if ($row[0] == 2) {
                        if($row[1] >= 0){
                            $sumaT += $row[1];
                        }
                    }else if($row[0] == 1){
                        if($row[1] <= 0){
                            $x = (float) substr($row[1],'1');
                            $sumaT += $x;
                        }
                    }
                }
                $valorD = $sumar;
                $vCnt  = $valorD;

                $sql_p = "SELECT id_unico FROM gf_comprobante_pptal WHERE numero = $rowD[1] AND tipocomprobante = $rowD[5]";
                $res_p = $mysqli->query($sql_p);
                $rowPt = $res_p->fetch_row();

                $sql_d = "SELECT valor FROM gf_detalle_comprobante_pptal WHERE comprobantepptal = $rowPt[0]";
                $res_d = $mysqli->query($sql_d);
                while($row_d = mysqli_fetch_row($res_d)){
                    $vPtl += $row_d[0];
                }

                list($numero, $fecha, $tercero) = array(
                    $rowD[1],
                    $rowD[2],
                    ucwords(mb_strtoupper($rowD[3]))
                );

                $vTot = $vCnt - $vPtl;

                echo "<tr>";
                echo "<td>".$numero."</td>";
                echo "<td>".$fecha."</td>";
                echo "<td>".$tercero."</td>";
                echo "<td>".number_format($vCnt, 2, ',', '.')."</td>";
                echo "<td>".number_format($vPtl, 2, ',', '.')."</td>";
                echo "<td>".number_format($vTot, 2, ',', '.')."</td>";
                echo "</tr>";

            }
    }
}
echo "</table>";
echo "</body>";
echo "<html>";