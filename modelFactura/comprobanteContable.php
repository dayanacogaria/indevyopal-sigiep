<?php
require_once ('./Conexion/db.php'); 

/**
 * Modelo de Comprobante Contable
 */
class comprobanteContable{
    public $id_unico;
    public $numero;
    public $fecha;
    public $descripcion;
    public $tipocomprobante;
    public $parametrizacionanno;
    public $compania;
    public $tercero;
    public $estado;

    private $mysqli;

    public function __construct(){
        $this->mysqli = conectar::conexion();
    }

    public function registrar(comprobanteContable $data){
        try {
            $sql = "INSERT INTO gf_comprobante_cnt(
                                    numero,
                                    fecha,
                                    descripcion,
                                    tipocomprobante,
                                    parametrizacionanno,
                                    compania,
                                    tercero,
                                    estado
                                ) VALUES(
                                    $data->numero,
                                    '$data->fecha',
                                    '$data->descripcion',
                                    $data->tipocomprobante,
                                    $data->parametrizacionanno,
                                    $data->compania,
                                    $data->tercero,
                                    $data->estado)";
            $res = $this->mysqli->query($sql);

            if($res == true){
                $rest = true;
            }else{
                $rest = false;
            }
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerUltimoRegistroTipo($tipo, $numero){
        try {
            $id  = 0;
            $sql = "SELECT MAX(id_unico) FROM gf_comprobante_cnt WHERE tipocomprobante = $tipo AND numero = $numero";
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

    public function obtnerTipoPptal($tipo){
        try {
            $id  = 0;
            $sql = "SELECT comprobante_pptal FROM gf_tipo_comprobante WHERE id_unico = $tipo";
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

    public function modificar($id_unico, $fecha, $descripcion){
        try {
            $sql = "UPDATE gf_comprobante_cnt
                    SET    fecha       = '$fecha',
                           descripcion = '$descripcion'
                    WHERE  id_unico    =  $id_unico";
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
            $sql = "SELECT id_unico FROM gf_comprobante_cnt WHERE md5(id_unico) = '$id_unico'";
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

    public function obtenerUltimoNumero($tipo, $param){
        try {
            $xxx = 0;
            $str = "SELECT MAX(numero) FROM gf_comprobante_cnt WHERE tipocomprobante = $tipo AND parametrizacionanno = $param";
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

    public function obtenerTipoComprobanteCausacion($tipo){
        try {
            $xxx = 0;
            $str = "SELECT tipo_comp_hom FROM gf_tipo_comprobante WHERE id_unico = $tipo";
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

    /**
     * @param $numero
     * @param $fecha
     * @param $descripcion
     * @param $tipocomprobante
     * @param $parametrizacionanno
     * @param $compania
     * @param $tercero
     * @param $estado
     * @return bool|mysqli_result
     */
    public function guardar($numero, $fecha, $descripcion, $tipocomprobante, $parametrizacionanno, $compania, $tercero, $estado){
        try {
            $str = "INSERT INTO gf_comprobante_cnt( numero, fecha, descripcion, tipocomprobante, parametrizacionanno, compania, tercero, estado) 
                                VALUES( $numero, '$fecha', '$descripcion', $tipocomprobante, $parametrizacionanno, $compania, $tercero, $estado)";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtenerDataCompania($id){
        try {
            $data = array();
            $str  = "SELECT UPPER(ter.razonsocial), CONCAT_WS(' ',ter.numeroidentificacion, ter.digitoverficacion), tel.valor, dir.direccion, ciu.nombre, dep.nombre, ter.ruta_logo 
                     FROM gf_tercero AS ter 
                     LEFT JOIN gf_telefono     AS tel ON ter.id_unico         = tel.tercero
                     LEFT JOIN gf_direccion    AS dir ON ter.id_unico         = dir.tercero
                     LEFT JOIN gf_ciudad       AS ciu ON dir.ciudad_direccion = ciu.id_unico
                     LEFT JOIN gf_departamento AS dep ON ciu.departamento     = dep.id_unico
                     WHERE ter.id_unico = $id";
            $res  = $this->mysqli->query($str);
            if($res->num_rows > 0){
                $row = $res->fetch_row();
                $data['razon'] = $row[0];
                $data['nit']   = $row[1];
                $data['tel']   = $row[2];
                $data['dir']   = $row[3];
                $data['ciu']   = $row[4];
                $data['dep']   = $row[5];
                $data['log']   = $row[6];
            }
            return $data;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function CambiarRemision($cnt, $numero, $tipo){
        try {
            $str = "UPDATE gf_comprobante_cnt SET numero = $numero, tipocomprobante = $tipo WHERE id_unico = $cnt";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function ContarDetalles($cnt){
        try {
            $xxx = 0;
            $str = "SELECT COUNT(*) FROM gf_detalle_comprobante WHERE comprobante = $cnt";
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
            $str = "DELETE FROM gf_comprobante_cnt WHERE id_unico = $id";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function buscarComprobante($numero, $tipo){
        try {
            $xxx = 0;
            $str = "SELECT id_unico FROM gf_comprobante_cnt WHERE numero = $numero AND tipocomprobante = $tipo";
            $res = $this->mysqli->query($str);
            if($res->fetch_all(MYSQLI_NUM)){
                $row = $res->fetch_row();
                $xxx = $row[0];
            }
            return $xxx;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerDetallesComprobante($cnt){
        try {
            $str = "SELECT id_unico FROM gf_detalle_comprobante WHERE comprobante = $cnt";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerDataComprobante($cuenta, $comprobante){
        try {
            $xxx = 0;
            $str = "SELECT id_unico FROM gf_detalle_comprobante WHERE cuenta = $cuenta AND comprobante = $comprobante";
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

    public function obtenerValorDetalle($id){
        try {
            $xxx = 0;
            $str = "SELECT valor FROM gf_detalle_comprobante WHERE id_unico = $id";
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

    public function actualizarValorDetalle($id, $valor){
        try {
            $str = "UPDATE gf_detalle_movimiento SET valor = $valor WHERE id_unico = $id";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerCuentaBanco($banco){
        try {
            $xxx = 0;
            $str = "SELECT cuenta FROM gf_cuenta_bancaria WHERE id_unico = $banco";
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

    public function obtenerCentroCosto($param, $nombre){
        try {
            $xxx = 0;
            $str = "SELECT id_unico FROM gf_centro_costo WHERE parametrizacionanno = $param AND nombre = '$nombre'";
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