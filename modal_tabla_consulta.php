<?php
#############################################################################################
#       ***************************     Modificaciones      ***************************     #
#############################################################################################
#19/04/2018 | Erica G.  | Parametrizacion
?>
<style>
    #btnCerrarModalMov:hover {
        border: 1px solid #020324;         
    }
    
    #btnCerrarModalMov{
        box-shadow: 1px 1px 1px 1px #424852;
    }
    
  .acotado
  {
    white-space: normal;
  }
  
</style>

<?php
    require_once('Conexion/conexion.php');
    @session_start();
    $anno = $_SESSION['anno'];
    $compania = $_SESSION['compania'];
    $queryInforme = ""; 
    $resultado = "";
    $num_col = 0;
    $id = 0;
    $tipoInf = 0;
    
    
    if(!empty($_POST['consulta']))
    {
        $sqlConsulta = str_replace("@", $anno,$_POST['consulta']);
        $sqlConsulta = str_replace("$", $compania,$sqlConsulta);
        $resultado = $mysqli->query($sqlConsulta);
        $num_col = $mysqli->field_count;
        $columnas_cb = array();

        for ($i=0; $i < $num_col; $i++)
        { 
            $info_campo = mysqli_fetch_field_direct($resultado, $i);
            $columnas_cb[$i] = $info_campo->name;;
        }
    }

?>



<div class="modal fade mov" id="mdlTablaConsulta" role="dialog" align="center" aria-labelledby="mdlFormulador" aria-hidden="true">
    <div class="modal-dialog" style="height:600px;width:1000px">
        <div class="modal-content">
            
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title"  style="font-size: 24px; padding: 3px;">Consulta</h4>
                <div class="col-sm-offset-11" style="margin-top:-30px;margin-right: -45px">
                    <button type="button" id="btnCerrarModalMov" class="btn btn-xs" style="color: #000;" data-dismiss="modal" ><li class="glyphicon glyphicon-remove"></li></button>
                </div>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="contenedorForma col-sm-12" style="margin-top:-10px;margin-right:-3px">
                            
           <div class="table-responsive" style="margin: 5px;">
          <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;"> 
              
          <table  id="tablaCon" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
              
            <thead>

              <tr>
                <td style="display: none;">Identificador</td>
                <td width="30px" align="center"></td>
                
                <?php 
                  for ($i = 0; $i < $num_col; $i++) 
                  { 
                    echo '<td><strong>'.ucwords($columnas_cb[$i]).'</strong></td>';
                  }
                ?>
                
              </tr>

              <tr>
                <th style="display: none;">Identificador</th>
                <th width="7%"></th>
                
                <?php 
                  for ($i = 0; $i < $num_col; $i++) 
                  { 
                    echo '<th>'.ucwords($columnas_cb[$i]).'</th>';
                  }

                ?>
                
              </tr>
             

            </thead>
            
            <tbody>
              
              <?php
                if(!empty($resultado))
                {
                while($row = mysqli_fetch_row($resultado))
                {
              ?>
              
              <tr>
                <td style="display: none;"></td>
                <td></td>
               
                  <?php 
                  for ($i = 0; $i < $num_col; $i++) 
                  { 
                    echo '<td><div class="acotado">'.ucwords(mb_strtolower($row[$i])).'</div></td>';
                  }

                ?>
                
              </tr> 
              
             <?php
               }
             }
              ?>

            </tbody>
          </table>

       </div>
        
      </div> 
                                               
        </div>      
    </div>
  </div>
</div>
            <div id="forma-modal" class="modal-footer">                
            </div>
        </div>
    </div>
</div>

<style>

#textoPantalla { width: 500px; border: 2px black solid; text-align: right;   
                 padding: 5px 5px;
                 background-color: white; font-family: "arial"; 
                 overflow: hidden;}
.largo{
    width: 88px;
    height: 34px;
    font-size: 14px;
}

</style>
<script src="js/cabezaTabla.js"></script>