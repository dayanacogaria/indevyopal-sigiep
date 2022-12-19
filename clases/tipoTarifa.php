<?php
/**
 * Created by PhpStorm.
 * User: SERVIDOR
 * Date: 29/05/2018
 * Time: 12:22
 */
require_once ('./Conexion/db.php');
class tipoTarifa{
    private $mysqli;
    public $id_unico;
    public $nombre;

    public function __construct(){
        $this->mysqli = conectar::conexion();
    }

    public function getIdUnico(){
        return $this->id_unico;
    }

    public function setIdUnico($id_unico){
        $this->id_unico = $id_unico;
    }

    public function getNombre(){
        return $this->nombre;
    }

    public function setNombre($nombre){
        $this->nombre = $nombre;
    }

    public function registrar(tipoTarifa $data){
        try {
            $str = "INSERT INTO gp_tipo_tarifa(nombre) VALUES ('$data->nombre')";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function modificar(tipoTarifa $data){
        try {
            $str = "UPDATE gp_tipo_tarifa SET nombre = '$data->nombre' WHERE id_unico = $data->id_unico";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function eliminar($id){
        try {
            $str = "DELETE FROM gp_tipo_tarifa WHERE id_unico = $id";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtener($id){
        try {
            $str = "SELECT id_unico, nombre FROM gp_tipo_tarifa WHERE md5(id_unico) = '$id'";
            $res = $this->mysqli->query($str);
            return $res->fetch_row();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerTodos(){
        try {
            $str = "SELECT id_unico, nombre FROM gp_tipo_tarifa ORDER BY nombre ASC";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}