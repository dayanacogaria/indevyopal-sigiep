<?php
require_once('../Conexion/db.php');
/**
 * Modelo de facturación, es decir de la tabla factura
 */
class factura{
    public $id_unico;
    public $numero_factura;
    public $tipofactura;
    public $tercero;
    public $fecha_factura;
    public $fecha_vencimiento;
    public $centrocosto;
    public $descripcion;
    public $estado_factura;
    public $responsable;
    public $parametrizacionanno;

    private $mysqli;

    public function __construct(){
        $this->mysqli = conectar::conexion();
    }

    public function obtnerFacturas($fecha_incial, $fecha_final){
        try {
            $sql = "SELECT tipofactura, id_unico, numero_factura, tercero
                    FROM   gp_factura
                    WHERE  (fecha_factura BETWEEN '$fecha_incial' AND '$fecha_final')";
            $res = $this->mysqli->query($sql);
            while($row = $res->fetch_row()){
                $facturas[] = "$row[0],$row[1],$row[2],$row[3]";
            }
            return $facturas;
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerCompania($id_unico){
        try {
            $sql = "SELECT      UPPER(IF(CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) IS NULL
                                OR CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos)='',
                                (ter.razonsocial),
                                CONCAT_WS(' ',ter.nombreuno,ter.nombredos,ter.apellidouno,ter.apellidodos))) AS 'NOMBRE',
                                CONCAT_WS(' :',UPPER(ti.nombre),ter.numeroidentificacion) AS IDENT,
                                ter.ruta_logo,
                                ter.digitoverficacion
                    FROM        gf_tercero             ter
                    LEFT JOIN   gf_tipo_identificacion ti ON ter.tipoidentificacion = ti.id_unico
                    WHERE       ter.id_unico = $id_unico";
            $res = $this->mysqli->query($sql);
            $row = mysqli_fetch_row($res);
            return $row;
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function valorDetallesFactura($id_unico){
        try {
            $xxx = 0;
            $sql = "SELECT valor FROM gp_detalle_factura WHERE factura = $id_unico";
            $res = $this->mysqli->query($sql);
            while($row = mysqli_fetch_row($res)){
                $xxx += $row[0];
            }
            return $xxx;
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    function obtnerComprobantesCntP($id_unico){
        try {
            $sql = "SELECT      cnt.id_unico as cnt, ptal.id_unico as ptal
                    FROM        gp_factura pg, gp_tipo_factura tpg, gf_tipo_comprobante tpc,gf_comprobante_cnt cnt, gf_tipo_comprobante_pptal tcp,gf_comprobante_pptal ptal
                    WHERE       pg.tipofactura        = tpg.id_unico
                    AND         tpc.id_unico          = tpg.tipo_comprobante
                    AND         cnt.tipocomprobante   = tpc.id_unico
                    AND         tpc.comprobante_pptal = tcp.id_unico
                    AND         ptal.tipocomprobante  = tcp.id_unico
                    AND         pg.numero_factura     = ptal.numero
                    AND         pg.numero_factura     = cnt.numero
                    AND         pg.id_unico           =  $id_unico";
            $res = $this->mysqli->query($sql);
            $row = mysqli_fetch_row($res);
            return $row;
            $this->mysqli->close();
        } catch (Exception $e) {
            $e->getMessage();
        }
    }

    public function obtnerValoresDetalleCnt($id_unico){
        try {
            $xxx = 0;
            $sql = "SELECT SUM(valor) FROM gf_detalle_comprobante WHERE comprobante = $id_unico AND naturaleza = 2";
            $res = $this->mysqli->query($sql);
            $row = mysqli_fetch_row($res);
            $xxx += $row[0];
            return $xxx;
            $this->mysqli->close();
        } catch(Exception $e){
            die($e->getMessage());
        }
    }

    public function obtnerValoresPptal($id_unico){
        try {
            $xxx = 0;
            $sql = "SELECT SUM(valor) FROM gf_detalle_comprobante_pptal WHERE comprobantepptal = $id_unico";
            $res = $this->mysqli->query($sql);
            $row = mysqli_fetch_row($res);
            $xxx += $row[0];
            return $xxx;
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerComprobantesDesdeDetalles($id_unico){
        try {
            $sql = "SELECT    dtc.comprobante,dtp.comprobantepptal
                    FROM      gp_detalle_factura dtf
                    LEFT JOIN gf_detalle_comprobante dtc       ON dtc.id_unico                = dtf.detallecomprobante
                    LEFT JOIN gf_detalle_comprobante_pptal dtp ON dtc.detallecomprobantepptal = dtp.id_unico
                    WHERE     dtf.factura = $id_unico AND dtc.detallecomprobantepptal IS NOT NULL";
            $res = $this->mysqli->query($sql);
            $row = mysqli_fetch_row($res);
            return $row[0];
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerValorTotalFacturIva($id_unico){
        try {
            $xxx = 0;
            $sql = "SELECT (valor * cantidad) + iva FROM gp_detalle_factura WHERE factura = $id_unico";
            $res = $this->mysqli->query($sql);
            while($row = mysqli_fetch_row($res)){
                $xxx += $row[0];
            }
            return $xxx;
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerMaxNumeroF($tipo){
        try {
            $sql = "SELECT MAX(numero_factura) FROM gp_factura WHERE tipofactura = $tipo";
            $res = $this->mysqli->query($sql);
            $row = mysqli_fetch_row($res);
            return $row[0];
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerFechaTipoNumero($tipo, $numero){
        try {
            $sql = "SELECT fecha_factura FROM gp_factura WHERE tipofactura = $tipo AND numero_factura = $numero";
            $res = $this->mysqli->query($sql);
            $row = mysqli_fetch_row($res);
            return $row[0];
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function contarFacturasTipo($tipo){
        try {
            $sql = "SELECT COUNT(*) FROM gp_factura WHERE tipofactura = $tipo";
            $res = $this->mysqli->query($sql);
            $row = mysqli_fetch_row($res);
            return $row[0];
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerNumeroFactura($id_unico){
        try {
            $sql = "SELECT numero_factura FROM gp_factura WHERE id_unico = $id_unico";
            $res = $this->mysqli->query($sql);
            $row = mysqli_fetch_row($res);
            return $row[0];
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
}