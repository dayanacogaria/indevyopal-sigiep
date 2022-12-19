<?php
session_start();
#@Autor:Alexander
#La session o el case inicia en dos porque ya existe la destrucción de sessiones de comprobante 
#y este acrhivo se convertira en el archivo  general de destrucción de sessiones 
$session = $_POST['session'];
switch ($session){
    case 2:
        if(!empty($_SESSION['factura']) && !empty($_SESSION['idFactura'])){
            $_SESSION['factura'] = "";
            $_SESSION['idFactura'] = ""; 
        }
    break;
    case 3:
        if(!empty($_SESSION['idpago']) && !empty($_SESSION['pago'])){
           $_SESSION['idpago']="";
           $_SESSION['pago']="";
           $_SESSION['cupones']="";
           $_SESSION['valor']="";
           $_SESSION['terceroConsulta'] = "";
        }
        break;
    case 4:
        echo 'RF_REQUISICION_ALMACEN.php';
        break;
    case 5:
        echo 'RF_ORDEN_DE_COMPRA.php';
        break;
    case 6:
        echo 'RF_MOVIMIENTO_ALMACEN.php';
        break;
    case 7:
        if(!empty($_SESSION['idComprobanteI'])){           
            $_SESSION['idComprobanteI'] = "";
        }        
        break;
    case 8:
        if(!empty($_SESSION['tipoProceso'])){
            $_SESSION['tipoProceso']  = "";
        }        
        break;
    case 9:
        if(!empty($_SESSION['data'])){
            $_SESSION['data'] = "";
        }
        break;
    case 10:
        echo 'registrar_GR_SALIDA_ALMACEN.php';
        break;
}
?>