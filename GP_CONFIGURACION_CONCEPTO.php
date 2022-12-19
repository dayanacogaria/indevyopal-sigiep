<?php
#####################################################################################
# ********************************* Modificaciones *********************************#
#####################################################################################
#23/05/2018 | Erica G. | Archivo Creado
####/################################################################################
require_once('head.php');
require_once('Conexion/conexion.php');
require_once('Conexion/ConexionPDO.php');
$con      = new ConexionPDO();
$anno     = $_SESSION['anno'];
$compania     = $_SESSION['compania'];
$txt      = "";
$gettexto = "";
$where    = " WHERE cp.compania = $compania ";
if (isset($_GET['texto']) && !empty($_GET['texto'])){
    $txt = $_GET['texto'];
    $gettexto = "&texto=".$txt;
    $where .= "AND cp.nombre LIKE '%".$txt."%'";
}
?>
    <link rel="stylesheet" href="css/select2.css">
    <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
    <link rel="stylesheet" href="css/jquery-ui.css">
    <script src="js/jquery-ui.js"></script>
    <script type="text/javascript" src="js/select2.js"></script>
    <title>Configuración Concepto</title>
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
                <h2 id="forma-titulo3" align="center" style="margin-top: 0px; margin-bottom: 5px; margin-right: 4px; margin-left: 4px; height: 40px; font-size: 25px;display:inline-block;width: 96%">Configuración Concepto</h2>
                <?php if(!empty($_REQUEST['tipo'])) { ?>
                <!--------  Buscar ------------->
                <div class="col-sm-12 col-md-12 col-lg-12">
                    <div class="form-group">
                        <input type="hidden" name="tipo" id="tipo" value="<?php echo $_REQUEST['tipo'];?>">
                        <input type="hidden" name="pgn" id="pgn" value="<?php echo $_REQUEST['pagina'];?>">
                        <div class="col-sm-4 col-md-4 col-lg-4" id="tBuscar" style="display:block">
                            <input type="txtBuscar" name="txtBuscar" id="txtBuscar" class="form-control col-sm-1" placeholder="Buscar" value="<?php echo $txt; ?>">
                        </div>
                        <div class="col-sm-6 col-md-6 col-lg-6">
                            <div class="col-sm-1 col-md-1 col-lg-1 text-left">
                                <a onclick="capturar_texto()" class="btn btn-primary" title="Buscar" id="btnBuscar"><i class="glyphicon glyphicon-search"></i></a>
                            </div>
                            <div class="col-sm-1 col-md-1 col-lg-1 text-left">
                                <a class="btn btn-primary" title="" href="GP_CONFIGURACION_CONCEPTO.php"><span class="glyphicon glyphicon-plus"></span></a>
                            </div>
                            <div class="col-sm-1 col-md-1 col-lg-1 text-left">
                                <a class="btn btn-primary" title="" href="informes/INF_GP_CONFIGURACION_CONCEPTO.php?tipo=<?php echo $_REQUEST['tipo'];?>" target="_blank"><span class="fa fa-file-excel-o"></span></a>
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
                        $tipo_c = $_REQUEST['tipo'];
                        $empieza = ($pagina - 1) * $items;
                        $t_t = "SELECT * FROM gp_concepto cp $where";
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
                            echo "<li><a href=\"GP_CONFIGURACION_CONCEPTO.php?tipo=$tipo_c&pagina=1$gettexto\" title=\"Primero\"><span class=\"glyphicon glyphicon-chevron-left\"></span></a></li>";
                            echo "<li><a href=\"GP_CONFIGURACION_CONCEPTO.php?tipo=$tipo_c&pagina=$ret$gettexto\" title=\"Anterior\"><span class=\"glyphicon glyphicon-menu-left\"></span></a></li>";
                            
                        }
                        for($i = $pagina; $i <= $pag; $i++){
                            if($i <= $total){
                                if($i == $pagina){
                                    echo "<li class=\"active\"><a href=\"GP_CONFIGURACION_CONCEPTO.php?tipo=$tipo_c&pagina=$i$gettexto\">$i</a></li>";
                                }else{
                                    echo "<li><a href=\"GP_CONFIGURACION_CONCEPTO.php?tipo=$tipo_c&pagina=$i$gettexto\">$i</a></li>";
                                }
                            }
                        }
                        if($pagina < $total){
                            echo "<li><a href=\"GP_CONFIGURACION_CONCEPTO.php?tipo=$tipo_c&pagina=$avn$gettexto\" title=\"Siguiente\"><span class=\"glyphicon glyphicon-menu-right\"></span></a></li>";
                            echo "<li><a href=\"GP_CONFIGURACION_CONCEPTO.php?tipo=$tipo_c&pagina=$total$gettexto\" title=\"Ultimo\"><span class=\"glyphicon glyphicon-chevron-right\"></span></a></li>";
                            
                        }
                        echo "</ul>";
                            
                        ?>
                </div>
                <script>
                    function capturar_texto(){
                        let texto = $("#txtBuscar").val();
                        let pagina  = $("#pgn").val();
                        let tipo  = $("#tipo").val();
                        if (pagina > 0 && texto.length > 0){
                            window.location = 'GP_CONFIGURACION_CONCEPTO.php?tipo='+tipo+'&pagina='+pagina+'&texto='+texto;
                        }else if (pagina < 1 && texto.length > 0){
                            window.location = 'GP_CONFIGURACION_CONCEPTO.php?tipo='+tipo+'&texto='+texto;
                        }else {
                            window.location = 'GP_CONFIGURACION_CONCEPTO.php?tipo='+tipo;
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
                        $html.= '<th class="cabeza cursor cb" title="Concepto Facturación">Concepto Facturación</th>';
                        $html.= '<th class="cabeza cursor cb" title="Concepto Rubro">Concepto Rubro</th>';
                        $html.= '<th class="cabeza cursor cb" title="Rubro Fuente">Rubro Fuente</th>';
                        $html.= '<th class="cabeza cursor cb" title="Guardar"></th>';
                        $html.= '</tr>';
                        $html.= '</thead>';
                        $html.= '<tbody>';
                        $sql  = $con->Listar("SELECT DISTINCT cp.id_unico, LOWER(tc.nombre), LOWER(cp.nombre) 
                            FROM gp_concepto cp 
                            LEFT JOIN gp_tipo_concepto tc ON cp.tipo_concepto = tc.id_unico 
                            $where
                            ORDER BY cp.nombre ASC 
                            LIMIT $empieza, $items");
                        for ($s = 0; $s < count($sql); $s++) {
                            $concepto = $sql[$s][0];
                            $html.= '<tr>';
                            $html.= '<form id="form'.$sql[$s][0].'" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="Javascript:guardar('.$sql[$s][0].')">';
                            $html.= '<input type ="hidden" name="concepto" id="concepto" value="'.$concepto.'">';
                            $html.= '<input type="hidden" name="tipo" id="tipo" value="'.$_REQUEST['tipo'].'">';
                            $html.='<td style="width:400px" class="cpt">';
                            $html.='<span name="concepto'.$sql[$s][0].'" id="concepto'.$sql[$s][0].'">'.'Tipo: '.ucwords($sql[$s][1]).' - Concepto: '.ucwords(($sql[$s][2])).'</span>';
                            $html.='</td>';
                            # ** Buscar Si Existe Configuración ** #
                            $conf = $con->Listar("SELECT
                                    cf.id_unico,
                                    cr.id_unico,
                                    LOWER(c.nombre),
                                    rb.codi_presupuesto,
                                    LOWER(rb.nombre),
                                    rf.id_unico,
                                    rbc.codi_presupuesto,
                                    LOWER(rbc.nombre),
                                    LOWER(f.nombre)
                                  FROM
                                    gp_configuracion_concepto cf
                                  LEFT JOIN
                                    gf_concepto_rubro cr ON cf.concepto_rubro = cr.id_unico
                                  LEFT JOIN
                                    gf_concepto c ON cr.concepto = c.id_unico
                                  LEFT JOIN
                                    gf_rubro_pptal rb ON cr.rubro = rb.id_unico
                                  LEFT JOIN
                                    gf_rubro_fuente rf ON cf.rubro_fuente = rf.id_unico
                                  LEFT JOIN
                                    gf_rubro_pptal rbc ON rf.rubro = rbc.id_unico
                                  LEFT JOIN
                                    gf_fuente f ON rf.fuente = f.id_unico
                                  WHERE cf.concepto =$concepto 
                                    AND cf.parametrizacionanno = $anno 
                                    AND cf.tipo_cartera = $tipo_c");
                            $row_cr = $con->Listar("SELECT cr.id_unico, 
                                    c.nombre, rb.codi_presupuesto, rb.nombre 
                                    FROM gf_concepto_rubro cr 
                                    LEFT JOIN gf_concepto c ON cr.concepto = c.id_unico
                                    LEFT JOIN gf_rubro_pptal rb ON cr.rubro = rb.id_unico 
                                    WHERE c.parametrizacionanno = $anno AND rb.parametrizacionanno = $anno 
                                    AND c.clase_concepto = 1 
                                    ORDER BY rb.codi_presupuesto ");
                            $html.= '<td style="width:400px">';
                            $html.= '<select class="select2 form-control col-sm-1"  style="width:200px;align:center" name="con_rubro'.$sql[$s][0].'" id="con_rubro'.$sql[$s][0].'"  onchange="cargarRubroF('.$sql[$s][0].',this.value)" >';
                            
                            if(count($conf)>0){   
                                $html.= '<option value="'.$conf[0][1].'">'.ucwords($conf[0][2]).' - '.$conf[0][3].' '.ucwords($conf[0][4]).'</option>';
                                $html.= '<option> - </option>';
                                for ($i = 0; $i < count($row_cr); $i++) {
                                    $html.= '<option value="'.$row_cr[$i][0].'">'.$row_cr[$i][1].' - '.$row_cr[$i][2].' '.$row_cr[$i][3].'</option>';
                                }
                            } else {
                                $html.= '<option> - </option>';
                                for ($i = 0; $i < count($row_cr); $i++) {
                                    $html.= '<option value="'.$row_cr[$i][0].'">'.$row_cr[$i][1].' - '.$row_cr[$i][2].' '.$row_cr[$i][3].'</option>';
                                }
                            }
                            $html.= '</select>';
                            $html.= '</td>';
                            $html.= '<td style="width:400px">';
                            $html.= '<select class="select2 form-control col-sm-1"  style="width:200px;align:center" name="rubro_fuente'.$sql[$s][0].'" id="rubro_fuente'.$sql[$s][0].'" >';
                            if(count($conf)>0){  
                                $html.= '<option value="'.$conf[0][5].'">'.$conf[0][6].' '.ucwords($conf[0][7]).' - '.ucwords($conf[0][8]).'</option>';
                                $html.= '<option> - </option>';
                            } else {
                                $html.= '<option> - </option>';
                            }
                            $html.= '</select>';
                            $html.= '</td>';
                            $html.= '<td style="width:100px">';
                            $html.= '<button style="margin-left:10px;width: 40px;margin-top:  5px;height: 30px;"  type="submit" class="btn sombra btn-primary" title="Guardar"><i class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></i></button>';
                            $html.='</td>';
                            $html.='</form>';
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
                </script>
                <script>
                    function guardar(concepto){
                        var nameform ="form"+concepto;
                        var formData = new FormData($("#"+nameform)[0]);  
                        $.ajax({
                            type: 'POST',
                            url: "jsonPptal/gf_facturaJson.php?action=18",
                            data:formData,
                            contentType: false,
                            processData: false,
                            success: function(response)
                            {
                                $("#mensaje").html(response);
                                $("#modalMensajes").modal("show");
                                $("#Aceptar").click(function(){
                                    $("#modalMensajes").modal("hide");
                                })
                            }
                        });
                    }
                    function cargarRubroF(concepto, valor){
                        var option = "<option value=''> - </option>";
                        var form_data = {action:11, concepto:concepto,valor:valor};  
                        $.ajax({
                            type: 'POST',
                            url: "jsonPptal/gf_facturaJson.php",
                            data: form_data, 
                            success: function(response)
                            {
                                console.log(response);
                                option      = option + response;
                                var name    = "rubro_fuente"+concepto
                                
                                $("#"+name).html(option);
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
                                   <label class="control-label" style="display:inline-block; width:140px; margin-top:5px"><strong class="obligado">*</strong>Tipo Cartera: </label>
                                    <select name="formatoe" id="formatoe" class="select2 form-control "  style="display:inline-block; width:250px; margin-top:10px"  title="Seleccione Tipo Cartera" required="required">
                                        <option value="">Tipo Cartera</option>
                                        <?php $fr = $con->Listar("SELECT * FROM gp_tipo_cartera");
                                            for ($f = 0; $f < count($fr); $f++) {
                                                echo '<option value ="'.$fr[$f][0].'">'.$fr[$f][1].' '.$fr[$f][2].' - '.$fr[$f][3].'</option>';
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
                             window.location = 'GP_CONFIGURACION_CONCEPTO.php?tipo='+formato;
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