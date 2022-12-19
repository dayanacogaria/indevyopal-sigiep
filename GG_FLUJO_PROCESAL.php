<?php
require_once ('Conexion/conexion.php');
require_once 'head_listar.php'; 
if(empty($_SESSION['tipoProceso'])){
   $tipo=''; 
}else {
   $tipo=$_SESSION['tipoProceso'];
}
#ESTADO 
$estado= "SELECT id_unico, nombre FROM gg_estado_proceso ORDER BY nombre ASC";
$estado = $mysqli->query($estado);

#Tipo Proceso
$proceso="SELECT id_unico, identificador, nombre FROM gg_tipo_proceso WHERE id_unico != '$tipo' ORDER BY identificador ASC";
$proceso = $mysqli->query($proceso);

#Fase
if(empty($tipo)){
$fase = "SELECT f.id_unico, f.nombre, ef.id_unico, ef.nombre "
        . "FROM gg_fase f "
        . "LEFT JOIN gg_elemento_flujo ef ON f.elemento_flujo = ef.id_unico ORDER BY f.nombre ASC";
$fase = $mysqli->query($fase);
} else {
    $busquedaFase = "SELECT * FROM gg_flujo_procesal fp LEFT JOIN gg_fase f ON fp.fase = f.id_unico WHERE f.id_unico ='0' AND fp.tipo_proceso = '$tipo'";
    $busquedaFase = $mysqli->query($busquedaFase);
    $numBusquedaFase= mysqli_num_rows($busquedaFase);
    if($numBusquedaFase==0){
        $fase = "SELECT f.id_unico, f.nombre, ef.id_unico, ef.nombre "
        . "FROM gg_fase f "
        . "LEFT JOIN gg_elemento_flujo ef ON f.elemento_flujo = ef.id_unico WHERE f.id_unico ='0' ORDER BY f.nombre ASC";
        $fase = $mysqli->query($fase);
        ?>  <script>
        $(function(){
          document.getElementById('duracion').value='0' ;
        })
        </script>  
    <?php } else {
        $fase = "SELECT
                f.id_unico,
                f.nombre,
                ef.id_unico,
                ef.nombre
              FROM
                gg_fase f
              LEFT JOIN
                gg_elemento_flujo ef ON f.elemento_flujo = ef.id_unico
              WHERE
                f.id_unico NOT IN(
                SELECT
                  fp.fase
                FROM
                  gg_flujo_procesal fp
                LEFT JOIN
                  gg_fase f1 ON fp.fase = f1.id_unico
                WHERE
                  fp.tipo_proceso = '$tipo'
              )
              ORDER BY
                f.nombre ASC";
        $fase = $mysqli->query($fase);
    }
}
#Unidad Tiempo
$unidad = "SELECT id_unico, nombre FROM gg_unidad_tiempo ORDER BY nombre ASC";
$unidad = $mysqli->query($unidad);

#Tipo día
$tipod= "SELECT id_unico, nombre FROM gg_tipo_dia ORDER BY nombre ASC";
$tipod= $mysqli->query($tipod);

#Tercero
$tercero = "SELECT  IF(CONCAT(t.nombreuno,' ', t.nombredos,' ',t.apellidouno,' ',t.apellidodos)='', 
        (t.razonsocial),(CONCAT(t.nombreuno,' ',t.nombredos,' ',t.apellidouno,' ',t.apellidodos))) AS NOMBRE , 
        t.id_unico, t.numeroidentificacion FROM gf_tercero t 
        LEFT JOIN gf_perfil_tercero pt ON t.id_unico = pt.tercero 
        WHERE pt.perfil = '2' ORDER BY NOMBRE ASC";
$tercero = $mysqli->query($tercero);

#Listar
$listar = "SELECT  IF(CONCAT(t.nombreuno,' ', t.nombredos,' ',t.apellidouno,' ',t.apellidodos)='', 
        (t.razonsocial),(CONCAT(t.nombreuno,' ',t.nombredos,' ',t.apellidouno,' ',t.apellidodos))) AS NOMBRE , 
        fp.id_unico, t.id_unico, t.numeroidentificacion, 
        fp.duracion, 
        fp.tipo_dia, td.nombre, 
        fp.tipo_proceso, tp.identificador, tp.nombre, 
        fp.fase, f.nombre,  
        fp.unidad_tiempo, ut.nombre, 
        fp.flujo_si, fps.duracion, 
        fp.flujo_no, fpn.duracion, 
        ef.id_unico, ef.nombre, 
        fp.estado, e.nombre
        FROM gg_flujo_procesal fp 
        LEFT JOIN gf_tercero t ON fp.tercero = t.id_unico 
        LEFT JOIN gg_tipo_dia td ON fp.tipo_dia = td.id_unico 
        LEFT JOIN gg_tipo_proceso tp ON fp.tipo_proceso = tp.id_unico 
        LEFT JOIN gg_fase f ON fp.fase = f.id_unico 
        LEFT JOIN gg_unidad_tiempo ut ON  fp.unidad_tiempo = ut.id_unico 
        LEFT JOIN gg_flujo_procesal fps ON fp.flujo_si = fps.id_unico 
        LEFT JOIN gg_flujo_procesal fpn ON fp.flujo_no = fpn.id_unico 
        LEFT JOIN gg_elemento_flujo ef ON ef.id_unico = f.elemento_flujo 
        LEFT JOIN gg_estado_proceso e ON fp.estado = e.id_unico 
        WHERE fp.tipo_proceso = '$tipo'";
$resultado = $mysqli->query($listar);
?>
<title>Flujo Procesal</title>
</head>
<body> 
<link href="css/select/select2.min.css" rel="stylesheet">
<script src="dist/jquery.validate.js"></script>

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
<style>
    body{
        font-size: 12px;
    }       
label#proceso-error, #fase-error, #unidad-error{
    display: inline-block;
    color: #155180;
    font-weight: normal;
    font-style: italic;
    width: 200px;

}
</style>
<style>
    table.dataTable thead th,table.dataTable thead td{padding:1px 18px; width:300px}
table.dataTable tbody td,table.dataTable tbody td{padding:1px 10px;}

    .cabeza{
        white-space:nowrap;
        padding: 20px;
    }
    
    .campos{
        padding:-20px;
    }
    td{
        width: 300px;
    }
</style>
    <div class="container-fluid text-center">
	<div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-10 text-left">
                <h2 align="center" class="tituloform" style="margin-top:-3px">Flujo Procesal</h2>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px; margin-top: -3px;" class="client-form">         
                    <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="jsonProcesos/registrar_GG_FLUJO_PROCESALJson.php" style="margin-left:10px">
                        <p align="center" style="margin-bottom: 25px; margin-top:0px;  font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                        <div class="form-group form-inline" style="margin-top:-25px;">
                            <!--TIPO PROCESO-->
                            <div class="form-group form-inline ">
                                <label for="proceso" class="control-label col-sm-4" style="display: inline; margin-top: -10px; width: 150px"><strong style="color:#03C1FB;">*</strong>Tipo Proceso :</label>
                            </div>
                            <div class="form-group form-inline " style="margin-left:10px;">
                                <input type="hidden" id="proceso" name="proceso" title="Seleccione proceso" required="required" value="<?php if(empty($tipo)) { echo '';} else { echo $tipo;}?>">
                                <select name="proceso1" id="proceso1"  class="select2_single form-control" title="Seleccione proceso" required onchange="proc();" style="display: inline; width: 200px">
                                <?php if($tipo =='') { ?>
                                    <option value="">Proceso</option>
                                    <?php while($rowP = mysqli_fetch_row($proceso)){?>
                                    <option value="<?php echo $rowP[0] ?>"><?php echo ucwords((strtolower($rowP[1].' - '.$rowP[2])));}?></option>;
                                <?php } else { 
                                    $procesos = "SELECT id_unico, identificador, nombre FROM gg_tipo_proceso WHERE id_unico ='$tipo'";
                                    $procesos = $mysqli->query($procesos);
                                    $procesos = mysqli_fetch_row($procesos);
                                ?>
                                    <option value="<?php echo $procesos[0]?>"><?php echo ucwords((strtolower($procesos[1].' - '.$procesos[2])));?></option>
                                    <?php while($rowP = mysqli_fetch_row($proceso)){?>
                                    <option value="<?php echo $rowP[0] ?>"><?php echo ucwords((strtolower($rowP[1].' - '.$rowP[2])));}?></option>;
                                <?php } ?>
                                </select> 
                            </div>
                        </div>
                        <div class="form-group form-inline" style="margin-top:-10px;">
                            <!--FASE-->
                            
                            <div class="form-group form-inline ">
                                <label for="fase" class="control-label col-sm-4" style="display: inline; margin-top: -10px; width: 150px"><strong style="color:#03C1FB;">*</strong>Fase:</label>
                            </div>
                            <div class="form-group form-inline " style="margin-left:10px;">
                                <input type="hidden" id="fase" name="fase" title="Seleccione fase" required="required">
                                <select name="fase1" id="fase1"  class="select2_single form-control" title="Seleccione fase" required onchange="elem();"  style="display: inline; width: 200px">
                                    <option value="">Fase</option>
                                    <?php while($rowE = mysqli_fetch_row($fase)){?>
                                    <option value="<?php echo $rowE[0] ?>"><?php echo ucwords((strtolower($rowE[1].' - '.$rowE[3])));}?></option>;
                                </select> 
                            </div>
                            <!--TERCERO-->
                            <div class="form-group form-inline ">
                                <label for="tercero" class="control-label col-sm-4" style="width:150px; display: inline; margin-top: -10px">Responsable:</label>
                            </div>
                            <div class="form-group form-inline " style="margin-left:10px;">
                                <select name="tercero" id="tercero"  class="select2_single form-control" title="Seleccione tercero" style="display: inline; width: 200px">
                                    <option value="">Responsable</option>
                                    <?php while($rowTer= mysqli_fetch_row($tercero)){?>
                                    <option value="<?php echo $rowTer[1] ?>"><?php echo ucwords((strtolower($rowTer[0].'('.$rowTer[2].')')));}?></option>;
                                </select> 
                            </div>
                            <!--ESTADO-->
                            <div class="form-group form-inline ">
                                <label for="estado" class="control-label col-sm-4" style="width:140px; display: inline; margin-top: -10px">Estado:</label>
                            </div>
                            <div class="form-group form-inline " style="margin-left:10px;">
                                
                                <?php if(!empty($_SESSION['tipoProceso']) && $numBusquedaFase==0) { ?>
                                <select name="estado" id="estado"  disabled="true" class="select2_single form-control" title="Seleccione estado" style="display: inline; width: 200px">
                                    <?php $estadod ="SELECT id_unico, nombre FROM gg_estado_proceso WHERE LOWER(nombre)='activo'";
                                          $estadod=$mysqli->query($estadod);
                                          $estadod=  mysqli_fetch_row($estadod);?>
                                    <option value="<?php echo $estadod[0]?>"><?php echo ucwords(strtolower($estadod[1]));?></option>
                                </select> 
                                <input type="hidden" id="estado2" name="estado2" value="<?php echo $estadod[0]?>">
                                <?php } else { ?>
                                <select name="estado" id="estado"  class="select2_single form-control" title="Seleccione estado" style="display: inline; width: 200px">
                                    <option value="">Estado</option>
                                    <?php while($rowEst= mysqli_fetch_row($estado)){?>
                                    <option value="<?php echo $rowEst[0] ?>"><?php echo ucwords((strtolower($rowEst[1])));}?></option>;
                                </select> 
                                <?php } ?>
                            </div>
                            
                        </div>
                        <div  class="form-group form-inline" style="margin-top:-18px;">
                            <!--DURACION-->
                            <div class="form-group form-inline ">
                                <label for="duracion" class="control-label col-sm-4" style="display: inline; margin-top: -10px; width: 150px">Duración:</label>
                            </div>
                            <div class="form-group form-inline " style="margin-left:10px;">
                                <input type="text" name="duracion" id="duracion" onkeypress="return txtValida(event, 'num')" title="Ingrese la duración" style="width:200px;; height: 29px; margin-top: 10px;" value="0">
                            </div>
                            <!--UNIDAD-->
                            <div class="form-group form-inline ">
                                <label for="unidad" class="control-label col-sm-4" style="display: inline; margin-top: -10px; width: 150px" ><strong style="color:#03C1FB;">*</strong>Unidad Tiempo:</label>
                            </div>
                            <div class="form-group form-inline " style="margin-left:10px;">
                                <input type="hidden" id="unidad" name="unidad" title="Seleccione unidad tiempo" >
                                <select name="unidad1" id="unidad1"  class="select2_single form-control" title="Seleccione Unidad Tiempo"  onchange="un();"  style="display: inline; width: 200px" disabled="true">
                                    <option value="">Unidad Tiempo</option>
                                    <?php while($rowU = mysqli_fetch_row($unidad)){?>
                                    <option value="<?php echo $rowU[0] ?>"><?php echo ucwords((strtolower($rowU[1])));}?></option>;
                                </select> 
                            </div>
                            <!--TIPO DIA-->
                            <div class="form-group form-inline ">
                                <label for="tipod" class="control-label col-sm-4" style="width:140px; display: inline; margin-top: -10px">Tipo día:</label>
                            </div>
                            <div class="form-group form-inline " style="margin-left:10px;">
                                <select name="tipod" id="tipod"  class="select2_single form-control" title="Seleccione tipo día" style="display: inline; width: 200px" disabled="true">
                                    <option value="">Tipo día</option>
                                    <?php while($rowTd = mysqli_fetch_row($tipod)){?>
                                    <option value="<?php echo $rowTd[0] ?>"><?php echo ucwords((strtolower($rowTd[1])));}?></option>;
                                </select> 
                            </div>
                            
                            <!--<div class="form-group form-inline " style="margin-top:-40px; margin-left: 955px">
                               <button  type="submit" class="btn btn-primary sombra" title="Guardar"> <i class="glyphicon glyphicon-floppy-disk" ></i></button>
                               <button  class="btn btn-primary sombra" title="Guardar"> <i class="glyphicon glyphicon-remove" ></i></button><i class="glyphicon glyphicon-tree-deciduous" >
                            </div>-->
                            
                            <div align="center" class="form-group" style="margin-top:12px; margin-left:69%;">
                                <button  type="submit" class="btn btn-primary sombra" title="Guardar" style="margin-left: 70px"> <i class="glyphicon glyphicon-floppy-disk" ></i></button>
                                <a href="#" onclick="javascript:vaciar()" class="btn btn-primary sombra" title="Vaciar" style="margin-left: 10px"> <i class="glyphicon glyphicon-remove" ></i></a>
                                <a href="javascript:void(0)" onclick="javascript:fnInforme()" class="btn btn-primary sombra" title="Informe" style="margin-left: 10px" target='_blank'> <i class="fa fa-file-excel-o" ></i></a>
                            </div>
                            <script>
                                function fnInforme(){
                                    let proceso = $("#proceso").val();
                                    window.location.href = 'jsonProcesos/registrar_GG_FLUJO_PInformeJson.php?tipo='+proceso;
                                }
                            </script>                            
                        </div>
                    </form>
                </div>
                <!--FUNCION PARA CUANDO CAMBIE EL TIPO DE PROCESO-->
                <script>
                    $("#duracion").change(function() {
                        var duracion = $("#duracion").val();
                        var dur = document.getElementById('unidad1');
                        var dur2 = document.getElementById('unidad');
                        if(duracion >0 ){
                            dur.disabled=false;
                            dur.setAttribute("required", "");
                            dur2.setAttribute("required", "");
                        } else {
                           dur.disabled=true;
                           document.getElementById('unidad1').value='';
                           dur.removeAttribute('required', "");
                           dur2.removeAttribute('required', "");
                        }
                    });
                </script>
                <!--FUNCION VACIAR LA SESION-->
                <script>
                    function vaciar(){
                        var form_data = { session: 8};
                        
                         $.ajax({
                           type: "POST",
                           url: "consultasBasicas/vaciarSessiones.php",
                           data: form_data,
                           success: function(data)
                           {
                             document.location.reload();                             
                           }
                         });
                    }
                </script>
                <!--FUNCION PARA HABILITAR UNIDAD TIEMPO-->
                <script>
                    $("#proceso1").change(function() {
                        var proceso1 = $("#proceso1").val();
                        if (proceso1 =='' || proceso1=="" ){
                            
                        } else {
                        var form_data = { existente: 11, proceso:proceso1 };
                        
                         $.ajax({
                           type: "POST",
                           url: "consultasBasicas/consultarNumeros.php",
                           data: form_data,
                           success: function(data)
                           {
                             document.location.reload();                             
                           }
                         });
                     }
                    });
                </script>
                <!--TABLA LISTAR-->
                <input type="hidden" id="idPrevio" value="">
               <div align="center" class="table-responsive" style="margin-left: 5px; margin-right: 5px; margin-top: 10px; margin-bottom: 5px;">          
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                        <table id="tabla" class="table table-striped table-condensed text-center" class="display" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <td class="oculto"></td>
                                    <td style="min-width: 20px; max-width: 20px;" align="center"></td>
                                    <td style="min-width: 200px;max-width: 200px;"><strong>Fase</strong></td>
                                    <td style="min-width: 60px; max-width: 60px;"><strong>Duración</strong></td>
                                    <td style="min-width: 80px; max-width: 80px;"><strong>Unidad Tiempo</strong></td>
                                    <td style="min-width: 80px; max-width: 80px;"><strong>Tipo Día</strong></td>
                                    <td style="min-width: 90px; max-width: 90px;"><strong>Estado</strong></td>
                                    <td style="min-width: 200px;max-width: 200px;"><strong>Responsable</strong></td>
                                    <td style="min-width: 200px;"><strong>Elemento Relacional</strong></td>
                                    <td style="min-width: 200px;"><strong>Elemento Relacional Incumplimiento</strong></td>
                                </tr>
                                <tr>
                                    <th class="oculto"></th>
                                    <th style="min-width: 20px; max-width: 20px;"></th>
                                    <th style="min-width: 200px;max-width: 200px;">Fase</th>
                                    <th style="min-width: 60px; max-width: 60px;">Duración</th>
                                    <th style="min-width: 80px; max-width: 80px;">Unidad Tiempo</th>
                                    <th style="min-width: 80px; max-width: 80px;">Tipo Día</th>
                                    <th style="min-width: 90px; max-width: 90px;">Estado</th>
                                    <th style="min-width: 200px;max-width: 200px;">Responsable</th>
                                    <th style="min-width: 200px">Elemento relacional</th>
                                    <th style="min-width: 200px">Elemento Relacional Incumplimiento</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                while($row = mysqli_fetch_row($resultado)){?>
                                
                                <tr>
                                    <!--BOTONES-->
                                    <td class="campos oculto"><?php echo $row[1]; ?> </td>
                                    
                                    <td  style="min-width: 20px; max-width: 20px;" class="campos">
                                        <?php $bus= "SELECT * FROM gg_flujo_procesal WHERE tipo_proceso = '$tipo'";
                                          $bus = $mysqli->query($bus);
                                          $bus= mysqli_num_rows($bus);
                                          if($bus>1 && $row[10]=='0') { ?>
                                          <?php } else { 
                                              if($bus==1 && $row[10]='0') { ?>
                                        <a href="#<?php echo $row[1];?>" onclick="javascript:eliminar(<?php echo $row[1]; ?>)" title="Eliminar">
                                            <li class="glyphicon glyphicon-trash"></li>
                                        </a>
                                          <?php } else { ?>
                                        <a href="#<?php echo $row[1];?>" onclick="javascript:eliminar(<?php echo $row[1]; ?>)" title="Eliminar">
                                            <li class="glyphicon glyphicon-trash"></li>
                                        </a>
                                          <?php } } ?>
                                        <a href="#<?php echo $row[1];?>" title="Modificar" id="mod" onclick="javascript:modificar(<?php echo $row[1]; ?>);return select(<?php echo $row[1]; ?>)">
                                            <li class="glyphicon glyphicon-edit"></li>
                                        </a>                                            
                                    </td>
                                    <!--FASE-->
                                    <td class="text-left" style="min-width: 200px;max-width: 200px;">
                                        <?php echo '<label style="text-align:left; font-weight:normal" id="fase'.$row[1].'">'.ucwords(strtolower($row[11].' - '.$row[19])).'</label>'; ?>
                                        <div style="display: none" id="Fase<?php echo $row[1]; ?>" class="campoD">
                                        <input type="hidden" name="tipop<?php echo $row[1]?>" id="tipop<?php echo $row[1]?>" value="<?php echo $row[7]?>">
                                        <?php if($row[10]=='0') { ?>
                                        <select  class="select2_single form-control" style="width:200px" id="modFase<?php echo $row[1]; ?>" disabled="true">
                                            <option value="<?php echo $row[10];?>"><?php echo ucwords(strtolower($row[11].' - '.$row[19])); ?></option>
                                        </select>
                                        <?php } else { ?>
                                        <select  class="select2_single form-control" style="width:200px" id="modFase<?php echo $row[1]; ?>">
                                            <option value="<?php echo $row[10];?>"><?php echo ucwords(strtolower($row[11].' - '.$row[19])); ?></option>
                                                    <?php 
                                                    $fasem = "SELECT
                                                            f.id_unico,
                                                            f.nombre,
                                                            ef.id_unico,
                                                            ef.nombre
                                                          FROM
                                                            gg_fase f
                                                          LEFT JOIN
                                                            gg_elemento_flujo ef ON f.elemento_flujo = ef.id_unico
                                                          WHERE
                                                            f.id_unico NOT IN(
                                                            SELECT
                                                              fp.fase
                                                            FROM
                                                              gg_flujo_procesal fp
                                                            LEFT JOIN
                                                              gg_fase f1 ON fp.fase = f1.id_unico
                                                            WHERE
                                                              fp.tipo_proceso = '$tipo') ORDER BY f.nombre ASC";
                                                    $fasem = $mysqli->query($fasem);
                                                    while ($f= mysqli_fetch_row($fasem)){
                                                        echo '<option value="'.$f[0].'">'.ucwords(strtolower($f[1].' - '.$f[3])).'</option>';
                                                    }
                                                    ?>                                                
                                        </select>
                                        <?php } ?>
                                        </div>
                                    </td>
                                    <!--DURACION-->
                                    <td class="text-left" style="min-width: 60px; max-width: 60px;">
                                        <?php echo '<label style="text-align:left; font-weight:normal" id="duracion'.$row[1].'">'.ucwords(strtolower($row[4])).'</label>'; ?>
                                        <div style="display: none" id="Duracion<?php echo $row[1]; ?>" class="col-sm-12">
                                            <input type="text" id="modduracion<?php echo $row[1]; ?>" class="form-control col-sm-12" style="width:50px" name="modduracion" title="Ingrese la duración" onkeypress="return txtValida(event, 'num')" style="width:60px" value="<?php echo $row[4]?>">
                                        </div>
                                    </td>
                                    <!--UNIDAD TIEMPO-->
                                    <td class="text-left" style="min-width: 80px; max-width: 80px;">
                                        <?php echo '<label style=" text-align:left; font-weight:normal" id="unidad'.$row[1].'">'.ucwords(strtolower($row[13])).'</label>'; ?>
                                        <div style="display: none" id="Unidad<?php echo $row[1]; ?>" class="campoD">
                                            <?php if($row[4]=='0'){?>
                                            <select  class="select2_single form-control" disabled="true" style="width:70px" id="modUnidad<?php echo $row[1]; ?>" onchange="cambiarUnidadT(<?php echo $row[1]?>)">
                                            <?php } else { ?>
                                             <select  class="select2_single form-control" style="width:70px" id="modUnidad<?php echo $row[1]; ?>" onchange="cambiarUnidadT(<?php echo $row[1]?>)">
                                            <?php } ?>
                                            <?php if (empty($row[13])) { ?>
                                                    <?php $unidadm = "SELECT id_unico, nombre FROM gg_unidad_tiempo ORDER BY nombre ASC";
                                                    $unidadm = $mysqli->query($unidadm);
                                                    while ($u= mysqli_fetch_row($unidadm)){
                                                        echo '<option value="'.$u[0].'">'.ucwords(strtolower($u[1])).'</option>';
                                                    }?>
                                            
                                            <?php } else { ?>
                                                <option value="<?php echo $row[12];?>"><?php echo ucwords(strtolower($row[13])); ?></option>
                                                    <?php 
                                                    $unidadm = "SELECT id_unico, nombre FROM gg_unidad_tiempo WHERE id_unico != $row[12] ORDER BY nombre ASC";
                                                    $unidadm = $mysqli->query($unidadm);
                                                    while ($u= mysqli_fetch_row($unidadm)){
                                                        echo '<option value="'.$u[0].'">'.ucwords(strtolower($u[1])).'</option>';
                                                    } 
                                                    
                                                    } ?>                                          
                                        </select>
                                        </div>
                                    </td>
                                    <script>
                                        $("#modduracion"+<?php echo $row[1]?>).change(function() {
                                            var duracion = $("#modduracion"+<?php echo $row[1]?>).val();
                                            var dur = document.getElementById('modUnidad'+<?php echo $row[1]?>);
                                            if(duracion >0 ){
                                                dur.disabled=false;
                                                dur.setAttribute("required", "");
                                            } else {
                                               dur.disabled=true;
                                               document.getElementById('modUnidad'+<?php echo $row[1]?>).value='';
                                               dur.removeAttribute('required', "");
                                            }
                                        });
                                    </script>
                                    <!--TIPO DIA-->
                                    <td class="text-left" style="min-width: 80px; max-width: 80px;">    
                                        <?php echo '<label style="text-align:left; font-weight:normal" id="tipod'.$row[1].'">'.ucwords(strtolower($row[6])).'</label>'; ?>
                                        <div style="display: none" id="Tipod<?php echo $row[1]; ?>" class="campoD">
                                        <select  class="select2_single form-control col-sm-12" style="width:70px" id="modTipod<?php echo $row[1]; ?>">
                                            <?php if (empty($row[5])) { ?>
                                                    <option value="">-</option>
                                                    <?php 
                                                    $tipodm= "SELECT id_unico, nombre FROM gg_tipo_dia ORDER BY nombre ASC";
                                                    $tipodm= $mysqli->query($tipodm);
                                                    while ($td= mysqli_fetch_row($tipodm)){
                                                        echo '<option value="'.$td[0].'">'.ucwords(strtolower($td[1])).'</option>';
                                                    }?>
                                            
                                            <?php } else { ?>
                                                <option value="<?php echo $row[5];?>"><?php echo ucwords(strtolower($row[6])); ?></option>
                                                    <?php 
                                                    echo '<option value="">-</option>';
                                                    $tipodm= "SELECT id_unico, nombre FROM gg_tipo_dia WHERE id_unico != $row[5] ORDER BY nombre ASC";
                                                    $tipodm= $mysqli->query($tipodm);
                                                    while ($td= mysqli_fetch_row($tipodm)){
                                                        echo '<option value="'.$td[0].'">'.ucwords(strtolower($td[1])).'</option>';
                                                    } 
                                                    
                                                    } ?>                                          
                                        </select>
                                        </div>
                                    </td>
                                     <script>
                                    function cambiarUnidadT(id){
                                        
                                        var combo = document.getElementById("modUnidad"+id);
                                        var valorA = combo.options[combo.selectedIndex].text
                                        var fi = document.getElementById("modTipod"+id);
                                        var valorA =valorA.toLowerCase();
                                        
                                        if(valorA =='dias' || valorA=='días' || valorA=='dia' || valorA=='día'){
                                              fi.disabled=false;
                                        } else {
                                            fi.value='';
                                            fi.disabled=true;
                                        }
                                    }
                                </script>
                                    <!--ESTADO-->
                                    <td class="text-left" style="min-width: 90px; max-width: 90px;">
                                        <?php echo '<label style="text-align:left; font-weight:normal" id="estado'.$row[1].'">'.ucwords(strtolower($row[21])).'</label>'; ?>
                                        <div style="display: none" id="Estado<?php echo $row[1]; ?>" class="campoD">
                                            <?php if ($row[10]=='0') { ?>
                                            <select  class="select2_single form-control col-sm-12" style="width:80px" id="modEstado<?php echo $row[1]; ?>" disabled="true">
                                            <?php } else { ?>
                                            <select  class="select2_single form-control col-sm-12" style="width:80px" id="modEstado<?php echo $row[1]; ?>" >
                                            <?php } ?>
                                            <?php if (empty($row[20])) { ?>
                                                    <option value="">-</option>
                                                    <?php 
                                                    $estadom= "SELECT id_unico, nombre FROM gg_estado_proceso ORDER BY nombre ASC";
                                                    $estadom= $mysqli->query($estadom);
                                                    while ($ep= mysqli_fetch_row($estadom)){
                                                        echo '<option value="'.$ep[0].'">'.ucwords(strtolower($ep[1])).'</option>';
                                                    }?>
                                            
                                            <?php } else { ?>
                                                <option value="<?php echo $row[20];?>"><?php echo ucwords(strtolower($row[21])); ?></option>
                                                    <?php 
                                                    echo '<option value="">-</option>';
                                                    $estadom= "SELECT id_unico, nombre FROM gg_estado_proceso WHERE id_unico != '$row[20]' ORDER BY nombre ASC";
                                                    $estadom= $mysqli->query($estadom);
                                                    while ($ep= mysqli_fetch_row($estadom)){
                                                        echo '<option value="'.$ep[0].'">'.ucwords(strtolower($ep[1])).'</option>';
                                                    }
                                                    } ?>                                          
                                        </select>
                                        </div>
                                    </td>
                           
                                    <!--RESPONSABLE-->
                                    <td class="text-left" style="min-width: 200px; max-width: 200px;">
                                        <?php if (empty($row[2])) { echo ''; } else { ?>
                                        <?php echo '<label style="font-weight:normal" id="responsable'.$row[1].'">'.ucwords(strtolower($row[0].'('.$row[3].')')).'</label>'; } ?>
                                        <div style="display: none" id="Responsable<?php echo $row[1]; ?>" class="campoD">
                                            <select  class="select2_single form-control" style="width:150px" id="modResponsable<?php echo $row[1]; ?>">
                                            <?php if (empty($row[2])) { ?>
                                                    <option value="">-</option>
                                                    <?php 
                                                    $tercerom = "SELECT  IF(CONCAT(t.nombreuno,' ', t.nombredos,' ',t.apellidouno,' ',t.apellidodos)='', 
                                                            (t.razonsocial),(CONCAT(t.nombreuno,' ',t.nombredos,' ',t.apellidouno,' ',t.apellidodos))) AS NOMBRE , 
                                                            t.id_unico, t.numeroidentificacion FROM gf_tercero t LEFT JOIN gf_perfil_tercero pt ON t.id_unico = pt.tercero 
                                                        WHERE pt.perfil = '2' ORDER BY NOMBRE ASC";
                                                    $tercerom = $mysqli->query($tercerom);
                                                    while ($tt= mysqli_fetch_row($tercerom)){
                                                        echo '<option value="'.$tt[1].'">'.ucwords(strtolower($tt[0]."(".$tt[2].")")).'</option>';
                                                    }?>
                                            
                                            <?php } else { ?>
                                                <option value="<?php echo $row[2];?>"><?php echo ucwords(strtolower($row[0].'('.$row[3].')')); ?></option>
                                                    <<?php 
                                                    $tercerom = "SELECT  IF(CONCAT(t.nombreuno,' ', t.nombredos,' ',t.apellidouno,' ',t.apellidodos)='', 
                                                        (t.razonsocial),(CONCAT(t.nombreuno,' ',t.nombredos,' ',t.apellidouno,' ',t.apellidodos))) AS NOMBRE , 
                                                        t.id_unico, t.numeroidentificacion FROM gf_tercero t 
                                                        LEFT JOIN gf_perfil_tercero pt ON t.id_unico = pt.tercero 
                                                        WHERE pt.perfil = '2' AND id_unico != $row[2] ORDER BY NOMBRE ASC";
                                                    $tercerom = $mysqli->query($tercerom);
                                                    echo '<option value="">-</option>';
                                                    while ($tt= mysqli_fetch_row($tercerom)){
                                                        echo '<option value="'.$tt[1].'">'.ucwords(strtolower($tt[0]."(".$tt[2].")")).'</option>';
                                                    } 
                                                    
                                                    } ?>                                          
                                        </select>
                                            <div style="display:inline">
                                            <table id="tab<?php echo $row[1] ?>" style="padding:0px;background-color:transparent;background:transparent; display: inline">
                                                <tbody>
                                                    <tr style="background-color:transparent;">
                                                        <td style="background-color:transparent; width:8px">
                                                            <a  href="#<?php echo $row[1];?>" title="Guardar" id="guardar<?php echo $row[1]; ?>"  onclick="javascript:guardarCambios(<?php echo $row[1]; ?>)">
                                                                <li class="glyphicon glyphicon-floppy-disk"></li>
                                                            </a>
                                                        </td>
                                                        <td style="background-color:transparent; width:8px">
                                                            <a href="#<?php echo $row[01];?>" title="Cancelar" id="cancelar<?php echo $row[1] ?>" onclick="javascript:cancelar(<?php echo $row[1];?>)" >
                                                                <i title="Cancelar" class="glyphicon glyphicon-remove" ></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        </div>
                                    </td>
                                    <td class="campos text-left">
                                        <?php $compar = strtolower($row[19]);
                                        $es=  strtolower($row[21]);
                                        if (empty($row[14])){
                                            if (empty($row[18])) { echo ''; } else {
                                        
                                            switch ($compar){ 
                                                case ('etapa especial'):
                                                    echo '';
                                                break;
                                                case ('condicion'):
                                                case ('condición'):
                                                    if($es=='cerrado' || $es=='anulado') { echo ''; } else { ?>
                                                    <center>
                                                        <a href="#"  onclick="javascript:flujoSi(<?php echo $row[1]?>);" class="btn btn-primary btnInfo" style="width: 80px; height: 30px" title="Asignar relación en caso de cumplimiento">Cumplimiento</a>          
                                                    </center>
                                    
                                           <?php } break;
                                                default : 
                                                    if($es=='cerrado' || $es=='anulado') { echo ''; } else { ?>
                                                        <center>
                                                            <a href="#"  onclick="javascript:flujoSi(<?php echo $row[1]?>);" class="btn btn-primary btnInfo" style="width: 130px; height: 30px;padding-left:3px" title="Asignar elemento flujo relacionado">Elemento flujo relacionado</a>          
                                                        </center>
                                                <?php
                                                    }
                                                break;
                                            }
                                            }
                                        } else {
                                            if ($compar=='etapa especial'){
                                                echo '';
                                            } else {
                                            $flujo1 = "SELECT  fp.id_unico ,fp.tipo_proceso, tp.identificador, tp.nombre, fp.fase, f.nombre, "
                                                    . "ef.id_unico, ef.nombre FROM gg_flujo_procesal fp "
                                                    . "LEFT JOIN gg_tipo_proceso tp ON fp.tipo_proceso = tp.id_unico "
                                                    . "LEFT JOIN gg_fase f ON fp.fase = f.id_unico "
                                                    . "LEFT JOIN gg_elemento_flujo ef ON ef.id_unico = f.elemento_flujo "
                                                    . "WHERE fp.id_unico = $row[14]";
                                            $flujo1 = $mysqli->query($flujo1);
                                            $flujo1=  mysqli_fetch_row($flujo1);
                                            echo ucwords(strtolower($flujo1[5]).' - '.$flujo1[7]);?>
                                            <a  href="#" onclick="javascript:eliminarFlujoSi(<?php echo $row[1]?>);"><i title="Eliminar flujo" class="glyphicon glyphicon-trash"></i></a>
                                            <a  href="#" onclick="javascript:modFlujoSi(<?php echo $row[1].','.$flujo1[0]?>);"><i title="Modificar flujo" class="glyphicon glyphicon-edit"></i></a>
                                        <?php } } 
                                        ?>
                                    </td> 
                                    <td class="campos text-left">
                                        <?php $compar = strtolower($row[19]);
                                        $es=  strtolower($row[21]);
                                        if (empty($row[16])){
                                            if (empty($row[18])) { echo ''; } else {
                                            switch ($compar){
                                                case ('etapa especial'):
                                                    echo '';
                                                break;
                                                case ('condicion'):
                                                case ('condición'):
                                                    if($es=='cerrado' || $es=='anulado') { echo ''; } else { ?>
                                                    <center>
                                                        <a href="#"  onclick="javascript:flujoNo(<?php echo $row[1]?>);" class="btn btn-primary btnInfo" style="width: 80px; height: 30px" title="Asignar relación en caso de incumplimiento">Incumplimiento</a>  
                                                    </center>
                                                    <?php } break;
                                            default :
                                                echo '';
                                            break;
                                            }
                                        }
                                        
                                        } else {
                                             $flujo2 = "SELECT  fp.id_unico ,fp.tipo_proceso, tp.identificador, tp.nombre, fp.fase, f.nombre, "
                                                    . "ef.id_unico, ef.nombre FROM gg_flujo_procesal fp "
                                                    . "LEFT JOIN gg_tipo_proceso tp ON fp.tipo_proceso = tp.id_unico "
                                                    . "LEFT JOIN gg_fase f ON fp.fase = f.id_unico "
                                                    . "LEFT JOIN gg_elemento_flujo ef ON ef.id_unico = f.elemento_flujo "
                                                    . "WHERE fp.id_unico = $row[16]";
                                            $flujo2 = $mysqli->query($flujo2);
                                            $flujo2=  mysqli_fetch_row($flujo2);
                                            echo ucwords(strtolower($flujo2[5]).' - '.$flujo2[7]);?>
                                            <a  href="#" onclick="javascript:eliminarFlujoNo(<?php echo $row[1]?>);"><i title="Eliminar flujo" class="glyphicon glyphicon-trash"></i></a>
                                            <a  href="#" onclick="javascript:modFlujoNo(<?php echo $row[1].','.$flujo2[0]?>);"><i title="Modificar flujo" class="glyphicon glyphicon-edit"></i></a>
                                        <?php }
                                        ?>
                                        
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
	</div>
    </div>
    <script>
        function select(id){
            var flujo = 'modFase'+id;
            $(".select2_single, #"+flujo).select2();
        }
    </script>
<!-- Registrar Flujo Si--> 
<div class="modal fade" id="myModalRegistrarFlujoSi" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content client-form1">
        <div id="forma-modal" class="modal-header">       
            <h4 class="modal-title" style="font-size: 24; padding: 3px;">Registro Flujo</h4>
        </div>
        <form  name="form" method="POST" action="javascript:registrarFlujoSi()">
            <div class="modal-body ">
            <input type="hidden" name="id" id="id">
            <div class="form-group" style="margin-top: 13px;">
                <label style="display:inline-block; width:140px"><strong style="color:#03C1FB;">*</strong>Flujo Procesal:</label>
                <select style="display:inline-block; width:250px; margin-bottom:15px; height:40px" name="flujo" id="flujo" class="select2_single form-control" title="Seleccione flujo procesal" required>
                    <option value="">Flujo Procesal</option>
                    
                </select>                                
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
<!--Mensajes Registrar flujo Si-->
    <div class="modal fade" id="myModal11" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
            <p>Información registrada correctamente.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver11" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="myModal12" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
           <p>La información no se ha podido registrar.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver12" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
        </div>
      </div>
    </div>
  </div>
<!--Fin Registrar Flujo Si-->
<!--Registrar Flujo No-->
<div class="modal fade" id="myModalRegistrarFlujoNo" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content client-form1">
        <div id="forma-modal" class="modal-header">       
            <h4 class="modal-title" style="font-size: 24; padding: 3px;">Registro Flujo</h4>
        </div>
        <form  name="form" method="POST" action="javascript:registrarFlujoNo()">
            <div class="modal-body ">
            <input type="hidden" name="idn" id="idn">
            <div class="form-group" style="margin-top: 13px;">
                <label style="display:inline-block; width:140px"><strong style="color:#03C1FB;">*</strong>Flujo Procesal:</label>
                <select style="display:inline-block; width:250px; margin-bottom:15px; height:40px" name="flujon" id="flujon" class="select2_single form-control" title="Seleccione flujo procesal" required>
                    <option value="">Flujo Procesal</option>
                    
                </select>                                
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
<!--Mensajes Registar flujo No-->
    <div class="modal fade" id="myModal13" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
            <p>Información registrada correctamente.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver13" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="myModal14" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
           <p>La información no se ha podido registrar.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver14" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
        </div>
      </div>
    </div>
  </div>
<!--Fin Registrar flujo No-->
<!--Eliminar Flujo Si--> 
    <div class="modal fade" id="myModalEliminarFlujoS" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <p>¿Desea eliminar el registro seleccionado?</p>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="verEfs" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>
 <div class="modal fade" id="myModal" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <p>Información eliminada correctamente</p>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="ver" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
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
                <p>No se pudo eliminar la información, el registro seleccionado esta siendo usado por otra dependencia.</p>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="ver2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
            </div>
        </div>
    </div>
</div>
<!--Fin eliminar flujo si-->
<!--Eliminar Flujo No--> 
    <div class="modal fade" id="myModalEliminarFlujoN" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <p>¿Desea eliminar el registro seleccionado?</p>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="verEfn" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
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
<!--Fin eliminar flujo No-->
 <!-- Modificar Flujo Si--> 
    <div class="modal fade" id="myModalModificarFlujoSi" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content client-form1">
            <div id="forma-modal" class="modal-header">       
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Modificar Flujo</h4>
            </div>
            <form  name="form" method="POST" action="javascript:modificarFlujoSi()">
                <div class="modal-body ">
                <input type="hidden" name="idm" id="idm">
                <div class="form-group" style="margin-top: 13px;">
                    <label style="display:inline-block; width:140px"><strong style="color:#03C1FB;">*</strong>Flujo Procesal:</label>
                    <select style="display:inline-block; width:250px; margin-bottom:15px; height:40px" name="flujom" id="flujom" class="select2_single form-control" title="Seleccione flujo procesal" required>
                        <option value="">Flujo Procesal</option>
                    </select>                                
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
    <!--Mensajes Modificar flujo Si-->
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
          <button type="button" id="ver5" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
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
           <p>La información no se ha podido modificar.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver6" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
        </div>
      </div>
    </div>
  </div>
<!--Fin Modificar Flujo Si-->
<!-- Modificar Flujo No--> 
    <div class="modal fade" id="myModalModificarFlujoNo" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content client-form1">
            <div id="forma-modal" class="modal-header">       
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Modificar Flujo</h4>
            </div>
            <?php  
            
            ?>
            <form  name="form" method="POST" action="javascript:modificarFlujoNo()">
                <div class="modal-body ">
                <input type="hidden" name="idmn" id="idmn">
                <div class="form-group" style="margin-top: 13px;">
                    <label style="display:inline-block; width:140px"><strong style="color:#03C1FB;">*</strong>Flujo Procesal:</label>
                    <select style="display:inline-block; width:250px; margin-bottom:15px; height:40px" name="flujomn" id="flujomn" class="select2_single form-control" title="Seleccione flujo procesal" required>
                        <option value="">Flujo Procesal</option>
                    </select>                                
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
    <!--Mensajes Modificar flujo No-->
    <div class="modal fade" id="myModal7" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
            <p>Información modificada correctamente.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver7" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="myModal8" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
           <p>La información no se ha podido modificar.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver8" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
        </div>
      </div>
    </div>
  </div>
<!--Fin Modificar Flujo No-->
<!--Mensajes eliminación de flujo procesal-->
    <div class="modal fade" id="myModal9" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <p>¿Desea eliminar el registro seleccionado de flujo procesal?</p>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="ver9" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="myModal10" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <p>Información eliminada correctamente</p>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="ver10" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="myModal20" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <p>No se pudo eliminar la información, el registro seleccionado esta siendo usado por otra dependencia.</p>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="ver20" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
            </div>
        </div>
    </div>
</div>
<!--Fin mensajes eliminar flujo procesal-->

    <!--Mensajes modificar flujo procesal-->
    <div class="modal fade" id="myModal25" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
            <p>Información modificada correctamente.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver25" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="myModal26" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
           <p>La información no se ha podido modificar.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver26" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
        </div>
      </div>
    </div>
  </div>
    <div class="modal fade" id="myModal27" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
           <p>El registro ingresado ya existe.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver27" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
        </div>
      </div>
    </div>
  </div>
<!--Fin Modificar Flujo No-->
<?php require_once 'footer.php'; ?>
<link rel="stylesheet" href="css/bootstrap-theme.min.css">
<script src="js/bootstrap.min.js"></script>
   <script src="js/select/select2.full.js"></script>
  <script>
    $(document).ready(function() {
      $(".select2_single").select2({
        allowClear: true,
      });
    });
  </script>
  <!--Llenar combos validar-->
  <script>
  function proc(){
      var proceso = document.getElementById('proceso1').value;
      document.getElementById('proceso').value= proceso;
  }
  function elem(){
      var fase = document.getElementById('fase1').value;
      document.getElementById('fase').value= fase;
      

  }
  function un(){
      var unidad = document.getElementById('unidad1').value;
      document.getElementById('unidad').value= unidad;
      var valorA = unidad1.options[unidad1.selectedIndex].text;
      var fi = document.getElementById("tipod");
      var valorA =valorA.toLowerCase();
      if(valorA =='dias' || valorA=='días' || valorA=='dia' || valorA=='día'){
          
            fi.disabled=false;
      } else {
          fi.value='';
          fi.disabled=true;
      }
  }
      
  </script>
  <!--Registrar flujo si-->
  <script>
      function flujoSi(id){ 
        
        $("#id").val(id);
        <?php if(!empty($_SESSION['tipoProceso'])){ $tipo = $_SESSION['tipoProceso'];} else { $tipo=''; } ?>
        var form_data={
            existente:12,
            id:id,
            tipo:<?php echo $tipo;?>        
        };

        $.ajax({
            type: 'POST',
            url: "consultasBasicas/consultarNumeros.php",
            data:form_data,
            success: function (data) { 
                $("#flujo").html(data).fadeIn();
                $("#flujo").css('display','none');
                $("#myModalRegistrarFlujoSi").modal('show');
            }
        });
     }  
     </script>
    <script>
        
       function registrarFlujoSi(){
          
           var id= document.getElementById('id').value;
           var flujo= document.getElementById('flujo').value;
           var form_data = { sesion: 9 };
                        
         $.ajax({
           type: "POST",
           url: "consultasBasicas/vaciarSessiones.php",
           data: form_data,
           success: function(data)
           {
           }
         });
          $.ajax({
                  type:"GET",
                  url:"jsonProcesos/registrar_GG_FLUJO_SIJson.php?id="+id+"&flujo="+flujo,
                  success: function (data) {
                  result = JSON.parse(data);
                  if(result==true) {
                      
                      $("#myModal11").modal('show');
                      $('#ver11').click(function(){
                          $("#myModalRegistrarFlujoSi").modal('hide');
                        document.location = 'GG_FLUJO_PROCESAL.php';
                      });
                  } else { 
                      $("#myModal12").modal('show');
                      $('#ver12').click(function(){
                           $("#myModalRegistrarFlujoSi").modal('hide');
                        document.location = 'GG_FLUJO_PROCESAL.php';
                      });
                  }
                  }
              });
      }
      
  </script>
  <!--Registrar flujo No-->
  <script>
      function flujoNo(id){
          
          $("#idn").val(id);
        <?php if(!empty($_SESSION['tipoProceso'])){ $tipo = $_SESSION['tipoProceso'];} else { $tipo=''; } ?>
        var form_data={
            existente:12,
            id:id,
            tipo:<?php echo $tipo;?>        
        };

        $.ajax({
            type: 'POST',
            url: "consultasBasicas/consultarNumeros.php",
            data:form_data,
            success: function (data) { 
                $("#flujon").html(data).fadeIn();
                $("#flujon").css('display','none');
                $("#myModalRegistrarFlujoNo").modal('show');   
            }
        });
           
       }
       function registrarFlujoNo(){
           var idn= document.getElementById('idn').value;
           var flujon= document.getElementById('flujon').value;
          $.ajax({
                  type:"GET",
                  url:"jsonProcesos/registrar_GG_FLUJO_NOJson.php?id="+idn+"&flujo="+flujon,
                  success: function (data) {
                  result = JSON.parse(data);
                  if(result==true) {
                      $("#myModal13").modal('show');
                      $('#ver13').click(function(){
                          $("#myModalRegistrarFlujoNo").modal('hide');
                        document.location = 'GG_FLUJO_PROCESAL.php';
                      });
                  } else { 
                      $("#myModal14").modal('show');
                      $('#ver14').click(function(){
                          $("#myModalRegistrarFlujoNo").modal('hide');
                        document.location = 'GG_FLUJO_PROCESAL.php';
                      });
                  }
                  }
              });
      }
      
  </script>
  <!--Eliminar flujo si-->
  <script>
  function eliminarFlujoSi(id){
      var result = '';
         $("#myModalEliminarFlujoS").modal('show');
         $("#verEfs").click(function(){
              $("#myModalEliminarFlujoS").modal('hide');
              $.ajax({
                  type:"GET",
                  url:"jsonProcesos/eliminar_GG_FLUJO_SIJson.php?id="+id,
                  success: function (data) {
                  result = JSON.parse(data);
                  if(result==true){
                      $("#myModal").modal('show');
                      $("#ver").click(function(){
                        document.location = 'GG_FLUJO_PROCESAL.php';
                    });
                  }else{
                      $("#myModal2").modal('show');
                      $("#ver2").click(function(){
                        document.location = 'GG_FLUJO_PROCESAL.php';
                    });
                  }}
              });
          });
  }
  </script>
  <!--Eliminar flujo No-->
  <script>
  function eliminarFlujoNo(id){
      var result = '';
         $("#myModalEliminarFlujoN").modal('show');
         $("#verEfn").click(function(){
              $("#myModalEliminarFlujoN").modal('hide');
              $.ajax({
                  type:"GET",
                  url:"jsonProcesos/eliminar_GG_FLUJO_NOJson.php?id="+id,
                  success: function (data) {
                  result = JSON.parse(data);
                  if(result==true){
                      $("#myModal3").modal('show');
                      $("#ver3").click(function(){
                        document.location = 'GG_FLUJO_PROCESAL.php';
                    });
                  }else{
                      $("#myModal4").modal('show');
                      $("#ver4").click(function(){
                        document.location = 'GG_FLUJO_PROCESAL.php';
                    });
                  }}
              });
          });
  }
  </script>
  <!--Modificar flujo si-->
  <script>
      function modFlujoSi(id, flujo){
          
          $("#idm").val(id);
         
          <?php if(!empty($_SESSION['tipoProceso'])){ $tipo = $_SESSION['tipoProceso'];} else { $tipo=''; } ?>
            var form_data={
                existente:12,
                id:id,
                tipo:<?php echo $tipo;?>        
            };

            $.ajax({
                type: 'POST',
                url: "consultasBasicas/consultarNumeros.php",
                data:form_data,
                success: function (data) { 
                    $("#flujom").html(data).fadeIn();
                     $("#flujom").val(flujo);
                    $("#flujom").css('display','none');
                    $("#myModalModificarFlujoSi").modal('show');
                }
            });
          
           
          
      }
    </script>
    <script>
      function modificarFlujoSi(){
          
           var id= document.getElementById('idm').value;
           var flujo= document.getElementById('flujom').value;
          
          $.ajax({
                  type:"GET",
                  url:"jsonProcesos/modificar_GG_FLUJO_SIJson.php?id="+id+"&flujo="+flujo,
                  success: function (data) {
                  result = JSON.parse(data);
                  if(result==true) {
                      
                      $("#myModal5").modal('show');
                      $('#ver5').click(function(){
                          $("#myModalModificarFlujoSi").modal('hide');
                        document.location = 'GG_FLUJO_PROCESAL.php';
                      });
                  } else { 
                      $("#myModal6").modal('show');
                      $('#ver6').click(function(){
                           $("#myModalModificarFlujoSi").modal('hide');
                        document.location = 'GG_FLUJO_PROCESAL.php';
                      });
                  }
                  }
              });
      }
  </script>
  <!--Modificar flujo No-->
  <script>
      function modFlujoNo(id, flujo){
          $("#idmn").val(id);
          <?php if(!empty($_SESSION['tipoProceso'])){ $tipo = $_SESSION['tipoProceso'];} else { $tipo=''; } ?>
            var form_data={
                existente:12,
                id:id,
                tipo:<?php echo $tipo;?>        
            };

            $.ajax({
                type: 'POST',
                url: "consultasBasicas/consultarNumeros.php",
                data:form_data,
                success: function (data) { 
                    $("#flujomn").html(data).fadeIn();
                     $("#flujomn").val(flujo);
                    $("#flujomn").css('display','none');
                    $("#myModalModificarFlujoNo").modal('show');
                }
            });
           
          
      }
    </script>
    <script>
      function modificarFlujoNo(){
          
           var id= document.getElementById('idmn').value;
           var flujo= document.getElementById('flujomn').value;
          
          $.ajax({
                  type:"GET",
                  url:"jsonProcesos/modificar_GG_FLUJO_NOJson.php?id="+id+"&flujo="+flujo,
                  success: function (data) {
                  result = JSON.parse(data);
                  if(result==true) {
                      
                      $("#myModal7").modal('show');
                      $('#ver7').click(function(){
                          $("#myModalModificarFlujoNo").modal('hide');
                        document.location = 'GG_FLUJO_PROCESAL.php';
                      });
                  } else { 
                      $("#myModal8").modal('show');
                      $('#ver8').click(function(){
                           $("#myModalModificarFlujoNo").modal('hide');
                        document.location = 'GG_FLUJO_PROCESAL.php';
                      });
                  }
                  }
              });
      }
  </script>
  <!--Eliminar flujo procesal-->
  <script>
  function eliminar(id)
      {
         var result = '';
         $("#myModal9").modal('show');
         $("#ver9").click(function(){
              $("#mymodal9").modal('hide');
              $.ajax({
                  type:"GET",
                  url:"jsonProcesos/eliminar_GG_FLUJO_PROCESALJson.php?id="+id,
                  success: function (data) {
                  result = JSON.parse(data);
                  if(result==true){
                      $("#myModal10").modal('show');
                      $("#ver10").click(function(){
                         $("#myModal10").modal('hide');
                        document.location = 'GG_FLUJO_PROCESAL.php';
                      })
                  }else{
                      $("#myModal20").modal('show');
                      $("#ver20").click(function(){
                         $("#myModal20").modal('hide');
                        document.location = 'GG_FLUJO_PROCESAL.php';
                      })
                  }
              }
              
              });
          });
      }
  </script>  
    
  <script>
      function modificar(id){
            if(($("#idPrevio").val() != 0)||($("#idPrevio").val() != "")){
                    var divFase = 'Fase'+$("#idPrevio").val();
                    var lblFase = 'fase'+$("#idPrevio").val();
                    var divproceso = 'Proceso'+$("#idPrevio").val();
                    var lblproceso= 'proceso'+$("#idPrevio").val();
                    var divduracion = 'Duracion'+$("#idPrevio").val();
                    var lblduracion= 'duracion'+$("#idPrevio").val();
                    var divunidad = 'Unidad'+$("#idPrevio").val();
                    var lblunidad= 'unidad'+$("#idPrevio").val();
                    var divtipod = 'Tipod'+$("#idPrevio").val();
                    var lbltipod= 'tipod'+$("#idPrevio").val();
                    var divresponsable = 'Responsable'+$("#idPrevio").val();
                    var lblresponsable= 'responsable'+$("#idPrevio").val();
                    var divestado = 'Estado'+$("#idPrevio").val();
                    var lblestado= 'estado'+$("#idPrevio").val();
                    
                    $("#"+divFase).css('display','none');                               
                    $("#"+lblFase).css('display','block');
                    $("#"+divproceso).css('display','none');
                    $("#"+lblproceso).css('display','block');
                    $("#"+divduracion).css('display','none');
                    $("#"+lblduracion).css('display','block');
                    $("#"+divunidad).css('display','none');
                    $("#"+lblunidad).css('display','block');
                    $("#"+divtipod).css('display','none');
                    $("#"+lbltipod).css('display','block');
                    $("#"+divresponsable).css('display','none');
                    $("#"+lblresponsable).css('display','block');
                    $("#"+divestado).css('display','none');
                    $("#"+lblestado).css('display','block');
                }
                
                var divFase = 'Fase'+id;
                var lblFase = 'fase'+id;
                var divproceso = 'Proceso'+id;
                var lblproceso = 'proceso'+id;
                var divduracion = 'Duracion'+id;
                var lblduracion = 'duracion'+id;
                var divunidad = 'Unidad'+id;
                var lblunidad= 'unidad'+id;
                var divtipod = 'Tipod'+id;
                var lbltipod= 'tipod'+id;
                var divresponsable = 'Responsable'+id;
                var lblresponsable= 'responsable'+id; 
                var divestado = 'Estado'+id; 
                var lblestado= 'estado'+id; 
                
                $("#"+divFase).css('display','block');                               
                $("#"+lblFase).css('display','none');
                $("#"+divproceso).css('display','block');
                $("#"+lblproceso).css('display','none');
                $("#"+divduracion).css('display','block');
                $("#"+lblduracion).css('display','none');
                $("#"+divunidad).css('display','block');
                $("#"+lblunidad).css('display','none');
                $("#"+divtipod).css('display','block');
                $("#"+lbltipod).css('display','none');
                $("#"+divresponsable).css('display','block');
                $("#"+lblresponsable).css('display','none');
                $("#"+divestado).css('display','block');
                $("#"+lblestado).css('display','none');
                if($("#idPrevio").val() != id){
                    $("#idPrevio").val(id);   
                }
                var combo = document.getElementById("modUnidad"+id);
                var valorA = combo.options[combo.selectedIndex].text;
                var fi = document.getElementById("modTipod"+id);
                if(valorA =='Dias' || valorA=='Días' || valorA=='dias' || valorA=='días'){
                      fi.disabled=false;
                } else {
                    var combo2 = document.getElementById("modTipod"+id);
                    var tipod = combo2.options[combo2.selectedIndex].text;
                    if(tipod=='' || tipod=='NULL' || tipod=='-'){
                    fi.value='';
                    fi.disabled=true;
                    }
                }
      }
  </script>
  <script>
      function cancelar(id){
            var divFase = 'Fase'+id;
            var lblFase = 'fase'+id;
            var divproceso = 'Proceso'+id;
            var lblproceso = 'proceso'+id;
            var divduracion = 'Duracion'+id;
            var lblduracion = 'duracion'+id;
            var divunidad = 'Unidad'+id;
            var lblunidad= 'unidad'+id;
            var divtipod = 'Tipod'+id;
            var lbltipod= 'tipod'+id;
            var divresponsable = 'Responsable'+id;
            var lblresponsable= 'responsable'+id; 
            var divestado = 'Estado'+id; 
            var lblestado= 'estado'+id; 

            $("#"+divFase).css('display','none');                               
            $("#"+lblFase).css('display','block');
            $("#"+divproceso).css('display','none');
            $("#"+lblproceso).css('display','block');
            $("#"+divduracion).css('display','none');
            $("#"+lblduracion).css('display','block');
            $("#"+divunidad).css('display','none');
            $("#"+lblunidad).css('display','block');
            $("#"+divtipod).css('display','none');
            $("#"+lbltipod).css('display','block');
            $("#"+divresponsable).css('display','none');
            $("#"+lblresponsable).css('display','block');
            $("#"+divestado).css('display','none');
            $("#"+lblestado).css('display','block');
      }
  </script>
   <script type="text/javascript">
            function guardarCambios(id){
                
                var tipopr ='tipop'+id;
                var fase = 'modFase'+id;
                var duracion = 'modduracion'+id;
                var unidad = 'modUnidad'+id;
                var responsable = 'modResponsable'+id;
                var estado = 'modEstado'+id;
                var tipod = 'modTipod'+id;
                var form_data = {
                    is_ajax:1,
                    id:+id,
                    tipop:$("#"+tipopr).val(),
                    estado:$("#"+estado).val(),
                    fase:$("#"+fase).val(),
                    duracion:$("#"+duracion).val(),
                    unidad:$("#"+unidad).val(),
                    tipod:$("#"+tipod).val(),
                    responsable:$("#"+responsable).val()
                };
                var result='';
                $.ajax({
                    type: 'POST',
                    url: "jsonProcesos/modificar_GG_FLUJO_PROCESALJson.php",
                    data:form_data,
                    success: function (data) {
                        result = JSON.parse(data);                        
                        if (result==true) {
                            $("#myModal25").modal('show');
                            $('#ver25').click(function(){
                                $("#myModal25").modal('hide');
                              document.location = 'GG_FLUJO_PROCESAL.php';
                            });
                        }else {                                
                            if(result=='3'){
                                $("#myModal27").modal('show');
                                $('#ver27').click(function(){
                                    $("#myModal27").modal('hide');
                                  document.location = 'GG_FLUJO_PROCESAL.php';
                                });
                            }else{
                                $("#myModal26").modal('show'); 
                                $('#ver26').click(function(){
                                    $("#myModal26").modal('hide');
                                  document.location = 'GG_FLUJO_PROCESAL.php';
                                });
                            }
                            
                        }                                                                        
                    }
                });
            }
        </script>
</body>
</html>


