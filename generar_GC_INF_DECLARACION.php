<?php
    ###################################################################################################
    #
    #05/12/2017 creado por Nestor B 
    #
    ####################################################################################################

    require_once ('head_listar.php');
    require_once ('./Conexion/conexion.php');
    #session_start();

    $compania = $_SESSION['compania'];
    $anno = $_SESSION['anno'];

    @$id = $_GET['idE'];
    $emp = "SELECT e.id_unico, e.tercero, CONCAT( t.nombreuno, ' ', t.nombredos, ' ', t.apellidouno,' ', t.apellidodos ) , t.tipoidentificacion, ti.id_unico, CONCAT(ti.nombre,' ',t.numeroidentificacion)
            FROM gn_empleado e
            LEFT JOIN gf_tercero t ON e.tercero = t.id_unico
            LEFT JOIN gf_tipo_identificacion ti ON t.tipoidentificacion = ti.id_unico
            WHERE md5(e.id_unico) = '$id'";
    $bus = $mysqli->query($emp);
    $busq = mysqli_fetch_row($bus);
    $idT = $busq[0];
    $datosTercero= $busq[2].' ('.$busq[5].')';
    $a = "none";
    if(empty($idT))
    {
        $tercero = "Empleado";    
    }
    else
    {
        $tercero = $datosTercero;
        $a="inline-block";
    }

?>

        <script src="dist/jquery.validate.js"></script>
        <link rel="stylesheet" href="css/jquery-ui.css">
        <link rel="stylesheet" href="css/select2.css">
        <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
        <style>
            label #sltPeriodo-error {
                display: block;
                color: #155180;
                font-weight: normal;
                font-style: italic;
                font-size: 10px
            }

            body{
                font-size: 11px;
            }

           /* Estilos de tabla*/
           table.dataTable thead th,table.dataTable thead td{padding:1px 18px;font-size:10px}
           table.dataTable tbody td,table.dataTable tbody td{padding:1px}
           .dataTables_wrapper .ui-toolbar{padding:2px;font-size: 10px;
               font-family: Arial;}
        </style>
        <script>


        $().ready(function() {
          var validator = $("#form").validate({
                ignore: "",
            errorPlacement: function(error, element) {

              $( element )
                .closest( "form" )
                  .find( "label[for='" + element.attr( "id" ) + "']" )
                    .append( error );
            },
          });

          $(".cancel").click(function() {
            validator.resetForm();
          });
        });
        </script>
        <script src="js/jquery-ui.js"></script>
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


                $("#sltFechaA").datepicker({changeMonth: true,}).val();
                $("#sltFechaR").datepicker({changeMonth: true}).val();


        });
        </script>
        <script src="js/jquery-ui.js"></script>

        <title>Informe de Declaración</title>
        <link href="css/select/select2.min.css" rel="stylesheet">
       
    </head>
    <body>
        <div class="container-fluid text-center">
            <div class="row content">
                <?php require_once 'menu.php'; ?>
                <div class="col-sm-8 text-left">
                    <h2 id="forma-titulo3" align="center" style="margin-top:0px; margin-right: 4px; margin-left: -10px;">Informe de Declaración</h2>
                    <h5 id="forma-titulo3a" align="center" style="margin-top:-20px; width:92%; display:<?php echo $a?>; margin-bottom: 10px; margin-right: 4px; margin-left: 4px;  background-color: #0e315a; color: white; border-radius: 5px"><?php echo ucwords((mb_strtolower($datosTercero)));?></h5>
                    <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">        
                        <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="informes/generar_INF_SABANA_PRIMANAV.php" target="_blank">
                            <p align="center" style="margin-bottom: 25px; margin-top: 0px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                            <?php 
                            $uniE = " SELECT id_unico, nombre FROM gn_unidad_ejecutora";
                            $Ueje = $mysqli->query($uniE);
                            ?>
                            <div class="form-group col-sm-12" style="margin-top: -5px">
                                <script type="text/javascript">
                                    $(document).ready(function() {
                                        $("#datepicker").datepicker();  
                                    });
                                </script>
        
                                <label for="sltFechaA" class="col-sm-2 col-md-2 col-lg-2 control-label">Fecha Inicial:</label>
                                <input style="width:13%;" class="col-sm-2 col-md-2 col-lg-2 input-sm" type="text" name="sltFechaA" id="sltFechaA" step="1" onchange="javascript:fechaInicial();" placeholder="Ingrese la fecha" title="Ingrese la fehca de afiliación" >                            

                                <label for="sltBancoI" class="control-label col-sm-2" style="margin-top: 0px; ">Banco Inicial:</label>
                                <select  name="sltBancoI" id="sltBancoI" title="Seleccione Banco Inicial" style="height: 30px;" class="select2_single form-control col-sm-4" >
                                    <option value="">Banco Inicial</option>
                                    <?php 
                                        while($rowE = mysqli_fetch_row($periodo)){   
                                            echo "<option value=".$rowE[0].">".$rowE[1]."</option>";
                                        }
                                    ?>       
                                </select>

                                <button  class="btn sombra btn-primary" title="Generar reporte PDF" ><li class="fa fa-file-pdf-o"></li></button>

                            </div>

                            <?php 
                                $grupoG = " SELECT id_unico, nombre FROM gn_grupo_gestion";
                                $Grupo = $mysqli->query($grupoG);
                            ?>
                            <div class="form-group col-sm-12" style="margin-top: -5px">
                                <label for="sltFechaR" class="col-sm-2 col-md-2 col-lg-2 control-label"><strong class="obligado"></strong>Fecha Final:</label>
                                <input  class="col-sm-2 input-sm" type="text" name="sltFechaR" id="sltFechaR" step="1" disabled="true" placeholder="Ingrese la fecha Final"  style="width: 13%;height: 30px" class="form-control col-sm-2 col-md-2 col-lg-2"> 

                                <label for="sltPeriodo" class="control-label col-sm-2" style="margin-top: 0px; ">Banco Final:</label>
                                <select  name="sltPeriodo" id="sltPeriodo" title="Seleccione Periodo" style="height: 30px;" class="select2_single form-control" >
                                    <option value="">Banco Inicial</option>
                                    <?php 
                                        while($rowE = mysqli_fetch_row($periodo)){   
                                            echo "<option value=".$rowE[0].">".$rowE[1]."</option>";
                                        }
                                    ?>       
                                </select>

                                <button  class="btn sombra btn-primary" title="Generar reporte EXCEL" ><li class="fa fa-file-excel-o"></li></button>

                            </div>
                        </form>      
                    </div>
                </div>
            </div>                                    
        </div>
        <div>

            <?php require_once './footer.php'; ?>

            <!--Script que dan estilo al formulario-->

            <script type="text/javascript" src="js/menu.js"></script>
            <link rel="stylesheet" href="css/bootstrap-theme.min.css">
            <script src="js/bootstrap.min.js"></script>
            <!--Scrip que envia los datos para la eliminación-->
            <script type="text/javascript">
                function eliminar(id){
                    var result = '';
                    $("#myModal").modal('show');
                    $("#ver").click(function(){
                        $("#mymodal").modal('hide');
                        $.ajax({
                            type:"GET",
                            url:"json/eliminarVacacionesJson.php?id="+id,
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
                function modal(){
                    $("#myModal").modal('show');
                }
            </script>
            <script type="text/javascript">
                function recargar(){
                    window.location.reload();     
                }
            </script>     
            <!--Actualiza la página-->
            <script type="text/javascript">
                $('#ver1').click(function(){ 
                    reload();
                    //window.location= '../registrar_GN_ACCIDENTE.php?idE=<?php #echo md5($_POST['sltEmpleado'])?>';
                    //window.location='../listar_GN_ACCIDENTE.php';
                    window.history.go(-1);        
                });
            </script>

            <script type="text/javascript">    
                $('#ver2').click(function(){
                    window.history.go(-1);
                });    
            </script>
        </div>

        <script>
            function reportePdf(){
                $('form').attr('action', 'informes/generar_INF_VOLANTE_PAGO.php');
            }
        </script>
        <script src="js/select/select2.full.js"></script>

        <script type="text/javascript"> 
            $("#sltPeriodo").select2();
            $("#sltGrupoG").select2();
            $("#sltUnidadE").select2();
            $("#sltConcepto").select2();
        </script>
        <script>
            function fechaInicial(){
                var fechain= document.getElementById('sltFechaA').value;
                var fechafi= document.getElementById('sltFechaR').value;
                var fi = document.getElementById("sltFechaR");
                fi.disabled=false;
       
                $( "#sltFechaR" ).datepicker( "destroy" );
                $( "#sltFechaR" ).datepicker({ changeMonth: true, minDate: fechain});
           
            }
        </script>
    </body>
</html>