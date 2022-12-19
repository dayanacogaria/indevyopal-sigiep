<?php
/**
 * Modelo de almacén para consulta de informes
 */
require_once ('../Conexion/db.php');
class almacen{

    private $mysqli;

    public function __construct(){
        $this->mysqli = conectar::conexion();
    }

    public function convertirFecha($fecha){
        $fecha = explode("/", $fecha);
        return $fecha[2]."-".$fecha[1]."-".$fecha[0];
    }

    public function obtenerDatosProducto($producto){
        $sql = "SELECT     pln.nombre  AS NOM_PLAN,
                           UPPER(pes.valor)   AS SERIE
                FROM       gf_producto pr
                LEFT JOIN  gf_movimiento_producto     mpr ON mpr.producto          = pr.id_unico
                LEFT JOIN  gf_detalle_movimiento      dtm ON mpr.detallemovimiento = dtm.id_unico
                LEFT JOIN  gf_plan_inventario         pln ON dtm.planmovimiento    = pln.id_unico
                LEFT JOIN  gf_producto_especificacion pes ON pes.producto          = pr.id_unico
                LEFT JOIN  gf_ficha_inventario        fic ON pes.fichainventario   = fic.id_unico
                WHERE      fic.elementoficha   = 6
                AND        pr.id_unico         = $producto
                ORDER BY   pr.id_unico DESC";
        $res = $this->mysqli->query($sql);
        $row = mysqli_fetch_row($res);
        return $row;
        $this->mysqli->close();
    }

    public function obtnerCompania($id_unico){
        try {
            $sql = "SELECT      UPPER(IF(CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) IS NULL
                                OR CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos)='',
                                (ter.razonsocial),
                                CONCAT_WS(' ',ter.nombreuno,ter.nombredos,ter.apellidouno,ter.apellidodos))) AS 'NOMBRE',
                                CONCAT_WS(' :',UPPER(ti.nombre),ter.numeroidentificacion) AS IDENT,
                                ter.digitoverficacion,
                                ter.ruta_logo
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
}