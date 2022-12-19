<?php
    #01/06/2017 --- Nestor B --- se modifico  la consulta que trae la informacion.
    require_once './Conexion/conexion.php';
    require_once ('./Conexion/conexion.php');
    #session_start();
    require_once './head_listar.php';

    $anno = $_SESSION['anno'];

    $sql = "SELECT d.id_unico,
                tr.numeroidentificacion, 
                IF(CONCAT_WS(' ',
                tr.nombreuno,
                tr.nombredos,
                tr.apellidouno,
                tr.apellidodos) 
                IS NULL OR CONCAT_WS(' ',
                tr.nombreuno,
                tr.nombredos,
                tr.apellidouno,
                tr.apellidodos) = '',
                (tr.razonsocial),
                CONCAT_WS(' ',
                tr.nombreuno,
                tr.nombredos,
                tr.apellidouno,
                tr.apellidodos)),
                d.cod_dec,
                d.fecha,
                c.codigo_mat 
            FROM gc_declaracion d 
            LEFT JOIN gc_contribuyente c ON d.contribuyente = c.id_unico
            LEFT JOIN gf_tercero      tr ON c.tercero = tr.id_unico
            LEFT JOIN gc_recaudo_comercial rc ON rc.declaracion = d.id_unico
            WHERE d.id_unico NOT IN(SELECT declaracion FROM gc_recaudo_comercial) AND d.parametrizacionanno = '$anno'  AND d.clase = 2";
  
    $resultado = $mysqli->query($sql);
?>
        <title>Listar Declaracion No Pagas</title>
        <script src="dist/jquery.validate.js"></script>
        <!-- Librerias de carga para el datapicker -->
        
        <!-- select2 -->
        <link rel="stylesheet" href="css/select2.css">
        <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
    </head>
    <body>
        <div class="container-fluid text-center">
            <div class="row content">
                <?php require_once './menu.php'; ?>
                <div class="col-sm-10 text-left">
                    <h2 id="forma-titulo3" align="center" style="margin-top: 0px; margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Declaraciones No Recaudadas</h2>
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;margin-top:-10px;">
                        <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                            <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <td style="display: none;">Identificador</td>
                                        <td width="7%" class="cabeza"></td>                                        
                                        <td class="cabeza"><strong>Código</strong></td>
                                        <td class="cabeza"><strong>Matricula</strong></td>
                                        <td class="cabeza"><strong>N° Identificacion</strong></td>
                                        <td class="cabeza"><strong>Contribuyente</strong></td>
                                        <td class="cabeza"><strong>fecha Declaración</strong></td>
                                    </tr>
                                    <tr>
                                        <th class="cabeza" style="display: none;">Identificador</th>
                                        <th width="7%"></th>                                        
                                        <th class="cabeza">Código</th>
                                        <th class="cabeza">Matricula</th>
                                        <th class="cabeza">N° Identificacion</th>
                                        <th class="cabeza">Contribuyente</th>
                                        <th class="cabeza">Fecha Declaración</th>
                                    </tr>
                                </thead>    
                                <tbody>
                                    <?php 
                                        while ($row = mysqli_fetch_row($resultado)) { 
                                            
                                            $cid      = $row[0];
                                            $cide     = $row[1];
                                            $cnomb    = $row[2];
                                            $ccod     = $row[3];
                                            $fdec     = date("d/m/Y", strtotime($row[4]));
                                            $matC     = $row[5];
                                        
                                    ?>
                                            <tr>
                                                <td class="campos" style="display: none;"><?php #echo $row[0]?></td>
                                                <td class="campos">
                                                    
                                                    <a class="campos" onclick="javascript:abrirRec(<?php echo $row[0] ?>);">
                                                        <i title="Generar Recaudo" class="glyphicon glyphicon-usd" ></i>
                                                    </a>
                                                </td>                                        
                                                <td class="campos"><?php echo $ccod?></td>                
                                                <td class="campos"><?php echo $matC?></td>                
                                                <td class="campos"><?php echo $cide?></td>                
                                                <td class="campos"><?php echo $cnomb?></td>                
                                                <td class="campos"><?php echo $fdec?></td>                
                                            </tr>
                                    <?php 
                                        }
                                    ?>
                                </tbody>
                            </table>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php 
            require_once 'modalRecaudo_Declracion_No_Paga.php'; 
        ?>
        <script>
            function abrirRec(id_dec){
                
                var form_data = {
                    id:id_dec
                }

                //Envio ajax
                $.ajax({
                    url:'modalRecaudo_Ica_Declaracion_No_Paga.php#modalRecaudo',
                    type:'POST',
                    data:form_data,
                    success: function(data,textStatus,jqXHR) {
                        $("#modalRecaudo").html(data);
                        $(".recaDec").modal('show');
                    },error: function(data,textStatus,jqXHR) {
                        alert('Error : D'+data+', status :'+textStatus+', jqXHR : '+jqXHR);
                    } 
                });
                
            }
        </script>
        
        <?php 
            
            require_once './footer.php'; 
        ?>
        
        <div class="modal fade" id="myModal" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">       
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <p>¿Desea eliminar el registro seleccionado de Concepto?</p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="ver"  class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                        <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="myModal1" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <p>Información eliminada correctamente.</p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="ver1" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="myModal2" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <p>No se pudo eliminar la información, el registo seleccionado está siendo utilizado por otra dependencia.</p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="ver2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    </div>
                </div>
            </div>
        </div>

        <!--Script que dan estilo al formulario-->

        <script type="text/javascript" src="js/menu.js"></script>
        <link rel="stylesheet" href="css/bootstrap-theme.min.css">
        <script src="js/bootstrap.min.js"></script>
        <!--Scrip que envia los datos para la eliminación-->
        <script type="text/javascript">
            function eliminar(id)
            {
                var result = '';
                $("#myModal").modal('show');
                $("#ver").click(function(){
                    $("#mymodal").modal('hide');
                    $.ajax({
                        type:"GET",
                        url:"json/eliminarConceptoJson.php?id="+id,
                        success: function (data) {
                            result = JSON.parse(data);
                            if(result==true)
                                $("#myModal1").modal('show');
                            else
                                $("#myModal2").modal('show');
                        }
                    });
                });
            }
        </script>

        <script type="text/javascript">
            function modal()
            {
                $("#myModal").modal('show');
            }
        </script>
        <!--Actualiza la página-->
        <script type="text/javascript">
            $('#ver1').click(function(){
                document.location = 'listar_GN_CONCEPTO.php';
            });
        </script>

        <script type="text/javascript">    
            $('#ver2').click(function(){
                document.location = 'listar_GN_CONCEPTO.php';
            });    
        </script>
        
    </body>
</html>