<?php
/**
 * Modelo de almacén relacionado a facturación y solo ingrese salida
 */
require_once ('./Conexion/db.php');

class movimiento{
    public $id_unico;
    public $numero;
    public $fecha;
    public $estado;
    public $descripcion;
    public $observaciones;
    public $tipomovimiento;
    public $parametrizacionanno;
    public $compania;
    public $tercero;
    public $tercero2;
    public $centrocosto;
    public $proyecto;
    public $dependencia;

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

    public function getNumero(){
        return $this->numero;
    }

    public function setNumero($numero){
        $this->numero = $numero;
    }

    public function getFecha(){
        return $this->fecha;
    }

    public function setFecha($fecha){
        $this->fecha = $fecha;
    }

    public function getEstado(){
        return $this->estado;
    }

    public function setEstado($estado){
        $this->estado = $estado;
    }

    public function getDescripcion(){
        return $this->descripcion;
    }

    public function setDescripcion($descripcion){
        $this->descripcion = $descripcion;
    }

    public function getObservaciones(){
        return $this->observaciones;
    }

    public function setObservaciones($observaciones){
        $this->observaciones = $observaciones;
    }

    public function getTipomovimiento(){
        return $this->tipomovimiento;
    }

    public function setTipomovimiento($tipomovimiento){
        $this->tipomovimiento = $tipomovimiento;
    }

    public function getParametrizacionanno(){
        return $this->parametrizacionanno;
    }

    public function setParametrizacionanno($parametrizacionanno){
        $this->parametrizacionanno = $parametrizacionanno;
    }

    public function getCompania(){
        return $this->compania;
    }

    public function setCompania($compania){
        $this->compania = $compania;
    }

    public function getTercero(){
        return $this->tercero;
    }

    public function setTercero($tercero){
        $this->tercero = $tercero;
    }

    public function getTercero2(){
        return $this->tercero2;
    }

    public function setTercero2($tercero2){
        $this->tercero2 = $tercero2;
    }

    public function getCentrocosto(){
        return $this->centrocosto;
    }

    public function setCentrocosto($centrocosto){
        $this->centrocosto = $centrocosto;
    }

    public function getProyecto(){
        return $this->proyecto;
    }

    public function setProyecto($proyecto){
        $this->proyecto = $proyecto;
    }

    public function getDependencia(){
        return $this->dependencia;
    }

    public function setDependencia($dependencia){
        $this->dependencia = $dependencia;
    }

    public function registrar(movimiento $data){
        try {
            $str = "INSERT INTO gf_movimiento( tipomovimiento, numero, fecha, estado, descripcion,
                        observaciones, parametrizacionanno, compania, tercero, tercero2, centrocosto,
                        proyecto, dependencia
                    ) VALUES(
                        $data->tipomovimiento, $data->numero, '$data->fecha', $data->estado, '$data->descripcion',
                        '$data->observaciones', $data->parametrizacionanno, $data->compania, $data->tercero,
                        $data->tercero2, $data->centrocosto, $data->proyecto, $data->dependencia
                    )";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function buscarMovimiento($tipo, $numero){
        try {
            $id_ = 0;
            $sql = "SELECT id_unico FROM gf_movimiento WHERE tipomovimiento = $tipo AND numero = $numero";
            $res = $this->mysqli->query($sql);
            if($res->num_rows > 0){
                $row = $res->fetch_row();
                $id_ = $row[0];
            }
            return $id_;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obnterDependencia($tercero){
        try {
            $id_ = 0;
            $sql = "SELECT dependencia FROM gf_dependencia_responsable WHERE responsable = $tercero";
            $res = $this->mysqli->query($sql);
            if($res->num_rows > 0){
                $row = $res->fetch_row();
                $id_ = $row[0];
            }
            return $id_;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerUltimoRegistro($tipo){
        try {
            $id_ = 0;
            $sql = "SELECT MAX(id_unico) FROM gf_movimiento WHERE tipomovimiento = $tipo";
            $res = $this->mysqli->query($sql);
            if($res->num_rows > 0){
                $row = $res->fetch_row();
                $id_ = $row[0];
            }
            return $id_;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function kardex($plan){
        try {
            $xent = 0;
            $xsal = 0;
            $sql = "SELECT    mpr.detallemovimiento, tpm.clase, dtm.cantidad
                    FROM      gf_movimiento_producto mpr
                    LEFT JOIN gf_detalle_movimiento  dtm ON mpr.detallemovimiento = dtm.id_unico
                    LEFT JOIN gf_movimiento          mov ON dtm.movimiento        = mov.id_unico
                    LEFT JOIN gf_tipo_movimiento     tpm ON mov.tipomovimiento    = tpm.id_unico
                    WHERE     dtm.planmovimiento = $plan
                    GROUP BY  dtm.id_unico";
            $res = $this->mysqli->query($sql);
            while($row = $res->fetch_row()){
                switch ($row[1]){
                    case 2:
                        $xent += $row[2];
                        break;

                    case 3:
                        $xsal += $row[2];
                        break;
                }
            }

            $texs = $xsal - $xent;
            return $texs;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerCantidadEntradas($plan){
        try{
            $xxx = 0;
            $sql = "SELECT    dtm.cantidad
                    FROM      gf_movimiento_producto mpr
                    LEFT JOIN gf_detalle_movimiento  dtm ON mpr.detallemovimiento = dtm.id_unico
                    LEFT JOIN gf_movimiento          mov ON dtm.movimiento        = mov.id_unico
                    LEFT JOIN gf_tipo_movimiento     tpm ON mov.tipomovimiento    = tpm.id_unico
                    WHERE     dtm.planmovimiento = $plan
                    AND       tpm.clase          = 2
                    GROUP BY  dtm.id_unico";
            $res = $this->mysqli->query($sql);
            if($res->num_rows > 0){
                while($row = $res->fetch_row()){
                    $xxx += $row[0];
                }
            }
            return $xxx;
        }catch (Exception $e){
            die($e->getMessage());
        }
    }

    public function resolverValores($xxx, $cE){
        $zzz = 0;
        for ($i = 0; $i < count($xxx); $i++) {
            $www = $xxx[$i];
            $yyy = explode(",",$www);
            $zzz += $yyy[0] * $yyy[1];
        }
        $ttt = $zzz / $cE;
        return $ttt;
    }

    public function obtnerProductos($plan){
        try {
            $sql = "SELECT   mpr.producto
                   FROM      gf_movimiento_producto mpr
                   LEFT JOIN gf_detalle_movimiento  dtm ON mpr.detallemovimiento = dtm.id_unico
                   LEFT JOIN gf_movimiento          mov ON dtm.movimiento        = mov.id_unico
                   LEFT JOIN gf_tipo_movimiento     tpf ON mov.tipomovimiento    = tpf.id_unico
                   WHERE     dtm.planmovimiento = $plan
                   AND       tpf.clase          = 2";
            $res = $this->mysqli->query($sql);
            $row = $res->fetch_all(MYSQLI_NUM);
            return $row;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerSalidasProducto($producto){
        try {
            $i   = 0;
            $sql = "SELECT   mpr.detallemovimiento
                   FROM      gf_movimiento_producto mpr
                   LEFT JOIN gf_detalle_movimiento  dtm ON mpr.detallemovimiento = dtm.id_unico
                   LEFT JOIN gf_movimiento          mov ON dtm.movimiento        = mov.id_unico
                   LEFT JOIN gf_tipo_movimiento     tpm ON mov.tipomovimiento    = tpm.id_unico
                   WHERE     tpm.clase    = 3
                   AND       mpr.producto = $producto";
            $res = $this->mysqli->query($sql);
            if($res->num_rows > 0){
                while($row = $res->fetch_row()){
                    $i++;
                }
            }
            return $i;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerEntradasProducto($producto){
        try {
            $i   = 0;
            $sql = "SELECT    mpr.detallemovimiento
                    FROM      gf_movimiento_producto mpr
                    LEFT JOIN gf_detalle_movimiento  dtm ON mpr.detallemovimiento = dtm.id_unico
                    LEFT JOIN gf_movimiento          mov ON dtm.movimiento        = mov.id_unico
                    LEFT JOIN gf_tipo_movimiento     tpm ON mov.tipomovimiento    = tpm.id_unico
                    WHERE     tpm.clase    = 3
                    AND       mpr.producto = $producto";
            $res = $this->mysqli->query($sql);
            if($res->num_rows > 0){
                while($row = $res->fetch_row()){
                    $i++;
                }
            }
            return $i;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerValorSalida($cantidad, $valor, $cen){
        $xxx = ($cantidad * $valor) / $cen;
        return $xxx;
    }

    public function tieneSalida($producto){
        try {
            $id_ = 0;
            $sql = "SELECT     dtm.id_unico
                    FROM       gf_movimiento_producto mpr
                    LEFT JOIN  gf_detalle_movimiento  dtm ON mpr.detallemovimiento = dtm.id_unico
                    LEFT JOIN  gf_movimiento          mov ON dtm.movimiento        = mov.id_unico
                    LEFT JOIN  gf_tipo_movimiento     tpm ON mov.tipomovimiento    = tpm.id_unico
                    WHERE      tpm.clase    = 3
                    AND        mpr.producto = $producto";
            $res = $this->mysqli->query($sql);
            if($res->num_rows > 0){
                $row = $res->fetch_row();
                $id_ = $row[0];
            }
            return $id_;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtenerId($id){
        try {
            $str = "SELECT id_unico FROM gf_movimiento WHERE md5(id_unico) = '$id'";
            $res = $this->mysqli->query($str);
            return $res->fetch_row();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function modificar($fecha, $descripcion, $observaciones, $tercero, $id){
        try {
            $str = "UPDATE gf_movimiento
                    SET    fecha         = '$fecha',
                           descripcion   = '$descripcion',
                           observaciones = '$observaciones',
                           tercero       = $tercero
                    WHERE  md5(id_unico) = '$id'";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtenerProyecto($nombre){
        try {
            $xxx = 0;
            $str = "SELECT id_unico FROM gf_proyecto WHERE nombre = '$nombre'";
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

    public function obtenerDependenciaTercero($ter){
        try {
            $xxx = 0;
            $str = "SELECT dependencia FROM gf_dependencia_responsable WHERE responsable = $ter";
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

    public function obtenerCantidadDetalles($mov){
        try {
            $xxx = 0;
            $str = "SELECT COUNT(*) FROM gf_detalle_movimiento WHERE movimiento = $mov";
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

    public function guardar($tipomovimiento, $numero, $fecha, $estado, $descripcion, $observaciones, $parametrizacionanno, $compania, $tercero, $tercero2, $centrocosto, $proyecto, $dependencia){
        try {
            $str = "INSERT INTO gf_movimiento( tipomovimiento, numero, fecha, estado, descripcion, observaciones, parametrizacionanno, compania, tercero, tercero2, centrocosto, proyecto, dependencia, descuento )
                        VALUES( $tipomovimiento, $numero, '$fecha', $estado, '$descripcion', '$observaciones', $parametrizacionanno, $compania,
                                $tercero, $tercero2, $centrocosto, $proyecto, $dependencia, 0 )";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function cambiarFechaMov($id, $fecha){
        try {
            $str = "UPDATE gf_movimiento SET fecha = '$fecha' WHERE id_unico = $id";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtenerIdNumFactura($num){
        $xxx = 0;
        $str = "SELECT id_unico FROM gf_movimiento WHERE  numero = $num";
        $res = $this->mysqli->query($str);
        if($res->num_rows > 0){
            $row = $res->fetch_row();
            $xxx = $row[0];
        }
        return $xxx;
    }

    public function actualizarTercero($id, $tercero){
        try {
            $str = "UPDATE gf_movimiento SET tercero = $tercero WHERE id_unico = $id";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerFactorElemento($elemento, $unidad){
        try {
            $xxx = 0;
            $str = "SELECT    geu.valor_conversion
                    FROM      gp_concepto_tarifa AS gct
                    LEFT JOIN gp_concepto        AS gcn ON gct.concepto        = gcn.id_unico
                    LEFT JOIN gf_elemento_unidad AS geu ON gct.elemento_unidad = geu.id_unico
                    WHERE     gcn.plan_inventario = $elemento
                    AND       geu.unidad_empaque  = $unidad";
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

    public function cambiarRemision($mov, $numero, $tipo){
        try {
            $str = "UPDATE gf_movimiento SET numero = $numero, tipomovimiento = $tipo WHERE  id_unico = $mov";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function formatearFecha($fecha, $separador){
        try {
            $xxx = explode($separador, $fecha);
            return "$xxx[2]-$xxx[1]-$xxx[0]";
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerTipoMovClase($clase){
        try {
            $str = "SELECT gtm.id_unico, UPPER(CONCAT_WS(' ',gtm.nombre, gtm.sigla))
                    FROM   gf_tipo_movimiento AS gtm
                    WHERE  gtm.clase = $clase";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function buscarMovimientoFechaTipo($fechaI, $fechaF, $elementoI){
        try {
            $str = "SELECT DISTINCT gdm.planmovimiento, gdm.cantidad, ((gdm.valor) * gdm.cantidad), gtp.clase, gmv.fecha, gdm.hora, gdm.id_unico
                    FROM            gf_detalle_movimiento AS gdm
                    LEFT JOIN       gf_movimiento         AS gmv ON gdm.movimiento     = gmv.id_unico
                    LEFT JOIN       gf_plan_inventario    AS gpl ON gdm.planmovimiento = gpl.id_unico
                    LEFT JOIN       gf_tipo_movimiento    AS gtp ON gmv.tipomovimiento = gtp.id_unico
                    WHERE           (gmv.fecha          BETWEEN '$fechaI'  AND '$fechaF')
                    AND             (gdm.planmovimiento = $elementoI)
                    ORDER BY        gmv.fecha, gdm.hora, gtp.clase";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function buscarElementosTF($elementoI, $elementoF){
        try {
            $str = "SELECT DISTINCT gdm.planmovimiento
                    FROM            gf_detalle_movimiento AS gdm
                    LEFT JOIN       gf_plan_inventario    AS gpl ON gdm.planmovimiento = gpl.id_unico
                    WHERE           (gdm.planmovimiento BETWEEN $elementoI AND $elementoF)
                    GROUP BY        gdm.planmovimiento";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function actualizarValorDetalleMov($id, $valor){
        try {
            $str = "UPDATE gf_detalle_movimiento SET valor = $valor WHERE id_unico = $id;";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function buscarValorMaximoElemento($elemento){
        try {
            $xxx = 0;
            $str = "SELECT    gdm.valor
                    FROM      gf_detalle_movimiento AS gdm
                    LEFT JOIN gf_movimiento         AS gmv ON gdm.movimiento     = gmv.id_unico
                    LEFT JOIN gf_tipo_movimiento    AS gtp ON gmv.tipomovimiento = gtp.id_unico
                    WHERE     gdm.planmovimiento = $elemento
                    AND       gdm.valor         != 0
                    ORDER BY  gdm.id_unico DESC
                    LIMIT     1";
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

    public function actualizarData($planmovimiento){
        try {
            $str = "UPDATE    gf_detalle_movimiento AS gdm
                    LEFT JOIN gf_movimiento         AS gmv ON gdm.movimiento = gmv.id_unico
                    LEFT JOIN gf_tipo_movimiento    AS gtp ON gmv.tipomovimiento = gtp.id_unico
                    SET       gdm.valor          = 0
                    WHERE     gdm.planmovimiento = $planmovimiento
                    AND       gtp.clase          = 3";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function buscarMovimientoFechaTipoFactura($fechaI, $fechaF, $elementoI){
        try {
            $str = "SELECT    gdm.id_unico, gdm.planmovimiento, gdm.unidad_origen, gdf.cantidad
                    FROM      gp_detalle_factura    AS gdf
                    LEFT JOIN gf_detalle_movimiento AS gdm ON gdf.detallemovimiento = gdm.id_unico
                    LEFT JOIN gf_movimiento         AS gmv ON gdm.movimiento        = gmv.id_unico
                    LEFT JOIN gf_tipo_movimiento    AS gtp ON gmv.tipomovimiento    = gtp.id_unico
                    WHERE     (gmv.fecha          BETWEEN '$fechaI'  AND '$fechaF')
                    AND       gdm.planmovimiento  = $elementoI
                    AND       gtp.clase           = 3";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function actualizarCantidadDetalleMov($detalle, $cantidad){
        try {
            $str = "UPDATE gf_detalle_movimiento SET cantidad = $cantidad WHERE id_unico = $detalle";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerRelacionHijosUnidad($padre, $unidad){
        try {
            $str = "SELECT    gpa.plan_hijo, gpa.cantidad
                    FROM      gf_plan_inventario_asociado AS gpa
                    LEFT JOIN gp_concepto_tarifa          AS gct ON gpa.tarifa          = gct.id_unico
                    LEFT JOIN gf_elemento_unidad          AS gel ON gct.elemento_unidad = gel.id_unico
                    WHERE     gpa.plan_padre     = $padre
                    AND       gel.unidad_empaque = $unidad";
            $res = $this->mysqli->query($str);
            return $res->fetch_all(MYSQLI_NUM);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerUltimo($tipo){
        try {
            $xxx = 0;
            $str = "SELECT MAX(id_unico) FROM gf_movimiento WHERE tipomovimiento = $tipo";
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

    public function eliminarPreciosDetalle($detalle){
        try {
            $str = "DELETE FROM gf_precio_producto WHERE detalle_mov = $detalle";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}