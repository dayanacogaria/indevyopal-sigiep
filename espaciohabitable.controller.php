<?php
require './clases/espacioHabitables.php';
require './modelFactura/concepto.php';
require './clases/tipodato.php';
require './clases/caracteristica.php';

class espaciohabitableController{

    private $espacio;
    private $conceptos;
    private $tipodato;
    private $caracteristica;

    public function __construct(){
        $this->espacio        = new espacioHabitables();
        $this->conceptos      = new concepto();
        $this->tipodato       = new tipodato();
        $this->caracteristica = new caracteristica();
    }

    public function index(){
        $data     = $this->espacio->obtenerListado();
        $tipoDato = $this->tipodato->obtenerListado();
        require_once './vistas/espacioHabitable/index.php';
    }

    public function registrar(){
        $tipos        = $this->espacio->obtenerTipos();
        $asociados    = $this->espacio->obtenerAsociados();
        require_once './vistas/espacioHabitable/create.php';
    }

    public function guardar(){    
        $ruta = "";
        $codigo = htmlspecialchars(filter_input(INPUT_POST, 'txtCodigo'), ENT_QUOTES, 'UTF-8');
        if ($_REQUEST['formato'] == 1){            
            if(!empty($_FILES['txtRuta']['name'])){           
                $path = "./documentos/archivos/";
                $trgt = $path.basename($codigo."_".$_FILES['txtRuta']['name']);
                move_uploaded_file($_FILES['txtRuta']['tmp_name'], $trgt);
                $ruta = $trgt;
            }else{                
                $ruta = '';
            }
        }else if ($_REQUEST['formato'] == 2){
            if (!empty($_REQUEST['txtUrl'])){
                $ruta = str_replace("watch?v=","embed/",$_REQUEST['txtUrl']);
            }else{
                $ruta = '';
            }
        }
        $espacio                         = new espacioHabitables();
        $this->espacio->tipo             = htmlspecialchars(filter_input(INPUT_POST, 'sltTipo'), ENT_QUOTES, 'UTF-8');
        $this->espacio->codigo           = htmlspecialchars(filter_input(INPUT_POST, 'txtCodigo'), ENT_QUOTES, 'UTF-8');
        $this->espacio->descripcion      = htmlspecialchars(filter_input(INPUT_POST, 'txtDescripcion'), ENT_QUOTES, 'UTF-8');
        $this->espacio->dependencia      = htmlspecialchars(filter_input(INPUT_POST, 'sltDependencia'), ENT_QUOTES, 'UTF-8');
        $this->espacio->asociado         = !empty($_POST['sltAsociado'])?htmlspecialchars(filter_input(INPUT_POST, 'sltAsociado'), ENT_QUOTES, 'UTF-8'):'NULL';
        $this->espacio->ruta             = $ruta;
        $data = $espacio->registrar($this->espacio);
        $url = 'access.php?controller=EspacioHabitable';
        require_once './vistas/respuesta/index.php';
    }

    public function actualizar(){
        $espacio = new espacioHabitables();
        $data    = $espacio->obtener($_REQUEST['id']);
        require_once './vistas/espacioHabitable/edit.php';
    }

    public function editar(){        
        $ruta = "";
        $codigo = htmlspecialchars(filter_input(INPUT_POST, 'txtCodigo'), ENT_QUOTES, 'UTF-8');
        if ($_REQUEST['formato'] == 1){
            if (!empty($_FILES['txtRuta']['name'])){
                if(!empty($_REQUEST['txtRutaX'])){
                    $url = $_REQUEST['txtRutaX'];
                    // valida si es una url valida
                    if (filter_var($url, FILTER_VALIDATE_URL) === false && !empty($url)) {
                        // si NO es una url valida elimina el archivo
                        unlink($_REQUEST['txtRutaX']);
                    }
                }
                $path = "./documentos/archivos/";
                $trgt = $path.basename($codigo."_".$_FILES['txtRuta']['name']);
                move_uploaded_file($_FILES['txtRuta']['tmp_name'], $trgt);
                $ruta = $trgt;
            }else{                    
                if(!empty($_REQUEST['txtRutaX'])){
                    $ruta = $_REQUEST['txtRutaX'];
                }else{                    
                    $ruta = '';
                }
            }
        }else if ($_REQUEST['formato'] == 2){
            if (!empty($_REQUEST['txtUrl'])){                
                $url = $_REQUEST['txtRutaX'];
                // valida si es una url valida
                if (filter_var($url, FILTER_VALIDATE_URL) === false) {
                    // si NO es una url valida elimina el archivo
                    unlink($_REQUEST['txtRutaX']);
                }
                $ruta = str_replace("watch?v=","embed/",$_REQUEST['txtUrl']);
            }else{
                $ruta = $_REQUEST['txtRutaX'];
            }            
        }    
        $espacio                         = new espacioHabitables();
        $id                              = $_REQUEST['txtId'];
        $asociado                        = htmlspecialchars(filter_input(INPUT_POST, 'sltAsociado'), ENT_QUOTES, 'UTF-8');
        $this->espacio->id_unico         = $id;
        $this->espacio->tipo             = htmlspecialchars(filter_input(INPUT_POST, 'sltTipo'), ENT_QUOTES, 'UTF-8');
        $this->espacio->codigo           = htmlspecialchars(filter_input(INPUT_POST, 'txtCodigo'), ENT_QUOTES, 'UTF-8');
        $this->espacio->descripcion      = htmlspecialchars(filter_input(INPUT_POST, 'txtDescripcion'), ENT_QUOTES, 'UTF-8');        
        $this->espacio->dependencia      = htmlspecialchars(filter_input(INPUT_POST, 'sltDependencia'), ENT_QUOTES, 'UTF-8');
        $this->espacio->asociado         = empty($asociado)?'NULL':$asociado;
        $this->espacio->ruta             = $ruta;
        $data = $espacio->actualizar($this->espacio);
        $url = 'access.php?controller=EspacioHabitable';
        require_once './vistas/respuesta/edit.php';
    }

    public  function eliminar(){
        $espacio  = new espacioHabitables();
        $id       = $_REQUEST['id'];
        $ruta     = $espacio->ObtenerRuta($id);
        if (!empty($ruta[0])){
            unlink($ruta[0]);
        }
        $data     = $espacio->eliminar($id);
        echo json_encode($data);
    }

    public function conceptosFactura(){
        require_once './vistas/espacioHabitable/conceptos.php';
        $data = $this->conceptos->obtenerListado();
    }

    public function obtenerCaracteristicasEspacios(){
        if(!empty($_REQUEST['espacio'])){
            $idE  = $_REQUEST['espacio'];
            $data = $this->caracteristica->obtenerCaracteristicasObjeto($_REQUEST['espacio']);
            $html = "";
            if(count($data) > 0){
                $html .= "<h4 class='text-center'>Características</h4>";
                foreach ($data as $row){
                    $html .= "<div class='form-group'>";
                    $html .= "\n\t<label class='control-label col-sm-2 col-md-2 col-lg-4 text-right text-info'>Caracteristica :</label>";
                    $html .= "\n\t<label class='control-label col-sm-3 col-md-3 col-lg-3 text-left'>$row[1]</label>";
                    $html .= "\n\t<label class='control-label col-sm-1 col-md-1 col-lg-1 text-right text-info'>Valor :</label>";
                    $html .= "\n\t<label class='col-sm-3 col-md-3 col-lg-3 text-left'>$row[2]</label>";
                    $html .= "\n\t<div class='col-sm-1 col-md-1 col-lg-1'>";
                    $html .= "\n\t\t<a class='glyphicon glyphicon-trash btn-sm btn-primary borde-sombra' href='javascript:eliminarCaracteristica($row[0], \"$idE\")' title='Eliminar Característica'></a>";
                    $html .= "\n\t</div>";
                    $html .= "</div>";
                    $html .= "<br/>";
                    $html .= "<br/>";
                }
            }else{
                $html .= "<h2 class='text-center'>No hay características definidas..</h2>";
            }
            echo $html;
        }
    }

    public function obtenerEspaciosSinCaracterisiticas(){
        $data = $this->espacio->obtenerListado();
        $html = "";
        while ($row = mysqli_fetch_row($data)){
            $html .= "<option value='$row[0]'>$row[2] $row[3]</option>";
        }
        echo $html;
    }

    public function GuardarCaracteristicas(){
        try {
            $car = new caracteristica();
            $dataE = $this->espacio->obtener($_REQUEST['espacio']);
            $id    = $dataE[0];
            if($_REQUEST['xTodos'] == 1){
                $espacios = $this->espacio->obtenerListado();
                while($item = mysqli_fetch_row($espacios)){
                    foreach ($_REQUEST['data'] as $row){
                        $this->caracteristica->tipo_dato = $row['tipo'];
                        $this->caracteristica->nombre    = $row['nombre'];
                        $this->caracteristica->valor     = $row['valor'];
                        $this->caracteristica->espacio   = $item[0];
                        $car->registrar($this->caracteristica);
                    }
                }
            }else{
                foreach ($_REQUEST['data'] as $row){
                    $this->caracteristica->tipo_dato = $row['tipo'];
                    $this->caracteristica->nombre    = $row['nombre'];
                    $this->caracteristica->valor     = $row['valor'];
                    $this->caracteristica->espacio   = $id;
                    $car->registrar($this->caracteristica);
                }
            }

            if(!empty($_REQUEST['espacios'])){
                $espacios = $_REQUEST['espacios'];
                for ($i = 0; $i < count($espacios); $i++){
                    echo $espacios[$i];
                    foreach ($_REQUEST['data'] as $row){
                        $this->caracteristica->tipo_dato = $row['tipo'];
                        $this->caracteristica->nombre    = $row['nombre'];
                        $this->caracteristica->valor     = $row['valor'];
                        $this->caracteristica->espacio   = $espacios[$i];
                        $car->registrar($this->caracteristica);
                    }
                }
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function EliminarCaracteristica(){
        if(!empty($_REQUEST['id'])){
            $res = $this->caracteristica->eliminarCaracteristica($_REQUEST['id']);
        }
    }

    public function novedad() {
        $data = $this->espacio->obtenerNovedad();
        $tipoDato = $this->tipodato->obtenerListado();
        require_once './vistas/espacioHabitable/novedad.php';
    }

    public function guardarNovedad(){
        $nombre = $_POST["nombre"];
        $estado = $_POST["estado"];
        $data = $this->espacio->registrarNovedad($nombre, $estado);
        $url = 'access.php?controller=EspacioHabitable&action=novedad';
        require_once './vistas/respuesta/index.php';
    }

    public function actualizarNovedad(){
        $id = $_POST["idx"];
        $nombre = $_POST["nombrex"];
        $estado = $_POST["estadox"];
        $data = $this->espacio->actualizarNovedad($id, $nombre, $estado);
        $url = 'access.php?controller=EspacioHabitable&action=novedad';
        require_once './vistas/respuesta/edit.php';
    }

    public function eliminarNovedad(){
        $id = $_GET["id"];
        $data = $this->espacio->eliminarNovedad($id);;
        echo json_encode($data);
    }

    public function novedadEspacio() {
        $id = $_GET['id'];
        $data = $this->espacio->obtenerNovedadEspacio($id);
        $espacio = $this->espacio->obtenerEspacio($id);
        $novedades = $this->espacio->obtenerNovedad();
        require_once './vistas/espacioHabitable/novedad_espacio.php';
    }

    public function guardarNovedadEspacio(){
        $novedad = $_POST['sltNovedad'];
        $espacio = $_POST['idespacio'];
        $fechaI  = espaciohabitableController::formatearFechaHora($_POST['txtFechaI']);
        $fechaF  = $_POST['txtFechaF'];
        if(!empty($fechaF)){
            $fechaF  = espaciohabitableController::formatearFechaHora($fechaF);
        }else{
            $fechaF = '';
        }
        $data = $this->espacio->registrarNovedadEspacio($novedad, $espacio, $fechaI, $fechaF);
        $url = 'access.php?controller=EspacioHabitable&action=novedadEspacio&id='.md5($espacio);
        require_once './vistas/respuesta/index.php';
    }

    public function validarNovedad(){
        $id = $_GET['id'];
        $data = $this->espacio->validarNovedad($id);
        if($data[0] > 0){
            $dataf = $this->espacio->obtenerFechaNovedad($data[0]);
            if($dataf[0] != '0000-00-00 00:00:00'){
                $fechaI = date("d/m/Y", strtotime(substr($dataf[0],1,10))) . " ". substr($dataf[0],11,5);
                echo $fechaI;
            }else{
                echo 0;
            }
        }else{
            echo 2;
        }
    }

    public function formatearFechaHora($fecha) {
        $ff = explode("/", $fecha);
        $xx = explode(" ", $ff[2]);
        $fecha = trim($xx[0]) . "-$ff[1]-$ff[0] $xx[1]:00";
        return $fecha;
    }

    public function validarEdicionNovedad(){
        $id = $_POST['id'];
        $data = $this->espacio->obtenerminFecha($id);
        if(!empty($data[0])){
            $fechaFval = date("d/m/Y", strtotime(substr($data[0],1,10))) . " ". substr($data[0],11,5);
        }else{
            $fechaFval = 0;
        }

        $fechaI = date("d/m/Y", strtotime($_POST['fechaI']));
        if(!empty($_POST['fechaF'])){
            $fechaF = date("d/m/Y", strtotime($_POST['fechaF']));
        }
        $json = array($fechaI, $fechaF, $fechaFval);
        echo $jsonarray = json_encode($json);
    }

    public function actualizarNovedadEspacio(){
        $id = $_POST["idnovespacio"];
        $novedad = $_POST["sltNovedadx"];
        $fechaI  = espaciohabitableController::formatearFechaHora($_POST["txtFechaIx"]);
        $fechaF = $_POST["txtFechaFx"];
        if(!empty($fechaF)){
            $fechaF  = espaciohabitableController::formatearFechaHora($fechaF);
        }else{
            $fechaF = '';
        }
        $espacio = $_POST["idespacio"];
        $data = $this->espacio->actualizarNovedadEspacio($id, $novedad, $fechaI, $fechaF);
        $url = 'access.php?controller=EspacioHabitable&action=novedadEspacio&id='.md5($espacio);
        require_once './vistas/respuesta/edit.php';
    }

    public function eliminarNovedadEspacio(){
        $id = $_GET["id"];
        $data = $this->espacio->eliminarNovedadEspacio($id);
        echo json_encode($data);
    }
    
    public function validacionIngreso(){
        @session_start();
        $anno = $_SESSION['anno'];
        $id = $_GET["id"];
        $fechaI = espaciohabitableController::formatearFechaHora($_GET["fechaI"]);
        $res =0; 
        $data1 = $this->espacio->validacionBloqueo($id, $fechaI);
        if (empty($data1[0])){
            $data2 = $this->espacio->validacionReserva($id, $fechaI);  
            if (empty($data2[0])){
                $data3 = $this->espacio->validacionIngreso($id, $fechaI);
                if (empty($data3[0])){
                    $res = 0;
                }else{
                    // no registra por ingreso
                    $res = 1;
                }
            }else{
                // no registra por reserva
                $res = 2;
            }
        }else {
            // no registra por bloqueo
            $res = 3;
        }
        echo $res;
    }
}
