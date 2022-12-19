<?php
require_once('Conexion/conexion.php');
require_once('head_listar.php');
require_once ('nombreBaseDatos.php');

$id_tabla_homologable = " ";
if (isset($_GET["id_tabla_homologable"]))
{ 
  $id_tabla_homologable = (($_GET["id_tabla_homologable"]));

  $queryTabHom = "SELECT tabHom.id, tabHom.tabla_origen, tabHom.columna_origen, tabHom.tabla_destino, tabHom.columna_destino, tabHom.tipo, tipHom.nombre tipoHomologacion, tabHom.informe, inf.nombre informe, tabHom.periodicidad, per.nombre periodicidad 
  FROM gn_tabla_homologable tabHom 
  LEFT JOIN gn_tipo_homologable tipHom ON tipHom.id = tabHom.tipo 
  LEFT JOIN gn_informe inf ON inf.id = tabHom.informe 
  LEFT JOIN gn_periodicidad per ON per.id = tabHom.periodicidad
  WHERE md5(tabHom.id) = '$id_tabla_homologable'";
}

$resultado = $mysqli->query($queryTabHom);
$row = mysqli_fetch_row($resultado);

//$baseDatos = 'u858942576_sigep';
//$baseDatos = 'sigep';

?>

  <title>Modificar Configuración Base</title>
</head>
<body>

  
<div class="container-fluid text-center">
  <div class="row content">
    
    <?php require_once ('menu.php'); ?>

    <div class="col-sm-10 text-left">

      <h2 id="forma-titulo3" align="center" style="margin-top: 0px; margin-bottom: 5px; margin-right: 4px; margin-left: 4px;">Modificar Configuración Base</h2>

       <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form col-sm-12">

          <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/modificar_GN_TABLA_HOMOLOGABLEJson.php">

          <p align="center" style="margin-bottom: 15px; margin-top: 5px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>

            <input type="hidden" name="id" value="<?php echo $row[0];?>">

            <div class="form-group col-sm-12" style="margin: 0px;">

                <label for="informe" class="col-sm-2 control-label">
                  <strong style="color:#03C1FB;">*</strong>
                  Informe:
                </label>

                <div class="col-sm-3">
                <?php 
                  $sqlInforme = "SELECT id, nombre 
                    FROM gn_informe     
                    ORDER BY nombre ASC";
                  $informe = $mysqli->query($sqlInforme);
                ?>
                <select name="informe" id="informe" class="form-control input-sm" title="Informe" style="width: 150px;" required>
                  <?php 

                    $selecInforme = '';
                    while($rowI = mysqli_fetch_row($informe))
                    {
                      if($rowI[0] == $row[7])
                      {
                        $selecInforme = 'selected = "selected"';
                      }
                      else
                      {
                        $selecInforme = '';
                      }
                      echo '<option value="'.$rowI[0].'" '.$selecInforme.'>'.ucwords(strtolower($rowI[1])).'</option>';
                    }
                  ?>
                </select> 
              </div>

               <label for="periodicidad" class="col-sm-2 control-label">
                <strong style="color:#03C1FB;">*</strong>
                Periodicidad:
              </label>

              <div class="col-sm-3">
                <?php 
                  $sqlPeriodicidad = "SELECT id, nombre 
                    FROM gn_periodicidad   
                    ORDER BY nombre ASC";
                  $periodicidad = $mysqli->query($sqlPeriodicidad);
                ?>
                <select name="periodicidad" id="periodicidad" class="form-control input-sm" title="Informe" style="width: 150px;" required>
                  <?php 

                    $selecPeriodicidad = '';
                    while($rowP = mysqli_fetch_row($periodicidad))
                    {
                      if($rowP[0] == $row[9])
                      {
                        $selecPeriodicidad = 'selected = "selected"';
                      }
                      else
                      {
                        $selecPeriodicidad = '';
                      }
                      echo '<option value="'.$rowP[0].'" '.$selecPeriodicidad.'>'.ucwords(strtolower($rowP[1])).'</option>';
                    }
                  ?>
                </select> 
              </div>

               <div class="col-sm-2">
                <button type="submit" class="btn btn-primary sombra habilita" style="background: #00548F; color: #fff; border-color: #1075C1; margin-top: 0px;">
                  Guardar
                </button>
              </div>
            
            </div>

            </div>

<div class="form-group col-sm-12" style="margin-top: 10px;">
  <div class="col-sm-6">

    <fieldset>

      <legend style="font-size: 14px; font-weight: bold;">
        Origen
      </legend>

      <div class="col-sm-12">

        <div class="col-sm-6" align="right">
          <label for="tabOrig" class="control-label">
            <strong style="color:#03C1FB;">*</strong>
            Tabla:
          </label>
        </div>
        
        <div class="col-sm-6">

          <?php 
            $sqlTablaOrigen = "SELECT table_name 
              FROM INFORMATION_SCHEMA.TABLES 
              WHERE TABLE_SCHEMA = '$baseDatos'";
            $tablaOrigen = $mysqli->query($sqlTablaOrigen);
          ?>
          <input type="hidden" id="tabOrigOcul" value="<?php echo $row[1];?>">
          <select name="tabOrig" id="tabOrig" class="form-control  habilita input-sm" title="Tabla Origen" style="width: 150px;" required>
          <?php 
            $selecTablaOrigen =  '';
            while($rowTO = mysqli_fetch_row($tablaOrigen))
            {
              if($rowTO[0] == $row[1])
              {
                $selecTablaOrigen = 'selected = "selected"';
              }
              else
              {
                $selecTablaOrigen = '';
              }
              echo '<option value="'.$rowTO[0].'" '.$selecTablaOrigen.'>'.ucwords(strtolower($rowTO[0])).'</option>';
            }
          ?>
          </select> 

        </div>

      </div>


      <div class="col-sm-12" style="margin-top: 5px;">

        <div class="col-sm-6" align="right">
              <label for="colOrg" class="control-label">
                <strong style="color:#03C1FB;">*</strong>
                Columna:
              </label>
        </div>

        <div class="col-sm-6">
          <input type="hidden" id="colOrigOcul" value="<?php echo $row[2];?>">    
          <select name="colOrg" id="colOrg" class="form-control  habilita input-sm" title="Columna Origen" style="width: 150px;" required>
          </select>
        </div>

      </div>

    </fieldset>

  </div>


  <div class="col-sm-6">

    <fieldset>
      <legend style="font-size: 14px; font-weight: bold;">
        Destino
      </legend>

      <div class="col-sm-12">

      <div class="col-sm-6" align="right">
        <label for="tabDes" class="control-label">
          <strong style="color:#03C1FB;">*</strong>
          Tabla:
        </label>
      </div>

      <div class="col-sm-6">

      <?php
        $sqlTablaDestino = "SELECT table_name 
          FROM INFORMATION_SCHEMA.TABLES 
          WHERE TABLE_SCHEMA = '$baseDatos'";
          $tablaDestino = $mysqli->query($sqlTablaDestino);
      ?>
        <input type="hidden" id="tabDesOcul" value="<?php echo $row[3];?>">
        <select name="tabDes" id="tabDes" class="form-control  habilita input-sm" title="Tabla Destino" style="width: 150px;" required>
          <?php 
            $selecTablaDestino = '';
            while($rowTD = mysqli_fetch_row($tablaDestino))
            {
              if($rowTD[0] == $row[3])
              {
                $selecTablaDestino = 'selected = "selected"';
              }
              else
              {
                $selecTablaDestino = '';
              }
              echo '<option value="'.$rowTD[0].'" '.$selecTablaDestino.'>'.ucwords(strtolower($rowTD[0])).'</option>';
            }
          ?>
        </select> 
      </div>

    </div>

    <div class="col-sm-12" style="margin-top: 5px;">

      <div class="col-sm-6" align="right">
        <label for="colDes" class="control-label">
          <strong style="color:#03C1FB;">*</strong>
          Columna:
        </label>
      </div>

      <div class="col-sm-6">
        <input type="hidden" id="colDesOcul" value="<?php echo $row[4];?>">      
        <select name="colDes" id="colDes" class="form-control  habilita input-sm" title="Columna Destino" style="width: 150px;" required>
        </select>

      </div>


    </div>

  </div>

  </fieldset>

  </div>

            <input type="hidden" name="MM_insert" >
          </form>
      <!--  </div>   Termina -->

       
        <script type="text/javascript">

          function tablaOrigen(ind)
          {
            var tabla = $("#tabOrig").val();
            var tablaPrev = $("#tabOrigOcul").val();
            var columPrev = $("#colOrigOcul").val();

            var form_data = { estruc: 1, tabla: tabla };  
            $.ajax({
              type: "POST",
              url: "estructura_gestor_informes.php",
              data: form_data,
              success: function(response)
              {
                $("#colOrg").html(response).fadeIn();

                if(tablaPrev == tabla)
                {
                  $('#colOrg > option[value="' + columPrev + '"]').attr('selected', 'selected');
                }

                if(ind == 1)
                {
                  $("#colOrg").focus(); 
                }
                                            
              }//Fin succes.
            }); //Fin ajax.
          }

        </script>

        <script type="text/javascript">

          function tablaDestino(ind)
          {
            var tabla = $("#tabDes").val();
            var tablaPrev = $("#tabDesOcul").val();
            var columPrev = $("#colDesOcul").val();

            var form_data = { estruc: 1, tabla: tabla };  
            $.ajax({
              type: "POST",
              url: "estructura_gestor_informes.php",
              data: form_data,
              success: function(response)
              {
                $("#colDes").html(response).fadeIn();

                if(tablaPrev == tabla)
                {
                  $('#colDes > option[value="' + columPrev + '"]').attr('selected', 'selected');
                }

                if(ind == 1)
                {
                  $("#colDes").focus();                           
                }
              }//Fin succes.
            }); //Fin ajax.
          }

        </script>

      
        <script type="text/javascript">

          $(document).ready(function()
          {
            tablaOrigen(2);
            tablaDestino(2);
          });

        </script>

         <script type="text/javascript">

          $(document).ready(function()
          {
            $("#tabOrig").change(function()
            {
              tablaOrigen(1);
            });
          });

        </script>

        <script type="text/javascript">
        
          $(document).ready(function()
          {
            $("#tabDes").change(function()
            {
              tablaDestino(1);
            });
          });

        </script>


    </div> <!-- col-sm-10 text-left -->

  </div> <!-- row content -->
</div> <!-- container-fluid text-center -->


  <?php require_once ('footer.php'); ?>

</body>
</html>

