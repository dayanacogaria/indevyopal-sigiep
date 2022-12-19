<?php
#Llamado a la cabeza
require_once './head.php';
#Llamado a la clase conexión
require_once './Conexion/conexion.php';
$compania = $_SESSION['compania'];
?>
        <!-- Link de css de la libreria css -->
        <link rel="stylesheet" href="css/select/select2.min.css">
        <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
        <!-- Titulo de la pagina -->
        <title>Listar Usuarios</title>
        <!-- Estilos -->
        <style type="text/css">            
            .cabeza{
                white-space:nowrap;
            }
        </style>
        <!-- Link de la libreria datatable -->
        <link rel="stylesheet" href="css/dataTables.jqueryui.min.css" type="text/css" media="screen" title="default" />
        <script src="js/jquery.dataTables.min.js" type="text/javascript"></script>
        <script src="js/dataTables.jqueryui.min.js" type="text/javascript"></script>
        <link rel="stylesheet" href="css/dataTables.jqueryui.min.css" type="text/css" media="screen" title="default" />
        <link rel="stylesheet" href="css/custom.css"/>
        <!-- Script para implementar diseño de tabla -->
        <script type="text/javascript">
            $(document).ready(function() {
               var i= 1;
                 $('#tabla thead th').each( function () {
                    if(i>1){ 
                    var title = $(this).text();
                    switch (i){                        
                        case 2:
                            $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                        break;
                        case 3:
                            $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                        break;
                        case 4:
                            $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                        break;
                        case 5:
                            $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                        break;
                        case 6:
                            $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                        break;
                        case 7:
                            $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                        break;
                        case 8:
                            $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
                        break;                                                
                    }
                    i = i+1;
                }else{
                  i = i+1;
                }
            });
            // DataTable
            var table = $('#tabla').DataTable({
                "autoFill": true,
                "scrollX": true,
                "pageLength": 5,
                  "language": {
                    "lengthMenu": "Mostrar _MENU_ registros",
                    "zeroRecords": "No Existen Registros...",
                    "info": "Página _PAGE_ de _PAGES_ ",
                    "infoEmpty": "No existen datos",
                    "infoFiltered": "(Filtrado de _MAX_ registros)",
                    "sInfo":"Mostrando _START_ - _END_ de _TOTAL_ registros","sInfoEmpty":"Mostrando 0 - 0 de 0 registros"
                  },
                  'columnDefs': [{
                   'targets': 0,
                   'searchable':false,
                   'orderable':false,
                   'className': 'dt-body-center'         
                }]
            });
            var i = 0;
                table.columns().every( function () {
                    var that = this;
                    if(i!=0){
                        $( 'input', this.header() ).on( 'keyup change', function () {
                            if ( that.search() !== this.value ) {
                                that
                                    .search( this.value )
                                    .draw();
                            }
                        } );
                        i = i+1;
                    }else{
                        i = i+1;
                    }
                });
             } );
        </script>
    </head>
    <body>
        <!-- Inicio de contenedor fluido -->
        <div class="container-fluid text-left">
            <!-- Inicio de contenido -->
            <div class="content row">
                <!-- Llamado al menú -->
                <?php require_once './menu.php'; ?>
                <!-- Inicio de contenedor primario -->
                <div class="col-sm-10">
                    <!-- Titulo del formulario -->
                    <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;margin-top:0px">Usuarios</h2>
                    <!-- Inicio de contenedor responsive -->
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;margin-top: -10px">
                        <!-- Inicio de contenedor responsive -->
                        <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                            <!-- Inicio de tabla -->
                            <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                                <!-- Inicio de cabeza de tabla -->
                                <thead>
                                    <!-- Inicio de campos de titulo -->
                                    <tr>
                                        <td class="cabeza" width="30px" align="center"></td>
                                        <td class="cabeza"><strong>Tercero</strong></td>
                                        <td class="cabeza"><strong>Usuario</strong></td>
                                        <td class="cabeza"><strong>Estado</strong></td>
                                        <td class="cabeza"><strong>Rol</strong></td> 
                                        <td class="cabeza"><strong>Fecha última actulización</strong></td>
                                        <td class="cabeza"><strong>Observaciones</strong></td>                                                                                                                       
                                        <!-- Cierre de campo de titulo -->
                                    </tr>
                                    <!-- Inicio de campo de filtrado -->
                                    <tr>
                                        <th class="cabeza"></th>
                                        <th class="cabeza">Tercero</th>
                                        <th class="cabeza">Usuario</th>
                                        <th class="cabeza">Estado</th>
                                        <th class="cabeza">Rol</th>
                                        <th class="cabeza">Fecha última actulización</th>
                                        <th class="cabeza">Observaciones</th>
                                        <!-- Cierre de campo de filtrado -->
                                    </tr>
                                    <!-- Cierre de cabeza de tabla -->
                                </thead>
                                <!-- Inicio de cuerpo de la tabla -->
                                <tbody>
                                    <?php
                                    $sql="  select  user.id_unico,
                                                    user.usuario,
                                                    md5(user.contrasen),
                                                    date_format(user.fechaactualizacion,'%d/%m/%Y'),
                                                    user.observaciones,
                                                    rol.nombre,
                                                    user.tercero,
                                                    est.nombre, 
                                                    IF(CONCAT_WS(' ',
                                                t.nombreuno,
                                                t.nombredos,
                                                t.apellidouno,
                                                t.apellidodos) 
                                                IS NULL OR CONCAT_WS(' ',
                                                t.nombreuno,
                                                t.nombredos,
                                                t.apellidouno,
                                                t.apellidodos) = '',
                                                (t.razonsocial),
                                                CONCAT_WS(' ',
                                                t.nombreuno,
                                                t.nombredos,
                                                t.apellidouno,
                                                t.apellidodos)) AS NOMBRE,
                                            t.id_unico, 
                                            IF(t.digitoverficacion IS NULL OR t.digitoverficacion='',
                                                t.numeroidentificacion, 
                                           CONCAT(t.numeroidentificacion, ' - ', t.digitoverficacion)) , 
                                           t.numeroidentificacion 
                                            from    gs_usuario user 
                                            left join gs_rol rol on user.rol=rol.id_unico 
                                            LEFT JOIN gf_tercero t ON user.tercero = t.id_unico 
                                            left join gs_estado_usuario est on user.estado=est.id_unico 
                                            WHERE t.compania = $compania";
                                    $result=$mysqli->query($sql);
                                    while($row= mysqli_fetch_row($result)){ 
                                        $m = 1;
                                        if($_SESSION['num_usuario']==900849655){    
                                        } else {
                                            if(900849655== $row[10]) {    
                                                $m = 0;
                                            } else { 
                                            }
                                        }
                                        if($m==1){ 
                                        ?>
                                    <tr>
                                        <td class="campos">
                                            <a href="#" class="campos" onclick="javascript:eliminar(<?php echo $row[0];?>);"><i title="Eliminar" class="glyphicon glyphicon-trash"></i></a>
                                            <a class="campos" href="modificar_GS_USUARIO.php?id=<?php echo md5($row[0]);?>"><i title="Modificar" class="glyphicon glyphicon-edit" ></i></a>
                                        </td>
                                        <td class="campos">
                                            <?php  
                                            echo ucwords(mb_strtolower($row[8]));
                                            ?>                                                    
                                        </td>
                                        <td class="campos">
                                            <?php echo $row[1]; ?>
                                        </td>
                                        <td class="campos">
                                            <?php echo ucwords(mb_strtolower($row[7])); ?>
                                        </td>
                                        <td class="campos">
                                            <?php echo ucwords(mb_strtolower($row[5])); ?>
                                        </td> 
                                        <td class="campos">
                                            <?php echo $row[3]; ?>
                                        </td>
                                        <td class="campos">
                                            <?php echo ucwords(mb_strtolower($row[4])); ?>
                                        </td>                                                                                                                       
                                    </tr>
                                    <?php } }  ?>
                                    <!-- Cierre de cuerpo de la tabla -->
                                </tbody>
                                <!-- Cierre de tabla -->
                            </table>
                            <!-- Cierre de contenedor responsive -->
                        </div>
                        <!-- Inicio de Botón de registro -->
                        <div align="right">
                            <a href="registrar_GS_USUARIO.php" class="btn btn-primary" style=" box-shadow: 0px 2px 5px 1px gray;color: #fff; border-color: #1075C1; margin-top:10px; margin-bottom: 20px; margin-left:-20px; margin-right:4px">Registrar Nuevo</a> 
                            <!-- Cierre de botón de registro -->
                        </div>
                    </div>
                    <!-- Cierre de contenedor primario -->                    
                </div>
                <!-- Cierre de contenido -->
            </div>
            <!-- Cierre de contenedor fluido -->            
        </div>
        <!-- Inicio de modales de eliminado -->
        <div class="modal fade" id="myModal" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <p>¿Desea eliminar el registro seleccionado de Usuario?</p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="ver" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
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
                        <p>Información eliminada correctamente</p>
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
                        <p>No se pudo eliminar la información, el registro seleccionado está siendo utilizado por otra dependencia.</p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="ver2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Fin de modales de eliminado -->        
        <!-- Link para estilo de tema de boostrap -->
        <link rel="stylesheet" href="css/bootstrap-theme.css"/>
        <!-- link para llamar al script de boostrap -->
        <script src="js/bootstrap.min.js"></script>
        <!-- Script para eliminar el usuario -->
        <script type="text/javascript">
            function eliminar(id){
                var result="";
                $("#myModal").modal('show');
                $("#ver").click(function(){
                    $("#myModal").modal('hide');
                    $.ajax({
                        type: 'GET',
                        url: "json/eliminarUsuarioJson.php?id="+id,                        
                        success: function (data, textStatus, jqXHR) {
                            if(result===true){
                                $("#myModal1").modal('show');
                            }else{
                                $("#myModal2").modal('show');
                            }
                        }
                    });
                });
            }
        </script>
        <!-- Inicio de footer -->
        <div>
            <?php require_once './footer.php'; ?>
            <!-- Cierre de footer -->
        </div>
    </body>
</html>