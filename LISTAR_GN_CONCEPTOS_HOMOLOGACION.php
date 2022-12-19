<?php
#####################################################################################################
#       ******************************      MODIFICACIONES      ******************************      # 
#####################################################################################################
#14/01/2019 | Erica G. | Parametrización
#24/07/2017 | ERICA G. | ARCHIVO CREADO 
#####################################################################################################
require ('head_listar.php');
require ('Conexion/conexion.php');
$anno = $_SESSION['anno'];  
?>
<link rel="stylesheet" href="css/select2.css">
<link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
<title>Listar Homologación Conceptos</title><!-- ./Titulo  de la pagina -->
</head><!-- ./head -->
<body><!-- body -->
    <div class="container-fluid"><!-- container-fluid -->
        <div class="row content"><!-- row content -->
            <?php require ('menu.php'); ?><!-- ./menu -->
            <div class="col-sm-10"><!-- col-sm-10 -->
                <h2 id="forma-titulo3" align="center" style="margin-top:0px">Homologación Conceptos</h2><!-- ./h2 -->
                <div class="table-responsive"><!-- table-responsive -->
                    <div class="table responsive"><!-- table-responsive -->
                        <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%"><!-- table -->
                            <thead><!-- thead -->
                                <tr><!-- tr-->
                                    <td class="oculto"></td><!-- ./td -->
                                    <td width="30px" align="center"></td><!-- ./td -->
                                    <td><strong>Concepto Nómina</strong></td><!-- ./td -->
                                    <td><strong>Concepto Financiero</strong></td><!-- ./td -->
                                    <td><strong>Rubro Presupuestal</strong></td><!-- ./td -->
                                    <td><strong>Grupo de Gestión</strong></td>
                                    <td><strong>Tercero</strong></td>
                                    <td><strong>Tipo</strong></td>
                                </tr><!-- ./tr -->
                                <tr><!-- tr-->
                                    <th class="oculto"></th><!-- ./th -->
                                    <th width="30px" align="center"></th><!-- ./th -->
                                    <th>Concepto Nómina</th><!-- ./th -->
                                    <th>Concepto Financiero</th><!-- ./th -->
                                    <th>Rubro Presupuestal</th><!-- ./th -->
                                    <th>Grupo Gestión</th>
                                    <th>Tercero</th>
                                    <th>Tipo</th>
                                </tr><!-- ./tr -->
                            </thead><!-- ./thead -->
                            <tbody><!-- tbody -->
                                <?php
                                ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                                //Consulta para obtener los valores de la tabla
                                ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                                $sql = "SELECT DISTINCT  cnf.id_unico, "
                                        . "CONCAT(cn.codigo,' - ',LOWER(cn.descripcion)), "
                                        . "LOWER(c.nombre),"
                                        . "CONCAT( rp.codi_presupuesto,' ',LOWER(rp.nombre), ' - ', LOWER(f.nombre)), "
                                        . "gg.nombre, "
                                        . "IF(CONCAT_WS(' ',
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
                                      tr.apellidodos)) AS NOMBRE, tr.numeroidentificacion, 
                                      tr.digitoverficacion, gg.id_unico, cnf.tipo "
                                        . "FROM gn_concepto_nomina_financiero cnf "
                                        . "LEFT JOIN gn_concepto cn ON cnf.concepto_nomina = cn.id_unico "
                                        . "LEFT JOIN gf_concepto_rubro cf ON cnf.concepto_financiero = cf.id_unico "
                                        . "LEFT JOIN gf_concepto c ON cf.concepto = c.id_unico "
                                        . "LEFT JOIN gf_rubro_fuente rf ON rf.id_unico = cnf.rubro_fuente  "
                                        . "LEFT JOIN gf_rubro_pptal rp ON cf.rubro = rp.id_unico "
                                        . "LEFT JOIN gf_fuente f ON rf.fuente = f.id_unico "
                                        . "LEFT JOIN gn_grupo_gestion gg ON cnf.grupo_gestion = gg.id_unico "
                                        . "LEFT JOIN gf_tercero tr ON cnf.tercero = tr.id_unico "
                                        . "WHERE cnf.parametrizacionanno = $anno";
                                $result = $mysqli->query($sql);
                                ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                                //Impresión de los valores retornado por la consulta
                                ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                                while ($row = mysqli_fetch_row($result)) {
                                    if (empty($row[7])) {
                                        $numeroI = $row[6];
                                    } else {
                                        $numeroI = $row[6] . '-' . $row[7];
                                    }
                                    ?> 
                                    <tr>
                                        <td style="display: none;"><?php echo $row[0] ?></td>
                                        <td>
                                            <a  href="#" onclick="javascript:eliminar(<?php echo $row[0]; ?>);"><i title="Eliminar" class="glyphicon glyphicon-trash"></i></a>
                                            <a href="GN_HOMOLOGACION_CONCEPTOS.php?gg=<?php echo $row[8].'&id='.$row[0]?>"><i title="Modificar" class="glyphicon glyphicon-edit" ></i></a>
                                        </td>
                                        <td><?php echo ucwords((($row[1]))); ?></td>
                                        <td><?php echo ucwords(mb_strtolower(($row[2]))); ?></td>
                                        <td><?php echo ucwords(mb_strtolower(($row[3]))); ?></td>
                                        <td><?php echo ucwords(mb_strtolower(($row[4]))); ?></td>
                                        <td><?php echo ucwords(mb_strtolower(($row[5]))) . ' - ' . $numeroI; ?></td>
                                        <td><?php if($row[9]==1){echo 'Pública';}elseif($row[9]==2){echo 'Privada';} else { echo '';} ?></td>
                                    </tr>
<?php } ?>
                            </tbody><!-- ./tbody -->
                        </table><!-- ./table -->


                        <div class="text-right"><!-- text-right -->
                            <button style="margin-right:30px; margin-top: 20px;" onclick="reporteExcel()" class="btn sombra btn-primary" title="Generar reporte Excel"><i class="fa fa-file-excel-o" aria-hidden="true"></i></button>
                            <button onclick="javaScript:grupog()" class="btn btn-primary " style=" box-shadow: 0px 2px 5px 1px gray;border-color: #1075C1; margin-top: 10px;margin-left:-20px; margin-right:4px">Registrar Nuevo</button>
                        </div><!-- /ext-right -->
                    </div><!-- ./table-responsive -->
                </div><!-- ./table-responsive -->
            </div><!-- ./col-sm-10 -->
<?php require ('footer.php'); ?><!-- ./footer -->
        </div><!-- ./row content -->
    </div><!-- ./container-fluid -->
    <div class="modal fade" id="myModal" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>¿Desea eliminar el registro seleccionado?</p>
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
                    <button type="button" id="ver1" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" onclick="reload_page()">Aceptar</button>
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
    </div>
</body><!-- ./body-->
</html><!-- ./html -->

<script type="text/javascript">
      function eliminar(id)
      {
         var result = '';
         $("#myModal").modal('show');
         $("#ver").click(function(){
              $("#mymodal").modal('hide');
              var form_data = {action:2,id:id};
              $.ajax({
                  type:"POST",
                  url:"jsonPptal/gn_nomina_financieraJson.php",
                  data: form_data,
                  success: function (data) {
                      console.log(data);
                  result = JSON.parse(data);
                    if (result == true)
                        $("#myModal1").modal('show');
                    else
                        $("#myModal2").modal('show');
                }
            });
        });
    }
</script>
<script type="text/javascript">

    $('#ver1').click(function () {
        document.location.reload();
    });

</script>

<script type="text/javascript">

    $('#ver2').click(function () {
        document.location.reload();
    });

</script>
    
<div class="modal fade" id="mdlgg" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Grupo de Gestión</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <label class="form_label"><strong style="color:#03C1FB;">*</strong>Grupo de Gestión: </label>
                <select name="grupog" id="grupog" class="select2_single form-control input-sm" title="Grupo de Gestión" style="width:250px;">
                    <option value="" >Grupo de Gestión</option>
                        <?php
                        $row1 = "SELECT id_unico, LOWER(nombre) FROM gn_grupo_gestion  ORDER BY nombre ASC";
                        $row1 = $mysqli->query($row1);

                        while ($row = mysqli_fetch_row($row1)) {
                            echo "<option value='$row[0]'>". ucwords($row[1])."</option>";
                        }
                        ?>
                </select> 
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="guardarG" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                <button type="button" id="cancelarG" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Cancelar</button>
            </div>
        </div>
    </div>
</div>
<link rel="stylesheet" href="css/bootstrap-theme.min.css">
<script src="js/bootstrap.min.js"></script>
<script type="text/javascript" src="js/select2.js"></script>

<script>
    $(".select2_single").select2();
</script>
<script>
    function reporteExcel() {

        window.open('informes/generar_INF_CONCEPTOS_HOMOLOGACION.php');
    }
    function grupog(){
        $("#mdlgg").modal("show");
    }
    $("#guardarG").click(function(){
        var gg = $("#grupog").val();
        if(gg!=''){
            window.location ='GN_HOMOLOGACION_CONCEPTOS.php?gg='+gg;
        } else {
            $("#mdlgg").modal("hide");
        }
    })
    $("#cancelarG").click(function(){
        $("#mdlgg").modal("hide");
    })
</script>  