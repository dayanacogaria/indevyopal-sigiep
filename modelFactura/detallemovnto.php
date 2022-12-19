<?php
require_once ('./Conexion/db.php');
require_once ('./modelAlmacen/salida.php');
class detallemovimiento{

    public $id_unico;
    public $cantidad;
    public $valor;
    public $iva;
    public $movimiento;
    public $planMovimiento;
    public $detalleasociado;
    private $salida;
    private $mysqli;

    public function getId_Unico() {
        return $this->id_unico;
    }

    public function setId_Unico($id_unico) {
        $this->id_unico = $id_unico;
        return $this;
    }

    public function getCantidad() {
        return $this->cantidad;
    }

    public function setCantidad($cantidad) {
        $this->cantidad = $cantidad;
        return $this;
    }

    public function getValor() {
        return $this->valor;
    }

    public function setValor($valor) {
        $this->valor = $valor;
        return $this;
    }

    public function getIva() {
        return $this->iva;
    }

    public function setIva($iva) {
        $this->iva = $iva;
        return $this;
    }

    public function getMovimiento() {
        return $this->movimiento;
    }

    public function setMovimiento($movimiento) {
        $this->movimiento = $movimiento;
        return $this;
    }

    public function getPlanMovimiento() {
        return $this->planMovimiento;
    }

    public function setPlanMovimiento($planMovimiento) {
        $this->planMovimiento = $planMovimiento;
        return $this;
    }

    public function getDetalleasociado() {
        return $this->detalleasociado;
    }

    public function setDetalleasociado($detalleasociado) {
        $this->detalleasociado = $detalleasociado;
        return $this;
    }

    public function __construct(){
        $this->mysqli = conectar::conexion();
        $this->salida = new salida();
    }

    public function buscar_plan_mov($codigo){
        try {
            $sql = "SELECT id_unico FROM gf_plan_inventario WHERE codi = $codigo ";
            $res = $this->mysqli->query($sql);
            $id  = $res->fetch_row();
            return $id;
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function validar_ficha($id_unico){
        try {
            $sql = "SELECT ficha FROM gf_plan_inventario WHERE id_unico = $id_unico ";
            $res = $this->mysqli->query($sql);

            $row = $res->fetch_row();

            if(empty($row[0])){
                $rest = 0;
            }else{
                $rest = 1;
            }

            return $rest;
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function buscarficha(){
        try {
            $sql = "SELECT id_unico FROM gf_ficha WHERE descripcion = 'GENERAL' ";
            $res = $this->mysqli->query($sql);
            $id  = $res->fetch_row();
            return $id;
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function asignar_ficha($id_unico, $ficha){
        try {
            $sql = "UPDATE gf_plan_inventario SET ficha = $ficha WHERE id_unico = $id_unico";
            $res = $this->mysqli->query($sql);

            if($res == true){
                $rest = 1;
            }else{
                $rest = 0;
            }

            return $rest;
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function registrar(detallemovimiento $data){
        try {
            date_default_timezone_set('America/Bogota');
            $hora = date('H:i:s');
            $sql = "INSERT INTO gf_detalle_movimiento(
                                    cantidad,
                                    valor,
                                    iva,
                                    movimiento,
                                    planmovimiento,
                                    hora
                                )VALUES(
                                    $data->cantidad,
                                    $data->valor,
                                    $data->iva,
                                    $data->movimiento,
                                    $data->planmovimiento,
                                    '$hora'
                                )";
            $res = $this->mysqli->query($sql);

            if($res == true){
                $rest = 1;
            }else{
                $rest = 0;
            }

            return $rest;
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function registrar_aso(detallemov $data){
        $sql = "INSERT INTO gf_detalle_movimiento(
                                cantidad,
                                valor,
                                iva,
                                movimiento,
                                planmovimiento,
                                detalleasociado
                            )VALUES(
                                \"$data->cantidad\",
                                \"$data->valor\",
                                \"$data->iva\",
                                $data->movimiento,
                                \"$data->planmovimiento\",
                                $data->detalleasociado
                            )";
        $res = $this->mysqli->query($sql);

        if($res == true){
            $rest = 1;
        }else{
            $rest = 0;
        }

        return $rest;
        $this->mysqli->close();
    }

    public function registrarDetalle(){
        try {
            date_default_timezone_set('America/Bogota');
            $hora = date('H:i:s');
            $sql = "INSERT INTO gf_detalle_movimiento(
                                    cantidad,
                                    valor,
                                    iva,
                                    movimiento,
                                    planmovimiento,
                                    hora
                                )VALUES(
                                    $this->cantidad,
                                    $this->valor,
                                    $this->iva,
                                    $this->movimiento,
                                    $this->planMovimiento,
                                    '$hora'
                                )";
            $res = $this->mysqli->query($sql);

            if($res == true){
                $rest = 1;
            }else{
                $rest = 0;
            }

            return $rest;
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function buscar_detalle($mov){
        try {
            $sql = "SELECT MAX(id_unico) FROM gf_detalle_movimiento WHERE movimiento = $mov";
            $res = $this->mysqli->query($sql);
            $row = $res->fetch_row();
            return $row[0];
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerCantidadPlan($plan){
        $xe = $this->salida->obtnerCantidadProductosPlan($plan);
        $xs = $this->salida->obtnerCantidadProductosPlanSalida($plan);
        $xx = $xe - $xs;
        return $xx;
    }

    public function eliminar($id){
        try {
            $str = "DELETE FROM gf_detalle_movimiento WHERE id_unico = $id";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function obtenerSaldoPlan($plan){
        $dtm = new detallemovimiento();
        $xe = $dtm->obtenerSaldoEntradaPlan($plan);
        $xs = $dtm->obtenerSaldoSalidaPlan($plan);
        $xx = $xe - $xs;
        return $xx;
    }

    public function obtenerSaldoEntradaPlan($id){
        try {
            $xxx = 0;
            $sql = "SELECT    (dtm.valor) * dtm.cantidad
                    FROM      gf_detalle_movimiento  dtm
                    LEFT JOIN gf_plan_inventario     pln ON dtm.planmovimiento    = pln.id_unico
                    LEFT JOIN gf_movimiento_producto mpr ON mpr.detallemovimiento = dtm.id_unico
                    LEFT JOIN gf_producto            pro ON mpr.producto          = pro.id_unico
                    LEFT JOIN gf_movimiento          mov ON dtm.movimiento        = mov.id_unico
                    LEFT JOIN gf_tipo_movimiento     tpm ON mov.tipomovimiento    = tpm.id_unico
                    WHERE (pln.id_unico = $id)
                    AND   (pro.baja IS NULL OR pro.baja = 0)
                    AND   (tpm.clase = 2)";
            $res = $this->mysqli->query($sql);
            if($res->num_rows > 0){
                $row = $res->fetch_all(MYSQLI_NUM);
                foreach ($row as $row) {
                    $xxx += $row[0];
                }
            }
            return $xxx;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtenerSaldoSalidaPlan($id){
        try {
            $xxx = 0;
            $sql = "SELECT    (dtm.valor) * dtm.cantidad
                    FROM      gf_detalle_movimiento  dtm
                    LEFT JOIN gf_plan_inventario     pln ON dtm.planmovimiento    = pln.id_unico
                    LEFT JOIN gf_movimiento_producto mpr ON mpr.detallemovimiento = dtm.id_unico
                    LEFT JOIN gf_producto            pro ON mpr.producto          = pro.id_unico
                    LEFT JOIN gf_movimiento          mov ON dtm.movimiento        = mov.id_unico
                    LEFT JOIN gf_tipo_movimiento     tpm ON mov.tipomovimiento    = tpm.id_unico
                    WHERE (pln.id_unico = $id)
                    AND   (pro.baja IS NULL OR pro.baja = 0)
                    AND   (tpm.clase = 3)";
            $res = $this->mysqli->query($sql);
            if($res->num_rows > 0){
                $row = $res->fetch_all(MYSQLI_NUM);
                foreach ($row as $row) {
                    $xxx += $row[0];
                }
            }
            return $xxx;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function actualizar($id, $cantidad){
        try {
            $str = "UPDATE gf_detalle_movimiento SET cantidad = $cantidad WHERE id_unico = $id";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function eliminarDetalles($id){
        try {
            $str = "DELETE FROM gf_detalle_movimiento WHERE md5(movimiento) = '$id'";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function guardar($cantidad, $valor, $iva, $movimiento, $planmovimiento, $unidad, $cantidadO){
        try {
            date_default_timezone_set('America/Bogota');
            $hora = date('H:i');
            $str = "INSERT INTO gf_detalle_movimiento( cantidad, valor, iva, movimiento, planmovimiento, hora, unidad_origen, cantidad_origen )
                          VALUES( $cantidad, $valor, $iva, $movimiento, $planmovimiento, '$hora', $unidad, $cantidadO )";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtenerUltimoRegistro($mov){
        try {
            $xxx = 0;
            $str = "SELECT MAX(id_unico) FROM gf_detalle_movimiento WHERE movimiento = $mov";
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

    public function buscarValorDetalleCantidad($id){
        try {
            $xxx = array();
            $str = "SELECT valor, cantidad FROM gf_detalle_movimiento WHERE id_unico = $id";
            $res = $this->mysqli->query($str);
            if($res->num_rows > 0){
                $row = $res->fetch_row();
                $xxx['valor']    = $row[0];
                $xxx['cantidad'] = $row[1];
            }
            return $xxx;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function actualizarDataDetalle($cantidadO, $unidadO, $cantidad, $valor, $id){
        try {
            $str = "UPDATE gf_detalle_movimiento SET cantidad_origen = $cantidadO, unidad_origen = $unidadO, cantidad = $cantidad, valor = $valor WHERE id_unico = $id";
            return $this->mysqli->query($str);
            $this->mysqli->close();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function guardarD($cantidad, $valor, $iva, $movimiento, $planmovimiento, $unidad, $cantidadO, $ajuste){
        try {
            date_default_timezone_set('America/Bogota');
            $hora = date('H:i');
            $str = "INSERT INTO gf_detalle_movimiento( cantidad, valor, iva, movimiento, planmovimiento, hora, unidad_origen, cantidad_origen, ajuste )
                          VALUES( $cantidad, $valor, $iva, $movimiento, $planmovimiento, '$hora', $unidad, $cantidadO, $ajuste)";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function guardarA($cantidad, $valor, $iva, $movimiento, $planmovimiento, $unidad, $cantidadO, $ajuste, $asociado){
        try {
            date_default_timezone_set('America/Bogota');
            $hora = date('H:i');
            $str = "INSERT INTO gf_detalle_movimiento( cantidad, valor, iva, movimiento, planmovimiento, hora, unidad_origen, cantidad_origen, ajuste, detalleasociado )
                          VALUES( $cantidad, $valor, $iva, $movimiento, $planmovimiento, '$hora', $unidad, $cantidadO, $ajuste, $asociado)";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function guardarData($cantidad, $valorX, $iva, $movimiento, $planmovimiento, $unidad, $cantidadO, $ajuste, $descuento, $valor){
        try {
            date_default_timezone_set('America/Bogota');
            $hora = date('H:i');
            $str = "INSERT INTO gf_detalle_movimiento( cantidad, valor, iva, movimiento, planmovimiento, hora, unidad_origen, cantidad_origen, ajuste, descuento, valor_origen )
                          VALUES( $cantidad, $valorX, $iva, $movimiento, $planmovimiento, '$hora', $unidad, $cantidadO, $ajuste, $descuento, $valor )";
            return $this->mysqli->query($str);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
}