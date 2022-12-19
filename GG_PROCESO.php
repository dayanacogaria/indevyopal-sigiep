<?php
require_once ('Conexion/conexion.php');
require_once 'head_listar.php';

?>
<!-- select2 -->
<link href="css/select/select2.min.css" rel="stylesheet">
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<style>
label#responsable-error, #estado-error, #tipoProceso-error, #fecha-error, #identificador-error{
    display: block;
    color: #155180;
    font-weight: normal;
    font-style: italic;

}
body{
    font-size: 12px;
}
 table.dataTable thead th,table.dataTable thead td
  {
    padding: 1px 18px;
  }

  table.dataTable tbody td,table.dataTable tbody td
  {
    padding: 1px;
  }
  .dataTables_wrapper .ui-toolbar
  {
    padding: 2px;
    font-size: 12px;
  }
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
<script type="text/javascript">
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
            changeYear: true,
            showMonthAfterYear: false,
            yearSuffix: ''
        };
        $.datepicker.setDefaults($.datepicker.regional['es']);
        $("#fecha").datepicker({changeMonth: true}).val(fecAct);
    });
    </script>
<title>Proceso</title>
</head>
<div class="container-fluid text-center">
    <div class="row content">
    <?php require_once 'menu.php'; ?>
        <div class="col-sm-9 text-left" style="margin-left: -16px;margin-top: -22px; ">
            <h2 class="tituloform" align="center" >Proceso</h2>
             <?php
                if(empty($_GET['id'])){
                #Estado
                $estado = "SELECT id_unico, nombre FROM gg_estado_proceso ORDER BY nombre ASC";
                $estado= $mysqli->query($estado);

                #Tipo Proceso
                $tipoProceso = "SELECT id_unico, identificador, nombre FROM gg_tipo_proceso ORDER BY identificador ASC";
                $tipoProceso= $mysqli->query($tipoProceso);

                #Tercero
                $responsable = "SELECT DISTINCT CONCAT(t.nombreuno,' ', t.nombredos,' ',t.apellidouno,' ',t.apellidodos) AS NOMBRE , "
                        . "t.id_unico, t.numeroidentificacion "
                        . "FROM gg_gestion_responsable gt "
                        . "LEFT JOIN  gf_tercero t ON t.id_unico = gt.tercero_uno OR t.id_unico = gt.tercero_dos "
                        . "ORDER BY NOMBRE ASC";
                $responsable = $mysqli->query($responsable);

                #Tipo Proceso
                $proceso = "SELECT p.id_unico, "
                        . "p.estado, "
                        . "ep.nombre, "
                        . "p.tipo_proceso, "
                        . "tp.identificador, "
                        . "tp.nombre "
                        . "FROM gg_proceso p  "
                        . "LEFT JOIN gg_estado_proceso ep ON p.estado = ep.id_unico "
                        . "LEFT JOIN gg_tipo_proceso tp ON tp.id_unico = p.tipo_proceso ";
                $proceso= $mysqli->query($proceso);
            ?>
            <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px; margin-top: -5px;" class="client-form">
                <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="jsonProcesos/registrar_GG_PROCESOJson.php">
                    <p align="center" style="margin-bottom: 20px; margin-top: 5px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                    <div class="form-group form-inline" style="margin-left:0px; margin-top:-10px">
                        <!--Identificador-->
                        <div class="form-group form-inline" style="margin-left:10px; margin-top:8px">
                            <label style="width:100px;" for="identificador" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Identificador:</label>
                            <input style="width:200px; height: 30px" type="text" name="identificador" id="identificador" class="form-control" onkeypress="return txtValida(event,'sin_espcio')" maxlength="50" title="Ingrese el identificador"  placeholder="Identificador" required>
                        </div>
                        <!--Tipo Proceso-->
                        <div class="form-group form-inline" style="margin-left:10px">
                            <label style="width:100px;" for="tipoProceso" class="control-label"><strong style="color:#03C1FB;">*</strong>Tipo Proceso:</label>
                            <input type="hidden" name="tipoProceso" id="tipoProceso" required="required" title="Seleccione Tipo Proceso">
                            <select style="width:200px;" name="tipoProceso1" id="tipoProceso1" required="required" class="select2_single form-control" title="Seleccione Tipo Proceso" required="required" onchange="llenar2();">
                                <option value="">Tipo Proceso</option>
                                <?php while($row2 = mysqli_fetch_row($tipoProceso)){
                                    #VERIFICAR SI TIENE FLUJO PROCESAL O NO 
                                    $flujoP = "SELECT * FROM gg_flujo_procesal WHERE tipo_proceso ='$row2[0]'";
                                    $flujoP = $mysqli->query($flujoP);
                                    if(mysqli_num_rows($flujoP)>0) { ?>
                                <option value="<?php echo $row2[0] ?>"><?php echo ucwords((strtolower($row2[1].' - '.$row2[2])));} } ?></option>;
                            </select> 
                        </div>
                        <!--Estado-->
                        <div class="form-group form-inline"style="margin-left:10px" >
                            <label style="width:100px;" for="estado" class="control-label"><strong style="color:#03C1FB;">*</strong>Estado:</label>
                            <input type="hidden" name="estado" id="estado" required="required" title="Seleccione estado">
                            <select  style="width:200px;"class="select2_single form-control" name="estado1" id="estado1" required="required"  title="Seleccione Estado" required="required" onchange="llenar();">
                                <option value="">Estado</option>
                                <?php while($row1 = mysqli_fetch_row($estado)){?>
                                <option value="<?php echo $row1[0] ?>"><?php echo ucwords((strtolower($row1[1])));}?></option>;
                            </select> 
                        </div>
                        
                    </div>
                    <div class="form-group form-inline" style="margin-left:0px; margin-top:-10px">
                        <!--Tercero-->
                        <div class="form-group form-inline" style="margin-left:10px;">
                            <label style="width:100px;" for="responsable" class="control-label"><strong style="color:#03C1FB;">*</strong>Responsable:</label>
                            <input type="hidden" name="responsable" id="responsable" required="required" title="Seleccione Responsable">
                            <select style="width:200px;" name="responsable1" id="responsable1" required="required" style="margin-left: 10px; margin-right: 10px;" class="select2_single form-control" title="Seleccione Responsable" required="required" onchange="llenar3();">
                                <option value="">Responsable</option>
                                <?php while($row3 = mysqli_fetch_row($responsable)){?>
                                <option value="<?php echo $row3[1] ?>"><?php echo ucwords((strtolower($row3[0].'('.$row3[2].')')));}?></option>;
                            </select> 
                        </div>
                        <!--FECHA-->
                        <div class="form-group form-inline" style="margin-left:10px">
                            <label style="width:100px;" for="fecha" class="control-label"><strong style="color:#03C1FB;">*</strong>Fecha:</label>
                            <input type="text" name="fecha" id="fecha" class="form-control" style="width:200px; display: inline; margin-top: 10px" required="required" title="Seleccione fecha" readonly="true">
                        </div>
                        <!--Proceso-->
                        <div class="form-group form-inline" style="margin-left:10px">
                            <label style="width:100px;" for="proceso" class="control-label">Proceso Asociado:</label>
                            <select style="width:200px;" name="proceso" id="proceso"  style="margin-left: 10px; margin-right: 10px;"   class="select2_single form-control col-sm-1" title="Seleccione Proceso Asociado">
                                <option value="">Proceso Asociado</option>
                                <?php while($row4 = mysqli_fetch_row($proceso)){?>
                                <option value="<?php echo $row4[0] ?>"><?php echo ucwords((strtolower($row4[4].' - '.$row4[5].' ( '.$row4[2].' )')));}?></option>;
                            </select> 
                        </div>
                        <div class="form-group form-inline text-right" style="margin-top: 20px;  margin-left: -25px">
                            <button  type="submit" class="btn btn-primary sombra" title="Guardar"> <i class="glyphicon glyphicon-floppy-disk" ></i></button>
                        </div>
                    </div>
                </form>
            </div>
            <div align="center" class="table-responsive" style="margin-left: 5px; margin-right: 5px; margin-top: 10px; margin-bottom: 5px;">          
                <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                    <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <td style="display: none;">Identificador</td>
                                <td width="30px"></td>
                                <td><strong>Característica</strong></td>
                                <td><strong>Valor</strong></td>
                                <td><strong>Unidad</strong></td>
                            </tr>
                            <tr>
                                <th style="display: none;">Identificador</th>
                                <th width="7%"></th>
                                <th>Característica</th>
                                <th>Valor</th>
                                <th>Unidad</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
            <script>
              function llenar(){
                    var estado = document.getElementById('estado1').value;
                    document.getElementById('estado').value= estado;

              }
              </script>
              <script>
              function llenar2(){
                  var tipoProceso = document.getElementById('tipoProceso1').value;
                  document.getElementById('tipoProceso').value= tipoProceso;
              }
              function llenar3(){
                  var responsable = document.getElementById('responsable1').value;
                  document.getElementById('responsable').value= responsable;
              }

            </script>
            <?php } else { 
            #REGISTRO A MODIFICAR
            $id=$_GET['id'];
            $_SESSION['url'] = 'GG_PROCESO.php?id='.$id;
            $query = "SELECT p.id_unico, "
                    . "p.estado, "
                    . "ep.nombre, "
                    . "p.tipo_proceso, "
                    . "tp.identificador, "
                    . "tp.nombre, "
                    . "p.tercero, "
                    . "CONCAT(t.nombreuno, ' ', t.nombredos,' ', t.apellidouno,' ', t.apellidodos, '(',t.numeroidentificacion, ')') AS TERCERO, "
                    . "p.proceso, "
                    . "epp.nombre, "
                    . "tpp.identificador, "
                    . "tpp.nombre, "
                    . "p.fecha, p.identificador "
                    . "FROM gg_proceso p  "
                    . "LEFT JOIN gg_estado_proceso ep ON p.estado = ep.id_unico "
                    . "LEFT JOIN gg_tipo_proceso tp ON tp.id_unico = p.tipo_proceso "
                    . "LEFT JOIN gf_tercero t ON p.tercero = t.id_unico "
                    . "LEFT JOIN gg_proceso pr ON p.proceso = pr.id_unico "
                    . "LEFT JOIN gg_estado_proceso epp ON pr.estado = epp.id_unico "
                    . "LEFT JOIN gg_tipo_proceso tpp ON tpp.id_unico = pr.tipo_proceso "
                    . "WHERE md5(p.id_unico)='$id'"; 
            $resultado = $mysqli->query($query);
            $row=  mysqli_fetch_row($resultado);
            #Estado
            $estado = "SELECT id_unico, nombre FROM gg_estado_proceso WHERE id_unico != $row[1] ORDER BY nombre ASC";
            $estado= $mysqli->query($estado);

            #Tipo Proceso
            $tipoProceso = "SELECT id_unico, identificador, nombre FROM gg_tipo_proceso WHERE id_unico != $row[3] ORDER BY identificador ASC";
            $tipoProceso= $mysqli->query($tipoProceso);

            #Tercero
            $responsable = "SELECT DISTINCT CONCAT(t.nombreuno,' ', t.nombredos,' ',t.apellidouno,' ',t.apellidodos) AS NOMBRE , "
                    . "t.id_unico, t.numeroidentificacion "
                    . "FROM gg_gestion_responsable gt "
                    . "LEFT JOIN  gf_tercero t ON t.id_unico = gt.tercero_uno OR t.id_unico = gt.tercero_dos "
                    . "WHERE t.id_unico != $row[6] ORDER BY NOMBRE ASC";
            $responsable = $mysqli->query($responsable);

            #Tipo Proceso
            $proceso = "SELECT p.id_unico, "
                    . "p.estado, "
                    . "ep.nombre, "
                    . "p.tipo_proceso, "
                    . "tp.identificador, "
                    . "tp.nombre "
                    . "FROM gg_proceso p  "
                    . "LEFT JOIN gg_estado_proceso ep ON p.estado = ep.id_unico "
                    . "LEFT JOIN gg_tipo_proceso tp ON tp.id_unico = p.tipo_proceso "
                    . "WHERE p.id_unico != $row[8] AND p.id_unico !=$row[0] ORDER BY tp.identificador ASC";
            $proceso= $mysqli->query($proceso);
            
            if(empty($row[12])|| $row[12]=='0000-00-00'){ 
            ?>
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
                    $("#fecha").datepicker({changeMonth: true}).val(fecAct);
                });
    
            </script>
            <?php } else { ?>
            <script>
                $(function(){
                    var fechaI = '<?php echo date("d/m/Y", strtotime($row[12]));?>';
                    $("#fecha").datepicker({changeMonth: true}).val(fechaI);
                });
    
            </script>
            <?php } ?>
            <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px; margin-top: -5px;" class="client-form">
                <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="jsonProcesos/modificar_GG_PROCESOJson.php">
                    <p align="center" style="margin-bottom: 20px; margin-top: 5px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                    <div class="form-group form-inline" style="margin-left:0px; margin-top:-10px">
                        <input type="hidden" id="id" name="id" value="<?php echo $row[0]?>">
                        <!--Identificador-->
                        <div class="form-group form-inline" style="margin-left:10px; margin-top:8px">
                            <label style="width:100px;" for="identificador" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Identificador:</label>
                            <input style="width:200px; height: 30px" type="text" name="identificador" id="identificador" class="form-control" onkeypress="return txtValida(event,'sin_espcio')" maxlength="50" title="Ingrese el identificador"  placeholder="Identificador" required value="<?php echo $row[13]?>">
                        </div>
                        <!--Tipo Proceso-->
                        <div class="form-group form-inline" style="margin-left:10px">
                            <label style="width:100px;" for="tipoProceso" class="control-label"><strong style="color:#03C1FB;">*</strong>Tipo Proceso:</label>
                            <input type="hidden" name="tipoProceso" id="tipoProceso" value="<?php echo $row[3]?>" required="required" title="Seleccione Tipo Proceso">
                            <?php $busCar= "SELECT * FROM gg_caracteristica_x WHERE proceso = '$row[0]'";
                            $busCar= $mysqli->query($busCar);
                            $busCar = mysqli_num_rows($busCar);
                            $busDet= "SELECT * FROM gg_detalle_proceso WHERE proceso = '$row[0]'";
                            $busDet= $mysqli->query($busDet);
                            $busDet = mysqli_num_rows($busDet);
                            if($busCar>0 || $busDet>0) { ?>
                            <select style="width:200px;" disabled="true" name="tipoProceso1" id="tipoProceso1" class="select2_single form-control" title="Seleccione Tipo Proceso" required="required" onchange="llenar2();">
                                <option value="<?php echo $row[3]?>"><?php echo ucwords(strtolower($row[4].' - '.$row[5]))?></option>
                                <?php while($row2 = mysqli_fetch_row($tipoProceso)){?>
                                <option value="<?php echo $row2[0] ?>"><?php echo ucwords((strtolower($row2[1].' - '.$row2[2])));}?></option>;
                            </select> 
                            <?php } else { ?>
                            <select style="width:200px;" name="tipoProceso1" id="tipoProceso1" class="select2_single form-control" title="Seleccione Tipo Proceso" required="required" onchange="llenar2();">
                                <option value="<?php echo $row[3]?>"><?php echo ucwords(strtolower($row[4].' - '.$row[5]))?></option>
                                <?php while($row2 = mysqli_fetch_row($tipoProceso)){?>
                                <option value="<?php echo $row2[0] ?>"><?php echo ucwords((strtolower($row2[1].' - '.$row2[2])));}?></option>;
                            </select> 
                            <?php } ?>
                        </div>
                        <!--Estado-->
                        <div class="form-group form-inline"style="margin-left:4px" >
                            <label style="width:100px;" for="estado" class="control-label"><strong style="color:#03C1FB;">*</strong>Estado:</label>
                            <input type="hidden" name="estado" id="estado" value="<?php echo $row[1]?>" required="required" title="Seleccione estado">
                            <select  style="width:200px;"class="select2_single form-control" name="estado1" id="estado1" required="required"  title="Seleccione Estado" required="required" onchange="llenar();">
                                <option value="<?php echo $row[1]?>"><?php echo ucwords((strtolower($row[2])));?></option>
                                <?php while($row1 = mysqli_fetch_row($estado)){?>
                                <option value="<?php echo $row1[0] ?>"><?php echo ucwords((strtolower($row1[1])));}?></option>;
                            </select> 
                        </div>
                        
                    </div>
                    <div class="form-group form-inline" style="margin-left:0px; margin-top:-10px">
                        <!--Tercero-->
                        <div class="form-group form-inline" style="margin-left:7px; ">
                            <label style="width:100px;" for="responsable" class="control-label"><strong style="color:#03C1FB;">*</strong>Responsable:</label>
                            <input type="hidden" name="responsable" id="responsable" value="<?php echo $row[6]?>" required="required" title="Seleccione Responsable">
                            <select style="width:200px;" name="responsable1" id="responsable1" required="required" style="margin-left: 10px; margin-right: 10px;" class="select2_single form-control" title="Seleccione Responsable" required="required" onchange="llenar3();">
                                <option value="<?php echo $row[6]?>"><?php echo ucwords(strtolower($row[7]))?></option>
                                <?php while($row3 = mysqli_fetch_row($responsable)){?>
                                <option value="<?php echo $row3[1] ?>"><?php echo ucwords((strtolower($row3[0].'('.$row3[2].')')));}?></option>;
                            </select> 
                        </div>
                        <!--FECHA-->
                        <div class="form-group form-inline" style="margin-left:10px">
                            <label style="width:100px;" for="fecha" class="control-label"><strong style="color:#03C1FB;">*</strong>Fecha:</label>
                            <input type="text" name="fecha" id="fecha" class="form-control" style="width:200px; display: inline; margin-top: 10px" required="required" title="Seleccione fecha" readonly="true" value="<?php echo $row[12]?>">
                        </div>
                        <!--Proceso-->
                        <div class="form-group form-inline" style="margin-left:7px">
                            <label style="width:100px;" for="proceso" class="control-label">Proceso Asociado:</label>
                            <select style="width:200px;" name="proceso" id="proceso"  style="margin-left: 10px; margin-right: 10px;"   class="select2_single form-control col-sm-1" title="Seleccione Proceso Asociado">
                                <?php if (empty($row[8])) { ?>
                                <option value="">-</option>
                                <?php while($row4 = mysqli_fetch_row($proceso)){?>
                                <option value="<?php echo $row4[0] ?>"><?php echo ucwords((strtolower($row4[4].' - '.$row4[5].' ( '.$row4[2].' )')));}?></option>
                                <?php } else  { ?>
                                <option value="<?php echo $row[8]?>"><?php echo ucwords((strtolower($row[10].' - '.$row[11].' ( '.$row[9].' )'))); ?></option>
                                <?php while($row4 = mysqli_fetch_row($proceso)){?>
                                <option value="<?php echo $row4[0] ?>"><?php echo ucwords((strtolower($row4[4].' - '.$row4[5].' ( '.$row4[2].' )')));}?></option>
                                <?php } ?>
                            </select> 
                        </div>
                        <div class="form-group form-inline" style="margin-top: 20px; margin-left: -26px">
                            <button  type="submit" class="btn btn-primary sombra" title="Modificar"> <i class="glyphicon glyphicon-floppy-disk" ></i></button>
                        </div>
                    </div>
                </form>
            </div>
            <?php 
                #LISTAR
                    $listar = "SELECT
                            pc.id_unico,
                            p.id_unico,
                            tp.id_unico,
                            tp.identificador,
                            tp.nombre,
                            c.id_unico,
                            c.nombre,
                            c.unidad,
                            td.nombre,
                            cx.id_unico,
                            cx.descripcion, 
                            uf.nombre 
                          FROM
                            gg_proceso p
                          LEFT JOIN
                            gg_tipo_proceso tp ON p.tipo_proceso = tp.id_unico
                          LEFT JOIN
                            gg_confi_caracteristica pc ON pc.tipo_proceso = tp.id_unico
                          LEFT JOIN
                            gg_caracteristica c ON pc.caracteristica = c.id_unico
                          LEFT JOIN
                            gf_tipo_dato td ON c.tipo_dato = td.id_unico
                          LEFT JOIN
                            gg_caracteristica_x cx ON cx.proceso = p.id_unico AND cx.caracteristica = c.id_unico 
                          LEFT JOIN 
                            gf_unidad_factor uf ON c.unidad = uf.id_unico 
                         WHERE p.id_unico = '$row[0]'";
                $listar = $mysqli->query($listar);
            ?>
            <div align="center" class="table-responsive" style="margin-left: 5px; margin-right: 5px; margin-top: 10px; margin-bottom: 5px;">          
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                        <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <td style="display: none;">Identificador</td>
                                    <td width="30px"></td>
                                    <td><strong>Característica</strong></td>
                                    <td><strong>Valor</strong></td>
                                    <td><strong>Unidad</strong></td>
                                </tr>
                                <tr>
                                    <th style="display: none;">Identificador</th>
                                    <th width="7%"></th>
                                    <th>Característica</th>
                                    <th>Valor</th>
                                    <th>Unidad</th>
                                </tr>
                            </thead>
                            <tbody>
                            
                             <?php 
                             
                             while($rowlistar = mysqli_fetch_row($listar)) { 
                                if(!empty($rowlistar[0])){ ?>
                                <tr>
                                    <td style="display: none;">
                                        <input type="hidden" id="proceso<?php echo $rowlistar[0]?>" name="proceso" value="<?php echo $rowlistar[1]?>">
                                        <input type="hidden" id="tipod<?php echo $rowlistar[0]?>" name="tipoD" value="<?php echo $rowlistar[8]?>">
                                        <input type="hidden" id="carac<?php echo $rowlistar[0]?>" name="carac" value="<?php echo $rowlistar[5]?>">
                                    <td>
                                        
                                        <?php if(empty($rowlistar[9])) { ?>
                                        <a href="#" onclick="guardar(<?php echo $rowlistar[0]?>)"><i title="Guardar" class="glyphicon glyphicon-floppy-disk" ></i></a>
                                        <?php } else { ?>
                                        <a  href="#" onclick="javascript:eliminar(<?php echo $rowlistar[9];?>);"><i title="Eliminar" class="glyphicon glyphicon-trash"></i></a>
                                        <a href="#" onclick="modificar(<?php echo $rowlistar[9]?>,'<?php echo $rowlistar[10];?>', '<?php echo $rowlistar[8]?>')"><i title="Modificar" class="glyphicon glyphicon-edit" ></i></a>
                                        <?php } ?>
                                    </td>
                                    <td><?php echo ucwords(strtolower(($rowlistar[6])));?></td>
                                    <td>
                                    <?php if(empty($rowlistar[9])) {
                                        switch ($rowlistar[8]){
                                            case('Alfabetico'):?>
                                               <input type="text" name="valorA" id="valorA<?php echo $rowlistar[0]?>" title="Ingrese el valor" class="form-control col-sm-2"  style="width:200px; height: 20px " onkeypress="return txtValida(event,'car')" maxlength="500" required="required" >
                                            <?php break;
                                            case('Alfanumerico'):?>
                                               <input type="text" name="valorAn" id="valorAn<?php echo $rowlistar[0]?>" title="Ingrese el valor" class="form-control col-sm-2"  style="width:200px; height: 20px " onkeypress="return txtValida(event,'num_car')" maxlength="500" required="required" >
                                            <?php break;
                                            case('Texto abierto'):?>
                                               <input type="text" name="valorTa" id="valorTa<?php echo $rowlistar[0]?>" title="Ingrese el valor" class="form-control col-sm-2"  style="width:200px; height: 20px " onkeypress="return txtValida(event)" maxlength="500" required="required" >
                                            <?php break;
                                            case('Numerico'):?>
                                               <input type="text" name="valorN" id="valorN<?php echo $rowlistar[0]?>" title="Ingrese el valor" class="form-control col-sm-2"  style="width:200px; height: 20px " onkeypress="return txtValida(event,'num')" maxlength="500" required="required" >
                                            <?php break;
                                            case('Booleano'):?>
                                               <input type="radio" name="valorB<?php echo $rowlistar[0]?>" id="valorB<?php echo $rowlistar[0]?>" title="Escoja el valor" value="Si">Sí
                                               <input  type="radio" name="valorB<?php echo $rowlistar[0]?>" id="valorB<?php echo $rowlistar[0]?>" title="Escoja el valor" value="No" checked>No
                                            <?php break;
                                            case('Fecha'):?>
                                               <script>
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
                                                   $(function(){
                                                        $.datepicker.regional['es'] = {
                                                        closeText: 'Cerrar',
                                                        prevText: 'Anterior',
                                                        nextText: 'Siguiente',
                                                        currentText: 'Hoy',
                                                        monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
                                                        monthNamesShort: ['Enero','Febrero','Marzo','Abril', 'Mayo','Junio','Julio','Agosto','Septiembre', 'Octubre','Noviembre','Diciembre'],
                                                        dayNames: ['Domingo', 'Lunes', 'Martes', 'Mi&eacute;rcoles', 'Jueves', 'Viernes', 'S&aacute;bado'],
                                                        dayNamesShort: ['Dom','Lun','Mar','Mi�','Juv','Vie','S&aacute;b'],
                                                        dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','S&aacute;'],
                                                        weekHeader: 'Sm',
                                                        dateFormat: 'dd/mm/yy',
                                                        firstDay: 1,
                                                        isRTL: false,
                                                        changeYear:true,
                                                        showMonthAfterYear: false,
                                                        yearSuffix: ''
                                                    };
                                                    $.datepicker.setDefaults($.datepicker.regional['es']);
                                                    $("#valorF<?php echo $rowlistar[0]?>").datepicker({changeMonth: true,}).val(fecAct);

                                            });
                                            </script>
                                               <input type="text" name="valorF" id="valorF<?php echo $rowlistar[0]?>" title="Ingrese el valor" class="form-control col-sm-2"  style="width:200px; height: 20px "  required="required" >
                                            <?php break;
                                        }
                                        ?>
                                    <?php } else {
                                            echo ucwords(strtolower($rowlistar[10]));
                                         } ?>
                                    </td>
                                    <td><?php echo ucwords(strtolower(($rowlistar[11])));?></td>
                                </tr>
                             <?php } } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!--Modales registro -->
                <div class="modal fade" id="myModal" role="dialog" align="center" >
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div id="forma-modal" class="modal-header">
                                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                            </div>
                            <div class="modal-body" style="margin-top: 8px">
                                <p>Información guardada correctamente.</p>
                            </div>
                            <div id="forma-modal" class="modal-footer">
                                <button type="button" id="ver" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
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
                                <p>El registro ingresado ya existe.</p>
                            </div>
                            <div id="forma-modal" class="modal-footer">
                                <button type="button" id="ver1" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
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
                                <p>No se ha podido guardar la informaci&oacuten.</p>
                            </div>
                            <div id="forma-modal" class="modal-footer">
                                <button type="button" id="ver2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Fin modales registro-->
                <!--Modales eliminar-->
                <div class="modal fade" id="myModalEliminar" role="dialog" align="center" >
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div id="forma-modal" class="modal-header">
                                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                            </div>
                            <div class="modal-body" style="margin-top: 8px">
                                <p>¿Desea eliminar el registro seleccionado?</p>
                            </div>
                            <div id="forma-modal" class="modal-footer">
                                <button type="button" id="verE" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                                <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="myModal3" role="dialog" align="center" >
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div id="forma-modal" class="modal-header">
                                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                            </div>
                            <div class="modal-body" style="margin-top: 8px">
                                <p>Información eliminada correctamente</p>
                            </div>
                            <div id="forma-modal" class="modal-footer">
                                <button type="button" id="ver3" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="myModal4" role="dialog" align="center" >
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div id="forma-modal" class="modal-header">
                                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                            </div>
                            <div class="modal-body" style="margin-top: 8px">
                                <p>No se pudo eliminar la información, el registro seleccionado esta siendo usado por otra dependencia.</p>
                            </div>
                            <div id="forma-modal" class="modal-footer">
                                <button type="button" id="ver4" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Modal modificar-->
                <div class="modal fade" id="myModalModificar" role="dialog" align="center" >
                    <div class="modal-dialog" >
                        <div class="modal-content client-form1">
                            <div id="forma-modal" class="modal-header">       
                                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Modificar</h4>
                            </div>
                            <div class="modal-body ">
                                <form  name="formM" id="formM" method="POST" action="javascript:modificarItem()" style="margin-top:10px;">
                                    <div class="form-group" >
                                        <!-- Alfabetico-->
                                        <div id="valorDA" style="display: none; margin-top: -30px; " >
                                            <label for="valorAm" style="display:inline-block; width:140px;margin-right:180px; margin-top:20px;"><strong style="color:#03C1FB;">*</strong>Valor:</label>
                                            <input type="text" name="valorAm" id="valorAm"  title="Ingrese el valor" class="form-control col-sm-2"  style="width:250px; height:40px; margin-left:230px; margin-top:-30px"  onkeypress="return txtValida(event,'car')" maxlength="500"><br/>
                                        </div>
                                        <!-- Alfanumerico -->
                                        <div id="valorDAn" style="display: none; margin-top: -30px; " >
                                            <label for="valorAnm" style="display:inline-block; width:140px;margin-right:180px;  margin-top:20px;"><strong style="color:#03C1FB;">*</strong>Valor:</label>
                                            <input type="text" name="valorAnm" id="valorAnm"  title="Ingrese el valor" class="form-control col-sm-2"  style="width:250px; height:40px; margin-left:230px; margin-top:-30px" onkeypress="return txtValida(event,'num_car')" maxlength="500">
                                        </div>
                                        <!-- Texto abierto-->
                                        <div id="valorDTa" style="display: none;  margin-top: -30px; " >
                                            <label for="valorTam" style="display:inline-block; width:140px;margin-right:180px; margin-top:20px;"><strong style="color:#03C1FB;">*</strong>Valor:</label>
                                            <input type="text" name="valorTam" id="valorTam" title="Ingrese el valor" class="form-control col-sm-2"  style="width:250px; height:40px; margin-left:230px; margin-top:-30px" maxlength="500" onkeypress="return txtValida(event)"  ><br/>
                                        </div>
                                        <!--Numérico-->
                                        <div id="valorDN" style="display: none; margin-top: -30px; " >
                                          <label for="valorNm" style="display:inline-block; width:140px;margin-right:180px; margin-top:20px;"><strong style="color:#03C1FB;">*</strong>Valor:</label>
                                          <input type="text" name="valorNm" id="valorNm" title="Ingrese el valor" class="form-control col-sm-2"  style="width:250px; height:40px; margin-left:230px; margin-top:-30px" onkeypress="return txtValida(event,'num')" maxlength="500" ><br/>
                                        </div>
                                        <!--Booleano Obligatorio-->
                                        <div id="valorDB" style="display: none;" >
                                            <label for="valorBm" style="display:inline-block; width:140px;"><strong style="color:#03C1FB;">*</strong>Valor:</label>
                                            <div style=" display:inline; margin-right:110px" >
                                              <input  type="radio" name="valorBm" id="valorBm"  value="Si" >SI
                                              <input  type="radio" name="valorBm" id="valorBm" value="No" checked>NO 
                                            </div>
                                        </div>
                                        <!--Fecha -->
                                        <div id="valorDF" style="display: none; ">
                                          <label for="valorFm" style="display:inline-block; width:140px;margin-right:180px; margin-top:20px;"><strong style="color:#03C1FB;">*</strong>Valor:</label>
                                          <input type="text" name="valorFm" id="valorFm" title="Ingrese el valor" class="form-control col-sm-2"  style="width:250px; height:40px; margin-left:230px; margin-top:-30px" readonly="true" maxlength="500"><br/>
                                        </div>
                                        <input type="hidden" id="idm" name="idm">  
                                        <input type="hidden" id="tipodm" name="tipodm">  
                                   </div>
                                </div>
                                   <div id="forma-modal" class="modal-footer">
                                       <button type="submit" class="btn" style="color: #000; margin-top: 2px">Guardar</button>
                                     <button class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>       
                                   </div>
                           </form>
                        </div>
                    </div>
                </div>
                <!--Modales modicar-->
                <div class="modal fade" id="myModal5" role="dialog" align="center" >
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div id="forma-modal" class="modal-header">
                                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                            </div>
                            <div class="modal-body" style="margin-top: 8px">
                                <p>Información modificada correctamente.</p>
                            </div>
                            <div id="forma-modal" class="modal-footer">
                                <button type="button" id="ver5" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="myModal6" role="dialog" align="center" >
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div id="forma-modal" class="modal-header">
                                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                            </div>
                            <div class="modal-body" style="margin-top: 8px">
                                <p>El registro ingresado ya existe.</p>
                            </div>
                            <div id="forma-modal" class="modal-footer">
                                <button type="button" id="ver6" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="myModal7" role="dialog" align="center" >
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div id="forma-modal" class="modal-header">
                                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                            </div>
                            <div class="modal-body" style="margin-top: 8px">
                                <p>No se ha podido modificar la informaci&oacuten.</p>
                            </div>
                            <div id="forma-modal" class="modal-footer">
                                <button type="button" id="ver7" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Funciones Validación-->
                <script>
                  function llenar(){
                        var estado = document.getElementById('estado1').value;
                        document.getElementById('estado').value= estado;

                  }
                  </script>
                  <script>
                  function llenar2(){
                      var tipoProceso = document.getElementById('tipoProceso1').value;
                      document.getElementById('tipoProceso').value= tipoProceso;
                  }
                  function llenar3(){
                      var responsable = document.getElementById('responsable1').value;
                      document.getElementById('responsable').value= responsable;
                  }

                </script>
                <!--Función guardar-->
                <script>
                    function guardar(id){
                        var tipoDato = document.getElementById('tipod'+id).value;
                        var proceso = document.getElementById('proceso'+id).value;
                        var caracteristica = document.getElementById('carac'+id).value;
                        switch(tipoDato){
                            case('Alfabetico'):
                                var valor= document.getElementById('valorA'+id).value;
                            break;
                            case('Alfanumerico'):
                                var valor= document.getElementById('valorAn'+id).value;
                            break;
                            case('Texto abierto'):
                                var valor= document.getElementById('valorTa'+id).value;
                            break;
                            case('Numerico'):
                                var valor= document.getElementById('valorN'+id).value;
                            break;
                            case('Booleano'):
                                if(document.getElementsByName('valorB'+id)[0].checked == true){
                                    valor='Si';
                                } else {
                                    valor='No';
                                }
                            break;
                            case('Fecha'):
                                var valor= document.getElementById('valorF'+id).value;
                            break;
                        }
                        if(valor =='' || valor ==""){
                            alert('Escriba un valor');
                        } else {
                        var form_data = {
                            is_ajax:1,
                            proceso:proceso,
                            valor:valor,
                            caracteristica:caracteristica
                        };
                        var result='';
                        $.ajax({
                            type: 'POST',
                            url: "jsonProcesos/registrar_GG_CARACTERISTICAXJson.php",
                            data:form_data,
                            success: function (data) {
                                result = JSON.parse(data);                        
                                if (result==true) {
                                    $("#myModal").modal('show');
                                    $('#ver').click(function(){
                                        $("#myModal").modal('hide');
                                      document.location = 'GG_PROCESO.php?id=<?php echo $id?>';
                                    });
                                }else {                                
                                    if(result=='3'){
                                        $("#myModal1").modal('show');
                                        $('#ver1').click(function(){
                                            $("#myModal1").modal('hide');
                                        });
                                    }else{
                                        $("#myModal2").modal('show'); 
                                        $('#ver2').click(function(){
                                            $("#myModal2").modal('hide');
                                        });
                                    }
                                }                                                                        
                            }
                        });
                        }
                    }
                </script>
                <!--Funcion eliminar-->
                <script>
                    function eliminar(id){
                        $("#myModalEliminar").modal('show');
                        $("#verE").click(function(){
                             $("#myModalEliminar").modal('hide');
                             $.ajax({
                                 type:"GET",
                                 url:"jsonProcesos/eliminar_GG_CARACTERISTICAXJson.php?id="+id,
                                 success: function (data) {
                                 result = JSON.parse(data);
                                 if(result==true){
                                     $("#myModal3").modal('show');
                                     $("#ver3").click(function(){
                                       document.location = 'GG_PROCESO.php?id=<?php echo $id?>';
                                   });
                                 }else{
                                     $("#myModal4").modal('show');
                                     $("#ver4").click(function(){
                                       $("#myModal4").modal('hide');
                                   });
                                 }}
                             });
                         });
                    }
                </script>
                <script>
                    function modificar(id, valor, tipodato){
                        $("#idm").val(id);
                        $("#tipodm").val(tipodato);
                        switch(tipodato){
                            case('Alfabetico'):
                                $("#valorAm").val(valor);
                                document.getElementById('valorDA').style.display = 'block';
                                document.getElementById('valorDAn').style.display = 'none';
                                document.getElementById('valorDTa').style.display = 'none';
                                document.getElementById('valorDN').style.display = 'none';
                                document.getElementById('valorDB').style.display = 'none';
                                document.getElementById('valorDF').style.display = 'none';
                            break;
                            case('Alfanumerico'):
                                $("#valorAnm").val(valor);
                                document.getElementById('valorDA').style.display = 'none';
                                document.getElementById('valorDAn').style.display = 'block';
                                document.getElementById('valorDTa').style.display = 'none';
                                document.getElementById('valorDN').style.display = 'none';
                                document.getElementById('valorDB').style.display = 'none';
                                document.getElementById('valorDF').style.display = 'none';
                            break;
                            case('Texto abierto'):
                                $("#valorTam").val(valor);
                                document.getElementById('valorDA').style.display = 'none';
                                document.getElementById('valorDAn').style.display = 'none';
                                document.getElementById('valorDTa').style.display = 'block';
                                document.getElementById('valorDN').style.display = 'none';
                                document.getElementById('valorDB').style.display = 'none';
                                document.getElementById('valorDF').style.display = 'none';
                            break;
                            case('Numerico'):
                                $("#valorNm").val(valor);
                                document.getElementById('valorDA').style.display = 'none';
                                document.getElementById('valorDAn').style.display = 'none';
                                document.getElementById('valorDTa').style.display = 'none';
                                document.getElementById('valorDN').style.display = 'block';
                                document.getElementById('valorDB').style.display = 'none';
                                document.getElementById('valorDF').style.display = 'none';
                            break;
                            case('Booleano'):
                                document.getElementById('valorDA').style.display = 'none';
                                document.getElementById('valorDAn').style.display = 'none';
                                document.getElementById('valorDTa').style.display = 'none';
                                document.getElementById('valorDN').style.display = 'none';
                                document.getElementById('valorDB').style.display = 'block';
                                document.getElementById('valorDF').style.display = 'none';
                                if(valor==='Si'){
                                    document.getElementsByName("valorBm")[0].checked = true;
                                }else { 
                                    document.getElementsByName("valorBm")[1].checked = true;
                                  }
                            break;
                            case('Fecha'):
                                document.getElementById('valorDA').style.display = 'none';
                                document.getElementById('valorDAn').style.display = 'none';
                                document.getElementById('valorDTa').style.display = 'none';
                                document.getElementById('valorDN').style.display = 'none';
                                document.getElementById('valorDB').style.display = 'none';
                                document.getElementById('valorDF').style.display = 'block';
                                document.getElementById('valorFm').value = valor;
                                $('#valorFm').datepicker();
                            break;
                        }
                        $("#myModalModificar").modal('show');
                    }
                </script>
                <script>
                    function modificarItem(){
                        var id= document.getElementById('idm').value; 
                        var tipod= document.getElementById('tipodm').value;
                        switch(tipod){
                            case('Alfabetico'):
                                var valor= document.getElementById('valorAm').value;
                            break;
                            case('Alfanumerico'):
                                var valor= document.getElementById('valorAnm').value;
                            break;
                            case('Texto abierto'):
                                var valor= document.getElementById('valorTam').value;
                            break;
                            case('Numerico'):
                               var valor= document.getElementById('valorNm').value;
                            break;
                            case('Booleano'):
                                if(document.getElementsByName('valorBm')[0].checked == true){
                                    var valor='Si';
                                } else {
                                    var valor='No';
                                }
                            break;
                            case('Fecha'):
                                var valor= document.getElementById('valorFm').value;
                            break;
                        }
                        if (valor =='' || valor == ""){
                            alert('Ingrese valor');
                        } else {
                          var form_data = {
                            is_ajax:1,
                            id:id,
                            valor:valor
                        };
                        var result='';
                        $.ajax({
                            type: 'POST',
                            url: "jsonProcesos/modificar_GG_CARACTERISTICAXJson.php",
                            data:form_data,
                            success: function (data) {
                                result = JSON.parse(data);                        
                                if (result==true) {
                                    $("#myModal5").modal('show');
                                    $('#ver5').click(function(){
                                        $("#myModal5").modal('hide');
                                      document.location = 'GG_PROCESO.php?id=<?php echo $id?>';
                                    });
                                }else {                                
                                    if(result=='3'){
                                        $("#myModal6").modal('show');
                                        $('#ver6').click(function(){
                                            $("#myModal6").modal('hide');
                                        });
                                    }else{
                                        $("#myModal7").modal('show'); 
                                        $('#ver7').click(function(){
                                            $("#myModal7").modal('hide');
                                        });
                                    }
                                }                                                                        
                            }
                        });
                        } 
                    }
                </script>
            <?php }?>
        </div>
        <?php if(empty($_GET['id'])) { ?>
        <div class="col-sm-6 col-sm-1" style="margin-top:-24px; margin-left: -20px" >
            <table class="tablaC table-condensed" style="margin-left: -3px; ">
                <thead>
                    <th>
                        <h2 class="titulo" align="center" style=" font-size:17px; height:35px">Adicional</h2>
                    </th>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <button class="btn btnInfo btn-primary" disabled="true" >DETALLE</button><br/>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <button class="btn btnInfo btn-primary" disabled="true" >DOCUMENTO</button><br/>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <button class="btn btnInfo btn-primary" disabled="true" >TERCERO ASOCIADO</button><br/>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <?php } else { ?>
        <div class="col-sm-6 col-sm-1" style="margin-top:-24px; margin-left: -20px" >
            <table class="tablaC table-condensed" style="margin-left: -3px; ">
                <thead>
                    <th>
                        <h2 class="titulo" align="center" style=" font-size:17px; height:35px">Adicional</h2>
                    </th>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <a href="GG_DETALLE.php?id=<?php echo md5($row[0])?>"><button class="btn btnInfo btn-primary">DETALLE</button></a><br/>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <a href="GG_DOCUMENTO_PROCESO.php?id=<?php echo md5($row[0])?>"><button class="btn btnInfo btn-primary">DOCUMENTO</button></a><br/>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <a href="GG_TERCERO_ASOCIADO.php?id=<?php echo md5($row[0])?>"><button class="btn btnInfo btn-primary">TERCERO ASOCIADO</button></a><br/>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <?php } ?>
    </div>
</div>
<?php require_once 'footer.php';?>
</body>
<script src="js/select/select2.full.js"></script>

  <script>
    $(document).ready(function() {
      $(".select2_single").select2({
        allowClear: true
      });
    });
  </script>
  