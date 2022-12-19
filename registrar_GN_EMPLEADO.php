<?php
#01/03/2017 --- Nestor B --- Se agrego la librería de busqueda rápida en los selects del formulario
#03/08/2017 --- Nestor B --- se agrego el campo del salario integral
require_once ('head.php');
require_once ('./Conexion/conexion.php');
#session_start();
?>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="css/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<link rel="stylesheet" href="css/select2.css">
<link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
<script src="js/jquery-ui.js"></script>

<style>
    label #sltTercero-error, #sltRiesgos-error {
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
   <title>Registrar Empleado</title>
   <link rel="stylesheet" href="css/select2.css">
   <link href="css/select/select2.min.css" rel="stylesheet">
    </head>
    <body>
        <div class="container-fluid text-center">
            <div class="row content">
                <?php require_once 'menu.php'; ?>
                <div class="col-sm-8 text-left">
                    <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Registrar Empleado</h2>
                    <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                        <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrarEmpleadoJson.php">
                            <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>                                                            
                            <!------------------------- Consulta para llenar campo Tercero-->
                            <?php 
                                $ter = "SELECT          pt.perfil,
                                                        pt.tercero,
                                                        ter.id_unico,
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
                                                        ter.apellidodos)) AS NOMBRE, ter.numeroidentificacion 
                                        FROM gf_perfil_tercero pt
                                        LEFT JOIN gf_tercero ter ON pt.tercero = ter.id_unico
                                        WHERE pt.perfil = 2";
                                $tercero = $mysqli->query($ter);
                            ?>
                            <div class="form-group" style="margin-top: -5px">
                                <label for="sltTercero" class="control-label col-sm-5">
                                    <strong class="obligado">*</strong>Tercero:
                                </label>
                                <select name="sltTercero" class="form-control" id="sltTercero" title="Seleccione tercero" style="height: 30px" required="required">
                                    <option value="">Tercero</option>
                                    <?php 
                                        while ($filaT = mysqli_fetch_row($tercero)) { ?>
                                            <option value="<?php echo $filaT[2];?>"><?php echo $filaT[3].' - '.$filaT[4]; ?></option>
                                    <?php
                                        }
                                    ?>
                                </select>   
                            </div>
                            <!----------Fin Consulta Para llenar Tercero-->                              
                            <!----------Campo para llenar código Interno-->
                            <div class="form-group" style="margin-top: -10px;">
                                <label for="CodigoI" class="col-sm-5 control-label"><strong class="obligado"></strong>Código Interno:</label>
                                <input type="text" name="txtCodigoI" id="nombre" class="form-control" maxlength="100" title="Ingrese el nombre" onkeypress="return txtValida(event,'num_car')" placeholder="Nombre">
                            </div>                                    
                            <!----------Fin Campo código Interno-->
                            <!------------------------- Consulta para llenar Estado Empleado-->
                            <?php 
                                $es   = "SELECT id_unico, nombre FROM gn_estado_empleado";
                                $esta = $mysqli->query($es);
                            ?>
                            <div class="form-group" style="margin-top: -5px">
                                <label class="control-label col-sm-5">
                                    <strong class="obligado"></strong>Estado:
                                </label>
                                <select name="sltEstado" class="form-control" id="sltEstado" title="Seleccione Estado" style="height: 30px">
                                    <option value="">Estado</option>
                                    <?php 
                                        while ($filaES = mysqli_fetch_row($esta)) { ?>
                                            <option value="<?php echo $filaES[0];?>"><?php echo $filaES[1]; ?></option>
                                    <?php
                                        }
                                    ?>
                                </select>   
                            </div>
                            <!----------Fin Consulta Para llenar Estado Empleado--> 
                            <!------------------------- Consulta para llenar Regimen Cesantías-->
                            <?php 
                                $rc   = "SELECT id_unico, nombre FROM gn_regimen_cesantias";
                                $regc = $mysqli->query($rc);
                            ?>
                            <div class="form-group" style="margin-top: -5px">
                                <label class="control-label col-sm-5">
                                    <strong class="obligado"></strong>Cesantías:
                                </label>
                                <select name="sltCesantias" class="form-control" id="sltCesantias" title="Seleccione Cesantías" style="height: 30px">
                                    <option value="">Cesantías</option>
                                    <?php 
                                        while ($filaRC = mysqli_fetch_row($regc)) { ?>
                                            <option value="<?php echo $filaRC[0];?>"><?php echo $filaRC[1]; ?></option>
                                    <?php
                                        }
                                    ?>
                                </select>   
                            </div>
                            <!----------Fin Consulta Para llenar Regimen Cesantías-->
                            <!------------------------- Consulta para llenar Medio Pago-->
                            <?php 
                                $mp   = "SELECT id_unico, nombre FROM gn_medio_pago";
                                $mpag = $mysqli->query($mp);
                            ?>
                            <div class="form-group" style="margin-top: -5px">
                                <label class="control-label col-sm-5">
                                    <strong class="obligado"></strong>Medio Pago:
                                </label>
                                <select name="sltMedioP" class="form-control" id="sltMedioP" title="Seleccione Medio Pago" style="height: 30px">
                                    <option value="">Medio de Pago</option>
                                    <?php 
                                        while ($filaMP = mysqli_fetch_row($mpag)) { ?>
                                            <option value="<?php echo $filaMP[0];?>"><?php echo $filaMP[1]; ?></option>
                                    <?php
                                        }
                                    ?>
                                </select>   
                            </div>
                            <!----------Fin Consulta Para llenar Medio Pago-->
                            <!------------------------- Consulta para llenar Unidad Ejecutora-->
                            <?php 
                                $ue  = "SELECT id_unico, nombre FROM gn_unidad_ejecutora";
                                $ueje = $mysqli->query($ue);
                            ?>
                            <div class="form-group" style="margin-top: -5px">
                                <label class="control-label col-sm-5">
                                    <strong class="obligado"></strong>Unidad Ejecutora:
                                </label>
                                <select name="sltUnidadE" class=" form-control" id="sltUnidadE" title="Seleccione Unidad Ejecutora" style="height: 30px">
                                    <option value="">Unidad Ejecutora</option>
                                    <?php 
                                        while ($filaUE = mysqli_fetch_row($ueje)) { ?>
                                            <option value="<?php echo $filaUE[0];?>"><?php echo $filaUE[1]; ?></option>
                                    <?php
                                        }
                                    ?>
                                </select>   
                            </div>
                            <!----------Fin Consulta Para llenar Unidad Ejecutora-->
                            <!------------------------- Consulta para llenar Grupo Gestión-->
                            <?php 
                                $gg   = "SELECT id_unico, nombre FROM gn_grupo_gestion";
                                $gges = $mysqli->query($gg);
                            ?>
                            <div class="form-group" style="margin-top: -5px">
                                <label class="control-label col-sm-5">
                                    <strong class="obligado"></strong>Grupo Gestión:
                                </label>
                                <select name="sltGrupoG" class="form-control" id="sltGrupoG" title="Seleccione Grupo Gestión" style="height: 30px">
                                    <option value="">Grupo Gestión</option>
                                    <?php 
                                        while ($filaGG = mysqli_fetch_row($gges)) { ?>
                                            <option value="<?php echo $filaGG[0];?>"><?php echo $filaGG[1]; ?></option>
                                    <?php
                                        }
                                    ?>
                                </select>   
                            </div>
                <!----------Fin Consulta Para llenar Grupo Gestión-->
                <!------------------------- Consulta para llenar tipo riesgo-->
                            <?php 
                                $cr   = "SELECT id_unico, nombre, round(valor,2) FROM gn_categoria_riesgos";
                                $cres = $mysqli->query($cr);
                            ?>
                            <div class="form-group" style="margin-top: -5px">
                                <label for="sltRiesgos" class="control-label col-sm-5">
                                    <strong class="obligado">*</strong>Tipo de Riesgo:
                                </label>
                                <select name="sltRiesgos" class="form-control" id="sltRiesgos" title="Seleccione el Tipo de Riesgo" style="height: 30px" required>
                                    <option value="">Tipo de Riesgo</option>
                                    <?php 
                                        while ($filaR = mysqli_fetch_row($cres)) { ?>
                                            <option value="<?php echo $filaR[0];?>"><?php echo $filaR[1].' - '.$filaR[2].'%'; ?></option>
                                    <?php
                                        }
                                    ?>
                                </select>   
                            </div>
                 <!------------------------- Fin Consulta para llenar tipo riesgo-->
                 <!------------------------- Consulta para llenar tipo contrato nomina electronica -->
                        <?php


                        $tipoC   = "SELECT id_unico, nombre FROM gn_tipo_contrato_nomina_E";

                        $tipCon = $mysqli->query($tipoC);
                        ?>
                        <div class="form-group" style="margin-top: -5px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado"></strong>Tipo Contrato Nomina:
                            </label>
                            <select name="sltContrato" class="select2_single form-control" id="sltContrato" title="Seleccione el Tipo de Contrato" style="height: 30px">
                            <option value="">Tipo de Contrato</option>
                                <?php
                                while ($filaCo = mysqli_fetch_row($tipCon)) { ?>
                                <option value="<?php echo $filaCo[0];?>"><?php echo $filaCo[1];?></option>
                                <?php
                                }
                                ?>
                            </select>
                        </div>
<!------------------------- Fin Consulta para llenar tipo contrato nomina electronica -->
                         
                            <div class="form-group" style="margin-top: -5px">
                                <label for="salaIn" class="control-label col-sm-5">
                                    <strong class="obligado"></strong>Salario Integral:
                                </label>
                                <input type="radio" name="salaIn" id="salaIn" value="1">SI
                                <input type="radio" name="salaIn" id="salaIn" value="2" checked>NO
                            </div>

                            <div class="form-group" style="margin-top: -5px">
                                <label for="Retro" class="control-label col-sm-5">
                                    <strong class="obligado"></strong>Regimén Retroactivo:
                                </label>
                                <input type="radio" name="Retro" id="Retro" value="1">SI
                                <input type="radio" name="Retro" id="Retro" value="2" checked>NO
                            </div>
                            <div class="form-group" style="margin-top: 10px;">
                               <label for="no" class="col-sm-5 control-label"></label>
                               <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px;margin-left: 0px  ;">Guardar</button>
                            </div>
                              

                        </form>
                    </div>
                </div>                  
                <div class="col-sm-8 col-sm-1" styl>
                    <div class="col-sm-8 col-sm-1" style>
                        <table class="tablaC table-condensed text-center" align="center">
                            <thead>
                                <tr>
                                    <tr>                                        
                                        <th>
                                            <h2 class="titulo" align="center" style=" font-size:17px;">Información adicional</h2>
                                        </th>
                                    </tr>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>                                    
                                    <td>
                                        <a class="btn btn-primary btnInfo" href="TerceroEmpleadoNatural2.php">TERCERO</a>
                                    </td>
                                </tr>
                                <tr>                                    
                                    <td>
                                        <a class="btn btn-primary btnInfo" href="registrar_GN_ESTADO_EMPLEADO.php">ESTADO</a>
                                    </td>
                                </tr>
                                <tr>                                    
                                    <td>
                                        <a class="btn btn-primary btnInfo" href="registrar_GN_REGIMEN_CESANTIAS.php">CESANTIAS</a>
                                    </td>
                                </tr>
                                <tr>                                    
                                    <td>
                                        <a class="btn btn-primary btnInfo" href="registrar_GN_MEDIO_PAGO.php">MEDIO PAGO</a>
                                    </td>
                                </tr>
                                <tr>                                    
                                    <td>
                                        <a class="btn btn-primary btnInfo" href="registrar_GN_UNIDAD_EJECUTORA.php">UNIDAD EJECUTORA</a>
                                    </td>
                                </tr>
                                <tr>                                    
                                    <td>
                                        <a class="btn btn-primary btnInfo" href="registrar_GN_GRUPO_GESTION.php">GRUPO GESTION</a>
                                    </td>
                                </tr>
                            </tbody>    
                        </table>
                    </div>
                </div>
            </div>
        </div>        
        <?php require_once './footer.php'; ?>
        <script src="js/select/select2.full.js"></script>
        <script type="text/javascript"> 
         $("#sltTercero").select2();
         $("#sltRiesgos").select2();
         $("#sltGrupoG").select2();
         $("#sltEstado").select2();
         $("#sltCesantias").select2();
         $("#sltMedioP").select2();
         $("#sltUnidadE").select2();
         $("#sltContrato").select2();
        </script>
    </body>
</html>
    