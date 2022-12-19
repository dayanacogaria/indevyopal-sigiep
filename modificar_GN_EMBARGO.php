<?php 
#13/03/2017 --- Nestor B --- se modificó la consulta que trae toda la información para que muestre eñl nombre de la entidad 
require_once('Conexion/conexion.php');
require_once ('./Conexion/conexion.php');
# session_start();
$id = (($_GET["id"]));
  $sql = "SELECT        em.id_unico,
                        em.empleado,
                        e.id_unico,
                        e.tercero,
                        t.id_unico,
                        CONCAT(t.nombreuno,' ',t.nombredos,' ',t.apellidouno,' ',t.apellidodos),
                        em.entidad,
                        ter.id_unico,
                        ter.razonsocial,
                        em.fechaembargo,
                        em.fechaliquidar,
                        em.fechainicio,
                        em.fechafin                        
                FROM gn_embargo em	 
                LEFT JOIN	gn_empleado e                   ON em.empleado = e.id_unico
                LEFT JOIN   gf_tercero t                    ON e.tercero = t.id_unico
                LEFT JOIN   gf_tercero ter                   ON em.entidad = ter.id_unico
                where md5(em.id_unico) = '$id'";
    $resultado = $mysqli->query($sql);
    $row = mysqli_fetch_row($resultado);    
    
        $emid   = $row[0];
        $ememp  = $row[1];
        $eid    = $row[2];
        $eter   = $row[3];
        $terid  = $row[4];
        $ternom = $row[5];
        $ement  = $row[6];
        $entid  = $row[7];
        $entnom = $row[8];
        $emfe   = $row[9];
        $emfl   = $row[10];
        $emfi   = $row[11];
        $emff   = $row[12];
         
/* 
    * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
    
require_once './head.php';
?>
<script src="js/jquery-ui.js"></script>


<title>Modificar Embargo</title>
    </head>
    <body>
        <div class="container-fluid text-center">
              <div class="row content">
                  <?php require_once 'menu.php';             
                  ?>
                  <div class="col-sm-10 text-left">
                      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Modificar Embargo</h2>
                      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                          <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/modificarEmbargoJson.php">
                              <input type="hidden" name="id" value="<?php echo $row[0] ?>">
                              <p align="center" style="margin-bottom: 25px; margin-top: 25px;margin-left: 30px; font-size: 80%">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
<!------------------------- Consulta para llenar campo Empleado-->
                        <?php 

                        $emp = "SELECT 						
                                                        e.id_unico,
                                                        e.tercero,
                                                        t.id_unico,
                                                        CONCAT(t.nombreuno,' ',t.nombredos,' ',t.apellidouno,' ',t.apellidodos)
                            FROM gn_empleado e
                            LEFT JOIN gf_tercero t ON e.tercero = t.id_unico WHERE e.id_unico != $ememp";
                        $empleado = $mysqli->query($emp);
                        ?>
                        <div class="form-group" style="margin-top: -5px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado">*</strong>Empleado:
                            </label>
                            <select name="sltEmpleado" class="form-control" id="sltEmpleado" title="Seleccione empleado" style="height: 30px" required="">
                            <option value="<?php echo $eid?>"><?php echo $ternom?></option>
                                <?php 
                                while ($filaT = mysqli_fetch_row($empleado)) { ?>
                                <option value="<?php echo $filaT[0];?>"><?php echo ucwords(($filaT[3])); ?></option>
                                <?php
                                }
                                ?>
                            </select>   
                        </div>
<!----------Fin Consulta Para llenar Empleado-->
<!------------------------- Consulta para llenar campo Entidad-->
                        <?php 
                        $ent = "SELECT          pt.perfil,
                                                pt.tercero,
                                                t.id_unico,
                                                t.razonsocial
                            FROM gf_perfil_tercero pt
                            LEFT JOIN gf_tercero t ON pt.tercero = t.id_unico
                            WHERE pt.perfil = 12 AND id_unico != $ement";
                        $enti = $mysqli->query($ent);
                        ?>
                        <div class="form-group" style="margin-top: -5px">
                            <label class="control-label col-sm-5">
                                    <strong class="obligado">*</strong>Entidad:
                            </label>
                            <select name="sltEntidad" class="form-control" id="sltEntidad" title="Seleccione entidad" style="height: 30px" required="">
                            <option value="<?php echo $entid?>"><?php echo $entnom?></option>
                                <?php 
                                while ($filaEN = mysqli_fetch_row($enti)) { ?>
                                <option value="<?php echo $filaEN[1];?>"><?php echo $filaEN[3]; ?></option>
                                <?php
                                }
                                ?>
                            </select>   
                        </div>
<!----------Fin Consulta Para llenar Entidad-->
<!----------Script para invocar Date Picker-->
<script type="text/javascript">
$(document).ready(function() {
   $("#datepicker").datepicker();
});
</script>
<!------------------------- Campo para seleccionar Fecha Embargo-->
           <div class="form-group" style="margin-top: -10px;">
                <label for="fechaE" type = "date" class="col-sm-5 control-label"><strong class="obligado"></strong>Fecha Embargo:</label>
                <input style="width:auto" class="col-sm-2 input-sm" type="date" name="sltFechaE" id="sltFechaE" step="1" value="<?php echo $emfe;?>">
           </div>
<!----------Fin Captura de Fecha Embargo-->
<!------------------------- Campo para seleccionar Fecha Liquidar-->
           <div class="form-group" style="margin-top: -10px;">
                <label for="fechaL" type = "date" class="col-sm-5 control-label"><strong class="obligado"></strong>Fecha Liquidar:</label>
                <input style="width:auto" class="col-sm-2 input-sm" type="date" name="sltFechaL" id="sltFechaL" step="1" value="<?php echo $emfl;?>">
           </div>
<!----------Fin Captura de Fecha Liquidar-->
<!------------------------- Campo para seleccionar Fecha Inicio-->
           <div class="form-group" style="margin-top: -10px;">
                <label for="fechaI" type = "date" class="col-sm-5 control-label"><strong class="obligado"></strong>Fecha Inicio:</label>
                <input style="width:auto" class="col-sm-2 input-sm" type="date" name="sltFechaI" id="sltFechaI" step="1" value="<?php echo $emfi;?>" >
           </div>
<!----------Fin Captura de Fecha Inicio-->
<!------------------------- Campo para seleccionar Fecha Fin-->
           <div class="form-group" style="margin-top: -10px;">
                <label for="fechaF" type = "date" class="col-sm-5 control-label"><strong class="obligado"></strong>Fecha Fin:</label>
                <input style="width:auto" class="col-sm-2 input-sm" type="date" name="sltFechaF" id="sltFechaF" step="1" value="<?php echo $emff;?>" >
           </div>
<!----------Fin Captura de Fecha Fin-->
                                                           
                            <div class="form-group" style="margin-top: 10px;">
                              <label for="no" class="col-sm-5 control-label"></label>
                              <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left: 0px;">Guardar</button>
                            </div>


                          </form>
                      </div>
                  </div>                  
              </div>
        </div>
        <?php require_once './footer.php'; ?>
        <script>
function fechaInicial(){
        var fechain= document.getElementById('sltFechaI').value;
        var fechafi= document.getElementById('sltFechaF').value;
          var fi = document.getElementById("sltFechaF");
        fi.disabled=false;
      
       
            $( "#sltFechaF" ).datepicker( "destroy" );
            $( "#sltFechaF" ).datepicker({ changeMonth: true, minDate: fechain});
        

           
           
}
</script>
    </body>
</html>
