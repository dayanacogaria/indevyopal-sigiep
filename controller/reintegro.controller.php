<?php
require_once ('./modelAlmacen/reintegro.php');
require_once ('./modelAlmacen/detallemovimiento.php');
/**
 * Control de Reintegro
 */
class reintegroController{

    private $reintegro;
    private $detallemov;

    public function __construct(){
        $this->reintegro  = new reintegro();
        $this->detallemov = new detallemov();
    }

    public function obtnerResponsableD(){
        if(!empty($_POST['sltDep'])){
            $row = $this->reintegro->obtnerResponsableDependencia($_POST['sltDep']);
            $html = "";
            foreach ($row as $rw) {
                $html .= "<option value=\"$rw[0]\">".$rw[1]." ($rw[2] - $rw[3])"."</option>";
            }
            echo $html;
        }
    }

    public function registrar(){
        $reg   = new reintegro();
        $dtmov = new detallemov();
        $ff = explode("/", $_REQUEST['fecha']);
        $fecha = "$ff[2]-$ff[1]-$ff[0]";

        $reg->tipomovimiento = $_REQUEST['tipo'];
        $reg->numero         = $_REQUEST['numero'];
        $reg->compania       = $_REQUEST['compania'];
        $reg->param          = $_REQUEST['param'];
        $reg->dependencia    = $this->reintegro->obtnerDependencia($_REQUEST['dependencia']);
        $reg->responsable    = $_REQUEST['responsable'];
        $reg->centrocosto    = 12;
        $reg->proyecto       = 2147483647;
        $reg->estado         = 2;
        $reg->fecha          = $fecha;

        $res = $this->reintegro->registrar($reg);
        if($res == true){
            $mov = $this->reintegro->ObtenerUltimoMov($reg->tipomovimiento);
            $por = $this->reintegro->obtnerPorcentaje();
            $this->reintegro->actualizarReintegro($por, $mov);
            $xxx = explode("," , $_REQUEST['marcados']);
            for ($i = 0; $i < count($xxx); $i++) {
                $z = 0;
                $data = explode("-", $xxx[$i]);
                list($dtmv, $proct) = array($data[0], $data[1]);
                $asoc = $this->reintegro->buscarAsociadoMov($dtmv, $mov);
                if(empty($asoc[0])){
                    $z++;
                    $values = $this->detallemov->obtnerDatosDetalle($dtmv);
                    $iva    = (($z * $values[3]) * $por) / 100;

                    $dtmov->cantidad        = $z;
                    $dtmov->valor           = $values[3];
                    $dtmov->iva             = $iva;
                    $dtmov->movimiento      = $mov;
                    $dtmov->detalleasociado = $values[0];
                    $dtmov->planmovimiento  = $values[1];
                    $rmov = $this->detallemov->registrar($dtmov);

                    if($rmov == true){
                        $dtmvn = $this->detallemov->ultimoRegistro($mov);
                        $this->detallemov->mov_pro($proct, $dtmvn[0]);
                    }
                }else{
                    $cant = $asoc[2] + 1;
                    $iva  = (($asoc[1] * $cant) * $por) / 100;
                    $this->detallemov->modificar($cant, $iva , $asoc[0]);
                }
            }
        }
        echo json_encode($res);
    }

    public function consecutivo(){
        if(!empty($_REQUEST['tipo'])){
            @session_start();
            $data = $this->reintegro->generarConsecutivo($_REQUEST['tipo'], $_SESSION['anno'], $_SESSION['compania']);
            echo json_encode($data);
        }
    }

    public function cargarBusqueda(){
        $html     = "";
        $html     .= "<option value=''>Buscar</option>";
        $data     = $this->reintegro->obnterMovsB($_REQUEST['compania'], $_REQUEST['param']);
        foreach ($data as $row) {
            $valorDetalle = $this->reintegro->valorAcumMov($row[0]);
            $html .= "<option value=\"".$row[0]."\">".$row[1]." ".$row[2]." $".number_format($valorDetalle,2,',','.')."</option><br/>";
        }
        echo $html;
    }

    public function eliminar(){
        if(!empty($_REQUEST['detalle'])){
            $res = $this->reintegro->eliminarRelMovP($_REQUEST['detalle'], $_REQUEST['producto']);
            if($res == true){
                $de = $this->reintegro->eliminar($_REQUEST['detalle']);
                echo json_encode($de);
            }else{
                echo json_encode(false);
            }
        }
    }
}