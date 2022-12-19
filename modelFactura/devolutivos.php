<?php
require_once('./Conexion/db.php');
@session_start();
class devolutivos{
    private  $mysqli;

    public function __construct(){
        $this->mysqli = conectar::conexion();
    }

    public function buscarFacturasTipo($tipo){
        try {
            $param    = $_SESSION['anno'];
            $str = "SELECT   gft.id_unico, 
                        CONCAT_WS(' ', gtf.prefijo, gft.numero_factura, DATE_FORMAT(gft.fecha_factura, '%d/%m/%Y'))
                    FROM    gp_factura      AS gft
                    LEFT JOIN gp_tipo_factura AS gtf ON gft.tipofactura = gtf.id_unico
                    WHERE  gft.tipofactura = $tipo AND gft.parametrizacionanno = $param
                    ORDER BY cast(gft.numero_factura as unsigned) DESC";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
    
    public function registrarData($factura, $concepto, $valor, $cant, $iva, $impo, $ajuste, $valorT, 
            $dtc, $dtm, $dta, $descuento, $unidad, $valorO, $desc){
        try {
             $str = "INSERT INTO gp_detalle_factura( factura, concepto_tarifa, valor, cantidad, iva, impoconsumo,
                          ajuste_peso, valor_total_ajustado, detallecomprobante, detallemovimiento, detalleafectado, descuento,
                          unidad_origen, valor_origen, descripcion)
                          VALUES ( $factura, $concepto, $valor, $cant, $iva, $impo, $ajuste, $valorT, $dtc, $dtm, $dta, $descuento, 
                          $unidad,$valorO, $desc)";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
    public function buscarAfectacionesDetalleDev($id){
        try {
            $xxx = 0;
            $str = "SELECT gpf.cantidad,gpf.valor FROM gp_detalle_factura AS gpf WHERE  gpf.detalleafectado = $id";
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
     public function buscarAfectacionesDetalleDevValor($id){
        try {
            $xxx = 0;
            $str = "SELECT gpf.valor FROM gp_detalle_factura AS gpf WHERE  gpf.detalleafectado = $id";
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

    public function obtenerListadoTerceros($orden, $compania){ 
        try {
            $str = "SELECT    ter.id_unico
                              ,IF( CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) = '',
                                ter.razonsocial,
                                CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos)
                              ) AS NOMBRE,
                              CONCAT_WS( ' ', tip.nombre, ter.numeroidentificacion, ter.digitoverficacion)
                    FROM      gf_tercero ter
                    LEFT JOIN gf_tipo_identificacion AS tip ON ter.tipoidentificacion = tip.id_unico
                    WHERE     ter.compania = $compania
                    ORDER BY id_unico $orden";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerTercerosDiff($id, $compania){
        try {
            $str = "SELECT    ter.id_unico,
                              UPPER(
                                IF(
                                  CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) = '',
                                  ter.razonsocial,
                                  CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos)
                                )
                              ),
                              CONCAT_WS(' ', tip.sigla, ter.numeroidentificacion)
                    FROM      gf_tercero             AS ter
                    LEFT JOIN gf_tipo_identificacion AS tip ON ter.tipoidentificacion = tip.id_unico
                    WHERE     ter.id_unico != $id
                    AND       ter.compania  = $compania
                    ORDER BY  ter.numeroidentificacion";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtnerDataFactura($id_unico){
        try {
            $str = "SELECT    gpf.id_unico, gpf.tipofactura, gpf.numero_factura, gpf.tercero, gpf.centrocosto,
                              DATE_FORMAT(gpf.fecha_factura,'%d/%m/%Y'),
                              DATE_FORMAT(gpf.fecha_vencimiento,'%d/%m/%Y'),
                              gpf.descripcion, gpf.estado_factura, gpf.tercero, gpf.descuento, gef.nombre, gtp.resolucion,
                              gtp.nombre,
                              (
                                IF(
                                  CONCAT_WS(' ', gtr.nombreuno, gtr.nombredos, gtr.apellidouno, gtr.apellidodos) = '',
                                  gtr.razonsocial,
                                  CONCAT_WS(' ', gtr.nombreuno, gtr.nombredos, gtr.apellidouno, gtr.apellidodos)
                                )
                              ),
                              gtr.numeroidentificacion,
                              gdr.direccion,
                              gci.nombre,
                              (
                                IF(
                                  CONCAT_WS(' ', gvn.nombreuno, gvn.nombredos, gvn.apellidouno, gvn.apellidodos) = '',
                                  gvn.razonsocial,
                                  CONCAT_WS(' ', gvn.nombreuno, gvn.nombredos, gvn.apellidouno, gvn.apellidodos)
                                )
                              ),
                              gpf.vendedor,
                              CONCAT_WS(' ', gtp.prefijo, gtp.nombre),
                              gcc.nombre, gpf.fecha_factura
                    FROM      gp_factura        AS gpf
                    LEFT JOIN gp_estado_factura AS gef ON gpf.estado_factura   = gef.id_unico
                    LEFT JOIN gp_tipo_factura   AS gtp ON gpf.tipofactura      = gtp.id_unico
                    LEFT JOIN gf_tercero        AS gtr ON gpf.tercero          = gtr.id_unico
                    LEFT JOIN gf_direccion      AS gdr ON gdr.tercero          = gtr.id_unico
                    LEFT JOIN gf_ciudad         AS gci ON gdr.ciudad_direccion = gci.id_unico
                    LEFT JOIN gf_tercero        AS gvn ON gpf.vendedor         = gvn.id_unico
                    LEFT JOIN gf_centro_costo   AS gcc ON gpf.centrocosto      = gcc.id_unico
                    WHERE     md5(gpf.id_unico) = '$id_unico'";
            $res = $this->mysqli->query($str);
            $row = mysqli_fetch_row($res);
            return $row;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtenerRecaudoFactura($factura){ 
        try {
            $xxx = 0;
            $str = "SELECT    gdp.pago
                    FROM      gp_detalle_pago    AS gdp
                    LEFT JOIN gp_detalle_factura AS gdf ON gdp.detalle_factura = gdf.id_unico
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
    
    public function obtenerDetalles($factura){
        try {
            $str = "SELECT    gdf.id_unico, gdf.concepto_tarifa, gdf.valor, gdf.cantidad, gdf.iva, gdf.impoconsumo, gdf.ajuste_peso, gdf.valor_total_ajustado, UPPER(gct.nombre),t.porcentaje_iva
                    FROM      gp_detalle_factura AS gdf
                    LEFT JOIN gp_concepto AS gct ON gdf.concepto_tarifa = gct.id_unico
                    LEFT JOIN gp_concepto_tarifa AS ct ON ct.concepto=gct.id_unico
                    LEFT JOIN gp_tarifa AS t ON t.id_unico=ct.tarifa
                    WHERE     md5(gdf.factura) = '$factura'";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
    
    public function guardarFact($num, $tipo, $tercero, $fecha, $estado, $responsable, $vendedor, $param, $centro, $desc){
        try {
            $str = "INSERT INTO gp_factura (numero_factura, tipofactura, tercero, fecha_factura, fecha_vencimiento,  estado_factura, responsable, vendedor, parametrizacionanno, centrocosto, descripcion)
                    VALUES ($num, $tipo, $tercero, '$fecha', '$fecha', $estado, $responsable, $vendedor, $param, $centro,'$desc')";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
    
    public function obtenerTipoMovimiento($id){
        try {
            $id_ = 0;
            $str = "SELECT tipo_movimiento FROM gp_tipo_factura WHERE id_unico = $id";
            $res = $this->mysqli->query($str);
            if($res->num_rows > 0){
                $row = $res->fetch_row();
                $id_ = $row[0];
            }
            return $id_;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
    
    public function obtenerUltimoIdTipo($tipo){
        try {
            $xxx = 0;
            $str = "SELECT MAX(id_unico) FROM gp_factura WHERE tipofactura = $tipo";
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
    
    public function obtenerData($id){
        try {
            $str = "SELECT    gdf.concepto_tarifa, gdf.cantidad, gdf.iva, gdf.valor, gdf.impoconsumo, gdm.valor,
                              gdm.unidad_origen, gdm.valor_origen, gdm.descuento, gdm.planmovimiento, gdf.valor_total_ajustado,
                              gdf.unidad_origen, gdf.valor_total_ajustado, gdf.descripcion, 
                               f.forma_pago, f.metodo_pago
                    FROM      gp_detalle_factura     AS gdf
                    LEFT JOIN gf_detalle_movimiento  AS gdm ON gdf.detallemovimiento  = gdm.id_unico
                    LEFT JOIN gf_detalle_comprobante AS gdc ON gdf.detallecomprobante = gdc.id_unico
                    LEFT JOIN gp_factura f ON gdf.factura = f.id_unico 
                    WHERE     gdf.id_unico = $id";
            $res = $this->mysqli->query($str);
            return $res->fetch_row();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
    public function obtenerUnidadFactor($unidad, $concepto){
        try {
            $xxx = 0;
            $str = "SELECT    geu.valor_conversion
                    FROM      gp_concepto_tarifa AS gct
                    LEFT JOIN gf_elemento_unidad AS geu ON gct.elemento_unidad = geu.id_unico
                    WHERE     gct.concepto       = $concepto
                    AND       geu.unidad_empaque = $unidad";
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


    public function ActualizarFact($id, $forma_pago, $metodo_pago){
        try {
            $str = "UPDATE gp_factura SET forma_pago =$forma_pago, metodo_pago = $metodo_pago WHERE id_unico = $id";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}