<!-- Historial de Actualizaciones  (ctl+F con la fecha para acceso rápido)
     08/02/2017 - Daniel N: Se añadieron los casos Entidad Afiliación, Entidad Financiera y Todos.
     -->
<?php
    
    $tercero = $_GET["tercero"];
    echo $tercero;
    
    switch ($tercero) {
    case "EntAfil":
        header('Location:listar_GF_TERCERO_ENTIDAD_AFILIACION.php');
        break;
    case "EntFinan":
        header('Location:listar_GF_TERCERO_ENTIDAD_FINANCIERA.php');
        break;
    case "EmpleadoN":
        header('Location:LISTAR_TERCERO_EMPLEADO_NATURAL2.php');
        break;
    case "ProveeJur":
        header('Location:LISTAR_TERCERO_PROVEEDOR_JURIDICA_2.php');
        break;
    case "ProveeNat" :
        header('Location:LISTAR_TERCERO_PROVEEDOR_NATURAL_2.php');
        break;
    case "AsociadoJ":
        header('Location:listar_GF_ASOCIADO_JURIDICA.php');
        break;
    case "AsociadoN":
        header('Location:listar_GF_ASOCIADO_NATURAL.php');
        break;
    case "BancoJ":
        header('Location:listar_GF_BANCO_JURIDICA.php');
        break;
    case "Compania":
        header('Location:TERCERO_COMPANIA.php');
        break;
    case "ClienteJ":
        header('Location:TERCERO_CLIENTE_JURIDICA.php');
        break;
    case "ContactoN":
        header('Location:TERCERO_CONTACTO_NATURAL.php');
        break;
    case "ClienteN":
        header('Location:TERCERO_CLIENTE_NATURAL.php');
        break;
        case "Todos":
        header('Location:TERCERO_TODOS.php');
        break;
    case "Perfil Tercero";
        header("Location:".$_SERVER['HTTP_REFERER']);
        break;
    case "Contribuyente":
        header('Location:listar_GF_TERCERO_CONTRIBUYENTE.php');
    break;
    }

?>