<?php 
##################################MODIFICACIONES########################################
#17/07/2017|ERICA G.|CAMBIAR EL METODO PARA QUE TENGA EN CUENTA LA PARAMETRIZACION ANNO
#########################################################################################
require_once '../Conexion/conexion.php';
session_start();
$numero = $_POST['numero'];
$tipo = $_POST['tipo'];
if ($numero == '""' || $numero != '""') {
     #############CALCULAR EL NUMERO##############
    $parametroAnno = $_SESSION['anno'];
    $sqlAnno = 'SELECT anno 
            FROM gf_parametrizacion_anno 
            WHERE id_unico = '.$parametroAnno;
    $paramAnno = $mysqli->query($sqlAnno);
    $rowPA = mysqli_fetch_row($paramAnno);
    $numero = $rowPA[0];

    $queryNumComp = 'SELECT MAX(numero) 
            FROM gf_comprobante_cnt
            WHERE tipocomprobante = '.$tipo .' AND parametrizacionanno = '.$parametroAnno. ' 
            AND numero LIKE \''.$numero.'%\'';
    $numComp = $mysqli->query($queryNumComp);
    $row = mysqli_fetch_row($numComp);
    if($row[0] == 0)
    {
        $numero .= '000001';
    }
    else
    {
        $numero = $row[0] + 1;
    }
    $numero = $numero;
    
    echo $numero;
}
?>