<?php
/**
 * summary
 */
require_once ('./Conexion/db.php');
class detallemov{
    public $id_unico;
    public $cantidad;
    public $valor;
    public $iva;
    public $movimiento;
    public $detalleasociado;
    public $planmovimiento;

    private $mysqli;

    public function __construct(){
        $this->mysqli = conectar::conexion();
    }

    public function obtnerDatosDetalle($id_unico){
        try {
            $sql = "SELECT id_unico, planmovimiento, cantidad, valor, iva, movimiento FROM gf_detalle_movimiento WHERE id_unico = $id_unico";
            $res = $this->mysqli->query($sql);
            $row = $res->fetch_row();
            return $row;
            $this->mysqli->close();
        } catch (Exception $e) {
           die($e->getMessage());
        }
    }

    public function registrar(detallemov $data){
        try {
            $sql = "INSERT INTO gf_detalle_movimiento (
                                                        cantidad,
                                                        valor,
                                                        iva,
                                                        movimiento,
                                                        detalleasociado,
                                                        planmovimiento
                                                    )
                                                    VALUES (
                                                        $data->cantidad,
                                                        $data->valor,
                                                        $data->iva,
                                                        $data->movimiento,
                                                        $data->detalleasociado,
                                                        $data->planmovimiento
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

    public function ultimoRegistro($mov){
        try {
            $sql = "SELECT MAX(id_unico) FROM gf_detalle_movimiento WHERE movimiento = $mov";
            $res = $this->mysqli->query($sql);
            $row = $res->fetch_row();
            return $row;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function mov_pro($producto, $detalle){
        try {
            $sql = "INSERT INTO gf_movimiento_producto(producto, detallemovimiento) VALUES ($producto, $detalle)";
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

    public function modificar($cant, $iva, $detalle){
        try {
            $sql = "UPDATE gf_detalle_movimiento SET cantidad = $cant, iva = $iva WHERE id_unico = $detalle";
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
}