<?php
/**
 * Created by PhpStorm.
 * User: SERVIDOR
 * Date: 08/06/2018
 * Time: 10:18
 */
require_once './Conexion/db.php';
class caracteristica{

    public $id_unico;
    public $tipo_dato;
    public $nombre;
    public $valor;
    public $espacio;
    private $mysqli;

    public function __construct(){
        $this->mysqli = conectar::conexion();
    }

    public function getIdUnico(){
        return $this->id_unico;
    }

    public function setIdUnico($id_unico){
        $this->id_unico = $id_unico;
    }

    public function getTipoDato(){
        return $this->tipo_dato;
    }

    public function setTipoDato($tipo_dato){
        $this->tipo_dato = $tipo_dato;
    }

    public function getNombre(){
        return $this->nombre;
    }

    public function setNombre($nombre){
        $this->nombre = $nombre;
    }

    public function getValor(){
        return $this->valor;
    }

    public function setValor($valor){
        $this->valor = $valor;
    }

    public function getEspacio(){
        return $this->espacio;
    }

    public function setEspacio($espacio){
        $this->espacio = $espacio;
    }

    public function registrar(caracteristica $data){
        try {
            $str = "INSERT INTO gh_espacio_caracteristica (tipo_dato, nombre, valor, espacio) VALUES ($data->tipo_dato, '$data->nombre', '$data->valor', $data->espacio)";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerCaracteristicasObjeto($espacio){
        try {
            $str = "SELECT id_unico, nombre, valor FROM gh_espacio_caracteristica WHERE md5(espacio) = '$espacio'";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function modificarCaracteristica($id, $valor){
        try {
            $str = "UPDATE gh_caracteristica_espacio SET valor = '$valor' WHERE id_unico = $id";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function eliminarCaracteristica($id){
        try {
            $str = "DELETE FROM gh_espacio_caracteristica WHERE id_unico = $id";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}