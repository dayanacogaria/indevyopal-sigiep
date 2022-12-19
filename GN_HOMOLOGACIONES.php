<?php
#############################################################################################
#       ***************************     Modificaciones      ***************************     #
#############################################################################################
#19/04/2018 | Erica G.  | Parametrizacion
#Modificado por:        Alexander Numpaque Fecha de modificación: 14/08/2017
#Descripción: Se quitaron las peticiones post por envios xajax, haciendo todo por GET directamente
#############################################################################################
require_once('head.php');
require_once('Conexion/conexion.php');
@session_start();
$anno = $_SESSION['anno'];
$compania = $_SESSION['compania'];
?>
    <link rel="stylesheet" href="css/select2.css">
    <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
    <link rel="stylesheet" href="css/jquery-ui.css">
    <script src="js/jquery-ui.js"></script>
    <script type="text/javascript" src="js/select2.js"></script>
    <title>Homologaciones</title>
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
                <?php
                //Declaraión de variables vacias
                $report = "";                  //Variable de captura para valor de report
                $idReport = "";                //Id de informe
                $typeReport = "";              //id de tipo informe
                $nameReport = "";              //Nombre de informe
                $nameTypeR  = "";              //Nombre de tipo de informe
                if(!empty($_GET['report'])) {  //Validación de variable informe en la url
                    $report = $_GET['report']; //Capturamos la variable de la url
                    //Consulta para obtener los datos de informe y tipo
                    $sqlRT    = "SELECT    inf.id, inf.nombre, tpi.id, tpi.nombre
                                 FROM      gn_informe inf
                                 LEFT JOIN gn_tipo_informe tpi ON inf.tipo_informe  = tpi.id
                                 WHERE     md5(inf.id) = '$report'";
                    $resultRT = $mysqli->query($sqlRT);
                    $rowRT    = mysqli_fetch_row($resultRT);
                    //Cargue de variables
                    $idReport   = $rowRT[0];
                    $nameReport = $rowRT[1];
                    $typeReport = $rowRT[2];
                    $nameTypeR  = $rowRT[3];
                }
                ?>
                <a href="index2.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Inicio"></a>
                <h2 id="forma-titulo3" align="center" style="margin-top: 0px; margin-bottom: 5px; margin-right: 4px; margin-left: 4px; height: 40px; font-size: 25px;display:inline-block;width: 96%"><?php echo empty($nameReport)?"Homologaciones":"Homologaciones (Informe:".ucwords(mb_strtolower($nameReport)).")";?></h2>
            </div>
            <div class="col-sm-12 col-md-12 col-lg-12">
                <div class="form-group">
                    <label for="Buscar" class="control-label col-sm-1 col-md-1 col-lg-1 text-left">Buscar:</label>
                    <div class="col-sm-2 col-md-2 col-lg-2">
                        <select name="" class="form-control text-left" id="filtrar">
                            <?php
                            echo "<option value=\"\">Filtrar por</option>";
                            if(!empty($idReport)){                              # Validamos que la variable $idReport no este vacia
                                # Consulta para obtener la query relacionada a la tabla de origen y la informe (Consultas preparadas)
                                $sql_r = "SELECT tbH.select_table_origen
                                          FROM   gn_tabla_homologable tbH
                                          WHERE  tbH.informe = $idReport";
                                $res_r = $mysqli->query($sql_r);
                                if($res_r->num_rows > 0){                        # Validamos que retorne valores
                                    $row_r   = $res_r->fetch_row();
                                    # Buscamos la posición de la palabra select dentro del string
                                    $x1      = stripos(strtolower($row_r[0]), "select");
                                    $x2      = strlen("select ");                     # Tomamos el tamaño de la palabra select
                                    $x3      = $x1 + $x2;                             # Sumamos la posición $x1 y el tamaño de la palabra
                                    $substr  = substr($row_r[0], $x3);                # Quitamos la palabra select
                                    $x4      = stripos($substr,"from");               # Buscamos la pablabra from
                                    $x5      = strlen("from");                        # Tomamos el tamaño de la palabra de from
                                    $x6      = $x4;                                   # Sumamos la posición de y el tamaño de la palabra from
                                    $substr2 = substr($substr, 0, $x6);               # Desde la posición 0 y la palabra armamos un string
                                    $x7      = strlen("distinct ");                   # Tamaño de la palabra distinct
                                    $substr3 = substr($substr2, $x7);                 # Creamos string con el tamaño de $x7
                                    $substr3 = str_replace(' ', '',$substr3);         # Reemplazamos los espacios
                                    $substr3 = str_replace('-', '',$substr3);         # Quitamos '-' por espacios en blanco
                                    $substr3 = str_replace('concat_ws', '',$substr3); # Quitamos la palabra concat_ws del string
                                    $substr3 = str_replace("'',", '',$substr3);       # Quitamos "''" del string
                                    $substr3 = str_replace(",'fuente:'", '',$substr3);# Quitamos la palbra fuente y doble comilla
                                    $substr3 = str_replace("(", '', $substr3);         # Quitamos ( del String
                                    $substr3 = str_replace(")", '', $substr3);        # Quitamos ) del string
                                    $substr3 = explode(",", $substr3);                # Creamos string con el string ya que $substr=a,b,c..
                                    for($i = 1; $i < count($substr3); $i++){          # Despleglamos el array formando las opciones
                                        echo "<option value=\"$substr3[$i]\">".$substr3[$i]."</option>";
                                    }
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-sm-3 col-md-3 col-lg-3">
                        <div class="col-sm-10 col-md-10 col-lg-10">
                            <input type="txtBuscar" name="txtBuscar" id="txtBuscar" class="form-control" placeholder="Buscar">
                        </div>
                        <div class="col-sm-1 col-md-1 col-lg-1 text-left">
                            <a onclick="capturar_texto($('#filtrar').val(),$('#txtBuscar').val())" class="btn btn-primary" title="Buscar" id="btnBuscar"><i class="glyphicon glyphicon-search"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-12 col-lg-12">
                <div class="col-sm-2 col-md-2 col-lg-2 text-left">
                    <a class="btn btn-primary" title="Abrir Ventana Homologar informe" onclick="openModal()"><span class="glyphicon glyphicon-plus"></span></a>
                </div>
                <div class="col-sm-10 col-md-10 col-lg-10 text-right">
                        <?php
                        $items = 10;
                        $total = "";
                        $empieza = "";

                        $filtro      = "";
                        $valorFiltro = "";
                        if(!empty($_GET['filtro']) && !empty($_GET['texto'])){
                            $filtro = $_GET['filtro']; $valorFiltro = $_GET['texto'];
                        }
                        if(!empty($idReport)){
                            if(isset($_GET['pagina'])){
                                $pagina = $_GET['pagina'];
                            }else{
                                $pagina = 1;
                            }

                            $empieza = ($pagina - 1) * $items;

                            # Consulta para obtener la query relacionada a la tabla de origen y la informe (Consultas preparadas)
                            $sql_t = "SELECT tbH.select_table_origen
                                      FROM   gn_tabla_homologable tbH
                                      WHERE  tbH.informe = $idReport";
                            $res_t = $mysqli->query($sql_t);
                            if($res_t->num_rows > 0){
                                $q_t = $res_t->fetch_row();
                                if(!empty($filtro) && !empty($valorFiltro)){
                                    $consu    = str_replace("@", $anno,$q_t[0]);
                                    $consu    = str_replace("$", $compania,$consu);
                                    $needle  = " $filtro like '%$valorFiltro%' ";
                                    $x1      = stripos(ucwords($consu), "where");                  #Posición en donde encontramos la palabrea where dentro de la query
                                    $substr1 = substr($consu, 0, $x1);                             #Extraemos un string de la consulta de la posición 0 hasta $x1
                                    $substr2 = substr($consu, $x1,strlen($consu));                #Extraemos un string desde $x1 hasta el tamaño total del string $qry_ori
                                    $x2      = stripos($substr2, "order");                          #Buscamos la posición en donde se encuentre la palabra order
                                    $substr3 = substr($substr2, 0, $x2);                            #Obtenemos un substring desde 0 hasta $x2 de $substr2
                                    $substr3 = substr($substr3, 5);                                 #A substr3 le quitamos la palabra where la cual es la posición de 0 a 5
                                    $substr4 = substr($substr2, $x2, strlen($substr2));             #Obtenemos un $substr de 2 desde $x2 hasta el total de $substr obteniendo el order
                                    $query  = $substr1."where".$needle." and ".$substr3.$substr4." LIMIT $empieza, $items";  #Armamos el string de la consulta incluyendo nuestro $needle
                                    $res_t = $mysqli->query($query);
                                    $q = $substr1."where".$needle." and ".$substr3.$substr4;
                                    $rs_f = $mysqli->query($q);
                                    $filas = $rs_f->num_rows;
                                }else{
                                    $consu    = str_replace("@", $anno,$q_t[0]);
                                    $consu    = str_replace("$", $compania,$consu);
                                    $query = $consu;
                                    $res_t = $mysqli->query($query);
                                    $filas = $res_t->num_rows;
                                }

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
                                    if(!empty($filtro) && !empty($valorFiltro)){
                                        echo "<li><a href=\"GN_HOMOLOGACIONES.php?report=$report&filtro=$filtro&texto=$valorFiltro&pagina=1\" title=\"Primero\"><span class=\"glyphicon glyphicon-chevron-left\"></span></a></li>";
                                        echo "<li><a href=\"GN_HOMOLOGACIONES.php?report=$report&filtro=$filtro&texto=$valorFiltro&pagina=$ret\" title=\"Anterior\"><span class=\"glyphicon glyphicon-menu-left\"></span></a></li>";
                                    }else{
                                        echo "<li><a href=\"GN_HOMOLOGACIONES.php?report=$report&pagina=1\" title=\"Primero\"><span class=\"glyphicon glyphicon-chevron-left\"></span></a></li>";
                                        echo "<li><a href=\"GN_HOMOLOGACIONES.php?report=$report&pagina=$ret\" title=\"Anterior\"><span class=\"glyphicon glyphicon-menu-left\"></span></a></li>";
                                    }
                                }
                                for($i = $pagina; $i <= $pag; $i++){
                                    if(!empty($filtro) && !empty($valorFiltro)){
                                        if($i <= $total){
                                            if($i == $pagina){
                                                echo "<li class=\"active\"><a href=\"GN_HOMOLOGACIONES.php?report=$report&filtro=$filtro&texto=$valorFiltro&pagina=$i\">$i</a></li>";
                                            }else{
                                                echo "<li><a href=\"GN_HOMOLOGACIONES.php?report=$report&filtro=$filtro&texto=$valorFiltro&pagina=$i\">$i</a></li>";
                                            }
                                        }
                                    }else{
                                        if($i <= $total){
                                            if($i == $pagina){
                                                echo "<li class=\"active\"><a href=\"GN_HOMOLOGACIONES.php?report=$report&pagina=$i\">$i</a></li>";
                                            }else{
                                                echo "<li><a href=\"GN_HOMOLOGACIONES.php?report=$report&pagina=$i\">$i</a></li>";
                                            }
                                        }
                                    }
                                }
                                if($pagina < $total){
                                    if(!empty($filtro) && !empty($valorFiltro)){
                                        echo "<li><a href=\"GN_HOMOLOGACIONES.php?report=$report&filtro=$filtro&texto=$valorFiltro&pagina=$avn\" title=\"Siguiente\"><span class=\"glyphicon glyphicon-menu-right\"></span></a></li>";
                                        echo "<li><a href=\"GN_HOMOLOGACIONES.php?report=$report&filtro=$filtro&texto=$valorFiltro&pagina=$total\" title=\"Ultimo\"><span class=\"glyphicon glyphicon-chevron-right\"></span></a></li>";
                                    }else{
                                        echo "<li><a href=\"GN_HOMOLOGACIONES.php?report=$report&pagina=$avn\" title=\"Siguiente\"><span class=\"glyphicon glyphicon-menu-right\"></span></a></li>";
                                        echo "<li><a href=\"GN_HOMOLOGACIONES.php?report=$report&pagina=$total\" title=\"Ultimo\"><span class=\"glyphicon glyphicon-chevron-right\"></span></a></li>";
                                    }
                                }
                                echo "</ul>";
                            }
                        }
                         ?>
                </div>
            </div>
            <div class="col-sm-12 col-md-12 col-lg-12" style="margin-top:8px">
                <div class="contTabla">
                    <?php
                    if(!empty($_REQUEST['report'])){      //Validamos que si la variable report no viene vacia
                        //Inicializamos las variables en 0 o vacio
                        $x    = 0;
                        $html = "";
                        $colO = "";                       //Nombre de columna origen
                        $colD = 0;                        //Contador de columnas destino
                        $columnasDestino = "";            //Nombres de columnas Destino
                        $tablaOrigen     = "";            //Nombres de tabla Origen
                        $tablasDestino   = "";            //Nombres de tablas Destino
                        $consultasTablaD = "";            //Consultas de tabla destino
                        $idTH            = "";            //Id de las tablas homologables
                        $consultaTablaO  = "";            //Consulta de la tabla de origen
                        $html.= "<table id=\"tblHologaciones\" name=\"tblHomologaciones\" class=\"table table-hover table-striped text-left table-condensed display\" cellspacing=\"0\" width=\"100%\">";
                        //Impresión de cabeza de la tabla
                        $html.= "<thead>";
                        $html.= "<tr>";
                        //Consulta para obtener columna de origen por informe
                        $sqlColO = "SELECT tbH.columna_origen,tbH.tabla_origen,tbH.select_table_origen
                                    FROM   gn_tabla_homologable tbH
                                    WHERE  tbH.informe = $idReport";
                        $resultColO = $mysqli->query($sqlColO);
                        $rowColO    = $resultColO->fetch_row();
                        //Asiganción de valores devueltos por consulta
                        $colO           = $rowColO[0];    //Captura de columna origen
                        $tablaOrigen    = $rowColO[1];    //Captura del nombre de la tabla
                        $consultaTablaO = str_replace("@", $anno,$rowColO[2]);    //Captura de select de la tabla origen
                        $consultaTablaO = str_replace("$", $compania,$consultaTablaO);    //Captura de select de la tabla origen
                        
                        //Impresión de valores devueltos por la consulta
                        $html.= "<th class=\"cabeza cursor cb\" title=\"Tabla de Origen : ".ucwords($tablaOrigen)."\">".ucfirst(ucwords($colO))."</th>";
                        //Consulta para obtener columnas de destino por informe
                        $sqlTableH = "SELECT  tbH.columna_destino,tbH.tabla_destino,tbH.select_table_destino,tbH.id
                                      FROM    gn_tabla_homologable tbH
                                      WHERE   tbH.informe = $idReport";
                        $resultTableH = $mysqli->query($sqlTableH);
                        while ($rowTH = $resultTableH->fetch_row()) {  //Impresión de valores devueltos por la consulta
                            $colD++;  //Contador de columnas de destino
                            #Impresión de Nombres de columna destino
                            $html.= "<th class=\"cursor cb\" style='width: 100px'>".ucfirst(ucwords($rowTH[0].PHP_EOL.'(Tabla: '.$rowTH[1].')'))."</th>";
                            $columnasDestino.= $rowTH[0].",";   //Captura de columnas destino
                            $tablasDestino  .=   $rowTH[1].","; //Captura de tablas destino
                            $consulta = str_replace("@", $anno,$rowTH[2]);
                            $consulta = str_replace("$", $compania,$consulta);
                            $consultasTablaD.= $consulta.";";   //Captura de consultas de tabla destino
                            $idTH.=$rowTH[3].",";               //Captura de ids de tabla homologable
                        }
                        $html.= "</tr>";
                        $html.= "</thead>";
                        #Impresión de cuerpo de tabla
                        $html.= "<tbody>";
                        $columnasDestino = substr($columnasDestino,0,strlen($columnasDestino)-1);   //Quitamos la ultima coma
                        $tablasDestino   = substr($tablasDestino,0,strlen($tablasDestino)-1);       //Quitamos la ultima coma
                        $idTH            = substr($idTH,0,strlen($idTH)-1);                         //Quitamos la ultima coma
                        $columnD         = explode(",",$columnasDestino);                           //Array de columnas destino
                        $tbD             = explode(",", $tablasDestino);                            //Array de tablas destino
                        $selectDestino   = explode(";",$consultasTablaD);                           //Array de selects destino
                        $idTablaH        = explode(",", $idTH);                                     //Array de las id de tabla homologable
                        //Consulta de tabla origen
                        if(!empty($consultaTablaO)){
                            if(!empty($filtro) && !empty($valorFiltro)){
                                $needle  = " $filtro like '%$valorFiltro%' ";
                                $x1      = stripos(ucwords($consultaTablaO), "where");                 #Posición en donde encontramos la palabrea where dentro de la query
                                $substr1 = substr($consultaTablaO, 0, $x1);                            #Extraemos un string de la consulta de la posición 0 hasta $x1
                                $substr2 = substr($consultaTablaO, $x1,strlen($consultaTablaO));              #Extraemos un string desde $x1 hasta el tamaño total del string $qry_ori
                                $x2      = stripos($substr2, "order");                          #Buscamos la posición en donde se encuentre la palabra order
                                $substr3 = substr($substr2, 0, $x2);                            #Obtenemos un substring desde 0 hasta $x2 de $substr2
                                $substr3 = substr($substr3, 5);                                 #A substr3 le quitamos la palabra where la cual es la posición de 0 a 5
                                $substr4 = substr($substr2, $x2, strlen($substr2));             #Obtenemos un $substr de 2 desde $x2 hasta el total de $substr obteniendo el order
                                $sqlT  = $substr1."where".$needle." and ".$substr3.$substr4." LIMIT $empieza, $items";  #Armamos el string de la consulta incluyendo nuestro $needle
                            }else{
                                $sqlT = "$consultaTablaO LIMIT $empieza, $items";
                            }

                            $resultT  = $mysqli->query($sqlT);
                            $cantidad = $resultT->num_rows;
                            $y = 0; //Contador de filas
                            while($rowT = $resultT->fetch_row()){  //Impresión de valores devueltos por la consulta
                                ++$y; //Contamos las filas
                                #Impresión de filas de la tabla
                                $html.= "<tr>";
                                $html.= "<td style=\"width:250px\" class=\"cpt\"><span name=\"Origen$rowT[0]\" id=\"Origen$rowT[0]\">".(ucwords(mb_strtolower($rowT[1])))."</span></td>";       //Impresión de campo conocido, de lta tabla origen
                                for ($a=0; $a <= $colD-1; ++$a) {                   //Ciclo de impresión para select
                                    
                                    $html.= "<td style='width:150px'>";
                                    $x++;
                                    $html.= "<input type=\"hidden\" id=\"txt$columnD[$a]$x\" value=\"\">";
                                    $html.= "<select class=\"select2 form-control col-sm-1\" onchange=\"guardarHomologacion($rowT[0],this.value,".$idTablaH[$a].",".$idTablaH[$a].","."$('#txt".$columnD[$a].$x."').val()".",'txt".$columnD[$a].$x."');\" name=\"$columnD[$a]$y\" id=\"$columnD[$a]$y\" style=\"width:150px;align:center\">";     //Campo select generado de manera dinamica
                                    $html.= '<option value=""></option>'; //opción con el nombre del campo
                                    $sqlTD       = $selectDestino[$a];             //Consulta de la tabla destino
                                    $resultTD    = $mysqli->query($sqlTD);
                                    while($rowTD = $resultTD->fetch_row()){        //Impresión de valores
                                        #Consulta para saber cuales registros o valores estan en gn_homologaciones
                                        if(!empty($rowTD[0])){
                                            $sqlHom = "SELECT    hom.id FROM gn_homologaciones hom
                                                       LEFT JOIN gn_tabla_homologable th1 ON hom.origen = th1.id
                                                       LEFT JOIN gn_informe i ON th1.informe = i.id
                                                       WHERE     hom.id_origen  = '$rowT[0]'
                                                       AND       hom.id_destino = '$rowTD[0]'
                                                       AND       hom.origen     = $idTablaH[$a]
                                                       AND       hom.destino    = $idTablaH[$a]
                                                       AND       th1.informe    = $idReport";
                                            $resultHom = $mysqli->query($sqlHom);
                                            $c = $resultHom->fetch_row();                  //Se carga el valor de la consulta
                                            if(!empty($c[0])){                             //Se valida que es diferente de vacio
                                                $pos = strpos($selectDestino[$a],"order"); //Buscamos la palabra order by en la Consulta
                                                if(!empty($pos)){                          //Validamos que no venga vacia
                                                    $str1 = substr($selectDestino[$a],0,$pos);//Tomamos la Consulta desde la posición 0 hasta la posicion en la que se encontro la palabra
                                                    $str2 = substr($selectDestino[$a],$pos);//Tomamos la consulta desde la posición en que se hayo la palbra
                                                    $sqlTD1 = $str1." AND id_unico = '$rowTD[0]' $str2"; //Armamos nuestra query para obtener el valor especifico
                                                }else{
                                                    $sqlTD1 = $selectDestino[$a]." AND id_unico = '$rowTD[0]' ";    //Consulta de la tabla destino cuando existe valor en la tabla gn_homologaciones
                                                }
                                                $resultTD1 = $mysqli->query($sqlTD1);
                                                $rowTD1 = mysqli_fetch_row($resultTD1);//Carga de valores
                                                $html.= "<option value=".$rowTD1[0]." selected>".ucwords(mb_strtolower($rowTD1[1]))."</option>";//Option con el valor optenido cuando exsite en la base de datos
                                                $html.= "<script>\n";
                                                $html.= "$(document).ready(function(){\n";
                                                $html.= "var fila$x = $c[0];\n";
                                                $html.= "$(\"#txt$columnD[$a]$x\").val(fila$x);\n";
                                                $html.= "});\n";
                                                $html.= "</script>";
                                            }
                                        }

                                        $html.= "<option value=".$rowTD[0].">".ucwords(mb_strtolower($rowTD[1]))."</option>"; //Opción impresa
                                    }
                                    $html.= "</select>\n";   //Fin del select
                                    $html.= "</td>\n";       //Fin de la celda
                                    
                                }
                                $html.= "</tr>\n";           //Fin de la fila
                            }
                        }else{
                            $html .= "<h1>No hay configuración</h1>";
                        }

                        $html.= "</tbody>\n";            //Fin del cuerpo de la tabla
                        $html.= "</table>";
                        $html.= "<script>\n";            //Script para carga la libreria select2 en los combos o campos de seleccion
                        $html.= "$('.select2').select2({";
                        $html.= "allowClear:true";
                        $html.= "});";
                        $html.= "</script>\n";
                        echo $html;
                    }
                    ?>
                </div>
            </div>
            <?php require_once('footer.php'); ?>
            <div class="modal fade" id="modalHomologaciones" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p id="mensaje"></p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="btnModal" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalCargaI" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog" style="width: 780px">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Homologaciones</h4>
                    <div class="col-sm-offset-11" style="margin-top:-30px;margin-right: -25px">
                        <button type="button" class="btn btn-xs" style="color: #000;" data-dismiss="modal" ><li class="glyphicon glyphicon-remove"></li></button>
                    </div>
                </div>
                <div class="modal-body row" style="margin-top: 8px">
                    <div class="client-form form-horizontal text-left" style="margin-top:-20px;">
                        <div class="form-group form-inline" style="margin-top: 10px;margin-bottom: 0px">
                            <label for="sltTipoInforme" class="control-label col-sm-3">Tipo Informe:</label>
                                <select name="sltTipoInforme" id="sltTipoInforme" class="form-control col-sm-1" onchange="cargarInformes()" title="Seleccione tipo de informe" style="width: 200px">
                                <?php
                                if(empty($typeReport)){ //Validación de variable $typeReport este vacia
                                    //Impresión de opción vacia
                                    echo "<option value=''>Tipo Informe</option>";
                                    //Consulta para obtener todos los tipos de informe
                                    $sqlTI = "SELECT id,nombre FROM gn_tipo_informe";
                                    $resultTI = $mysqli->query($sqlTI); //Impresión de valores de consulta
                                    while ($rowTI = mysqli_fetch_row($resultTI)) {
                                        echo "<option value=".$rowTI[0].">".ucwords(mb_strtolower($rowTI[1]))."</option>";
                                    }
                                }else{
                                    //Impresión de opción con el id y el nombre de tipo reporte
                                    echo "<option value='$typeReport'>".ucwords(mb_strtolower($nameTypeR))."</option>";
                                    //Consulta de tipos de informes diferentes al que se selecciono
                                    $sqlTI = "SELECT id,nombre FROM gn_tipo_informe WHERE id != $typeReport";
                                    $resultTI = $mysqli->query($sqlTI);
                                    while ($rowTI = mysqli_fetch_row($resultTI)) {  //Impresión de valores de consulta
                                        echo "<option value=".$rowTI[0].">".ucwords(mb_strtolower($rowTI[1]))."</option>";
                                    }
                                }
                                ?>
                                </select>
                                <label for="sltInforme" class="control-label col-sm-2">Informe :</label>
                                <select name="sltInforme" id="sltInforme" class="form-control col-sm-1" onchange="cargaConfiguracion()" title="Seleccione Informe" style="width: 200px">
                                <?php
                                if(empty($idReport)){  //Validación de variable $idReport este vacia
                                    //Impresión de etiqueta vacia
                                    echo "<option value=''>Informe</option>";
                                }else{
                                    //Impresión de etiqueta con el informe seleccionado
                                    echo "<option value='$idReport'>".ucwords(mb_strtolower($nameReport))."</option>";
                                    //Consulta de informes por tipos diferentes al id seleccionada
                                    $sqlReport = "SELECT id,nombre FROM gn_informe WHERE tipo_informe = $typeReport AND id != $idReport";
                                    $resultReport = $mysqli->query($sqlReport);
                                    while ($rowR = mysqli_fetch_row($resultReport)) { //Impresión de valores de consulta
                                        echo "<option value='$rowR[0]'>".ucwords(mb_strtolower($rowR[1]))."</option>";
                                    }
                                }
                                ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="js/md5.js"></script>
    <script type="text/javascript">
        //Cargue de informes por tipo
        function cargarInformes(){
            var tipo = parseInt($("#sltTipoInforme").val());  //Captura de valor de tipo informe
            if(!isNaN(tipo)){                //Validación de valor
                var form_data = {
                    existente:41,
                    tipo:tipo
                };
                $.ajax({
                    type:'POST',
                    url:'consultasBasicas/consultarNumeros.php',
                    data:form_data,
                    success: function(data){
                        $("#sltInforme").html(data).fadeIn();   //Cargue de campo con valores devueltos de la consulta
                        $("#sltInforme").css('display','none'); //Ocultar campo
                    }
                });
            }
        }
        //Función para cargue de formulario y configuación de homologación
        function cargaConfiguracion() {
            var informe = parseInt($("#sltInforme").val());   //Captura de valor de informe
            if(!isNaN(informe)) {                             //Validación de valor
                window.location = 'GN_HOMOLOGACIONES.php?report='+md5(informe);
            }
        }
        //Agregando libreria select
        $("#sltTipoInforme, #sltInforme, #filtrar").select2({ //Tipo de informe
            allowClear :true
        });
        //Función de guardado en la tabla de homologaciones
        function guardarHomologacion(idOrigen,idDestino,origen,destino,id_hom,campo) {
            var idDestino = (idDestino);//Redefinición de la variable destino
            var form_data = {     //Array de envio data de ajax
                idOrigen:idOrigen,
                idDestino:idDestino,
                origen:origen,
                destino:destino,
                existente:45,
                id_hom:id_hom
            };
            //variable de resultado vacia
            var result = '';
            //Envio ajax
            $.ajax({
                type:'POST',
                url:'consultasBasicas/consultarNumeros.php',
                data:form_data,
                success: function(data,textStatus,jqXHR) {
                    console.log(data,textStatus,jqXHR);
                    result = JSON.parse(data);
                    var d = result.split(';');
                    $("#"+campo).val(d[1]);
                },error : function(data,textStatus,jqXHR){
                    console.log(data,textStatus,jqXHR);
                    alert('Error :'+textStatus+', ->XHR ->'+jqXHR);
                }
            });
        }
        //Función para abrir modal
        function openModal(){
          $("#modalCargaI").modal('show');
        }
        var report = parseInt(<?php echo $idReport ?>); //Variable de captura del valor de reporte
        if(isNaN(report)){
            openModal();
        }

        function capturar_texto($needle ,$texto){
            window.location = '<?php echo "GN_HOMOLOGACIONES.php?report=$report" ?>'+'&filtro='+$needle+'&texto='+$texto;;
        }
    </script>
    </div>
</body>
</html>