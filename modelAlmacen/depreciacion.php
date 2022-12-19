<?php
require_once ('../Conexion/db.php');
class depreciacion{

    public $id_unico;
    public $producto;
    public $fecha_dep;
    public $dias_dep;
    public $valor;

    private $mysqli;

    public function __construct(){
        $this->mysqli = conectar::conexion();
    }

    public function registrar(depreciacion $data){
        try {
            $sql = "INSERT INTO ga_depreciacion(
                                    producto,
                                    fecha_dep,
                                    dias_dep,
                                    valor
                                )VALUES(
                                    $data->producto,
                                    '$data->fecha_dep',
                                    $data->dias_dep,
                                    $data->valor
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

    public function obtnerValorTotalDepreciacionProducto($producto){
        try {
            $xxx = 0;

            $sql = "SELECT valor FROM ga_depreciacion WHERE producto = $producto";
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

    public function obtnerValorProductoEntrada($producto){
        try {
            $xxx = 0;

            $sql = "SELECT   dtm.valor
                   FROM      gf_movimiento_producto mpr
                   LEFT JOIN gf_detalle_movimiento  dtm ON mpr.detallemovimiento = dtm.id_unico
                   LEFT JOIN gf_movimiento mov          ON dtm.movimiento        = mov.id_unico
                   LEFT JOIN gf_tipo_movimiento     tpm ON mov.tipomovimiento    = tpm.id_unico
                   LEFT JOIN gf_plan_inventario     pln ON dtm.planmovimiento = pln.id_unico
                   WHERE     mpr.producto       = $producto
                   AND       tpm.clase          = 2
                   AND       pln.tipoinventario = 2";
            $res = $this->mysqli->query($sql);
            $row = mysqli_fetch_row($res);
            if($res->num_rows > 0){
                $xxx = $row[0];
            }

            return $xxx;

            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function tieneSalida($producto){
        try {
            $sql = "SELECT    mpr.detallemovimiento
                    FROM      gf_movimiento_producto mpr
                    LEFT JOIN gf_detalle_movimiento dtm  ON mpr.detallemovimiento = dtm.id_unico
                    LEFT JOIN gf_movimiento mov          ON dtm.movimiento        = mov.id_unico
                    LEFT JOIN gf_tipo_movimiento tpm     ON mov.tipomovimiento    = tpm.id_unico
                    LEFT JOIN gf_plan_inventario pln     ON dtm.planmovimiento    = pln.id_unico
                    WHERE     tpm.clase          = 3
                    AND       mpr.producto       = $producto
                    AND       pln.tipoinventario = 2";
            $res = $this->mysqli->query($sql);

            $row = mysqli_fetch_row($res);
            return $row[0];

            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function encontrarSalidas($producto, $fechaIni, $fechaFin){
        try {
            $sql = "SELECT    mov.id_unico, mov.fecha
                    FROM      gf_movimiento_producto mpr
                    LEFT JOIN gf_detalle_movimiento dtm  ON mpr.detallemovimiento = dtm.id_unico
                    LEFT JOIN gf_movimiento mov          ON dtm.movimiento        = mov.id_unico
                    LEFT JOIN gf_tipo_movimiento tpm     ON mov.tipomovimiento    = tpm.id_unico
                    WHERE     (tpm.clase    = 3)
                    AND       (mpr.producto = $producto)
                    AND       (mov.fecha BETWEEN '$fechaIni' AND '$fechaFin')";
            $res = $this->mysqli->query($sql);

            $row = mysqli_fetch_row($res);
            return $row[1];

            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function encontrarProductosPeriodo($proI, $proF){
        $sql = "SELECT   pro.id_unico,
                         pro.meses,
                         pro.vida_util_remanente,
                         mov.fecha,
                         tpa.valor,
                         pln.nombre,
                         pro.fecha_adquisicion,
                         pln.tipoinventario
              FROM       gf_producto pro
              LEFT JOIN  gf_movimiento_producto  mpr ON mpr.producto          = pro.id_unico
              LEFT JOIN  gf_detalle_movimiento   dtm ON mpr.detallemovimiento = dtm.id_unico
              LEFT JOIN  gf_movimiento           mov ON dtm.movimiento        = mov.id_unico
              LEFT JOIN  gf_tipo_movimiento      tpm ON mov.tipomovimiento    = tpm.id_unico
              LEFT JOIN  gf_plan_inventario      pln ON dtm.planmovimiento    = pln.id_unico
              LEFT JOIN  gf_tipo_activo          tpa ON pln.tipoactivo        = tpa.id_unico
              WHERE      pro.id_unico BETWEEN $proI AND $proF
              AND        tpm.clase          = 2
              AND        pln.tipoinventario = 2
              AND        mpr.detallemovimiento IS NOT NULL
              ORDER BY   pro.id_unico ASC";
        $res = $this->mysqli->query($sql);
        return $res;
        $this->mysqli->close();
    }

    public function ultimoDia($ano, $mes){
        $dia = date("d", mktime(0, 0, 0, $mes + 1, 0, $ano));
        return date('Y-m-d', mktime(0, 0, 0, $mes, $dia, $ano));
    }

    public function primerDia($ano, $mes){
        return date("Y-m-d", mktime(0, 0, 0, $mes, 1, $ano));
    }

    public function obtnerMesA($fecha){
        $fecha = explode("-", $fecha);
        return $fecha[0].",".$fecha[1];
    }

    public function obtenerAnos($annoI, $annoF){
        $annos   = array();
        $annos[] = $annoI;

        $b = 1;

        while($b != 0){
            $b++;

            $annos[] = $annoI = $annoI + 1;

            if($annoI == $annoF){
                $b = 0;
            }

            $i;
        }

        return $annos;
    }

    public function diasFaltantes($fechaI, $fechaF){
        $s = strtotime($fechaF) - strtotime($fechaI);
        $d = intval($s/86400);
        return $d;
    }

    public function obtnerMeses($anno, $fecha){
        $fechaF     = new DateTime("$anno-12-31");
        $fechaI     = new DateTime($fecha);
        $diferencia = $fechaI->diff($fechaF);
        $meses      = ($diferencia->y * 12) + $diferencia->m;
        return $meses;
    }

    public function obtenerUltimodiaAnno($anno){
        return date('Y-m-d', mktime(0, 0, 0, 12, 31, $anno));
    }

    public function separarObjeto($separador, $objeto){
        $x = explode("$separador", $objeto);
        return $x;
    }

    public function depreciaicion_1($valor, $meses){
        if(empty($meses)){
            $res = 0;
        }else{
            $res = $valor / $meses;
        }
        return $res;
    }

    public function depreciacion_2($valor, $meses, $dias){
        return $valor * $meses / 30 * $dias;;
    }

    public function depreciacion_3($valor, $ndias){
        return ($valor / $ndias);
    }

    public function obtenerDiaFecha($fecha){
        $fecha = explode("-", $fecha);
        return $fecha[2];
    }

    public function obtnerValorAnno($anno){
        try {
            $param = "";
            $sql = "SELECT uvt, salariominimo, minimacuantia, menorcuantia FROM gf_parametrizacion_anno WHERE anno  = $anno";
            $res = $this->mysqli->query($sql);

            if($res->num_rows > 0){
                $row   = mysqli_fetch_row($res);
                $param = $row[0].",".$row[1].",".$row[2].",".$row[3];
            }

            return $param;
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function existeDepreciacion($fecha, $producto){
        try {
            $xxx = 0;
            $sql = "SELECT id_unico FROM ga_depreciacion WHERE fecha_dep = '$fecha' AND producto = $producto";
            $res = $this->mysqli->query($sql);

            if($res->num_rows > 0){
                $row = mysqli_fetch_row($res);
                $xxx = $row[0];
            }

            return $xxx;
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function encontrarDepreciacionProductos($fechaInicial, $fechaFinal, $proI, $proF){
        try {
            $sql = "SELECT DISTINCT dep.producto, UPPER(pln.nombre)
                    FROM      ga_depreciacion        dep
                    LEFT JOIN gf_movimiento_producto mpr ON mpr.producto          = dep.producto
                    LEFT JOIN gf_detalle_movimiento  dtm ON mpr.detallemovimiento = dtm.id_unico
                    LEFT JOIN gf_plan_inventario     pln ON dtm.planmovimiento    = pln.id_unico
                    WHERE (dep.fecha_dep BETWEEN '$fechaInicial' AND '$fechaFinal')
                    AND   (dep.producto  BETWEEN $proI AND $proF)
                    AND   (pln.tipoinventario = 2)
                    ORDER BY pln.codi ASC";
            $res = $this->mysqli->query($sql);
            return $res;
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function encontrarDepreciacionProductosPeriodo($fechaInicial, $fechaFinal){
        try {
            $sql = "SELECT DISTINCT dep.producto, UPPER(pln.nombre), pln.codi
                    FROM      ga_depreciacion            dep
                    LEFT JOIN gf_movimiento_producto     mpr ON mpr.producto          = dep.producto
                    LEFT JOIN gf_detalle_movimiento      dtm ON mpr.detallemovimiento = dtm.id_unico
                    LEFT JOIN gf_plan_inventario         pln ON dtm.planmovimiento    = pln.id_unico
                    LEFT JOIN gf_producto_especificacion pes ON pes.producto      = mpr.producto
                    WHERE (dep.fecha_dep BETWEEN '$fechaInicial' AND '$fechaFinal')
                    AND   (pes.fichainventario = 6)
                    AND   (pln.tipoinventario  = 2)
                    ORDER BY pln.codi ASC";
            $res = $this->mysqli->query($sql);
            return $res;
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function encontrarDepreciacionProductosPeriodoCod($fechaInicial, $fechaFinal, $codi){
        try {
            $sql = "SELECT DISTINCT dep.producto, UPPER(pln.nombre), pln.codi
                    FROM      ga_depreciacion        dep
                    LEFT JOIN gf_movimiento_producto mpr ON mpr.producto          = dep.producto
                    LEFT JOIN gf_detalle_movimiento  dtm ON mpr.detallemovimiento = dtm.id_unico
                    LEFT JOIN gf_plan_inventario     pln ON dtm.planmovimiento    = pln.id_unico
                    WHERE     (dep.fecha_dep BETWEEN '$fechaInicial' AND '$fechaFinal')
                    AND       (pln.codi LIKE '$codi%')
                    AND       (pln.tipoinventario = 2)
                    ORDER BY  pln.codi ASC";
            $res = $this->mysqli->query($sql);
            return $res;
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function tercero_informe($id_unico){
        try {
            $sql = "SELECT     UPPER(IF(CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) IS NULL
                               OR CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos)='',
                               (ter.razonsocial),
                               CONCAT_WS(' ',ter.nombreuno,ter.nombredos,ter.apellidouno,ter.apellidodos))) AS 'NOMBRE',
                               CONCAT_WS(' :',UPPER(ti.nombre),ter.numeroidentificacion) AS IDENT,
                               ter.ruta_logo,
                               ter.digitoverficacion
                    FROM       gf_tercero             ter
                    LEFT JOIN  gf_tipo_identificacion ti ON ter.tipoidentificacion = ti.id_unico
                    WHERE      ter.id_unico = $id_unico";
            $res = $this->mysqli->query($sql);
            $row = mysqli_fetch_row($res);
            return $row;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerDepreciacionesProducto($producto){
        try {
            $x = array();
            $sql = "SELECT fecha_dep, dias_dep, valor FROM ga_depreciacion WHERE producto = $producto";
            $res = $this->mysqli->query($sql);
            while($row = mysqli_fetch_row($res)){
                $x[] = $row[0]."/".$row[1]."/".$row[2];
            }
            return $x;
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerFechaAquisicion($id_unico){
        try {
            $xxx = "";
            $sql = "SELECT DATE_FORMAT(fecha_adquisicion, '%d/%m/%Y') FROM gf_producto WHERE id_unico = $id_unico";
            $res = $this->mysqli->query($sql);
            if($res->num_rows > 0){
                $row = $res->fetch_row();
                $xxx = $row[0];
            }
            return $xxx;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function fechaEntrada($id_unico){
        try {
            $xxx = "";
            $sql = "SELECT    DATE_FORMAT(mov.fecha, '%d/%m/%Y')
                    FROM      gf_producto pro
                    LEFT JOIN gf_movimiento_producto mpr ON mpr.producto          = pro.id_unico
                    LEFT JOIN gf_detalle_movimiento  dtm ON mpr.detallemovimiento = dtm.id_unico
                    LEFT JOIN gf_movimiento          mov ON dtm.movimiento        = mov.id_unico
                    LEFT JOIN gf_tipo_movimiento     tpm ON mov.tipomovimiento    = tpm.id_unico
                    WHERE     (pro.id_unico = $id_unico)
                    AND       (tpm.clase    = 2)";
            $res = $this->mysqli->query($sql);
            if($res->num_rows > 0){
                $row = $res->fetch_row();
                $xxx = $row[0];
            }
            return $xxx;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerValorAcumuladoDrp($id_unico, $fechaI, $fechaF){
        try {
            $xxx = 0;
            $sql = "SELECT valor FROM ga_depreciacion WHERE (producto = $id_unico) AND (fecha_dep BETWEEN '$fechaI' AND '$fechaF')";
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

    public function eliminarDepreciacion($id_unico){
        try {
            $sql = "DELETE FROM ga_depreciacion WHERE id_unico = $id_unico";
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

    public function obtnerFechaSalida($id_unico){
        try {
            $fecha = "";

            $sql = "SELECT    DATE_FORMAT(mov.fecha, '%d/%m/%Y') as fecha
                    FROM      gf_movimiento_producto mpr
                    LEFT JOIN gf_detalle_movimiento  dtm ON mpr.detallemovimiento = dtm.id_unico
                    LEFT JOIN gf_movimiento          mov ON dtm.movimiento        = mov.id_unico
                    LEFT JOIN gf_tipo_movimiento     tpm ON mov.tipomovimiento    = tpm.id_unico
                    WHERE     (mpr.producto = $id_unico)
                    AND       (tpm.clase    = 3)";
            $res = $this->mysqli->query($sql);
            if($res->num_rows > 0){
                $row   = $res->fetch_row();
                $fecha = $row[0];
            }
            return $fecha;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function contar_meses($id_unico){
        try {
            $xxx = 0;
            $sql = "SELECT id_unico FROM ga_depreciacion WHERE producto = $id_unico AND valor != 0";
            $res = $this->mysqli->query($sql);
            $xxx = $res->num_rows;
            return $xxx;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtner_vida_util($codigo){
        try {
            $xxx = 0;
            $sql = "SELECT    tpa.valor
                    FROM      gf_plan_inventario pln
                    LEFT JOIN gf_tipo_activo tpa ON pln.tipoactivo = tpa.id_unico
                    WHERE     pln.codi = '$codigo'";
            $res = $this->mysqli->query($sql);
            if($res->num_rows > 0){
                $row = $res->fetch_row();
                $xxx = $row[0];
            }
            return $xxx;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obnterDpreciacionMes($fecha, $producto){
        try {
            $xxx = 0;
            $sql = "SELECT valor FROM ga_depreciacion WHERE fecha_dep = '$fecha' AND producto = $producto";
            $res = $this->mysqli->query($sql);
            if($res->num_rows > 0){
                $row = $res->fetch_row();
                $xxx = $row[0];
            }
            return $xxx;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtner_cod_dep_cnt($codigo){
        try {
            $xxx = 0;
            $sql = "SELECT    cta.codi_cuenta
                    FROM      gf_configuracion_almacen cfa
                    LEFT JOIN gf_plan_inventario pln ON cfa.plan_inventario = pln.id_unico
                    LEFT JOIN gf_cuenta          cta ON cfa.cuenta_credito  = cta.id_unico
                    WHERE     (pln.codi           = '$codigo')";
            $res = $this->mysqli->query($sql);
            if($res->num_rows > 0){
                $row = $res->fetch_row();
                $xxx = $row[0];
            }
            return $xxx;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function eliminarDepreciacionProducto($producto){
        try {
            $sql = "DELETE FROM ga_depreciacion WHERE producto = $producto";
            $res = $this->mysqli->query($sql);

            if($res == true){
                $rest = true;
            }else{
                $rest = false;
            }

            return $rest;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerValorParametroBasico(){
        try {
            $xxx = 0;
            $sql = "SELECT valor FROM gs_parametros_basicos WHERE id_unico = 11";
            $res = $this->mysqli->query($sql);
            if($res->num_rows > 0){
                $row = $res->fetch_row();
                $xxx = $row[0];
            }
            return $xxx;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obnterUltimaFecha($producto){
        try {
            $xxx = "";
            $sql = "SELECT MAX(fecha_dep) FROM ga_depreciacion WHERE producto = $producto";
            $res = $this->mysqli->query($sql);
            if($res->num_rows > 0){
                $row = $res->fetch_row();
                $xxx = $row[0];
            }
            return $xxx;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtenerPlanInventarioProducto($producto){
        try {
            $xxx = 0;
            $str = "SELECT    pln.tipoinventario FROM gf_movimiento_producto AS mpr
                    LEFT JOIN gf_detalle_movimiento AS dtm ON mpr.detallemovimiento = dtm.id_unico
                    LEFT JOIN gf_plan_inventario    AS pln ON dtm.planmovimiento    = pln.id_unico
                    WHERE mpr.producto = $producto";
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