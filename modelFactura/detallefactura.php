<?php
require_once ('./Conexion/db.php');
/**
 * Modelo de detalle factura
 */
class detalleFactura{
    public $id_unico;
    public $factura;
    public $concepto_tarifa;
    public $valor;
    public $cantidad;
    public $iva;
    public $impoconsumo;
    public $ajuste_peso;
    public $valor_total_ajustado;
    public $detallecomprobante;
    public $detallemovimiento;
    public $detalleafectado;
    public $descuento;
    public $unidad_origen;

    private $mysqli;

    public function getIdUnico(){
        return $this->id_unico;
    }

    public function setIdUnico($id_unico){
        $this->id_unico = $id_unico;
    }

    public function getFactura(){
        return $this->factura;
    }

    public function setFactura($factura){
        $this->factura = $factura;
    }

    public function getConceptoTarifa(){
        return $this->concepto_tarifa;
    }

    public function setConceptoTarifa($concepto_tarifa){
        $this->concepto_tarifa = $concepto_tarifa;
    }

    public function getValor(){
        return $this->valor;
    }

    public function setValor($valor){
        $this->valor = $valor;
    }

    public function getCantidad(){
        return $this->cantidad;
    }

    public function setCantidad($cantidad){
        $this->cantidad = $cantidad;
    }

    public function getIva(){
        return $this->iva;
    }

    public function setIva($iva){
        $this->iva = $iva;
    }

    public function getImpoconsumo(){
        return $this->impoconsumo;
    }

    public function setImpoconsumo($impoconsumo){
        $this->impoconsumo = $impoconsumo;
    }

    public function getAjustePeso(){
        return $this->ajuste_peso;
    }

    public function setAjustePeso($ajuste_peso){
        $this->ajuste_peso = $ajuste_peso;
    }

    public function getValorTotalAjustado(){
        return $this->valor_total_ajustado;
    }

    public function setValorTotalAjustado($valor_total_ajustado){
        $this->valor_total_ajustado = $valor_total_ajustado;
    }

    public function getDetallecomprobante(){
        return $this->detallecomprobante;
    }

    public function setDetallecomprobante($detallecomprobante){
        $this->detallecomprobante = $detallecomprobante;
    }

    public function getDetallemovimiento(){
        return $this->detallemovimiento;
    }

    public function setDetallemovimiento($detallemovimiento){
        $this->detallemovimiento = $detallemovimiento;
    }

    public function getDetalleafectado(){
        return $this->detalleafectado;
    }

    public function setDetalleafectado($detalleafectado){
        $this->detalleafectado = $detalleafectado;
    }

    public function getDescuento(){
        return $this->descuento;
    }

    public function setDescuento($descuento){
        $this->descuento = $descuento;
    }

    public function __construct(){
        $this->mysqli = conectar::conexion();
    }

    public function registrar(detalleFactura $data){
        try {
            $sql = "INSERT INTO gp_detalle_factura(
                                    factura,
                                    concepto_tarifa,
                                    valor,
                                    cantidad,
                                    iva,
                                    impoconsumo,
                                    ajuste_peso,
                                    valor_total_ajustado,
                                    detallecomprobante,
                                    detallemovimiento
                                ) VALUES (
                                    $data->factura,
                                    $data->concepto_tarifa,
                                    $data->valor,
                                    $data->cantidad,
                                    $data->iva,
                                    $data->impoconsumo,
                                    $data->ajuste_peso,
                                    $data->valor_total_ajustado,
                                    $data->detallecomprobante,
                                    $data->detallemovimiento
                                )";
            $res = $this->mysqli->query($sql);

            if($res == true){
                $rest = true;
            }else{
                $rest = false;
            }

            return $rest;
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerUltimoId($factura){
        try {
            $id  = 0;
            $sql = "SELECT MAX(id_unico) FROM gp_detalle_factura WHERE factura = $factura";
            $res = $this->mysqli->query($sql);
            if($res->num_rows > 0){
                $row = mysqli_fetch_row($res);
                $id  = $row[0];
            }
            return $id;
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function modificar(detalleFactura $data){
        try {
            $sql = "UPDATE gp_detalle_factura
                    SET    concepto_tarifa      = $data->concepto_tarifa,
                           unidad_origen        = $data->unidad_origen,
                           cantidad             = $data->cantidad,
                           valor                = $data->valor,
                           iva                  = $data->iva,
                           impoconsumo          = $data->impoconsumo,
                           ajuste_peso          = $data->ajuste_peso,
                           valor_total_ajustado = $data->valor_total_ajustado
                    WHERE  id_unico             = $data->id_unico";
            $res = $this->mysqli->query($sql);
            if($res == true){
                $rest = true;
            }else{
                $rest = false;
            }
            return $rest;
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function eliminar($id_unico){
        try {
            $sql = "DELETE FROM gp_detalle_factura WHERE id_unico = $id_unico";
            $res = $this->mysqli->query($sql);

            if($res = true){
                $rest = true;
            }else{
                $rest = false;
            }

            return $rest;
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function desplazmientoDetallesCompleto($id_unico){
        try {
            $sql = "SELECT     dtc.detallecomprobantepptal  AS 'detalle_pp',
                               dtf.detallecomprobante       AS 'cuenta_debito',
                               dta.id_unico                 AS 'cuenta_credito',
                               dti.id_unico                 AS 'cuenta_iva_1',
                               dtv.id_unico                 AS 'cuenta_iva_2',
                               dtm.id_unico                 AS 'cuenta_impo_1',
                               dto.id_unico                 AS 'cuenta_impo_2'
                    FROM       gp_detalle_factura dtf
                    LEFT JOIN  gf_detalle_comprobante dtc ON dtf.detallecomprobante = dtc.id_unico
                    LEFT JOIN  gf_detalle_comprobante dta ON dta.detalleafectado    = dtc.id_unico
                    LEFT JOIN  gf_detalle_comprobante dti ON dti.detalleafectado    = dta.id_unico
                    LEFT JOIN  gf_detalle_comprobante dtv ON dtv.detalleafectado    = dti.id_unico
                    LEFT JOIN  gf_detalle_comprobante dtm ON dtm.detalleafectado    = dtv.id_unico
                    LEFT JOIN  gf_detalle_comprobante dto ON dto.detalleafectado    = dtm.id_unico
                    WHERE      dtf.id_unico = $id_unico";
            $res = $this->mysqli->query($sql);
            $row = mysqli_fetch_row($res);
            return $row;
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function desplazmientoDetallesMinimo($id_unico){
        try {
            $sql = "SELECT    dtc.detallecomprobantepptal AS 'detalle_pp',
                              dtf.detallecomprobante      AS 'cuenta_debito',
                              dta.id_unico                AS 'cuenta_credito'
                    FROM      gp_detalle_factura dtf
                    LEFT JOIN gf_detalle_comprobante dtc ON dtf.detallecomprobante = dtc.id_unico
                    LEFT JOIN gf_detalle_comprobante dta ON dta.detalleafectado    = dtc.id_unico
                    WHERE     dtf.id_unico = $id_unico";
            $res = $this->mysqli->query($sql);
            $row = mysqli_fetch_row($res);
            return $row;
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerDetalleContable($id_unico){
        try {
            $id  = 0;
            $sql = "SELECT detallecomprobante FROM gp_detalle_factura WHERE id_unico = $id_unico";
            $res = $this->mysqli->query($sql);
            if($res->num_rows > 0){
                $row = mysqli_fetch_row($res);
                $id  = $row[0];
            }
            return $id;
            $this->mysqli->close();

        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function eliminarDetallesFactura($id_unico){
        try {
            $sql = "DELETE FROM gp_detalle_factura WHERE factura = $id_unico";
            $res = $this->mysqli->query($sql);

            if($res == true){
                $rest = true;
            }else{
                $rest = false;
            }

            return $rest;
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerListados($factura){
        try {
            $sql = "SELECT    dtf.id_unico,
                              cnp.id_unico,
                              cnp.nombre,
                              dtf.cantidad,
                              dtf.valor,
                              dtf.iva,
                              dtf.impoconsumo,
                              dtf.ajuste_peso,
                              fat.numero_factura,
                              dtf.valor_total_ajustado,
                              dtf.detallemovimiento,
                              uf.id_unico,
                              uf.nombre, dtf.descripcion 
                    FROM      gp_detalle_factura dtf
                    LEFT JOIN gp_factura fat  ON fat.id_unico = dtf.factura
                    LEFT JOIN gp_concepto cnp ON cnp.id_unico = dtf.concepto_tarifa
                    LEFT JOIN   gf_unidad_factor uf ON dtf.unidad_origen = uf.id_unico
                    WHERE     dtf.factura = $factura";
            $res = $this->mysqli->query($sql);
            return $res;
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerDetallesFactura($factura){
        try {
            $sql = "SELECT    dtf.id_unico,
                              cnp.id_unico,
                              dtf.cantidad,
                              dtf.valor,
                              dtf.iva,
                              dtf.impoconsumo,
                              dtf.ajuste_peso,
                              fat.numero_factura,
                              dtf.valor_total_ajustado
                    FROM      gp_detalle_factura dtf
                    LEFT JOIN gp_factura fat  ON fat.id_unico = dtf.factura
                    LEFT JOIN gp_concepto cnp ON cnp.id_unico = dtf.concepto_tarifa
                    WHERE     dtf.factura = $factura";
            $res = $this->mysqli->query($sql);
            $row = mysqli_fetch_all($res, MYSQLI_NUM);
            return $row;
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerConceptoF($id_unico){
        try {
            $id_ = 0;
            $sql = "SELECT concepto_financiero FROM gp_concepto WHERE id_unico = $id_unico";
            $res = $this->mysqli->query($sql);
            if($res->num_rows > 0){
                $row = mysqli_fetch_row($res);
                $id_ = $row[0];
            }
            return $id_;
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerConceptoRB($concepto){
        try {
            $sql = "SELECT id_unico, rubro FROM gf_concepto_rubro WHERE concepto = $concepto";
            $res = $this->mysqli->query($sql);
            if($res->num_rows > 0){
                $row = mysqli_fetch_row($res);
            }
            return $row;
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerRubroFuente($rubro){
        try {
            $id_ = 0;
            $sql = "SELECT id_unico FROM gf_rubro_fuente WHERE rubro = $rubro";
            $res = $this->mysqli->query($sql);
            if($res->num_rows > 0){
                $row = mysqli_fetch_row($res);
                $id_ = $row[0];
            }
            return $id_;
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerConfigRubroCuenta($concepto_rubro){
        try {
             $sql = "SELECT cuenta_debito,
                           cuenta_credito,
                           cuenta_iva,
                           cuenta_impoconsumo
                    FROM   gf_concepto_rubro_cuenta
                    WHERE  concepto_rubro = $concepto_rubro";
            $res = $this->mysqli->query($sql);
            $row = mysqli_fetch_row($res);
            return $row;
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function destruirRelacionDetalles($factura){
        try {
            $sql = "UPDATE  gp_detalle_factura
                    SET     detallecomprobante = NULL
                    WHERE   factura            = $factura";
            $res = $this->mysqli->query($sql);

            if($res == true){
                $rest = true;
            }else{
                $rest = false;
            }

            return $rest;
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function relacionarDetalleComprobante($id_unico, $detalle){
        try {
            $sql = "UPDATE  gp_detalle_factura
                    SET     detallecomprobante = $detalle
                    WHERE   id_unico           = $id_unico";
            $res = $this->mysqli->query($sql);

            if($res == true){
                $rest = true;
            }else{
                $rest = false;
            }

            return $rest;
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerDataBusquedaPtal($idCnt, $idPptal){
        $str = "select    dtp.id_unico,dtc.id_unico
                from      gf_detalle_comprobante dtc
                left join gf_detalle_comprobante_pptal dtp on dtc.detallecomprobantepptal = dtp.id_unico
                where     dtc.comprobante      = $idCnt
                and       dtp.comprobantepptal = $idPptal";
        $res = $this->mysqli->query($str);
        return $res->num_rows;
    }

    public function registrarAso(detalleFactura $data){
        try {
            $str = "INSERT INTO gp_detalle_factura(
                                    factura,
                                    concepto_tarifa,
                                    valor,
                                    cantidad,
                                    iva,
                                    impoconsumo,
                                    ajuste_peso,
                                    valor_total_ajustado,
                                    detallecomprobante,
                                    detallemovimiento,
                                    detalleafectado
                                ) VALUES (
                                    $data->factura,
                                    $data->concepto_tarifa,
                                    $data->valor,
                                    $data->cantidad,
                                    $data->iva,
                                    $data->impoconsumo,
                                    $data->ajuste_peso,
                                    $data->valor_total_ajustado,
                                    $data->detallecomprobante,
                                    $data->detallemovimiento,
                                    $data->detalleafectado
                                )";
            $res = $this->mysqli->query($str);
            return $res;

        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtenerDataAfectado($id){
        try {
            $str = "SELECT valor_total_ajustado FROM gp_detalle_factura WHERE detalleafectado = $id";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function registrarDetalle($factura, $concepto, $valor, $cant, $iva, $impo, $ajuste, $valorT, $dtc, $dtm, $dta){
        try {
            $str = "INSERT INTO gp_detalle_factura( factura, concepto_tarifa, valor, cantidad, iva, impoconsumo,
                          ajuste_peso, valor_total_ajustado, detallecomprobante, detallemovimiento, detalleafectado)
                          VALUES ( $factura, $concepto, $valor, $cant, $iva, $impo, $ajuste, $valorT, $dtc, $dtm, $dta)";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function contarDetallesFactura($factura){
        try {
            $xxx = 0;
            $str = "SELECT COUNT(*) FROM gp_detalle_factura WHERE factura = $factura";
            $res = $this->mysqli->query($str);
            if($res->num_rows > 0){
                $row = $res->fetch_row();
                $xxx = $row[0];
            }
            return $xxx;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function registrarData($factura, $concepto, $valor, $cant, $iva, $impo, $ajuste, $valorT, $dtc, $dtm, $dta, $descuento, $unidad, $descripcion){
        try {
             $str = "INSERT INTO gp_detalle_factura( factura, concepto_tarifa, valor, cantidad, iva, impoconsumo,
                          ajuste_peso, valor_total_ajustado, detallecomprobante, detallemovimiento, detalleafectado, descuento,
                          unidad_origen, descripcion)
                          VALUES ( $factura, $concepto, $valor, $cant, $iva, $impo, $ajuste, $valorT, $dtc, $dtm, $dta, $descuento, $unidad, '$descripcion')";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function actualizarIdDetalleMov($id, $detalle){
        try {
            $str = "UPDATE gp_detalle_factura SET detallemovimiento = $detalle WHERE id_unico = $id";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerData($id){
        try {
            $str = "SELECT    gdf.concepto_tarifa, gdf.cantidad, gdf.iva, gdf.valor, gdf.impoconsumo, gdm.valor,
                              gdf.unidad_origen, gdm.valor_origen, gdm.descuento, gdm.planmovimiento, gdf.valor_total_ajustado,
                              gdf.unidad_origen, gdf.valor_total_ajustado, gdf.descripcion 
                    FROM      gp_detalle_factura     AS gdf
                    LEFT JOIN gf_detalle_movimiento  AS gdm ON gdf.detallemovimiento  = gdm.id_unico
                    LEFT JOIN gf_detalle_comprobante AS gdc ON gdf.detallecomprobante = gdc.id_unico
                    WHERE     gdf.id_unico = $id";
            $res = $this->mysqli->query($str);
            return $res->fetch_row();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}