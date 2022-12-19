<?php
require_once ('./modelAlmacen/bajaElementos.php');
require_once ('./modelAlmacen/detallemovimiento.php');
require_once ('./modelAlmacen/reintegro.php');
/**
 * Controllador de Baja de elementos
 */
class bajaController{

    private $baja;
    private $detallemov;
    private $reintegro;

    public function __construct(){
        $this->baja       = new baja();
        $this->detallemov = new detallemov();
        $this->reintegro  = new reintegro();
    }

    public function registrar(){
        $bja = new reintegro();
        $dtm = new detallemov();

        $dependencia         = $this->reintegro->obtnerDependencia($_REQUEST['dependencia']);

        $f                   = explode("/", $_REQUEST['fecha']);
        $fecha               = "$f[2]-$f[1]-$f[0]";

        $bja->tipomovimiento = $_REQUEST['tipo'];
        $bja->numero         = $_REQUEST['numero'];
        $bja->compania       = $_REQUEST['compania'];
        $bja->param          = $_REQUEST['param'];
        $bja->dependencia    = $dependencia;
        $bja->responsable    = $this->baja->obtnerResponsableDepdencia($dependencia);
        $bja->centrocosto    = 12;
        $bja->proyecto       = 2147483647;
        $bja->estado         = 2;
        $bja->fecha          = $fecha;

        $res = $this->reintegro->registrar($bja);

        if($res == true){
            $mov = $this->reintegro->ObtenerUltimoMov($bja->tipomovimiento);
            $por = $this->reintegro->obtnerPorcentaje();
            $this->reintegro->actualizarReintegro($por, $mov);
            $xxx = explode(",", $_REQUEST['marcados']);
            for ($i = 0; $i < count($xxx); $i++) {
                $x = 0;
                $data = explode("-", $xxx[$i]);
                list($dtmv, $pro) = array($data[0], $data[1]);
                $asoc = $this->reintegro->buscarAsociadoMov($dtmv, $mov);

                if(empty($asoc[0])){
                    $x++;
                    $values = $this->detallemov->obtnerDatosDetalle($dtmv);
                    $iva    = (($x * $values[3]) * $por) / 100;

                    $dtm->cantidad        = $x;
                    $dtm->valor           = $values[3];
                    $dtm->iva             = $iva;
                    $dtm->movimiento      = $mov;
                    $dtm->detalleasociado = $values[0];
                    $dtm->planmovimiento  = $values[1];

                    $rmov = $this->detallemov->registrar($dtm);

                    if($rmov == true){
                        $dtmvn = $this->detallemov->ultimoRegistro($mov);
                        $this->detallemov->mov_pro($pro, $dtmvn[0]);
                    }
                }else{
                    $cant = $asoc[2] + 1;
                    $iva  = (($asoc[1] * $cant) * $por) / 100;
                    $this->detallemov->modificar($cant, $iva, $asoc[0]);
                    $this->detallemov->mov_pro($pro, $asoc[0]);
                }

                $this->baja->modificarBaja($pro, 1);
            }
        }
        echo json_encode($res);
    }

    public function obtnerRes(){
        if(!empty($_REQUEST['dependencia'])){
            $data = $this->baja->obtenerResponsablesDepdencia($_REQUEST['dependencia']);
            echo json_encode($data);
        }
    }

    public function consecutivo(){
        if(!empty($_REQUEST['tipo'])){
            @session_start();
            $data = $this->baja->generarConsecutivo($_REQUEST['tipo'], $_SESSION['anno'], $_SESSION['compania']);
            echo json_encode($data);
        }
    }

    public function cargarBusqueda(){
        $html     = "";
        $html     .= "<option value=''>Buscar</option>";
        $data     = $this->baja->obnterMovsB($_REQUEST['compania'], $_REQUEST['param']);
        foreach ($data as $row) {
            $valorDetalle = $this->baja->valorAcumMov($row[0]);
            $html .= "<option value=\"".$row[0]."\">".$row[1]." ".$row[2]." $".number_format($valorDetalle,2,',','.')."</option><br/>";
        }
        echo $html;
    }

    public function eliminar(){
        if(!empty($_REQUEST['detalle']) && !empty($_REQUEST['producto'])){
            $res = $this->baja->eliminarRelMovP($_REQUEST['detalle'], $_REQUEST['producto']);
            if($res == true){
                $st = $this->baja->cambiarEstado($_REQUEST['producto']);
                $xx = $this->baja->eliminar($_REQUEST['detalle']);
                echo json_encode($xx);
            }else{
                echo json_encode(false);
            }
        }else{
            echo json_encode(false);
        }
    }
}