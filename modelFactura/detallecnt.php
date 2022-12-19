<?php
require_once ('./Conexion/db.php');
/**
 * Modelo de detalle comprobante contable
 */
class detalleCnt{
    public $id_unico;
    public $fecha;
    public $descripcion;
    public $valor;
    public $valorejecucion;
    public $comprobante;
    public $cuenta;
    public $naturaleza;
    public $tercero;
    public $proyecto;
    public $centrocosto;
    public $detallecomprobantepptal;
    public $detalleafectado;

    private $mysqli;

    public function __construct(){
        $this->mysqli = conectar::conexion();
    }

    public function registrar(detalleCnt $data){
        try {
            $sql = "INSERT INTO gf_detalle_comprobante(
                                    fecha,
                                    descripcion,
                                    valor,
                                    valorejecucion,
                                    comprobante,
                                    cuenta,
                                    naturaleza,
                                    tercero,
                                    proyecto,
                                    centrocosto,
                                    detallecomprobantepptal,
                                    detalleafectado
                                ) VALUES(
                                    '$data->fecha',
                                    '$data->descripcion',
                                    $data->valor,
                                    $data->valorejecucion,
                                    $data->comprobante,
                                    $data->cuenta,
                                    $data->naturaleza,
                                    $data->tercero,
                                    $data->proyecto,
                                    $data->centrocosto,
                                    $data->detallecomprobantepptal,
                                    $data->detalleafectado
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

    public function obtenerUltimoRegistro($comprobante){
        try {
            $id  = 0;
            $sql = "SELECT MAX(id_unico) FROM gf_detalle_comprobante WHERE comprobante = $comprobante";
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
            $sql = "UPDATE gf_detalle_comprobante SET valor = $valor WHERE id_unico = $id_unico";
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

    public function obtnerAfectados($id_unico){
        try {
            $sql = "SELECT detalleafectado,detallecomprobantepptal FROM gf_detalle_comprobante WHERE id_unico = $id_unico";
            $res = $this->mysqli->query($sql);
            $row = mysqli_fetch_row($res);
            return $row;
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function eliminar($id_unico){
        try {
            $sql = "DELETE FROM gf_detalle_comprobante WHERE id_unico = $id_unico";
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

    public function eliminarDetallesCnt($id_unico){
        try {
            $sql = "DELETE FROM gf_detalle_comprobante WHERE comprobante = $id_unico";
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

    public function destruirRelacionArbolDetallesCnt($id_unico){
       try {
            $sql = "UPDATE gf_detalle_comprobante SET detalleafectado = NULL WHERE comprobante = $id_unico";
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

    public function obtnerTercero($id_unico){
        try {
            $id_ = 0;
            $sql = "SELECT tercero FROM gf_comprobante_cnt WHERE id_unico = $id_unico";
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

    public function obtnerFecha($id_unico){
        try {
            $x = 0;
            $sql = "SELECT DATE_FORMAT(fecha,'%Y-%m-%d') FROM gf_comprobante_cnt WHERE id_unico = $id_unico";
            $res = $this->mysqli->query($sql);
            if($res->num_rows > 0){
                $row = mysqli_fetch_row($res);
                $x = $row[0];
            }
            return $x;
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerDescripcion($id_unico){
        try {
            $x = 0;
            $sql = "SELECT descripcion FROM gf_comprobante_cnt WHERE id_unico = $id_unico";
            $res = $this->mysqli->query($sql);
            if($res->num_rows > 0){
                $row = mysqli_fetch_row($res);
                $x = $row[0];
            }
            return $x;
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function validarNaturaleza($xxx, $valor){
        if($xxx == 1){
            $valor = $valor;
        }else{
            $valor = $valor * -1;
        }
        return $valor;
    }

    public function obtenerNaturaleza($id){
        try {
            $xxx = 0;
            $str = "SELECT naturaleza FROM gf_cuenta WHERE id_unico = $id";
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

    public function obtenerCuentaBanco($id){
        try {
            $data = array();
            $str  = "SELECT cb.cuenta, c.naturaleza FROM gf_cuenta_bancaria cb LEFT JOIN gf_cuenta c ON cb.cuenta = c.id_unico WHERE cb.id_unico = $id";
            $res = $this->mysqli->query($str);
            if($res->num_rows > 0){
                $row = $res->fetch_row();
                $data['cuenta']     = $row[0];
                $data['naturaleza'] = $row[1];
            }
            return $data;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function guardar($fecha, $descripcion, $valor, $valorejecucion, $comprobante, $cuenta, $naturaleza, $tercero, $proyecto, $centrocosto, $detallecomprobantepptal, $detalleafectado){
        try {
            $str = "INSERT INTO gf_detalle_comprobante( fecha, descripcion, valor, valorejecucion, comprobante, cuenta, naturaleza, tercero, proyecto, centrocosto, detallecomprobantepptal, detalleafectado) 
                          VALUES( '$fecha', '$descripcion', $valor, $valorejecucion, $comprobante, $cuenta, $naturaleza, $tercero, $proyecto, $centrocosto, $detallecomprobantepptal, $detalleafectado)";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
}