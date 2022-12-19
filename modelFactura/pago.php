<?php
require_once ('./Conexion/db.php');
class pago{

    private $mysqli;

    public function __construct(){
        $this->mysqli = conectar::conexion();
    }

    public function guardar($numero, $tipo_pago, $responsable, $fecha_pago, $banco, $estado, $parametrizacionanno, $usuario){
        try {
            $str = "INSERT INTO gp_pago(numero_pago, tipo_pago, responsable, fecha_pago, banco, estado, parametrizacionanno, usuario)
                            VALUES ($numero, $tipo_pago, $responsable, '$fecha_pago', $banco, $estado, $parametrizacionanno, $usuario)";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerMaxId($tipo){
        try {
            $xxx = 0;
            $str = "SELECT MAX(id_unico) FROM gp_pago WHERE tipo_pago = $tipo";
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
     * guardar_detalle
     *
     * Metodo para registrar el detalle de pago
     *
     * @param $detalle_factura
     * @param $valor
     * @param $iva
     * @param $impoconsumo
     * @param $ajuste
     * @param $saldo_credito
     * @param $pago
     * @param $detallecomprobante
     * @return bool|mysqli_result|string
     */
    public function guardar_detalle($detalle_factura, $valor, $iva, $impoconsumo, $ajuste, $saldo_credito, $pago, $detallecomprobante){
        try {
            $str = "INSERT INTO gp_detalle_pago(detalle_factura, valor, iva, impoconsumo, ajuste_peso, saldo_credito, pago, detallecomprobante)
                                        VALUES ($detalle_factura, $valor, $iva, $impoconsumo, $ajuste, $saldo_credito, $pago, $detallecomprobante)";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerConceptoPlan($id){
        try {
            $xxx = 0;
            $str = "SELECT id_unico FROM gp_concepto WHERE plan_inventario = $id";
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

    public function obtenerConfiguracionConcepto($id, $param){
        try {
            $data = array();
            $str  = "SELECT     gct.concepto_rubro, gct.rubro_fuente
                     FROM       gp_configuracion_concepto AS gct
                     WHERE      gct.concepto = $id
                     AND        gct.parametrizacionanno = $param";
            $res  = $this->mysqli->query($str);
            if($res->num_rows > 0){
                $row = $res->fetch_row();
                $data['concepto_rubro'] = $row[0];
                $data['rubro_fuente']   = $row[1];
            }
            return $data;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerConceptoContabilidad($id){
        try {
            $xxx = 0;
            $str = "SELECT concepto FROM gf_concepto_rubro WHERE id_unico = $id";
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

    public function obtenerConfiguracionCuentasPorConceptoRubro($concepto_rubro){
        try {
            $data = array();
            $str  = "SELECT cuenta_debito, cuenta_credito, cuenta_iva, cuenta_impoconsumo, centrocosto, proyecto
                    FROM gf_concepto_rubro_cuenta WHERE concepto_rubro = $concepto_rubro";
            $res  = $this->mysqli->query($str);
            if($res->num_rows > 0){
                $row = $res->fetch_row();
                $data['cuenta_debito']      = $row[0];
                $data['cuenta_credito']     = $row[1];
                $data['cuenta_iva']         = $row[2];
                $data['cuenta_impoconsumo'] = $row[3];
                $data['centrocosto']        = $row[4];
                $data['proyecto']           = $row[5];
            }
            return $data;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerTercero($id){
        try {
            $xxx = 0;
            $str = "SELECT (IF(
                              CONCAT_WS(' ', nombreuno, nombredos, apellidouno, apellidodos) = '',
                              razonsocial,
                              CONCAT_WS(' ', nombreuno, nombredos, apellidouno, apellidodos)
                            )) AS nom
                    FROM gf_tercero
                    WHERE id_unico = $id";
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

    public function obtenerCuentasB($param, $compania){
        try {
            $str = "SELECT    cta.id_unico, cta.descripcion
                    FROM      gf_cuenta_bancaria         AS cta
                    LEFT JOIN gf_cuenta_bancaria_tercero AS ctt ON cta.id_unico = ctt.cuentabancaria
                    WHERE     parametrizacionanno = $param
                    AND       ctt.tercero         = $compania";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function validarNumero($tipo, $param){
        try {
            $xxx = $this->obtenerUltimoNumero($tipo, $param);
            if(empty($xxx)){
                $anno = $this->obtenerAnnoParametrizacion($param);
                $num  = $anno.'000001';
            }else{
                $num  = $xxx + 1;
            }
            return $num;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerAnnoParametrizacion($param){
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
            try {
                $xxx = 0;
                $str = "SELECT MAX(numero_pago) FROM gp_pago WHERE tipo_pago = $tipo AND parametrizacionanno = $param";
                $res = $this->mysqli->query($str);
                if($res->num_rows > 0){
                    $row = $res->fetch_row();
                    $xxx = $row[0];
                }
                return $xxx;
            } catch (Exception $e) {
                return $e->getMessage();
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerDataTerceroVarios($compania){
        try {
            $xxx = 0;
            $str = "SELECT id_unico FROM gf_tercero WHERE compania = $compania AND nombreuno = 'Varios'";
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

    public function obtenerTipoFactura($factura){
        try {
            $xxx = 0;
            $str = "SELECT tipofactura FROM gp_factura WHERE id_unico = $factura";
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

    public function obtenerTipoRecaudo($tipo){
        try {
            $xxx = 0;
            $str = "SELECT tipo_recaudo FROM gp_tipo_factura WHERE id_unico = $tipo";
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

    public function obtenerTipoComprobante($tipo){
        $xxx = 0;
        $str = "SELECT tipo_comprobante FROM gp_tipo_pago WHERE id_unico = $tipo";
        $res = $this->mysqli->query($str);
        if($res->num_rows > 0){
           $row = $res->fetch_row();
           $xxx = $row[0];
        }
        return $xxx;
    }

    public function obtenerTipoComprobantePptal($tipo){
        try {
            $xxx = 0;
            $str = "SELECT comprobante_pptal FROM gf_tipo_comprobante WHERE id_unico = $tipo";
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

    public function obtenerTipoComprobanteHom($tipo){
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

    public function CambiarRemision($pago, $numero, $tipo){
        try {
            $str = "UPDATE gp_pago SET numero_pago = $numero, tipo_pago = $tipo WHERE id_unico = $pago";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerTotalPago($pago){
        try {
            $xxx = 0;
            $str = "SELECT (valor + iva + impoconsumo + ajuste_peso) FROM gp_detalle_pago WHERE pago = $pago";
            $res = $this->mysqli->query($str);
            if($res->num_rows > 0){
                $data = $res->fetch_all(MYSQLI_NUM);
                foreach ($data as $row){
                    $xxx += $row[0];
                }
            }
            return $xxx;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerData($id){
        try {
            $str = "SELECT tipo_pago FROM gp_pago WHERE id_unico = $id";
            $res = $this->mysqli->query($str);
            return $res->fetch_row();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerFacturasFecha($fechaI, $fechaF, $tipo){
        try {
            $str = "SELECT    DISTINCT gpf.id_unico
                    FROM      gp_factura       AS gpf
                    LEFT JOIN gp_tipo_factura  AS gtf ON gpf.tipofactura = gtf.id_unico
                    WHERE     gpf.fecha_factura BETWEEN '$fechaI' AND '$fechaF'
                    AND       gtf.id_unico = $tipo";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerDetalleFacturaPago($factura){
        try {
            $xxx = 0;
            $str = "SELECT    gdp.id_unico
                    FROM      gp_detalle_pago    AS gdp
                    LEFT JOIN gp_detalle_factura As gdf ON gdp.detalle_factura = gdf.id_unico
                    WHERE     gdf.factura = $factura";
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

    public function buscarPagoFactura($factura){
        try {
            $xxx = 0;
            $str = "SELECT    gpd.pago
                    FROM      gp_detalle_pago    AS gpd
                    LEFT JOIN gp_detalle_factura AS gdf ON gpd.detalle_factura = gdf.id_unico
                    WHERE     gdf.factura = $factura";
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

    public function obtenerDetallesPago($pago){
        try {
            $str = "SELECT id_unico FROM gp_detalle_pago WHERE pago = $pago";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function eliminarPago($pago){
        try {
            $str = "DELETE FROM gp_detalle_pago WHERE pago = $pago";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function actualizarDetalleComprobanteDetallePago($id, $dtc){
        try {
            $str = "UPDATE gp_detalle_pago SET detallecomprobante = $dtc WHERE id_unico = $id";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerUltimoRegistroPago($pago){
        try {
            $xxx = 0;
            $str = "SELECT MAX(id_unico) FROM gp_detalle_pago WHERE pago = $pago";
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

    public function eliminarDetallesPago($pago){
        try {
            $str = "DELETE FROM gp_detalle_pago WHERE pago = $pago";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerPagosFechaTipo($fechaI, $fechaF, $tipo){
        try {
            $str = "SELECT    gpg.id_unico, gpg.numero_pago, gpg.fecha_pago, gpg.responsable
                    FROM      gp_detalle_pago    AS gdp
                    LEFT JOIN gp_detalle_factura AS gdf ON gdp.detalle_factura = gdf.id_unico
                    LEFT JOIN gp_pago            AS gpg ON gdp.pago            = gpg.id_unico
                    LEFT JOIN gp_factura         AS gft ON gdf.factura         = gft.id_unico
                    WHERE     (gft.tipofactura = $tipo)
                    AND       (gpg.fecha_pago BETWEEN '$fechaI' AND '$fechaF')
                    AND       (gdp.detallecomprobante IS NULL)
                    GROUP BY  gpg.id_unico";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function buscarComprobanteRecaudo($pago){
        try {
            $xxx = 0;
            $str = "SELECT    gdc.comprobante
                    FROM      gp_detalle_pago        AS gdp
                    LEFT JOIN gf_detalle_comprobante AS gdc ON gdp.detallecomprobante = gdc.id_unico
                    WHERE     gdp.pago = $pago";
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

    public function buscarDetallesPago($pago){
        try {
            $str = "SELECT    gdp.id_unico, gdf.concepto_tarifa, (gdf.valor * gdf.cantidad), (gdf.iva * gdf.cantidad),
                              (gdf.impoconsumo * gdf.cantidad)
                    FROM      gp_detalle_pago    AS gdp
                    LEFT JOIN gp_detalle_factura AS gdf ON gdp.detalle_factura = gdf.id_unico
                    WHERE     gdp.pago = $pago";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function listadoRecuados($fechaI, $fechaF, $tipoI, $tipoF, $clase){
        try {
            $str = "SELECT DISTINCT
                              gpg.id_unico, DATE_FORMAT(gpg.fecha_pago, '%d/%m/%Y'), UPPER(gtp.nombre), gpg.numero_pago,
                              gft.id_unico, DATE_FORMAT(gft.fecha_factura, '%d/%m/%Y'), gtf.prefijo, gft.numero_factura,
                              (
                                IF(
                                  CONCAT_WS(' ', gtr.nombreuno, gtr.nombredos, gtr.apellidouno, gtr.apellidodos) = ' ',
                                  gtr.razonsocial,
                                  CONCAT_WS(' ', gtr.nombreuno, gtr.nombredos, gtr.apellidouno, gtr.apellidodos)
                                )
                              ),
                              (
                                IF(
                                  gtr.razonsocial != ' ',
                                  CONCAT_WS(' ', gti.sigla, gtr.numeroidentificacion, gtr.digitoverficacion),
                                  CONCAT_WS(' ', gti.sigla, gtr.numeroidentificacion)
                                )
                              ),
                              UPPER(gft.descripcion)
                    FROM      gp_pago                AS gpg
                    LEFT JOIN gp_detalle_pago        AS gdp ON gpg.id_unico           = gdp.pago
                    LEFT JOIN gp_tipo_pago           AS gtp ON gpg.tipo_pago          = gtp.id_unico
                    LEFT JOIN gp_detalle_factura     AS gdf ON gdp.detalle_factura    = gdf.id_unico
                    LEFT JOIN gp_factura             AS gft ON gdf.factura            = gft.id_unico
                    LEFT JOIN gp_tipo_factura        AS gtf ON gft.tipofactura        = gtf.id_unico
                    LEFT JOIN gf_tercero             AS gtr ON gpg.responsable        = gtr.id_unico
                    LEFT JOIN gf_tipo_identificacion AS gti ON gtr.tipoidentificacion = gti.id_unico
                    WHERE     (gpg.fecha_pago  BETWEEN '$fechaI' AND '$fechaF')
                    AND       (gft.tipofactura BETWEEN $tipoI    AND $tipoF)
                    AND       (gtf.clase_factura IN ($clase))
                    ORDER BY  gpg.fecha_pago ASC, gpg.numero_pago ASC";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerDataPago($id){
        try {
            $str = "SELECT id_unico, banco, fecha_pago, responsable, usuario, numero_pago FROM gp_pago WHERE id_unico = $id";
            $res = $this->mysqli->query($str);
            return $res->fetch_row();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}