<?php

require_once './Conexion/conexion.php';
require_once './head_listar.php';

?>

<?php 

$id=$_GET["id"]; //ID del Contribuyente
$sql="SELECT c.id_unico,c.codigo_mat,c.codigo_mat_ant,
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
                                t.apellidodos)) AS NOMBRETERCERO ,
                                c.cod_postal,
                                c.repre_legal,
                                c.tercero,
                                t.numeroidentificacion,
                                IF(CONCAT_WS(' ',
                                ter.nombreuno,
                                ter.nombredos,
                                ter.apellidouno,
                                ter.apellidodos) 
                                IS NULL OR CONCAT_WS(' ',
                                ter.nombreuno,
                                ter.nombredos,
                                ter.apellidouno,
                                ter.apellidodos) = '',
                                (ter.razonsocial),
                                CONCAT_WS(' ',
                                ter.nombreuno,
                                ter.nombredos,
                                ter.apellidouno,
                                ter.apellidodos)) AS nombreRL,
                                ter.numeroidentificacion,
                                ter.id_unico,
                                c.estado,
                                ec.nombre,
                                c.fechainscripcion,
                                c.dir_correspondencia,
                                c.telefono
                            

        FROM gc_contribuyente c 
        LEFT JOIN gc_estado_contribuyente ec ON c.estado = ec.id_unico
        LEFT JOIN gf_tercero t ON t.id_unico=c.tercero
        LEFT JOIN gf_tercero ter ON ter.id_unico=c.repre_legal
        WHERE md5(c.id_unico) = '$id'"; 

$resultado  = $mysqli->query($sql);
$rowC = mysqli_fetch_row($resultado);

        ?>   


        <title>Modificar Contribuyente</title>
        <script src="dist/jquery.validate.js"></script>
        <!-- Librerias de carga para el datapicker -->
        <link rel="stylesheet" href="css/jquery-ui.css">
        <script src="js/jquery-ui.js"></script>
        <!-- select2 -->
        <link rel="stylesheet" href="css/select2.css">
        <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
        
        <style>
        /*Estilos tabla*/
        table.dataTable thead th,table.dataTable thead td{padding:1px 18px;font-size:10px}
        table.dataTable tbody td,table.dataTable tbody td{padding:1px}
        .dataTables_wrapper .ui-toolbar{padding:2px}
        /*Campos dinamicos*/
        .campoD:focus {
            border-color: #66afe9;
            outline: 0;            
            box-shadow: inset 0 1px 1px rgba(0,0,0,.075), 0 0 8px rgba(102, 175, 233, .6);            
        }
        .campoD:hover{
            cursor: pointer;
        }
        /*Campos dinamicos label*/
        .valorLabel{
            font-size: 10px;
        }
        .valorLabel:hover{
            cursor: pointer;
            color:#1155CC;
        }
        /*td de la tabla*/
        .campos{
            padding: 0px;
            font-size: 10px
        }
        /*cuerpo*/
        body{
            font-size: 10px;
            font-family: Arial;
        }

        .client-form input[type="text"]{
            width: 100%;
        }
        .client-form select{
            width: 100%;
        }

        .client-form input[type="file"]{
            width: 100%;
        }

        </style>  

    </head>
    <script type="text/javascript">
  $(document).ready(function() {
     var i= 1;
    $('#tabla1 thead th').each( function () {
        if(i != 1){ 
        var title = $(this).text();
        switch (i){
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
          case 6:
              $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
          break;
          case 7:
              $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
          break;
          case 8:
              $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
          break;
          case 9:
              $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
          break;
          case 10:
              $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
          break;
          case 11:
              $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
          break;
          case 12:
              $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
          break;
          case 13:
              $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
          break;
          case 14:
              $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
          break;
          case 15:
              $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
          break;
          case 16:
              $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
          break;
          case 17:
              $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
          break;
          case 18:
              $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
          break;
          case 19:
              $(this).html( '<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>' );
          break;
  
        }
        i = i+1;
      }else{
        i = i+1;
      }
    } );
 
    // DataTable
   var table = $('#tabla1').DataTable({
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
    } );
} );
</script>

<script>

    $(function(){
        var fecha = new Date();
        var dia = fecha.getDate();
        var mes = fecha.getMonth() + 1;
        if(dia < 10){
            dia = "0" + dia;
        }
        if(mes < 10){
            mes = "0" + mes;
        }
        var fecAct = dia + "/" + mes + "/" + fecha.getFullYear();
        $.datepicker.regional['es'] = {
            closeText: 'Cerrar',
            prevText: 'Anterior',
            nextText: 'Siguiente',
            currentText: 'Hoy',
            monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
            monthNamesShort: ['Enero','Febrero','Marzo','Abril', 'Mayo','Junio','Julio','Agosto','Septiembre', 'Octubre','Noviembre','Diciembre'],
            dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
            dayNamesShort: ['Dom','Lun','Mar','Mié','Juv','Vie','Sáb'],
            dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sá'],
            weekHeader: 'Sm',
            dateFormat: 'dd/mm/yy',
            firstDay: 1,
            isRTL: false,
            showMonthAfterYear: false,
            yearSuffix: '',
            changeYear: true
        };
        $.datepicker.setDefaults($.datepicker.regional['es']);

        $("#fechaInsc").datepicker({changeMonth: true,})
        $("#fechaini").datepicker({changeMonth: true,})
        $("#fechaCierre").datepicker({changeMonth: true});


    });
</script>



<body >   

<div class="container-fluid text-left">
   <div class="row content">
    <?php require_once ('menu.php'); ?>
    <div class="col-sm-8" style="margin-top:-22px;">
        <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;margin-bottom: 10px;">Modificar Contribuyente</h2>
        <!--Volver-->
        <a href="listar_GC_CONTRIBUYENTE.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:8px;margin-top: -5.5px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
        <h5 id="forma-titulo3a" align="center" style="width:95%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-10px;  background-color: #0e315a; color: white; border-radius: 5px;"><?php echo "Contribuyente: ".$rowC[7]." - ". ucwords(mb_strtolower($rowC[3])) ?></h5>
        <div class="client-form contenedorForma" style="margin-top:-7px;">
            <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="jsonComercio/modificarContribuyenteJson.php" style="margin-bottom:-45px">
                <p align="center" style="margin-bottom: 25px; margin-top: 15px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                <div class="form-group" style="margin-top: 5px; margin-left: 5px;">

                    <input type="hidden" name="id" value="<?php echo $rowC[0] ?>">

                    <label for="cact" class="control-label col-sm-1 col-md-1 col-lg-1" ><strong class="obligado">*</strong>Código Matrícula Actual:</label>
                    <div class="col-sm-2 col-md-2 col-lg-2">
                        <input  type="text" name="codigoActual" required="" id="cact" class="form-control" title="Código Actual" onkeypress="return txtValida(event,'num_car')" placeholder="Código Actual" value="<?php echo $rowC[1] ?>" maxlength="15">
                    </div>

                    <div class="col-sm-1 col-md-1 col-lg-1"></div>
                        
                    <label for="cante" class="control-label col-sm-1 col-md-1 col-lg-1">Código Matrícula Anterior:</label>
                    <div class="col-sm-2 col-md-2 col-lg-2">
                        <input   type="text" name="codigoAnterior" id="cante" class="form-control" title="Código Anterior" onkeypress="return txtValida(event,'num_car')" placeholder="Código Anterior" value="<?php echo $rowC[2] ?>" maxlength="15">
                    </div>

                    <label for="sltctai" class="col-sm-1 col-md-1 col-lg-1 control-label"><strong style="color:#03C1FB;">*</strong>Tercero:</label>
                    <div class="col-sm-2 col-md-2 col-lg-2">
                        <select name="tercero" id="sltctai" required  class="form-control select2" title="Seleccione Tercero">
                            <?php  
                                $idtercero=$rowC[6];
        
                                $consulta=" SELECT t.id_unico,
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
                                                        t.apellidodos)) AS nombreTerceroSeleccionado, 
                                                        t.numeroidentificacion
                                                        FROM gf_tercero t WHERE t.id_unico=$idtercero
                                                        ORDER BY nombreTerceroSeleccionado ASC";
                                       
                                $rr=$mysqli->query($consulta); 
                                $fa=mysqli_fetch_array($rr);
                            ?>

                            <option value="<?php echo $fa['id_unico']?>"><?php echo $fa['numeroidentificacion']." - ".ucwords(mb_strtolower($fa['nombreTerceroSeleccionado'] )) ?></option>
                                        
                            <?php
                                $cuentaI = "SELECT t.id_unico,
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
                                                        t.apellidodos)) AS NOMBRETERCERO, 
                                                        t.numeroidentificacion
                                                FROM gf_tercero t WHERE t.id_unico!=$idtercero 
                                                ORDER BY NOMBRETERCERO ASC ";
                                $rsctai = $mysqli->query($cuentaI);
                            ?>

                            <?php   while($row=mysqli_fetch_array($rsctai)){ ?>
                                        <option value="<?php echo $row['id_unico']?>"><?php echo $row['numeroidentificacion']." - ".ucwords(mb_strtolower($row['NOMBRETERCERO'] )) ?></option>
                            <?php   } ?>
                        </select>
                    </div>
                </div>

                <div class="form-group" style="margin-top: 5px; margin-left: 5px;">

                    <!--<label for="codp" class="control-label col-sm-2 col-md-2 col-lg-2">Código Postal:</label>
                    <input type="text" name="codigoPostal" id="codp" class="form-control" maxlength="15" title="Código Postal" onkeypress="return txtValida(event,'num_car')" placeholder="Código Postal" value="<?php echo $rowC[4] ?>" >-->

                    <label for="cact" class="control-label col-sm-1 col-md-1 col-lg-1" >Código Postal:</label>
                    <div class="col-sm-2 col-md-2 col-lg-2">
                        <input  type="text" name="codigoPostal"  id="cact" class="form-control" title="Código Postal" onkeypress="return txtValida(event,'num_car')" placeholder="Código Postal" value="<?php echo $rowC[4] ?>"  maxlength="15">
                    </div>

                    <label for="slaccom" class="col-sm-2 col-md-2 col-lg-2">Representante   Legal:</label>
                    <div class="col-sm-2 col-md-2 col-lg-2">
                        <select name="representanteLegal" id="slaccom" class="form-control select2" title="Seleccione Representante Legal" >
                                          
                            <?php
                                $idRL=$rowC[10];  

                                if($idRL!="") { 
                                    $cuentaI = "SELECT ter.id_unico,
                                                        IF(CONCAT_WS(' ',
                                                        ter.nombreuno,
                                                        ter.nombredos,
                                                        ter.apellidouno,
                                                        ter.apellidodos) 
                                                        IS NULL OR CONCAT_WS(' ',
                                                        ter.nombreuno,
                                                        ter.nombredos,
                                                        ter.apellidouno,
                                                        ter.apellidodos) = '',
                                                        (ter.razonsocial),
                                                        CONCAT_WS(' ',
                                                        ter.nombreuno,
                                                        ter.nombredos,
                                                        ter.apellidouno,
                                                        ter.apellidodos)) AS nombreRL,
                                                        ter.numeroidentificacion 
                                                        FROM gf_tercero ter WHERE ter.id_unico!=$idRL";
                                    $rsctai = $mysqli->query($cuentaI);
                            ?>
                                    <option value="<?php echo $rowC[10] ?>"><?php echo $rowC[9]." - ".ucwords(mb_strtolower($rowC[8])) ?></option>
                                    <?php 
                                        while($rowrl=mysqli_fetch_row($rsctai)){ ?>
                                                <option value="<?php echo $rowrl[0] ?>"><?php echo $rowrl[2]." - ".ucwords(mb_strtolower($rowrl[1])) ?></option>
                                    <?php 
                                        } 
                                }else{ ?>
                                    <option value="">Representante Legal</option>

                            <?php    
                                    $cuentaI = "SELECT ter.id_unico,
                                                                IF(CONCAT_WS(' ',
                                                                ter.nombreuno,
                                                                ter.nombredos,
                                                                ter.apellidouno,
                                                                ter.apellidodos) 
                                                                IS NULL OR CONCAT_WS(' ',
                                                                ter.nombreuno,
                                                                ter.nombredos,
                                                                ter.apellidouno,
                                                                ter.apellidodos) = '',
                                                                (ter.razonsocial),
                                                                CONCAT_WS(' ',
                                                                ter.nombreuno,
                                                                ter.nombredos,
                                                                ter.apellidouno,
                                                                ter.apellidodos)) AS nombreRL,
                                                                ter.numeroidentificacion 
                                                                FROM gf_tercero ter";
                                    $rsctai = $mysqli->query($cuentaI);
                                      
                                    while($rowrl=mysqli_fetch_row($rsctai)){ ?>
                                        <option value="<?php echo $rowrl[0] ?>"><?php echo $rowrl[2]." - ".ucwords(mb_strtolower($rowrl[1])) ?></option>
                            <?php 
                                    } 
                                } 
                            ?>
                        </select>
                    </div>

                    <?php 
                        if(empty($rowC[11])){
                            $estado = "SELECT id_unico , nombre FROM gc_estado_contribuyente ";
                            $est = "Estado";
                            $id_est = "";
                        }else{
                            $estado = "SELECT id_unico , nombre FROM gc_estado_contribuyente WHERE id_unico != $rowC[11] ";
                            $est = $rowC[12];
                            $id_est = $rowC[11];
                        }
                                    
                        $esta = $mysqli->query($estado);
                    ?>
                            
                    <label for="sltEst" class="control-label col-sm-1 col-md-1 col-lg-1 "><strong style="color:#03C1FB;">*</strong>Estado:</label>
                    <div class="col-sm-2 col-md-2 col-lg-2" >
                        <select name="sltEst" id="sltEst"  style="height: 30px" class="form-control select2 " title="Seleccione Estado" required>
                            <option value="<?php echo $id_est ?>"><?php echo $est ?></option>
                                
                            <?php 
                                while($EST=mysqli_fetch_row($esta)){ ?>
                                    <option value="<?php echo $EST[0] ?>"><?php echo $EST[1] ?></option>
                                                
                            <?php 
                                } 
                            ?>
                        </select>
                    </div>
                </div>

                <div class="form-group" style="margin-top: 5px; margin-left: 5px;">

                    <!--<label for="codp" class="control-label col-sm-2 col-md-2 col-lg-2">Código Postal:</label>
                    <input type="text" name="codigoPostal" id="codp" class="form-control" maxlength="15" title="Código Postal" onkeypress="return txtValida(event,'num_car')" placeholder="Código Postal" value="<?php echo $rowC[4] ?>" >-->
                    <script type="text/javascript">
                      $(document).ready(function() {
                        $("#datepicker").datepicker();
                      });
                    </script>
                    <?php
                        if(!empty($rowC[13])){
                            $fecha_div =  explode("-", $rowC[13]);
                            $anio1 = $fecha_div[0];
                            $mes1 = $fecha_div[1];
                            $dia1 = $fecha_div[2];
                            $fecha = ''.$dia1.'/'.$mes1.'/'.$anio1.'"'; 
                        }else{
                            $fecha = "";
                        }
                       
                    ?>
                    <label for="fechaInsc" class="control-label col-sm-1 col-md-1 col-lg-1" >Fecha Inscripción:</label>
                    <div class="col-sm-2 col-md-2 col-lg-2">
                        <input  type="text" name="fechaInsc"  id="fechaInsc" class="form-control" readonly title="Código Postal"  value="<?php echo $fecha ?>"  maxlength="15">
                    </div>

                    <label for="txtDirC" class="control-label col-sm-2 col-md-2 col-lg-2" >Dirección Correspondencia:</label>
                    <div class="col-sm-2 col-md-2 col-lg-2">
                        <input  type="text" name="txtDirC"  id="txtDirC" class="form-control" title="Código Postal"  value="<?php echo $rowC[14] ?>"  maxlength="15">
                    </div>
                    <label for="txtTelC" class="control-label col-sm-1 col-md-1 col-lg-1" >Teléfono:</label>
                    <div class="col-sm-2 col-md-2 col-lg-2">
                        <input  type="text" name="txtTelC"  id="txtTelC" class="form-control" title="Código Postal"  value="<?php echo $rowC[15] ?>"  maxlength="15">
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="col-sm-1 col-md-1 col-lg-1 col-sm-push-10  col-md-push-10  col-lg-push-10 text-right">
                        <button type="submit" id="btnGuardarDetalle" style="margin-top: -200px" class="btn btn-primary sombra"><li class="glyphicon glyphicon-floppy-disk"></li></button>                              
                    </div>
                </div>
            </form>
        </div>
    </div>


          <!--informacion adicional-->
         <div class="col-sm-3 col-sm-2 col-sm-offset-8" style="margin-top:-265px;">
            <table class="tablaC table-condensed text-center" align="center">
                <thead>
                    <tr>
                        <tr>                                        
                            <th>
                                <h2 class="titulo" align="center" style=" font-size:17px;">Información Adicional</h2>
                            </th>
                        </tr>
                    </tr>
                </thead>
                <tbody>

                <?php $idContribuyente=$_GET['id']; ?>
                    <tr>                                    
                        <td>
                          <!--<a href="registrar_GF_TERCERO.php" class="btn btn-primary btnInfo">TERCERO</a>!-->
                          <a href="#" class="btn btn-primary btnInfo" disabled>TERCERO</a>
                        </td>
                    </tr>
                    <tr>                                    
                        <td>
                            <!-- onclick="return ventanaSecundaria('registrar_GF_DESTINO.php')" -->
                            <a href="?id=<?php echo $idContribuyente."&ac" ?>" class="btn btn-primary btnInfo">ACTIVIDAD <br> CONTRIBUYENTE   </a>                                         
                        </td>
                    </tr>
                    <tr>                                    
                        <td>
                            <a class="btn btn-primary btnInfo" href="?id=<?php echo $idContribuyente."&e" ?>">ESTABLECIMIENTO</a>
                        </td>
                    </tr>                               
                    <tr>                                    
                        <td>
                            <a class="btn btn-primary btnInfo" href="?id=<?php echo $idContribuyente."&veh" ?>">VEHÍCULO</a>
                        </td>
                    </tr>
                    <tr>                                    
                        <td>
                            <a class="btn btn-primary btnInfo" href="?id=<?php echo $idContribuyente."&nc" ?>">NOVEDADES<br>COMERCIO</a>
                        </td>
                    </tr>
                     <tr>                                    
                        <td>
                            <a class="btn btn-primary btnInfo" href="?id=<?php echo $idContribuyente."&doc" ?>">DOCUMENTOS <br> CONTRIBUYENTE </a>
                        </td>
                    </tr>
                     <tr>                                    
                        <td>
                            <a class="btn btn-primary btnInfo" href="?id=<?php echo $idContribuyente."&mut" ?>">MUTACIONES</a>
                        </td>
                    </tr>
                    <tr>                                    
                        <td>
                            <a class="btn btn-primary btnInfo" href="listar_GC_DECLARACION_PRESENTADA.php?id=<?php echo md5($rowC[0]) ?>">DECLARACIONES<br>PRESENTADAS</a>
                        </td>
                    </tr>
           
                </tbody>
            </table>
        </div>

      <!---->
    <?php if(isset($_GET['ac'])){ ?>

<script>
$().ready(function() {
            var validator = $("#form2").validate({
                ignore: "",
                rules:{
                    sltTipoPredio:"required",
                    txtCodigo:"required"
                },
                messages:{
                    sltTipoPredio: "Seleccione Actividad Comercial",
                },
                errorElement:"em",
                errorPlacement: function(error, element){
                    error.addClass('help-block');
                },
                highlight: function(element, errorClass, validClass){
                    var elem = $(element);
                    if(elem.hasClass('select2-offscreen')){
                        $("#s2id_"+elem.attr("id")).addClass('has-error').removeClass('has-success');
                    }else{
                        $(elem).parents(".form-group").addClass("has-error").removeClass('has-success');                       
                    }
                    if($(element).attr('type') == 'radio'){
                        $(element.form).find("input[type=radio]").each(function(which){
                            $(element.form).find("label[for=" + this.id + "]").addClass("has-error");
                            $(this).addClass("has-error");
                        });
                    } else {
                        $(element.form).find("label[for=" + element.id + "]").addClass("has-error");
                        $(element).addClass("has-error");
                    }
                },
                unhighlight:function(element, errorClass, validClass){
                    var elem = $(element);
                    if(elem.hasClass('select2-offscreen')){
                        $("#s2id_"+elem.attr("id")).addClass('has-success').removeClass('has-error');
                    }else{
                        $(element).parents(".form-group").addClass('has-success').removeClass('has-error');                        
                    }
                    if($(element).attr('type') == 'radio'){
                        $(element.form).find("input[type=radio]").each(function(which){
                            $(element.form).find("label[for=" + this.id + "]").addClass("has-success").removeClass("has-error");
                            $(this).addClass("has-success").removeClass("has-error");
                        });
                    } else {
                        $(element.form).find("label[for=" + element.id + "]").addClass("has-success").removeClass("has-error");
                        $(element).addClass("has-success").removeClass("has-error");
                    }
                }
            });
            $(".cancel").click(function() {
                validator.resetForm();
            });
        });
</script>


      <!--2do formulario Actividad contribuyente -->
      <div class="col-sm-8 text-center " style="margin-top:-9.8%;" align="">                    
          <div class="client-form" style="" class="col-sm-12">
              <form id="form2" name="form2" class="form-inline" method="POST"  enctype="multipart/form-data" action="jsonComercio/registrarActividadesContribuyenteJson.php" style="margin-top:-45px" >
                   <h5 id="forma-titulo3a" align="center" style="width:100%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:5px;  background-color: #0e315a; color: white; border-radius: 5px;"><?php echo "Registrar Actividad Comercial";?></h5>

                      <input type="hidden" name="contribuyente" value="<?php echo $idContribuyente ?>" required>
                       <?php
                        $cuentaI = "SELECT acom.id_unico,acom.codigo,acom.descripcion 

                         FROM gc_actividad_comercial acom ORDER BY  acom.codigo ";
                        $rsctai = $mysqli->query($cuentaI);
                        ?>
                       <div style="margin-top: 25px;">
                         <div class="form-group" style="width: 35%;">
                            <label for="sacom" class="control-label col-sm-1 col-md-1 col-lg-1" style="padding-right: 23%;">
                                <strong class="obligado">*</strong>Actividad Comercial:
                            </label>
                            <select name="actividadComercial" id="sacom" class="form-control  select2"  style="widht: 20px;" title="Seleccione Actividad Comercial" required title="Actividad Comercial" >
                                    <option value="">Actividad Comercial</option>
                                        <?php while($row=mysqli_fetch_row($rsctai)){ ?> 
                                             <option value="<?php echo $row[0] ?>"><?php echo $row[1]." - ".$row[2] ?></option>
                                        <?php } ?>
                            </select>
                          </div>

                           <div class="form-group" >
                            <label style="padding-right: 26%;" for="fechaini" type = "date" class="control-label col-sm-2 col-md-2 col-lg-2"><strong class="obligado">*</strong>Fecha Inicio:</label>

                            <input readonly="readonly " type="text" name="fechaInicio" onkeypress="return justNumbers(event);" id="fechaini"  class="form-control" style="height:30px;width:100px" required="" />
                          </div>

                          <div class="form-group" style="margin-left: 10px;">
                            <label for="fechaCierre" type = "date" class="col-sm-1 col-md-1 col-lg-1" style="padding-right: 26%;">Fecha Cierre:</label>

                            <input readonly="readonly " type="text" name="fechaCierre" onkeypress="return justNumbers(event);" id="fechaCierre"  class="form-control" style="height:30px;width:100px;"  />
                          </div>
                          <div class="form-group" style="margin-top: 10px;margin-left: -20px;">
                              <button type="submit" id="btnGuardarDetalle" class="btn btn-primary sombra"><li class="glyphicon glyphicon-floppy-disk"></li></button>                                
                              <input type="hidden" name="MM_insert" >
                             
                          </div>  
                        </div>
              </form>                        
          </div>
              

                <!--listado actividad contribuyente-->
    
        <h5 id="forma-titulo3a" align="center" style="width:100%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:35px;  background-color: #0e315a; color: white; border-radius: 5px;"><?php echo "Listado de Actividades";?></h5>
          <?php 
          if(!empty($idComprobanteI)){
              $numero = $idComprobanteI;
              $result = "";
              $sql = "SELECT DISTINCT dtc.id_unico, 
                                      ct.id_unico, 
                                      ct.nombre, 
                                      rb.id_unico rubro, 
                                      rb.codi_presupuesto, 
                                      rb.nombre, 
                                      cnt.id_unico cuenta, 
                                      cnt.codi_cuenta, 
                                      cnt.nombre, 
                                      dtc.naturaleza, 
                                      dtc.valor, 
                                      pr.id_unico proyecto, 
                                      pr.nombre, 
                                      ctr.id_unico centroc, 
                                      ctr.nombre, 
                                      dtc.tercero,
                                      pptal.id_unico,
                                      ft.nombre,
                                      pptal.id_unico
                      FROM gf_detalle_comprobante dtc 
                      LEFT JOIN gf_detalle_comprobante_pptal pptal ON dtc.detallecomprobantepptal = pptal.id_unico 
                      LEFT JOIN gf_concepto_rubro cnr ON pptal.conceptoRubro = cnr.id_unico 
                      LEFT JOIN gf_concepto ct ON cnr.concepto = ct.id_unico 
                      LEFT JOIN gf_rubro_fuente rbf ON rbf.rubro = cnr.rubro 
                      LEFT JOIN gf_rubro_pptal rb ON rbf.rubro = rb.id_unico 
                      LEFT JOIN gf_fuente ft ON rbf.fuente = ft.id_unico 
                      LEFT JOIN gf_concepto_rubro_cuenta ctrb ON cnr.id_unico = ctrb.concepto_rubro 
                      LEFT JOIN gf_cuenta cnt ON dtc.cuenta = cnt.id_unico 
                      LEFT JOIN gf_proyecto pr ON dtc.proyecto = pr.id_unico 
                      LEFT JOIN gf_centro_costo ctr ON dtc.centrocosto = ctr.id_unico 
                      LEFT JOIN gf_tercero ter ON dtc.tercero = ter.id_unico 
                      WHERE dtc.comprobante = $idComprobanteI ";
              $result = $mysqli->query($sql);
          }                    
          ?>
          <input type="hidden" id="idPrevio" value="">
          <input type="hidden" id="idActual" value="">

          <?php 
            $sqlAC= "SELECT acont.id_unico,
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
                            t.apellidodos)) AS NOMBRETERCEROCONTRIBUYENTE, 
                            acom.cod_ciiu,acom.descripcion, DATE_FORMAT(acont.fechainicio,'%d-%m-%Y') AS fechaInicio, DATE_FORMAT(acont.fechacierre,'%d-%m-%Y') AS fechaCierre 
                    FROM gc_actividad_contribuyente acont 
                    LEFT JOIN gc_contribuyente c ON c.id_unico=acont.contribuyente
                    LEFT JOIN gf_tercero t ON t.id_unico=c.tercero
                    LEFT JOIN gc_actividad_comercial acom ON acom.id_unico=acont.actividad
                     WHERE md5(acont.contribuyente)='$id'";

                    $resultadoAC=$mysqli->query($sqlAC);



           ?>

                <div class="table-responsive contTabla" >
                    <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                        <thead>    
                            <tr>
                                <td class="oculto" >Identificador</td>                            
                                <td width="7%" class="cabeza"></td>
                                <td class="cabeza"><strong>Actividad Comercial</strong></td>
                                <td class="cabeza"><strong>Fecha Inicio</strong></td>
                                <td class="cabeza"><strong>Fecha Cierre</strong></td>
                            </tr>
                            <tr>
                                <th class="oculto">Identificador</th>                                    
                                <th width="7%" class="cabeza"></th>
                                <th class="cabeza">Actividad Comercial</th>
                                <th class="cabeza">Fecha Inicio</th>
                                <th class="cabeza">Fecha Cierre</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = mysqli_fetch_row($resultadoAC)){ ?>
                                <tr>
                                    <td style="display: none;"><?php echo $row[0]?></td>
                                    <td>
                                        <a href="#" onclick="javascript:eliminarac(<?php echo $row[0];?>);">
                                            <i title="Eliminar" class="glyphicon glyphicon-trash"></i>
                                        </a>     
                                         <a title="Modificar" style="text-decoration: none" class="glyphicon glyphicon-edit" onclick="javascript:open_modal_ac(<?php echo $row[0] ?>)"></a>
                                    </td>
                                    <td class="campos"><?php echo $row[2]." - ".$row[3]?></td> 
                                    <td class="campos"><?php echo $row[4]?></td>
                                    <td class="campos"><?php echo $row[5]?></td>                            

                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                <!--modales de eliminacion-->
                <div class="modal fade" id="myModal" role="dialog" align="center" >
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                      <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                      <p>¿Desea eliminar el registro seleccionado de Actividad Contribuyente?</p>
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
                
                <?php require 'GC_ACTIVIDAD_CONTRIBUYENTE_MODAL.php' ; ?>

                <!--Scrip que envia los datos para la eliminación-->

                <script type="text/javascript">
                      function eliminarac(id)
                      {
                         var result = '';
                         $("#myModal").modal('show');
                         $("#ver").click(function(){
                              $("#mymodal").modal('hide');
                              $.ajax({
                                  type:"GET",
                                  url:"jsonComercio/eliminarActividadContribuyenteJson.php?id="+id,
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

                        function open_modal_ac(id){  

                              var form_data={                            
                                id:id 
                              };
                              $.ajax({
                                  type:"POST",
                                  url: "GC_ACTIVIDAD_CONTRIBUYENTE_MODAL.php#mdlModificar",
                                  data:form_data,
                                  success: function (data) { 
                                    $("#mdlModificar").html(data);
                                    $(".modald").modal('show');
                                 }
                             })  
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
                        document.location = 'modificar_GC_CONTRIBUYENTE.php.php';
                      });
                    
                  </script>

                  <script type="text/javascript">    
                      $('#ver2').click(function(){
                        document.location = 'modificar_GC_CONTRIBUYENTE.php.php';
                      });    
                  </script>




          <script type="text/javascript" >
            function abrirdetalleMov(id,valor){                                                                                                   
              var form_data={                            
                id:id,
                valor:valor                          
              };
              $.ajax({
                type: 'POST',
                  url: "registrar_GF_DETALLE_COMPROBANTE_MOVIMIENTO_2.php#mdlDetalleMovimiento",
                  data:form_data,
                  success: function (data) { 
                    $("#mdlDetalleMovimiento").html(data);
                    $(".mov").modal('show');
                  }
              });
            }
            function abrirdetalleMov1(id,valor){                                                                                                   
              var form_data={                            
                id:id,
                valor:valor                          
              };
              $.ajax({
                type: 'POST',
                  url: "registrar_GF_DETALLE_COMPROBANTE_MOVIMIENTO.php#mdlDetalleMovimiento",
                  data:form_data,
                  success: function (data) { 
                    $("#mdlDetalleMovimiento").html(data);
                    $(".mov").modal('show');
                  }
              });
            }                                                                                          
          </script>

          <style>
              .valores:hover{
                  cursor: pointer;
                  color:#1155CC;
              }
          </style>
          <div class="container">

          </div>

          <script>
            $("#btnGuardarM").click(function(){
                if($("#sltBanco").val()==""){
                    $("#mdlBanco").modal('show');
                }else{
                    var form_data = {
                        banco:$("#sltBanco").val(),
                        fecha:$("#fecha").val(),
                        descripcion:$("#txtDescripcion").val(),
                        valorEjecucion:'0',
                        comprobante:$("#id").val(),
                        tercero:$("#slttercero").val(),
                        proyecto:$("#sltproyecto").val(),
                        centro:$("#sltcentroc").val()
                    };
                    var result = '';
                    
                    $.ajax({
                        type: 'POST',
                        url: "consultarComprobanteIngreso/GuardarBanco.php",
                        data:form_data,
                        success: function (data) {
                            result = JSON.parse(data);
                            console.log(data);
                            if(result==true){
                                $("#guardado").modal('show');
                            }else{
                                $("#noguardado").modal('show');
                            }
                        }
                    });
                }                                                                                                
            });                        
          </script> 
      </div>

         
    <?php }else{ ?>
          <!---->
              <?php if (isset($_GET['e'])){ ?>
<script>
 $().ready(function() {
            var validator = $("#form3").validate({
                ignore: "",
                rules:{
                    sltTipoPredio:"required",
                    txtCodigo:"required"
                },
                messages:{
                    sltTipoPredio: "Seleccione Actividad Comercial",
                },
                errorElement:"em",
                errorPlacement: function(error, element){
                    error.addClass('help-block');
                },
                highlight: function(element, errorClass, validClass){
                    var elem = $(element);
                    if(elem.hasClass('select2-offscreen')){
                        $("#s2id_"+elem.attr("id")).addClass('has-error').removeClass('has-success');
                    }else{
                        $(elem).parents(".form-group").addClass("has-error").removeClass('has-success');          

                    }
                    if($(element).attr('type') == 'radio'){
                        $(element.form).find("input[type=radio]").each(function(which){
                            $(element.form).find("label[for=" + this.id + "]").addClass("has-error");
                            $(this).addClass("has-error");
                        });
                    } else {
                        $(element.form).find("label[for=" + element.id + "]").addClass("has-error");
                        $(element).addClass("has-error");
                    }
                },
                unhighlight:function(element, errorClass, validClass){
                    var elem = $(element);
                    if(elem.hasClass('select2-offscreen')){
                        $("#s2id_"+elem.attr("id")).addClass('has-success').removeClass('has-error');
                    }else{
                        $(element).parents(".form-group").addClass('has-success').removeClass('has-error');   

                    }
                    if($(element).attr('type') == 'radio'){
                        $(element.form).find("input[type=radio]").each(function(which){
                            $(element.form).find("label[for=" + this.id + "]").addClass("has-success").removeClass("has-error");
                            $(this).addClass("has-success").removeClass("has-error");
                        });
                    } else {
                        $(element.form).find("label[for=" + element.id + "]").addClass("has-success").removeClass("has-error");
                        $(element).addClass("has-success").removeClass("has-error");
                    }
                }
            });
            $(".cancel").click(function() {
                validator.resetForm();
            });
        }); 

</script>   
                     <!--2do formulario Establecimiento -->

 <div class="col-sm-8 col-md-8 col-lg-8 client-form" style="margin-top:-13.8%;margin-right: 10px;margin-left: 1%;padding: 5px 5px 5px 5px;">   

 <form name="form" method="POST"  enctype="multipart/form-data" action="jsonComercio/registrarEstablecimientosJson.php" class="form-inline" style="margin: 5px" id="form3">
 <h5 id="forma-titulo3a" align="center" style="width:100%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:5px;  background-color: #0e315a; color: white; border-radius: 5px;"><?php echo "Registrar Establecimiento";?></h5>

                             <div style="margin-top: 25px;">    
                                 <div class="form-group" style="margin-top: 0.5%;">
                                          <label for="cante" class="control-label col-sm-1 col-md-1 col-lg-1" style="margin-right: 27%;">
                                                  Nombre:
                                          </label>
                                            <input  style="width: 57%;" type="text" name="nombre" id="cante" class="form-control" title="Ingrese Nombre" onkeypress="return txtValida(event,'car')" placeholder="Nombre">
                                  </div>
                                  <div class="form-group" style="margin-top: -0.5%;">
                                          <label for="fechaini" type = "date" class="control-label col-sm-1 col-md-1 col-lg-1" style="margin-right: 20.5%;"><strong class="obligado">*</strong>Fecha <span style="padding-left: 11px">Inicio:</span></label>
                                          <input style="width: 40%;"readonly="readonly " type="text" name="fecha" onkeypress="return justNumbers(event);" id="fechaini"  required="" />
                                  </div>
                                          <input type="hidden" name="contribuyente" value="<?php echo $idContribuyente ?>">

                                           <?php
                                            $cuentaI = "SELECT * FROM gc_estrato e ORDER BY codigo ASC";
                                            $rsctai = $mysqli->query($cuentaI);
                                            ?>

                                          <div class="form-group" style="margin-top: -0.5%;">
                                             <label style="margin-left: -65%;" for="slestrato" class="col-sm-1 col-md-1 col-lg-1">
                                                  <strong class="obligado">*</strong>Estrato:
                                             </label>
                                             <select name="estrato" id="slestrato" class="select2 form-control" title="Seleccione Estrato" required style="width: 123px">
                                                    <option value="">Estrato</option>
                                                        <?php while($row=mysqli_fetch_row($rsctai)){ ?>
                                                             <option value="<?php echo $row[0] ?>"><?php echo $row[1].'-'.$row[2] ?></option>
                                                        <?php } ?>
                                             </select>

                                          </div>
                                  <div class="form-group" style="margin-top: 0.5%;">
                                     <label for="cante" class="col-sm-1 col-md-1 col-lg-1" style="margin-right:30%">
                                              Dirección:
                                     </label>
                                     <input   type="text" name="direccion" id="cante" class="form-control" maxlength="50" title="Dirección"  placeholder="Dirección" style="width: 60%;height:30px;">
                                  </div>

                               <!--2da--> 
  
                                  <div class="form-group" style="margin-top: 0.5%;">
                                             <label for="cante" class="col-sm-1 col-md-1 col-lg-1" style="margin-right: 18%;" >
                                                  Código Catastral:
                                             </label>
                                           <div class="col-sm-2 col-md-2 col-lg-2">
                                             <input   type="text" name="codigo" id="cante" class="form-control" maxlength="15" title="Código Catastral" onkeypress="return txtValida(event,'num_car')" placeholder="Código Catastral" style="width: 128px;">
                                           </div>

                                  </div>
                                      <input type="hidden" name="contribuyente" value="<?php echo $idContribuyente ?>">

                                       <?php
                                        $sc = "SELECT * FROM gf_ciudad c ORDER BY nombre ASC";
                                        $rc = $mysqli->query($sc);
                                        ?>
                                      <div class="form-group" style="margin-top: -0.5%">
                                          <label for="sltRubroFuente" class="col-sm-1 col-md-1 col-lg-1" style="margin-left: -24%;margin-right: 29.5%;">
                                              <strong class="obligado">*</strong>Ciudad:
                                          </label>  
                                          <select name="ciudad" id="sltRubroFuente" class="select2"  title="Seleccione Ciudad" required style="width: 123px">
                                                <option value="">Ciudad</option>
                                     
                                                    <?php while($rowCiu=mysqli_fetch_row($rc)){ ?>
                                                              <option value="<?php echo $rowCiu[0] ?>"><?php echo $rowCiu[1] ?></option>
                                                       
                                                    <?php } ?>
                                           </select>
                                      </div>
                                      <input type="hidden" name="contribuyente" value="<?php echo $idContribuyente ?>">

                                       
                                       <?php
                                        $sb = "SELECT * FROM gp_barrio b ORDER BY nombre ASC";
                                        $rb = $mysqli->query($sb);
                                        ?>
                                        <div class="form-group">
                                          <label class="col-sm-1 col-md-1 col-lg-1" style="margin-left: 4%;">
                                              Barrio:
                                          </label>
                                          <select name="barrio" id="sltRubroFuente" class="select2 form-control" title="Seleccione Barrio" style="width: 302px;margin-left: 27%;">
                                                <option value="">Barrio</option>
                                     
                                                    <?php while($rowB=mysqli_fetch_row($rb)){ ?>
                                                           <option value="<?php echo $rowB[0] ?>"><?php echo $rowB[1] ?></option>
                                                    <?php } ?>
                                            </select>
                                        </div>
                                      <input type="hidden" name="contribuyente" value="<?php echo $idContribuyente ?>">


                                  



                                <!--<label for="codp" class="control-label col-sm-2 col-md-2 col-lg-2">
                                        Código Postal:
                                </label>
                                <input type="text" name="codigoPostal" id="codp" class="form-control" maxlength="15" title="Código Postal" onkeypress="return txtValida(event,'num_car')" placeholder="Código Postal" value="<?php echo $rowC[4] ?>" >-->
                              <div class="form-group">
                                <label for="cact" class="control-label col-sm-1 col-md-1 col-lg-1" style="margin-right: 21%;" >
                                     <span style="margin-right: 40%">Tipo</span> Entidad:
                                </label>
                                <div class="col-sm-2 col-md-2 col-lg-2" style="margin-top: 0.9%;">
                                       <?php
                                        $ste = "SELECT * FROM gf_tipo_entidad tn ORDER BY nombre ASC";
                                        $rte = $mysqli->query($ste);
                                        ?>

                                          <select style="    width: 127px;" name="tipoEntidad" id="sltRubroFuente" class="select2"  title="Seleccione Tipo Entidad" style="width: 123px">
                                              <option value="">Tipo Entidad</option>
                                                  <?php while($rowte=mysqli_fetch_row($rte)){ ?>
                                                     <option value="<?php echo $rowte[0] ?>"><?php echo $rowte[1] ?></option>
                                                  <?php } ?>
                                          </select>

                                </div>
                              </div>
                              <div class="form-group" style="margin-top: 0.9%;">
                                <label for="cact" class="control-label col-sm-1 col-md-1 col-lg-1" style="margin-right: 20%;
    margin-left: -6%;">
                                     Tamaño Entidad:
                                </label>
                                <div class="col-sm-3 col-md-3 col-lg-3" style="padding-left: 0.5%;">
                                       <?php
                                        $stee = "SELECT * FROM gc_tamanno_entidad te ORDER BY nombre ASC";
                                        $rteee = $mysqli->query($stee);
                                        ?>

                                          <select name="tamannoEntidad" id="sltRubroFuente" class="select2 form-control" title="Seleccione Tamaño Entidad" style="width: 123px">
                                                <option value="">Tamaño Entidad</option>
                                     
                                                    <?php while($rowtea=mysqli_fetch_row($rteee)){ ?>
                                                          <option value="<?php echo $rowtea[0] ?>"><?php echo $rowtea[1] ?></option>
                                                    <?php } ?>
                                            </select>

                                </div>
                                     </div>
                                <div class="form-group" style="margin-top: 0.9%;">
                                 <label for="cact" class="col-sm-1 col-md-1 col-lg-1" style="margin-right: 28.5%;
    margin-left: -22%;">
                                       Localización:
                                  </label>
                                  <div class="col-sm-2 col-md-2 col-lg-2" style="margin-left: 3%;">
     
                                         <?php
                                          $stee = "SELECT * FROM gc_tamanno_entidad te ORDER BY nombre ASC";
                                          $rteee = $mysqli->query($stee);
                                          ?>

                                            <select name="tamannoEntidad" id="sltRubroFuente" class="select2 form-control"  title="Seleccione Tamaño Entidad" style="width: 123px">
                                                  <option value="">Localización</option>
                                       
                                                      <?php while($rowtea=mysqli_fetch_row($rteee)){ ?>
                                                            <option value="<?php echo $rowtea[0] ?>"><?php echo $rowtea[1] ?></option>
                                                      <?php } ?>
                                              </select>

                                  </div>
                                </div>
                                   <div class="col-sm-1 form-group" style="    margin-top: -3%;margin-left: 75%;">
                                      <button type="submit" id="btnGuardarDetalle" class="btn btn-primary sombra"><li class="glyphicon glyphicon-floppy-disk"></li></button>                                
                                      <input type="hidden" name="MM_insert" >
                                     
                                   </div> 
                         
                     </div>       
                            
    </form>  

            <!--listado establecimiento contribuyente-->

                  
                    
                       <h5 id="forma-titulo3a" align="center" style="width:100%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:35px;  background-color: #0e315a; color: white; border-radius: 5px;"><?php echo "Listado de Establecimientos";?></h5>
                          

                          <?php 
                                 $sqlE = "SELECT e.id_unico,
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
                                        t.apellidodos)) AS NOMBRETERCEROCONTRIBUYENTE, 
                                        e.nombre,
                                        DATE_FORMAT(e.fechainicioAct,'%d-%m-%Y') AS fechaFacConvertida,
                                        est.nombre,
                                        e.direccion,
                                        e.cod_catastral,
                                        ciu.nombre,
                                        b.nombre,
                                        l.nombre,
                                        te.nombre,
                                        tame.nombre
                                         
                                FROM gc_establecimiento e
                                LEFT JOIN gc_contribuyente c ON c.id_unico=e.contribuyente
                                LEFT JOIN gf_tercero t ON t.id_unico=c.tercero
                                LEFT JOIN gc_estrato est ON est.id_unico=e.estrato
                                LEFT JOIN gf_ciudad ciu ON ciu.id_unico=e.ciudad
                                LEFT JOIN gp_barrio b ON b.id_unico=e.barrio
                                LEFT JOIN gc_localizacion l ON l.id_unico=e.localizacion
                                LEFT JOIN gf_tipo_entidad te ON te.id_unico=e.tipo_entidad
                                LEFT JOIN gc_tamanno_entidad tame ON tame.id_unico=e.tamanno_entidad
                                WHERE md5(e.contribuyente)='$id'";

                                $resultadoE=$mysqli->query($sqlE);



                           ?>

                                <div class="table-responsive contTabla" >
                                <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <td style="display: none;">Identificador</td>
                                        <td width="7%" class="cabeza"></td>
                                        <td class="cabeza"><strong>Nombre</strong></td>
                                        <td class="cabeza"><strong>Fecha Inicial</strong></td>
                                        <td class="cabeza"><strong>Estrato</strong></td>
                                        <td class="cabeza"><strong>Dirección</strong></td>
                                        <td class="cabeza"><strong>Código Catastral</strong></td>
                                        <td class="cabeza"><strong>Ciudad</strong></td>
                                        <td class="cabeza"><strong>Barrio</strong></td>
                                        <td class="cabeza"><strong>Localización</strong></td>
                                        <td class="cabeza"><strong>Tipo Entidad</strong></td>
                                        <td class="cabeza"><strong>Tamaño Entidad</strong></td>
                          

                                    </tr>
                                    <tr>
                                        <th class="cabeza" style="display: none;">Identificador</th>
                                        <th width="7%"></th>
                                        <th class="cabeza">nombre</th>
                                        <th class="cabeza">Fecha Inicial</th>
                                        <th class="cabeza">Estrato</th>
                                        <th class="cabeza">Dirección</th>
                                        <th class="cabeza">Código Catastral</th>
                                        <th class="cabeza">Ciudad</th>
                                        <th class="cabeza">Barrio</th>
                                        <th class="cabeza">Localización</th>
                                        <th class="cabeza">Tipo Entidad</th>
                                        <th class="cabeza">Tamaño Entidad</th>
                     
        
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    while ($row = mysqli_fetch_row($resultadoE)) { 
                                    $tid    = $row[0];
                                    $tnom   = $row[1];
                                    $tanio  = $row[2];
                                    $tctar  = $row[3];
                                    $tlimi  = $row[4];
                                    $tlims  = $row[5];
                                    $tpori  = $row[6];
                                    $tval   = $row[7];
                                    $tpors  = $row[8];
                                    $tporia = $row[9];
                                    $tbasei = $row[10];
                                    $tbasea = $row[11];
                                
                                    ?>
                                     <tr>
                                        <td style="display: none;"><?php echo $row[0]?></td>
                                        <td>
                                            <a href="#" onclick="javascript:eliminare(<?php echo $row[0];?>);">
                                                <i title="Eliminar" class="glyphicon glyphicon-trash"></i>
                                            </a>
                                            <a title="Modificar" style="text-decoration: none" class="glyphicon glyphicon-edit" onclick="javascript:open_modal_e(<?php echo $row[0] ?>)"></a>
                                        </td>
               
                                        <td class="campos"><?php echo $tanio ?></td>                
                                        <td class="campos"><?php echo $tctar ?></td>                
                                        <td class="campos"><?php echo $tlimi ?></td>                
                                        <td class="campos"><?php echo $tlims ?></td>                
                                        <td class="campos"><?php echo $tpori ?></td>                
                                        <td class="campos"><?php echo $tval  ?></td>                
                                        <td class="campos"><?php echo $tpors ?></td>                
                                        <td class="campos"><?php echo $tporia?></td>                
                                        <td class="campos"><?php echo $tbasei?></td>                
                                        <td class="campos"><?php echo $tbasea?></td>                
                                    
                                
                                    </tr>
                                    <?php }
                                    ?>
                                </tbody>
                            </table>
                          </div>

           <!--modales de eliminacion-->
                <div class="modal fade" id="myModal" role="dialog" align="center" >
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                      <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                      <p>¿Desea eliminar el registro seleccionado de Establecimiento?</p>
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
                
                <?php require 'GC_ESTABLECIMIENTO_MODAL.php' ; ?>
                <!--Script que envia los datos para la eliminación-->
                <script type="text/javascript">
                      function eliminare(id)
                      {
                         var result = '';
                         $("#myModal").modal('show');
                         $("#ver").click(function(){
                              $("#mymodal").modal('hide');
                              $.ajax({
                                  type:"GET",
                                  url:"jsonComercio/eliminarEstablecimientosJson.php?id="+id,
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

                       function open_modal_e(id){  
                              var form_data={                            
                                id:id 
                              };
                              $.ajax({
                                  type:"POST",
                                  url: "GC_ESTABLECIMIENTO_MODAL.php#mdlModificar",
                                  data:form_data,
                                  success: function (data) { 
                                    $("#mdlModificar").html(data);
                                    $(".modale").modal('show');
                                 }
                             })  
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
                        document.location = 'modificar_GC_CONTRIBUYENTE.php.php';
                      });
                    
                  </script>

                  <script type="text/javascript">    
                      $('#ver2').click(function(){
                        document.location = 'modificar_GC_CONTRIBUYENTE.php.php';
                      });    
                  </script>

                          <script type="text/javascript" >
                            function abrirdetalleMov(id,valor){                                                                                                   
                              var form_data={                            
                                id:id,
                                valor:valor                          
                              };
                              $.ajax({
                                type: 'POST',
                                  url: "registrar_GF_DETALLE_COMPROBANTE_MOVIMIENTO_2.php#mdlDetalleMovimiento",
                                  data:form_data,
                                  success: function (data) { 
                                    $("#mdlDetalleMovimiento").html(data);
                                    $(".mov").modal('show');
                                  }
                              });
                            }
                            function abrirdetalleMov1(id,valor){                                                                                                   
                              var form_data={                            
                                id:id,
                                valor:valor                          
                              };
                              $.ajax({
                                type: 'POST',
                                  url: "registrar_GF_DETALLE_COMPROBANTE_MOVIMIENTO.php#mdlDetalleMovimiento",
                                  data:form_data,
                                  success: function (data) { 
                                    $("#mdlDetalleMovimiento").html(data);
                                    $(".mov").modal('show');
                                  }
                              });
                            }                                                                                          
                          </script>

                          <style>
                              .valores:hover{
                                  cursor: pointer;
                                  color:#1155CC;
                              }
                          </style>
                          <div class="container">

                          </div>

                          <script>
                            $("#btnGuardarM").click(function(){
                                if($("#sltBanco").val()==""){
                                    $("#mdlBanco").modal('show');
                                }else{
                                    var form_data = {
                                        banco:$("#sltBanco").val(),
                                        fecha:$("#fecha").val(),
                                        descripcion:$("#txtDescripcion").val(),
                                        valor:<?php echo $w; ?>,
                                        valorEjecucion:'0',
                                        comprobante:$("#id").val(),
                                        tercero:$("#slttercero").val(),
                                        proyecto:$("#sltproyecto").val(),
                                        centro:$("#sltcentroc").val()
                                    };
                                    var result = '';
                                    
                                    $.ajax({
                                        type: 'POST',
                                        url: "consultarComprobanteIngreso/GuardarBanco.php",
                                        data:form_data,
                                        success: function (data) {
                                            result = JSON.parse(data);
                                            console.log(data);
                                            if(result==true){
                                                $("#guardado").modal('show');
                                            }else{
                                                $("#noguardado").modal('show');
                                            }
                                        }
                                    });
                                }                                                                                                
                            });                        
                          </script>
                             
                      </div>


              <?php } else{ ?>

                  <?php if(isset($_GET['veh'])){ ?>

                      <script src="dist/jquery.validate.js"></script>

  



<script>
 $().ready(function() {
            var validator = $("#formv").validate({
                ignore: "",

                errorElement:"em",
                errorPlacement: function(error, element){
                    error.addClass('help-block');
                },
                highlight: function(element, errorClass, validClass){
                    var elem = $(element);
                    if(elem.hasClass('select2-offscreen')){
                        $("#s2id_"+elem.attr("id")).addClass('has-error').removeClass('has-success');
                    }else{
                       $(elem).parents(".col-sm-1").addClass("has-error").removeClass('has-success');   
                       $(elem).parents(".col-sm-3").addClass("has-error").removeClass('has-success');




                    }
                    if($(element).attr('type') == 'radio'){
                        $(element.form).find("input[type=radio]").each(function(which){
                            $(element.form).find("label[for=" + this.id + "]").addClass("has-error");
                            $(this).addClass("has-error");
                        });
                    } else {
                        $(element.form).find("label[for=" + element.id + "]").addClass("has-error");
                        $(element).addClass("has-error");
                    }
                },
                unhighlight:function(element, errorClass, validClass){
                    var elem = $(element);
                    if(elem.hasClass('select2-offscreen')){
                        $("#s2id_"+elem.attr("id")).addClass('has-success').removeClass('has-error');
                    }else{
                        $(element).parents(".col-sm-1").addClass('has-success').removeClass('has-error');  
                        $(element).parents(".col-sm-3").addClass('has-success').removeClass('has-error');   

                                           
                    }
                    if($(element).attr('type') == 'radio'){
                        $(element.form).find("input[type=radio]").each(function(which){
                            // $(element.form).find("label[for=" + this.id + "]").addClass("has-success").removeClass("has-error");
                            // $(this).addClass("has-success").removeClass("has-error");
                        });
                    } else {
                        // $(element.form).find("label[for=" + element.id + "]").addClass("has-success").removeClass("has-error");
                        // $(element).addClass("has-success").removeClass("has-error");
                    }
                }
            });
            $(".cancel").click(function() {
                validator.resetForm();
            });
        }); 

</script>   


                      <!--2do formulario Vehiculo -->
                      <div class="col-sm-8 text-center" style="margin-top:-70px;" align="">                    
                          <div class="client-form" style="" class="col-sm-12">
                              <form  id="formv" name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="jsonComercio/registrarVehiculosJson.php" style="margin-top:-11.9%">
                                 <!-- <div class="col-sm-1" style="margin-right:20px;">
                                    
                                  </div>-->  
                                   <h5 id="forma-titulo3a" align="center" style="width:100%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:5px;  background-color: #0e315a; color: white; border-radius: 5px;"><?php echo "Registrar Vehículo";?></h5>

                              <div class="form-group" style="margin-bottom: -15px; margin-top: 25px;">  

                                  <div class="col-sm-1" style="margin-right:100px;margin-left:115px;width: 120px;">
                                      <input type="hidden" name="contribuyente" value="<?php echo $idContribuyente ?>">

                                      <div class="form-group" style="margin-top: 8px;"  align="left"> 

                                       <?php
                                        $sv = "SELECT * FROM gc_tipo_vehiculo v ORDER BY nombre ASC";
                                        $rv = $mysqli->query($sv);
                                        ?>

                                          <label id="ssstv" class="control-label">
                                              <strong class="obligado">*</strong>Tipo Vehículo:
                                          </label>

                                          <select name="tipo" id="ssstv" class="form-control col-sm-1 select2" style="width:150px;height:30px;padding:2px" title="Seleccione Tipo Vehículo" required> 
                                                <option value="">Tipo Vehículo</option>
                                     
                                                    <?php while($row=mysqli_fetch_row($rv)){ ?>
                                                         <option value="<?php echo $row[0] ?>"><?php echo $row[1] ?></option>
                                                    <?php } ?>
                                          </select>

                                      </div>
                                   </div>

                                  <div class="col-sm-1" style="margin-right:100px;width: 120px;">
                                      <input type="hidden" name="contribuyente" value="<?php echo $idContribuyente ?>">

                                      <div class="form-group" style="margin-top: 8px;"  align="left"> 


                                       <?php
                                        $st = "SELECT t.id_unico,
                                                t.numeroidentificacion,
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
                                                t.apellidodos)) AS nombreTercero
                                               FROM gf_tercero t 
                                               ORDER BY nombreTercero ASC";
                                        $rt = $mysqli->query($st);
                                        ?>

                                          <label class="control-label">
                                             Tercero:
                                          </label>

                                          <select name="tercero" id="sltRubroFuente" class="select2-container form-control col-sm-1 select2" style="width:150px;height:30px;padding:4px;" title="Seleccione Tercero"> 
                                                <option value="">Tercero</option>
                                     
                                                <?php while($rowt=mysqli_fetch_row($rt)){ ?>
                                                     
                                                     <option value="<?php echo $rowt[0] ?>"><?php echo $rowt[1]." - ".ucwords(mb_strtolower($rowt[2])) ?></option>
                                                                  
                                                <?php } ?>
                                          </select>

                                      </div>
                                   </div>

                                   <div class="col-sm-2" style="margin-right:100px;width: 120px;margin-top: 7px;">
                                      <label for="icodigo" class="control-label" >
                                          Código Interno: 
                                      </label>
                                      <input style="width: 100px;height:30px;margin-top:5px;" type="text" name="codigo"  id="icodigo" class="form-control" maxlength="10" title="Ingrese Código" onkeypress="return txtValida(event,'num_car')" placeholder="Código Interno">
                                  </div>
                                </div>
                                
  
                                <div class="col-sm-1" style="margin-right:100px;margin-left:100px;width: 120px;">
                                            <input type="hidden" name="contribuyente" value="<?php echo $idContribuyente ?>">

                                            <div class="form-group" style="margin-top: 8px;"  align="left"> 

                                             <?php
                                              $sts = "SELECT * FROM gc_tipo_servicio tc ORDER BY nombre ASC";
                                              $rts = $mysqli->query($sts);
                                              ?>

                                                <label class="control-label" style="margin-top: -5px;">Tipo Servicio:</label>

                                                <select name="tipoServicio" id="sltRubroFuente" class="select2-container form-control col-sm-1 select2" style="width:150px;height:30px;padding:2px" title="Seleccione Tipo Servicio" > 
                                                      <option value="">Tipo Servicio</option>
                                           
                                                          <?php while($rowts=mysqli_fetch_row($rts)){ ?>
                                                                <option value="<?php echo $rowts[0] ?>"><?php echo $rowts[1] ?></option>
                                                          <?php } ?>
                                                </select>

                                            </div>
                                </div>

                                <div class="col-sm-1" style="margin-right:110px;margin-left: -10px;width: 120px;margin-top: 8px;">
                                          <label for="icodigo" class="control-label col-sm-2" style="margin-left: -10px;margin-top: -5px;" >
                                             <strong style="color:#03C1FB;">*</strong>Placa: 
                                          </label>
                                          <input style="width: 141px;height:30px;margin-top:2px;" type="text" name="placa"  id="icodigo" class="form-control" maxlength="10" title="Ingrese Placa" onkeypress="return txtValida(event,'num_car')" placeholder="Placa" required>
                                 </div>  
                                 <div class="col-sm-3" style="margin-right:20px;">
                                          <label for="icodigo" class="control-label" style="margin-left: -34%;margin-top: 5px;">
                                             <strong style="color:#03C1FB;">*</strong>Porcentaje Propiedad: 
                                          </label>
                                          <input style="width: 100px;height:30px;" type="text" name="porcentajePropiedad"  id="icodigo" class="form-control" maxlength="16" title="Ingrese Porcentaje Propiedad" onkeypress="return txtValida(event,'decimales')" placeholder="Porcentaje Propiedad" required>
                                  </div>

                                  <div class="col-sm-1 form-group" style="margin-top:30px;margin-left:-70px">
                                          <button type="submit" id="btnGuardarDetalle" class="btn btn-primary sombra"><li class="glyphicon glyphicon-floppy-disk"></li></button>                                
                                          <input type="hidden" name="MM_insert" >
                                         
                                  </div>     
                               
                              </form>                        
                          </div>
                           <!--listado vehiculos contribuyente-->
                                   <h5 id="forma-titulo3a" align="center" style="width:100%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:5px;  background-color: #0e315a; color: white; border-radius: 5px;"><?php echo "Listado de Vehículos";?></h5>

                          <?php 
                                 $sqlV = "SELECT v.id_unico,

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
                                        t.apellidodos)) AS NOMBRECONTRIBUYENTE ,  

                                        tv.nombre,

                                        IF(CONCAT_WS(' ',
                                        terv.nombreuno,
                                        terv.nombredos,
                                        terv.apellidouno,
                                        terv.apellidodos) 
                                        IS NULL OR CONCAT_WS(' ',
                                        terv.nombreuno,
                                        terv.nombredos,
                                        terv.apellidouno,
                                        terv.apellidodos) = '',
                                        (terv.razonsocial),
                                        CONCAT_WS(' ',
                                        terv.nombreuno,
                                        terv.nombredos,
                                        terv.apellidouno,
                                        terv.apellidodos)) AS NOMBRETERCERO,  


                                        v.cod_inter,
                                        tser.nombre AS nombreServicio,
                                        v.placa,
                                        v.porc_propiedad

                                        FROM gc_vehiculo v

                                        LEFT JOIN gc_contribuyente c ON c.id_unico=v.contribuyente
                                        LEFT JOIN gf_tercero t ON t.id_unico=c.tercero
                                        LEFT JOIN gc_tipo_vehiculo tv ON tv.id_unico=v.tipo_vehiculo
                                        LEFT JOIN gf_tercero terv ON terv.id_unico=v.tercero
                                        LEFT JOIN gc_tipo_servicio tser ON tser.id_unico=v.tipo_serv
                                WHERE md5(v.contribuyente)='$id'";

                                $resultadoV=$mysqli->query($sqlV);

                           ?>

                                <div class="table-responsive contTabla" >
                                  <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                                      <thead>
                                          <tr>
                                              <td style="display: none;">Identificador</td>
                                              <td width="7%" class="cabeza"></td>
                                              
                                              <td class="cabeza"><strong>Tipo Vehículo</strong></td>
                                              <td class="cabeza"><strong>Tercero Empresa</strong></td>
                                              <td class="cabeza"><strong>Código Interno</strong></td>
                                              <td class="cabeza"><strong>Tipo Servicio</strong></td>
                                              <td class="cabeza"><strong>Placa</strong></td>
                                              <td class="cabeza"><strong>Porcentaje Propiedad</strong></td>


                                          </tr>
                                          <tr>
                                              <th class="cabeza" style="display: none;">Identificador</th>
                                              <th width="7%"></th>
                                              
                                              <th class="cabeza">Tipo Vehículo</th>
                                              <th class="cabeza">Tercero Empresa</th>
                                              <th class="cabeza">Código Interno</th>
                                              <th class="cabeza">Tipo Servicio</th>
                                              <th class="cabeza">Placa</th>
                                              <th class="cabeza">Porcentaje de Propiedad</th>

                                          </tr>
                                      </thead>
                                      <tbody>
                                          <?php 
                                          while ($row = mysqli_fetch_array($resultadoV)) { 
                                          ?>
                                           <tr>
                                              <td style="display: none;"><?php echo $row['id_unico']?></td>
                                              <td>
                                                  <a href="#" onclick="javascript:eliminarveh(<?php echo $row[0];?>);">
                                                     <i title="Eliminar" class="glyphicon glyphicon-trash"></i>
                                                  </a>
                                                  <a title="Modificar" style="text-decoration: none" class="glyphicon glyphicon-edit" onclick="javascript:open_modal_veh(<?php echo $row[0] ?>)"></a>
                                              </td>
             
                                              <td class="campos"><?php echo $row['nombre'] ?></td>                
                                              <td class="campos"><?php echo ucwords(mb_strtolower($row['NOMBRETERCERO'])) ?></td>                
                                              <td class="campos"><?php echo $row['cod_inter'] ?></td>
                                              <td class="campos"><?php echo $row['nombreServicio']?></td>                
                                              <td class="campos"><?php echo $row['placa']?></td>      
                                              <td class="campos"><?php echo $row['porc_propiedad']?></td>                

                                          </tr>
                                          <?php }
                                          ?>
                                      </tbody>
                                  </table>
                                </div>

                      <!--modales de eliminacion-->
                            <div class="modal fade" id="myModal" role="dialog" align="center" >
                            <div class="modal-dialog">
                              <div class="modal-content">
                                <div id="forma-modal" class="modal-header">
                                  <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                                </div>
                                <div class="modal-body" style="margin-top: 8px">
                                  <p>¿Desea eliminar el registro seleccionado de Vehiculo?</p>
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
                            <?php require 'GC_VEHICULO_MODAL.php' ; ?>

                     <!--Script que envia los datos para la eliminación-->
                          <script type="text/javascript">
                                function eliminarveh(id)
                                {
                                   var result = '';
                                   $("#myModal").modal('show');
                                   $("#ver").click(function(){
                                        $("#mymodal").modal('hide');
                                        $.ajax({
                                            type:"GET",
                                            url:"jsonComercio/eliminarVehiculosJson.php?id="+id,
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


                             function open_modal_veh(id){  
                                    var form_data={                            
                                      id:id 
                                    };
                                    $.ajax({
                                        type:"POST",
                                        url: "GC_VEHICULO_MODAL.php#mdlModificar",
                                        data:form_data,
                                        success: function (data) { 
                                          $("#mdlModificar").html(data);
                                          $(".modalv").modal('show');
                                       }
                                   })  
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
                                  document.location = 'modificar_GC_CONTRIBUYENTE.php.php';
                                });
                              
                            </script>

                            <script type="text/javascript">    
                                $('#ver2').click(function(){
                                  document.location = 'modificar_GC_CONTRIBUYENTE.php.php';
                                });    
                            </script>




                          <script type="text/javascript" >
                            function abrirdetalleMov(id,valor){                                                                                                   
                              var form_data={                            
                                id:id,
                                valor:valor                          
                              };
                              $.ajax({
                                type: 'POST',
                                  url: "registrar_GF_DETALLE_COMPROBANTE_MOVIMIENTO_2.php#mdlDetalleMovimiento",
                                  data:form_data,
                                  success: function (data) { 
                                    $("#mdlDetalleMovimiento").html(data);
                                    $(".mov").modal('show');
                                  }
                              });
                            }
                            function abrirdetalleMov1(id,valor){                                                                                                   
                              var form_data={                            
                                id:id,
                                valor:valor                          
                              };
                              $.ajax({
                                type: 'POST',
                                  url: "registrar_GF_DETALLE_COMPROBANTE_MOVIMIENTO.php#mdlDetalleMovimiento",
                                  data:form_data,
                                  success: function (data) { 
                                    $("#mdlDetalleMovimiento").html(data);
                                    $(".mov").modal('show');
                                  }
                              });
                            }                                                                                          
                          </script>

                          <style>
                              .valores:hover{
                                  cursor: pointer;
                                  color:#1155CC;
                              }
                          </style>
                          <div class="container">

                          </div>

                          <script>
                            $("#btnGuardarM").click(function(){
                                if($("#sltBanco").val()==""){
                                    $("#mdlBanco").modal('show');
                                }else{
                                    var form_data = {
                                        banco:$("#sltBanco").val(),
                                        fecha:$("#fecha").val(),
                                        descripcion:$("#txtDescripcion").val(),
                                        valor:<?php echo $w; ?>,
                                        valorEjecucion:'0',
                                        comprobante:$("#id").val(),
                                        tercero:$("#slttercero").val(),
                                        proyecto:$("#sltproyecto").val(),
                                        centro:$("#sltcentroc").val()
                                    };
                                    var result = '';
                                    
                                    $.ajax({
                                        type: 'POST',
                                        url: "consultarComprobanteIngreso/GuardarBanco.php",
                                        data:form_data,
                                        success: function (data) {
                                            result = JSON.parse(data);
                                            console.log(data);
                                            if(result==true){
                                                $("#guardado").modal('show');
                                            }else{
                                                $("#noguardado").modal('show');
                                            }
                                        }
                                    });
                                }                                                                                                
                            });                        
                          </script>

                      </div>           

                     
                      
                  <?php }else{ ?>
                      <?php if(isset($_GET['nc'])){ ?>
                      <script src="dist/jquery.validate.js"></script>

<script>
 $().ready(function() {
            var validator = $("#form4").validate({
                ignore: "",

                errorElement:"em",
                errorPlacement: function(error, element){
                    error.addClass('help-block');
                },
                highlight: function(element, errorClass, validClass){
                    var elem = $(element);
                    if(elem.hasClass('select2-offscreen')){
                        $("#s2id_"+elem.attr("id")).addClass('has-error').removeClass('has-success');
                    }else{
                       $(elem).parents(".col-sm-2").addClass("has-error").removeClass('has-success');   
                    }
                    if($(element).attr('type') == 'radio'){
                        $(element.form).find("input[type=radio]").each(function(which){
                            $(element.form).find("label[for=" + this.id + "]").addClass("has-error");
                            $(this).addClass("has-error");
                        });
                    } else {
                        $(element.form).find("label[for=" + element.id + "]").addClass("has-error");
                        $(element).addClass("has-error");
                    }
                },
                unhighlight:function(element, errorClass, validClass){
                    var elem = $(element);
                    if(elem.hasClass('select2-offscreen')){
                        $("#s2id_"+elem.attr("id")).addClass('has-success').removeClass('has-error');
                    }else{
                        $(element).parents(".col-sm-2").addClass('has-success').removeClass('has-error');  
                    }
                    if($(element).attr('type') == 'radio'){
                        $(element.form).find("input[type=radio]").each(function(which){
                            // $(element.form).find("label[for=" + this.id + "]").addClass("has-success").removeClass("has-error");
                            // $(this).addClass("has-success").removeClass("has-error");
                        });
                    } else {
                        // $(element.form).find("label[for=" + element.id + "]").addClass("has-success").removeClass("has-error");
                        // $(element).addClass("has-success").removeClass("has-error");
                    }
                }
            });
            $(".cancel").click(function() {
                validator.resetForm();
            });
        }); 

</script>   

                      <!--2do formulario novedades Comercio -->
                     <div class="col-sm-8 text-center " style="margin-top:-40px;" align="">                    
                          <div class="client-form" style="" class="col-sm-12">
                              <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="jsonComercio/registrarNovedadComercioJson.php" style="margin-top:-16%" id="form4"> 
                                 <h5 id="forma-titulo3a" align="center" style="width:100%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px;   background-color: #0e315a; color: white; border-radius: 5px;"><?php echo "Registrar Novedad";?></h5>                                  
                              <div class="form-group" style="padding-top: 25px;" >

                                  <div class="col-sm-3" style="margin-right:85px;margin-left: -300px;">
                                    
                                  </div>  

                                  <div class="col-sm-2" style="margin-left: 70px; margin-right:45px;">

                                      <input type="hidden" name="contribuyente" value="<?php echo $idContribuyente ?>">


                                       <?php
                                        $cuentaI = "SELECT * FROM gc_tipo_novedad tn ORDER BY nombre ASC";
                                        $rsctai = $mysqli->query($cuentaI);
                                        ?>

                                          <label class="control-label" style="margin-left: -36px">
                                              <strong class="obligado">*</strong>Tipo Novedad:
                                          </label>

                                          <select name="tipo" id="sltRubroFuente" class="select2-container form-control col-sm-1 select2" style="width:140px;height:30px;" title="Seleccione Tipo Novedad" required> 
                                                <option value="">Tipo Novedad</option>
                                     
                                                    <?php while($row=mysqli_fetch_row($rsctai)){ ?>
                                                         <option value="<?php echo $row[0] ?>"><?php echo $row[1] ?></option>
                                                    <?php } ?>
                                          </select>



                                   </div>

                                  <div class="col-sm-1" style="margin-right:65px;">
                                         <div class="form-group"  align="left">
                    
                                          <label for="fechaini" type = "date" class="control-label"><strong class="obligado">*</strong>Fecha Inicio:</label>

                                          <input readonly="readonly " type="text" name="fecha" onkeypress="return justNumbers(event);" id="fechaini"  class="form-control" style="height:30px;padding:2px;width:100px" required="" />
                                      </div>
                                  </div>
                                  <div class="col-sm-1">
                                     <div class="form-group">
                                        <label for="cact" class="control-label">
                                            Número Acto: 
                                        </label>
                                        <input  type="text" name="numeroActo"  id="cact" class="form-control" maxlength="20" title="Ingrese Número Acto" onkeypress="return txtValida(event,'num_car')" placeholder="Número Acto">
                                    </div>
                                  </div>
                                  <div class="col-sm-1" style="margin-right:45px;margin-left: 50px;">
                                     <div class="form-group">
                                            <label for="Observaciones" class="control-label">
                                                Observaciones:
                                            </label>    
                                            <textarea name="observaciones"  placeholder="Observaciones" id="Observaciones"  class="form-control" rows="3" style="width: 215px;height:40px;padding-top: 2px;margin-top: 0px" title="Ingrese Observaciones" maxlength="100"></textarea>
                                      </div>
                                  </div>       

                                  <div class="col-sm-1 form-group" style="margin-top:32px;margin-left:60px">
                                      <button type="submit" id="btnGuardarDetalle" class="btn btn-primary sombra"><li class="glyphicon glyphicon-floppy-disk"></li></button>                                
                                      <input type="hidden" name="MM_insert" >
                                     
                                  </div>  

                                </div>                         
                              </form>                        
                          </div>
                      </div>
 
                      <!--listado novedades comercio contribuyente-->
                      <div class="col-sm-8" style="margin-top:-3%">
                           <h5 id="forma-titulo3a" align="center" style="width:100%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:5px;  background-color: #0e315a; color: white; border-radius: 5px;"><?php echo "Listado de Novedades";?></h5>


                      <?php 

                      $sqlNC="
                      SELECT nc.id_unico,
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
                      t.apellidodos)) AS NOMBRETERCERO,
                      tn.nombre,
                      DATE_FORMAT(nc.fecha,'%d-%m-%Y') AS fechaFacConvertida,
                      nc.num_acto,
                      nc.observaciones

                      FROM gc_novedades_comercio nc
                      LEFT JOIN gc_contribuyente c ON c.id_unico=nc.contribuyente
                      LEFT JOIN gf_tercero t ON t.id_unico=c.tercero
                      LEFT JOIN gc_tipo_novedad tn ON tn.id_unico=nc.tipo_novedad
                       WHERE md5(nc.contribuyente)='$id'
                      ";
                          
                      $resultadoNC  = $mysqli->query($sqlNC);



                       ?>

                                <div class="table-responsive contTabla" >

                            <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <td style="display: none;">Identificador</td>
                                        <td width="7%" class="cabeza"></td>
                                        
                                        <td class="cabeza"><strong>Tipo Novedad</strong></td>
                                        <td class="cabeza"><strong>Fecha</strong></td>    
                                        <td class="cabeza"><strong>Número de Acto</strong></td>    
                                        <td class="cabeza"><strong>Observaciones</strong></td>    
                                    </tr>
                                    <tr>
                                        <th class="cabeza" style="display: none;">Identificador</th>
                                        <th width="7%"></th>
                                        
                                        <th class="cabeza">Tipo Novedad</th>
                                        <th class="cabeza">Fecha</th>
                                        <th class="cabeza">Número de Acto</th>
                                        <th class="cabeza">Observaciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    while ($row=mysqli_fetch_row($resultadoNC)) { 
                                    ?>
                                     <tr>
                                        <td style="display: none;"><?php echo $row[0]?></td>
                                        <td>
                                            <a href="#" onclick="javascript:eliminarnc(<?php echo $row[0];?>);">
                                                <i title="Eliminar" class="glyphicon glyphicon-trash"></i>
                                            </a>
                                            <a title="Modificar" style="text-decoration: none" class="glyphicon glyphicon-edit" onclick="javascript:open_modal_nc(<?php echo $row[0] ?>)"></a>
                                        </td>
                                        <td class="campos"><?php echo $row[2] ?></td>                            
                                        <td class="campos"><?php echo $row[3] ?></td> 
                                        <td class="campos"><?php echo $row[4] ?></td>             
                                        <td class="campos"><?php echo $row[5] ?></td>                
                                    </tr>
                                    <?php }
                                    ?>
                                </tbody>
                            </table>
                         </div>

                        <!--modales de eliminacion-->
                        <div class="modal fade" id="myModal" role="dialog" align="center" >
                        <div class="modal-dialog">
                          <div class="modal-content">
                            <div id="forma-modal" class="modal-header">
                              <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                            </div>
                            <div class="modal-body" style="margin-top: 8px">
                              <p>¿Desea eliminar el registro seleccionado de Novedad Comercio?</p>
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
                        <?php require 'GC_NOVEDADES_COMERCIO_MODAL.php'; ?>
                        <!--Scrip que envia los datos para la eliminación-->
                        <script type="text/javascript">
                              function eliminarnc(id)
                              {
                                 var result = '';
                                 $("#myModal").modal('show');
                                 $("#ver").click(function(){
                                      $("#mymodal").modal('hide');
                                      $.ajax({
                                          type:"GET",
                                          url:"jsonComercio/eliminarNovedadesComercioJson.php?id="+id,
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

                              function open_modal_nc(id){  

                                    var form_data={                            
                                      id:id 
                                    };
                                    $.ajax({
                                        type:"POST",
                                        url: "GC_NOVEDADES_COMERCIO_MODAL.php#mdlModificar",
                                        data:form_data,
                                        success: function (data) { 
                                          $("#mdlModificar").html(data);
                                          $(".modalnc").modal('show');
                                       }
                                   })  
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
                                document.location = 'modificar_GC_CONTRIBUYENTE.php.php';
                              });
                            
                          </script>

                          <script type="text/javascript">    
                              $('#ver2').click(function(){
                                document.location = 'modificar_GC_CONTRIBUYENTE.php.php';
                              });    
                          </script>

                          <script type="text/javascript" >
                            function abrirdetalleMov(id,valor){                                                                                                   
                              var form_data={                            
                                id:id,
                                valor:valor                          
                              };
                              $.ajax({
                                type: 'POST',
                                  url: "registrar_GF_DETALLE_COMPROBANTE_MOVIMIENTO_2.php#mdlDetalleMovimiento",
                                  data:form_data,
                                  success: function (data) { 
                                    $("#mdlDetalleMovimiento").html(data);
                                    $(".mov").modal('show');
                                  }
                              });
                            }
                            function abrirdetalleMov1(id,valor){                                                                                                   
                              var form_data={                            
                                id:id,
                                valor:valor                          
                              };
                              $.ajax({
                                type: 'POST',
                                  url: "registrar_GF_DETALLE_COMPROBANTE_MOVIMIENTO.php#mdlDetalleMovimiento",
                                  data:form_data,
                                  success: function (data) { 
                                    $("#mdlDetalleMovimiento").html(data);
                                    $(".mov").modal('show');
                                  }
                              });
                            }                                                                                          
                          </script>

                          <style>
                              .valores:hover{
                                  cursor: pointer;
                                  color:#1155CC;
                              }
                          </style>
                          <div class="container">

                          </div>

                          <script>
                            $("#btnGuardarM").click(function(){
                                if($("#sltBanco").val()==""){
                                    $("#mdlBanco").modal('show');
                                }else{
                                    var form_data = {
                                        banco:$("#sltBanco").val(),
                                        fecha:$("#fecha").val(),
                                        descripcion:$("#txtDescripcion").val(),
                                        valor:<?php echo $w; ?>,
                                        valorEjecucion:'0',
                                        comprobante:$("#id").val(),
                                        tercero:$("#slttercero").val(),
                                        proyecto:$("#sltproyecto").val(),
                                        centro:$("#sltcentroc").val()
                                    };
                                    var result = '';
                                    
                                    $.ajax({
                                        type: 'POST',
                                        url: "consultarComprobanteIngreso/GuardarBanco.php",
                                        data:form_data,
                                        success: function (data) {
                                            result = JSON.parse(data);
                                            console.log(data);
                                            if(result==true){
                                                $("#guardado").modal('show');
                                            }else{
                                                $("#noguardado").modal('show');
                                            }
                                        }
                                    });
                                }                                                                                                
                            });                        
                          </script>
                      </div>  


                      <?php }else{ ?>
                            <?php  if(isset($_GET['doc'])){ ?>
<script>
 $().ready(function() {
            var validator = $("#form5").validate({
                ignore: "",

                errorElement:"em",
                errorPlacement: function(error, element){
                    error.addClass('help-block');
                },
                highlight: function(element, errorClass, validClass){
                    var elem = $(element);
                    if(elem.hasClass('select2-offscreen')){
                        $("#s2id_"+elem.attr("id")).addClass('has-error').removeClass('has-success');
                    }else{
                       $(elem).parents(".col-sm-2").addClass("has-error").removeClass('has-success'); 
                       $(elem).parents(".col-sm-1").addClass("has-error").removeClass('has-success');   

                    }
                    if($(element).attr('type') == 'radio'){
                        $(element.form).find("input[type=radio]").each(function(which){
                            $(element.form).find("label[for=" + this.id + "]").addClass("has-error");
                            $(this).addClass("has-error");
                        });
                    } else {
                        $(element.form).find("label[for=" + element.id + "]").addClass("has-error");
                        $(element).addClass("has-error");
                    }
                },
                unhighlight:function(element, errorClass, validClass){
                    var elem = $(element);
                    if(elem.hasClass('select2-offscreen')){
                        $("#s2id_"+elem.attr("id")).addClass('has-success').removeClass('has-error');
                    }else{
                        $(element).parents(".col-sm-2").addClass('has-success').removeClass('has-error');  
                        $(element).parents(".col-sm-1").addClass('has-success').removeClass('has-error');  

                    }
                    if($(element).attr('type') == 'radio'){
                        $(element.form).find("input[type=radio]").each(function(which){
                            // $(element.form).find("label[for=" + this.id + "]").addClass("has-success").removeClass("has-error");
                            // $(this).addClass("has-success").removeClass("has-error");
                        });
                    } else {
                        // $(element.form).find("label[for=" + element.id + "]").addClass("has-success").removeClass("has-error");
                        // $(element).addClass("has-success").removeClass("has-error");
                    }
                }
            });
            $(".cancel").click(function() {
                validator.resetForm();
            });
        }); 

</script>   
                     <!--2do formulario documento Contribuyente -->
                     <div class="col-sm-8 text-center " style="margin-top:-30px;" align="">                    
                          <div class="client-form" style="" class="col-sm-12">
                              <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="jsonComercio/registrarDocumentosContribuyenteJson.php" style="margin-top:-17.3%" id="form5">
                                   <h5 id="forma-titulo3a" align="center" style="width:100%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px;   background-color: #0e315a; color: white; border-radius: 5px;"><?php echo "Registrar Documento";?></h5>         
                               
                                 <div class="form-group" >

                                  <div class="col-sm-3" style="margin-right:60px;">                                  
                                  </div>

                                  <div class="col-sm-2" style="margin-right: 70px;margin-left: -190px;">

                                      <input type="hidden" name="contribuyente" value="<?php echo $idContribuyente ?>">
                                       <?php
                                        $cuentaI = "SELECT da.id_unico,cd.nombre,da.descripcion
                                                    FROM gc_tipo_documento_adjunto da 
                                                    LEFT JOIN gc_clase_documento cd ON cd.id_unico=da.clase_doc";
                                        $rsctai = $mysqli->query($cuentaI);
                                        ?>

                                          <label class="control-label" style="margin-left: -23px">
                                              <strong class="obligado">*</strong>Tipo Documento:
                                          </label>

                                          <select name="tipoDocumento" id="sltRubroFuente" class="select2-container form-control col-sm-1 select2" style="width:150px;height:30px;padding:2px" title="Seleccione Tipo Documento" required>
                                                <option value="">Tipo Documento</option>
                                     
                                                    <?php while($row=mysqli_fetch_row($rsctai)){ ?>
                                                         <option value="<?php echo $row[0]?>"><?php echo $row[1]." - ".ucwords(mb_strtolower($row[2] )) ?></option>
                                                    <?php } ?>
                                          </select>

                                   </div>
                                  <div class="col-sm-1" style="margin-right:190px;">
                                    <label for="ruta" class="col-sm-1 control-label"  >
                                          <strong class="obligado">*</strong>Archivo:
                                    </label>
                                    <input style="height:35px;padding:2px;width:200px" type="file" name="txtRuta"  id="ruta" class="form-control"  required="true" title="Cargue un Archivo" />
                                  </div>
                           
                    
                                          <label for="fechaCierre" type="date" class="control-label" style="margin-left: -24%">Fecha Expedición:</label>

                                          <input readonly="readonly " type="text" name="fechaExpedicion" onkeypress="return justNumbers(event);" id="fechaCierre"  class="form-control" style="height:30px;padding:2px;width:100px;margin-top: 5px;"  />
                 
                                  <div class="col-sm-1 form-group" style="margin-top:-4.2%;margin-left:75%">
                                      <button type="submit" id="btnGuardarDetalle" class="btn btn-primary sombra"><li class="glyphicon glyphicon-floppy-disk"></li></button>                                
                                      <input type="hidden" name="MM_insert" >
                                  </div> 

                                 </div>                           
                              </form>                        
                          </div>

                      </div>




                      <!--listado establecimiento contribuyente-->
                      <div class="col-sm-8" style="margin-top:-3%">
                           <h5 id="forma-titulo3a" align="center" style="width:100%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:0px;  background-color: #0e315a; color: white; border-radius: 5px;"><?php echo "Listado de documentos";?></h5>
<?php 
                                     
                    $sqlDC = "
                    SELECT dc.id_unico,
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
                    t.apellidodos)) AS NOMBRETERCERO,
                    tda.descripcion,
                    cd.nombre,
                    dc.ruta,
                    DATE_FORMAT(dc.fecha_exp,'%d-%m-%Y') AS fechaEXP

                    FROM gc_documento_contribuyente  dc
                    LEFT JOIN gc_contribuyente c ON c.id_unico=dc.contribuyente
                    LEFT JOIN gf_tercero t ON t.id_unico=c.tercero
                    LEFT JOIN gc_tipo_documento_adjunto tda ON tda.id_unico=dc.tipo_doc
                    LEFT JOIN gc_clase_documento cd ON cd.id_unico=tda.clase_doc
                    WHERE md5(dc.contribuyente)='$id'";
                        
                    $resultadoDC  = $mysqli->query($sqlDC);


                       ?>

                                <div class="table-responsive contTabla" >

                                    <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <td style="display: none;">Identificador</td>
                                                <td width="7%" class="cabeza"></td>
                                                <td class="cabeza"><strong>Tipo Documento</strong></td>
                                                <td class="cabeza"><strong>Fecha Expedición</strong></td>
                                                <td class="cabeza"><strong>Descargar</strong></td>


                                            </tr>
                                            <tr>
                                                <th class="cabeza" style="display: none;">Identificador</th>
                                                <th width="7%"></th>
                                                <th class="cabeza">Tipo Documento</th>
                                                <th class="cabeza">Fecha Expedición</th>
                                                <th class="oculto"></th>

                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            while ($row = mysqli_fetch_row($resultadoDC)) { 
                                            ?>
                                             <tr>
                                                <td style="display: none;"><?php echo $row[0]?></td>
                                                <td>
                                                    <a href="#" onclick="javascript:eliminardc(<?php echo $row[0];?>);">
                                                        <i title="Eliminar" class="glyphicon glyphicon-trash"></i>
                                                    </a>
                                                     <a title="Modificar" style="text-decoration: none" class="glyphicon glyphicon-edit" onclick="javascript:open_modal_doc(<?php echo $row[0] ?>)"></a>
                                                </td>      
                                                <td class="campos"><?php echo ucwords(mb_strtolower($row[2]." - ".$row[3])) ?></td>    <!--descripcion de gc_tipo_documento_adjunto y nombre de gc_clase_documento-->           


                                                <td class="campos"><?php echo $row[5] ?></td>
                                                <td class="campos"><a href="<?php echo substr($row[4],3,strlen($row[4])) ?>" download="" class="glyphicon glyphicon-download-alt"></a></td>

                                            </tr>
                                            <?php }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>

                     <!--modales de eliminacion-->
                        <div class="modal fade" id="myModal" role="dialog" align="center" >
                        <div class="modal-dialog">
                          <div class="modal-content">
                            <div id="forma-modal" class="modal-header">
                              <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                            </div>
                            <div class="modal-body" style="margin-top: 8px">
                              <p>¿Desea eliminar el registro seleccionado de Documento Contribuyente?</p>
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
                        <?php require 'GC_DOCUMENTO_MODAL.php'; ?>
                        <!--Scrip que envia los datos para la eliminación-->
                        <script type="text/javascript">
                              function eliminardc(id)
                              {
                                 var result = '';
                                 $("#myModal").modal('show');
                                 $("#ver").click(function(){
                                      $("#mymodal").modal('hide');
                                      $.ajax({
                                          type:"GET",
                                          url:"jsonComercio/eliminarDocumentoContribuyenteJson.php?id="+id,
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

                              function open_modal_doc(id){  
                                    var form_data={                            
                                      id:id 
                                    };
                                    $.ajax({
                                        type:"POST",
                                        url: "GC_DOCUMENTO_MODAL.php#mdlModificar",
                                        data:form_data,
                                        success: function (data) { 
                                          $("#mdlModificar").html(data);
                                          $(".modaldoc").modal('show');
                                       }
                                   })  
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
                                document.location = 'modificar_GC_CONTRIBUYENTE.php.php';
                              });
                            
                          </script>

                          <script type="text/javascript">    
                              $('#ver2').click(function(){
                                document.location = 'modificar_GC_CONTRIBUYENTE.php.php';
                              });    
                          </script>




                          <script type="text/javascript" >
                            function abrirdetalleMov(id,valor){                                                                                                   
                              var form_data={                            
                                id:id,
                                valor:valor                          
                              };
                              $.ajax({
                                type: 'POST',
                                  url: "registrar_GF_DETALLE_COMPROBANTE_MOVIMIENTO_2.php#mdlDetalleMovimiento",
                                  data:form_data,
                                  success: function (data) { 
                                    $("#mdlDetalleMovimiento").html(data);
                                    $(".mov").modal('show');
                                  }
                              });
                            }
                            function abrirdetalleMov1(id,valor){                                                                                                   
                              var form_data={                            
                                id:id,
                                valor:valor                          
                              };
                              $.ajax({
                                type: 'POST',
                                  url: "registrar_GF_DETALLE_COMPROBANTE_MOVIMIENTO.php#mdlDetalleMovimiento",
                                  data:form_data,
                                  success: function (data) { 
                                    $("#mdlDetalleMovimiento").html(data);
                                    $(".mov").modal('show');
                                  }
                              });
                            }                                                                                          
                          </script>

                          <style>
                              .valores:hover{
                                  cursor: pointer;
                                  color:#1155CC;
                              }
                          </style>
                          <div class="container">

                          </div>

                          <script>
                            $("#btnGuardarM").click(function(){
                                if($("#sltBanco").val()==""){
                                    $("#mdlBanco").modal('show');
                                }else{
                                    var form_data = {
                                        banco:$("#sltBanco").val(),
                                        fecha:$("#fecha").val(),
                                        descripcion:$("#txtDescripcion").val(),
                                        valor:<?php echo $w; ?>,
                                        valorEjecucion:'0',
                                        comprobante:$("#id").val(),
                                        tercero:$("#slttercero").val(),
                                        proyecto:$("#sltproyecto").val(),
                                        centro:$("#sltcentroc").val()
                                    };
                                    var result = '';
                                    
                                    $.ajax({
                                        type: 'POST',
                                        url: "consultarComprobanteIngreso/GuardarBanco.php",
                                        data:form_data,
                                        success: function (data) {
                                            result = JSON.parse(data);
                                            console.log(data);
                                            if(result==true){
                                                $("#guardado").modal('show');
                                            }else{
                                                $("#noguardado").modal('show');
                                            }
                                        }
                                    });
                                }                                                                                                
                            });                        
                          </script>
                      </div>  


                            <?php  } ?>
                      <?php } ?>
                  <?php } ?>

              <?php  } ?>
    <?php } ?>



  </div>
</div>
        <?php require_once './footer.php'; ?>
        <!-- Modal de carga de datos -->
        <div class="modal fade" id="mdlBanco" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <p>No hay un banco seleccionado</p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="btnModal" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="mdltipocomprobante" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <p>Seleccione un tipo de comprobante.</p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="tbmtipoF" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modales de guardado -->
        <div class="modal fade" id="guardado" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <p>Información guardada correctamente.</p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="btnG" class="btn" onclick="reloda()" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="noguardado" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">          
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <p>No se ha podido guardar la información.</p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="btnG2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Moidales de eliminado -->
        <!--<div class="modal fade" id="myModal" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <p>¿Desea eliminar el registro seleccionado de Detalle Ingreso?</p>
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
                        <p>No se pudo eliminar la información, el registro seleccionado está siendo utilizado por otra dependencia.</p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="ver2" class="btn" style="" data-dismiss="modal" >Aceptar</button>
                    </div>
                </div>
            </div>
        </div>-->

        <!-- Modales de modificado -->
        <div class="modal fade" id="infoM" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <p>Información modificada correctamente.</p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="btnModifico" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="noModifico" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">          
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <p>No se ha podido modificar la información.</p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="btnNoModifico" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
                    </div>
                </div>
            </div>
        </div>
        <?php require_once 'modalComprobanteCausacion_Ingreso.php'; ?>
        <!-- Librerias -->
        <script src="js/bootstrap.min.js"></script>
        <script>
            
        function modificarComprobante(){
            var tipoComprobante = $("#sltTipoM").val();
            var fecha = $("#fecha").val();
            var numero = $("#txtNumero").val();
            var tercero = $("#sltTercero").val();
            var claseContrato = $("#sltClaseContrato").val();
            var numeroContrato = $("#txtNumeroCT").val();
            var descripcion = $("#txtDescripcion").val();
            var id = $("#id").val();
                                    
            var form_data = {
                is_ajax:1,
                id:id,
                fecha:fecha,
                tipoComprobante:tipoComprobante,
                numero:numero,
                tercero:tercero,
                descripcion:descripcion,
                claseContrato:claseContrato,
                numeroContrato:numeroContrato
            };

            var result = ' ';
            $.ajax({
                type: 'POST',
                url: "json/modificarComprobanteIngreso.php",
                data: form_data,
                success: function (data) {
                    result = JSON.parse(data);
                    if (result==true) {
                        $("#infoM").modal('show');
                    }else{
                        $("#noModifico").modal('show');
                    }                    
                }
            });
        }
        </script>                       
        <script type="text/javascript">           
        function eliminar(id,pptal){
            var result = '';
            $("#myModal").modal('show');
            $("#ver").click(function(){
                $("#mymodal").modal('hide');
                $.ajax({
                    type:"GET",
                    url:"json/eliminarDetalleComprobanteIngreso.php?id="+id+"&pptal="+pptal,
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
        function cargarT2(id){
            $("#sltTercero"+id).prop('disabled',true);
            var padre = 0;
            $("#sltC"+id).change(function(){
                if ($("#sltC"+id).val()=="" || $("#sltC"+id).val()==0) {
                    padre = 0;         
                    $("#sltTercero"+id).prop('disabled',true);
                }else{
                    padre = $("#sltC"+id).val();
                }

                var form_data = {
                    is_ajax:1,
                    data:+padre
                };

                $.ajax({
                    type:"POST",
                    url:"consultasDetalleComprobante/consultarTercero.php",
                    data:form_data,                                                    
                    success: function (data) {
                        var tercero = document.getElementById('sltTercero'+id);
                        if (data==1) {
                             tercero.disabled=false;
                        }else if(data==2){
                            $("#sltTercero"+id).prop('disabled',true);
                        }                                                       
                    }
                });
            });
        }
        function cargarT(id){
            $("#sltTercero"+id).prop('disabled',true);
            var padre = 0;
            $("#sltC"+id).append(function(){
                if ($("#sltC"+id).val()=="" || $("#sltC"+id).val()==0) {
                    padre = 0;         
                    $("#sltTercero"+id).prop('disabled',true);
                }else{
                    padre = $("#sltC"+id).val();
                }

                var form_data = {
                    is_ajax:1,
                    data:+padre
                };

                $.ajax({
                    type:"POST",
                    url:"consultasDetalleComprobante/consultarTercero.php",
                    data:form_data,                                                    
                    success: function (data) {
                        var tercero = document.getElementById('sltTercero'+id);
                        if (data==1) {
                             tercero.disabled=false;
                        }else if(data==2){
                            $("#sltTercero"+id).prop('disabled',true);
                        }                                                       
                    }
                });
            });
        }
        
        function cargarCentro(id){
            $("#sltcentroC"+id).prop('disabled',true);
            var padre = 0;
            $("#sltC"+id).append(function(){
                if ($("#sltC"+id).val()=="" || $("#sltC"+id).val()==0) {
                    padre = 0;         
                    $("#sltcentroC"+id).prop('disabled',true);
                }else{
                    padre = $("#sltC"+id).val();
                }
                var form_data = {
                    is_ajax:1,
                    data:+padre
                };                                        
                $.ajax({
                    type:"POST",
                    url:"consultasDetalleComprobante/consultarCentroC.php",
                    data:form_data,                                                    
                    success: function (data) {
                        var centro = document.getElementById('sltcentroC'+id);
                        if (data==1) {
                            centro.disabled=false; 
                        }else if(data==2){
                            centro.disabled=true; 
                        }                                                       
                    }
                });
            });
        }
        
        function cargarCentro2(id){
            $("#sltcentroC"+id).prop('disabled',true);
            var padre = 0;
            $("#sltC"+id).append(function(){
                if ($("#sltC"+id).val()=="" || $("#sltC"+id).val()==0) {
                    padre = 0;         
                    $("#sltcentroC"+id).prop('disabled',true);
                }else{
                    padre = $("#sltC"+id).val();
                }
                var form_data = {
                    is_ajax:1,
                    data:+padre
                };                                        
                $.ajax({
                    type:"POST",
                    url:"consultasDetalleComprobante/consultarCentroC.php",
                    data:form_data,                                                    
                    success: function (data) {
                        var centro = document.getElementById('sltcentroC'+id);
                        if (data==1) {
                            centro.disabled=false; 
                        }else if(data==2){
                            centro.disabled=true; 
                        }                                                       
                    }
                });
            });
        }
        
        function cargarProyecto(id){
            var padre = 0;
            $("#sltProyecto"+id).prop('disabled',true);
            $("#sltC"+id).append(function(){
                if ($("#sltC"+id).val()=="" || $("#sltC"+id).val()==0) {
                    padre = 0;         
                    $("#sltProyecto"+id).prop('disabled',true);
                }else{
                    padre = $("#sltC"+id).val();
                }
                var form_data = {
                    is_ajax:1,
                    data:+padre
                };                                        
                $.ajax({
                    type:"POST",
                    url:"consultasDetalleComprobante/consultaProyecto.php",
                    data:form_data,                                                    
                    success: function (data) {
                        var proyecto = document.getElementById('sltProyecto'+id);
                        if (data==1) {
                            proyecto.disabled=false; 
                        }else if(data==2){
                            $("#sltProyecto"+id).prop('disabled',true);
                        }                                                       
                    }
                });
            });
        }
        function cargarProyecto2(id){
            var padre = 0;
            $("#sltProyecto"+id).prop('disabled',true);
            $("#sltC"+id).change(function(){
                if ($("#sltC"+id).val()=="" || $("#sltC"+id).val()==0) {
                    padre = 0;         
                    $("#sltProyecto"+id).prop('disabled',true);
                }else{
                    padre = $("#sltC"+id).val();
                }
                var form_data = {
                    is_ajax:1,
                    data:+padre
                };                                        
                $.ajax({
                    type:"POST",
                    url:"consultasDetalleComprobante/consultaProyecto.php",
                    data:form_data,                                                    
                    success: function (data) {
                        var proyecto = document.getElementById('sltProyecto'+id);
                        if (data==1) {
                            proyecto.disabled=false; 
                        }else if(data==2){
                            $("#sltProyecto"+id).prop('disabled',true);
                        }                                                       
                    }
                });
            });
        }
        
        function rubroFuente(id){                
            $("#sltconcepto"+id).change(function(){
                var form_data={
                    is_ajax:1,
                    concepto:$("#sltconcepto"+id).val()
                };
                $.ajax({
                    type: 'POST',
                    url: "consultarComprobanteIngreso/conceptoRubro.php",
                    data:form_data,
                    success: function (data) {                                                            
                        $("#sltrubroFte"+id).html(data).fadeIn();
                    }
                });
            });
        }
        
        function rubroCuenta(id){                                                
            $("#sltrubroFte"+id).change(function(){
                var form_data={
                    is_ajax:1,
                    rubro:$("#sltrubroFte"+id).val()
                };
                $.ajax({
                    type: 'POST',
                    url: "consultarComprobanteIngreso/cuenta.php",
                    data:form_data,
                    success: function (data) {
                        $("#sltC"+id).html(data).fadeIn();
                    }
                });
            });
        }
        function modificar(id){
          if(($("#idPrevio").val() != 0)||($("#idPrevio").val() != "")){
              var sltcuentaC = 'sltC'+$("#idPrevio").val();
              var lblCuentaC = 'cuenta'+$("#idPrevio").val();
              var sltTerceroC = 'sltTercero'+$("#idPrevio").val();
              var lblTerceroC = 'tercero'+$("#idPrevio").val();
              var sltCentroCC = 'sltcentroC'+$("#idPrevio").val();
              var lblCentroCC = 'centroC'+$("#idPrevio").val();
              var sltProyectoC = 'sltProyecto'+$("#idPrevio").val();
              var lblProyectoC = 'proyecto'+$("#idPrevio").val();
              var txtDebitoC = 'txtDebito'+$("#idPrevio").val();
              var lblDebitoC = 'debitoP'+$("#idPrevio").val();
              var txtCreditoC = 'txtCredito'+$("#idPrevio").val();
              var lblCreditoC = 'creditoP'+$("#idPrevio").val();
              var guardarC = 'guardar'+$("#idPrevio").val();
              var cancelarC = 'cancelar'+$("#idPrevio").val();
              var tablaC = 'tab'+$("#idPrevio").val();                    
              var lblRubroFuenteC = 'rubroFuente'+$("#idPrevio").val();
              var sltRubroFuenteC = 'sltrubroFte'+$("#idPrevio").val();
              
              $("#"+sltcuentaC).css('display','none');                               
              $("#"+lblCuentaC).css('display','block');
              $("#"+sltTerceroC).css('display','none');
              $("#"+lblTerceroC).css('display','block');
              $("#"+sltCentroCC).css('display','none');
              $("#"+lblCentroCC).css('display','block');
              $("#"+sltProyectoC).css('display','none');
              $("#"+lblProyectoC).css('display','block');
              $("#"+txtDebitoC).css('display','none');
              $("#"+lblDebitoC).css('display','block');
              $("#"+txtCreditoC).css('display','none');
              $("#"+lblCreditoC).css('display','block');                
              $("#"+guardarC).css('display','none');
              $("#"+cancelarC).css('display','none');
              $("#"+tablaC).css('display','none');
              $("#"+lblRubroFuenteC).css('display','block');
              $("#"+sltRubroFuenteC).css('display','none');
          }
            
          var sltcuenta = 'sltC'+id;
          var lblCuenta = 'cuenta'+id;
          var sltTercero = 'sltTercero'+id;
          var lblTercero = 'tercero'+id;
          var sltCentroC = 'sltcentroC'+id;
          var lblCentroC = 'centroC'+id;
          var sltProyecto = 'sltProyecto'+id;
          var lblProyecto = 'proyecto'+id;
          var txtDebito = 'txtDebito'+id;
          var lblDebito = 'debitoP'+id;
          var txtCredito = 'txtCredito'+id;
          var lblCredito = 'creditoP'+id;
          var guardar = 'guardar'+id;
          var cancelar = 'cancelar'+id;
          var tabla = 'tab'+id;
          var sltRubroFuente = 'sltrubroFte'+id;
          var lblRubroFuente = 'rubroFuente'+id;
          
          $("#"+sltcuenta).css('display','block');                               
          $("#"+lblCuenta).css('display','none');
          $("#"+sltTercero).css('display','block');
          $("#"+lblTercero).css('display','none');
          $("#"+sltCentroC).css('display','block');
          $("#"+lblCentroC).css('display','none');
          $("#"+sltProyecto).css('display','block');
          $("#"+lblProyecto).css('display','none');
          $("#"+txtDebito).css('display','block');
          $("#"+lblDebito).css('display','none');
          $("#"+txtCredito).css('display','block');
          $("#"+lblCredito).css('display','none');                
          $("#"+guardar).css('display','block');
          $("#"+cancelar).css('display','block');
          $("#"+tabla).css('display','block');
          $("#"+sltRubroFuente).css('display','block');
          $("#"+lblRubroFuente).css('display','none');
          $("#idActual").val(id);
          if($("#idPrevio").val() != id){
            $("#idPrevio").val(id);   
          }
        }
        
        function cancelar(id){
          var sltcuenta = 'sltC'+id;
          var lblCuenta = 'cuenta'+id;
          var sltTercero = 'sltTercero'+id;
          var lblTercero = 'tercero'+id;
          var sltCentroC = 'sltcentroC'+id;
          var lblCentroC = 'centroC'+id;
          var sltProyecto = 'sltProyecto'+id;
          var lblProyecto = 'proyecto'+id;
          var txtDebito = 'txtDebito'+id;
          var lblDebito = 'debitoP'+id;
          var txtCredito = 'txtCredito'+id;
          var lblCredito = 'creditoP'+id;
          var guardar = 'guardar'+id;
          var cancelar = 'cancelar'+id;
          var tabla = 'tab'+id;           
          var sltRubroFuente = 'sltrubroFte'+id;
          var lblRubroFuente = 'rubroFuente'+id;
          
          $("#"+sltcuenta).css('display','none');                               
          $("#"+lblCuenta).css('display','block');
          $("#"+sltTercero).css('display','none');
          $("#"+lblTercero).css('display','block');
          $("#"+sltCentroC).css('display','none');
          $("#"+lblCentroC).css('display','block');
          $("#"+sltProyecto).css('display','none');
          $("#"+lblProyecto).css('display','block');
          $("#"+txtDebito).css('display','none');
          $("#"+lblDebito).css('display','block');
          $("#"+txtCredito).css('display','none');
          $("#"+lblCredito).css('display','block');                
          $("#"+guardar).css('display','none');
          $("#"+cancelar).css('display','none');
          $("#"+tabla).css('display','none');
          $("#"+lblRubroFuente).css('display','block');
          $("#"+sltRubroFuente).css('display','none');
        }
      
        function guardarCambios(id){
          var sltcuenta = 'sltC'+id;
          var sltTercero = 'sltTercero'+id;
          var sltCentroC = 'sltcentroC'+id;
          var sltProyecto = 'sltProyecto'+id;
          var txtDebito = 'txtDebito'+id;
          var txtCredito = 'txtCredito'+id;
          var sltContribuyente = 'sltconcepto'+id;
          var sltRubroFuente = 'sltrubroFte'+id;
          
          var form_data = {
              is_ajax:1,
              id:+id,
              cuenta:$("#"+sltcuenta).val(),
              tercero:$("#"+sltTercero).val(),
              centroC:$("#"+sltCentroC).val(),
              proyecto:$("#"+sltProyecto).val(),
              debito:$("#"+txtDebito).val(),
              credito:$("#"+txtCredito).val(),
              concepto:$("#"+sltContribuyente).val(),
              rubroFuente:$("#"+sltRubroFuente).val()
          };
          var result='';
          $.ajax({
              type: 'POST',
              url: "json/modificarDetalleComprobanteIngreso.php",
              data:form_data,
              success: function (data) {
                  result = JSON.parse(data);                        
                  if (result==true) {
                      $("#infoM").modal('show');
                  }else{
                      $("#noModifico").modal('show');
                  }                    
              }
          });
        }
      /*  function limpiar_campos(){
          $("#sltContribuyente").prop('selectedIndex',0);
          $("#sltRubroFuente").prop('selectedIndex',0);
          $("#sltcuenta").prop('selectedIndex',0);
          $("#txtValor").val('');
        };*/

        function show_inputs(id){
          if(($("#idPrevio").val() != 0)||($("#idPrevio").val() != "")){
            var txtDebitoC = 'txtDebito'+$("#idPrevio").val();
            var lblDebitoC = 'debitoP'+$("#idPrevio").val();
            var txtCreditoC = 'txtCredito'+$("#idPrevio").val();
            var lblCreditoC = 'creditoP'+$("#idPrevio").val();
            var guardarC = 'guardarX'+$("#idPrevio").val();
            var cancelarC = 'cancelar'+$("#idPrevio").val();
            var tablaC = 'tab'+$("#idPrevio").val();

            $("#"+txtDebitoC).css('display','none');
            $("#"+lblDebitoC).css('display','block');
            $("#"+txtCreditoC).css('display','none');
            $("#"+lblCreditoC).css('display','block');                
            $("#"+guardarC).css('display','none');
            $("#"+cancelarC).css('display','none');
            $("#"+tablaC).css('display','none');                   
          }

          var txtDebito = 'txtDebito'+id;
          var lblDebito = 'debitoP'+id;
          var txtCredito = 'txtCredito'+id;
          var lblCredito = 'creditoP'+id;
          var guardar = 'guardarX'+id;
          var cancelar = 'cancelar'+id;
          var tabla = 'tab'+id;           

          $("#"+txtDebito).css('display','block');
          $("#"+lblDebito).css('display','none');
          $("#"+txtCredito).css('display','block');
          $("#"+lblCredito).css('display','none');                
          $("#"+guardar).css('display','block');
          $("#"+cancelar).css('display','block');
          $("#"+tabla).css('display','block');

          $("#idActual").val(id);
          if($("#idPrevio").val() != id){
            $("#idPrevio").val(id);   
          }
        }
        //Función para actualizar banco
        function update_bank(id){
          var debito = $("#txtDebito"+id).val();    //valor en debito
          var credito = $("#txtCredito"+id).val();  //valor en credito
          //Array de envio
          var form_data = {
            existente:46,
            debito:debito,
            credito:credito,
            id:id
          };
          //variable de captura de 
          var result = '';
          //envio ajax
          $.ajax({
            type:'POST',
            url:'consultasBasicas/consultarNumeros.php',
            data:form_data,
            success: function(data,textStatus,jqXHR){
              result = JSON.parse(data);
              if(result===true){
                $("#infoM").modal('show');
              }else{
                $("#noModifico").modal('show');
              }
            },error: function(data,textStatus,jqXHR){
              alert('Error : '+data+' ,'+textStatus+' ,'+jqXHR);
            }
          });
        }
        //Función para generar causación
  
        //Función para abrir modal de causación @id_com {id de comprobante de reconocimiento}
        function abrirModalCausa(id_com) {
          //Array de envio
          var form_data = {
            com:id_com
          };
          //Envio ajax
          $.ajax({
            url:'modalComprobanteCausacion_Ingreso.php#modalCausacion',
            type:'POST',
            data:form_data,
            success: function(data,textStatus,jqXHR) {
              $("#modalCausacion").html(data);
              $(".causa").modal('show');
            },error: function(data,textStatus,jqXHR) {
              alert('Error : D'+data+', status :'+textStatus+', jqXHR : '+jqXHR);
            } 
          });
        }
        </script>
        <script type="text/javascript" src="js/select2.js"></script>

        <script>
          $(".select2").select2();
          $("#slttercero").select2();                           
          $("#sltBanco").select2();                             
          $("#sltContribuyente").select2();                          
          $('#btnModifico').click(function(){
              window.location.reload();
          });        
          $('#btnNoModifico').click(function(){
              window.location.reload();
          });        
          $('#ver1').click(function(){
              window.location.reload();
          });        
          $('#ver2').click(function(){  
            window.location.reload();
          });        
          $('#btnG').click(function(){
              window.location.reload();
          });        
          $('#btnG2').click(function(){  
              window.location.reload();
          });            
          $("#btnMdlEliminadoP").click(function(){  
              window.location.reload();
          });
          function reload()  {
            window.location.reload();
          }
        </script>
        <?php require_once './registrar_GF_DETALLE_COMPROBANTE_MOVIMIENTO_2.php'; ?>        
        <script type="text/javascript">
        $("#modalCausacion").on('shown.bs.modal',function(){
            var dataTable = $("#tablaDetalleC").DataTable();
            dataTable.columns.adjust().responsive.recalc();
        });
      </script>
    </body>
</html>