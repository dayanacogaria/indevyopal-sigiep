<?php
require_once './Conexion/db.php';

class depreciacion{
    private $mysqli;

    public function __construct(){
        $this->mysqli = conectar::conexion();
    }

    public function buscarDepreciacion($fechaI, $fechaF, $proI, $proF){
        try {
            $row = 0;
            $sql = "SELECT    dep.producto, DATE_FORMAT(dep.fecha_dep, '%d/%m/%Y') as fecha, CONCAT_WS(' ', pln.codi, UPPER(pln.nombre)) as nombre, FORMAT(dep.valor,2) as valor
                    FROM      ga_depreciacion dep
                    LEFT JOIN gf_producto_especificacion pes ON dep.producto          = pes.producto
                    LEFT JOIN gf_movimiento_producto     mpr ON mpr.producto          = dep.producto
                    LEFT JOIN gf_detalle_movimiento      dtm ON mpr.detallemovimiento = dtm.id_unico
                    LEFT JOIN gf_movimiento              mov ON dtm.movimiento        = mov.id_unico
                    LEFT JOIN gf_tipo_movimiento         tpm ON mov.tipomovimiento    = tpm.id_unico
                    LEFT JOIN gf_plan_inventario         pln ON dtm.planmovimiento    = pln.id_unico
                    WHERE     (dep.fecha_dep BETWEEN '$fechaI' AND '$fechaF')
                    AND       (dep.producto  BETWEEN $proI     AND $proF)
                    AND       (tpm.clase = 2)";
            $res = $this->mysqli->query($sql);
            if($res->num_rows > 0){
                $row = $res->fetch_all(MYSQLI_ASSOC);
            }
            return $row;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function buscarFecha($fecha, $proI, $proF){
        try {
            $row = 0;
            $sql = "SELECT    dep.producto, DATE_FORMAT(dep.fecha_dep, '%d/%m/%Y') as fecha, CONCAT_WS(' ', pln.codi, UPPER(pln.nombre)) as nombre, FORMAT(dep.valor,2) as valor
                    FROM      ga_depreciacion dep
                    LEFT JOIN gf_producto_especificacion pes ON dep.producto          = pes.producto
                    LEFT JOIN gf_movimiento_producto     mpr ON mpr.producto          = dep.producto
                    LEFT JOIN gf_detalle_movimiento      dtm ON mpr.detallemovimiento = dtm.id_unico
                    LEFT JOIN gf_movimiento              mov ON dtm.movimiento        = mov.id_unico
                    LEFT JOIN gf_tipo_movimiento         tpm ON mov.tipomovimiento    = tpm.id_unico
                    LEFT JOIN gf_plan_inventario         pln ON dtm.planmovimiento    = pln.id_unico
                    WHERE     (dep.fecha_dep = '$fecha')
                    AND       (dep.producto  BETWEEN $proI     AND $proF)
                    AND       (tpm.clase = 2)";
            $res = $this->mysqli->query($sql);
            if($res->num_rows > 0){
                $row = $res->fetch_all(MYSQLI_ASSOC);
            }
            return $row;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function ultimoDia($ano, $mes){
        $dia = date("d", mktime(0, 0, 0, $mes + 1, 0, $ano));
        return date('Y-m-d', mktime(0, 0, 0, $mes, $dia, $ano));
    }

    public function primerDia($ano, $mes){
        return date("Y-m-d", mktime(0, 0, 0, $mes, 1, $ano));
    }

    public function separarObjeto($separador, $objeto){
        $x = explode("$separador", $objeto);
        return $x;
    }

    public function buscarParametroInicio(){
        try {
            $xxx = 0;
            $sql = "SELECT valor FROM gs_parametros_basicos WHERE id_unico = 11";
            $res = $this->mysqli->query($sql);
            $row = $res->fetch_row();
            $xxx = $row[0];
            return $xxx;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function insertar_valor($valor){
        try {
            $sql = "REPLACE INTO gs_parametros_basicos(id_unico, nombre, valor) VALUES (11, 'Inicio_dep','2017-01')";
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
}