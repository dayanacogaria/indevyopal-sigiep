<?php
require_once ('./Conexion/db.php');
/**
 * Modelo de detalle presupuestal
 */
class detallePptal{
    public $id_unico;
    public $descripcion;
    public $comprobantepptal;
    public $rubrofuente;
    public $conceptorubro;
    public $tercero;
    public $proyecto;
    public $valor;
    private $mysqli;

    public function getIdUnico(){
        return $this->id_unico;
    }

    public function setIdUnico($id_unico){
        $this->id_unico = $id_unico;
    }

    public function getDescripcion(){
        return $this->descripcion;
    }

    public function setDescripcion($descripcion){
        $this->descripcion = $descripcion;
    }

    public function getComprobantepptal(){
        return $this->comprobantepptal;
    }

    public function setComprobantepptal($comprobantepptal){
        $this->comprobantepptal = $comprobantepptal;
    }

    public function getRubrofuente(){
        return $this->rubrofuente;
    }

    public function setRubrofuente($rubrofuente){
        $this->rubrofuente = $rubrofuente;
    }

    public function getConceptorubro(){
        return $this->conceptorubro;
    }

    public function setConceptorubro($conceptorubro){
        $this->conceptorubro = $conceptorubro;
    }

    public function getTercero(){
        return $this->tercero;
    }

    public function setTercero($tercero){
        $this->tercero = $tercero;
    }

    public function getProyecto(){
        return $this->proyecto;
    }

    public function setProyecto($proyecto){
        $this->proyecto = $proyecto;
    }

    public function getValor(){
        return $this->valor;
    }

    public function setValor($valor){
        $this->valor = $valor;
    }

    public function __construct(){
        $this->mysqli = conectar::conexion();
    }

    public function registrar(detallePptal $data){
        try {
            $sql = "INSERT INTO gf_detalle_comprobante_pptal(
                                    descripcion,
                                    valor,
                                    comprobantepptal,
                                    rubrofuente,
                                    conceptorubro,
                                    tercero,
                                    proyecto
                                ) VALUES(
                                    '$data->descripcion',
                                    $data->valor,
                                    $data->comprobantepptal,
                                    $data->rubrofuente,
                                    $data->conceptorubro,
                                    $data->tercero,
                                    $data->proyecto
                                )";
            return $this->mysqli->query($sql);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerUltimoRegistroPptal($comprobante){
        try {
            $id  = 0;
            $sql = "SELECT MAX(id_unico) FROM gf_detalle_comprobante_pptal WHERE comprobantepptal = $comprobante";
            $res = $this->mysqli->query($sql);
            if($res->num_rows > 0){
                $row = mysqli_fetch_row($res);
                $id  = $row[0];
            }
            return $id;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function modificar($id_unico, $valor){
        try {
            $sql = "UPDATE gf_detalle_comprobante_pptal SET valor = $valor WHERE id_unico = $id_unico";
            return $this->mysqli->query($sql);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function eliminar($id_unico){
        try {
            $sql = "DELETE FROM gf_detalle_comprobante_pptal WHERE id_unico = $id_unico";
            return $this->mysqli->query($sql);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function eliminarDetalleComprobante($id_unico){
        try {
            $sql = "DELETE FROM gf_detalle_comprobante_pptal WHERE comprobantepptal  = $id_unico";
            return $this->mysqli->query($sql);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function guardar($descripcion, $valor, $comprobantepptal, $rubrofuente, $conceptorubro, $tercero, $proyecto){
        try {
            $str = "INSERT INTO gf_detalle_comprobante_pptal( descripcion, valor, comprobantepptal, rubrofuente, conceptorubro, tercero, proyecto ) 
                    VALUES( '$descripcion', $valor, $comprobantepptal, $rubrofuente, $conceptorubro, $tercero, $proyecto )";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtenerUltimoDetalle($pto){
        try {
            $xxx = 0;
            $str = "SELECT MAX(id_unico) FROM gf_detalle_comprobante_pptal WHERE comprobantepptal = $pto";
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
}