<?php
    #creado : | Nestor Bautista | 01/08/2018 | 

    require_once ('head_listar.php');
    require_once ('./Conexion/conexion.php');
    #session_start();
    $vig = $_SESSION['anno'];
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

        <title>Actividad Contribuyente</title>
        <link href="css/select/select2.min.css" rel="stylesheet">
       
    </head>
    <body>
        <div class="container-fluid text-center">
            <div class="row content">
                <?php require_once 'menu.php'; ?>
                <div class="col-sm-8 text-left">
                    <h2 id="forma-titulo3" align="center" style="margin-top:0px; margin-right: 4px; margin-left: -10px;">Informe de Cobro Persuasivo</h2>
               
                    <h5 id="forma-titulo3a" align="center" style="margin-top:-20px; width:92%; display:<?php echo $a?>; margin-bottom: 10px; margin-right: 4px; margin-left: 4px;  background-color: #0e315a; color: white; border-radius: 5px"><?php echo ucwords((mb_strtolower($datosTercero)));?></h5>
                    <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">        
                        <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="informesComercio/generar_GC_INF_COBRO_PERSUASIVO.php" target="_blank">
                            <p align="center" style="margin-bottom: 25px; margin-top: 0px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                            
                            <?php
                                $contri = "SELECT c.id_unico,
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
                                                c.codigo_mat,
                                                t.numeroidentificacion,
                                                t.representantelegal,
                                                c.dir_correspondencia,
                                                c.telefono,
                                                c.fechainscripcion

                                                
                                        FROM gc_contribuyente c 
                                        LEFT JOIN gf_tercero t ON t.id_unico=c.tercero
                                        WHERE c.estado = 1 ORDER BY c.id_unico ASC";
                                $cont = $mysqli->query($contri);
                            ?>
                            <div class="form-group" style="margin-top: -5px">
                                <div class="clasUni" style="margin-top: -5px"> 
                                    <label for="sltCont1" class="control-label col-sm-5" style="margin-top: 0px">
                                         Contribuyente Inicial:
                                    </label>
                                    <select name="sltCont1" id="sltCont1" title="Seleccione Unidad Ejecutora" style="height: 30px; " class="select2_single form-control " >
                                        <option value="">Contribuyente Inicial</option>
                                        <?php
                                            while($C = mysqli_fetch_row($cont)){
                                                echo "<option value=".$C[0].">".$C[2].' - '.$C[3].' - '.$C[1]."</option>";
                                            }
                                        ?>
                                    </select>
                                </div>    
                            </div>
                            

                            <?php
                                $contri2 = "SELECT c.id_unico,
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
                                                c.codigo_mat,
                                                t.numeroidentificacion,
                                                t.representantelegal,
                                                c.dir_correspondencia,
                                                c.telefono,
                                                c.fechainscripcion

                                                
                                        FROM gc_contribuyente c 
                                        LEFT JOIN gf_tercero t ON t.id_unico=c.tercero
                                        WHERE c.estado = 1 ORDER BY c.id_unico DESC";
                                $cont2 = $mysqli->query($contri2);
                            ?>
                            <div class="form-group" style="margin-top: 2%">
                                <div class="clasUni" style="margin-top: -5px"> 
                                    <label for="sltCont2" class="control-label col-sm-5" style="margin-top: 0px">
                                         Contribuyente Final:
                                    </label>
                                    <select name="sltCont2" id="sltCont2" title="Seleccione Unidad Ejecutora" style="height: 30px; " class="select2_single form-control " >
                                        <option value="">Contribuyente Final</option>
                                        <?php
                                            while($C2 = mysqli_fetch_row($cont2)){
                                                echo "<option value=".$C2[0].">".$C2[2].' - '.$C2[3].' - '.$C2[1]."</option>";
                                            }
                                        ?>
                                    </select>
                                </div>    
                            </div>
                            
                            <div class="form-group" style="margin-top: -2%">   
                                <label for="no" class="col-sm-7 control-label"></label>
                                <button  class="btn sombra btn-primary" title="Generar reporte PDF" style="margin-top: 15px;"><li class="glyphicon glyphicon-print"></li></button>
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
            
            <script type="text/javascript">    
                $('#ver2').click(function(){
                    window.history.go(-1);
                });    
            </script>
        </div>

        <script src="js/select/select2.full.js"></script>

        <script type="text/javascript"> 
            $("#sltCont1").select2();
            $("#sltCont2").select2();
       </script>
    </body>
</html>