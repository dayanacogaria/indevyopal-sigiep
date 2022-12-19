<?php
echo "\n<style type=\"text/css\">";
echo "\n\tli {list-style: none;}";
echo "\t\t.lnk{color: black;}";
echo "\n</style>";
echo "\n<ul class=\"text-left\" style=\"margin-left: -40px;margin-top: 5px\">";
//Función para determinar los padres de primer nivel
function padres($plan,$posicion,$mov){
    $dtll = get_values_detail_mov_pl($mov,$plan);           #Obtenemos los valores del detalle
    $plans = get_values_pl($plan);                          #consulta para obtener los datos del plan inventario
    $n = get_n_products ($plans[2], $dtll[2]);              #Consulta para determinar la cantidad de elmentos que tiene registrado
    if($n > 0){
        if(count($plans) > 0){                              #Validamos que el array no este nulo
            #imprimimos la lista
            echo "\n\t<li><a class=\"lnk arbol\" href=\"#".$posicion."\" data-toggle=\"modal\" onclick=\"return abrirFichaIn('".$dtll[0]."','".$plans[2]."','".$dtll[1]."','".$dtll[2]."','".$posicion."','".$plans[3]."');\">".trim($plans[1]).PHP_EOL.$posicion."</a>";
            echo "\n\t\t<ul class=\"text-left\" style=\"margin-left: -20px\">";
            hijos($plan,$posicion,$mov);
            echo "\n\t\t</ul>";
            echo "\n\t</li>";
        }
    }else{
        echo "\n\t<li><a class=\"lnk arbol\" href=\"#".$posicion."\" data-toggle=\"modal\" onclick=\"return abrirFichaI('".$dtll[0]."','".$plans[2]."','".$dtll[1]."','".$dtll[2]."','".$mov."')\">".trim($plans[1])."</a>";
        echo "\n\t\t<ul class=\"text-left\" style=\"margin-left: -20px\">";
        hijos($plan,$posicion,$mov);
        echo "\n\t\t</ul>";
        echo "\n\t</li>";
    }
}
//Función para determinar los hijos que tiene el plan inventario padre
function hijos($padre,$posicion,$mov){
    require '../Conexion/conexion.php';
    $sql="SELECT DISTINCT plan_hijo FROM gf_plan_inventario_asociado WHERE plan_padre = $padre";    #consulta de obtención de hijos por padre
    $result = $mysqli->query($sql);
    while($row = mysqli_fetch_row($result)){
        $c = mysqli_num_rows($result);
        if($c > 0){
            $query = "SELECT DISTINCT id_unico, CONCAT(codi, ' - ', nombre), nombre, ficha FROM gf_plan_inventario WHERE id_unico = $row[0]";
            $resultD = $mysqli->query($query);
            $field = mysqli_fetch_row($resultD);
            $dtll = get_values_detail_mov_pl($mov, $row[0]);    #Consulta para determinar los datos del detalle
            $rowsD = count($dtll);
            if($rowsD > 0) {
                $n = get_n_products($field[3], $dtll[2]);       #Consulta para determinar la cantidad de elmentos que tiene registrado
                if($n > 0){
                    echo "\n\t<li><a href=\"#".$posicion."\" data-toggle=\"modal\" class=\"lnk arbol\" onclick=\"return abrirFichaIn('".$dtll[0]."','".$field[3]."','".$dtll[1]."','".$dtll[2]."','".$posicion."','".$field[2]."');\">".trim($field[1]).PHP_EOL.$posicion."</a>";
                    echo "\n\t\t<ul class=\"text-left\" style=\"margin-left:-20px\">";
                    hijos($row[0],$posicion,$mov);
                    echo "\n\t\t</ul>";
                    echo "\n\t</li>";
                }else{
                    echo "\n\t<li><a href=\"#".$posicion."\" data-toggle=\"modal\" class=\"lnk arbol\" onclick=\"return abrirFichaI('".$dtll[0]."','".$field[3]."','".$dtll[1]."','".$dtll[2]."','".$posicion."','".$mov."')\">".trim($field[1])."</a>";
                    echo "\n\t\t<ul class=\"text-left\" style=\"margin-left:-20px\">";
                    hijos($row[0],$posicion,$mov);
                    echo "\n\t\t</ul>";
                    echo "\n\t</li>";
                }
            }
        }
    }
}

function get_values_detail_mov_pl ($mov, $plan) {
    require '../Conexion/conexion.php';
    $data = array();
    $sqlD = "SELECT det.cantidad, det.valor, det.id_unico FROM gf_detalle_movimiento det WHERE det.movimiento = $mov AND det.planmovimiento = $plan";
    $resultD = $mysqli->query($sqlD);
    $rows = mysqli_num_rows($resultD);
    if($rows > 0) {
        $row = mysqli_fetch_row($resultD);
        $data = array("0" => $row[0], "1" => $row[1], "2" => $row[2]);
    }
    return $data;
}

function get_values_pl ($plan) {
    require '../Conexion/conexion.php';
    $data = array();
    $sqlPlan = "SELECT DISTINCT id_unico, CONCAT(codi, ' - ', nombre), ficha, nombre FROM gf_plan_inventario WHERE id_unico = $plan";
    $resultPlan = $mysqli->query($sqlPlan);
    $rows = mysqli_num_rows($resultPlan);
    if ($rows > 0) {
        $row = mysqli_fetch_row($resultPlan);
        $data = array("0" => $row[0], "1" => $row[1], "2" => $row[2], "3" => $row[3]);
    }
    return $data;
}

function get_n_products ($ficha, $id_detalle) {
    require '../Conexion/conexion.php';
    $sqlProducto = "SELECT DISTINCT COUNT(prdes.fichainventario) AS cantidad FROM gf_producto_especificacion prdes
                LEFT JOIN gf_producto pr                ON pr.id_unico = prdes.producto
                LEFT JOIN gf_ficha_inventario fin       ON prdes.fichainventario  = fin.id_unico
                LEFT JOIN gf_ficha fch                  ON fin.ficha = fch.id_unico
                LEFT JOIN gf_movimiento_producto movp   ON movp.producto = pr.id_unico
                WHERE     fch.id_unico = $ficha     AND movp.detallemovimiento = $id_detalle
                GROUP BY  prdes.fichainventario";
    $resultProducto = $mysqli->query($sqlProducto);
    $x = mysqli_num_rows($resultProducto);
    return $x;
}

if(!empty($detalle)){
    $sqlMov = "SELECT movimiento FROM gf_detalle_movimiento WHERE id_unico = $detalle";
} else {
    $sqlMov = "SELECT movimiento FROM gf_detalle_movimiento WHERE id_unico = $movimiento";
}

$resultMov = $mysqli->query($sqlMov);
$movimiento = mysqli_fetch_row($resultMov);
$sqlPlans = "SELECT dtm.planmovimiento FROM gf_movimiento mov LEFT JOIN gf_detalle_movimiento dtm ON dtm.movimiento = mov.id_unico WHERE mov.id_unico = $movimiento[0]";
$resultPlns = $mysqli->query($sqlPlans);
while($datos= mysqli_fetch_row($resultPlns)){
    //Consulta para determinar el primer nivel
    $query ="SELECT DISTINCT plan_hijo FROM gf_plan_inventario_asociado WHERE plan_padre = $datos[0]";
    $resultQ = $mysqli->query($query);
    $pd = mysqli_num_rows($resultQ);
    if($pd > 0) {
        padres($datos[0],$posicion,$movimiento[0]);
    }
}
echo '</ul>';
?>