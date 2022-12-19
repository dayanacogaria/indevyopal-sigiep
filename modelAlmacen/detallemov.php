<?php
require_once ('../Conexion/db.php');
class detallemov{

    public $id_unico;
    public $cantidad;
    public $valor;
    public $iva;
    public $movimiento;
    public $planMovimiento;

    private $mysqli;

    public function __construct(){
        $this->mysqli = conectar::conexion();
    }

    public function buscar_plan_mov($codigo, $compania){
        try {
            $id  = 0;
            $sql = "SELECT id_unico FROM gf_plan_inventario WHERE codi = '$codigo' AND compania = $compania ";
            $res = $this->mysqli->query($sql);
            if($res->num_rows > 0){
                $id  = $res->fetch_row();
            }
            return $id;
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function validar_ficha($id_unico){
        try {
            $sql = "SELECT ficha FROM gf_plan_inventario WHERE id_unico = $id_unico ";
            $res = $this->mysqli->query($sql);

            $row = $res->fetch_row();

            if(empty($row[0])){
                $rest = 0;
            }else{
                $rest = 1;
            }

            return $rest;
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function buscarficha(){
        try {
            $sql = "SELECT id_unico FROM gf_ficha WHERE descripcion = \"GENERAL\" ";
            $res = $this->mysqli->query($sql);
            $id  = $res->fetch_row();
            return $id;
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function asignar_ficha($id_unico, $ficha){
        try {
            $sql = "UPDATE gf_plan_inventario SET ficha = $ficha WHERE id_unico = $id_unico";
            $res = $this->mysqli->query($sql);

            if($res == true){
                $rest = 1;
            }else{
                $rest = 0;
            }

            return $rest;
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function registrar(detallemov $data){
        try {
            $sql = "INSERT INTO gf_detalle_movimiento(
                                    cantidad,
                                    valor,
                                    iva,
                                    movimiento,
                                    planmovimiento, 
                                    vida_util
                                )VALUES(
                                    $data->cantidad,
                                    $data->valor,
                                    $data->iva,
                                    $data->movimiento,
                                    $data->planmovimiento,
                                    $data->vida_util 
                                )";
            $res = $this->mysqli->query($sql);

            if($res == true){
                $rest = 1;
            }else{
                $rest = 0;
            }

            return $rest;
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function buscar_detalle($mov){
        try {
            $xxx = 0;
            if(!empty($mov)){
                $sql = "SELECT MAX(id_unico) FROM gf_detalle_movimiento WHERE movimiento = $mov";
                $res = $this->mysqli->query($sql);
                if($res->num_rows > 0){
                    $row = $res->fetch_row();
                    $xxx = $row[0];
                }
            }
            return $xxx;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function registrar_aso(detallemov $data){
        $sql = "INSERT INTO gf_detalle_movimiento(
                                cantidad,
                                valor,
                                iva,
                                movimiento,
                                planmovimiento,
                                detalleasociado
                            )VALUES(
                                $data->cantidad,
                                $data->valor,
                                $data->iva,
                                $data->movimiento,
                                $data->planmovimiento,
                                $data->detalleasociado
                            )";
        $res = $this->mysqli->query($sql);

        if($res == true){
            $rest = 1;
        }else{
            $rest = 0;
        }

        return $rest;
        $this->mysqli->close();
    }

    public function buscar_tipo_inventario($id_unico){
        try {
            $xxx = 0;
            $sql = "SELECT tipoinventario FROM gf_plan_inventario WHERE id_unico = $id_unico";
            $res = $this->mysqli->query($sql);
            if($res->num_rows > 0){
                $row = $res->fetch_row();
                $xxx = $row[0];
            }
            return $xxx;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
}