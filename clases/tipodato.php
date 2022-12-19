<?php
/**
 * Created by PhpStorm.
 * User: SERVIDOR
 * Date: 08/06/2018
 * Time: 8:23
 */
require_once './Conexion/db.php';
class tipodato{

    public $id_unico;
    public $nombre;
    private $mysqli;

    public function __construct(){
        $this->mysqli = conectar::conexion();
    }

    public function obtenerListado(){
        try {
            $str = "SELECT id_unico, nombre FROM gf_tipo_dato ORDER BY nombre ASC";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

}