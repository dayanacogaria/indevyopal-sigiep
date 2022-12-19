<?php
#####################################################################################
# ********************************* Modificaciones *********************************#
#####################################################################################
#03/05/2018 | Erica G. | Eliminar Seleccion
#17/04/2018 | Erica G. | Archivo Creado
####/################################################################################
require_once('head.php');
require_once('Conexion/conexion.php');
require_once('Conexion/ConexionPDO.php');
$con    = new ConexionPDO();
$anno   = $_SESSION['anno'];
$compania   = $_SESSION['compania'];
$ctas = "";
$codigo = '';
if(!empty($_REQUEST['formato'])) {
    $df = $con->Listar("SELECT formato FROM gf_formatos_exogenas WHERE id_unico =".$_REQUEST['formato'] );
    $codigo = $df[0][0];
    if($codigo=='2276'){
        $ctas  = $con->Listar("SELECT id_unico, codigo, descripcion 
            FROM gn_concepto WHERE compania = $compania
            ORDER BY codigo ASC");

    } else {
        $ctas   = $con->Listar("SELECT id_unico, codi_cuenta, nombre FROM gf_cuenta 
              WHERE parametrizacionanno = $anno 
              AND (movimiento=1 OR auxiliartercero =1 OR auxiliarproyecto = 1 OR centrocosto = 1 ) 
              ORDER BY codi_cuenta ASC");
    }
}

?>
    <link rel="stylesheet" href="css/select2.css">
    <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
    <link rel="stylesheet" href="css/jquery-ui.css">
    <script src="js/jquery-ui.js"></script>
    <script type="text/javascript" src="js/select2.js"></script>
    <title>Configuración Exógenas</title>
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
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-10 col-md-10 col-lg-10">
                <h2 id="forma-titulo3" align="center" style="margin-top: 0px; margin-bottom: 5px; margin-right: 4px; margin-left: 4px; height: 40px; font-size: 25px;display:inline-block;width: 96%">Configuración Exógenas</h2>
                <?php if(!empty($_REQUEST['formato'])) { ?>
                <!--------  Buscar ------------->
                <div class="col-sm-12 col-md-12 col-lg-12">
                    <div class="form-group">
                        <input type="hidden" name="format" id="format" value="<?php echo $_REQUEST['formato'];?>">
                        <label for="Buscar" class="control-label col-sm-1 col-md-1 col-lg-1 text-left">Buscar:</label>
                        <div class="col-sm-6 col-md-6 col-lg-6">
                            <div class="col-sm-4 col-md-4 col-lg-4">
                                <select name="filtrar" class="select2 form-control col-sm-1 text-left" id="filtrar" >
                                    <option value="">Buscar Por </option>
                                    <?php  if($codigo=='2276'){  
                                        echo '<option value="2">Concepto Empieza Por </option>';
                                    }else {
                                        echo '<option value="1">Clase Cuenta</option>
                                        <option value="2">Cuenta Empieza Por </option>';
                                    }?>
                                    
                                </select>
                            </div>
                            <script>
                                $("#filtrar").change(function(){
                                    var filtro = $("#filtrar").val();
                                    if(filtro != ""){
                                        if(filtro==1){
                                            $("#tBuscar").css('display','none');
                                            $("#tBuscarC").css('display','block');
                                        } else {
                                            $("#tBuscar").css('display','block');
                                            $("#tBuscarC").css('display','none');
                                        }
                                    } 
                                })
                            </script>
                            <div class="col-sm-4 col-md-4 col-lg-4" id="tBuscar" style="display:block">
                                <input type="txtBuscar" name="txtBuscar" id="txtBuscar" class="form-control col-sm-1" placeholder="Buscar">
                            </div>
                            <div class="col-sm-4 col-md-4 col-lg-4" id="tBuscarC" style="display:none">
                                <select name="txtBuscarc" class="select2 form-control col-sm-1 text-left" id="txtBuscarc" >
                                    <option value="">Clase</option>
                                    <?php $cl = $con->Listar("SELECT * FROM gf_clase_cuenta Order by id_unico");
                                        for ($i = 0; $i < count($cl); $i++) {
                                            echo '<option value ="'.$cl[$i][0].'">'.$cl[$i][1].'</option>';
                                        }
                                    ?>
                                </select>
                            </div>
                            <div class="col-sm-1 col-md-1 col-lg-1 text-left">
                                <a onclick="capturar_texto()" class="btn btn-primary" title="Buscar" id="btnBuscar"><i class="glyphicon glyphicon-search"></i></a>
                            </div>
                            <div class="col-sm-1 col-md-1 col-lg-1 text-left">
                                <a class="btn btn-primary" title="" href="GF_CONFIGURACION_EXOGENAS.php"><span class="glyphicon glyphicon-plus"></span></a>
                            </div>
                            <div class="col-sm-1 col-md-1 col-lg-1 text-left">
                                <a class="btn btn-primary" title="" href="informes/INF_CONFIGURACION_EXOGENAS.php?formato=<?php echo $_REQUEST['formato'];?>" target="_blank"><span class="fa fa-file-excel-o"></span></a>
                            </div>
                        </div>
                        
                    </div>
                </div>
                <div class="col-sm-10 col-md-10 col-lg-10 text-right">
                        <?php
                        $format         = $_REQUEST['formato'];
                        $items          = 10;
                        $total          = "";
                        $empieza        = "";
                        $valorFiltro    = "";
                        $filtro         = "";
                        $busqueda       = "";
                        if(!empty($_GET['texto']) && !empty($_GET['filtro'])){
                            $valorFiltro = $_GET['texto'];
                            $filtro      = $_GET['filtro'];
                            if($filtro==1){
                                $busqueda       = "AND clasecuenta = $valorFiltro";
                            } else {
                                if($codigo=='2276'){  
                                     $busqueda       = "AND CONCAT_WS(' ', codigo, descripcion) like '$valorFiltro%'";
                                } else {
                                    $busqueda       = "AND CONCAT_WS(' ', codi_cuenta, nombre) like '$valorFiltro%'";
                                }
                            }
                        } elseif(!empty($_GET['texto'])){
                            $valorFiltro = $_GET['texto'];
                            if($codigo=='2276'){  
                                $busqueda       = "AND CONCAT_WS(' ', codigo, descripcion) like '%$valorFiltro%'";
                                
                            } else {
                                $busqueda       = "AND CONCAT_WS(' ', codi_cuenta, nombre) like '%$valorFiltro%'";
                            }
                        }
                        if(isset($_GET['pagina'])){
                            $pagina     = $_GET['pagina'];
                        }else{
                            $pagina     = 1;
                        }

                        $empieza = ($pagina - 1) * $items;
                        if($codigo=='2276'){  
                            $t_t = "SELECT id_unico, codigo, descripcion 
                                FROM gn_concepto WHERE compania = $compania 
                                $busqueda 
                                ORDER BY codigo ASC";
                        } else {
                            $t_t = "SELECT id_unico, codi_cuenta, nombre FROM gf_cuenta 
                                WHERE parametrizacionanno = $anno 
                                AND (movimiento=1 OR auxiliartercero =1 OR auxiliarproyecto = 1 OR centrocosto = 1) 
                                $busqueda 
                                ORDER BY codi_cuenta ASC";
                        }
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
                            echo "<li><a href=\"GF_CONFIGURACION_EXOGENAS.php?formato=$format&pagina=1\" title=\"Primero\"><span class=\"glyphicon glyphicon-chevron-left\"></span></a></li>";
                            echo "<li><a href=\"GF_CONFIGURACION_EXOGENAS.php?formato=$format&pagina=$ret\" title=\"Anterior\"><span class=\"glyphicon glyphicon-menu-left\"></span></a></li>";
                            
                        }
                        for($i = $pagina; $i <= $pag; $i++){
                            if($i <= $total){
                                if($i == $pagina){
                                    echo "<li class=\"active\"><a href=\"GF_CONFIGURACION_EXOGENAS.php?formato=$format&filtro=$filtro&texto=$valorFiltro&pagina=$i\">$i</a></li>";
                                }else{
                                    echo "<li><a href=\"GF_CONFIGURACION_EXOGENAS.php?formato=$format&filtro=$filtro&texto=$valorFiltro&pagina=$i\">$i</a></li>";
                                }
                            }
                        }
                        if($pagina < $total){
                            echo "<li><a href=\"GF_CONFIGURACION_EXOGENAS.php?formato=$format&filtro=$filtro&texto=$valorFiltro&pagina=$avn\" title=\"Siguiente\"><span class=\"glyphicon glyphicon-menu-right\"></span></a></li>";
                            echo "<li><a href=\"GF_CONFIGURACION_EXOGENAS.php?formato=$format&filtro=$filtro&texto=$valorFiltro&pagina=$total\" title=\"Ultimo\"><span class=\"glyphicon glyphicon-chevron-right\"></span></a></li>";
                            
                        }
                        echo "</ul>";
                            
                        ?>
                </div>
                <div class="col-sm-12 col-md-12 col-lg-12" style="margin-top:8px">
                    <div class="contTabla">
                        <?php
                        $html = '';
                        $html.= '<table id="tableE" name="confExogenas" class="table table-hover table-striped text-left table-condensed display" cellspacing="0" width="100%">';
                        $html.= '<thead>';
                        $html.= '<tr>';
                        $html.= '<th class="cabeza cursor cb" title="Cuenta">Cuenta</th>';
                        #   ****    Buscar Formatos    *****   #
                        $fm = $con->Listar("SELECT * FROM gf_formatos_exogenas WHERE id_unico = $format");
                        $html.= '<th class="cabeza cursor cb" title="Cuenta">'.$fm[0][1].' - '.$fm[0][2].'</th>';
                        $html.= '</tr>';
                        $html.= '</thead>';
                        $html.= '<tbody>';
                        if($codigo=='2276'){  
                            $sql  = $con->Listar("SELECT id_unico, codigo, LOWER(descripcion) 
                                FROM gn_concepto 
                                WHERE compania = $compania  
                                $busqueda 
                                ORDER BY codigo ASC  
                                LIMIT $empieza, $items");
                            
                        } else {
                            $sql  = $con->Listar("SELECT id_unico, codi_cuenta, LOWER(nombre) FROM gf_cuenta 
                                WHERE parametrizacionanno = $anno 
                                AND (movimiento=1 OR auxiliartercero =1 OR auxiliarproyecto = 1 OR centrocosto = 1) 
                                $busqueda 
                                ORDER BY codi_cuenta ASC  
                                LIMIT $empieza, $items");
                        }
                        for ($s = 0; $s < count($sql); $s++) {
                            $cuenta = $sql[$s][0];
                            $html.= '<tr>';
//                            $html.= "<td style='width:150px'>";
//                                    $html.= "<select class=\"select2 form-control col-sm-1\"  style=\"width:150px;align:center\">";     //Campo select generado de manera dinamica
//                                    $html.= "<option value=''></option>"; //opción con el nombre del campo
//                                    $html.= "<option value=".$empieza." selected>".ucwords(mb_strtolower($items))."</option>";//Option con el valor optenido cuando exsite en la base de datos
//                                    $html.= "<option value=".$empieza.">".ucwords(mb_strtolower($items))."</option>"; //Opción impresa
//                                    
//                                    $html.= "</select>\n";   //Fin del select
//                                    $html.= "</td>\n"; 
                                    
                                    
                            $html.='<td style="width:400px" class="cpt"><span name="cuenta'.$sql[$s][0].'" id="cuenta'.$sql[$s][0].'">'.$sql[$s][1].' - '.ucwords(($sql[$s][2])).'</span></td>';
                            $fm = $con->Listar("SELECT * FROM gf_formatos_exogenas WHERE id_unico =$format ORDER BY id_unico ASC");
                            $formato = $fm[0][0];
                            $html.= '<td style="width:400px">';
                            # ** Buscar Si Existe Configuración ** #
                            $conf    = $con->Listar("SELECT cf.id_unico, cn.codigo, cn.nombre, 
                                        cf.concepto_exogenas, cn.id_unico 
                                        FROM gf_configuracion_exogenas cf 
                                        LEFT JOIN gf_concepto_exogenas cn ON cf.concepto_exogenas = cn.id_unico 
                                        WHERE cf.cuenta = $cuenta AND cn.formato= $formato");
                            $cg     = $con->Listar("SELECT id_unico, codigo, nombre 
                                        FROM gf_concepto_exogenas WHERE formato =$formato"); 
                            $html.= '<select class="select2 form-control col-sm-1"  style="width:150px;align:center" name="conf'.$cuenta.''.$formato.'" id="conf'.$cuenta.''.$formato.'"  onchange="guardar('.$cuenta.','.$formato.',this.value)">'; 
                            if(count($conf)>0){                                
                                $html   .= '<option value=""></option>'; //opción con el nombre del campo
                                $html.= '<option value="'.$conf[0][4].'" selected>'.$conf[0][1].' - '.$conf[0][2].'</option>';
                                for ($c = 0; $c < count($cg); $c++) {
                                    $html.= '<option value="'.$cg[$c][0].'">'.$cg[$c][1].' - '.$cg[$c][2].'</option>';
                                }
                            } else {
                                $html   .= '<option value=""></option>';
                                for ($c = 0; $c < count($cg); $c++) {
                                    $html   .= '<option value="'.$cg[$c][0].'">'.$cg[$c][1].' - '.$cg[$c][2].'</option>';
                                }
                            }
                            $html   .= '</select>';
                            $html.= '</td>';
                            
                            $html.= '</tr>';
                        }
                        
                        $html.= '</tbody>';            //Fin del cuerpo de la tabla
                        $html.= '</table>';
                        
                        echo $html;
                    
                    ?>
                </div>
                
                <script>
                    $(".select2").select2({
                        allowClear:true
                    });
                    function capturar_texto(){
                        var format = $("#format").val();
                        var filtro = $("#filtrar").val();
                        if(filtro !=""){
                            if(filtro==1){
                                var texto = $("#txtBuscarc").val();
                                window.location = 'GF_CONFIGURACION_EXOGENAS.php?formato='+format+'&filtro=1&texto='+texto;
                            } else {
                                var texto = $("#txtBuscar").val();
                                window.location = 'GF_CONFIGURACION_EXOGENAS.php?formato='+format+'&filtro=2&texto='+texto;
                            }
                        } else {
                           var texto = $("#txtBuscar").val(); 
                           window.location = 'GF_CONFIGURACION_EXOGENAS.php?formato='+format+'&texto='+texto;
                        }
                        
                    }
                    function guardar(cuenta,formato, valor){
                        var valor  = valor;
                        var cta = $.trim(cuenta);
                        var formt = $.trim(formato);
                        var form_data = {action:9, cuenta:cuenta,formato:formato, valor:valor};  
                        $.ajax({
                            type: 'POST',
                            url: "jsonPptal/gf_exogenasJson.php",
                            data: form_data, 
                            success: function(response)
                            {
                                console.log(cta,formt);
                                console.log('Guardar'+response);
                                if(response!=0){
                                    $("#conf12503").val(response);
                                }
                            }
                        });
                    }
                </script>
                </div>
                <?php } else { ?>
                <script>
                    $(document).ready(function() {
                        $("#modalconfiguracion").modal("show");
                    });
                    $(".select2").select2({
                        allowClear:true
                    });
                </script>
                <div class="modal fade" id="modalconfiguracion" role="dialog" align="center" >
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div id="forma-modal" class="modal-header">
                                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                            </div>
                            <div class="modal-body" style="margin-top: 8px">
                                <div class="form-group" style="margin-top: -5px;">                                    
                                   <label class="control-label" style="display:inline-block; width:140px; margin-top:5px"><strong class="obligado">*</strong>Formato: </label>
                                    <select name="formatoe" id="formatoe" class="select2 form-control "  style="display:inline-block; width:250px; margin-top:10px"  title="Seleccione Formato" required="required">
                                        <option value="">Formato</option>
                                        <?php $fr = $con->Listar("SELECT * FROM gf_formatos_exogenas WHERE parametrizacionanno =$anno ORDER BY formato ASC");
                                            for ($f = 0; $f < count($fr); $f++) {
                                                echo '<option value ="'.$fr[$f][0].'">'.$fr[$f][1].' - '.$fr[$f][2].'</option>';
                                            }
                                        ?>
                                    </select>
                                </div> 
                            </div>
                            <div id="forma-modal" class="modal-footer">
                            </div>
                        </div>
                    </div>
                </div>
                <script>
                    $("#formatoe").change(function(){
                        var formato = $("#formatoe").val();
                        if(formato !=""){
                             window.location = 'GF_CONFIGURACION_EXOGENAS.php?formato='+formato;
                        }
                    })
                    $("#formatoe").select2({ 
                        allowClear :true
                    });
                </script>
                <?php } ?>
            </div>                
        </div>
        <?php require_once('footer.php'); ?>
        <link rel="stylesheet" href="css/bootstrap-theme.min.css">
        <script src="js/bootstrap.min.js"></script>
        </div>
    <script src="js/md5.js"></script>
    <div class="modal fade" id="modalMensajes" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <label id="mensaje" name="mensaje" style="font-weight: normal"></label>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="Aceptar" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    
</body>
</html>