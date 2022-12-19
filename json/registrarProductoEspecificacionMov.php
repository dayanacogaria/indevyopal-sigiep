<?php
session_start();
require_once '../Conexion/conexion.php';
require_once '../Conexion/ConexionPDO.php';
$valor = '"'.$mysqli->real_escape_string(''.$_POST['valor'].'').'"';
$descripcion = '"'.$mysqli->real_escape_string(''.$_POST['txtDescripcion'].'').'"';
$detalleMovimiento = $_POST['movimiento'];
$con = new ConexionPDO();
//Buscar vida útil
$panno = $_SESSION['anno'];
#* Datos Movimiento 
$dm = $con->Listar("SELECT DISTINCT m.fecha, dm.planmovimiento 
        FROM gf_detalle_movimiento dm 
        LEFT JOIN gf_movimiento m ON m.id_unico = dm.movimiento 
        WHERE dm.id_unico = $detalleMovimiento");

#* Buscar los parámetros de anno segun el valor
$dp = $con->Listar("SELECT IF($valor <= minimacuantia,0, 
            if($valor >= menorcuantia and $valor <= menorcuantia_m, 12, 
            if($valor>=mayorcuantia,(SELECT ta.valor FROM gf_plan_inventario pi 
                         LEFT JOIN gf_tipo_activo ta ON pi.tipoactivo = ta.id_unico 
                        WHERE pi.id_unico = ".$dm[0][1]."),0))) as tpo 
        FROM gf_parametrizacion_anno where id_unico = ".$panno);
if(!empty($dp[0][0])){
    $vu = $dp[0][0];
} else {
    $vu = 0;
}
echo $insertProducto = "insert into gf_producto(valor,descripcion, vida_util_remanente,fecha_adquisicion) "
        . "values ($valor,$descripcion, $vu,".$dm[0][0]." )";
$resulltProducto = $mysqli->query($insertProducto);

$sqlProducto = "select MAX(id_unico) from gf_producto where valor=$valor and descripcion=$descripcion";
$resultProducto = $mysqli->query($sqlProducto);
$filaProducto = mysqli_fetch_row($resultProducto);
$producto = $filaProducto[0];
$insertMovimientoProducto = "insert into gf_movimiento_producto(producto,detallemovimiento) values ($producto,$detalleMovimiento)";
$resultMovimientoProducto = $mysqli->query($insertMovimientoProducto);
$ficha = $_POST['ficha'];
$sqlFicha="select distinct                                                              
                elm.id_unico,
                elm.nombre,
                fin.id_unico,
                elm.tipodato
        from gf_ficha_inventario fin 
        left join gf_elemento_ficha elm on elm.id_unico = fin.elementoficha 
        left join gf_tipo_dato tpd on elm.tipodato = tpd.id_unico
        WHERE fin.ficha = $ficha ORDER BY elm.id_unico ASC";
$resultFicha = $mysqli->query($sqlFicha);
$contar = 0;
while($campo = mysqli_fetch_row($resultFicha)){
    $contar++;
    #Reemplazamos los campos vacios en el nombre del elemento
    $fila = str_replace(' ', '', $campo[1]);
    #Validación de tipo de dato
    switch ($campo[3]){
        case 1:
            $valor = '"'.$mysqli->real_escape_string(''.$_POST["$fila"].'').'"';
            break;
        case 2:
            $valor = '"'.$mysqli->real_escape_string(''.$_POST["$fila"].'').'"';
            break;
        case 3:
            $valor = '"'.$mysqli->real_escape_string(''.$_POST["$fila"].'').'"';
            break;
        case 4:
            $valor = '"'.$mysqli->real_escape_string(''.$_POST["$fila"].'').'"';
            break;
        case 5:
            $valor = '"'.$mysqli->real_escape_string(''.$_POST["$fila"].'').'"';
            break;
        case 6:
            $fechaT = ''.$mysqli->real_escape_string(''.$_POST["$fila"].'').'';
            $valorF = explode("/",$fechaT);
            $valor =  '"'.$valorF[2].'-'.$valorF[1].'-'.$valorF[0].'"';             
            break;
    }    
    #Tomamos el valor de ficha inventario
    $fichaInventario = $campo[2];
    #realizamos el insertado a la tabla producto especificación
    $insertProductoE="insert into gf_producto_especificacion(fichainventario,producto,valor) values ($fichaInventario,$producto,$valor)";
    $resultProductoEspecificación = $mysqli->query($insertProductoE);
}
?>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <script src="../js/md5.pack.js"></script>
    <script src="../js/jquery.min.js"></script>
    <link rel="stylesheet" href="../css/jquery-ui.css" type="text/css" media="screen" title="default" />
    <script type="text/javascript" language="javascript" src="../js/jquery-1.10.2.js"></script>
</head>
<body>
</body>
</html>
<!--Modal para informar al usuario que se ha registrado-->
<div class="modal fade" id="myModal1" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <p>Información guardada correctamente.</p>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="ver1" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
            </div>
        </div>
    </div>
</div>
<!--Modal para informar al usuario que no se ha poodido registrar la informacion-->
<div class="modal fade" id="myModal2" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <p>No se ha podido guardar la información.</p>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="ver2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
            </div>
        </div>
    </div>
</div>
<!--links para el estilo de la pagina-->
<script type="text/javascript" src="../js/menu.js"></script>
<link rel="stylesheet" href="../css/bootstrap-theme.min.css">
<script src="../js/bootstrap.min.js"></script>
<!--Abre la pagina de listar para mostrar la información guardada-->
<?php if($resultProductoEspecificación == true){ ?>
    <script type="text/javascript">
        $("#myModal1").modal('show');
        $("#ver1").click(function(){
            $("#myModal1").modal('hide');
            window.location=window.history.go(-1);
        });
    </script>
<?php }else{ ?>
    <script type="text/javascript">
        $("#myModal2").modal('show');
        $("#ver2").click(function(){
            $("#myModal1").modal('hide');
            window.location=window.history.go(-1);
        });
    </script>
<?php } ?>