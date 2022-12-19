<?php
require_once ('./Conexion/db.php');
/**
 * Modelo de Salida de almacén
 */
class salida{

    private $mysqli;

    public function __construct(){
        $this->mysqli = conectar::conexion();
    }

    public function obtnerElementosInventario(){
        try {
            $sql = "SELECT DISTINCT pln.id_unico, CONCAT_WS(' - ',pln.codi, UPPER(pln.nombre))
                    FROM            gf_plan_inventario     pln
                    LEFT JOIN       gf_detalle_movimiento  dtm ON dtm.planmovimiento    = pln.id_unico
                    LEFT JOIN       gf_movimiento_producto mpr ON mpr.detallemovimiento = dtm.id_unico
                    LEFT JOIN       gf_producto            pro ON mpr.producto          = pro.id_unico
                    LEFT JOIN       gf_movimiento          mov ON dtm.movimiento        = mov.id_unico
                    LEFT JOIN       gf_tipo_movimiento     tpm ON mov.tipomovimiento    = tpm.id_unico
                    WHERE           (pro.baja IS NULL OR pro.baja = 0)
                    AND             (tpm.clase = 2)
                    ORDER BY        pln.codi ASC";
            $res = $this->mysqli->query($sql);
            $row = $res->fetch_all(MYSQLI_NUM);
            return $row;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerCantidadProductosPlan($id){
        try {
            $xxx = 0;
            $ti   = "SELECT tipoinventario FROM gf_plan_inventario where id_unico = $id";
            $resr = $this->mysqli->query($ti);
            $rowr = $resr->fetch_row();
            if($rowr[0]==5){
                $xxx =1000; 
            } else { 

                $sql = "SELECT    dtm.cantidad
                        FROM      gf_detalle_movimiento  dtm
                        LEFT JOIN gf_plan_inventario     pln ON dtm.planmovimiento    = pln.id_unico
                        LEFT JOIN gf_movimiento_producto mpr ON mpr.detallemovimiento = dtm.id_unico
                        LEFT JOIN gf_producto            pro ON mpr.producto          = pro.id_unico
                        LEFT JOIN gf_movimiento          mov ON dtm.movimiento        = mov.id_unico
                        LEFT JOIN gf_tipo_movimiento     tpm ON mov.tipomovimiento    = tpm.id_unico
                        WHERE (pln.id_unico = $id)
                        AND   (pro.baja IS NULL OR pro.baja = 0)
                        AND   (tpm.clase = 2)";
                $res = $this->mysqli->query($sql);
                $row = $res->fetch_all(MYSQLI_NUM);
                foreach ($row as $row) {
                    $xxx += $row[0];
                }
            }
            return $xxx;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerCantidadProductosPlanSalida($id){
        try {
            $xxx = 0;
            $ti   = "SELECT tipoinventario FROM gf_plan_inventario where id_unico = $id";
            $resr = $this->mysqli->query($ti);
            $rowr = $resr->fetch_row();
            if($rowr[0]==5){
                $xxx = 0;
            } else { 
                $sql = "SELECT    dtm.cantidad
                        FROM      gf_detalle_movimiento  dtm
                        LEFT JOIN gf_plan_inventario     pln ON dtm.planmovimiento    = pln.id_unico
                        LEFT JOIN gf_movimiento_producto mpr ON mpr.detallemovimiento = dtm.id_unico
                        LEFT JOIN gf_producto            pro ON mpr.producto          = pro.id_unico
                        LEFT JOIN gf_movimiento          mov ON dtm.movimiento        = mov.id_unico
                        LEFT JOIN gf_tipo_movimiento     tpm ON mov.tipomovimiento    = tpm.id_unico
                        WHERE (pln.id_unico = $id)
                        AND   (pro.baja IS NULL OR pro.baja = 0)
                        AND   (tpm.clase = 3)";
                $res = $this->mysqli->query($sql);
                $row = $res->fetch_all(MYSQLI_NUM);
                foreach ($row as $row) {
                    $xxx += $row[0];
                }
            }
            return $xxx;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerValorProductoPlan($id){
        try {
            $xxx = 0;
            $sql = "SELECT    dtm.valor
                    FROM      gf_detalle_movimiento  dtm
                    LEFT JOIN gf_plan_inventario     pln ON dtm.planmovimiento    = pln.id_unico
                    LEFT JOIN gf_movimiento_producto mpr ON mpr.detallemovimiento = dtm.id_unico
                    LEFT JOIN gf_producto            pro ON mpr.producto          = pro.id_unico
                    LEFT JOIN gf_movimiento          mov ON dtm.movimiento        = mov.id_unico
                    LEFT JOIN gf_tipo_movimiento     tpm ON mov.tipomovimiento    = tpm.id_unico
                    WHERE (pln.id_unico = $id)
                    AND   (pro.baja IS NULL OR pro.baja = 0)
                    AND   (tpm.clase = 2)";
            $res = $this->mysqli->query($sql);
            $row = $res->fetch_row();
            $xxx = $row[0];
            return $xxx;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function buscarDetallesRelacionadosPlan($id){
        try {
            $sql = "SELECT DISTINCT dtm.id_unico, dtm.cantidad
                    FROM            gf_detalle_movimiento  dtm
                    LEFT JOIN       gf_plan_inventario     pln ON dtm.planmovimiento    = pln.id_unico
                    LEFT JOIN       gf_movimiento_producto mpr ON mpr.detallemovimiento = dtm.id_unico
                    LEFT JOIN       gf_producto            pro ON mpr.producto          = pro.id_unico
                    LEFT JOIN       gf_movimiento          mov ON dtm.movimiento        = mov.id_unico
                    LEFT JOIN       gf_tipo_movimiento     tpm ON mov.tipomovimiento    = tpm.id_unico
                    WHERE           (pln.id_unico = $id)
                    AND             (pro.baja IS NULL OR pro.baja = 0)
                    AND             (tpm.clase = 2)";
            $res = $this->mysqli->query($sql);
            $row = $res->fetch_all(MYSQLI_NUM);
            return $row;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function buscarCantidadSalidaDetalleAsociado($id){
        try {
            $sql = "SELECT DISTINCT dtm.id_unico, dtm.cantidad
                    FROM            gf_detalle_movimiento dtm
                    LEFT JOIN       gf_plan_inventario pln ON dtm.planmovimiento = pln.id_unico
                    LEFT JOIN       gf_movimiento_producto mpr ON mpr.detallemovimiento = dtm.id_unico
                    LEFT JOIN       gf_producto pro ON mpr.producto = pro.id_unico
                    LEFT JOIN       gf_movimiento mov ON dtm.movimiento = mov.id_unico
                    LEFT JOIN       gf_tipo_movimiento tpm ON mov.tipomovimiento = tpm.id_unico
                    WHERE           (dtm.detalleasociado = $id)
                    AND             (pro.baja IS NULL OR pro.baja = 0)
                    AND             (tpm.clase = 3)";
            $res = $this->mysqli->query($sql);
            $row = $res->fetch_all(MYSQLI_NUM);
            return $row;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function guardarDetalleSalida($cantidad, $valor, $iva, $movimiento, $detalleasociado, $planmovimiento){
        try {
            $sql = "INSERT INTO gf_detalle_movimiento(cantidad, valor, iva, movimiento, detalleasociado, planmovimiento) VALUES($cantidad, $valor, $iva, $movimiento, $detalleasociado, $planmovimiento)";
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

    public function buscarDatosSalida($id_unico){
        try {
            $sql = "SELECT id_unico FROM gf_movimiento WHERE md5(id_unico) = '$id_unico'";
            $res = $this->mysqli->query($sql);
            $row = $res->fetch_row();
            return $row;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
}