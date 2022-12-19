<?php
ini_set('max_execution_time', 0);
session_start();
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=RelacionEntreComprobantesFacturacion.xls");
require_once ('../modelFactura/facturaC.php');
require ('../Conexion/conexion.php');
if(!empty($_POST['txtFechaInicial']) && !empty($_POST['txtFechaFinal'])){
    $factura = new factura();
    $i = explode("/", $_POST['txtFechaInicial']);
    $f = explode("/", $_POST['txtFechaFinal']);
    $fecha_incial = "$i[2]-$i[1]-$i[0]";
    $fecha_final  = "$f[2]-$f[1]-$f[0]";

    $facturas = $factura->obtnerFacturas($fecha_incial, $fecha_final);

    $usuario = $_SESSION['usuario'];
    $compa   = $_SESSION['compania'];

    $comp = $factura->obtnerCompania($compa);
    $nombreCompania = $comp[0];
    $ruta = $comp[2];
    if(empty($comp[3])) {
        $nitCompania = $comp[1];
    } else {
        $nitCompania = $comp[1].' - '.$comp[3];
    }

    function valorDetallesFactura($id_unico){
        try {
            require ('../Conexion/conexion.php');
            $xxx = 0;
            $sql = "SELECT valor FROM gp_detalle_factura WHERE factura = $id_unico";
            $res = $mysqli->query($sql);
            while($row = mysqli_fetch_row($res)){
                $xxx += $row[0];
            }
            return $xxx;
            $mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    function obtnerComprobantesCntP($id_unico){
        try {
            require ('../Conexion/conexion.php');
            $sql = "SELECT      cnt.id_unico as cnt, ptal.id_unico as ptal
                    FROM        gp_factura pg, gp_tipo_factura tpg, gf_tipo_comprobante tpc,gf_comprobante_cnt cnt, gf_tipo_comprobante_pptal tcp,gf_comprobante_pptal ptal
                    WHERE       pg.tipofactura        = tpg.id_unico
                    AND         tpc.id_unico          = tpg.tipo_comprobante
                    AND         cnt.tipocomprobante   = tpc.id_unico
                    AND         tpc.comprobante_pptal = tcp.id_unico
                    AND         ptal.tipocomprobante  = tcp.id_unico
                    AND         pg.numero_factura     = ptal.numero
                    AND         pg.numero_factura     = cnt.numero
                    AND         pg.id_unico           =  $id_unico";
            $res = $mysqli->query($sql);
            $row = mysqli_fetch_row($res);
            return $row;
            $mysqli->close();
        } catch (Exception $e) {
            $e->getMessage();
        }
    }

    function obtnerValoresDetalleCnt($id_unico){
        try {
            require ('../Conexion/conexion.php');
            $xxx = 0;
            $sql = "SELECT SUM(valor) FROM gf_detalle_comprobante WHERE comprobante = $id_unico AND naturaleza = 2";
            $res = $mysqli->query($sql);
            $row = mysqli_fetch_row($res);
            $xxx += $row[0];
            return $xxx;
            $mysqli->close();
        } catch(Exception $e){
            die($e->getMessage());
        }
    }

    function obtnerValoresPptal($id_unico){
        try {
            $xxx = 0;
            require ('../Conexion/conexion.php');
            $sql = "SELECT SUM(valor) FROM gf_detalle_comprobante_pptal WHERE comprobantepptal = $id_unico";
            $res = $mysqli->query($sql);
            $row = mysqli_fetch_row($res);
            $xxx += $row[0];
            return $xxx;
            $mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    function obtnerComprobantesDesdeDetalles($id_unico){
        try {
            require ('../Conexion/conexion.php');
            $sql = "SELECT    dtc.comprobante, dtp.comprobantepptal
                    FROM      gp_detalle_factura           dtf
                    LEFT JOIN gf_detalle_comprobante       dtc ON dtc.id_unico = dtf.detallecomprobante
                    LEFT JOIN gf_detalle_comprobante_pptal dtp ON dtp.id_unico = dtc.detallecomprobantepptal
                    WHERE     (dtf.factura = $id_unico)
                    AND       (dtc.detallecomprobantepptal IS NOT NULL)";
            $res = $mysqli->query($sql);
            $row = mysqli_fetch_row($res);
            return $row[0];
            $mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    function obtnerValorTotalFacturIva($id_unico){
        try {
            require ('../Conexion/conexion.php');
            $xxx = 0;
            $sql = "SELECT (valor * cantidad) + iva FROM gp_detalle_factura WHERE factura = $id_unico";
            $res = $mysqli->query($sql);
            while($row = mysqli_fetch_row($res)){
                $xxx += $row[0];
            }
            return $xxx;
            $mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    $html = "";
    $html .= "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">";
    $html .= "<html xmlns= \"http://www.w3.org/1999/xhtml\">";
    $html .= "<head>";
    $html .= "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />";
    $html .= "<title>RELACIÓN DE FACTURACION ENTRE COMPROBANTES</title>";
    $html .= "</head>";
    $html .= "<body>";
    $html .= "<table width=\"100%\" border=\"1\" cellspacing=\"0\" cellpadding=\"0\">";
    $html .= "<thead>";
    $html .= "<tr>";
    $html .= "<th colspan=\"7\" align=\"center\">".$nombreCompania."NIT: $nitCompania<br>RELACIÓN DE FACTURACION ENTRE COMPROBANTES</th>";
    $html .= "</tr>";
    $html .= "<tr>";
    $html .= "<th>TIPO</th>";
    $html .= "<th>NÚMERO</th>";
    $html .= "<th>TERCERO</th>";
    $html .= "<th>VALOR FACTURA</th>";
    $html .= "<th>VALOR TOTAL FACTURA</th>";
    $html .= "<th>VALOR CONTABLE</th>";
    $html .= "<th>VALOR PRESUPUESTO</th>";
    $html .= "</t>";
    $html .= "</thead>";
    $html .= "<tbody>";
    for ($i = 0; $i < count($facturas); $i++){
        $prefijo  = ""; $numero   = "";
        $nombt    = ""; $data     = explode("," , $facturas[$i]);
        $id_tipo  = $data[0]; $id_unico = $data[1];
        $numero   = $data[2]; $tercero  = $data[3];
        $sqlt = "SELECT prefijo, tipo_comprobante FROM gp_tipo_factura WHERE id_unico = $id_tipo";
        $rest = $mysqli->query($sqlt);
        $rowt = mysqli_fetch_row($rest);
        $prefijo = $rowt[0]; $tipo_co = $rowt[1];
        $sqlr = "SELECT   UPPER(IF(CONCAT_WS(' ',ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) IS NULL OR
                             CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos)='',
                             (ter.razonsocial),
                             CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos)
                          )) AS 'NOMBRE'
                 FROM     gf_tercero ter
                 WHERE    ter.id_unico = $tercero";
        $resr = $mysqli->query($sqlr);
        $rowr = mysqli_fetch_row($resr);
        $nombt = $rowr[0];
        if(!empty($tipo_co)){
            $valorF = 0; $valorT = 0;
            $valorF = $factura->valorDetallesFactura($id_unico);
            $valorT = $factura->obtnerValorTotalFacturIva($id_unico);
            $dataID = $factura->obtnerComprobantesCntP($id_unico);
            $id_cnt = $dataID[0]; $id_pptal = $dataID[1];
            if(!empty($id_cnt)){
                $valorC = 0; $valorP = 0;
                $valorC = $factura->obtnerValoresDetalleCnt($id_cnt);
                $valorP = $factura->obtnerValoresPptal($id_pptal);
                $html .= "<tr>";
                $html .= "<td>$prefijo</td>";
                $html .= "<td>$numero</td>";
                $html .= "<td>$nombt</td>";
                $html .= "<td align=\"right\">".number_format($valorF,2,'.',',')."</td>";
                $html .= "<td align=\"right\">".number_format($valorT,2,'.',',')."</td>";
                $html .= "<td align=\"right\">".number_format($valorC,2,'.',',')."</td>";
                $html .= "<td align=\"right\">".number_format($valorP,2,'.',',')."</td>";
                $html .= "</tr>";
            }else{
                $dataID = $factura->obtnerComprobantesDesdeDetalles($id_unico);
                $id_cnt = $dataID[0]; $id_pptal = $dataID[1];
                if(!empty($id_cnt)){
                    $valorC = 0; $valorP = 0;
                    $valorC = $factura->obtnerValoresDetalleCnt($id_cnt);
                    $valorP = $factura->obtnerValoresPptal($id_pptal);
                    $html .= "<tr>";
                    $html .= "<td>$prefijo</td>";
                    $html .= "<td>$numero</td>";
                    $html .= "<td>$nombt</td>";
                    $html .= "<td align=\"right\">".number_format($valorF,2,'.',',')."</td>";
                    $html .= "<td align=\"right\">".number_format($valorT,2,'.',',')."</td>";
                    $html .= "<td align=\"right\">".number_format($valorC,2,'.',',')."</td>";
                    $html .= "<td align=\"right\">".number_format($valorP,2,'.',',')."</td>";
                    $html .= "</tr>";
                }
            }
        }
    }
    $html .= "</tbody>";
    $html .= "</table>";
    $html .= "</body>";
    $html .= "</html>";

    echo $html;
}