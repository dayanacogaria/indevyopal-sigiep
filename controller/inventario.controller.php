<?php
require_once('./modelAlmacen/inventario.php');
require_once('./modelFactura/concepto.php');
require_once('./clases/tarifa.php');
require_once('./clases/tipoTarifa.php');
require_once './clases/almacen.php';
class inventarioController{

    private $plan;
    private $concepto;
    private $tarifas;
    private $tipo;
    private $alm;

    public function __construct(){
        $this->plan     = new inventario();
        $this->concepto = new concepto();
        $this->tarifas  = new tarifa();
        $this->tipo     = new tipoTarifa();
        $this->alm      = new almacen();
    }

    public function obnterCodigoHijo(){
        $padre      = $_REQUEST['padre'];
        $data_padre = $this->plan->obnterPadreId($padre);
        list($codigo, $predecesor, $ncod, $codigo_hijo, $nivel) = array($data_padre[2], $data_padre[3], strlen($data_padre[2]), 0, 0);
        if(!empty($predecesor)){
            $data_gran_padre    = $this->plan->obnterPadreId($predecesor);
            $digitos_gran_padre = strlen($data_gran_padre[2]);
        }else{
            $digitos_gran_padre = strlen($codigo);
        }

        $totat_digitos = $digitos_gran_padre + 7;

        if($ncod == $totat_digitos){
            $codigo_hijo = 0;
        }else{
            $xhijos = $this->plan->obtnerCantidadHijos($padre);

            if($xhijos != 0){
                $idHijo             = $this->plan->obtnerUltimoCodigoHijo($padre);
                $codigo_ultimo_hijo = $this->plan->obtenerCodigo($idHijo);
                $codigo_hijo        = $codigo_ultimo_hijo + 1;
                $totat_digitos      = $digitos_gran_padre + 4;

                if($ncod == $totat_digitos){
                    $nivel = 8;
                    $codigo_maximo = ($codigo * 1000) + 1000;
                    if($codigo_maximo == $codigo_hijo)
                        $codigo_hijo = 0;
                }else{
                    $codigo_maximo = ($codigo * 100) + 100;
                    if($codigo_maximo == $codigo_hijo)
                        $codigo_hijo = 0;
                }
            }else{
                $totat_digitos = $digitos_gran_padre + 4;
                if($ncod == $totat_digitos){
                    $codigo_hijo = $codigo."001";
                    $nivel       = 8;
                }else{
                    $codigo_hijo = $codigo."001";
                }
            }
        }
        echo $codigo_hijo;
    }

    public function validarTipoInventario(){
        $padre = $_REQUEST['padre'];
        if(!empty($_REQUEST['padre'])){
            $data  = $this->plan->obtnerDatosPlan($padre);
        }else{
            $data[5] = "";
        }
        $html  = "";
        if(!empty($data[5])){
            $tipo  = $data[5];
            $data_tipo  = $this->plan->obtnerTipoInventarioId($tipo);
            $data_tipos = $this->plan->obtnerTipoInventarioDiferentes($tipo);
            $html .= "<option selected value=\"$data_tipo[0]\">".ucwords(mb_strtolower($data_tipo[1]))."</option>";
            foreach ($data_tipos as $row) {
                $html .= "<option value=\"$row[0]\">".ucwords(mb_strtolower($row[1]))."</option>";
            }
        }else{
            $html .= "<option selected value=\"\">Tipo Inventario</option>";
            $tipos = $this->plan->obtnerTipoInventario();
            foreach ($tipos as $row) {
                $html .= "<option value=\"$row[0]\">".ucwords(mb_strtolower($row[1]))."</option>";
            }
        }
        echo $html;
    }

    public function asignar_tipo(){
        $padre = $_REQUEST['codigo'];
        $tipo  = $_REQUEST['tipo'];
        $data  = $this->plan->obtner_hijos($padre);
        $x     = count($data);
        $i     = 0;
        foreach ($data as $row) {
            $res    = $this->plan->actualizar_tipo_inv($row[0], $tipo);
            if($res == true){
                $i++;
            }
        }
        if($x == $i){
            echo json_encode(true);
        }
    }

    public function vistaConceptos(){
        error_reporting(E_ALL);
        ini_set('display_errors', '1');
        $this->concepto->setPlanInventario($_GET['plan']);
        $data      = $this->concepto->obtenerConceptoPlan();
        $tarifas   = $this->tarifas->obtenerListado();
        $conceptos = $this->concepto->obtenerConceptosPlanId();
        $xda       = $this->plan->obtenerConceptoFactura($_GET['plan']);
        $tipo      = $this->tipo->obtenerTodos();
        $xuni      = $this->plan->obtnerUnidadFactor();
        $this->plan->setIdUnico($_GET['plan']);
        $info      = $this->plan->obtener($_GET['plan']);
        require_once './vistas/inventario/conceptos.php';
    }

    public function listadoProductos(){
        require_once './vistas/inventario/producto.listado.php';
    }

    public function ActualizarPorcentajeIncremento(){
        $this->alm->ActualizarPorcentajeIncremento($_REQUEST['id'], $_REQUEST['porcentaje']);
    }

    public function obtenerListadoD(){
        $html = "";
        if(!empty($_REQUEST['elemento'])){
            $data = $this->plan->obtenerElementosDiferenteId($_REQUEST['elemento']);
        }else{
            $data = $this->plan->obtenerListadoElementos();
        }

        foreach ($data as $row){
            $html .= "<option value='$row[0]'>$row[1]</option>";
        }
        echo $html;
    }

    public function obtenerDataAsociados(){
        if(!empty($_REQUEST['elemento'])){
            $data = $this->plan->obtenerDataAsociados($_REQUEST['elemento'], $_REQUEST['tarifa']);
            $html  = "";
            if (count($data) > 0){
                foreach ($data as $datum) {
                    $html .= "<tr>";
                    $html .= "<td>$datum[1]</td>";
                    $html .= "<td>$datum[2]</td>";
                    $html .= "<td><a href='javascript:eliminarHerencia($datum[0])' id='linkD$datum[0]' data-tarifa='".$_REQUEST['tarifa']."'><span class='glyphicon glyphicon-trash'></span></a></td>";
                    $html .= "</tr>";
                }
            }else{
                $html .= "<tr>";
                $html .= "<td colspan='3' style='text-align: center;'>No hay relación de herencia</td>";
                $html .= "</tr>";
            }
        }
        echo $html;
    }

    public function registroDataHerencia(){
        if(!empty($_REQUEST['hijos'])){
            $hijos = explode(",", $_REQUEST['hijos']);
            $x     = 0;
            foreach ($hijos as $dthijo){
                $xxx = $this->plan->guardarRelacionData($_REQUEST['padre'], $dthijo, $_REQUEST['tarifa'], $_REQUEST['cantidad']);
                if($xxx == true){
                    $x++;
                }
            }
            echo json_encode(array("data" => $x));
        }else{
            echo json_encode(array("data" => 0));
        }
    }

    public function eliminarRelacion(){
        if(!empty($_REQUEST['id'])){
            $xxx = $this->plan->eliminarRelacion($_REQUEST['id']);
            echo json_encode(array("data" => $xxx));
        }else{
            echo json_encode(array("data" => false));
        }
    }
}