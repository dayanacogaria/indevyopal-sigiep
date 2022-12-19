<?php
require_once ('../Conexion/db.php');

class tipoOperacion{
    public $id_unico;
    public $nombre;

    private $mysqli;

    public function __construct(){
        $this->mysqli = conectar::conexion();
    }

    public function registrar(tipoOperacion $data){
        try {
            $sql = "INSERT INTO gp_tipo_operacion (nombre)
                    VALUES('$data->nombre')";
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

    public function modificar(tipoOperacion $data){
        try {
            $sql = "UPDATE gp_tipo_operacion
                    SET    nombre   = '$data->nombre'
                    WHERE  id_unico = $data->id_unico";
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
            $sql = "DELETE FROM gp_tipo_operacion WHERE id_unico = $id_unico";
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