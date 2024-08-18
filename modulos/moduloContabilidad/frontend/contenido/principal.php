<style>
        .texto-justificado {
            text-align: justify;
            text-justify: inter-word; /* Mejora la justificación */
            margin: 20px; /* Espacio alrededor del texto */
        }
    </style>
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Accesos Rápidos</h6>
    </div>
    <div class="card-body">
        <div class="card-deck">
            <div class="card">
                <div class="card-body ">
                    <h5 class="card-title texto-justificado">Tipo de Partidas</h5>
                    <p class="card-text texto-justificado">Esta categoría se utiliza para clasificar y organizar las transacciones económicas dentro del sistema contable.</p>
                    <button class="btn btn-success texto-justificado mt-1" href="#" id="tipopartidainicio">
                        <i class="fa fa-angle-right" ></i> Ir
                    </button>
                </div>
            </div>

            <div class="card">
                <div class="card-body ">
                    <h5 class="card-title texto-justificado">Catalogo de Cuentas</h5>
                    <p class="card-text texto-justificado">Este apartado conetiene la lista de las ceuntas registradas por el personal donde podemos ingresar, editar y eliminar las cuentas.</p>
                    <button class="btn btn-success texto-justificado mt-1" href="#" id="catalogorapido">
                        <i class="fa fa-angle-right" ></i> Ir
                    </button>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h5 class="card-title texto-justificado">Bitacora</h5>
                   
                    <p class="card-text texto-justificado">Acceso rapido al reporte de bitacora donde se detalla todos los movimientos que se han generado en el sistema y el usuario responsable.</p>
                
                    <button class="btn btn-success texto-justificado mt-1" href="#" id="bitacorarep">
                        <i class="fa fa-angle-right" ></i> Ir
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    
      $("#tipopartidainicio").click(function(){
        $("#render").load("load/adminTipoPartidas.php");
    });

    $("#catalogorapido").click(function(){
        $("#render").load("load/adminCatalogo.php");
    });

    $("#bitacorarep").click(function(){
        $("#render").load("load/adminbitacora.php");
    });
    
</script>