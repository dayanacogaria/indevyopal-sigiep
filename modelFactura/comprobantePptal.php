<?php
require_once ('./Conexion/db.php');

/**
 * Model de comprobante pptal
 */
class comprobantePptal{
    public $id_unico;
    public $numero;
    public $fecha;
    public $fechavencimiento;
    public $descripcion;
    public $parametrizacionanno;
    public $tipocomprobante;
    public $tercero;
    public $estado;
    public $responsable;

    private $mysqli;
    public function __construct(){
        $this->mysqli = conectar::conexion();
    }

    public function registrar(comprobantePptal $data){
        try {
            $sql = "INSERT INTO gf_comprobante_pptal(
                                    numero,
                                    fecha,
                                    fechavencimiento,
                                    descripcion,
                                    parametrizacionanno,
                                    tipocomprobante,
                                    tercero,
                                    estado,
                                    responsable
                                ) VALUES(
                                    $data->numero,
                                    '$data->fecha',
                                    '$data->fechavencimiento',
                                    '$data->descripcion',
                                    $data->parametrizacionanno,
                                    $data->tipocomprobante,
                                    $data->tercero,
                                    $data->estado,
                                    $data->responsable
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

    public function obtnerUltimoRegistroTipoN($tipo, $numero){
        try {
            $id  = 0;
            $sql = "SELECT MAX(id_unico) FROM  gf_comprobante_pptal WHERE tipocomprobante = $tipo AND numero = $numero";
            $res = $this->mysqli->query($sql);
            if($res->num_rows > 0){
                $row = mysqli_fetch_row($res);
                $id  = $row[0];
            }
            return $id;
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function modificar($id_unico, $fecha, $fechaVencimiento, $descripcion){
        try {
            $sql = "UPDATE gf_comprobante_pptal
                    SET    fecha            = '$fecha',
                           fechavencimiento = '$fechaVencimiento',
                           descripcion      = '$descripcion'
                    WHERE  id_unico         = $id_unico";
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

    public function obtner($id_unico){
        try {
            $id_ = 0;
            $sql = "SELECT id_unico FROM gf_comprobante_pptal WHERE md5(id_unico) = '$id_unico'";
            $res = $this->mysqli->query($sql);
            if($res->num_rows > 0){
                $row = mysqli_fetch_row($res);
                $id_ = $row[0];
            }
            return $id_;
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function validarNumero($tipo, $param){
        try {
            $xxx = $this->obtenerUltimoNumero($tipo, $param);
            if(empty($xxx)){
                $anno = $this->obtenerAnnoParam($param);
                $num  = $anno.'000001';
            }else{
                $num  = $xxx + 1;
            }
            return $num;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerAnnoParam($param){
        try {
            $xxx = 0;
            $str = "SELECT anno FROM gf_parametrizacion_anno WHERE id_unico = $param";
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

    public function obtenerUltimoNumero($tipo, $param){
        try {
            $xxx = 0;
            $str = "SELECT MAX(numero) FROM gf_comprobante_pptal WHERE tipocomprobante = $tipo AND parametrizacionanno = $param";
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

    public function guardar($numero, $fecha, $fechavencimiento, $descripcion, $parametrizacionanno, $tipocomprobante, $tercero, $estado, $responsable){
        try {
            $str = "INSERT INTO gf_comprobante_pptal( numero, fecha, fechavencimiento, descripcion, parametrizacionanno, tipocomprobante, tercero, estado, responsable ) 
                          VALUES( $numero, '$fecha', '$fechavencimiento', '$descripcion', $parametrizacionanno, $tipocomprobante, $tercero, $estado, $responsable)";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function CambiarRemision($pto, $numero, $tipo){
        try {
            $str = "UPDATE gf_comprobante_pptal SET numero = $numero, tipocomprobante = $tipo WHERE  id_unico = $pto";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function ContarDetalles($pto){
        try {
            $xxx = 0;
            $str = "SELECT COUNT(*) FROM gf_detalle_comprobante_pptal WHERE comprobantepptal = $pto";
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

    public function eliminar($id){
        try {
            $str = "DELETE FROM gf_comprobante_pptal WHERE id_unico = $id";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}