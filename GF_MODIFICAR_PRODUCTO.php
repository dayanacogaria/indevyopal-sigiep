<?php
require_once('head.php');
require_once('Conexion/conexion.php');
require_once('Conexion/ConexionPDO.php');
$con      = new ConexionPDO();
$anno     = $_SESSION['anno'];
$compania     = $_SESSION['compania'];
$txt      = "";
$gettexto = "";
$where    = " WHERE m.compania = $compania AND p.id_unico IS NOT NULL";
if (isset($_GET['texto']) && !empty($_GET['texto'])){
    $txt = $_GET['texto'];
    $gettexto = "&texto=".$txt;
    $where .= "  AND CONCAT_WS(' ',pi.codi, pi.nombre,p.descripcion ) LIKE '%$txt%'";
}
$rowf = $con->Listar("SELECT DISTINCT ef.id_unico, ef.nombre FROM gf_movimiento m 
LEFT JOIN gf_detalle_movimiento dm ON m.id_unico = dm.movimiento 
LEFT JOIN gf_movimiento_producto mp ON dm.id_unico = mp.detallemovimiento 
LEFT JOIN gf_producto p ON mp.producto = p.id_unico
LEFT JOIN gf_producto_especificacion pe ON p.id_unico = pe.producto 
LEFT JOIN gf_ficha_inventario fi ON pe.fichainventario = fi.id_unico
LEFT JOIN gf_elemento_ficha ef ON fi.elementoficha = ef.id_unico 
WHERE m.compania = $compania  and ef.id_unico IS NOT NULL 
ORDER BY ef.id_unico ");
?>
    <link rel="stylesheet" href="css/select2.css">
    <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
    <link rel="stylesheet" href="css/jquery-ui.css">
    <script src="js/jquery-ui.js"></script>
    <script type="text/javascript" src="js/select2.js"></script>
    <title>Modificar Productos</title>
    <style type="text/css" media="screen">
        table{
            background-color: #f6f6f6;
            border-radius: 2px;
            font-size: 10px;
        }

        td .select2-container.form-control{
            border-radius: 0px;
        }

        .cursor{
            cursor: default;
            font-size: 10px;
        }

        td .select2-container .select2-choice, .select2-container .select2-choices, .select2-container .select2-choices .select2-search-field input {
            border-radius: 0px;
        }

        td .select2-drop{
            border-radius: 0px;
        }

        .pagination{
            margin: 0px 0;
        }

        .cb{
            background: linear-gradient(to right, #69bcf4, #005e84);
            color: #fff;
            font-size: 13px;
        }

        table{
            box-shadow: 2px 2px 0px 0px gray;
        }
    </style>
</head>
<body>
    <div class="container-fluid text-center">
        <div class="row content">
            <div class="col-sm-12 col-md-12 col-lg-12">
                <a href="index2.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Inicio"></a>
                <h2 id="forma-titulo3" align="center" style="margin-top: 0px; margin-bottom: 5px; margin-right: 4px; margin-left: 4px; height: 40px; font-size: 25px;display:inline-block;width: 96%">Modificar Producto</h2>
                <div class="col-sm-12 col-md-12 col-lg-12">
                    <div class="form-group">
                        <input type="hidden" name="pgn" id="pgn" value="<?php echo $_REQUEST['pagina'];?>">
                        <div class="col-sm-4 col-md-4 col-lg-4" id="tBuscar" style="display:block">
                            <input type="txtBuscar" name="txtBuscar" id="txtBuscar" class="form-control col-sm-1" placeholder="Buscar Por Elemento, (Código, Nombre, Descripción)" value="<?php echo $txt; ?>" autocomplete="off" >
                        </div>
                        <div class="col-sm-6 col-md-6 col-lg-6">
                            <div class="col-sm-1 col-md-1 col-lg-1 text-left">
                                <a onclick="capturar_texto()" class="btn btn-primary" title="Buscar" id="btnBuscar"><i class="glyphicon glyphicon-search"></i></a>
                            </div>
                            <div class="col-sm-1 col-md-1 col-lg-1 text-left">
                                <a class="btn btn-primary" title="" href="GF_MODIFICAR_PRODUCTO.php"><span class="glyphicon glyphicon-plus"></span></a>
                            </div>
                            <div class="col-sm-1 col-md-1 col-lg-1 text-left">
                                <a class="btn btn-primary" title="" href="informes_almacen/INF_GF_MODIFICAR_PRODUCTO.php" target="_blank"><span class="fa fa-file-excel-o"></span></a>
                                <label name="mensaje" id="mensaje" style="color:#bd081c"></label>
                            </div>
                        </div>
                        
                    </div>
                </div>
                <div class="col-sm-10 col-md-10 col-lg-10 text-right">
                        <?php
                        $items          = 10;
                        $total          = "";
                        $empieza        = "";
                        
                        if(isset($_GET['pagina'])){
                            $pagina     = $_GET['pagina'];
                        }else{
                            $pagina     = 1;
                        }
                        $empieza = ($pagina - 1) * $items;
                        $t_t = "SELECT DISTINCT  p.id_unico 
                                FROM gf_movimiento m 
                                LEFT JOIN gf_detalle_movimiento dm ON m.id_unico = dm.movimiento 
                                LEFT JOIN gf_movimiento_producto mp ON dm.id_unico = mp.detallemovimiento 
                                LEFT JOIN gf_producto p ON mp.producto = p.id_unico
                                LEFT JOIN gf_plan_inventario pi ON dm.planmovimiento = pi.id_unico
                                $where 
                                ORDER BY p.id_unico";
                        $query = $t_t;
                        $res_t = $mysqli->query($query);
                        $filas = $res_t->num_rows;
                        
                        $total = ceil($filas/$items);
                        if($total > 5){
                            $pag = $pagina + 5;
                        }else{
                            $pag = $total;
                        }

                        $ret = $pagina - 1;
                        $avn = $pagina + 1;                        
                        echo "<ul class=\"pagination text-right\">";                        
                        if($pagina > 1){
                            echo "<li><a href=\"GF_MODIFICAR_PRODUCTO.php?pagina=1$gettexto\" title=\"Primero\"><span class=\"glyphicon glyphicon-chevron-left\"></span></a></li>";
                            echo "<li><a href=\"GF_MODIFICAR_PRODUCTO.php?pagina=$ret$gettexto\" title=\"Anterior\"><span class=\"glyphicon glyphicon-menu-left\"></span></a></li>";
                            
                        }
                        for($i = $pagina; $i <= $pag; $i++){
                            if($i <= $total){
                                if($i == $pagina){
                                    echo "<li class=\"active\"><a href=\"GF_MODIFICAR_PRODUCTO.php?pagina=$i$gettexto\">$i</a></li>";
                                }else{
                                    echo "<li><a href=\"GF_MODIFICAR_PRODUCTO.php?pagina=$i$gettexto\">$i</a></li>";
                                }
                            }
                        }
                        if($pagina < $total){
                            echo "<li><a href=\"GF_MODIFICAR_PRODUCTO.php?pagina=$avn$gettexto\" title=\"Siguiente\"><span class=\"glyphicon glyphicon-menu-right\"></span></a></li>";
                            echo "<li><a href=\"GF_MODIFICAR_PRODUCTO.php?pagina=$total$gettexto\" title=\"Ultimo\"><span class=\"glyphicon glyphicon-chevron-right\"></span></a></li>";
                            
                        }
                        echo "</ul>";
                            
                        ?>
                </div>
                <script>
                    function capturar_texto(){
                        let texto = $("#txtBuscar").val();
                        let pagina  = $("#pgn").val();
                        if (pagina > 0 && texto.length > 0){
                            window.location = 'GF_MODIFICAR_PRODUCTO.php?texto='+texto;
                        }else if (pagina < 1 && texto.length > 0){
                            window.location = 'GF_MODIFICAR_PRODUCTO.php?&texto='+texto;
                        }else {
                            window.location = 'GF_MODIFICAR_PRODUCTO.php';
                        }
                    }
                </script>
                <div class="col-sm-12 col-md-12 col-lg-12" style="margin-top:8px">
                    <div class="contTabla">
                        <?php
                        $html = '';
                        $html.= '<table id="tableE" name="conf" class="table table-hover table-striped text-left table-condensed display" cellspacing="0" width="100%">';
                        $html.= '<thead>';
                        $html.= '<tr>';
                        $html.= '<th class="cabeza cursor cb" title="Imágen">Imágen</th>';
                        $html.= '<th class="cabeza cursor cb" title="Nombre Activo">Nombre Activo</th>';
                        $html.= '<th class="cabeza cursor cb" title="Descripción">Descripción</th>';
                        $html.= '<th class="cabeza cursor cb" title="Valor">Valor</th>';
                        $html.= '<th class="cabeza cursor cb" title="Vida Útil">Vida Útil</th>';
                        $html.= '<th class="cabeza cursor cb" title="Fecha Adquisición">Fecha Adquisición</th>';
                        for ($i = 0; $i < count($rowf); $i++) {
                            $html.= '<th class="cabeza cursor cb" title="'.$rowf[$i][1].'">'.$rowf[$i][1].'</th>';
                        }
                        $html.= '</tr>';
                        $html.= '</thead>';
                        $html.= '<tbody>';
                        $row  = $con->Listar("SELECT DISTINCT  p.id_unico, 
                            CONCAT_WS(' ',pi.codi, pi.nombre),p.descripcion,p.valor, p.vida_util_remanente, DATE_FORMAT(p.fecha_adquisicion,'%d/%m/%Y') , 
                            pi.ficha 
                         FROM gf_movimiento m 
                         LEFT JOIN gf_detalle_movimiento dm ON m.id_unico = dm.movimiento 
                         LEFT JOIN gf_movimiento_producto mp ON dm.id_unico = mp.detallemovimiento 
                         LEFT JOIN gf_producto p ON mp.producto = p.id_unico
                         LEFT JOIN gf_plan_inventario pi ON dm.planmovimiento = pi.id_unico
                         $where 
                         ORDER BY p.id_unico
                         LIMIT $empieza, $items");
                        for ($r = 0; $r < count($row); $r++) {
                            $html.= '<tr>';
                            $html.='<td style="width:400px" class="cpt"><button type="button" id="" onclick="return imagenes('.$row[$r][0].');" title="Imágenes" onclick="" class="btn btn-primary" name=""><li class="glyphicon glyphicon-picture"></li></button></td>';
                            $html.='<td style="width:400px" class="cpt">'.$row[$r][1].'</td>';
                            $html.='<td style="width:400px" class="cpt"><textarea class="area" onchange="javaScript:modificarPD(this.value,'.$row[$r][0].','.$r.')">'.$row[$r][2].'</textarea></td>';
                            $html.='<td style="width:400px" class="cpt">'.number_format($row[$r][3],2,',','.').'</td>';
                            $html.='<td style="width:400px" class="cpt"><textarea class="area" onchange="javaScript:modificarP(this.value,'.$row[$r][0].','.$r.')">'.$row[$r][4].'</textarea></td>';
                            $html.='<td style="width:400px" class="cpt">'.$row[$r][5].'</td>';
                            for ($i = 0; $i < count($rowf); $i++) {
                                $dp = $con->Listar("SELECT DISTINCT  pe.id_unico, pe.valor,  fi.autogenerado, fi.id_unico
                                    FROM gf_producto_especificacion pe
                                    LEFT JOIN gf_ficha_inventario fi ON pe.fichainventario = fi.id_unico 
                                    WHERE pe.producto = ".$row[$r][0]." 
                                    AND fi.elementoficha=  ".$rowf[$i][0]);
                                if(empty($dp[0][0])){
                                    if($dp[0][2]==1){
                                        $html.= '<td style="width:400px" class="cpt"></td>';
                                    } else { 
                                        //Buscar Ficha Inventario
                                        $fi = $con->Listar("SELECT * FROM `gf_ficha_inventario` WHERE elementoficha = ".$rowf[$i][0]." AND ficha = ".$row[$r][6]);
                                        if(!empty($fi[0][0])){
                                            $html.= '<td style="width:400px" class="cpt"><textarea class="area" onchange="javaScript:guardarEspecificacion(this.value,'.$row[$r][0].','.$r.','.$fi[0][0].')"></textarea></td>';
                                        } else {
                                            $html.= '<td style="width:400px" class="cpt"></td>';
                                        }
                                    }
                                } else { 
                                    if($dp[0][2]==1){
                                        $html.= '<td style="width:400px" class="cpt">'.$dp[0][1].'</td>';
                                    } else { 
                                        $html.= '<td style="width:400px" class="cpt"><textarea class="area" onchange="javaScript:modificarEspecificacion(this.value,'.$dp[0][0].','.$r.')">'.$dp[0][1].'</textarea></td>';
                                    }
                                }
                            }
                            $html.= '</tr>';
                        }
                        
                        $html.= '</tbody>';            //Fin del cuerpo de la tabla
                        $html.= '</table>';
                        
                        echo $html;
                    
                    ?>
                </div>
                </div>
            </div>                
        </div>
        <?php require_once('footer.php'); 
        require_once './modalProductoImagen.php';?>
        <link rel="stylesheet" href="css/bootstrap-theme.min.css">
        <script src="js/bootstrap.min.js"></script>
        <script src="js/md5.js"></script>
    <script>
        function modificarP(valor,id, nmsj){
            $("#mensaje").html('');
            var form_data ={action:1,producto:id,  valor:valor}
            $.ajax({
                type: "POST",
                url: "jsonPptal/gf_almacenJson.php",
                data: form_data,
                success: function(response)
                {
                    console.log(response);
                    if(response==1){
                        $("#mensaje").html('Guardado');
                    } else {
                        $("#mensaje").html('Error');
                    }
                }
            })
        }
        function modificarPD(valor,id, nmsj){
            $("#mensaje").html('');
            var form_data ={action:4,producto:id,  valor:valor}
            $.ajax({
                type: "POST",
                url: "jsonPptal/gf_almacenJson.php",
                data: form_data,
                success: function(response)
                {
                    console.log(response);
                    if(response==1){
                        $("#mensaje").html('Guardado');
                    } else {
                        $("#mensaje").html('Error');
                    }
                }
            })
        }
        function modificarEspecificacion(valor,id, nmsj){
            $("#mensaje").html('');
            var form_data ={action:2,producto:id,  valor:valor}
            $.ajax({
                type: "POST",
                url: "jsonPptal/gf_almacenJson.php",
                data: form_data,
                success: function(response)
                {
                    console.log(response);
                    if(response==1){
                        $("#mensaje").html('Guardado');
                    } else {
                        $("#mensaje").html('Error');
                    }
                }
            })
        }
        function guardarEspecificacion(valor,producto, nmsj, ficha){
            $("#mensaje").html('');
            var form_data ={action:3,producto:producto,  valor:valor, ficha:ficha}
            $.ajax({
                type: "POST",
                url: "jsonPptal/gf_almacenJson.php",
                data: form_data,
                success: function(response)
                {
                    console.log(response);
                    if(response==1){
                        $("#mensaje").html('Guardado');
                    } else {
                        $("#mensaje").html('Error'); 
                    }
                }
            })
        }
        function imagenes(producto){
            $("#mdlMensaje").modal('hide');
            $("#modalImagen").modal('hide');
            $('.modalIm').modal({show:false});
            $("#modalFichaInventario").modal('hide');
            $('.modalI').modal({show:false});
            var form_data = {
                producto:producto
            };

            $.ajax({
                type: 'POST',
                url: "modalProductoImagen.php#modalImagen",
                data: form_data,
                success: function (data, textStatus, jqXHR) {
                    $("#modalImagen").html(data);
                    $('.modalIm').modal({backdrop: 'static', keyboard: false,show:true});
                }
            });
        }
    </script>
    
<div class="modal fade" id="mdlMensaje" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24px; padding: 3px;">Información</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <label id="txtmsjmdl" name="txtmsjmdl" ></label>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="btnMsj" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
            </div>
        </div>
    </div>
</div>
</html> 
<script>
    function guardarImagen(){
        producto = $("#txtProducto").val();
        var formData = new FormData($("#formImagen")[0]);  
        $.ajax({
            type: 'POST',
            url: "jsonPptal/gf_almacenJson.php?action=5",
            data:formData,
            contentType: false,
             processData: false,
            success: function (data) {  
                console.log(data);
                $("#modalImagen").modal('hide');
                $('.modalIm').modal({show:false});
                if(data==1){ 
                    $("#txtmsjmdl").html("Imágen Guardada Correctamente");
                    $("#mdlMensaje").modal("show");
                } else {
                    $("#txtmsjmdl").html("No Se Ha podido Cargar la Imágen");
                    $("#mdlMensaje").modal("show");
                }
                $("#btnMsj").click(function(){
                    $("#mdlMensaje").modal("hide");
                    imagenes(producto);    
                })               
            }
        })
    }
</script>
<script>
    function eliminarImagen(id, producto){
        var form_data = { action:6, id:id };
        $.ajax({
          type: "POST",
          url: "jsonPptal/gf_almacenJson.php",
          data: form_data,
            success: function (data) {  
                $("#modalImagen").modal('hide');
                $('.modalIm').modal({show:false});
                if(data==1){ 
                    $("#txtmsjmdl").html("Imágen Eliminada Correctamente");
                    $("#mdlMensaje").modal("show");
                } else {
                    $("#txtmsjmdl").html("No Se Ha podido Eliminar la Imágen");
                    $("#mdlMensaje").modal("show");
                }
                $("#btnMsj").click(function(){
                    $("#mdlMensaje").modal("hide");
                    imagenes(producto);    
                })               
            }
        })
    }
    $("#btnCerrarModalMov").click(function(){
        $("#modalImagen").modal('hide');
        $('.modalIm').modal({show:false});
    })
</script>
</body>
</html>
