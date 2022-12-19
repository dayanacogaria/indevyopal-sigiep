<?php
require_once ('../Conexion/db.php');

class conceptoTarifa{
    public $id_unico;
    public $nombre;
    public $concepto;
    public $tarifa;

    private $mysqli;

    public function __construct(){
        $this->mysqli = conectar::conexion();
    }

    public function registrar(conceptoTarifa $data){
        try {
            $sql = "INSERT INTO gp_concepto_tarfia(
                                    nombre,
                                    concepto,
                                    tarifa
                                ) VALUES(
                                    '$data->nombre',
                                    $data->concepto,
                                    $data->tarifa
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

    public function modificar(conceptoTarifa $data){
        try {
            $sql = "UPDATE gp_concepto_tarfia SET
                           nombre   = '$data->nombre',
                           concepto = $data->concepto,
                           tarifa   = $data->tarifa
                    WHERE  id_unico = $data->id_unico";
            $res = $mysqli->query($sql);

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
            $sql = "DELETE FROM gp_concepto_tarfia WHERE id_unico = $id_unico";
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