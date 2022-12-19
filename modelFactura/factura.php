<?php
require_once('./Conexion/db.php');
/**
 * Modelo de facturación
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
    public $vendedor;
    public $parametrizacionanno;
    public $descuento;

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

    public function getNumeroFactura(){
        return $this->numero_factura;
    }

    public function setNumeroFactura($numero_factura){
        $this->numero_factura = $numero_factura;
    }

    public function getTipofactura(){
        return $this->tipofactura;
    }

    public function setTipofactura($tipofactura){
        $this->tipofactura = $tipofactura;
    }

    public function getTercero(){
        return $this->tercero;
    }

    public function setTercero($tercero){
        $this->tercero = $tercero;
    }

    public function getFechaFactura(){
        return $this->fecha_factura;
    }

    public function setFechaFactura($fecha_factura){
        $this->fecha_factura = $fecha_factura;
    }

    public function getFechaVencimiento(){
        return $this->fecha_vencimiento;
    }

    public function setFechaVencimiento($fecha_vencimiento){
        $this->fecha_vencimiento = $fecha_vencimiento;
    }

    public function getCentrocosto(){
        return $this->centrocosto;
    }

    public function setCentrocosto($centrocosto){
        $this->centrocosto = $centrocosto;
    }

    public function getDescripcion(){
        return $this->descripcion;
    }

    public function setDescripcion($descripcion){
        $this->descripcion = $descripcion;
    }

    public function getEstadoFactura(){
        return $this->estado_factura;
    }

    public function setEstadoFactura($estado_factura){
        $this->estado_factura = $estado_factura;
    }

    public function getResponsable(){
        return $this->responsable;
    }

    public function setResponsable($responsable){
        $this->responsable = $responsable;
    }

    public function getVendedor(){
        return $this->vendedor;
    }

    public function setVendedor($vendedor){
        $this->vendedor = $vendedor;
    }

    public function getParametrizacionanno(){
        return $this->parametrizacionanno;
    }

    public function setParametrizacionanno($parametrizacionanno){
        $this->parametrizacionanno = $parametrizacionanno;
    }

    public function getDescuento(){
        return $this->descuento;
    }

    public function setDescuento($descuento){
        $this->descuento = $descuento;
    }

    public function registrar(factura $data){
        try {
             $sql = "INSERT INTO gp_factura( numero_factura, tipofactura, tercero, fecha_factura, fecha_vencimiento, centrocosto,
                                    descripcion, estado_factura, responsable, vendedor, parametrizacionanno
                                ) VALUES(
                                    $data->numero_factura, $data->tipofactura, $data->tercero, '$data->fecha_factura',
                                    '$data->fecha_vencimiento', $data->centrocosto, '$data->descripcion', $data->estado_factura,
                                    $data->responsable, $data->vendedor, $data->parametrizacionanno
                                )";
             return $this->mysqli->query($sql);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function modificar($id_unico, $fecha, $fecha_vencimiento, $descripcion, $tercero, $vendedor){
        try {
            $sql = "UPDATE gp_factura
                    SET    fecha_factura     = '$fecha',
                           fecha_vencimiento = '$fecha_vencimiento',
                           descripcion       = '$descripcion',
                           tercero           = $tercero, 
                           vendedor          = $vendedor 
                    WHERE  id_unico          = $id_unico";
            return $this->mysqli->query($sql);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerFactura($id_unico){
        try {
            $str = "SELECT    gpf.id_unico, gpf.tipofactura, gpf.numero_factura, gpf.tercero, gpf.centrocosto,
                              DATE_FORMAT(gpf.fecha_factura,'%d/%m/%Y'),
                              DATE_FORMAT(gpf.fecha_vencimiento,'%d/%m/%Y'),
                              gpf.descripcion, gpf.estado_factura, gpf.tercero, gpf.descuento, gef.nombre, gtp.resolucion,
                              gtp.nombre,
                              (
                                IF(
                                    gtr.nombre_comercial = ' ',
                                    IF(
                                      CONCAT_WS(' ', gtr.nombreuno, gtr.nombredos, gtr.apellidouno, gtr.apellidodos) = ' ',
                                      gtr.razonsocial,
                                      CONCAT_WS(' ', gtr.nombreuno, gtr.nombredos, gtr.apellidouno, gtr.apellidodos)
                                    ),
                                    IF(
                                      CONCAT_WS(' ', gtr.nombreuno, gtr.nombredos, gtr.apellidouno, gtr.apellidodos) = ' ',
                                      CONCAT_WS(' ', gtr.razonsocial, gtr.nombre_comercial),
                                      CONCAT_WS(' ', gtr.nombreuno, gtr.nombredos, gtr.apellidouno, gtr.apellidodos, gtr.nombre_comercial)
                                    )
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
                              gpf.vendedor
                    FROM      gp_factura        AS gpf
                    LEFT JOIN gp_estado_factura AS gef ON gpf.estado_factura   = gef.id_unico
                    LEFT JOIN gp_tipo_factura   AS gtp ON gpf.tipofactura      = gtp.id_unico
                    LEFT JOIN gf_tercero        AS gtr ON gpf.tercero          = gtr.id_unico
                    LEFT JOIN gf_direccion      AS gdr ON gdr.tercero          = gtr.id_unico
                    LEFT JOIN gf_ciudad         AS gci ON gdr.ciudad_direccion = gci.id_unico
                    LEFT JOIN gf_tercero        AS gvn ON gpf.vendedor         = gvn.id_unico
                    WHERE     md5(gpf.id_unico) = '$id_unico'";
            $res = $this->mysqli->query($str);
            $row = mysqli_fetch_row($res);
            return $row;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerUltimaFacturaTN($tipo, $numero){
        try {
            $id  = 0;
            $sql = "SELECT MAX(id_unico) FROM gp_factura WHERE tipofactura = $tipo AND numero_factura = $numero";
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

    public function obtnerTipoComprobanteCnt($tipo){
        try {
            $id  = 0;
            $sql = "SELECT tipo_comprobante FROM gp_tipo_factura WHERE id_unico = $tipo";
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

    public function obtnerFecha($tipo){
        try {
            $sql = "SELECT MAX(fecha_factura) FROM gp_factura WHERE tipofactura = $tipo";
            $res = $this->mysqli->query($sql);
            $row = mysqli_fetch_row($res);
            return $row[0];
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerEstado($id_unico){
        try {
            if(!empty($id_unico)){
                $nom = "";
                $sql = "SELECT nombre FROM gp_estado_factura WHERE id_unico = $id_unico";
                $res = $this->mysqli->query($sql);
                if($res->num_rows > 0){
                    $row = mysqli_fetch_row($res);
                    $nom = $row[0];
                }
                return $nom;
            }
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerTipoCompania($id_unico){
        try {
            $id_ = 0;
            $sql = "SELECT tipo_compania FROM gf_tercero WHERE id_unico = $id_unico";
            $res = $this->mysqli->query($sql);
            if($res->num_rows > 0){
                $row = mysqli_fetch_row($res);
                $id_ = $row[0];
            }
            return $id_;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerClaseFactura($id_unico){
        try {
            $id_ = 0;
            $sql = "SELECT clase_factura FROM gp_tipo_factura WHERE id_unico = $id_unico";
            $res = $this->mysqli->query($sql);
            if($res->num_rows > 0){
                $row = mysqli_fetch_row($res);
                $id_ = $row[0];
            }
            return TRIM($id_);
        } catch (Exception $e) {
            die($e->getMessage());
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

    public function obtenerDependenciasResponsable($tercero){
        try {
            $str = "SELECT    dep.id_unico, CONCAT_WS(' ', sigla, dep.nombre)
                    FROM      gf_dependencia_responsable as dpr
                    LEFT JOIN gf_tercero                 as ter ON dpr.responsable = ter.id_unico
                    LEFT JOIN gf_dependencia             as dep ON dpr.dependencia = dep.id_unico
                    WHERE     dpr.responsable = $tercero
                    AND       dep.xFactura    = 0
                    OR        dep.tipodependencia != 1";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerDependencias(){
        try {
            $str = "SELECT id_unico, CONCAT_WS(' ',sigla, nombre) FROM gf_dependencia ORDER BY nombre ASC";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerDataTercero($id){
        try {
            $str = "SELECT    ter.id_unico,
                              (
                                IF(
                                    CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) = '',
                                    ter.razonsocial,
                                    CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos)
                                )
                              )
                    FROM      gf_tercero as ter
                    LEFT JOIN gf_tipo_identificacion as tpi ON ter.tipoidentificacion = tpi.id_unico
                    WHERE     ter.id_unico = $id";
            $res = $this->mysqli->query($str);
            return $res->fetch_row();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function buscarNumeroMaximo($tipo, $param){
        try {
            $xxx = 0;
            $str = "SELECT MAX(numero) FROM gf_movimiento WHERE  tipomovimiento = $tipo AND parametrizacionanno = $param";
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

    public function validarNumero($tipo, $param){
        $xxx = $this->buscarNumeroMaximo($tipo, $param);
        if(empty($xxx)){
            $anno = $this->obtenerAnnoParam($param);
            $num  = $anno.'000001';
        }else{
            $num  = $xxx + 1;
        }
        return $num;
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

    public function buscarComprobantesFactura($factura){
        try {
            $str = "SELECT      cnt.id_unico as cnt, ptal.id_unico as ptal
                    FROM        gp_factura pg, gp_tipo_factura tpg, gf_tipo_comprobante tpc,gf_comprobante_cnt cnt, gf_tipo_comprobante_pptal tcp,gf_comprobante_pptal ptal
                    WHERE       pg.tipofactura        = tpg.id_unico
                    AND         tpc.id_unico          = tpg.tipo_comprobante
                    AND         cnt.tipocomprobante   = tpc.id_unico
                    AND         tpc.comprobante_pptal = tcp.id_unico
                    AND         ptal.tipocomprobante  = tcp.id_unico
                    AND         pg.numero_factura     = ptal.numero
                    AND         pg.numero_factura     = cnt.numero
                    AND         pg.id_unico           =  $factura";
            $res = $this->mysqli->query($str);
            return $res->fetch_row();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function buscarMovFactura($factura){
        try {
            $str = "SELECT detallemovimiento FROM gp_detalle_factura WHERE factura = $factura";
            $res = $this->mysqli->query($str);
            return $res->fetch_row();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenercomprobanteCntFactura($factura){
        try {
            $str = "SELECT      dtc.comprobante
                    FROM        gp_detalle_factura dtf
                    LEFT JOIN   gf_detalle_comprobante dtc ON dtc.id_unico = dtf.detallecomprobante
                    WHERE       dtf.factura = $factura";
            $res = $this->mysqli->query($str);
            return $res->fetch_row();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerDetalles($factura){
        try {
            $str = "SELECT    gdf.id_unico, gdf.concepto_tarifa, gdf.valor, gdf.cantidad, gdf.iva, gdf.impoconsumo, gdf.ajuste_peso, gdf.valor_total_ajustado, UPPER(gct.nombre)
                    FROM      gp_detalle_factura AS gdf
                    LEFT JOIN gp_concepto AS gct ON gdf.concepto_tarifa = gct.id_unico
                    WHERE     md5(gdf.factura) = '$factura'";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerMovimientoFactura($clase){
        try {
            $xxx = 0;
            $str = "SELECT MIN(id_unico) FROM gp_tipo_factura WHERE clase_factura = $clase";
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

    public function guardarFact($num, $tipo, $tercero, $fecha, $estado, $responsable, $vendedor, $param, $centro){
        try {
            $str = "INSERT INTO gp_factura (numero_factura, tipofactura, tercero, fecha_factura, fecha_vencimiento,  estado_factura, responsable, vendedor, parametrizacionanno, centrocosto)
                    VALUES ($num, $tipo, $tercero, '$fecha', '$fecha', $estado, $responsable, $vendedor, $param, $centro)";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function validarNumeroFactura($tipo, $param){
        try {
            $xxx = $this->obtenerMaximoFactura($tipo,$param);
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

    public function obtenerMaximoFactura($tipo, $param){
        try {
            $xxx = 0;
            $str = "SELECT MAX(numero_factura) FROM gp_factura WHERE tipofactura = $tipo AND parametrizacionanno = $param";
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

    public function obtenerCantidadDetalles($factura){
        try {
            $xxx = 0;
            $str = "SELECT COUNT(*) FROM gp_detalle_factura WHERE factura = $factura";
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

    public function obtenerDetallesFactura($factura){
        try {
            $str = "SELECT    dtf.id_unico, dtf.concepto_tarifa, pln.codi, pln.nombre, dtf.cantidad, dtf.iva, dtf.impoconsumo,
                              dtf.valor, dtf.valor_total_ajustado, dtf.ajuste_peso, dtf.detallemovimiento, dtf.factura, dtm.unidad_origen, pln.id_unico,
                              dtm.cantidad_origen, dtm.valor, guf.nombre
                    FROM      gp_detalle_factura    AS dtf
                    LEFT JOIN gp_concepto           AS con ON dtf.concepto_tarifa   = con.id_unico
                    LEFT JOIN gf_plan_inventario    AS pln ON con.plan_inventario   = pln.id_unico
                    LEFT JOIN gf_detalle_movimiento AS dtm ON dtf.detallemovimiento = dtm.id_unico
                    LEFT JOIN gf_unidad_factor      AS guf ON dtm.unidad_origen     = guf.id_unico
                    WHERE     factura = $factura
                    ORDER BY  dtf.id_unico DESC";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerValorProducto($concepto){
        try {
            $dat = array();
            $str = "SELECT    gtr.valor, gtr.porcentaje_iva, gtr.porcentaje_impoconsumo
                    FROM      gp_concepto_tarifa AS gct
                    LEFT JOIN gp_tarifa          AS gtr ON gct.tarifa = gtr.id_unico
                    WHERE gct.concepto = $concepto";
            $res = $this->mysqli->query($str);
            if($res->num_rows > 0){
                $row = $res->fetch_row();
                $dat['valor'] = $row[0];
                $dat['iva']   = $row[1];
                $dat['impo']  = $row[2];
            }
            return $dat;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtener($id){
        try {
            $data = array();
            $str  = "SELECT    tpf.nombre, fat.numero_factura, tpf.resolucion, fat.estado_factura,
                               (
                                IF(
                                    ter.nombre_comercial = ' ',
                                    IF(
                                      CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) = ' ',
                                      ter.razonsocial,
                                      CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos)
                                    ),
                                    IF(
                                      CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) = ' ',
                                      CONCAT_WS(' ', ter.razonsocial, ter.nombre_comercial),
                                      CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos, ter.nombre_comercial)
                                    )
                                )
                               ),
                               ter.numeroidentificacion, dir.direccion, tel.valor, DATE_FORMAT(fat.fecha_factura,'%d/%m/%Y')
                     FROM      gp_factura      AS fat
                     LEFT JOIN gp_tipo_factura AS tpf ON fat.tipofactura = tpf.id_unico
                     LEFT JOIN gf_tercero      AS ter ON fat.tercero     = ter.id_unico
                     LEFT JOIN gf_direccion    AS dir ON dir.tercero     = ter.id_unico
                     LEFT JOIN gf_telefono     AS tel ON tel.tercero     = ter.id_unico
                     WHERE     fat.id_unico = $id";
            $res = $this->mysqli->query($str);
            if($res->num_rows > 0){
                $row = $res->fetch_row();
                $data['tipo']     = $row[0];
                $data['num']      = $row[1];
                $data['res']      = $row[2];
                $data['estd']     = $row[3];
                $data['cliente']  = $row[4];
                $data['doc']      = $row[5];
                $data['dir']      = $row[6];
                $data['tel']      = $row[7];
                $data['fecha']    = $row[8];
            }
            return $data;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerFacturasFechaa($fechaI, $fechaF){
        try {
            $str = "SELECT fat.id_unico, fat.numero_factura, DATE_FORMAT(fat.fecha_factura, '%d/%m/%Y')
                    FROM   gp_factura AS fat
                    WHERE  fat.fecha_factura BETWEEN '$fechaI' AND '$fechaF'";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerValorFactura($id){
        try {
            $xxx = 0;
            $str = "SELECT valor_total_ajustado FROM gp_detalle_factura WHERE factura = $id";
            $res = $this->mysqli->query($str);
            if($res->num_rows > 0){
                foreach($res->fetch_all(MYSQLI_NUM) as $row){
                    $xxx += $row[0];
                }
            }
            return $xxx;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function cambiarFechaFactura($id, $fecha){
        try {
            $str = "UPDATE gp_factura SET fecha_factura = '$fecha' WHERE id_unico = $id";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerMovAlmacen($factura){
        try {
            $xxx = 0;
            $str = "SELECT    gdm.movimiento
                    FROM      gp_detalle_factura    AS gpd
                    LEFT JOIN gf_detalle_movimiento AS gdm ON gpd.detallemovimiento = gdm.id_unico
                    WHERE     gpd.factura = $factura
                    AND       gdm.id_unico IS NOT NULL";
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

    public function obtenerRelacionFactura($factura){
        try {
            $str = "SELECT
                cnt.id_unico AS cnt,
                ptal.id_unico AS ptal
            FROM
                gp_factura pg
            LEFT JOIN gp_tipo_factura tpg ON
                pg.tipofactura = tpg.id_unico
            LEFT JOIN gf_tipo_comprobante tpc ON
                tpc.id_unico = tpg.tipo_comprobante
            LEFT JOIN gf_comprobante_cnt cnt ON
                cnt.tipocomprobante = tpc.id_unico AND pg.numero_factura = cnt.numero
            LEFT JOIN gf_tipo_comprobante_pptal tcp ON
                tpc.comprobante_pptal = tcp.id_unico
            LEFT JOIN gf_comprobante_pptal ptal ON
                ptal.tipocomprobante = tcp.id_unico AND pg.numero_factura = ptal.numero
            WHERE 
                pg.id_unico  = $factura";
            $res = $this->mysqli->query($str);
            return $res->fetch_row();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function buscarRelacionCnt($factura){
        try {
            $xxx = 0;
            $str = "SELECT    dtc.comprobante FROM gp_detalle_factura dtf
                    LEFT JOIN gf_detalle_comprobante dtc ON dtc.id_unico = dtf.detallecomprobante
                    WHERE     dtf.factura = $factura
                    AND       dtc.comprobante IS NOT NULL";
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

    public function obtenerTarifasElementosD($elemento){
        try {
            $str = "SELECT    gct.id_unico, gtr.valor, gtr.porcentaje_iva, gtr.porcentaje_impoconsumo, gun.id_unico, gun.nombre,
                              glu.valor_conversion, gct.porcentajeI, gtr.id_unico
                    FROM      gp_concepto_tarifa AS gct
                    LEFT JOIN gp_tarifa          AS gtr ON gct.tarifa          = gtr.id_unico
                    LEFT JOIN gf_elemento_unidad AS glu ON gct.elemento_unidad = glu.id_unico
                    LEFT JOIN gf_unidad_factor   AS gun ON glu.unidad_empaque  = gun.id_unico
                    LEFT JOIN gp_concepto        AS gcn ON gct.concepto        = gcn.id_unico
                    WHERE     gcn.plan_inventario  = $elemento";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerInfoConceptoTarifa($id){
        try {
            $data = array();
            $str  = "SELECT    gtr.valor, gtr.porcentaje_iva, gtr.porcentaje_impoconsumo, gun.id_unico, gun.nombre, glu.valor_conversion, gcn.id_unico
                     FROM      gp_concepto_tarifa AS gct
                     LEFT JOIN gp_tarifa          AS gtr ON gct.tarifa          = gtr.id_unico
                     LEFT JOIN gf_elemento_unidad AS glu ON gct.elemento_unidad = glu.id_unico
                     LEFT JOIN gf_unidad_factor   AS gun ON glu.unidad_empaque  = gun.id_unico
                     LEFT JOIN gp_concepto        AS gcn ON gct.concepto        = gcn.id_unico
                     WHERE     gct.id_unico       = $id";
            $res  = $this->mysqli->query($str);
            if($res->num_rows > 0){
                $row  = $res->fetch_row();
                $data['valor']    = $row[0];
                $data['iva']      = $row[1];
                $data['impo']     = $row[2];
                $data['factor']   = $row[5];
                $data['concepto'] = $row[6];
                $data['unidad']   = $row[3];
            }
            return $data;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerDetalleMov($id){
        try {
            $xxx = array();
            $str = "SELECT dtf.detallemovimiento, dtm.cantidad, dtm.valor
                    FROM gp_detalle_factura AS dtf
                    LEFT JOIN gf_detalle_movimiento AS dtm ON dtf.detallemovimiento = dtm.id_unico
                    WHERE dtf.id_unico = $id";
            $res = $this->mysqli->query($str);
            if($res->num_rows > 0){
                $row = $res->fetch_row();
                $xxx['id']       = $row[0];
                $xxx['cantidad'] = $row[1];
                $xxx['valor']    = $row[2];
            }
            return $xxx;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function actualizarDetalleFac($cantidad, $iva, $impo, $valor, $total, $id){
        try {
            $str = "UPDATE gp_detalle_factura SET cantidad = $cantidad, iva = $iva, impoconsumo = $impo, valor = $valor, valor_total_ajustado = $total WHERE id_unico = $id";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerFacturaDetalle($id){
        try {
            $xxx = 0;
            $str = "SELECT factura FROM gp_detalle_factura WHERE id_unico = $id";
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

    public function obtenerUnidadMinimaPlan($id){
        try {
            $xxx = 0;
            $str = "SELECT unidad FROM gf_plan_inventario WHERE id_unico = $id";
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

    public function obtenerUnidadesConcepto($concepto){
        try {
            $str = "SELECT    gun.id_unico, gun.nombre
                    FROM      gp_concepto_tarifa AS gtr
                    LEFT JOIN gf_elemento_unidad AS gel ON gtr.elemento_unidad = gel.id_unico
                    LEFT JOIN gf_unidad_factor   AS gun ON gel.unidad_empaque  = gun.id_unico
                    WHERE     gtr.concepto = $concepto";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function buscarFacturasFecha($fecha, $clase){
        try {
            $str = "SELECT    fat.id_unico, fat.numero_factura
                    FROM      gp_factura      AS fat
                    LEFT JOIN gp_tipo_factura AS tpf ON fat.tipofactura = tpf.id_unico
                    WHERE     fat.fecha_factura = '$fecha'
                    AND       tpf.clase_factura =  $clase
                    ORDER BY  fat.fecha_factura ASC";
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

    public function actualizarTercero($id, $tercero){
        try {
            $str = "UPDATE gp_factura SET tercero = $tercero WHERE id_unico = $id";
            return $this->mysqli->query($str);
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

    public function cambiarEstadoFactura($factura, $estado){
        try {
            $str = "UPDATE gp_factura SET estado_factura = $estado WHERE id_unico = $factura";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function CambiarRemision($factura, $numero, $tipo){
        try {
            $str = "UPDATE gp_factura SET numero_factura = $numero, tipofactura = $tipo WHERE id_unico = $factura";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerClaseFactura($factura){
        try {
            $xxx = 0;
            $str = "SELECT    gtp.clase_factura
                    FROM      gp_factura      AS gft
                    LEFT JOIN gp_tipo_factura AS gtp ON gft.tipofactura = gtp.id_unico
                    WHERE     gft.id_unico = $factura";
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

    public function obtenerTipoRecaudo($factura){
        $xxx = 0;
        $str = "SELECT tipo_recaudo FROM gp_tipo_factura WHERE id_unico = $factura";
        $res = $this->mysqli->query($str);
        if($res->num_rows > 0){
            $row = $res->fetch_row();
            $xxx = $row[0];
        }
        return $xxx;
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

    public function obtenerRelacioncontableCnt($pago){
        try {
            $data = array();
            $str  = "SELECT   gdc.comprobante, gdt.comprobantepptal
                    FROM      gp_detalle_pago              AS gdp
                    LEFT JOIN gf_detalle_comprobante       AS gdc ON gdp.detallecomprobante      = gdc.id_unico
                    LEFT JOIN gf_detalle_comprobante_pptal AS gdt ON gdc.detallecomprobantepptal = gdt.id_unico
                    WHERE     gdp.pago = $pago";
            $res = $this->mysqli->query($str);
            if($res->num_rows > 0){
                $row         = $res->fetch_row();
                $data['cnt'] = $row[0];
                $data['pto'] = $row[1];
            }
            return $data;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerTiposComprobantes($tipo){
        try {
            $data = array();
            $str  = "SELECT    gtp.id_unico, gtc.id_unico, gtc.comprobante_pptal
                     FROM      gp_tipo_factura     AS gtf
                     LEFT JOIN gp_tipo_pago        AS gtp ON gtf.tipo_recaudo     = gtp.id_unico
                     LEFT JOIN gf_tipo_comprobante AS gtc ON gtp.tipo_comprobante = gtc.id_unico
                     WHERE     gtf.id_unico = $tipo";
            $res = $this->mysqli->query($str);
            if($res->num_rows > 0){
                $row               = $res->fetch_row();
                $data['tipo_pago'] = $row[0];
                $data['tipo_cnt']  = $row[1];
                $data['tipo_pto']  = $row[2];
            }
            return $data;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function buscarIndicador($tipo){
        try {
            $xxx = 0;
            $str = "SELECT xDescuento FROM gp_tipo_factura WHERE id_unico = $tipo";
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

    public function obtenerUnidadConceptoTarifa($concepto, $unidad){
        try {
            $str = "SELECT    gtf.valor, gtr.id_unico
                    FROM      gp_concepto_tarifa AS gtr
                    LEFT JOIN gf_elemento_unidad AS geu ON gtr.elemento_unidad = geu.id_unico
                    LEFT JOIN gp_tarifa          AS gtf ON gtr.tarifa          = gtf.id_unico
                    WHERE     gtr.concepto       = $concepto
                    AND       geu.unidad_empaque = $unidad ORDER BY  gtf.valor DESC";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function InsertarData($numero_factura, $tipofactura, $tercero, $fecha_factura, $fecha_vencimiento, $centrocosto, $descripcion,
                                 $estado_factura, $responsable, $vendedor, $parametrizacionanno, $descuento){
        try {
            $str = "INSERT INTO gp_factura(
                        numero_factura, tipofactura, tercero, fecha_factura, fecha_vencimiento, centrocosto, descripcion,
                        estado_factura, responsable, vendedor, parametrizacionanno, descuento
                      ) VALUES(
                        $numero_factura, $tipofactura, $tercero, '$fecha_factura', '$fecha_vencimiento', $centrocosto,
                        '$descripcion', $estado_factura, $responsable, $vendedor, $parametrizacionanno, $descuento
                      )";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtenerIdElementoUnidad($concepto, $unidad){
        try {
            $xxx = 0;
            $str = "SELECT    gel.id_unico
                    FROM      gp_concepto_tarifa AS gtr
                    LEFT JOIN gf_elemento_unidad AS gel ON gtr.elemento_unidad = gel.id_unico
                    WHERE     gtr.concepto       = $concepto
                    AND       gel.unidad_empaque = $unidad";
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

    public function modificarElementoUnidad($id, $factor, $unidad){
        try {
            $str = "UPDATE gf_elemento_unidad SET unidad_empaque = $unidad, valor_conversion = $factor WHERE id_unico = $id";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function actualizarVendedor($id, $tercero){
        try {
            $str = "UPDATE gp_factura SET vendedor = $tercero WHERE id_unico = $id";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerUnidadElemento($elemento){
        try {
            $xxx = 0;
            $str = "SELECT unidad FROM gf_plan_inventario where  id_unico = $elemento";
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

    public function obtenerTiposClase($clase, $orden){
        try {
            $str = "SELECT gtp.id_unico, CONCAT_WS(' ', gtp.nombre, gtp.prefijo)
                    FROM   gp_tipo_factura AS gtp
                    WHERE  gtp.clase_factura IN ($clase)
                    ORDER BY gtp.id_unico $orden";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerListadoConceptos($orden, $param){
        try {
            $str = "SELECT id_unico, nombre FROM gp_concepto WHERE compania = $param ORDER BY id_unico $orden";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
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

    public function listdaoFacturas($fechaI, $fechaF, $tipoI, $tipoF, $clase){
        try {
            $str = "SELECT    gpf.id_unico, DATE_FORMAT(gpf.fecha_factura, '%d/%m/%Y'), gtf.prefijo, gpf.numero_factura, gpf.descripcion,
                              (
                                IF(
                                  CONCAT_WS(' ',gtr.nombreuno, gtr.nombredos, gtr.apellidouno, gtr.apellidodos) = '',
                                  gtr.razonsocial,
                                  CONCAT_WS(' ',gtr.nombreuno, gtr.nombredos, gtr.apellidouno, gtr.apellidodos)
                                )
                              )
                    FROM      gp_factura      AS gpf
                    LEFT JOIN gp_tipo_factura AS gtf ON gpf.tipofactura = gtf.id_unico
                    LEFT JOIN gf_tercero      AS gtr ON gpf.tercero     = gtr.id_unico
                    WHERE     gpf.fecha_factura BETWEEN '$fechaI' AND '$fechaF'
                    AND       gtf.id_unico   BETWEEN $tipoI AND $tipoF
                    AND       gtf.clase_factura IN ($clase)";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function listdaoFacturasDetalle($fechaI, $fechaF, $clase){
        try {
            $str = "SELECT    gpf.id_unico, DATE_FORMAT(gpf.fecha_factura, '%d/%m/%Y'), gtf.prefijo, gpf.numero_factura, gpf.descripcion,
                              (
                                IF(
                                  CONCAT_WS(' ',gtr.nombreuno, gtr.nombredos, gtr.apellidouno, gtr.apellidodos) = '',
                                  gtr.razonsocial,
                                  CONCAT_WS(' ',gtr.nombreuno, gtr.nombredos, gtr.apellidouno, gtr.apellidodos)
                                )
                              )
                    FROM      gp_factura      AS gpf
                    LEFT JOIN gp_tipo_factura AS gtf ON gpf.tipofactura = gtf.id_unico
                    LEFT JOIN gf_tercero      AS gtr ON gpf.tercero     = gtr.id_unico
                    WHERE     gpf.fecha_factura BETWEEN '$fechaI' AND '$fechaF'
                    AND       gtf.clase_factura IN ($clase)";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerListadoConceptosFactura($conceptoI, $conceptoF){
        try {
            $str = "SELECT    gdf.concepto_tarifa, UPPER(gct.nombre)
                    FROM      gp_detalle_factura AS gdf
                    LEFT JOIN gp_concepto        AS gct ON gdf.concepto_tarifa = gct.id_unico
                    WHERE     gdf.concepto_tarifa BETWEEN $conceptoI AND $conceptoF
                    GROUP BY  gct.id_unico";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function listdaoFacturasConcepto($fechaI, $fechaF, $concepto, $clase){
        try {
            $str = "SELECT    gpf.id_unico, DATE_FORMAT(gpf.fecha_factura, '%d/%m/%Y'), gtf.prefijo, gpf.numero_factura, gpf.descripcion,
                              (
                                IF(
                                  CONCAT_WS(' ',gtr.nombreuno, gtr.nombredos, gtr.apellidouno, gtr.apellidodos) = '',
                                  gtr.razonsocial,
                                  CONCAT_WS(' ',gtr.nombreuno, gtr.nombredos, gtr.apellidouno, gtr.apellidodos)
                                )
                              )
                    FROM      gp_detalle_factura AS gdf
                    LEFT JOIN gp_factura         AS gpf ON gdf.factura  = gpf.id_unico
                    LEFT JOIN gp_tipo_factura    AS gtf ON gpf.tipofactura = gtf.id_unico
                    LEFT JOIN gf_tercero         AS gtr ON gpf.tercero     = gtr.id_unico
                    WHERE     gpf.fecha_factura BETWEEN '$fechaI' AND '$fechaF'
                    AND       gtf.clase_factura   IN ($clase)
                    AND       gdf.concepto_tarifa = $concepto
                    GROUP BY  gpf.id_unico";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerDetallesConcepto($factura, $concepto){
        try {
            $str = "SELECT    gdf.id_unico, gdf.concepto_tarifa, gdf.valor, gdf.cantidad, gdf.iva, gdf.impoconsumo, gdf.ajuste_peso, gdf.valor_total_ajustado, UPPER(gct.nombre)
                    FROM      gp_detalle_factura AS gdf
                    LEFT JOIN gp_concepto AS gct ON gdf.concepto_tarifa = gct.id_unico
                    WHERE     md5(gdf.factura)    = '$factura'
                    AND       gdf.concepto_tarifa = $concepto";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerListadoTercerosFactura($terI, $terF){
        $str = "SELECT    gtr.id_unico,
                          CONCAT_WS(' ',
                            (
                              IF(
                                CONCAT_WS(' ', gtr.nombreuno, gtr.nombredos, gtr.apellidouno, gtr.apellidodos) =' ',
                                gtr.razonsocial,
                                CONCAT_WS(' ', gtr.nombreuno, gtr.nombredos, gtr.apellidouno, gtr.apellidodos)
                              )
                            ),
                            UPPER(gti.nombre),
                            CONCAT_WS(' ', gtr.numeroidentificacion, gtr.digitoverficacion)
                          )
                FROM      gp_factura             AS gft
                LEFT JOIN gf_tercero             AS gtr ON gft.tercero = gtr.id_unico
                LEFT JOIN gf_tipo_identificacion AS gti ON gtr.tipoidentificacion = gti.id_unico
                WHERE     gtr.id_unico BETWEEN $terI AND $terF
                GROUP BY  gtr.id_unico";
        $res = $this->mysqli->query($str);
        return $res->fetch_all(MYSQLI_NUM);
    }

    public function listdaoFacturasTercero($fechaI, $fechaF, $tercero, $clase){
        try {
            $str = "SELECT    gpf.id_unico, DATE_FORMAT(gpf.fecha_factura, '%d/%m/%Y'), gtf.prefijo, gpf.numero_factura, gpf.descripcion,
                              (
                                IF(
                                  CONCAT_WS(' ',gtr.nombreuno, gtr.nombredos, gtr.apellidouno, gtr.apellidodos) = '',
                                  gtr.razonsocial,
                                  CONCAT_WS(' ',gtr.nombreuno, gtr.nombredos, gtr.apellidouno, gtr.apellidodos)
                                )
                              )
                    FROM      gp_factura      AS gpf
                    LEFT JOIN gp_tipo_factura AS gtf ON gpf.tipofactura = gtf.id_unico
                    LEFT JOIN gf_tercero      AS gtr ON gpf.tercero     = gtr.id_unico
                    WHERE     gpf.fecha_factura BETWEEN '$fechaI' AND '$fechaF'
                    AND       gtf.clase_factura   IN ($clase)
                    AND       gpf.tercero         = $tercero";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerValorRecaudoFactura($factura){
        try {
            $xxx = 0;
            $str = "SELECT    (gdp.valor + gdp.iva + gdp.impoconsumo + gdp.ajuste_peso)
                    FROM      gp_detalle_pago AS gdp
                    LEFT JOIN gp_detalle_factura AS gdf ON gdp.detalle_factura = gdf.id_unico
                    WHERE     gdf.factura = $factura";
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

    public function ListadoFacturasClaseOrden($clase, $orden, $param){
        try {
            $str = "SELECT      fat.id_unico,
                                CONCAT_WS(' ',tpf.prefijo, fat.numero_factura),
                                IF(CONCAT(IF(ter.nombreuno='','',ter.nombreuno),' ',IF(ter.nombredos IS NULL,'',ter.nombredos),' ',IF(ter.apellidouno IS NULL,'',IF(ter.apellidouno IS NULL,'',ter.apellidouno)),' ',IF(ter.apellidodos IS NULL,'',ter.apellidodos))='' OR CONCAT(IF(ter.nombreuno='','',ter.nombreuno),' ',IF(ter.nombredos IS NULL,'',ter.nombredos),' ',IF(ter.apellidouno IS NULL,'',IF(ter.apellidouno IS NULL,'',ter.apellidouno)),' ',IF(ter.apellidodos IS NULL,'',ter.apellidodos)) IS NULL ,(ter.razonsocial),                                            CONCAT(IF(ter.nombreuno='','',ter.nombreuno),' ',IF(ter.nombredos IS NULL,'',ter.nombredos),' ',IF(ter.apellidouno IS NULL,'',IF(ter.apellidouno IS NULL,'',ter.apellidouno)),' ',IF(ter.apellidodos IS NULL,'',ter.apellidodos))) AS 'NOMBRE',
                                CONCAT_WS(' ',ti.nombre, ter.numeroidentificacion, ter.digitoverficacion) AS 'TipoD',
                                DATE_FORMAT(fat.fecha_factura, '%d/%m/%Y')
                    FROM        gp_factura             AS fat
                    LEFT JOIN   gp_tipo_factura        AS tpf ON tpf.id_unico = fat.tipofactura
                    LEFT JOIN   gf_tercero             AS ter ON ter.id_unico = fat.tercero
                    LEFT JOIN   gf_tipo_identificacion AS ti  ON ti.id_unico  = ter.tipoidentificacion
                    WHERE       tpf.clase_factura       IN ($clase)
                    AND         fat.parametrizacionanno = $param
                    ORDER BY    fat.id_unico $orden";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerListadoFacturasClase($fechaI, $fechaF, $fatI, $fatF, $clase){
        try {
            $str = "SELECT    gft.id_unico,
                              CONCAT_WS(' ', gtf.prefijo, gft.numero_factura),
                              DATE_FORMAT(gft.fecha_factura, '%d/%m/%Y'),
                              (
                                CONCAT_WS(' ',
                                  IF(
                                    CONCAT_WS(' ', gtr.nombreuno, gtr.nombredos, gtr.apellidouno, gtr.apellidodos) = ' ',
                                    gtr.razonsocial,
                                    CONCAT_WS(' ', gtr.nombreuno, gtr.nombredos, gtr.apellidouno, gtr.apellidodos)
                                  ),
                                  CONCAT_WS(' ', gti.nombre, gtr.numeroidentificacion, gtr.digitoverficacion)
                                )
                              )
                    FROM      gp_factura             AS gft
                    LEFT JOIN gp_tipo_factura        AS gtf ON gft.tipofactura        = gtf.id_unico
                    LEFT JOIN gf_tercero             AS gtr ON gft.tercero            = gtr.id_unico
                    LEFT JOIN gf_tipo_identificacion AS gti ON gtr.tipoidentificacion = gti.id_unico
                    WHERE     gft.id_unico      BETWEEN $fatI AND $fatF
                    AND       gft.fecha_factura BETWEEN '$fechaI' AND '$fechaF'
                    AND       gtf.clase_factura IN ($clase)";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerValorTotalFactura($factura){
        try {
            $xxx = 0;
            $str = "SELECT (valor + iva + impoconsumo + ajuste_peso) * cantidad FROM gp_detalle_factura WHERE factura = $factura";
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

    public function obtenerRecaudosFactura($factura){
        try {
            $str = "SELECT    DATE_FORMAT(gpg.fecha_pago, '%d/%m/%Y'),
                              gtp.nombre, gpg.numero_pago, gdp.valor,
                              gdp.iva, gdp.impoconsumo, gdp.ajuste_peso
                    FROM      gp_detalle_pago    AS gdp
                    LEFT JOIN gp_pago            AS gpg ON gdp.pago            = gpg.id_unico
                    LEFT JOIN gp_tipo_pago       AS gtp ON gpg.tipo_pago       = gtp.id_unico
                    LEFT JOIN gp_detalle_factura AS gft ON gdp.detalle_factura = gft.id_unico
                    WHERE     gft.factura = $factura";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerListadoProductosOrden($orden){
        try {
            $str = "SELECT    gpl.id_unico, CONCAT_WS(' ', gpl.codi, UPPER(gpl.nombre))
                    FROM      gp_concepto AS gct
                    LEFT JOIN gf_plan_inventario AS gpl ON gct.plan_inventario = gpl.id_unico
                    ORDER BY  gpl.id_unico $orden";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function listadoFacturasClase($fechaI, $fechaF, $clase){
        try {
            $str = "SELECT    gft.id_unico, UPPER(gct.nombre), SUM(gpd.valor * gpd.cantidad), SUM(gpd.cantidad), UPPER(gun.nombre),
                              SUM(gdm.cantidad), SUM(gdm.valor * gdm.cantidad), UPPER(gum.nombre)
                    FROM      gp_detalle_factura    AS gpd
                    LEFT JOIN gp_factura            AS gft ON gpd.factura           = gft.id_unico
                    LEFT JOIN gp_tipo_factura       AS gtf ON gft.tipofactura       = gtf.id_unico
                    LEFT JOIN gp_concepto           AS gct ON gpd.concepto_tarifa   = gct.id_unico
                    LEFT JOIN gf_detalle_movimiento AS gdm ON gpd.detallemovimiento = gdm.id_unico
                    LEFT JOIN gf_unidad_factor      AS gun ON gdm.unidad_origen     = gun.id_unico
                    LEFT JOIN gf_plan_inventario    AS gpl ON gct.plan_inventario   = gpl.id_unico
                    LEFT JOIN gf_unidad_factor      AS gum ON gpl.unidad            = gum.id_unico
                    WHERE     gft.fecha_factura BETWEEN '$fechaI' AND '$fechaF'
                    AND       gtf.clase_factura IN ($clase)
                    GROUP BY  gct.id_unico";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function listadoProductosFechaPlanI($fechaI, $fechaF, $prodI, $podF, $clase){
        try {
            $str = "SELECT    DATE_FORMAT(gft.fecha_factura, '%d/%m/%Y'), UPPER(gct.nombre),
                              SUM(gdf.valor * gdf.cantidad), SUM(gdf.cantidad), UPPER(gun.nombre), UPPER(gum.nombre),
                              SUM(gdm.cantidad), SUM(gdm.valor * gdm.cantidad), gun.id_unico, gum.id_unico,
                              gdm.planmovimiento, gft.id_unico
                    FROM      gp_detalle_factura    AS gdf
                    LEFT JOIN gf_detalle_movimiento AS gdm ON gdf.detallemovimiento = gdm.id_unico
                    LEFT JOIN gp_factura            AS gft ON gdf.factura           = gft.id_unico
                    LEFT JOIN gf_plan_inventario    AS gpl ON gdm.planmovimiento    = gpl.id_unico
                    LEFT JOIN gp_tipo_factura       AS gtf ON gft.tipofactura       = gtf.id_unico
                    LEFT JOIN gp_concepto           AS gct ON gdf.concepto_tarifa   = gct.id_unico
                    LEFT JOIN gf_unidad_factor      AS gun ON gdm.unidad_origen     = gun.id_unico
                    LEFT JOIN gf_unidad_factor      AS gum ON gpl.unidad            = gum.id_unico
                    WHERE     (gft.fecha_factura BETWEEN '$fechaI' AND '$fechaF')
                    AND       (gpl.id_unico      BETWEEN $prodI    AND $podF)
                    AND       (gtf.clase_factura IN ($clase))
                    GROUP BY  gft.fecha_factura, gct.id_unico
                    ORDER BY  gft.fecha_factura, gct.nombre ASC ";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function listadoProductosCostoFechaTipoVendedorClase($fechaI, $fechaF, $tipoI, $tipoF, $vendI, $vendF, $clase){
        try {
            $str = "SELECT    DATE_FORMAT(gft.fecha_factura, '%d/%m/%Y'), UPPER(gtf.prefijo), gft.numero_factura,
                              UPPER(gct.nombre), UPPER(gun.nombre), gdf.cantidad, (gdf.valor * gdf.cantidad),
                              UPPER(gum.nombre), gdm.cantidad, gdm.valor, (gdm.valor * gdm.cantidad), gdm.planmovimiento,
                              gun.id_unico, gdm.id_unico, gum.id_unico
                    FROM      gp_detalle_factura    AS gdf
                    LEFT JOIN gp_factura            AS gft ON gdf.factura           = gft.id_unico
                    LEFT JOIN gp_concepto           AS gct ON gdf.concepto_tarifa   = gct.id_unico
                    LEFT JOIN gf_detalle_movimiento AS gdm ON gdf.detallemovimiento = gdm.id_unico
                    LEFT JOIN gp_tipo_factura       AS gtf ON gft.tipofactura       = gtf.id_unico
                    LEFT JOIN gf_unidad_factor      AS gun ON gdm.unidad_origen     = gun.id_unico
                    LEFT JOIN gf_plan_inventario    AS gpl ON gdm.planmovimiento    = gpl.id_unico
                    LEFT JOIN gf_unidad_factor      AS gum ON gpl.unidad            = gum.id_unico
                    WHERE     gft.fecha_factura BETWEEN '$fechaI' AND '$fechaF'
                    AND       gft.tipofactura   BETWEEN $tipoI    AND $tipoF
                    AND       gft.vendedor      BETWEEN $vendI    AND $vendF
                    AND       gtf.clase_factura IN ($clase)
                    ORDER BY  gft.fecha_factura, gft.numero_factura";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerListadoCentrocosto($param, $compania){
        try {
            $str = "SELECT id_unico, UPPER(nombre) FROM gf_centro_costo WHERE parametrizacionanno = $param AND compania = $compania ORDER BY nombre DESC";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function listadoConceptos($param){
        try {
            $str = "SELECT    DISTINCTROW cnp.id_unico, CONCAT_WS(' ', pln.codi, UPPER(cnp.nombre)), unf.nombre
                    FROM      gp_concepto_tarifa AS cont
                    LEFT JOIN gp_concepto        AS cnp ON cont.concepto           = cnp.id_unico
                    LEFT JOIN gf_plan_inventario AS pln ON cnp.plan_inventario     = pln.id_unico
                    LEFT JOIN gf_unidad_factor   AS unf ON pln.unidad              = unf.id_unico
                    WHERE     cnp.id_unico IS NOT NULL AND cnp.parametrizacionanno = $param";
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

    public function listadoBancos($compania){
        try {
            $str = "SELECT    ctb.id_unico, CONCAT_WS(' ',ctb.numerocuenta, UPPER(ctb.descripcion))
                    FROM      gf_cuenta_bancaria         AS ctb
                    LEFT JOIN gf_cuenta_bancaria_tercero AS ctbt ON ctb.id_unico = ctbt.cuentabancaria
                    WHERE     ctbt.tercero = $compania
                    ORDER BY  ctb.numerocuenta";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerUnidadConceptoTarifaPrimero($concepto, $unidad){
        try {
            $xxx = 0;
            $str = "SELECT    gtf.valor
                    FROM      gp_concepto_tarifa AS gtr
                    LEFT JOIN gf_elemento_unidad AS geu ON gtr.elemento_unidad = geu.id_unico
                    LEFT JOIN gp_tarifa          AS gtf ON gtr.tarifa          = gtf.id_unico
                    WHERE     gtr.concepto       = $concepto
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

    public function obtenerDataConceptoTarifa($id_unico){
        try {
            $str = "SELECT tarifa, elemento_unidad, concepto FROM gp_concepto_tarifa WHERE  id_unico = $id_unico";
            $res = $this->mysqli->query($str);
            return $res->fetch_row();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function eliminarConceptoTarifa($id_unico){
        try {
            $str = "DELETE FROM gp_concepto_tarifa WHERE id_unico = $id_unico";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function eliminarTarifa($id_unico){
        try {
            $str = "DELETE FROM gp_tarifa WHERE id_unico = $id_unico";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function eliminarElementoUnidad($id_unico){
        try {
            $str = "DELETE FROM gf_elemento_unidad WHERE id_unico = $id_unico";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function eliminarConcepto($id_unico){
        try {
            $str = "DELETE FROM gp_concepto WHERE id_unico = $id_unico";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function GuardarFacturaPrecio($detalle, $concepto, $unidad, $precio_ant, $precio_act, $estado, $fecha, $usuario){
        try {
            $str = "INSERT INTO gf_precio_producto(detalle_mov, concepto_tarifa, unidad, precio_ant, precio_act, estado, fecha, usuario)
                                          VALUES ($detalle, $concepto, $unidad, $precio_ant, $precio_act, $estado, '$fecha', $usuario);";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function actualizarValorTarifa($id, $valor){
        try {
            $str = "UPDATE gp_tarifa SET valor = $valor WHERE id_unico = $id";
            return $res = $this->mysqli->query($str);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerListadoPrecios($estado){
        try {
            $str = "SELECT    gpr.id_unico, DATE_FORMAT(gpr.fecha, '%d/%m/%Y'), gpl.codi, UPPER(gpl.nombre),
                              UPPER(guf.nombre), gpr.precio_act, gpr.precio_ant, gdm.valor, gep.id_unico, UPPER(gep.nombre),
                              gpl.id_unico, guf.id_unico, gtr.id_unico, gdm.planmovimiento
                    FROM      gf_precio_producto    AS gpr
                    LEFT JOIN gf_detalle_movimiento AS gdm ON gpr.detalle_mov     = gdm.id_unico
                    LEFT JOIN gp_concepto_tarifa    AS gct ON gpr.concepto_tarifa = gct.id_unico
                    LEFT JOIN gp_tarifa             AS gtr ON gct.tarifa          = gtr.id_unico
                    LEFT JOIN gf_unidad_factor      AS guf ON gpr.unidad          = guf.id_unico
                    LEFT JOIN gf_estado_precio      AS gep ON gpr.estado          = gep.id_unico
                    LEFT JOIN gf_plan_inventario    AS gpl ON gdm.planmovimiento  = gpl.id_unico
                    WHERE     gpr.estado IN ($estado)
                    ORDER BY  gpr.id_unico DESC, gpl.id_unico, guf.nombre ASC";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerListadoPreciosFecha($estado, $fechaI, $fechaF){
        try {
            $str = "SELECT    gpr.id_unico, DATE_FORMAT(gpr.fecha, '%d/%m/%Y'), gpl.codi, UPPER(gpl.nombre),
                              UPPER(guf.nombre), gpr.precio_act, gpr.precio_ant, gdm.valor, gep.id_unico, UPPER(gep.nombre),
                              gpl.id_unico, guf.id_unico, gtr.id_unico, gdm.planmovimiento
                    FROM      gf_precio_producto    AS gpr
                    LEFT JOIN gf_detalle_movimiento AS gdm ON gpr.detalle_mov     = gdm.id_unico
                    LEFT JOIN gp_concepto_tarifa    AS gct ON gpr.concepto_tarifa = gct.id_unico
                    LEFT JOIN gp_tarifa             AS gtr ON gct.tarifa          = gtr.id_unico
                    LEFT JOIN gf_unidad_factor      AS guf ON gpr.unidad          = guf.id_unico
                    LEFT JOIN gf_estado_precio      AS gep ON gpr.estado          = gep.id_unico
                    LEFT JOIN gf_plan_inventario    AS gpl ON gdm.planmovimiento  = gpl.id_unico
                    WHERE     gpr.estado IN ($estado)
                    AND       gpr.fecha  BETWEEN '$fechaI' AND '$fechaF'
                    ORDER BY  gpr.id_unico DESC, gpl.id_unico, guf.nombre ASC";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerValorAnterior($unidad, $id, $plan){
        try {
            $str = "SELECT    gdm.valor
                    FROM      gf_precio_producto    AS gpr
                    LEFT JOIN gf_detalle_movimiento AS gdm on gpr.detalle_mov = gdm.id_unico
                    WHERE     gpr.unidad        = $unidad
                    AND       gpr.id_unico       < $id
                    AND       gdm.planmovimiento = $plan
                    ORDER BY  gpr.id_unico DESC LIMIT 1";
            $res = $this->mysqli->query($str);
            return $res->fetch_row();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function CambiarEstadoPrecio($id, $estado){
        try {
            $str = "UPDATE gf_precio_producto SET estado = $estado WHERE id_unico = $id";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function modificarPrecioEstado($id, $precio, $estado){
        try {
            $str = "UPDATE gf_precio_producto SET precio_act = $precio, estado = $estado WHERE id_unico = $id";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function modificarTarifa($id, $valor){
        try {
            $str = "UPDATE gp_tarifa SET valor = $valor WHERE id_unico = $id";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerValorConversionUnidadConcepto($concepto, $unidad){
        try {
            $xxx = 0;
            $str = "SELECT    gun.valor_conversion
                    FROM      gp_concepto_tarifa AS gct
                    LEFT JOIN gf_elemento_unidad AS gun ON gct.elemento_unidad = gun.id_unico
                    WHERE     gct.concepto       = $concepto
                    AND       gun.unidad_empaque = $unidad";
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

    public function obtenerDetallePlanInventario($fat, $plan){
        try {
            $xxx = 0;
            $str = "SELECT    gdm.id_unico
                    FROM      gp_detalle_factura    AS gdf
                    LEFT JOIN gf_detalle_movimiento AS gdm ON gdf.detallemovimiento = gdm.id_unico
                    LEFT JOIN gp_concepto           AS gct ON gdf.concepto_tarifa   = gct.id_unico
                    WHERE     gdf.factura         = $fat
                    AND       gct.plan_inventario = $plan";
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

    public function buscarAsociadosPlanInventario($id){
        try {
            $str = "SELECT gdm.valor
                    FROM   gf_detalle_movimiento AS gdm
                    WHERE  gdm.detalleasociado = $id";
            $res= $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerValorAsociadosSalida($padre){
        try {
            $xxx = 0;
            $str = "SELECT valor FROM gf_detalle_movimiento WHERE detalleasociado = $padre";
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

    public function obtenerValorConversionUnidadElemento($elemento, $unidad){
        try {
            $xxx = 0;
            $str = "SELECT    gun.valor_conversion
                    FROM      gp_concepto_tarifa AS gct
                    LEFT JOIN gf_elemento_unidad AS gun ON gct.elemento_unidad = gun.id_unico
                    LEFT JOIN gp_concepto        AS gcn ON gct.concepto = gcn.id_unico
                    WHERE     gcn.plan_inventario = $elemento
                    AND       gun.unidad_empaque  = $unidad";
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

    public function facturasFechaTipo($fechaI, $fechaF, $tipo){
        try {
            $str = "SELECT gpf.id_unico, gpf.numero_factura, gpf.fecha_factura, gpf.fecha_vencimiento, gpf.descripcion,
                           gpf.centrocosto, gpf.tercero
                    FROM   gp_factura AS gpf
                    WHERE  (gpf.fecha_factura BETWEEN '$fechaI' AND '$fechaF')
                    AND    (gpf.tipofactura = $tipo )";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function actualizarDetalleCnt($id, $detalle){
        try {
            $str = "UPDATE gp_detalle_factura SET detallecomprobante = $detalle WHERE  id_unico = $id";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerValorDetalleCnt($comprobante, $cuenta){
        try {
            $xxx = 0;
            $str = "SELECT valor FROM gf_detalle_comprobante WHERE comprobante =$comprobante AND cuenta = $cuenta";
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

    public function actualizarDataComprobante($valor, $comprobante, $cuenta){
        try {
            $str = "UPDATE gf_detalle_comprobante
                    SET    valor       = valor + ($valor)
                    WHERE  comprobante = $comprobante
                    AND    cuenta      = $cuenta";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * obtenerTerceroPerfilCompania
     *
     * Metodo para obtener los terceros por medio de perfiles
     *
     * @param int|integer $compania Id de compañia logueada
     * @param string|mixed $orden Ordenamiento para la consulta (ASC, DESC)
     * @return mixed|string
     */
    public function obtenerTerceroOrdenCompania($compania, $orden){
        try {
            $str = "SELECT DISTINCT gtr.id_unico,
                              (
                                IF(
                                  CONCAT_WS(' ', gtr.nombreuno, gtr.nombredos, gtr.apellidouno, gtr.apellidodos) = ' ',
                                  CONCAT_WS(' ', gtr.razonsocial, gti.sigla, gtr.numeroidentificacion, gtr.digitoverficacion),
                                  UPPER(CONCAT_WS(' ', gtr.nombreuno, gtr.nombredos, gtr.apellidouno, gtr.apellidodos, gti.sigla, gtr.numeroidentificacion))
                                )
                              )
                    FROM      gf_tercero             AS gtr
                    LEFT JOIN gf_tipo_identificacion AS gti ON gtr.tipoidentificacion = gti.id_unico
                    WHERE     (gtr.compania = $compania)
                    ORDER BY  gtr.id_unico $orden";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function listarTercerosCuentas($terI, $terF){
        try {
            $str = "SELECT  DISTINCT gtr.id_unico,
                              (
                                IF(
                                  CONCAT_WS(' ', gtr.nombreuno, gtr.nombredos, gtr.apellidouno, gtr.apellidodos) = ' ',
                                  gtr.razonsocial,
                                  UPPER(CONCAT_WS(' ', gtr.nombreuno, gtr.nombredos, gtr.apellidouno, gtr.apellidodos))
                                )
                              ),
                              CONCAT_WS(' ', gti.sigla, gtr.numeroidentificacion)
                    FROM      gp_factura             AS gft
                    LEFT JOIN gf_tercero             AS gtr ON gft.tercero            = gtr.id_unico
                    LEFT JOIN gf_tipo_identificacion AS gti ON gtr.tipoidentificacion = gti.id_unico
                    WHERE     (gtr.id_unico BETWEEN $terI AND $terF)
                    GROUP BY  gtr.id_unico";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerFacturasCliente($cliente, $tipI, $tipF){
        try {
            $str = "SELECT    gft.id_unico, DATE_FORMAT(gft.fecha_factura, '%d/%m/%Y'), CONCAT_WS(' ', gtf.prefijo, gft.numero_factura)
                    FROM      gp_factura      AS gft
                    LEFT JOIN gp_tipo_factura AS gtf ON gft.tipofactura = gtf.id_unico
                    WHERE     (gft.tercero     = $cliente)
                    AND       (gtf.id_unico BETWEEN $tipI AND $tipF)
                    ORDER BY  gft.fecha_factura ASC";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function VerificarRecaudoFactura($factura){
        try {
            $xxx = 0;
            $str = "SELECT DISTINCT gdp.pago
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

    public function buscarAbonosPago($factura){
        try {
            $xxx = 0;
            $str = "SELECT    gdp.valor + gdp.iva + gdp.impoconsumo
                    FROM      gp_detalle_pago AS gdp
                    LEFT JOIN gp_detalle_factura AS gdf ON gdp.detalle_factura = gdf.id_unico
                    WHERE     gdf.factura = $factura";
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

    public function obtenerFacturasXFechaTipoSinMovimiento($fechaI, $fechaF, $tipo){
        try {
            $str = "SELECT    gdf.id_unico, gpl.id_unico, gdf.unidad_origen, gpl.unidad, gdf.cantidad, gft.id_unico
                    FROM      gp_detalle_factura AS gdf
                    LEFT JOIN gp_factura         AS gft ON gdf.factura         = gft.id_unico
                    LEFT JOIN gp_concepto        AS gct ON gdf.concepto_tarifa = gct.id_unico
                    LEFT JOIN gf_plan_inventario AS gpl ON gct.plan_inventario = gpl.id_unico
                    WHERE     (gft.fecha_factura BETWEEN '$fechaI' AND '$fechaF')
                    AND       (gft.tipofactura = $tipo)
                    AND       (gdf.detallemovimiento IS NULL)";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function buscarIdMovimientoFactura($factura){
        try {
            $xxx = 0;
            $str = "SELECT    gdm.movimiento
                    FROM      gp_detalle_factura    AS gdf
                    LEFT JOIN gf_detalle_movimiento AS gdm on gdf.detallemovimiento = gdm.id_unico
                    WHERE     factura = $factura
                    AND       gdf.detallemovimiento IS NOT NULL";
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

    public function obtenerTercero($num){
        try {
            $str = "SELECT gtr.id_unico,
                           (
                            IF(
                              CONCAT_WS(' ', gtr.nombreuno, gtr.nombredos, gtr.apellidouno, gtr.apellidodos) = ' ',
                              gtr.razonsocial,
                              CONCAT_WS(' ', gtr.nombreuno, gtr.nombredos, gtr.apellidouno, gtr.apellidodos)
                            )
                           )
                    FROM   gf_tercero AS gtr
                    WHERE  (gtr.numeroidentificacion LIKE '%$num%')
                    OR     (gtr.razonsocial          LIKE '%$num%')
                    OR     (gtr.nombreuno            LIKE '%$num%')
                    OR     (gtr.apellidouno          LIKE '%$num%')
                    LIMIT 10";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function buscarFacturasTercero($tercero, $clase){
        try {
            $str = "SELECT    fat.id_unico, fat.numero_factura
                    FROM      gp_factura      AS fat
                    LEFT JOIN gp_tipo_factura AS tpf ON fat.tipofactura = tpf.id_unico
                    WHERE     fat.tercero       = $tercero
                    AND       tpf.clase_factura =  $clase
                    ORDER BY  fat.fecha_factura ASC";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerVendedores($orden){
        try {
            $str = "SELECT    DISTINCT
                              gtr.id_unico,
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
                                  CONCAT_WS(' ', gti.sigla ,gtr.numeroidentificacion, gtr.digitoverficacion),
                                  CONCAT_WS(' ', gti.sigla, gtr.numeroidentificacion)
                                )
                              )
                    FROM      gp_factura             AS gpf
                    LEFT JOIN gf_tercero             AS gtr ON gpf.vendedor           = gtr.id_unico
                    LEFT JOIN gf_tipo_identificacion AS gti ON gtr.tipoidentificacion = gti.id_unico
                    WHERE     gpf.vendedor IS NOT NULL
                    GROUP BY  gpf.vendedor
                    ORDER BY  gtr.id_unico $orden";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function listadoTercerosFactura($orden){
        try {
            $str = "SELECT    DISTINCT
                              gtr.id_unico,
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
                                  CONCAT_WS(' ', gti.sigla ,gtr.numeroidentificacion, gtr.digitoverficacion),
                                  CONCAT_WS(' ', gti.sigla, gtr.numeroidentificacion)
                                )
                              )
                    FROM      gp_factura             AS gft
                    LEFT JOIN gf_tercero             AS gtr ON gft.tercero            = gtr.id_unico
                    LEFT JOIN gf_tipo_identificacion AS gti ON gtr.tipoidentificacion = gti.id_unico
                    GROUP BY  gtr.id_unico
                    ORDER BY  gtr.id_unico $orden";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerFacturaAso($fat){
        try {
            $xxx = 0;
            $str = "SELECT    gda.factura
                    FROM      gp_detalle_factura AS gdf
                    LEFT JOIN gp_detalle_factura AS gda ON gdf.detalleafectado = gda.id_unico
                    WHERE gdf.factura = $fat";
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

    public function buscarAfectacionesDetalle($id){
        try {
            $xxx = 0;
            $str = "SELECT gpf.cantidad FROM gp_detalle_factura AS gpf WHERE  gpf.detalleafectado = $id";
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

    public function listadoFacturasSinCoste($clase, $fecha){
        try {
            $str = "SELECT    DATE_FORMAT(gft.fecha_factura, '%d/%m/%Y') AS fecha, UPPER(gpi.nombre) AS nom, gtf.prefijo,
                              gft.numero_factura, gtm.sigla, gmv.numero, UPPER(guf.nombre) as unm, UPPER(guo.nombre) AS uns,
                              (gpf.valor + gpf.iva + gpf.impoconsumo) * gpf.cantidad AS valor
                    FROM      gp_detalle_factura    AS gpf
                    LEFT JOIN gp_factura            AS gft ON gpf.factura           = gft.id_unico
                    LEFT JOIN gp_tipo_factura       AS gtf ON gft.tipofactura       = gtf.id_unico
                    LEFT JOIN gf_detalle_movimiento AS gdm ON gpf.detallemovimiento = gdm.id_unico
                    LEFT JOIN gf_movimiento         AS gmv ON gdm.movimiento        = gmv.id_unico
                    LEFT JOIN gp_concepto           AS gct ON gpf.concepto_tarifa   = gct.id_unico
                    LEFT JOIN gf_plan_inventario    AS gpi ON gct.plan_inventario   = gpi.id_unico
                    LEFT JOIN gf_unidad_factor      AS guf ON gpi.unidad            = guf.id_unico
                    LEFT JOIN gf_unidad_factor      AS guo ON gdm.unidad_origen     = guo.id_unico
                    LEFT JOIN gf_tipo_movimiento    AS gtm ON gmv.tipomovimiento    = gtm.id_unico
                    WHERE     gtf.clase_factura IN ($clase)
                    AND       gft.fecha_factura <= '$fecha'
                    AND       gdm.valor         = 0
                    ORDER BY  gft.fecha_factura ASC, gtf.id_unico";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function ListadoProductosConEntrada(){
        try {
            $str = "SELECT     gpi.id_unico, CONCAT_WS(' ', gpi.codi, UPPER(gpi.nombre)) AS gpnom
                    FROM       gf_detalle_movimiento AS gdm
                    LEFT JOIN  gp_concepto           AS gct ON gct.plan_inventario = gdm.id_unico
                    LEFT JOIN  gf_movimiento         AS gmv ON gdm.movimiento      = gmv.id_unico
                    LEFT JOIN  gf_tipo_movimiento    AS gtm ON gmv.tipomovimiento  = gtm.id_unico
                    LEFT JOIN  gf_plan_inventario    AS gpi ON gdm.planmovimiento  = gpi.id_unico
                    WHERE      gtm.clase  = 2
                    GROUP BY   gpi.id_unico";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function BuscarSalidasProductoEntreFechas($producto, $fechaI, $fechaF){
        try {
            $str = "SELECT     gdf.id_unico
                    FROM       gp_detalle_factura AS gdf
                    LEFT JOIN  gp_factura         AS gft ON gdf.factura         = gft.id_unico
                    LEFT JOIN  gp_concepto        AS gct ON gdf.concepto_tarifa = gct.id_unico
                    WHERE      (gct.plan_inventario = $producto)
                    AND        (gft.fecha_factura BETWEEN '$fechaI' AND '$fechaF')";
            $res = $this->mysqli->query($str);
            return $res->fetch_all();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerUltimoCosteElemento($id){
        try {
            $xxx = array();
            $str = "SELECT    gdm.valor, DATE_FORMAT(gmv.fecha, '%d/%m/%Y') as fecha
                    FROM      gf_detalle_movimiento AS gdm
                    LEFT JOIN gf_movimiento         AS gmv ON gdm.movimiento     = gmv.id_unico
                    LEFT JOIN gf_tipo_movimiento    AS gtm ON gmv.tipomovimiento = gtm.id_unico
                    WHERE     gtm.clase          = 2
                    AND       gdm.planmovimiento = $id
                    ORDER BY  gdm.id_unico DESC";
            $res = $this->mysqli->query($str);
            if($res->num_rows > 0){
                $row = $res->fetch_row();
                $xxx['valor'] = $row[0];
                $xxx['fecha'] = $row[1];
            }
            return $xxx;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerFechaVenta($id){
        try {
            $xxx = "";
            $str = "SELECT    DATE_FORMAT(gft.fecha_factura, '%d/%m/%Y') as fecha
                    FROM      gp_detalle_factura AS gdf
                    LEFT JOIN gp_factura         AS gft ON gdf.factura = gft.id_unico
                    LEFT JOIN gp_concepto        AS gct ON gdf.concepto_tarifa = gct.id_unico
                    WHERE     gct.plan_inventario = $id
                    ORDER BY  gft.id_unico";
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