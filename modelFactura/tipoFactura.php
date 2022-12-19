<?php
require_once ('./Conexion/db.php');
class tipoFactura{
    public $id_unico;
    public $nombre;
    public $prefijo;
    public $servicio;
    public $tipo_comprobante;
    public $tipo_movimiento;
    public $clase_factura;

    private $mysqli;

    public function __construct(){
        $this->mysqli = conectar::conexion();
    }

    public function registrar(tipoFactura $data){
        try {
            $sql = "INSERT INTO gp_tipo_factura(
                                    nombre, prefijo, servicio, tipo_comprobante, tipo_movimiento, clase_factura
                                ) VALUES(
                                    '$data->nombre', '$data->prefijo', $data->servicio, $data->tipo_comprobante,
                                    $data->tipo_movimiento, $data->clase_factura
                                )";
            return $this->mysqli->query($sql);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function modificar(tipoFactura $data){
        try {
            $sql = "UPDATE gp_tipo_factura
                    SET    nombre           = '$data->nombre',
                           prefijo          = '$data->prefijo',
                           servicio         = $data->servicio,
                           tipo_comprobante = $data->tipo_comprobante,
                           tipo_movimiento  = $data->tipo_movimiento,
                           clase_factura    = $data->clase_factura
                    WHERE  id_unico         = $data->id_unico";
            return $this->mysqli->query($sql);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function eliminar($id_unico){
        try {
            $sql = "DELETE FROM gp_tipo_factura WHERE id_unico = $id_unico";
            return $this->mysqli->query($sql);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerTipoComprobanteCnt($id_unico){
        try {
            $id  = 0;
            $sql = "SELECT tipo_comprobante FROM gp_tipo_factura WHERE id_unico = $id_unico";
            $res = $this->mysqli->query($sql);
            if($res->num_rows > 0){
                $row = $res->fetch_row();
                $id  = $row[0];
            }
            return $id;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function listar(){
        try {
            $sql = "SELECT    tpf.id_unico, tpf.nombre, tpf.prefijo, tpf.servicio, tpf.tipo_comprobante, tpc.nombre,
                              UPPER(tpm.nombre), UPPER(clf.nombre)
                    FROM      gp_tipo_factura     AS tpf
                    LEFT JOIN gf_tipo_comprobante AS tpc ON tpc.id_unico        = tpf.tipo_comprobante
                    LEFT JOIN gf_tipo_movimiento  AS tpm ON tpf.tipo_movimiento = tpm.id_unico
                    LEFT JOIN gp_clase_factura    AS clf ON tpf.clase_factura   = clf.id_unico";
            $res = $this->mysqli->query($sql);
            $row = mysqli_fetch_all($res, MYSQLI_NUM);
            return $row;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtner($id_unico){
        try {
            $sql = "SELECT    tpf.id_unico, tpf.nombre, tpf.prefijo, tpf.servicio, tpf.tipo_comprobante, tpc.nombre,
                              tpc.sigla, tpm.id_unico, tpm.sigla, UPPER(tpm.nombre), clp.id_unico, clp.nombre
                    FROM      gp_tipo_factura     AS tpf
                    LEFT JOIN gf_tipo_comprobante AS tpc ON tpc.id_unico = tpf.tipo_comprobante
                    LEFT JOIN gf_tipo_movimiento  AS tpm ON tpm.id_unico = tpf.tipo_movimiento
                    LEFT JOIN gp_clase_factura    AS clp ON clp.id_unico = tpf.clase_factura
                    WHERE     md5(tpf.id_unico) = '$id_unico'";
            $res = $this->mysqli->query($sql);
            $row = mysqli_fetch_row($res);
            return $row;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function listarTodo($clase){
        try {
            @session_start();
            $compania = $_SESSION['compania'];
            $str = "SELECT id_unico, UPPER(nombre), prefijo FROM gp_tipo_factura WHERE clase_factura IN ($clase) AND compania = $compania ORDER BY nombre ASC";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function tipofc($factura){
        try {
            $str = "SELECT * FROM gp_factura f LEFT JOIN gp_tipo_factura tf ON f.tipofactura = tf.id_unico WHERE f.id_unico = $factura AND tf.tipo_cambio is not null";
            $res = $this->mysqli->query($str);
            $row = mysqli_fetch_row($res);
            if(!empty($row[0])){
                return $row[0];
            } else {
                return 0;
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function valortrm($factura){
        try {
           $sql = "SELECT tr.valor FROM gp_factura f
                LEFT JOIN gp_tipo_factura tf ON f.tipofactura = tf.id_unico 
                LEFT JOIN gf_tipo_cambio tc ON tf.tipo_cambio = tc.id_unico 
                LEFT JOIN gf_trm tr ON tc.id_unico = tr.tipo_cambio 
                WHERE f.id_unico = $factura AND tr.fecha = f.fecha_factura";
            $res = $this->mysqli->query($sql);
            $row = mysqli_fetch_row($res);
            if(!empty($row[0])){
                return $row[0];
            } else {
                return 0;
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}