<?php
require_once './modelAlmacen/dep.php';

class depController{
    private $dep;

    public function __construct(){
        $this->dep = new depreciacion();
    }

    public function buscarDep(){
        if(
            !empty($_REQUEST['txtFechaFinal'])      &&
            !empty($_REQUEST['sltProductoInicial']) &&
            !empty($_REQUEST['sltProductoFinal'])
        ){

            $fff = $this->dep->separarObjeto("/", $_REQUEST['txtFechaFinal']);

            $fechaF = $this->dep->ultimoDia($fff[1], $fff[0]);

            $data   = $this->dep->buscarDepreciacion($fechaF, $_REQUEST['sltProductoInicial'], $_REQUEST['sltProductoFinal']);
            echo json_encode($data);
        }
    }

    public function verificarMesAnterior(){
        if(
            !empty($_REQUEST['txtFechaFinal'])      &&
            !empty($_REQUEST['sltProductoInicial']) &&
            !empty($_REQUEST['sltProductoFinal'])
        ){
            $fff = $this->dep->separarObjeto("/", $_REQUEST['txtFechaFinal']);

            $fechaF = $this->dep->ultimoDia($fff[1], $fff[0]);
            $fechaF = strtotime($fechaF);
            $fechaFn = strtotime('-1 month -1 day', $fechaF);

            $data    = $this->dep->buscarFecha($fechaFn, $_REQUEST['sltProductoInicial'], $_REQUEST['sltProductoFinal']);
            echo json_encode($data);
        }
    }

    public function buscarParametroInicio(){
        $data = $this->dep->buscarParametroInicio();
        echo json_encode($data);
    }

    public function registrarParametroInicial(){
        if(!empty($_REQUEST['txtDepInici'])){
            $data = $this->dep->insertar_valor($_REQUEST['txtDepInici']);
            echo json_encode($data);
        }
    }
}