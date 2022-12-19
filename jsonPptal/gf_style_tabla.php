
    <html lang="en">
        <head>
            <script src="js/jquery.min.js"></script>
            <link rel="stylesheet" href="css/dataTables.jqueryui.min.css" type="text/css" media="screen" title="default" />
            <script src="js/jquery.dataTables.min.js" type="text/javascript"></script>
            <script src="js/dataTables.jqueryui.min.js" type="text/javascript"></script>
            <link rel="stylesheet" href="css/dataTables.jqueryui.min.css" type="text/css" media="screen" title="default" />
            <script type="text/javascript">
              $(document).ready(function () {
                  var i = 1;
                  $('#tabla thead th').each(function () {
                      if (i != 1) {
                          var title = $(this).text();
                          switch (i) {
                              case 3:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 4:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 5:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 6:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 6:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 7:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 8:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 9:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 10:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 11:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 12:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 13:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 14:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 15:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 16:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 17:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 18:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 19:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 20:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 21:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 22:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 23:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 24:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 25:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 26:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 27:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 28:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 29:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 30:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 31:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 32:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 33:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 34:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 35:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 36:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 37:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 38:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 39:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 40:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 41:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 42:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 43:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 44:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 45:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 46:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 47:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 48:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 49:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                              case 50:
                                  $(this).html('<input type="text" style="width:100%;" placeholder="Filtrar" class="campos"/>');
                                  break;
                          }
                          i = i + 1;
                      } else {
                          i = i + 1;
                      }
                  });

                  // DataTable
                  var table = $('#tabla').DataTable({
                      "autoFill": true,
                      "scrollX": true,
                      "pageLength": 5,
                      "language": {
                          "lengthMenu": "Mostrar _MENU_ registros",
                          "zeroRecords": "No Existen Registros...",
                          "info": "Página _PAGE_ de _PAGES_ ",
                          "infoEmpty": "No existen datos",
                          "infoFiltered": "(Filtrado de _MAX_ registros)",
                          "sInfo": "Mostrando _START_ - _END_ de _TOTAL_ registros", "sInfoEmpty": "Mostrando 0 - 0 de 0 registros"
                      },
                      'columnDefs': [{
                              'targets': 0,
                              'searchable': false,
                              'orderable': false,
                              'className': 'dt-body-center'
                          }]
                  });

                  var i = 0;
                  table.columns().every(function () {
                      var that = this;
                      if (i != 0) {
                          $('input', this.header()).on('keyup change', function () {
                              if (that.search() !== this.value) {
                                  that
                                          .search(this.value)
                                          .draw();
                              }
                          });
                          i = i + 1;
                      } else {
                          i = i + 1;
                      }
                  });
              });
            </script>

            <style>
                /* Remove the navbar's default margin-bottom and rounded borders */
                .navbar {
                    margin-bottom: 0;
                    border-radius: 0;
                }

                /* Set height of the grid so .sidenav can be 100% (adjust as needed) */
                .row.content {height: 510px}

                /* Set gray background color and 100% height */
                .sidenav {
                    padding-top: 20px;
                    background-color: #f1f1f1;
                    height: 100%;
                }

                /* Set black background color, white text and some padding */
                footer {
                    background-color: #555;
                    color: white;
                    padding: 15px;
                }

                /* On small screens, set height to 'auto' for sidenav and grid */
                @media screen and (max-width: 767px) {
                    .sidenav {
                        height: auto;
                        padding: 15px;
                    }
                    .row.content {height:auto;}
                }
            </style>
        </head>
        <link rel="stylesheet" href="css/bootstrap-theme.min.css">
        <script src="js/bootstrap.min.js"></script>