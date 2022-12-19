<?php
/**
 * Created by PhpStorm.
 * User: SERVIDOR
 * Date: 26/04/2018
 * Time: 9:57
 */
require_once ('./Conexion/db.php');

class concepto_tarifa{

    public $id_unico;
    public $nombre;
    public $concepto;
    public $tarifa;
    public $elemento_unidad;
    public $porcentajeI;

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

    public function getNombre(){
        return $this->nombre;
    }

    public function setNombre($nombre){
        $this->nombre = $nombre;
    }

    public function getConcepto(){
        return $this->concepto;
    }

    public function setConcepto($concepto){
        $this->concepto = $concepto;
    }

    public function getTarifa(){
        return $this->tarifa;
    }

    public function setTarifa($tarifa){
        $this->tarifa = $tarifa;
    }

    public function getElementoUnidad(){
        return $this->elemento_unidad;
    }

    public function setElementoUnidad($elemento_unidad){
        $this->elemento_unidad = $elemento_unidad;
    }

    public function getPorcentajeI(){
        return $this->porcentajeI;
    }

    public function setPorcentajeI($porcentajeI){
        $this->porcentajeI = $porcentajeI;
    }

    public function guardar(){
        try{
            $str = "INSERT INTO gp_concepto_tarifa(nombre, concepto, tarifa, elemento_unidad, porcentajeI) 
                    VALUES ('NULL', $this->concepto, $this->tarifa, $this->elemento_unidad, $this->porcentajeI)";
            return $this->mysqli->query($str);
        }catch (Exception $e){
            die($e->getMessage());
        }
    }

    public function eliminar(){
        try{
            $str = "DELETE FROM gp_concepto_tarifa WHERE tarifa = $this->tarifa AND concepto = $this->concepto";
            return $this->mysqli->query($str);
        }catch (Exception $e){
            die($e->getMessage());
        }
    }

    public function registrarUnidadE($unidad, $valor_conversion){
        try {
            $str = "INSERT INTO gf_elemento_unidad(unidad_empaque, valor_conversion) VALUES($unidad, $valor_conversion)";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerUltimoElementoUnidad(){
        try {
            $xxx = 0;
            $str = "SELECT MAX(id_unico) FROM gf_elemento_unidad";
            $res = $this->mysqli->query($str);
            if($res->num_rows > 0){
                $row = $res->fetch_row();
                $xxx = $row[0];
            }
            return $xxx;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function modificarPorcentaje($concepto, $tarifa, $elemento, $porcentaje){
        try {
            $str = "UPDATE gp_concepto_tarifa 
                    SET    porcentajeI     = $porcentaje 
                    WHERE  concepto        = $concepto 
                    AND    tarifa          = $tarifa 
                    AND    elemento_unidad = $elemento";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}