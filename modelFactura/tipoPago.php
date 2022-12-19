<?php
require_once('../Conexion/db.php');

class tipoPago{
    public $id_unico;
    public $nombre;
    public $tipo_comprobante;

    private $mysqli;

    public function __construct(){
        $this->mysqli = conectar::conexion();
    }

    public function registrar(tipoPago $data){
        try {
            $sql = "INSERT INTO gp_tipo_pago(
                                    nombre,
                                    tipo_comprobante
                                ) VALUES(
                                    '$data->nombre',
                                    $data->tipo_comprobante
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

    public function modificar(tipoPago $data){
        try {
            $sql = "UPDATE SET gp_tipo_pago
                    SET   nombre           = '$data->nombre',
                          tipo_comprobante = $data->tipo_comprobante
                    WHERE id_unico         = $data->id_unico";
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
            $sql = "DELETE FROM gp_tipo_pago WHERE id_unico = $id_unico";
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