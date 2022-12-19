<?php
require_once('./Conexion/db.php');

class recaudoFactura{

    private $mysqli;

    public function __construct(){
        $this->mysqli = conectar::conexion();
    }

    public function destruirRelacionPago($id_pago){
        try {
            $sql = "UPDATE gp_detalle_pago SET detallecomprobante = NULL WHERE pago = $id_pago";
            $res = $this->mysqli->query($sql);

            if($res == true){
                $rest = true;
            }else{
                $rest = false;
            }

            return $rest;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function eliminarDetalleContabilidad($id_unico){
        try {
            $sql = "DELETE FROM gf_detalle_comprobante WHERE comprobante = $id_unico";
            $res = $this->mysqli->query($sql);

            if($res == true){
                $rest = true;
            }else{
                $rest = false;
            }

            return $rest;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtenerDatosPago($id_pago){
        try {
            $sql = "SELECT id_unico, detalle_factura, valor, iva, impoconsumo FROM gp_detalle_pago WHERE pago = $id_pago";
            $res = $this->mysqli->query($sql);
            $row = $res->fetch_all(MYSQLI_NUM);
            return $row;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerDatosPresupuesto($id_pptal){
        try {
            $sql = "SELECT * FROM gf_detalle_comprobante_pptal WHERE comprobantepptal = $id_pptal";
            $res = $this->mysqli->query($sql);
            $row = $res->fetch_all(MYSQLI_NUM);
            return $row;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerBanco($id_pago){
        try {
            $sql = "SELECT banco FROM gp_pago WHERE id_unico = $id_pago";
            $res = $this->mysqli->query($sql);
            $row = $res->fetch_row();
            return $row;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerCuentaBanco($cuenta){
        try {
            $sql = "SELECT cuenta FROM gf_cuenta_bancaria WHERE id_unico = $cuenta";
            $res = $this->mysqli->query($sql);
            $row = $res->fetch_row();
            return $row;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerDatosDetalleFactura($id_unico){
        try {
            $sql = "SELECT detallecomprobante FROM gp_detalle_factura WHERE id_unico = $id_unico";
            $res = $this->mysqli->query($sql);
            $row = $res->fetch_row();
            return $row;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function datosDetalleCnt($id_unico){
        try {
            $sql = "SELECT cuenta FROM gf_detalle_comprobante WHERE id_unico = $id_unico";
            $res = $this->mysqli->query($sql);
            $row = $res->fetch_row();
            return $row;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function destruirRelacionAfectadoContable($id_unico){
        try {
            $sql = "UPDATE gf_detalle_comprobante SET detalleafectado = NULL WHERE comprobante = $id_unico";
            $res = $this->mysqli->query($sql);

            if($res == true){
                $rest = true;
            }else{
                $rest = false;
            }

            return $rest;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtenerUltimoDetalle($id_unico){
        try {
            $sql = "SELECT MAX(id_unico) FROM gf_detalle_comprobante WHERE comprobante = $id_unico";
            $res = $this->mysqli->query($sql);
            $row = $res->fetch_row();
            return $row;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function actualizarRelacionPagoContablidad($id_detalle_pago, $id_detalle_cnt){
        try {
            $sql = "UPDATE gp_detalle_pago SET detallecomprobante = $id_detalle_cnt WHERE id_unico = $id_detalle_pago";
            $res = $this->mysqli->query($sql);

            if($res == true){
                $rest = true;
            }else{
                $rest = false;
            }
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obntnerTerceroPago($id_unico){
        try {
            $sql = "SELECT responsable FROM gp_pago WHERE id_unico = $id_unico";
            $res = $this->mysqli->query($sql);
            $row = $res->fetch_row();
            return $row;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obnterIdPtal($valor, $comprobante){
        try {
            $sql = "SELECT id_unico FROM gf_detalle_comprobante_pptal WHERE comprobantepptal = $comprobante AND valor = $valor";
            $res = $this->mysqli->query($sql);
            $row = $res->fetch_row();
            return $row;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function guardarDatosDetalle($valor, $comprobante, $cuenta, $nat, $tercero){
        try {
            $fecha = date('Y-m-d');
            $sql = "INSERT INTO gf_detalle_comprobante(
                                    fecha,
                                    valor,
                                    comprobante,
                                    cuenta,
                                    naturaleza,
                                    tercero,
                                    proyecto,
                                    centrocosto
                               )VALUES(
                                    '$fecha',
                                    $valor,
                                    $comprobante,
                                    $cuenta,
                                    $nat,
                                    $tercero,
                                    2147483647,
                                    12
                               )
                               ";

            $res = $this->mysqli->query($sql);
            if($res == true){
                $rest = true;
            }else{
                $rest = false;
            }
            return $rest;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obnterDatosCuenta($id_unico){
        try {
            $sql = "SELECT naturaleza FROM gf_cuenta WHERE id_unico = $id_unico";
            $res = $this->mysqli->query($sql);
            $row = $res->fetch_row();
            return $row;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
}