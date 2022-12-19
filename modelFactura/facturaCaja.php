<?php
require_once './Conexion/db.php';

/**
 * Clase para los procesos de la tabla gp_factura_caja
 * Class facturacaja
 */
class facturacaja{
    /**
     * @var mysqli
     * @access private
     */
    private $mysqli;

    /**
     * facturacaja constructor.
     */
    public function __construct(){
        $this->mysqli = conectar::conexion();
    }

    /**
     * Registra los datos en la tabla gp_factura_caja
     *
     * @param int $factura Identificador de factura
     * @param int $tipopago Identificador de tipo pago
     * @param int $tipo_mov_caja Identificador de tipo movimiento de caja
     * @param float|int $valor Valor de la factura o movimiento
     * @param int cajero Identificador de tercero o cajero
     * @param int $caja Identificador de caja
     * @param date|mixed $fecha Fecha del movimiento de facturación
     * @param date|string|mixed $hora Hora del movimiento
     * @return bool|mysqli_result|string respuesta del movimiento
     */
    public function registrar($factura, $tipopago, $tipo_mov_caja, $valor, $cajero, $caja, $fecha, $hora){
        try {
            $str = "INSERT INTO gp_factura_caja(factura, tipopago, tipo_mov_caja, valor, cajero, caja, fecha, hora) 
                      VALUES ($factura, $tipopago, $tipo_mov_caja, $valor, $cajero, $caja, '$fecha', '$hora')";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Obtiene listado de las cajas guardadas en la base de datos
     *
     * @return mixed|string
     */
    public function obtenerListadoCaja(){
        try {
            $str = "SELECT id_unico, nombre FROM gp_caja";
            $res = $this->mysqli->query($str);
        return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Obtiene los datos de la caja
     *
     * @param int $id
     * @return string|mixed
     */
    public function obtenerdataCaja($id){
        try {
            $xxx = "";
            $str = "SELECT sigla FROM gp_caja WHERE id_unico = $id";
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