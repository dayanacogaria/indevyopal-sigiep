<?
require_once ('../Conexion/db.php');
class proEsp{

    public $id_unico;
    public $valor;
    public $producto;
    public $fichainventario;

    private $mysqli;

    public function __construct(){
        $this->mysqli = conectar::conexion();
    }

    public function registrar(proEsp $data){
        try {
            $sql = "INSERT INTO gf_producto_especificacion(
                                    valor,
                                    producto,
                                    fichainventario
                                ) VALUES(
                                    \"$data->valor\",
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

    public function buscarFichaI($id_unico){
        try {
            $sql = "SELECT    fin.id_unico
                    FROM      gf_ficha_inventario fin
                    LEFT JOIN gf_elemento_ficha elm ON elm.id_unico = fin.elementoficha
                    LEFT JOIN gf_tipo_dato tpd      ON elm.tipodato = tpd.id_unico
                    WHERE     elm.id_unico = $id_unico
                    ORDER BY  elm.id_unico ASC";
            $res = $this->mysqli->query($sql);
            $row = $res->fetch_row();
            return $row[0];
            $this->mysqli->close();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
}