<?php
require_once ('../Conexion/db.php');
class producto{

    public $id_unico;
    public $descripcion;
    public $valor;
    public $meses;
    public $fecha;
    public $vida_util_remanente;

    private $mysqli;

    public function __construct(){
        $this->mysqli = conectar::conexion();
    }

    public function registrar(producto $data){
        try {
            $sql = "INSERT INTO gf_producto(descripcion, valor, fecha_adquisicion) VALUES('$data->descripcion', $data->valor, '$data->fecha')";
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

    public function ultimo_registro(){
        try {
            $sql = "SELECT MAX(id_unico) FROM gf_producto";
            $res = $this->mysqli->query($sql);
            $row = $res->fetch_row();
            return $row[0];
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtenerProductoEntrada($placa, $compania, $codigo){
        try {
            if(!empty($placa)){
                $sql = "SELECT    mpr.detallemovimiento,mpr.producto
                        FROM      gf_movimiento_producto mpr
                        LEFT JOIN gf_producto_especificacion  pes ON pes.producto          = mpr.producto
                        LEFT JOIN gf_detalle_movimiento       dtm ON mpr.detallemovimiento = dtm.id_unico
                        LEFT JOIN gf_movimiento               mov ON dtm.movimiento        = mov.id_unico
                        LEFT JOIN gf_tipo_movimiento          tpm ON mov.tipomovimiento    = tpm.id_unico
                        LEFT JOIN gf_producto                 pro ON mpr.producto          = pro.id_unico
                        WHERE     pes.fichainventario = 6
                        AND       pes.valor           = '$placa'
                        AND       tpm.clase           = 2
                        AND       mov.compania        = $compania 
                        ORDER BY  mpr.producto DESC";
            }  else { 
                $sql = "SELECT   DISTINCT  mpr.detallemovimiento,mpr.producto
                    FROM      gf_movimiento_producto mpr
                    LEFT JOIN gf_producto_especificacion  pes ON pes.producto          = mpr.producto
                    LEFT JOIN gf_detalle_movimiento       dtm ON mpr.detallemovimiento = dtm.id_unico
                    LEFT JOIN gf_movimiento               mov ON dtm.movimiento        = mov.id_unico
                    LEFT JOIN gf_tipo_movimiento          tpm ON mov.tipomovimiento    = tpm.id_unico
                    LEFT JOIN gf_producto                 pro ON mpr.producto          = pro.id_unico
                    LEFT JOIN gf_plan_inventario          pi  ON dtm.planmovimiento    = pi.id_unico 
                    WHERE     tpm.clase           = 2   
                    AND       pi.codi = '$codigo'
                    AND       mov.compania        = $compania  
                    AND       mpr.producto NOT IN 
                        (SELECT mpa.producto 
                         FROM gf_movimiento_producto mpa 
                         LEFT JOIN gf_detalle_movimiento dma ON mpa.detallemovimiento = dma.id_unico 
                         LEFT JOIN gf_movimiento ma ON dma.movimiento = ma.id_unico 
                         LEFT JOIN gf_tipo_movimiento tma ON ma.tipomovimiento = tma.id_unico 
                         WHERE tma.clase = 3)
                    ORDER BY  dtm.id_unico";
            }
            $res = $this->mysqli->query($sql);
            $row = mysqli_fetch_array($res);
            return $row;
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerUltimoConsecutivo(){
        try {
            $sql = "SELECT
                               pes.valor  AS SERIE
                    FROM       gf_producto pr
                    LEFT JOIN  gf_movimiento_producto     mpr ON mpr.producto          = pr.id_unico
                    LEFT JOIN  gf_detalle_movimiento      dtm ON mpr.detallemovimiento = dtm.id_unico
                    LEFT JOIN  gf_plan_inventario         pln ON dtm.planmovimiento    = pln.id_unico
                    LEFT JOIN  gf_producto_especificacion pes ON pes.producto          = pr.id_unico
                    LEFT JOIN  gf_ficha_inventario        fic ON pes.fichainventario   = fic.id_unico
                    WHERE      fic.elementoficha   = 6
                    ORDER BY   pes.id_unico DESC
                    LIMIT 1";
            $res = $this->mysqli->query($sql);
            $row = mysqli_fetch_row($res);
            return $row[0];
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public  function obtenerProductosDetalle($detalle){
        try {
            $detalles = array();
            $sql = "SELECT producto FROM gf_movimiento_producto WHERE detallemovimiento = $detalle";
            $res = $this->mysqli->query($sql);
            if(mysqli_num_rows($res) > 0){
                while($row = mysqli_fetch_row($res)){
                    $detalles[] = $row[0];
                }
            }
            return $detalles;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerValorProducto($producto){
        try {
            $sql = "SELECT valor FROM gf_producto WHERE id_unico = $producto";
            $res = $this->mysqli->query($sql);

            $row = mysqli_fetch_row($res);
            return $row[0];
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerCodigoProducto($id_unico){
        try {
            $sql = "SELECT
                               pes.valor  AS SERIE
                    FROM       gf_producto pr
                    LEFT JOIN  gf_movimiento_producto     mpr ON mpr.producto          = pr.id_unico
                    LEFT JOIN  gf_detalle_movimiento      dtm ON mpr.detallemovimiento = dtm.id_unico
                    LEFT JOIN  gf_plan_inventario         pln ON dtm.planmovimiento    = pln.id_unico
                    LEFT JOIN  gf_producto_especificacion pes ON pes.producto          = pr.id_unico
                    LEFT JOIN  gf_ficha_inventario        fic ON pes.fichainventario   = fic.id_unico
                    WHERE      fic.id_unico   = 6
                    AND        pr.id_unico    = $id_unico
                    ORDER BY   pes.id_unico DESC";
            $res = $this->mysqli->query($sql);
            $row = mysqli_fetch_row($res);
            return $row[0];
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerCodigoProductoPlaca($id_unico){
        try {
            $sql = "SELECT
                               pes.valor  AS PLACA
                    FROM       gf_producto pr
                    LEFT JOIN  gf_movimiento_producto     mpr ON mpr.producto          = pr.id_unico
                    LEFT JOIN  gf_detalle_movimiento      dtm ON mpr.detallemovimiento = dtm.id_unico
                    LEFT JOIN  gf_plan_inventario         pln ON dtm.planmovimiento    = pln.id_unico
                    LEFT JOIN  gf_producto_especificacion pes ON pes.producto          = pr.id_unico
                    LEFT JOIN  gf_ficha_inventario        fic ON pes.fichainventario   = fic.id_unico
                    WHERE      fic.elementoficha   = 6
                    AND        pr.id_unico         = $id_unico
                    ORDER BY   pes.id_unico DESC";
            $res = $this->mysqli->query($sql);
            $row = mysqli_fetch_row($res);
            return $row[0];
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerFechaEntrada($id_unico){
        try {
            $sql = "SELECT     DATE_FORMAT(mov.fecha,'%d/%m/%Y')
                    FROM       gf_movimiento_producto mpr
                    LEFT JOIN  gf_detalle_movimiento  dtm ON mpr.detallemovimiento = dtm.id_unico
                    LEFT JOIN  gf_producto            pro ON mpr.producto          = pro.id_unico
                    LEFT JOIN  gf_movimiento          mov ON dtm.movimiento        = mov.id_unico
                    LEFT JOIN  gf_tipo_movimiento     tpm ON mov.tipomovimiento    = tpm.id_unico
                    WHERE      pro.id_unico  = $id_unico
                    AND        tpm.clase     = 2";
            $res = $this->mysqli->query($sql);
            $row = mysqli_fetch_row($res);
            return $row[0];
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerValorEntrada($id_unico){
        try {
            $sql = "SELECT     dtm.valor
                    FROM       gf_movimiento_producto mpr
                    LEFT JOIN  gf_detalle_movimiento  dtm ON mpr.detallemovimiento = dtm.id_unico
                    LEFT JOIN  gf_producto            pro ON mpr.producto          = pro.id_unico
                    LEFT JOIN  gf_movimiento          mov ON dtm.movimiento        = mov.id_unico
                    LEFT JOIN  gf_tipo_movimiento     tpm ON mov.tipomovimiento    = tpm.id_unico
                    WHERE      pro.id_unico  = $id_unico
                    AND        tpm.clase     = 2";
            $res = $this->mysqli->query($sql);
            $row = mysqli_fetch_row($res);
            return $row[0];
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function obtnerVidaUtil($id_unico){
        try {
            $vida = 0;
            $sql = "SELECT vida_util_remanente FROM gf_producto WHERE id_unico = $id_unico";
            $res = $this->mysqli->query($sql);
            if($res->num_rows > 0){
                $row  = mysqli_fetch_row($res);
                $vida = $row[0];
            }
            return $vida;
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage($e));
        }
    }

    public function encontrarProducto($placa){
        try {
            $xxx = 0;
            $sql = "SELECT producto FROM gf_producto_especificacion WHERE valor = $placa AND fichainventario = 6";
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
}

class productoEsp{

    public $id_unico;
    public $valor;
    public $producto;
    public $fichainventario;

    private $mysqli;

    public function __construct(){
        $this->mysqli = conectar::conexion();
    }

    public function buscarFichaI($id_unico){
        try {
            $xx = 0;
            if(!empty($id_unico)){
                $sql = "SELECT    fin.id_unico
                        FROM      gf_ficha_inventario fin
                        LEFT JOIN gf_elemento_ficha elm ON elm.id_unico = fin.elementoficha
                        LEFT JOIN gf_tipo_dato tpd      ON elm.tipodato = tpd.id_unico
                        WHERE     elm.id_unico = $id_unico
                        AND       fin.ficha    = 2
                        ORDER BY  elm.id_unico ASC";
                $res = $this->mysqli->query($sql);
                $row = $res->fetch_row();
                $xx  = $row[0];
            }
            return $xx;

        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function registrar(productoEsp $data){
        try {
            $sql = "INSERT INTO gf_producto_especificacion(
                                    valor,
                                    producto,
                                    fichainventario
                                ) VALUES(
                                    '$data->valor',
                                    $data->producto,
                                    $data->fichainventario
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
}