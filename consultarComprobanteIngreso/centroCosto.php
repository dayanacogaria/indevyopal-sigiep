<?php
session_start();
require_once '../Conexion/conexion.php';
$anno = $_SESSION['anno'];
$cuenta = $_POST['cuenta'];



$sql = "SELECT id_unico,nombre FROM gf_centro_costo WHERE parametrizacionanno = $anno order by nombre='Varios' desc";
$res = $mysqli->query($sql);
while($row = mysqli_fetch_row($res)){
    echo '<option value="'.$row[0].'">'.ucwords(strtolower($row[1])).'</option>';
}
?>